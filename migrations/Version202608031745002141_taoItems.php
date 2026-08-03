<?php

declare(strict_types=1);

namespace oat\taoItems\migrations;

use Doctrine\DBAL\Schema\Schema;
use oat\oatbox\reporting\Report;
use oat\tao\scripts\tools\migrations\AbstractMigration;
use oat\tao\scripts\update\OntologyUpdater;
use oat\taoItems\scripts\install\RegisterItemCommentServices;

/**
 * phpcs:disable Squiz.Classes.ValidClassName
 */
final class Version202608031745002141_taoItems extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Register Item Comments RDF ontology and persistence services (NYSED-19)';
    }

    public function up(Schema $schema): void
    {
        OntologyUpdater::syncModels();

        $script = new RegisterItemCommentServices();
        $script->setServiceLocator($this->getServiceLocator());
        $script([]);

        $this->addReport(Report::createSuccess('Item Comment services registered with RDF default adapter'));
    }

    public function down(Schema $schema): void
    {
        // Intentionally left empty: ontology and services remain for forward compatibility.
    }
}
