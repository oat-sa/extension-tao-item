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

use common_exception_Unauthorized;
use common_session_Session;
use common_user_User;
use core_kernel_classes_Resource;
use InvalidArgumentException;
use oat\generis\model\data\Ontology;
use oat\oatbox\session\SessionService;
use oat\tao\model\accessControl\PermissionCheckerInterface;
use oat\tao\model\session\Context\UserDataSessionContext;
use oat\taoItems\model\Comment\ItemComment;
use oat\taoItems\model\Comment\ItemCommentPersistenceInterface;
use oat\taoItems\model\Comment\ItemCommentService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ItemCommentServiceTest extends TestCase
{
    private const ITEM_URI = 'http://example.test/item#1';

    /** @var ItemCommentPersistenceInterface|MockObject */
    private $persistence;

    /** @var SessionService|MockObject */
    private $sessionService;

    /** @var Ontology|MockObject */
    private $ontology;

    /** @var PermissionCheckerInterface|MockObject */
    private $permissionChecker;

    private ItemCommentService $sut;

    protected function setUp(): void
    {
        $this->persistence = $this->createMock(ItemCommentPersistenceInterface::class);
        $this->sessionService = $this->createMock(SessionService::class);
        $this->ontology = $this->createMock(Ontology::class);
        $this->permissionChecker = $this->createMock(PermissionCheckerInterface::class);

        $this->sut = new ItemCommentService(
            $this->persistence,
            $this->sessionService,
            $this->ontology,
            $this->permissionChecker
        );
    }

    public function testListReturnsComments(): void
    {
        $this->configureAuthorizedItem(false);

        $comment = new ItemComment(
            'c1',
            self::ITEM_URI,
            'user-1',
            'Alice',
            'Hello',
            '2026-08-03T10:00:00+00:00'
        );

        $this->persistence
            ->expects($this->once())
            ->method('findByItemUri')
            ->with(self::ITEM_URI)
            ->willReturn([$comment]);

        $result = $this->sut->list(self::ITEM_URI);

        $this->assertSame(1, $result['count']);
        $this->assertSame('c1', $result['comments'][0]['id']);
        $this->assertSame('Hello', $result['comments'][0]['body']);
        $this->assertFalse($result['comments'][0]['edited']);
        $this->assertFalse($result['comments'][0]['resolved']);
    }

    public function testListRejectsEmptyItemUri(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('itemUri is required');

        $this->sut->list('   ');
    }

    public function testListRejectsUnknownItem(): void
    {
        $resource = $this->createMock(core_kernel_classes_Resource::class);
        $resource->method('exists')->willReturn(false);

        $this->ontology
            ->expects($this->once())
            ->method('getResource')
            ->with(self::ITEM_URI)
            ->willReturn($resource);

        $this->persistence->expects($this->never())->method('findByItemUri');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Item not found');

        $this->sut->list(self::ITEM_URI);
    }

    public function testListRejectsUnauthorizedItem(): void
    {
        $this->configureItemExists();
        $this->permissionChecker
            ->expects($this->once())
            ->method('hasReadAccess')
            ->with(self::ITEM_URI)
            ->willReturn(false);

        $this->persistence->expects($this->never())->method('findByItemUri');

        $this->expectException(common_exception_Unauthorized::class);

        $this->sut->list(self::ITEM_URI);
    }

    public function testCreateUsesLtiUserNameAsAuthorLabel(): void
    {
        $this->configureAuthorizedItem(true);
        $this->configureLtiSession(new UserDataSessionContext('admin', 'adminLogin', 'Alice Admin'));

        $this->persistence
            ->expects($this->once())
            ->method('create')
            ->with($this->callback(static function (ItemComment $comment): bool {
                return $comment->getAuthorId() === 'admin'
                    && $comment->getAuthorLabel() === 'Alice Admin'
                    && $comment->getBody() === 'LTI comment'
                    && $comment->getItemUri() === self::ITEM_URI;
            }))
            ->willReturnCallback(static function (ItemComment $comment): ItemComment {
                return $comment;
            });

        $created = $this->sut->create(self::ITEM_URI, 'LTI comment');

        $this->assertSame('admin', $created->getAuthorId());
        $this->assertSame('Alice Admin', $created->getAuthorLabel());
        $this->assertFalse($created->isEdited());
        $this->assertFalse($created->isResolved());
    }

    public function testCreateFallsBackToUserLoginWhenUserNameIsNull(): void
    {
        $this->configureAuthorizedItem(true);
        $this->configureLtiSession(new UserDataSessionContext('admin', 'adminLogin'));

        $this->persistence
            ->expects($this->once())
            ->method('create')
            ->with($this->callback(static function (ItemComment $comment): bool {
                return $comment->getAuthorId() === 'admin'
                    && $comment->getAuthorLabel() === 'adminLogin';
            }))
            ->willReturnCallback(static function (ItemComment $comment): ItemComment {
                return $comment;
            });

        $created = $this->sut->create(self::ITEM_URI, 'LTI comment');

        $this->assertSame('adminLogin', $created->getAuthorLabel());
    }

    public function testCreateFallsBackToUserLoginWhenUserNameIsEmpty(): void
    {
        $this->configureAuthorizedItem(true);
        $this->configureLtiSession(new UserDataSessionContext('admin', 'adminLogin', ''));

        $this->persistence
            ->expects($this->once())
            ->method('create')
            ->with($this->callback(static function (ItemComment $comment): bool {
                return $comment->getAuthorId() === 'admin'
                    && $comment->getAuthorLabel() === 'adminLogin';
            }))
            ->willReturnCallback(static function (ItemComment $comment): ItemComment {
                return $comment;
            });

        $created = $this->sut->create(self::ITEM_URI, 'LTI comment');

        $this->assertSame('adminLogin', $created->getAuthorLabel());
    }

    public function testCreateRejectsEmptyBody(): void
    {
        $this->configureAuthorizedItem(true);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Comment body must not be empty');

        $this->sut->create(self::ITEM_URI, '   ');
    }

    public function testCreateFailsWithoutAuthenticatedUser(): void
    {
        $this->configureAuthorizedItem(true);

        $this->sessionService
            ->method('getCurrentSession')
            ->willReturn(null);

        $this->persistence->expects($this->never())->method('create');

        $this->expectException(common_exception_Unauthorized::class);
        $this->expectExceptionMessage('Authenticated session required to create item comments');

        $this->sut->create(self::ITEM_URI, 'hello');
    }

    private function configureAuthorizedItem(bool $requireWriteAccess): void
    {
        $this->configureItemExists();

        if ($requireWriteAccess) {
            $this->permissionChecker
                ->method('hasWriteAccess')
                ->with(self::ITEM_URI)
                ->willReturn(true);
        } else {
            $this->permissionChecker
                ->method('hasReadAccess')
                ->with(self::ITEM_URI)
                ->willReturn(true);
        }
    }

    private function configureItemExists(): void
    {
        $resource = $this->createMock(core_kernel_classes_Resource::class);
        $resource->method('exists')->willReturn(true);

        $this->ontology
            ->method('getResource')
            ->with(self::ITEM_URI)
            ->willReturn($resource);
    }

    private function configureLtiSession(UserDataSessionContext $context): void
    {
        $user = $this->createMock(common_user_User::class);
        $user->method('getIdentifier')->willReturn('https://example.test/ontologies/tao.rdf#superUser');

        $session = $this->createMock(common_session_Session::class);
        $session->method('getUser')->willReturn($user);
        $session->method('getUserLabel')->willReturn('user');
        $session->method('getContexts')
            ->with(UserDataSessionContext::class)
            ->willReturn([$context]);

        $this->sessionService
            ->method('getCurrentSession')
            ->willReturn($session);
    }
}
