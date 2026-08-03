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

use oat\generis\test\ServiceManagerMockTrait;
use oat\taoItems\model\Comment\ItemComment;
use oat\taoItems\model\Comment\ItemCommentPersistenceInterface;
use oat\taoItems\model\Comment\ItemCommentPersistenceProxy;
use oat\taoItems\model\Comment\RdfItemCommentAdapter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ItemCommentPersistenceProxyTest extends TestCase
{
    use ServiceManagerMockTrait;

    /** @var ItemCommentPersistenceInterface|MockObject */
    private $adapter;

    private ItemCommentPersistenceProxy $sut;

    protected function setUp(): void
    {
        $this->adapter = $this->createMock(ItemCommentPersistenceInterface::class);

        $this->sut = new ItemCommentPersistenceProxy([
            ItemCommentPersistenceProxy::OPTION_ACTIVE_ADAPTER => 'activeAdapterService',
        ]);
        $this->sut->setServiceLocator(
            $this->getServiceManagerMock([
                'activeAdapterService' => $this->adapter,
            ])
        );
    }

    public function testDelegatesCreateToActiveAdapter(): void
    {
        $comment = new ItemComment(
            'c1',
            'item-1',
            'author',
            'Author',
            'body',
            '2026-08-03T10:00:00+00:00'
        );

        $this->adapter
            ->expects($this->once())
            ->method('create')
            ->with($comment)
            ->willReturn($comment);

        $this->assertSame($comment, $this->sut->create($comment));
    }

    public function testDefaultsToRdfAdapterServiceId(): void
    {
        $proxy = new ItemCommentPersistenceProxy();
        $this->assertSame(
            RdfItemCommentAdapter::SERVICE_ID,
            $proxy->getOption(
                ItemCommentPersistenceProxy::OPTION_ACTIVE_ADAPTER,
                RdfItemCommentAdapter::SERVICE_ID
            )
        );
    }
}
