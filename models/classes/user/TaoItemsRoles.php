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
 * Copyright (c) 2021 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace oat\taoItems\model\user;

interface TaoItemsRoles
{
    public const ITEM_AUTHOR = 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemAuthor';
    public const ITEM_TRANSLATOR = 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemTranslator';
    public const ITEM_AUTHOR_ABSTRACT = 'http://www.tao.lu/Ontologies/TAOItem.rdf#AbstractItemAuthor';
    public const ITEM_MANAGER = 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemsManagerRole';

    public const ITEM_CLASS_NAVIGATOR = 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemClassNavigatorRole';
    public const ITEM_CLASS_EDITOR = 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemClassEditorRole';
    public const ITEM_CLASS_CREATOR = 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemClassCreatorRole';
    public const ITEM_CLASS_SCHEMA_MANAGER = 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemClassSchemaManagerRole';

    public const ITEM_VIEWER = 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemViewerRole';
    public const ITEM_REPLICATOR = 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemReplicatorRole';
    public const ITEM_PREVIEWER = 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemPreviewerRole';
    public const ITEM_PROPERTIES_EDITOR = 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemPropertiesEditorRole';
    public const ITEM_CONTENT_CREATOR = 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemContentCreatorRole';
    public const ITEM_RESOURCE_CREATOR = 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemResourceCreatorRole';
    public const ITEM_IMPORTER = 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemImporterRole';
    public const ITEM_DELETER = 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemDeleterRole';
    public const RESTRICTED_ITEM_AUTHOR = 'http://www.tao.lu/Ontologies/TAO.rdf#RestrictedItemAuthor';
}
