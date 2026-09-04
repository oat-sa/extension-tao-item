<?php

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 31 Milk St # 960789 Boston, MA 02196 USA
 *
 * Copyright (c) 2026 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace oat\taoItems\model\Comment;

use common_Logger;
use core_kernel_users_GenerisUser;
use oat\generis\model\data\Ontology;
use oat\tao\helpers\UserHelper;
use oat\tao\model\TaskOrchestrator\CommentMentionDeepLinkBuilder;
use oat\tao\model\TaskOrchestrator\CommentMentionEmailPayload;
use oat\tao\model\TaskOrchestrator\TaskOrchestratorEmailService;
use Throwable;

/**
 * Sends comment-mention emails (via Task Orchestrator) after a successful save.
 * Skips recipients without a usable email. Failures never roll back the comment.
 */
class CommentMentionNotificationService
{
    private CommentMentionParser $parser;
    private Ontology $ontology;
    private TaskOrchestratorEmailService $emailService;
    private CommentMentionDeepLinkBuilder $deepLinkBuilder;

    public function __construct(
        CommentMentionParser $parser,
        Ontology $ontology,
        TaskOrchestratorEmailService $emailService,
        CommentMentionDeepLinkBuilder $deepLinkBuilder
    ) {
        $this->parser = $parser;
        $this->ontology = $ontology;
        $this->emailService = $emailService;
        $this->deepLinkBuilder = $deepLinkBuilder;
    }

    /**
     * @param list<array{id: string, login: string}>|null $previousMentions Mentions from previous body (update only)
     * @return array{initiated: int, skippedNoEmail: int, failed: int}
     */
    public function notifyForComment(
        ItemComment $comment,
        string $mentionedByLabel,
        ?array $previousMentions = null
    ): array {
        $mentions = $this->parser->parse($comment->getBody());
        if ($previousMentions !== null) {
            $previousIds = array_fill_keys($this->parser->ids($previousMentions), true);
            $mentions = array_values(array_filter(
                $mentions,
                static fn (array $mention): bool => !isset($previousIds[$mention['id']])
            ));
        }

        $stats = [
            'initiated' => 0,
            'skippedNoEmail' => 0,
            'failed' => 0,
        ];

        if ($mentions === []) {
            return $stats;
        }

        $resourceLabel = $this->resolveResourceLabel($comment->getResourceUri());
        $resourceUrl = $this->deepLinkBuilder->build(
            $comment->getResourceType(),
            $comment->getResourceUri()
        );

        foreach ($mentions as $mention) {
            try {
                $recipient = $this->resolveMentionRecipient($mention);
                if ($recipient === null) {
                    $stats['skippedNoEmail']++;
                    continue;
                }
                if ($recipient === false) {
                    $stats['failed']++;
                    continue;
                }

                $this->emailService->sendCommentMention(
                    $recipient['login'],
                    $recipient['email'],
                    new CommentMentionEmailPayload(
                        $mentionedByLabel !== '' ? $mentionedByLabel : 'TAO user',
                        $recipient['login'],
                        $comment->getResourceType(),
                        $resourceUrl,
                        $resourceLabel,
                        $recipient['name']
                    )
                );
                $stats['initiated']++;
            } catch (Throwable $exception) {
                $stats['failed']++;
                common_Logger::w(
                    sprintf(
                        'Comment mention email failed for user %s on comment %s: %s',
                        $mention['id'],
                        $comment->getId(),
                        $exception->getMessage()
                    )
                );
            }
        }

        return $stats;
    }

    /**
     * Resolve mentioned RDF user for delivery.
     * Email comes from ontology PROPERTY_USER_MAIL (persistence), not portal-user.
     *
     * @param array{id: string, login: string} $mention
     * @return array{login: string, email: string, name: ?string}|null|false null = no email, false = unresolvable
     */
    protected function resolveMentionRecipient(array $mention)
    {
        $userResource = $this->ontology->getResource($mention['id']);
        if ($userResource === null || !$userResource->exists()) {
            return false;
        }

        $user = new core_kernel_users_GenerisUser($userResource);
        $email = trim((string) UserHelper::getUserMail($user));
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return null;
        }

        $login = $mention['login'] !== ''
            ? $mention['login']
            : (string) UserHelper::getUserLogin($user);

        if ($login === '') {
            return false;
        }

        $name = UserHelper::getUserName($user, true);
        $name = is_string($name) && trim($name) !== '' ? trim($name) : null;

        return [
            'login' => $login,
            'email' => $email,
            'name' => $name,
        ];
    }

    private function resolveResourceLabel(string $resourceUri): string
    {
        $resource = $this->ontology->getResource($resourceUri);
        $label = trim((string) $resource->getLabel());

        return $label !== '' ? $label : $resourceUri;
    }
}
