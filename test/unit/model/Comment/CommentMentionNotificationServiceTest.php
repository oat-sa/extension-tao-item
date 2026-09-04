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
use oat\tao\model\menu\Perspective;
use oat\tao\model\menu\Section;
use oat\tao\model\menu\Tree;
use oat\tao\model\TaskOrchestrator\CommentMentionDeepLinkBuilder;
use oat\tao\model\TaskOrchestrator\CommentMentionEmailTemplatePayload;
use oat\tao\model\TaskOrchestrator\TaskOrchestratorEmailService;
use oat\tao\model\TaoOntology;
use oat\taoItems\model\Comment\CommentMentionNotificationService;
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

        $sut->notifyForComment($this->comment('plain text'), 'Alice', []);
    }

    public function testNotifyOnlyNewMentionsOnUpdate(): void
    {
        $sut = $this->createSutWithRecipient(null);
        $this->emailService->expects($this->never())->method('sendCommentMention');

        $sut->notifyForCommentUpdate(
            $this->comment('<p>Hi @alice</p>'),
            'Alice Author',
            [['id' => 'u1', 'login' => 'alice']],
            [['id' => 'u1', 'login' => 'alice']]
        );
    }

    public function testNotifySendsCommentMentionWithRequiredTemplateData(): void
    {
        $resource = $this->createMock(core_kernel_classes_Resource::class);
        $resource->method('getLabel')->willReturn('Item Label');
        $this->ontology->method('getResource')->willReturn($resource);

        $this->emailService
            ->expects($this->once())
            ->method('sendCommentMention')
            ->with(
                'alice',
                'alice@example.com',
                $this->callback(static function (CommentMentionEmailTemplatePayload $payload): bool {
                    $data = $payload->toTemplateData();

                    return $data['mentionedBy'] === 'Alice Author'
                        && $data['username'] === 'alice'
                        && $data['resourceType'] === ResourceCommentType::ITEM
                        && str_contains($data['resourceUrl'], 'structure=items')
                        && $data['resourceLabel'] === 'Item Label'
                        && $data['name'] === 'Alice Mentioned';
                })
            )
            ->willReturn('job-1');

        $sut = $this->createSutWithRecipient([
            'login' => 'alice',
            'email' => 'alice@example.com',
            'name' => 'Alice Mentioned',
        ]);

        $sut->notifyForComment(
            $this->comment('<p>Hi @alice</p>'),
            'Alice Author',
            [['id' => 'u1', 'login' => 'alice']]
        );
    }

    public function testNotifySkipsRecipientWithoutEmail(): void
    {
        $resource = $this->createMock(core_kernel_classes_Resource::class);
        $resource->method('getLabel')->willReturn('Item Label');
        $this->ontology->method('getResource')->willReturn($resource);

        $sut = $this->createSutWithRecipient(null);
        $this->emailService->expects($this->never())->method('sendCommentMention');

        $sut->notifyForComment(
            $this->comment('<p>Hi @alice</p>'),
            'Alice Author',
            [['id' => 'u1', 'login' => 'alice']]
        );
    }

    private function createSut(): CommentMentionNotificationService
    {
        return new CommentMentionNotificationService(
            $this->ontology,
            $this->emailService,
            $this->createDeepLinkBuilder()
        );
    }

    /**
     * @param array{login: string, email: string, name: ?string}|null|false $recipient
     */
    private function createSutWithRecipient($recipient): CommentMentionNotificationService
    {
        return new class (
            $this->ontology,
            $this->emailService,
            $this->createDeepLinkBuilder(),
            $recipient
        ) extends CommentMentionNotificationService {
            /** @var array{login: string, email: string, name: ?string}|null|false */
            private $fixedRecipient;

            public function __construct(
                Ontology $ontology,
                TaskOrchestratorEmailService $emailService,
                CommentMentionDeepLinkBuilder $deepLinkBuilder,
                $fixedRecipient
            ) {
                parent::__construct($ontology, $emailService, $deepLinkBuilder);
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
