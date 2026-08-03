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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2026 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace oat\taoItems\test\unit\model\Comment;

use common_exception_Unauthorized;
use oat\generis\test\ServiceManagerMockTrait;
use oat\tao\model\featureFlag\FeatureFlagChecker;
use oat\tao\model\featureFlag\FeatureFlagCheckerInterface;
use oat\taoItems\model\Comment\ItemComment;
use oat\taoItems\model\Comment\ItemCommentPersistenceInterface;
use oat\taoItems\model\Comment\ItemCommentPersistenceProxy;
use oat\taoItems\model\Comment\ItemCommentService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ItemCommentServiceTest extends TestCase
{
    use ServiceManagerMockTrait;

    /** @var ItemCommentPersistenceInterface|MockObject */
    private $persistence;

    /** @var FeatureFlagCheckerInterface|MockObject */
    private $featureFlagChecker;

    private ItemCommentService $sut;

    protected function setUp(): void
    {
        $this->persistence = $this->createMock(ItemCommentPersistenceInterface::class);
        $this->featureFlagChecker = $this->createMock(FeatureFlagCheckerInterface::class);

        $this->sut = new ItemCommentService();
        $this->sut->setServiceLocator(
            $this->getServiceManagerMock([
                ItemCommentPersistenceProxy::SERVICE_ID => $this->persistence,
                FeatureFlagChecker::class => $this->featureFlagChecker,
            ])
        );
    }

    public function testListThrowsWhenFeatureFlagDisabled(): void
    {
        $this->featureFlagChecker
            ->method('isEnabled')
            ->with(FeatureFlagCheckerInterface::FEATURE_FLAG_ITEM_COMMENTS_ENABLED)
            ->willReturn(false);

        $this->expectException(common_exception_Unauthorized::class);
        $this->sut->list('http://example.test/item#1');
    }

    public function testListReturnsCommentsWhenEnabled(): void
    {
        $this->featureFlagChecker
            ->method('isEnabled')
            ->willReturn(true);

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
        $this->featureFlagChecker->method('isEnabled')->willReturn(true);
        $this->expectException(\InvalidArgumentException::class);
        $this->sut->count('  ');
    }
}
