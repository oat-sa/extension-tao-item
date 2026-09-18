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
use oat\taoItems\model\Comment\CommentMentionNotificationService;
use oat\taoItems\model\Comment\ItemComment;
use oat\taoItems\model\Comment\ResourceCommentType;
use oat\taoItems\model\event\CommentMentionNotificationRequestedEvent;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CommentMentionNotificationServiceTest extends TestCase
{
    private Ontology|MockObject $ontology;
    private EventManager|MockObject $eventManager;

    protected function setUp(): void
    {
        $this->ontology = $this->createMock(Ontology::class);
        $this->eventManager = $this->createMock(EventManager::class);
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

    public function testNotifySendsCommentMentionWithRequiredPayloadData(): void
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

                    return $payload['mentionedBy'] === 'Alice Author'
                        && $payload['username'] === 'alice'
                        && $payload['resourceType'] === ResourceCommentType::ITEM
                        && $payload['resourceUri'] === 'http://example.test/item#1'
                        && $payload['resourceLabel'] === 'Item Label'
                        && $payload['name'] === 'Alice Mentioned';
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

    public function testNotifyDoesNotApplyEligibilityFiltering(): void
    {
        $resource = $this->createMock(core_kernel_classes_Resource::class);
        $resource->method('getLabel')->willReturn('Item Label');
        $this->ontology->method('getResource')->willReturn($resource);

        $this->eventManager
            ->expects($this->once())
            ->method('trigger')
            ->with($this->isInstanceOf(CommentMentionNotificationRequestedEvent::class))
            ->willReturn(null);

        $sut = new class (
            $this->ontology,
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
                EventManager $eventManager,
                $fixedRecipient
            ) {
                parent::__construct($ontology, $eventManager);
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

                    return $payload['username'] === 'rdf-login';
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
            $this->eventManager,
            $recipient
        ) extends CommentMentionNotificationService {
            /** @var array{login: string, email: string, name: ?string}|null */
            private $fixedRecipient;

            public function __construct(
                Ontology $ontology,
                EventManager $eventManager,
                $fixedRecipient
            ) {
                parent::__construct($ontology, $eventManager);
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
}
