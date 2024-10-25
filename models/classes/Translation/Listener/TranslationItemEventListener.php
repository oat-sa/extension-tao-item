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

namespace oat\taoItems\model\Translation\Listener;

use core_kernel_classes_Property;
use core_kernel_classes_Resource;
use InvalidArgumentException;
use oat\generis\model\data\Ontology;
use oat\oatbox\event\Event;
use oat\tao\model\featureFlag\FeatureFlagCheckerInterface;
use oat\tao\model\TaoOntology;
use oat\tao\model\Translation\Service\ResourceLanguageRetriever;
use oat\tao\model\Translation\Service\TranslationDeletionService;
use oat\taoItems\model\event\ItemCreatedEvent;
use oat\taoItems\model\event\ItemRemovedEvent;
use oat\taoItems\model\event\ItemUpdatedEvent;
use Psr\Log\LoggerInterface;

class TranslationItemEventListener
{
    private FeatureFlagCheckerInterface $featureFlagChecker;
    private Ontology $ontology;
    private ResourceLanguageRetriever $resourceLanguageRetriever;
    private LoggerInterface $logger;
    private TranslationDeletionService $translationDeletionService;

    public function __construct(
        FeatureFlagCheckerInterface $featureFlagChecker,
        Ontology $ontology,
        ResourceLanguageRetriever $resourceLanguageRetriever,
        LoggerInterface $logger,
        TranslationDeletionService $translationDeletionService
    ) {
        $this->featureFlagChecker = $featureFlagChecker;
        $this->ontology = $ontology;
        $this->resourceLanguageRetriever = $resourceLanguageRetriever;
        $this->logger = $logger;
        $this->translationDeletionService = $translationDeletionService;
    }

    public function populateTranslationProperties(Event $event): void
    {
        if (!$this->featureFlagChecker->isEnabled('FEATURE_FLAG_TRANSLATION_ENABLED')) {
            return;
        }

        if (!$event instanceof ItemCreatedEvent && !$event instanceof ItemUpdatedEvent) {
            throw new InvalidArgumentException(
                sprintf('Event %s is not supported to populate translation properties', get_class($event))
            );
        }

        $item = $this->ontology->getResource($event->getItemUri());

        $this->setLanguage($item);
        $this->setTranslationType($item);
        $this->setTranslationStatus($item);
    }

    public function deleteTranslations(ItemRemovedEvent $event): void
    {
        if ($this->featureFlagChecker->isEnabled('FEATURE_FLAG_TRANSLATION_ENABLED')) {
            $this->translationDeletionService
                ->deleteByOriginResourceUri($event->jsonSerialize()[ItemRemovedEvent::PAYLOAD_KEY_ITEM_URI]);
        }
    }

    private function setLanguage(core_kernel_classes_Resource $item): void
    {
        $this->setProperty(
            $item,
            TaoOntology::PROPERTY_LANGUAGE,
            TaoOntology::LANGUAGE_PREFIX . $this->resourceLanguageRetriever->retrieve($item)
        );
    }

    private function setTranslationType(core_kernel_classes_Resource $item): void
    {
        $this->setProperty(
            $item,
            TaoOntology::PROPERTY_TRANSLATION_TYPE,
            TaoOntology::PROPERTY_VALUE_TRANSLATION_TYPE_ORIGINAL
        );
    }

    private function setTranslationStatus(core_kernel_classes_Resource $item): void
    {
        $this->setProperty(
            $item,
            TaoOntology::PROPERTY_TRANSLATION_STATUS,
            TaoOntology::PROPERTY_VALUE_TRANSLATION_STATUS_NOT_READY
        );
    }

    private function setProperty(core_kernel_classes_Resource $item, string $propertyUri, string $value): void
    {
        $property = $this->ontology->getProperty($propertyUri);

        if (!$this->isPropertySet($item, $property)) {
            $item->editPropertyValues($property, $value);
        }
    }

    private function isPropertySet(core_kernel_classes_Resource $item, core_kernel_classes_Property $property): bool
    {
        if (empty($item->getOnePropertyValue($property))) {
            return false;
        }

        $this->logger->info(
            sprintf(
                'The property "%s" for the item "%s" has already been set.',
                $property->getUri(),
                $item->getUri()
            )
        );

        return true;
    }
}
