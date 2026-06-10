<?php

declare(strict_types=1);

namespace oat\taoItems\migrations;

use Doctrine\DBAL\Schema\Schema;
use oat\oatbox\reporting\Report;
use oat\tao\scripts\tools\migrations\AbstractMigration;
use oat\taoItems\scripts\install\SetupSectionVisibilityFilters;

/**
 * phpcs:disable Squiz.Classes.ValidClassName
 */
final class Version202606101250452141_taoItems extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Hide Usage and Item Statistics actions when FEATURE_FLAG_HIDE_MANAGE_ITEMS is enabled';
    }

    public function up(Schema $schema): void
    {
        $script = new SetupSectionVisibilityFilters();
        $script->setServiceLocator($this->getServiceLocator());
        $script([]);

        $this->addReport(
            Report::createSuccess(
                'Usage and Item Statistics actions are hidden when FEATURE_FLAG_HIDE_MANAGE_ITEMS is enabled'
            )
        );
    }

    public function down(Schema $schema): void
    {
        // Intentionally left empty: feature flag paths are merged into runtime config.
    }
}
