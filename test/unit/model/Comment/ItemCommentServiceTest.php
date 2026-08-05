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

use common_session_Session;
use common_user_User;
use oat\oatbox\session\SessionService;
use oat\tao\model\session\Context\UserDataSessionContext;
use oat\taoItems\model\Comment\ItemComment;
use oat\taoItems\model\Comment\ItemCommentPersistenceInterface;
use oat\taoItems\model\Comment\ItemCommentService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ItemCommentServiceTest extends TestCase
{
    /** @var ItemCommentPersistenceInterface|MockObject */
    private $persistence;

    /** @var SessionService|MockObject */
    private $sessionService;

    private ItemCommentService $sut;

    protected function setUp(): void
    {
        $this->persistence = $this->createMock(ItemCommentPersistenceInterface::class);
        $this->sessionService = $this->createMock(SessionService::class);

        $this->sut = new ItemCommentService(
            $this->persistence,
            $this->sessionService
        );
    }

    public function testListReturnsComments(): void
    {
        $comment = new ItemComment(
            'c1',
            'http://example.test/item#1',
            'user-1',
            'Alice',
            'Hello',
            '2026-08-03T10:00:00+00:00'
        );

        $this->persistence
            ->expects($this->once())
            ->method('findByItemUri')
            ->with('http://example.test/item#1')
            ->willReturn([$comment]);

        $result = $this->sut->list('http://example.test/item#1');

        $this->assertSame(1, $result['count']);
        $this->assertSame('c1', $result['comments'][0]['id']);
        $this->assertSame('Hello', $result['comments'][0]['body']);
    }

    public function testCountRequiresItemUri(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->sut->count('  ');
    }

    public function testCreateUsesLtiUserDataSessionContextForAuthor(): void
    {
        $user = $this->createMock(common_user_User::class);
        $user->method('getIdentifier')->willReturn('https://example.test/ontologies/tao.rdf#superUser');

        $session = $this->createMock(common_session_Session::class);
        $session->method('getUser')->willReturn($user);
        $session->method('getUserLabel')->willReturn('user');
        $session->method('getContexts')
            ->with(UserDataSessionContext::class)
            ->willReturn([
                new UserDataSessionContext('admin', 'admin'),
            ]);

        $this->sessionService
            ->method('getCurrentSession')
            ->willReturn($session);

        $this->persistence
            ->expects($this->once())
            ->method('create')
            ->with($this->callback(static function (ItemComment $comment): bool {
                return $comment->getAuthorId() === 'admin'
                    && $comment->getAuthorLabel() === 'admin'
                    && $comment->getBody() === 'LTI comment'
                    && $comment->getItemUri() === 'http://example.test/item#1';
            }))
            ->willReturnCallback(static function (ItemComment $comment): ItemComment {
                return $comment;
            });

        $created = $this->sut->create('http://example.test/item#1', 'LTI comment');

        $this->assertSame('admin', $created->getAuthorId());
        $this->assertSame('admin', $created->getAuthorLabel());
    }
}
