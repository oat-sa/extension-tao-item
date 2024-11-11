<?php

declare(strict_types=1);

namespace oat\taoItems\migrations;

use Doctrine\DBAL\Schema\Schema;
use oat\oatbox\event\EventManager;
use oat\oatbox\reporting\Report;
use oat\tao\model\accessControl\func\AccessRule;
use oat\tao\model\accessControl\func\AclProxy;
use oat\tao\model\menu\SectionVisibilityFilter;
use oat\tao\scripts\tools\migrations\AbstractMigration;
use oat\tao\scripts\update\OntologyUpdater;
use oat\taoItems\model\event\ItemCreatedEvent;
use oat\taoItems\model\event\ItemRemovedEvent;
use oat\taoItems\model\event\ItemUpdatedEvent;
use oat\taoItems\model\Translation\Listener\TranslationItemEventListener;
use oat\taoItems\model\user\TaoItemsRoles;

final class Version202409060743452142_taoItems extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add new Item Translator Role, sync models and add new listener';
    }

    public function up(Schema $schema): void
    {
        OntologyUpdater::syncModels();

        AclProxy::applyRule($this->getRule());

        $this->addReport(Report::createSuccess('Applied rules for role ' . TaoItemsRoles::ITEM_TRANSLATOR));

        /** @var SectionVisibilityFilter $sectionVisibilityFilter */
        $sectionVisibilityFilter = $this->getServiceManager()->get(SectionVisibilityFilter::SERVICE_ID);

        $sectionVisibilityFilter->showSectionByFeatureFlag(
            $sectionVisibilityFilter->createSectionPath(
                [
                    'manage_items',
                    'item-translate'
                ]
            ),
            'FEATURE_FLAG_TRANSLATION_ENABLED'
        );
        $this->getServiceManager()->register(SectionVisibilityFilter::SERVICE_ID, $sectionVisibilityFilter);

        $this->addReport(
            Report::createSuccess('Hide item section for feature flag FEATURE_FLAG_TRANSLATION_ENABLED')
        );

        /** @var EventManager $eventManager */
        $eventManager = $this->getServiceManager()->get(EventManager::SERVICE_ID);
        $eventManager->attach(
            ItemCreatedEvent::class,
            [TranslationItemEventListener::class, 'populateTranslationProperties']
        );
        $eventManager->attach(
            ItemUpdatedEvent::class,
            [TranslationItemEventListener::class, 'populateTranslationProperties']
        );
        $eventManager->attach(
            ItemRemovedEvent::class,
            [TranslationItemEventListener::class, 'deleteTranslations']
        );
        $this->getServiceManager()->register(EventManager::SERVICE_ID, $eventManager);

        $this->addReport(
            Report::createSuccess('Listen to ' . ItemCreatedEvent::class . ' at ' . TranslationItemEventListener::class)
        );
    }

    public function down(Schema $schema): void
    {
        AclProxy::revokeRule($this->getRule());

        /** @var EventManager $eventManager */
        $eventManager = $this->getServiceManager()->get(EventManager::SERVICE_ID);
        $eventManager->detach(
            ItemCreatedEvent::class,
            [TranslationItemEventListener::class, 'populateTranslationProperties']
        );
        $eventManager->detach(
            ItemUpdatedEvent::class,
            [TranslationItemEventListener::class, 'populateTranslationProperties']
        );
        $eventManager->detach(
            ItemRemovedEvent::class,
            [TranslationItemEventListener::class, 'deleteTranslations']
        );
        $this->getServiceManager()->register(EventManager::SERVICE_ID, $eventManager);
    }

    private function getRule(): AccessRule
    {
        return new AccessRule(
            AccessRule::GRANT,
            TaoItemsRoles::ITEM_TRANSLATOR,
            [
                'ext' => 'tao',
                'mod' => 'Translation'
            ]
        );
    }
}
