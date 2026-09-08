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
use oat\tao\model\user\MentionEligibleUsersProviderInterface;
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
    private MentionEligibleUsersProviderInterface $eligibleUsersProvider;

    public function __construct(
        Ontology $ontology,
        TaskOrchestratorEmailService $emailService,
        CommentMentionDeepLinkBuilder $deepLinkBuilder,
        MentionEligibleUsersProviderInterface $eligibleUsersProvider
    ) {
        $this->ontology = $ontology;
        $this->emailService = $emailService;
        $this->deepLinkBuilder = $deepLinkBuilder;
        $this->eligibleUsersProvider = $eligibleUsersProvider;
    }

    /**
     * Notify all mentions in a newly created comment.
     *
     * @param list<array{id: string, login: string}> $mentions
     * @param string $actorLogin Comment author login — TO job actor (user.login)
     */
    public function notifyForComment(
        ItemComment $comment,
        string $mentionedByLabel,
        array $mentions,
        string $actorLogin
    ): void {
        $this->notifyMentions($comment, $mentionedByLabel, $actorLogin, $mentions);
    }

    /**
     * Notify only mentions added during a comment edit.
     *
     * @param list<array{id: string, login: string}> $currentMentions Mentions in the updated body
     * @param list<array{id: string, login: string}> $previousMentions Mentions from the body before update
     * @param string $actorLogin Comment author login — TO job actor (user.login)
     */
    public function notifyForCommentUpdate(
        ItemComment $comment,
        string $mentionedByLabel,
        array $currentMentions,
        array $previousMentions,
        string $actorLogin
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

        $this->notifyMentions($comment, $mentionedByLabel, $actorLogin, $newMentions);
    }

    /**
     * @param list<array{id: string, login: string}> $mentions
     */
    private function notifyMentions(
        ItemComment $comment,
        string $mentionedByLabel,
        string $actorLogin,
        array $mentions
    ): void {
        if ($mentions === []) {
            return;
        }

        if (!$this->emailService->isConfigured()) {
            common_Logger::w(
                sprintf(
                    'Comment mention email skipped for comment %s: Task Orchestrator email is not configured',
                    $comment->getId()
                )
            );

            return;
        }

        $actorLogin = trim($actorLogin);
        if ($actorLogin === '') {
            common_Logger::w(
                sprintf(
                    'Comment mention email skipped for comment %s: empty actor login',
                    $comment->getId()
                )
            );

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
                $userUri = isset($mention['id']) && is_string($mention['id']) ? $mention['id'] : '';
                if (!$this->isEligibleMention($comment->getResourceUri(), $userUri)) {
                    common_Logger::w(
                        sprintf(
                            'Comment mention email skipped for ineligible user %s on comment %s',
                            $userUri,
                            $comment->getId()
                        )
                    );

                    continue;
                }

                $recipient = $this->resolveMentionRecipient($mention);
                if ($recipient === null) {
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
                    ),
                    $actorLogin
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
     * Resource-scoped eligibility (null provider result = unrestricted).
     */
    private function isEligibleMention(string $resourceUri, string $userUri): bool
    {
        if ($userUri === '') {
            return false;
        }

        $eligibleUris = $this->eligibleUsersProvider->getEligibleUserUris($resourceUri);
        if ($eligibleUris === null) {
            return true;
        }

        return in_array($userUri, $eligibleUris, true);
    }

    /**
     * Resolve mentioned RDF user for delivery.
     * Login/email/name come from the ontology user resource — never from HTML attributes.
     *
     * @param array{id: string, login: string} $mention
     * @return array{login: string, email: string, name: ?string}|null null = unresolved or no usable email
     */
    protected function resolveMentionRecipient(array $mention): ?array
    {
        $userResource = $this->ontology->getResource($mention['id']);
        if ($userResource === null || !$userResource->exists()) {
            return null;
        }

        $user = new core_kernel_users_GenerisUser($userResource);
        $email = trim((string) UserHelper::getUserMail($user));
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return null;
        }

        $login = trim((string) UserHelper::getUserLogin($user));
        if ($login === '') {
            return null;
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
