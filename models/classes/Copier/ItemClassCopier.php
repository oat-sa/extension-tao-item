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

namespace oat\taoItems\model\Copier;

use InvalidArgumentException;
use core_kernel_classes_Class;
use oat\generis\model\data\Ontology;
use oat\tao\model\resources\Command\ResourceTransferCommand;
use oat\tao\model\resources\Contract\ResourceTransferInterface;
use oat\tao\model\resources\ResourceTransferResult;
use oat\tao\model\TaoOntology;
use oat\tao\model\resources\Contract\ClassCopierInterface;

class ItemClassCopier implements ClassCopierInterface, ResourceTransferInterface
{
    /** @var ClassCopierInterface|ResourceTransferInterface */
    private $taoClassCopier;
    private Ontology $ontology;

    public function __construct(ResourceTransferInterface $taoClassCopier, Ontology $ontology)
    {
        $this->taoClassCopier = $taoClassCopier;
        $this->ontology = $ontology;
    }

    public function transfer(ResourceTransferCommand $command): ResourceTransferResult
    {
        $this->assertInItemsRootClass($this->ontology->getClass($command->getFrom()));

        return $this->taoClassCopier->transfer($command);
    }

    public function copy(
        core_kernel_classes_Class $class,
        core_kernel_classes_Class $destinationClass
    ): core_kernel_classes_Class {
        $result = $this->transfer(
            new ResourceTransferCommand(
                $class->getUri(),
                $destinationClass->getUri(),
                ResourceTransferCommand::ACL_KEEP_ORIGINAL,
                ResourceTransferCommand::TRANSFER_MODE_COPY
            )
        );

        return $this->ontology->getClass($result->getDestination());
    }

    private function assertInItemsRootClass(core_kernel_classes_Class $class): void
    {
        $rootClass = $class->getClass(TaoOntology::CLASS_URI_ITEM);

        if (!$class->equals($rootClass) && !$class->isSubClassOf($rootClass)) {
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
