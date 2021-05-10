<?php

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *
 */

use oat\tao\model\user\TaoRoles;
use oat\taoItems\model\user\TaoItemsRoles;
use oat\tao\model\accessControl\func\AccessRule;
use oat\taoItems\scripts\install\RegisterNpmPaths;
use oat\taoItems\scripts\install\CreateItemDirectory;
use oat\taoItems\scripts\install\SetRolesPermissions;
use oat\taoItems\scripts\install\RegisterCategoryService;
use oat\taoItems\scripts\install\RegisterAssetTreeBuilder;
use oat\taoItems\scripts\install\RegisterItemPreviewerRegistryService;

/*
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
$extpath = __DIR__ . DIRECTORY_SEPARATOR;

return [
    'name' => 'taoItems',
    'label' => 'Item core extension',
    'description' => 'TAO Items extension',
    'license' => 'GPL-2.0',
    'author' => 'Open Assessment Technologies, CRP Henri Tudor',
    'models' => [
        'http://www.tao.lu/Ontologies/TAOItem.rdf',
    ],
    'install' => [
        'rdf' => [
            __DIR__ . '/models/ontology/taoitem.rdf',
            __DIR__ . '/models/ontology/taoItemRunner.rdf',
            __DIR__ . '/models/ontology/indexation.rdf',
            __DIR__ . '/models/ontology/category.rdf',
        ],
        'php' => [
            CreateItemDirectory::class,
            RegisterCategoryService::class,
            RegisterNpmPaths::class,
            RegisterItemPreviewerRegistryService::class,
            RegisterAssetTreeBuilder::class,
            SetRolesPermissions::class,
        ],
    ],
    'update' => taoItems_scripts_update_Updater::class,
    'managementRole' => TaoItemsRoles::ITEM_MANAGER,
    'acl' => [
        [
            AccessRule::GRANT,
            TaoItemsRoles::ITEM_MANAGER,
            ['ext' => 'taoItems']
        ],
        [
            AccessRule::GRANT,
            TaoItemsRoles::ITEM_AUTHOR_ABSTRACT,
            'taoItems_actions_ItemContent'
        ],
        [
            AccessRule::GRANT,
            TaoRoles::DELIVERY,
            ['ext' => 'taoItems', 'mod' => 'ItemRunner']
        ],
        [
            AccessRule::GRANT,
            TaoRoles::REST_PUBLISHER,
            ['ext' => 'taoItems', 'mod' => 'RestItems']
        ],
        [
            AccessRule::GRANT,
            TaoRoles::REST_PUBLISHER,
            ['ext' => 'taoItems', 'mod' => 'RestFormItem']
        ],
        [
            AccessRule::GRANT,
            TaoItemsRoles::ITEM_CLASS_NAVIGATOR,
            ['ext' => 'taoItems', 'mod' => 'Items', 'act' => 'editClassLabel']
        ],
        [
            AccessRule::GRANT,
            TaoItemsRoles::ITEM_CLASS_NAVIGATOR,
            ['ext' => 'taoItems', 'mod' => 'Items', 'act' => 'getOntologyData']
        ],
        [
            AccessRule::GRANT,
            TaoItemsRoles::ITEM_CLASS_NAVIGATOR,
            ['ext' => 'taoItems', 'mod' => 'Items', 'act' => 'index']
        ],
        [
            AccessRule::GRANT,
            TaoItemsRoles::ITEM_CLASS_CREATOR,
            ['ext' => 'taoItems', 'mod' => 'Items', 'act' => 'addSubClass']
        ],
    ],
    'optimizableClasses' => [
        'http://www.tao.lu/Ontologies/TAOItem.rdf#Item',
        'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemModels',
        'http://www.tao.lu/Ontologies/TAOItem.rdf#ModelStatus',
    ],
    'constants' => [
        # actions directory
        'DIR_ACTIONS' => $extpath . 'actions' . DIRECTORY_SEPARATOR,

        # views directory
        'DIR_VIEWS' => $extpath . 'views' . DIRECTORY_SEPARATOR,

        # default module name
        'DEFAULT_MODULE_NAME' => 'Items',

        #default action name
        'DEFAULT_ACTION_NAME' => 'index',

        #BASE PATH: the root path in the file system (usually the document root)
        'BASE_PATH' => $extpath,

        #BASE URL (usually the domain root)
        'BASE_URL' => ROOT_URL . 'taoItems/',
    ],
];
