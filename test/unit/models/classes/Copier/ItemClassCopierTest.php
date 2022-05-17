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
 * Copyright (c) 2022 (original work) Open Assessment Technologies SA.
 *
 * @author Andrei Shapiro <andrei.shapiro@taotesting.com>
 */

declare(strict_types=1);

namespace oat\taoItems\test\unit\models\classes\Copier;

use InvalidArgumentException;
use core_kernel_classes_Class;
use oat\tao\model\TaoOntology;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use oat\taoItems\model\Copier\ItemClassCopier;
use oat\tao\model\resources\Contract\ClassCopierInterface;

class ItemClassCopierTest extends TestCase
{
    /** @var ItemClassCopier */
    private $sut;

    /** @var ClassCopierInterface|MockObject */
    private $classCopier;

    protected function setUp(): void
    {
        $this->classCopier = $this->createMock(ClassCopierInterface::class);

        $this->sut = new ItemClassCopier($this->classCopier);
    }

    public function testCopy(): void
    {
        $rootClass = $this->createMock(core_kernel_classes_Class::class);

        $class = $this->createMock(core_kernel_classes_Class::class);
        $class
            ->expects($this->once())
            ->method('getClass')
            ->with(TaoOntology::CLASS_URI_ITEM)
            ->willReturn($rootClass);
        $class
            ->expects($this->once())
            ->method('equals')
            ->with($rootClass)
            ->willReturn(true);
        $class
            ->expects($this->never())
            ->method('isSubClassOf');
        $class
            ->expects($this->never())
            ->method('getUri');

        $destinationClass = $this->createMock(core_kernel_classes_Class::class);
        $newClass = $this->createMock(core_kernel_classes_Class::class);

        $this->classCopier
            ->expects($this->once())
            ->method('copy')
            ->with($class, $destinationClass)
            ->willReturn($newClass);

        $this->assertEquals($newClass, $this->sut->copy($class, $destinationClass));
    }

    public function testCopyInvalidClass(): void
    {
        $rootClass = $this->createMock(core_kernel_classes_Class::class);

        $classUri = 'classUri';

        $class = $this->createMock(core_kernel_classes_Class::class);
        $class
            ->expects($this->once())
            ->method('getClass')
            ->with(TaoOntology::CLASS_URI_ITEM)
            ->willReturn($rootClass);
        $class
            ->expects($this->once())
            ->method('equals')
            ->with($rootClass)
            ->willReturn(false);
        $class
            ->expects($this->once())
            ->method('isSubClassOf')
            ->with($rootClass)
            ->willReturn(false);
        $class
            ->expects($this->once())
            ->method('getUri')
            ->willReturn($classUri);

        $this->classCopier
            ->expects($this->never())
            ->method('copy');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Selected class (%s) is not supported because it is not part of the items root class (%s).',
                $classUri,
                TaoOntology::CLASS_URI_ITEM
            )
        );

        $this->sut->copy($class, $this->createMock(core_kernel_classes_Class::class));
    }
}
