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
use oat\tao\model\TaskOrchestrator\CommentMentionEmailTemplatePayload;
use oat\tao\model\TaskOrchestrator\TaskOrchestratorEmailService;
use Throwable;

/**
 * NotificationAdapter: sends comment-mention emails via Task Orchestrator.
 *
 * Callers supply already-parsed mentions (mention parsing belongs to the mentions
 * feature). Skips recipients without a usable email. Failures never roll back the comment.
 */
class CommentMentionNotificationService
{
    private Ontology $ontology;
    private TaskOrchestratorEmailService $emailService;
    private CommentMentionDeepLinkBuilder $deepLinkBuilder;

    public function __construct(
        Ontology $ontology,
        TaskOrchestratorEmailService $emailService,
        CommentMentionDeepLinkBuilder $deepLinkBuilder
    ) {
        $this->ontology = $ontology;
        $this->emailService = $emailService;
        $this->deepLinkBuilder = $deepLinkBuilder;
    }

    /**
     * Notify all mentions in a newly created comment.
     *
     * @param list<array{id: string, login: string}> $mentions
     */
    public function notifyForComment(ItemComment $comment, string $mentionedByLabel, array $mentions): void
    {
        $this->notifyMentions($comment, $mentionedByLabel, $mentions);
    }

    /**
     * Notify only mentions added during a comment edit.
     *
     * @param list<array{id: string, login: string}> $currentMentions Mentions in the updated body
     * @param list<array{id: string, login: string}> $previousMentions Mentions from the body before update
     */
    public function notifyForCommentUpdate(
        ItemComment $comment,
        string $mentionedByLabel,
        array $currentMentions,
        array $previousMentions
    ): void {
        $previousIds = [];
        foreach ($previousMentions as $mention) {
            if (isset($mention['id']) && is_string($mention['id']) && $mention['id'] !== '') {
                $previousIds[$mention['id']] = true;
            }
        }

        $newMentions = array_values(array_filter(
            $currentMentions,
            static fn (array $mention): bool => isset($mention['id']) && !isset($previousIds[$mention['id']])
        ));

        $this->notifyMentions($comment, $mentionedByLabel, $newMentions);
    }

    /**
     * @param list<array{id: string, login: string}> $mentions
     */
    private function notifyMentions(
        ItemComment $comment,
        string $mentionedByLabel,
        array $mentions
    ): void {
        if ($mentions === []) {
            return;
        }

        $mentionedByLabel = $mentionedByLabel !== '' ? $mentionedByLabel : 'TAO user';
        $resourceLabel = $this->resolveResourceLabel($comment->getResourceUri());
        $resourceUrl = $this->deepLinkBuilder->build(
            ResourceCommentType::classUri($comment->getResourceType()),
            $comment->getResourceUri()
        );

        foreach ($mentions as $mention) {
            try {
                $recipient = $this->resolveMentionRecipient($mention);
                if ($recipient === null || $recipient === false) {
                    continue;
                }

                $this->emailService->sendCommentMention(
                    $recipient['login'],
                    $recipient['email'],
                    new CommentMentionEmailTemplatePayload(
                        $mentionedByLabel,
                        $recipient['login'],
                        $comment->getResourceType(),
                        $resourceUrl,
                        $resourceLabel,
                        $recipient['name']
                    )
                );
            } catch (Throwable $exception) {
                common_Logger::w(
                    sprintf(
                        'Comment mention email failed for user %s on comment %s: %s',
                        $mention['id'] ?? '',
                        $comment->getId(),
                        $exception->getMessage()
                    )
                );
            }
        }
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

        $login = ($mention['login'] ?? '') !== ''
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
