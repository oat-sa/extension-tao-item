<?php

declare(strict_types=1);

namespace oat\taoItems\migrations;

use Doctrine\DBAL\Schema\Schema;
use oat\tao\scripts\tools\migrations\AbstractMigration;
use oat\taoItems\model\user\TaoItemsRoles;
use oat\tao\scripts\tools\accessControl\SetRolesAccess;

final class Version202107270944252141_taoItems extends AbstractMigration
{
    private const CONFIG = [
        SetRolesAccess::CONFIG_RULES => [
            TaoItemsRoles::ITEM_CONTENT_CREATOR => [
                ['ext' => 'taoItems', 'mod' => 'Items', 'act' => 'authoring'],
                ['ext' => 'taoItems', 'mod' => 'ItemContent', 'act' => 'files'],
            ],
        ]
    ];

    public function getDescription(): string
    {
        return 'Item content creator role to author existing item';
    }

    public function up(Schema $schema): void
    {
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
