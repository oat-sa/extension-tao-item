<?php

declare(strict_types=1);

namespace oat\taoItems\migrations;

use Doctrine\DBAL\Schema\Schema;
use oat\oatbox\reporting\Report;
use oat\tao\model\accessControl\func\AccessRule;
use oat\tao\model\accessControl\func\AclProxy;
use oat\tao\scripts\tools\migrations\AbstractMigration;
use oat\tao\scripts\update\OntologyUpdater;
use oat\taoItems\model\user\TaoItemsRoles;

final class Version202409040743452140_taoItems extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add new Item Translator Role';
    }

    public function up(Schema $schema): void
    {
        OntologyUpdater::syncModels();

        AclProxy::applyRule($this->getRule());
        
        $this->addReport(Report::createSuccess('Applied rules for role ' . TaoItemsRoles::ITEM_TRANSLATOR));
    }

    public function down(Schema $schema): void
    {
        AclProxy::revokeRule($this->getRule());
    }

    private function getRule(): AccessRule
    {
        return new AccessRule(
            AccessRule::GRANT,
            TaoItemsRoles::ITEM_TRANSLATOR,
            [
                'ext' => 'taoItems',
                'mod' => 'Translation'
            ]
        );
    }
}
