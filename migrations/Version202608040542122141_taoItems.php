<?php

declare(strict_types=1);

namespace oat\taoItems\migrations;

use Doctrine\DBAL\Schema\Schema;
use oat\oatbox\reporting\Report;
use oat\tao\model\accessControl\func\AccessRule;
use oat\tao\model\accessControl\func\AclProxy;
use oat\tao\scripts\tools\migrations\AbstractMigration;
use oat\taoItems\model\user\TaoItemsRoles;

/**
 * phpcs:disable Squiz.Classes.ValidClassName
 */
final class Version202608040542122141_taoItems extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Grant ACL for RestItemComments module to authoring roles (NYSED-20)';
    }

    public function up(Schema $schema): void
    {
        foreach ($this->getRules() as $rule) {
            AclProxy::applyRule($rule);
        }

        $this->addReport(Report::createSuccess('Applied RestItemComments ACL rules'));
    }

    public function down(Schema $schema): void
    {
        foreach ($this->getRules() as $rule) {
            AclProxy::revokeRule($rule);
        }

        $this->addReport(Report::createSuccess('Revoked RestItemComments ACL rules'));
    }

    /**
     * @return AccessRule[]
     */
    private function getRules(): array
    {
        // Prefer ext/mod mask — string class masks can fail silently if class_exists is false
        // during applyRule evaluation.
        $mask = [
            'ext' => 'taoItems',
            'mod' => 'RestItemComments',
        ];

        return [
            new AccessRule(AccessRule::GRANT, TaoItemsRoles::ITEM_AUTHOR_ABSTRACT, $mask),
            new AccessRule(AccessRule::GRANT, TaoItemsRoles::ITEM_MANAGER, $mask),
            new AccessRule(AccessRule::GRANT, TaoItemsRoles::ITEM_CONTENT_CREATOR, $mask),
            new AccessRule(AccessRule::GRANT, TaoItemsRoles::ITEM_AUTHOR, $mask),
            new AccessRule(AccessRule::GRANT, TaoItemsRoles::ITEM_VIEWER, $mask),
            new AccessRule(
                AccessRule::GRANT,
                'http://www.tao.lu/Ontologies/TAO.rdf#GlobalManagerRole',
                $mask
            ),
        ];
    }
}