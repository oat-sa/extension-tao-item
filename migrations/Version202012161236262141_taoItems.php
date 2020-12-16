<?php

declare(strict_types=1);

namespace oat\taoItems\migrations;

use Doctrine\DBAL\Schema\Schema;
use oat\tao\scripts\tools\migrations\AbstractMigration;
use oat\taoItems\model\media\AssetTreeBuilder;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version202012161236262141_taoItems extends AbstractMigration
{

    public function up(Schema $schema): void
    {
        $this->getServiceManager()->register(AssetTreeBuilder::SERVICE_ID, new AssetTreeBuilder([
            AssetTreeBuilder::OPTION_PAGINATION_LIMIT => 15,
        ]));
    }

    public function down(Schema $schema): void
    {
        $this->getServiceManager()->unregister(AssetTreeBuilder::SERVICE_ID);
    }
}
