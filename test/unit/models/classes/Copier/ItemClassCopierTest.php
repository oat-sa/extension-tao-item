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
 * Copyright (c) 2022-2023 (original work) Open Assessment Technologies SA.
 *
 * @author Andrei Shapiro <andrei.shapiro@taotesting.com>
 */

declare(strict_types=1);

namespace oat\taoItems\test\unit\models\classes\Copier;

use InvalidArgumentException;
use core_kernel_classes_Class;
use oat\generis\model\data\Ontology;
use oat\tao\model\resources\Command\ResourceTransferCommand;
use oat\tao\model\resources\Contract\ResourceTransferInterface;
use oat\tao\model\resources\ResourceTransferResult;
use oat\tao\model\TaoOntology;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use oat\taoItems\model\Copier\ItemClassCopier;

class ItemClassCopierTest extends TestCase
{
    private ItemClassCopier $sut;

    /** @var ResourceTransferInterface|MockObject */
    private $classCopier;
    /** @var Ontology|MockObject */
    private $ontology;

    protected function setUp(): void
    {
        $this->classCopier = $this->createMock(ResourceTransferInterface::class);
        $this->ontology = $this->createMock(Ontology::class);

        $this->sut = new ItemClassCopier($this->classCopier, $this->ontology);
    }

    public function testTransfer(): void
    {
        $this->mockClassVerification(true, true);

        $result = new ResourceTransferResult('newClassUri');
        $command = new ResourceTransferCommand(
            'classUri',
            'destinationClassUri',
            ResourceTransferCommand::ACL_KEEP_ORIGINAL,
            ResourceTransferCommand::TRANSFER_MODE_COPY
        );

        $this->classCopier
            ->expects($this->once())
            ->method('transfer')
            ->with($command)
            ->willReturn($result);

        $this->assertEquals($result, $this->sut->transfer($command));
    }

    public function testCopy(): void
    {
        $class = $this->mockClassVerification(true, true);
        $destinationClass = $this->createClass('destinationClassUri');

        $result = new ResourceTransferResult('newClassUri');
        $command = new ResourceTransferCommand(
            'classUri',
            'destinationClassUri',
            ResourceTransferCommand::ACL_KEEP_ORIGINAL,
            ResourceTransferCommand::TRANSFER_MODE_COPY
        );

        $this->classCopier
            ->expects($this->once())
            ->method('transfer')
            ->with($command)
            ->willReturn($result);

        $this->assertSame('newClassUri', $this->sut->copy($class, $destinationClass)->getUri());
    }

    public function testCopyInvalidClass(): void
    {
        $this->mockClassVerification(false, false);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Selected class (%s) is not supported because it is not part of the items root class (%s).',
                'classUri',
                TaoOntology::CLASS_URI_ITEM
            )
        );

        $this->sut->transfer(
            new ResourceTransferCommand(
                'classUri',
                'destinationClassUri',
                ResourceTransferCommand::ACL_KEEP_ORIGINAL,
                ResourceTransferCommand::TRANSFER_MODE_COPY
            )
        );
    }

    private function createClass(string $uri): MockObject
    {
        $class = $this->createMock(core_kernel_classes_Class::class);

        $class->method('getUri')
            ->willReturn($uri);

        return $class;
    }

    private function mockClassVerification(bool $isRootClass, bool $isSubclassOfRoot): MockObject
    {
        $rootClass = $this->createClass(TaoOntology::CLASS_URI_ITEM);
        $class = $this->createClass('classUri');
        $newClass = $this->createClass('newClassUri');

        $class->expects($this->once())
            ->method('getClass')
            ->with(TaoOntology::CLASS_URI_ITEM)
            ->willReturn($rootClass);

        $class->expects($this->once())
            ->method('equals')
            ->with($rootClass)
            ->willReturn($isRootClass);

        $class->method('isSubClassOf')
            ->with($rootClass)
            ->willReturn($isSubclassOfRoot);

        $this->ontology->method('getClass')
            ->willReturnCallback(
                function ($uri) use ($class, $newClass) {
                    if ($uri === 'classUri') {
                        return $class;
                    }

                    if ($uri === 'newClassUri') {
                        return $newClass;
                    }

                    return null;
                }
            );

        return $class;
    }
}
