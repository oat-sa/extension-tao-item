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
use oat\generis\model\data\Ontology;
use oat\oatbox\user\UserLanguageService;
use oat\tao\model\featureFlag\FeatureFlagCheckerInterface;
use oat\tao\model\TaoOntology;
use oat\taoItems\model\event\ItemCreatedEvent;
use Psr\Log\LoggerInterface;

class ItemCreatedEventListener
{
    private FeatureFlagCheckerInterface $featureFlagChecker;
    private Ontology $ontology;
    private UserLanguageService $userLanguageService;
    private LoggerInterface $logger;

    public function __construct(
        FeatureFlagCheckerInterface $featureFlagChecker,
        Ontology $ontology,
        UserLanguageService $userLanguageService,
        LoggerInterface $logger
    ) {
        $this->featureFlagChecker = $featureFlagChecker;
        $this->ontology = $ontology;
        $this->userLanguageService = $userLanguageService;
        $this->logger = $logger;
    }

    public function populateTranslationProperties(ItemCreatedEvent $event): void
    {
        if (!$this->featureFlagChecker->isEnabled('FEATURE_FLAG_TRANSLATION_ENABLED')) {
            return;
        }

        $item = $this->ontology->getResource($event->getItemUri());

        $this->setLanguage($item);
        $this->setTranslationType($item);
        $this->setTranslationStatus($item);
    }

    private function setLanguage(core_kernel_classes_Resource $item): void
    {
        $translationLanguageProperty = $this->ontology->getProperty(TaoOntology::PROPERTY_LANGUAGE);

        if ($this->isPropertySet($item, $translationLanguageProperty)) {
            return;
        }

        $defaultLanguage = $this->userLanguageService->getDefaultLanguage();
        $item->setPropertyValue($translationLanguageProperty, TaoOntology::LANGUAGE_PREFIX . $defaultLanguage);
    }

    private function setTranslationType(core_kernel_classes_Resource $item): void
    {
        $translationTypeProperty = $this->ontology->getProperty(TaoOntology::PROPERTY_TRANSLATION_TYPE);

        if ($this->isPropertySet($item, $translationTypeProperty)) {
            return;
        }

        $item->setPropertyValue($translationTypeProperty, TaoOntology::PROPERTY_VALUE_TRANSLATION_TYPE_ORIGINAL);
    }

    private function setTranslationStatus(core_kernel_classes_Resource $item): void
    {
        $translationStatusProperty = $this->ontology->getProperty(TaoOntology::PROPERTY_TRANSLATION_STATUS);

        if ($this->isPropertySet($item, $translationStatusProperty)) {
            return;
        }

        $item->setPropertyValue($translationStatusProperty, TaoOntology::PROPERTY_VALUE_TRANSLATION_STATUS_NOT_READY);
    }

    private function isPropertySet(core_kernel_classes_Resource $item, core_kernel_classes_Property $property): bool
    {
        if (empty((string) $item->getOnePropertyValue($property))) {
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
