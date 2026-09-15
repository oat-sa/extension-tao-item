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

namespace oat\taoItems\test\unit\model\Comment;

use core_kernel_classes_Resource;
use oat\generis\model\data\Ontology;
use oat\oatbox\event\EventManager;
use oat\tao\model\menu\Perspective;
use oat\tao\model\menu\Section;
use oat\tao\model\menu\Tree;
use oat\tao\model\TaskOrchestrator\CommentMentionDeepLinkBuilder;
use oat\tao\model\TaskOrchestrator\TaskOrchestratorEmailService;
use oat\tao\model\TaoOntology;
use oat\tao\model\user\MentionEligibleUsersProviderInterface;
use oat\taoItems\model\Comment\CommentMentionNotificationService;
use oat\taoItems\model\Comment\ItemComment;
use oat\taoItems\model\Comment\ResourceCommentType;
use oat\taoItems\model\event\CommentMentionNotificationRequestedEvent;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CommentMentionNotificationServiceTest extends TestCase
{
    private Ontology|MockObject $ontology;
    private TaskOrchestratorEmailService|MockObject $emailService;
    private MentionEligibleUsersProviderInterface|MockObject $eligibleUsersProvider;
    private EventManager|MockObject $eventManager;

    protected function setUp(): void
    {
        $this->ontology = $this->createMock(Ontology::class);
        $this->emailService = $this->createEmailServiceMock();
        $this->emailService->method('isConfigured')->willReturn(true);
        $this->eligibleUsersProvider = $this->createMock(MentionEligibleUsersProviderInterface::class);
        $this->eligibleUsersProvider->method('getEligibleUserUris')->willReturn(null);
        $this->eventManager = $this->createMock(EventManager::class);
    }

    public function testNotifySkipsWhenEmailNotConfigured(): void
    {
        $emailService = $this->createEmailServiceMock();
        $emailService->method('isConfigured')->willReturn(false);
        $emailService->expects($this->never())->method('sendCommentMention');

        $sut = new CommentMentionNotificationService(
            $this->ontology,
            $emailService,
            $this->createDeepLinkBuilder(),
            $this->eligibleUsersProvider,
            $this->eventManager
        );

        $sut->notifyForComment(
            $this->comment('<p>Hi @alice</p>'),
            'Alice Author',
            [['id' => 'u1', 'login' => 'alice']],
            'alice.author'
        );
    }

    public function testNotifySkipsWhenNoMentions(): void
    {
        $sut = $this->createSut();
        $this->eventManager->expects($this->never())->method('trigger');

        $sut->notifyForComment($this->comment('plain text'), 'Alice', [], 'alice.author');
    }

    public function testNotifySkipsWhenActorLoginIsWhitespace(): void
    {
        $sut = $this->createSut();
        $this->eventManager->expects($this->never())->method('trigger');

        $sut->notifyForComment(
            $this->comment('<p>Hi @alice</p>'),
            'Alice Author',
            [['id' => 'u1', 'login' => 'alice']],
            '   '
        );
    }

    public function testNotifyOnlyNewMentionsOnUpdate(): void
    {
        $sut = $this->createSutWithRecipient(null);
        $this->eventManager->expects($this->never())->method('trigger');

        $sut->notifyForCommentUpdate(
            $this->comment('<p>Hi @alice</p>'),
            'Alice Author',
            [['id' => 'u1', 'login' => 'alice']],
            [['id' => 'u1', 'login' => 'alice']],
            'alice.author'
        );
    }

    public function testNotifySendsCommentMentionWithRequiredTemplateData(): void
    {
        $resource = $this->createMock(core_kernel_classes_Resource::class);
        $resource->method('getLabel')->willReturn('Item Label');
        $this->ontology->method('getResource')->willReturn($resource);

        $this->eventManager
            ->expects($this->once())
            ->method('trigger')
            ->with(
                $this->callback(static function ($event): bool {
                    if (!$event instanceof CommentMentionNotificationRequestedEvent) {
                        return false;
                    }

                    if (
                        $event->getCommentId() !== 'c1'
                        || $event->getRecipientUserUri() !== 'u1'
                        || $event->getRecipientLogin() !== 'alice'
                        || $event->getRecipientEmail() !== 'alice@example.com'
                        || $event->getActorLogin() !== 'alice.author'
                    ) {
                        return false;
                    }

                    $payload = $event->getPayload();
                    $data = $payload->toTemplateData();

                    return $data['mentionedBy'] === 'Alice Author'
                        && $data['username'] === 'alice'
                        && $data['resourceType'] === ResourceCommentType::ITEM
                        && str_contains($data['resourceUrl'], 'structure=items')
                        && $data['resourceLabel'] === 'Item Label'
                        && $data['name'] === 'Alice Mentioned';
                })
            )
            ->willReturn(null);

        $sut = $this->createSutWithRecipient([
            'login' => 'alice',
            'email' => 'alice@example.com',
            'name' => 'Alice Mentioned',
        ]);

        $sut->notifyForComment(
            $this->comment('<p>Hi @alice</p>'),
            'Alice Author',
            [['id' => 'u1', 'login' => 'alice']],
            'alice.author'
        );
    }

    public function testNotifySkipsRecipientWithoutEmail(): void
    {
        $resource = $this->createMock(core_kernel_classes_Resource::class);
        $resource->method('getLabel')->willReturn('Item Label');
        $this->ontology->method('getResource')->willReturn($resource);

        $sut = $this->createSutWithRecipient(null);
        $this->eventManager->expects($this->never())->method('trigger');

        $sut->notifyForComment(
            $this->comment('<p>Hi @alice</p>'),
            'Alice Author',
            [['id' => 'u1', 'login' => 'alice']],
            'alice.author'
        );
    }

    public function testNotifySkipsIneligibleSubmittedMention(): void
    {
        $resource = $this->createMock(core_kernel_classes_Resource::class);
        $resource->method('getLabel')->willReturn('Item Label');
        $this->ontology->method('getResource')->willReturn($resource);

        $eligibleUsersProvider = $this->createMock(MentionEligibleUsersProviderInterface::class);
        $eligibleUsersProvider
            ->expects($this->once())
            ->method('getEligibleUserUris')
            ->with('http://example.test/item#1')
            ->willReturn(['http://example.test/user#allowed']);

        $this->eventManager->expects($this->never())->method('trigger');

        $sut = new class (
            $this->ontology,
            $this->emailService,
            $this->createDeepLinkBuilder(),
            $eligibleUsersProvider,
            $this->eventManager,
            [
                'login' => 'forged-login',
                'email' => 'forged@example.com',
                'name' => 'Forged',
            ]
        ) extends CommentMentionNotificationService {
            /** @var array{login: string, email: string, name: ?string}|null */
            private $fixedRecipient;

            public function __construct(
                Ontology $ontology,
                TaskOrchestratorEmailService $emailService,
                CommentMentionDeepLinkBuilder $deepLinkBuilder,
                MentionEligibleUsersProviderInterface $eligibleUsersProvider,
                EventManager $eventManager,
                $fixedRecipient
            ) {
                parent::__construct($ontology, $emailService, $deepLinkBuilder, $eligibleUsersProvider, $eventManager);
                $this->fixedRecipient = $fixedRecipient;
            }

            protected function resolveMentionRecipient(array $mention): ?array
            {
                return $this->fixedRecipient;
            }
        };

        $sut->notifyForComment(
            $this->comment(
                '<span class="comment-mention" data-user-id="u1" '
                . 'data-user-login="forged-login">@forged</span>'
            ),
            'Alice Author',
            [['id' => 'u1', 'login' => 'forged-login']],
            'alice.author'
        );
    }

    public function testNotifyUsesResolvedRecipientLoginNotHtmlLogin(): void
    {
        $resource = $this->createMock(core_kernel_classes_Resource::class);
        $resource->method('getLabel')->willReturn('Item Label');
        $this->ontology->method('getResource')->willReturn($resource);

        $this->eventManager
            ->expects($this->once())
            ->method('trigger')
            ->with(
                $this->callback(static function ($event): bool {
                    if (!$event instanceof CommentMentionNotificationRequestedEvent) {
                        return false;
                    }

                    if (
                        $event->getRecipientLogin() !== 'rdf-login'
                        || $event->getRecipientEmail() !== 'alice@example.com'
                        || $event->getActorLogin() !== 'alice.author'
                    ) {
                        return false;
                    }

                    $payload = $event->getPayload();

                    return $payload->toTemplateData()['username'] === 'rdf-login';
                })
            )
            ->willReturn(null);

        $sut = $this->createSutWithRecipient([
            'login' => 'rdf-login',
            'email' => 'alice@example.com',
            'name' => 'Alice',
        ]);

        $sut->notifyForComment(
            $this->comment('<span data-user-id="u1" data-user-login="forged-from-html">@x</span>'),
            'Alice Author',
            [['id' => 'u1', 'login' => 'forged-from-html']],
            'alice.author'
        );
    }

    private function createSut(): CommentMentionNotificationService
    {
        return new CommentMentionNotificationService(
            $this->ontology,
            $this->emailService,
            $this->createDeepLinkBuilder(),
            $this->eligibleUsersProvider,
            $this->eventManager
        );
    }

    /**
     * @param array{login: string, email: string, name: ?string}|null $recipient
     */
    private function createSutWithRecipient($recipient): CommentMentionNotificationService
    {
        return new class (
            $this->ontology,
            $this->emailService,
            $this->createDeepLinkBuilder(),
            $this->eligibleUsersProvider,
            $this->eventManager,
            $recipient
        ) extends CommentMentionNotificationService {
            /** @var array{login: string, email: string, name: ?string}|null */
            private $fixedRecipient;

            public function __construct(
                Ontology $ontology,
                TaskOrchestratorEmailService $emailService,
                CommentMentionDeepLinkBuilder $deepLinkBuilder,
                MentionEligibleUsersProviderInterface $eligibleUsersProvider,
                EventManager $eventManager,
                $fixedRecipient
            ) {
                parent::__construct($ontology, $emailService, $deepLinkBuilder, $eligibleUsersProvider, $eventManager);
                $this->fixedRecipient = $fixedRecipient;
            }

            protected function resolveMentionRecipient(array $mention): ?array
            {
                return $this->fixedRecipient;
            }
        };
    }

    private function comment(string $body): ItemComment
    {
        return new ItemComment(
            'c1',
            'http://example.test/item#1',
            ResourceCommentType::ITEM,
            'author-1',
            'Author',
            $body,
            '2026-09-03T10:00:00+00:00'
        );
    }

    private function createEmailServiceMock(): MockObject
    {
        return $this->createMock(TaskOrchestratorEmailService::class);
    }

    private function createDeepLinkBuilder(): CommentMentionDeepLinkBuilder
    {
        $tree = new Tree(['rootNode' => TaoOntology::CLASS_URI_ITEM, 'name' => 'Items']);
        $section = new Section(
            [
                'id' => 'manage_items',
                'name' => 'Manage items',
                'url' => '/',
                'extension' => 'taoItems',
                'controller' => 'Items',
                'action' => 'index',
                'binding' => null,
                'policy' => Section::POLICY_MERGE,
                'disabled' => false,
            ],
            [$tree],
            []
        );
        $perspective = new Perspective(
            [
                'id' => 'items',
                'extension' => 'taoItems',
                'name' => 'Items',
                'group' => Perspective::GROUP_DEFAULT,
                'level' => '0',
                'description' => '',
                'binding' => null,
                'icon' => null,
            ],
            [$section]
        );

        return new CommentMentionDeepLinkBuilder('https://example.test', [$perspective]);
    }
}
