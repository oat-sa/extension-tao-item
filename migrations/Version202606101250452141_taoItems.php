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
        return 'Gate Usage and Item Statistics actions behind FEATURE_FLAG_MANAGE_ITEMS';
    }

    public function up(Schema $schema): void
    {
        $script = new SetupSectionVisibilityFilters();
        $script->setServiceLocator($this->getServiceLocator());
        $script([]);

        $this->addReport(
            Report::createSuccess(
                'Usage and Item Statistics actions are now controlled by FEATURE_FLAG_MANAGE_ITEMS'
            )
        );
    }

    public function down(Schema $schema): void
    {
        // Intentionally left empty: feature flag paths are merged into runtime config.
    }
}
