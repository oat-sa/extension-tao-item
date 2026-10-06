<?php

declare(strict_types=1);

namespace oat\taoItems\migrations;

use Doctrine\DBAL\Schema\Schema;
use oat\tao\scripts\tools\migrations\AbstractMigration;

/**
 * Revert migration Version202608051012062141_taoItems.
 *
 * phpcs:disable Squiz.Classes.ValidClassName
 */
final class Version202610060428292141_taoItems extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Revert migration Version202608051012062141_taoItems (NYSED-13 ACL grant)';
    }

    public function up(Schema $schema): void
    {
        (new Version202608051012062141_taoItems())->down($schema);
    }

    public function down(Schema $schema): void
    {
        (new Version202608051012062141_taoItems())->up($schema);
    }
}
