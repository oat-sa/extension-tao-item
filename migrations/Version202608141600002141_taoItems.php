<?php

declare(strict_types=1);

namespace oat\taoItems\migrations;

use Doctrine\DBAL\Schema\Schema;
use oat\oatbox\reporting\Report;
use oat\tao\scripts\tools\migrations\AbstractMigration;
use oat\taoItems\model\media\AssetSearchBuilder;

/**
 * phpcs:disable Squiz.Classes.ValidClassName
 */
final class Version202608141600002141_taoItems extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Register AssetSearchBuilder for Resource Manager scoped asset search';
    }

    public function up(Schema $schema): void
    {
        $this->getServiceManager()->register(
            AssetSearchBuilder::SERVICE_ID,
            new AssetSearchBuilder()
        );

        $this->addReport(Report::createSuccess('AssetSearchBuilder registered'));
    }

    public function down(Schema $schema): void
    {
        $this->getServiceManager()->unregister(AssetSearchBuilder::SERVICE_ID);
        $this->addReport(Report::createSuccess('AssetSearchBuilder unregistered'));
    }
}
