<?php

declare(strict_types=1);

namespace oat\taoItems\migrations;

use Doctrine\DBAL\Schema\Schema;
use oat\tao\scripts\tools\migrations\AbstractMigration;
use oat\tao\scripts\tools\accessControl\SetRolesAccess;
use oat\taoItems\model\user\TaoItemsRoles;
use oat\tao\model\accessControl\ActionAccessControl;
use oat\tao\scripts\update\OntologyUpdater;
use taoItems_actions_ItemContent;

final class Version202108250549242141_taoItems extends AbstractMigration
{
    private const CONFIG = [
        SetRolesAccess::CONFIG_PERMISSIONS => [
            taoItems_actions_ItemContent::class => [
                'upload' => [
                    TaoItemsRoles::ITEM_CONTENT_CREATOR => ActionAccessControl::DENY,
                ],
                'download' => [
                    TaoItemsRoles::ITEM_CONTENT_CREATOR => ActionAccessControl::DENY,
                ],
                'delete' => [
                    TaoItemsRoles::ITEM_CONTENT_CREATOR => ActionAccessControl::DENY,
                ],
                'previewAsset' => [
                    TaoItemsRoles::ITEM_CONTENT_CREATOR => ActionAccessControl::DENY,
                ],
                'downloadAsset' => [
                    TaoItemsRoles::ITEM_CONTENT_CREATOR => ActionAccessControl::DENY,
                ],
                'uploadAsset' => [
                    TaoItemsRoles::ITEM_CONTENT_CREATOR => ActionAccessControl::DENY,
                ],
                'deleteAsset' => [
                    TaoItemsRoles::ITEM_CONTENT_CREATOR => ActionAccessControl::DENY,
                ],
            ],
        ],
    ];

    public function getDescription(): string
    {
        return 'Configure insert/delete/download asset permissions for Item Content creator role';
    }

    public function up(Schema $schema): void
    {
        OntologyUpdater::syncModels();

        $setRolesAccess = $this->propagate(new SetRolesAccess());
        $setRolesAccess([
            '--' . SetRolesAccess::OPTION_CONFIG, self::CONFIG,
        ]);
    }

    public function down(Schema $schema): void
    {
        $setRolesAccess = $this->propagate(new SetRolesAccess());
        $setRolesAccess([
            '--' . SetRolesAccess::OPTION_REVOKE,
            '--' . SetRolesAccess::OPTION_CONFIG, self::CONFIG,
        ]);
    }
}
