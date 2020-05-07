<?php

declare(strict_types=1);

namespace oat\taoItems\migrations;

use Doctrine\DBAL\Schema\Schema;
use oat\tao\scripts\tools\migrations\AbstractMigration;
use oat\oatbox\service\ServiceManagerAwareInterface;
use oat\oatbox\service\ServiceManagerAwareTrait;
use oat\tao\scripts\update\OntologyUpdater;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version202005070944372141_taoItems extends AbstractMigration implements ServiceManagerAwareInterface
{
    use ServiceManagerAwareTrait;

    public function getDescription() : string
    {
        return 'Sync the ontology model';
    }

    public function up(Schema $schema) : void
    {
        OntologyUpdater::syncModels();
    }

    public function down(Schema $schema) : void
    {
        OntologyUpdater::syncModels();
    }
}
