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
use oat\taoItems\scripts\install\CreateItemDirectory;
use oat\taoItems\scripts\install\RegisterCategoryService;
use oat\taoItems\scripts\install\RegisterNpmPaths;

/*
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 *
 */
$extpath = __DIR__ . DIRECTORY_SEPARATOR;

return [
    'name' => 'taoItems',
    'label' => 'Item core extension',
    'description' => 'TAO Items extension',
    'license' => 'GPL-2.0',
    'version' => '10.8.5',
    'author' => 'Open Assessment Technologies, CRP Henri Tudor',
    'requires' => [
        'taoBackOffice' => '>=3.0.0',
        'generis' => '>=12.5.0',
        'tao' => '>=45.1.3'
    ],
    'models' => [
        'http://www.tao.lu/Ontologies/TAOItem.rdf'
    ],
    'install' => [
        'rdf' => [
            __DIR__ . '/models/ontology/taoitem.rdf',
            __DIR__ . '/models/ontology/taoItemRunner.rdf',
            __DIR__ . '/models/ontology/indexation.rdf',
            __DIR__ . '/models/ontology/category.rdf',
        ],
        'php'   => [
            CreateItemDirectory::class,
            RegisterCategoryService::class,
            RegisterNpmPaths::class,
        ]
    ],
    'update' => 'taoItems_scripts_update_Updater',
    'managementRole' => 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemsManagerRole',
    'acl' => [
        ['grant', 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemsManagerRole', ['ext' => 'taoItems']],
        ['grant', 'http://www.tao.lu/Ontologies/TAOItem.rdf#AbstractItemAuthor', 'taoItems_actions_ItemContent'],
        ['grant', 'http://www.tao.lu/Ontologies/TAO.rdf#DeliveryRole', ['ext' => 'taoItems', 'mod' => 'ItemRunner']],
        ['grant', TaoRoles::REST_PUBLISHER, ['ext' => 'taoItems', 'mod' => 'RestItems']],
        ['grant', TaoRoles::REST_PUBLISHER, ['ext' => 'taoItems', 'mod' => 'RestFormItem']],
    ],
    'optimizableClasses' => [
            'http://www.tao.lu/Ontologies/TAOItem.rdf#Item',
            'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemModels',
            'http://www.tao.lu/Ontologies/TAOItem.rdf#ModelStatus'
    ],
    'constants' => [
        # actions directory
        "DIR_ACTIONS"           => $extpath . "actions" . DIRECTORY_SEPARATOR,

        # views directory
        "DIR_VIEWS"             => $extpath . "views" . DIRECTORY_SEPARATOR,

        # default module name
        'DEFAULT_MODULE_NAME'   => 'Items',

        #default action name
        'DEFAULT_ACTION_NAME'   => 'index',

        #BASE PATH: the root path in the file system (usually the document root)
        'BASE_PATH'             => $extpath,

        #BASE URL (usually the domain root)
        'BASE_URL'              => ROOT_URL . 'taoItems/',
    ]
];
