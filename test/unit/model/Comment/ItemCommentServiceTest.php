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
use core_kernel_classes_Class;
use core_kernel_classes_Resource;
use InvalidArgumentException;
use oat\generis\model\data\Ontology;
use oat\oatbox\session\SessionService;
use oat\tao\model\accessControl\PermissionCheckerInterface;
use oat\tao\model\session\Context\UserDataSessionContext;
use oat\tao\model\TaoOntology;
use oat\taoItems\model\Comment\ItemComment;
use oat\taoItems\model\Comment\ItemCommentPersistenceInterface;
use oat\taoItems\model\Comment\ItemCommentService;
use oat\taoItems\model\Comment\ResourceCommentType;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ItemCommentServiceTest extends TestCase
{
    private const RESOURCE_URI = 'http://example.test/item#1';

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
        $this->configureAuthorizedResource(false);

        $comment = new ItemComment(
            'c1',
            self::RESOURCE_URI,
            ResourceCommentType::ITEM,
            'user-1',
            'Alice',
            'Hello',
            '2026-08-03T10:00:00+00:00'
        );

        $this->persistence
            ->expects($this->once())
            ->method('findByResource')
            ->with(self::RESOURCE_URI, ResourceCommentType::ITEM)
            ->willReturn([$comment]);

        $result = $this->sut->list(self::RESOURCE_URI, ResourceCommentType::ITEM);

        $this->assertSame(1, $result['count']);
        $this->assertSame('c1', $result['comments'][0]['id']);
        $this->assertSame(self::RESOURCE_URI, $result['comments'][0]['resourceUri']);
        $this->assertSame(ResourceCommentType::ITEM, $result['comments'][0]['resourceType']);
        $this->assertSame('Hello', $result['comments'][0]['body']);
        $this->assertFalse($result['comments'][0]['edited']);
        $this->assertFalse($result['comments'][0]['resolved']);
    }

    public function testListRejectsEmptyResourceUri(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('resourceUri is required');

        $this->sut->list('   ', ResourceCommentType::ITEM);
    }

    public function testListRejectsInvalidResourceType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('resourceType must be one of');

        $this->sut->list(self::RESOURCE_URI, 'delivery');
    }

    public function testListRejectsUnknownResource(): void
    {
        $resource = $this->createMock(core_kernel_classes_Resource::class);
        $resource->method('exists')->willReturn(false);

        $this->ontology
            ->expects($this->once())
            ->method('getResource')
            ->with(self::RESOURCE_URI)
            ->willReturn($resource);

        $this->persistence->expects($this->never())->method('findByResource');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Resource not found');

        $this->sut->list(self::RESOURCE_URI, ResourceCommentType::ITEM);
    }

    public function testListRejectsResourceTypeMismatch(): void
    {
        $resource = $this->createMock(core_kernel_classes_Resource::class);
        $resource->method('exists')->willReturn(true);
        $resource->method('isInstanceOf')->willReturn(false);

        $expectedClass = $this->createMock(core_kernel_classes_Class::class);

        $this->ontology
            ->method('getResource')
            ->with(self::RESOURCE_URI)
            ->willReturn($resource);
        $this->ontology
            ->method('getClass')
            ->with(TaoOntology::CLASS_URI_ITEM)
            ->willReturn($expectedClass);

        $this->persistence->expects($this->never())->method('findByResource');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf('Resource type does not match resourceType "%s"', ResourceCommentType::ITEM)
        );

        $this->sut->list(self::RESOURCE_URI, ResourceCommentType::ITEM);
    }

    public function testListRejectsUnauthorizedResource(): void
    {
        $this->configureResourceExistsMatchingType();
        $this->permissionChecker
            ->expects($this->once())
            ->method('hasReadAccess')
            ->with(self::RESOURCE_URI)
            ->willReturn(false);

        $this->persistence->expects($this->never())->method('findByResource');

        $this->expectException(common_exception_Unauthorized::class);

        $this->sut->list(self::RESOURCE_URI, ResourceCommentType::ITEM);
    }

    public function testCreateUsesLtiUserNameAsAuthorLabel(): void
    {
        $this->configureAuthorizedResource(true);
        $this->configureLtiSession(new UserDataSessionContext('admin', 'adminLogin', 'Alice Admin'));

        $this->persistence
            ->expects($this->once())
            ->method('create')
            ->with($this->callback(static function (ItemComment $comment): bool {
                return $comment->getAuthorId() === 'admin'
                    && $comment->getAuthorLabel() === 'Alice Admin'
                    && $comment->getBody() === 'LTI comment'
                    && $comment->getResourceUri() === self::RESOURCE_URI
                    && $comment->getResourceType() === ResourceCommentType::ITEM;
            }))
            ->willReturnCallback(static function (ItemComment $comment): ItemComment {
                return $comment;
            });

        $created = $this->sut->create(self::RESOURCE_URI, ResourceCommentType::ITEM, 'LTI comment');

        $this->assertSame('admin', $created->getAuthorId());
        $this->assertSame('Alice Admin', $created->getAuthorLabel());
        $this->assertFalse($created->isEdited());
        $this->assertFalse($created->isResolved());
    }

    public function testCreateFallsBackToUserLoginWhenUserNameIsNull(): void
    {
        $this->configureAuthorizedResource(true);
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

        $created = $this->sut->create(self::RESOURCE_URI, ResourceCommentType::ITEM, 'LTI comment');

        $this->assertSame('adminLogin', $created->getAuthorLabel());
    }

    public function testCreateFallsBackToUserLoginWhenUserNameIsEmpty(): void
    {
        $this->configureAuthorizedResource(true);
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

        $created = $this->sut->create(self::RESOURCE_URI, ResourceCommentType::ITEM, 'LTI comment');

        $this->assertSame('adminLogin', $created->getAuthorLabel());
    }

    public function testCreateRejectsEmptyBody(): void
    {
        $this->configureAuthorizedResource(true);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Comment body must not be empty');

        $this->sut->create(self::RESOURCE_URI, ResourceCommentType::ITEM, '   ');
    }

    public function testCreateFailsWithoutAuthenticatedUser(): void
    {
        $this->configureAuthorizedResource(true);

        $this->sessionService
            ->method('getCurrentSession')
            ->willReturn(null);

        $this->persistence->expects($this->never())->method('create');

        $this->expectException(common_exception_Unauthorized::class);
        $this->expectExceptionMessage('Authenticated session required to create item comments');

        $this->sut->create(self::RESOURCE_URI, ResourceCommentType::ITEM, 'hello');
    }

    public function testUpdateEditsOwnComment(): void
    {
        $this->configureAuthorizedResource(true);
        $this->configureLtiSession(new UserDataSessionContext('admin', 'adminLogin', 'Alice Admin'));

        $existing = new ItemComment(
            'c1',
            self::RESOURCE_URI,
            ResourceCommentType::ITEM,
            'admin',
            'Alice Admin',
            'Old body',
            '2026-08-03T10:00:00+00:00'
        );

        $this->persistence
            ->expects($this->once())
            ->method('findById')
            ->with('c1')
            ->willReturn($existing);

        $this->persistence
            ->expects($this->once())
            ->method('update')
            ->with($this->callback(static function (ItemComment $comment): bool {
                return $comment->getId() === 'c1'
                    && $comment->getBody() === 'New body'
                    && $comment->isEdited()
                    && $comment->getResourceType() === ResourceCommentType::ITEM;
            }))
            ->willReturnCallback(static function (ItemComment $comment): ItemComment {
                return $comment;
            });

        $updated = $this->sut->update('c1', 'New body');

        $this->assertSame('New body', $updated->getBody());
        $this->assertTrue($updated->isEdited());
    }

    public function testUpdateRejectsNonAuthor(): void
    {
        $this->configureAuthorizedResource(true);
        $this->configureLtiSession(new UserDataSessionContext('admin', 'adminLogin', 'Alice Admin'));

        $existing = new ItemComment(
            'c1',
            self::RESOURCE_URI,
            ResourceCommentType::ITEM,
            'someone-else',
            'Bob',
            'Old body',
            '2026-08-03T10:00:00+00:00'
        );

        $this->persistence
            ->expects($this->once())
            ->method('findById')
            ->with('c1')
            ->willReturn($existing);

        $this->persistence->expects($this->never())->method('update');

        $this->expectException(common_exception_Unauthorized::class);

        $this->sut->update('c1', 'New body');
    }

    public function testListMarksOwnCommentsEditable(): void
    {
        $this->configureAuthorizedResource(false);
        $this->configureLtiSession(new UserDataSessionContext('admin', 'adminLogin', 'Alice Admin'));

        $own = new ItemComment(
            'c1',
            self::RESOURCE_URI,
            ResourceCommentType::ITEM,
            'admin',
            'Alice Admin',
            'Mine',
            '2026-08-03T10:00:00+00:00'
        );
        $other = new ItemComment(
            'c2',
            self::RESOURCE_URI,
            ResourceCommentType::ITEM,
            'other',
            'Bob',
            'Theirs',
            '2026-08-03T11:00:00+00:00'
        );

        $this->persistence
            ->expects($this->once())
            ->method('findByResource')
            ->willReturn([$own, $other]);

        $result = $this->sut->list(self::RESOURCE_URI, ResourceCommentType::ITEM);

        $this->assertTrue($result['comments'][0]['editable']);
        $this->assertTrue($result['comments'][0]['deletable']);
        $this->assertFalse($result['comments'][1]['editable']);
        $this->assertFalse($result['comments'][1]['deletable']);
    }

    public function testListMarksResolvedOwnCommentNotEditableButDeletable(): void
    {
        $this->configureAuthorizedResource(false);
        $this->configureLtiSession(new UserDataSessionContext('admin', 'adminLogin', 'Alice Admin'));

        $resolvedOwn = new ItemComment(
            'c1',
            self::RESOURCE_URI,
            ResourceCommentType::ITEM,
            'admin',
            'Alice Admin',
            'Mine',
            '2026-08-03T10:00:00+00:00',
            false,
            true
        );

        $this->persistence
            ->expects($this->once())
            ->method('findByResource')
            ->willReturn([$resolvedOwn]);

        $result = $this->sut->list(self::RESOURCE_URI, ResourceCommentType::ITEM);

        $this->assertFalse($result['comments'][0]['editable']);
        $this->assertTrue($result['comments'][0]['deletable']);
        $this->assertTrue($result['comments'][0]['resolved']);
    }

    public function testListMarksResolvedOtherCommentNotEditableNorDeletable(): void
    {
        $this->configureAuthorizedResource(false);
        $this->configureLtiSession(new UserDataSessionContext('admin', 'adminLogin', 'Alice Admin'));

        $resolvedOther = new ItemComment(
            'c1',
            self::RESOURCE_URI,
            ResourceCommentType::ITEM,
            'someone-else',
            'Bob',
            'Theirs',
            '2026-08-03T10:00:00+00:00',
            false,
            true
        );

        $this->persistence
            ->expects($this->once())
            ->method('findByResource')
            ->willReturn([$resolvedOther]);

        $result = $this->sut->list(self::RESOURCE_URI, ResourceCommentType::ITEM);

        $this->assertFalse($result['comments'][0]['editable']);
        $this->assertFalse($result['comments'][0]['deletable']);
        $this->assertTrue($result['comments'][0]['resolved']);
    }

    public function testUpdateRejectsResolvedComment(): void
    {
        $this->configureAuthorizedResource(true);
        $this->configureLtiSession(new UserDataSessionContext('admin', 'adminLogin', 'Alice Admin'));

        $existing = new ItemComment(
            'c1',
            self::RESOURCE_URI,
            ResourceCommentType::ITEM,
            'admin',
            'Alice Admin',
            'Old body',
            '2026-08-03T10:00:00+00:00',
            false,
            true
        );

        $this->persistence
            ->expects($this->once())
            ->method('findById')
            ->with('c1')
            ->willReturn($existing);

        $this->persistence->expects($this->never())->method('update');

        $this->expectException(common_exception_Unauthorized::class);
        $this->expectExceptionMessage('Resolved comments cannot be edited until reopened');

        $this->sut->update('c1', 'New body');
    }

    public function testResolveMarksCommentResolved(): void
    {
        $this->configureAuthorizedResource(true);
        $this->configureLtiSession(new UserDataSessionContext('admin', 'adminLogin', 'Alice Admin'));

        $existing = new ItemComment(
            'c1',
            self::RESOURCE_URI,
            ResourceCommentType::ITEM,
            'someone-else',
            'Bob',
            'Body',
            '2026-08-03T10:00:00+00:00'
        );

        $this->persistence
            ->expects($this->once())
            ->method('findById')
            ->with('c1')
            ->willReturn($existing);

        $this->persistence
            ->expects($this->once())
            ->method('update')
            ->with($this->callback(static function (ItemComment $comment): bool {
                return $comment->getId() === 'c1' && $comment->isResolved();
            }))
            ->willReturnCallback(static function (ItemComment $comment): ItemComment {
                return $comment;
            });

        $resolved = $this->sut->resolve('c1', true);

        $this->assertTrue($resolved->isResolved());
    }

    public function testResolveIsIdempotentWhenAlreadyResolved(): void
    {
        $this->configureAuthorizedResource(true);
        $this->configureLtiSession(new UserDataSessionContext('admin', 'adminLogin', 'Alice Admin'));

        $existing = new ItemComment(
            'c1',
            self::RESOURCE_URI,
            ResourceCommentType::ITEM,
            'admin',
            'Alice Admin',
            'Body',
            '2026-08-03T10:00:00+00:00',
            false,
            true
        );

        $this->persistence
            ->expects($this->once())
            ->method('findById')
            ->with('c1')
            ->willReturn($existing);

        $this->persistence->expects($this->never())->method('update');

        $resolved = $this->sut->resolve('c1', true);

        $this->assertTrue($resolved->isResolved());
    }

    public function testDeleteRemovesOwnComment(): void
    {
        $this->configureAuthorizedResource(true);
        $this->configureLtiSession(new UserDataSessionContext('admin', 'adminLogin', 'Alice Admin'));

        $existing = new ItemComment(
            'c1',
            self::RESOURCE_URI,
            ResourceCommentType::ITEM,
            'admin',
            'Alice Admin',
            'Body',
            '2026-08-03T10:00:00+00:00'
        );

        $this->persistence
            ->expects($this->once())
            ->method('findById')
            ->with('c1')
            ->willReturn($existing);

        $this->persistence
            ->expects($this->once())
            ->method('delete')
            ->with('c1');

        $this->sut->delete('c1');
    }

    public function testDeleteRejectsNonAuthor(): void
    {
        $this->configureAuthorizedResource(true);
        $this->configureLtiSession(new UserDataSessionContext('admin', 'adminLogin', 'Alice Admin'));

        $existing = new ItemComment(
            'c1',
            self::RESOURCE_URI,
            ResourceCommentType::ITEM,
            'someone-else',
            'Bob',
            'Body',
            '2026-08-03T10:00:00+00:00'
        );

        $this->persistence
            ->expects($this->once())
            ->method('findById')
            ->with('c1')
            ->willReturn($existing);

        $this->persistence->expects($this->never())->method('delete');

        $this->expectException(common_exception_Unauthorized::class);

        $this->sut->delete('c1');
    }

    public function testDeleteRemovesOwnResolvedComment(): void
    {
        $this->configureAuthorizedResource(true);
        $this->configureLtiSession(new UserDataSessionContext('admin', 'adminLogin', 'Alice Admin'));

        $existing = new ItemComment(
            'c1',
            self::RESOURCE_URI,
            ResourceCommentType::ITEM,
            'admin',
            'Alice Admin',
            'Body',
            '2026-08-03T10:00:00+00:00',
            false,
            true
        );

        $this->persistence
            ->expects($this->once())
            ->method('findById')
            ->with('c1')
            ->willReturn($existing);

        $this->persistence
            ->expects($this->once())
            ->method('delete')
            ->with('c1');

        $this->sut->delete('c1');
    }

    public function testDeleteRejectsNonAuthorResolvedComment(): void
    {
        $this->configureAuthorizedResource(true);
        $this->configureLtiSession(new UserDataSessionContext('admin', 'adminLogin', 'Alice Admin'));

        $existing = new ItemComment(
            'c1',
            self::RESOURCE_URI,
            ResourceCommentType::ITEM,
            'someone-else',
            'Bob',
            'Body',
            '2026-08-03T10:00:00+00:00',
            false,
            true
        );

        $this->persistence
            ->expects($this->once())
            ->method('findById')
            ->with('c1')
            ->willReturn($existing);

        $this->persistence->expects($this->never())->method('delete');

        $this->expectException(common_exception_Unauthorized::class);

        $this->sut->delete('c1');
    }

    private function configureAuthorizedResource(bool $requireWriteAccess): void
    {
        $this->configureResourceExistsMatchingType();

        if ($requireWriteAccess) {
            $this->permissionChecker
                ->method('hasWriteAccess')
                ->with(self::RESOURCE_URI)
                ->willReturn(true);
        } else {
            $this->permissionChecker
                ->method('hasReadAccess')
                ->with(self::RESOURCE_URI)
                ->willReturn(true);
        }
    }

    private function configureResourceExistsMatchingType(): void
    {
        $resource = $this->createMock(core_kernel_classes_Resource::class);
        $resource->method('exists')->willReturn(true);
        $resource->method('isInstanceOf')->willReturn(true);

        $expectedClass = $this->createMock(core_kernel_classes_Class::class);

        $this->ontology
            ->method('getResource')
            ->with(self::RESOURCE_URI)
            ->willReturn($resource);
        $this->ontology
            ->method('getClass')
            ->with(TaoOntology::CLASS_URI_ITEM)
            ->willReturn($expectedClass);
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
