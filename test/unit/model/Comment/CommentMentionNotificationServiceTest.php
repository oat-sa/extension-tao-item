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
use oat\tao\model\TaskOrchestrator\CommentMentionDeepLinkBuilder;
use oat\tao\model\TaskOrchestrator\CommentMentionEmailPayload;
use oat\tao\model\TaskOrchestrator\TaskOrchestratorEmailService;
use oat\taoItems\model\Comment\CommentMentionNotificationService;
use oat\taoItems\model\Comment\CommentMentionParser;
use oat\taoItems\model\Comment\ItemComment;
use oat\taoItems\model\Comment\ResourceCommentType;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CommentMentionNotificationServiceTest extends TestCase
{
    private Ontology|MockObject $ontology;
    private TaskOrchestratorEmailService|MockObject $emailService;

    protected function setUp(): void
    {
        $this->ontology = $this->createMock(Ontology::class);
        $this->emailService = $this->createMock(TaskOrchestratorEmailService::class);
    }

    public function testNotifySkipsWhenNoMentions(): void
    {
        $sut = $this->createSut();
        $this->emailService->expects($this->never())->method('sendCommentMention');

        $stats = $sut->notifyForComment(
            $this->comment('plain text'),
            'Author'
        );

        $this->assertSame(
            ['initiated' => 0, 'skippedNoEmail' => 0, 'failed' => 0],
            $stats
        );
    }

    public function testNotifyOnlyNewMentionsOnUpdate(): void
    {
        $sut = $this->createSut();
        $this->emailService->expects($this->never())->method('sendCommentMention');

        $html = '<span class="comment-mention" data-user-id="http://u#1" data-user-login="alice">@alice</span>';
        $previous = [['id' => 'http://u#1', 'login' => 'alice']];

        $stats = $sut->notifyForComment($this->comment($html), 'Author', $previous);

        $this->assertSame(0, $stats['initiated']);
    }

    public function testNotifySendsCommentMentionWithRequiredTemplateData(): void
    {
        $resource = $this->createMock(core_kernel_classes_Resource::class);
        $resource->method('getLabel')->willReturn('Item ABC');

        $this->ontology
            ->expects($this->once())
            ->method('getResource')
            ->with('http://example.test/item#1')
            ->willReturn($resource);

        $this->emailService
            ->expects($this->once())
            ->method('sendCommentMention')
            ->with(
                'alice',
                'alice@example.com',
                $this->callback(static function (CommentMentionEmailPayload $payload): bool {
                    return $payload->toTemplateData() === [
                        'mentionedBy' => 'Alice Author',
                        'username' => 'alice',
                        'resourceType' => ResourceCommentType::ITEM,
                        'resourceUrl' => 'https://example.test/tao/Main/index?structure=items&ext=taoItems&section=manage_items&uri=http%3A%2F%2Fexample.test%2Fitem%231',
                        'resourceLabel' => 'Item ABC',
                        'name' => 'Alice Mentioned',
                    ];
                })
            )
            ->willReturn('job-1');

        $html = '<span class="comment-mention" data-user-id="http://u#1" data-user-login="alice">@alice</span>';
        $sut = $this->createSutWithRecipient([
            'login' => 'alice',
            'email' => 'alice@example.com',
            'name' => 'Alice Mentioned',
        ]);

        $stats = $sut->notifyForComment($this->comment($html), 'Alice Author');

        $this->assertSame(
            ['initiated' => 1, 'skippedNoEmail' => 0, 'failed' => 0],
            $stats
        );
    }

    private function createSut(): CommentMentionNotificationService
    {
        return new CommentMentionNotificationService(
            new CommentMentionParser(),
            $this->ontology,
            $this->emailService,
            new CommentMentionDeepLinkBuilder('https://example.test')
        );
    }

    /**
     * @param array{login: string, email: string, name: ?string}|null|false $recipient
     */
    private function createSutWithRecipient($recipient): CommentMentionNotificationService
    {
        return new class (
            new CommentMentionParser(),
            $this->ontology,
            $this->emailService,
            new CommentMentionDeepLinkBuilder('https://example.test'),
            $recipient
        ) extends CommentMentionNotificationService {
            /** @var array{login: string, email: string, name: ?string}|null|false */
            private $fixedRecipient;

            public function __construct(
                CommentMentionParser $parser,
                Ontology $ontology,
                TaskOrchestratorEmailService $emailService,
                CommentMentionDeepLinkBuilder $deepLinkBuilder,
                $fixedRecipient
            ) {
                parent::__construct($parser, $ontology, $emailService, $deepLinkBuilder);
                $this->fixedRecipient = $fixedRecipient;
            }

            protected function resolveMentionRecipient(array $mention)
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
