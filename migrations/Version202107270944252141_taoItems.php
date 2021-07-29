<?php

declare(strict_types=1);

namespace oat\taoItems\migrations;

use Doctrine\DBAL\Schema\Schema;
use taoItems_actions_ItemContent;
use oat\taoItems\model\user\TaoItemsRoles;
use oat\tao\scripts\update\OntologyUpdater;
use oat\tao\model\accessControl\ActionAccessControl;
use oat\tao\scripts\tools\accessControl\SetRolesAccess;
use oat\tao\scripts\tools\migrations\AbstractMigration;

final class Version202107270944252141_taoItems extends AbstractMigration
{
    private const CONFIG = [
        SetRolesAccess::CONFIG_RULES => [
            TaoItemsRoles::ITEM_PREVIEWER => [
                ['ext' => 'taoItems', 'mod' => 'ItemContent', 'act' => 'files'],
                ['ext' => 'taoItems', 'mod' => 'ItemContent', 'act' => 'download'],
            ],
            TaoItemsRoles::ITEM_CONTENT_CREATOR => [
                ['ext' => 'taoItems', 'mod' => 'Items', 'act' => 'authoring'],
            ],
        ],
        SetRolesAccess::CONFIG_PERMISSIONS => [
            taoItems_actions_ItemContent::class => [
                'files' => [
                    TaoItemsRoles::ITEM_CLASS_NAVIGATOR => ActionAccessControl::DENY,
                ],
            ],
        ],
    ];

    public function getDescription(): string
    {
        return 'Item content creator role to author existing item';
    }

    public function up(Schema $schema): void
    {
        OntologyUpdater::syncModels();

        $setRolesAccess = $this->propagate(new SetRolesAccess());
        $setRolesAccess(
            [
                '--' . SetRolesAccess::OPTION_CONFIG,
                self::CONFIG,
            ]
        );
    }

    public function down(Schema $schema): void
    {
        $this->throwIrreversibleMigrationException(
            'This role should have been applied in the past, so we should not roll it back'
        );
    }
}
