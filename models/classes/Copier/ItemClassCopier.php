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

namespace oat\taoItems\model\Copier;

use InvalidArgumentException;
use core_kernel_classes_Class;
use oat\tao\model\TaoOntology;
use oat\tao\model\resources\Contract\ClassCopierInterface;

class ItemClassCopier implements ClassCopierInterface
{
    /** @var ClassCopierInterface */
    private $taoClassCopier;

    public function __construct(ClassCopierInterface $taoClassCopier)
    {
        $this->taoClassCopier = $taoClassCopier;
    }

    /**
     * @inheritDoc
     */
    public function copy(
        core_kernel_classes_Class $class,
        core_kernel_classes_Class $destinationClass
    ): core_kernel_classes_Class {
        $this->assertInItemsRootClass($class);

        return $this->taoClassCopier->copy($class, $destinationClass);
    }

    private function assertInItemsRootClass(core_kernel_classes_Class $class): void
    {
        if (
            $class->getUri() !== TaoOntology::CLASS_URI_ITEM
            && !$class->isSubClassOf($class->getClass(TaoOntology::CLASS_URI_ITEM))
        ) {
            throw new InvalidArgumentException(
                sprintf(
                    'Selected class (%s) is not supported because it is not part of the items root class (%s).',
                    $class->getUri(),
                    TaoOntology::CLASS_URI_ITEM
                )
            );
        }
    }
}
