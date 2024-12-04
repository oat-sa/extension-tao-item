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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg
 *                         (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung
 *                         (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor
 *                         (under the project TAO-SUSTAIN & TAO-DEV);
 *
 */

use oat\tao\model\user\TaoRoles;
use oat\taoBackOffice\controller\Lists;
use oat\taoItems\model\Form\ServiceProvider\FormServiceProvider;
use oat\taoItems\model\search\ItemClassListServiceProvider;
use oat\taoItems\model\Translation\ServiceProvider\TranslationServiceProvider;
use oat\taoItems\model\user\TaoItemsRoles;
use oat\tao\model\accessControl\func\AccessRule;
use oat\taoItems\scripts\install\RegisterNpmPaths;
use oat\taoItems\model\Copier\CopierServiceProvider;
use oat\taoItems\scripts\install\CreateItemDirectory;
use oat\taoItems\scripts\install\SetRolesPermissions;
use oat\taoItems\scripts\install\RegisterCategoryService;
use oat\taoItems\scripts\install\RegisterAssetTreeBuilder;
use oat\taoItems\scripts\install\RegisterItemPreviewerRegistryService;
use oat\taoItems\scripts\install\SetupEventListeners;
use oat\taoItems\scripts\install\SetupSectionVisibilityFilters;

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
            SetupEventListeners::class,
            SetupSectionVisibilityFilters::class
        ],
    ],
    'update' => taoItems_scripts_update_Updater::class,
    'managementRole' => TaoItemsRoles::ITEM_MANAGER,
    'acl' => [
        [
            AccessRule::GRANT,
            TaoItemsRoles::ITEM_MANAGER,
            ['ext' => 'taoItems'],
        ],
        [
            AccessRule::GRANT,
            TaoItemsRoles::ITEM_AUTHOR_ABSTRACT,
            'taoItems_actions_ItemContent',
        ],
        [
            AccessRule::GRANT,
            TaoRoles::DELIVERY,
            ['ext' => 'taoItems', 'mod' => 'ItemRunner'],
        ],
        [
            AccessRule::GRANT,
            TaoRoles::REST_PUBLISHER,
            ['ext' => 'taoItems', 'mod' => 'RestItems'],
        ],
        [
            AccessRule::GRANT,
            TaoRoles::REST_PUBLISHER,
            ['ext' => 'taoItems', 'mod' => 'RestFormItem'],
        ],
        [
            AccessRule::GRANT,
            TaoItemsRoles::ITEM_CLASS_NAVIGATOR,
            ['ext' => 'taoItems', 'mod' => 'Items', 'act' => 'editClassLabel'],
        ],
        [
            AccessRule::GRANT,
            TaoItemsRoles::ITEM_CLASS_NAVIGATOR,
            ['ext' => 'taoItems', 'mod' => 'Items', 'act' => 'getOntologyData'],
        ],
        [
            AccessRule::GRANT,
            TaoItemsRoles::ITEM_CLASS_NAVIGATOR,
            ['ext' => 'taoItems', 'mod' => 'Items', 'act' => 'index'],
        ],
        [
            AccessRule::GRANT,
            TaoItemsRoles::ITEM_CLASS_CREATOR,
            ['ext' => 'taoItems', 'mod' => 'Items', 'act' => 'addSubClass'],
        ],
        [
            AccessRule::GRANT,
            TaoItemsRoles::ITEM_CLASS_SCHEMA_MANAGER,
            ['ext' => 'taoItems', 'mod' => 'Items', 'act' => 'editItemClass'],
        ],
        [
            AccessRule::GRANT,
            TaoItemsRoles::ITEM_CLASS_SCHEMA_MANAGER,
            ['ext' => 'taoItems', 'mod' => 'Items', 'act' => 'addClassProperty'],
        ],
        [
            AccessRule::GRANT,
            TaoItemsRoles::ITEM_CLASS_SCHEMA_MANAGER,
            ['ext' => 'taoItems', 'mod' => 'Items', 'act' => 'removeClassProperty'],
        ],
        [
            AccessRule::GRANT,
            TaoItemsRoles::ITEM_CLASS_SCHEMA_MANAGER,
            ['ext' => 'taoItems', 'mod' => 'Category', 'act' => 'getExposedsByClass'],
        ],
        [
            AccessRule::GRANT,
            TaoItemsRoles::ITEM_CLASS_SCHEMA_MANAGER,
            ['ext' => 'taoItems', 'mod' => 'Category', 'act' => 'setExposed'],
        ],
        [
            AccessRule::GRANT,
            TaoItemsRoles::ITEM_CLASS_SCHEMA_MANAGER,
            Lists::class . '@getListElements',
        ],
        [
            AccessRule::GRANT,
            TaoItemsRoles::ITEM_VIEWER,
            ['ext' => 'taoItems', 'mod' => 'Items', 'act' => 'editItem'],
        ],
        [
            AccessRule::GRANT,
            TaoItemsRoles::ITEM_REPLICATOR,
            ['ext' => 'taoItems', 'mod' => 'Items', 'act' => 'cloneInstance'],
        ],
        [
            AccessRule::GRANT,
            TaoItemsRoles::ITEM_REPLICATOR,
            ['ext' => 'taoItems', 'mod' => 'Items', 'act' => 'copyInstance'],
        ],
        [
            AccessRule::GRANT,
            TaoItemsRoles::ITEM_PREVIEWER,
            ['ext' => 'taoItems', 'mod' => 'ItemPreview', 'act' => 'index'],
        ],
        [
            AccessRule::GRANT,
            TaoItemsRoles::ITEM_PREVIEWER,
            ['ext' => 'taoItems', 'mod' => 'ItemContent', 'act' => 'files'],
        ],
        [
            AccessRule::GRANT,
            TaoItemsRoles::ITEM_PREVIEWER,
            ['ext' => 'taoItems', 'mod' => 'ItemContent', 'act' => 'download'],
        ],
        [
            AccessRule::GRANT,
            TaoItemsRoles::ITEM_CONTENT_CREATOR,
            ['ext' => 'taoItems', 'mod' => 'Items', 'act' => 'authoring'],
        ],
        [
            AccessRule::GRANT,
            TaoItemsRoles::ITEM_CONTENT_CREATOR,
            ['ext' => 'taoItems', 'mod' => 'ItemContent', 'act' => 'delete'],
        ],
        [
            AccessRule::GRANT,
            TaoItemsRoles::ITEM_CONTENT_CREATOR,
            ['ext' => 'taoItems', 'mod' => 'ItemContent', 'act' => 'fileExists'],
        ],
        [
            AccessRule::GRANT,
            TaoItemsRoles::ITEM_CONTENT_CREATOR,
            ['ext' => 'taoItems', 'mod' => 'ItemContent', 'act' => 'upload'],
        ],
        [
            AccessRule::GRANT,
            TaoItemsRoles::ITEM_IMPORTER,
            ['ext' => 'taoItems', 'mod' => 'ItemImport', 'act' => 'index'],
        ],
        [
            AccessRule::GRANT,
            TaoItemsRoles::ITEM_IMPORTER,
            ['ext' => 'taoItems', 'mod' => 'RestItem', 'act' => 'getItemClasses'],
        ],
        [
            AccessRule::GRANT,
            TaoItemsRoles::ITEM_DELETER,
            ['ext' => 'taoItems', 'mod' => 'Items', 'act' => 'deleteItem'],
        ],
        [
            AccessRule::GRANT,
            TaoItemsRoles::ITEM_DELETER,
            ['ext' => 'taoItems', 'mod' => 'Items', 'act' => 'moveInstance'],
        ],
        [
            AccessRule::GRANT,
            TaoItemsRoles::RESTRICTED_ITEM_AUTHOR,
            ['ext' => 'taoItems', 'mod' => 'Items']
        ],
        [
            AccessRule::GRANT,
            TaoItemsRoles::RESTRICTED_ITEM_AUTHOR,
            ['ext' => 'taoItems', 'mod' => 'ItemExport']
        ],
        [
            AccessRule::GRANT,
            TaoItemsRoles::ITEM_TRANSLATOR,
            [
                'ext' => 'tao',
                'mod' => 'Translation'
            ]
        ]
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
    'containerServiceProviders' => [
        CopierServiceProvider::class,
        ItemClassListServiceProvider::class,
        TranslationServiceProvider::class,
        FormServiceProvider::class,
    ],
];
