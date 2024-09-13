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
 * Copyright (c) 2024 (original work) Open Assessment Technologies SA.
 */

declare(strict_types=1);

namespace oat\taoItems\test\unit\models\classes\Translation\Listener;

use core_kernel_classes_Property;
use core_kernel_classes_Resource;
use oat\generis\model\data\Ontology;
use oat\tao\model\featureFlag\FeatureFlagCheckerInterface;
use oat\tao\model\TaoOntology;
use oat\taoItems\model\event\ItemCreatedEvent;
use oat\taoItems\model\Translation\Listener\ItemCreatedEventListener;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class ItemCreatedEventListenerTest extends TestCase
{
    /** @var ItemCreatedEvent|MockObject */
    private ItemCreatedEvent $itemCreatedEvent;

    /** @var core_kernel_classes_Resource|MockObject */
    private core_kernel_classes_Resource $item;

    /** @var core_kernel_classes_Property|MockObject */
    private core_kernel_classes_Property $property;

    /** @var FeatureFlagCheckerInterface|MockObject */
    private FeatureFlagCheckerInterface $featureFlagChecker;

    /** @var Ontology|MockObject */
    private Ontology $ontology;

    /** @var LoggerInterface|MockObject */
    private LoggerInterface $logger;

    private ItemCreatedEventListener $sut;

    protected function setUp(): void
    {
        $this->itemCreatedEvent = $this->createMock(ItemCreatedEvent::class);
        $this->item = $this->createMock(core_kernel_classes_Resource::class);
        $this->property = $this->createMock(core_kernel_classes_Property::class);

        $this->featureFlagChecker = $this->createMock(FeatureFlagCheckerInterface::class);
        $this->ontology = $this->createMock(Ontology::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->sut = new ItemCreatedEventListener($this->featureFlagChecker, $this->ontology, $this->logger);
    }

    public function testPopulateTranslationPropertiesTranslationDisabled(): void
    {
        $this->featureFlagChecker
            ->expects($this->once())
            ->method('isEnabled')
            ->with('FEATURE_TRANSLATION_ENABLED')
            ->willReturn(false);

        $this->ontology
            ->expects($this->never())
            ->method($this->anything());
        $this->logger
            ->expects($this->never())
            ->method($this->anything());
        $this->itemCreatedEvent
            ->expects($this->never())
            ->method($this->anything());
        $this->item
            ->expects($this->never())
            ->method($this->anything());

        $this->sut->populateTranslationProperties($this->itemCreatedEvent);
    }

    public function testPopulateTranslationProperties(): void
    {
        $this->featureFlagChecker
            ->expects($this->once())
            ->method('isEnabled')
            ->with('FEATURE_TRANSLATION_ENABLED')
            ->willReturn(true);

        $this->ontology
            ->expects($this->once())
            ->method('getProperty')
            ->with(TaoOntology::PROPERTY_TRANSLATION_TYPE)
            ->willReturn($this->property);

        $this->itemCreatedEvent
            ->expects($this->once())
            ->method('getItemUri')
            ->willReturn('itemUri');

        $this->ontology
            ->expects($this->once())
            ->method('getResource')
            ->with('itemUri')
            ->willReturn($this->item);

        $this->item
            ->expects($this->once())
            ->method('getOnePropertyValue')
            ->with($this->property)
            ->willReturn(null);

        $this->logger
            ->expects($this->never())
            ->method('info');

        $this->item
            ->expects($this->once())
            ->method('setPropertyValue')
            ->with($this->property, TaoOntology::PROPERTY_VALUE_TRANSLATION_TYPE_ORIGINAL);

        $this->sut->populateTranslationProperties($this->itemCreatedEvent);
    }

    public function testPopulateTranslationPropertiesValueSet(): void
    {
        $this->featureFlagChecker
            ->expects($this->once())
            ->method('isEnabled')
            ->with('FEATURE_TRANSLATION_ENABLED')
            ->willReturn(true);

        $this->ontology
            ->expects($this->once())
            ->method('getProperty')
            ->with(TaoOntology::PROPERTY_TRANSLATION_TYPE)
            ->willReturn($this->property);

        $this->itemCreatedEvent
            ->expects($this->once())
            ->method('getItemUri')
            ->willReturn('itemUri');

        $this->ontology
            ->expects($this->once())
            ->method('getResource')
            ->with('itemUri')
            ->willReturn($this->item);

        $this->item
            ->expects($this->once())
            ->method('getOnePropertyValue')
            ->with($this->property)
            ->willReturn('propertyValue');

        $this->logger
            ->expects($this->once())
            ->method('info');

        $this->item
            ->expects($this->never())
            ->method('setPropertyValue');

        $this->sut->populateTranslationProperties($this->itemCreatedEvent);
    }
}
