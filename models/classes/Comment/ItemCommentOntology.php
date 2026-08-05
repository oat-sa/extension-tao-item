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
 * Foundation, Inc., 31 Milk St # 960789 Boston, MA 02196 USA
 *
 * Copyright (c) 2026 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace oat\taoItems\model\Comment;

final class ItemCommentOntology
{
    public const CLASS_URI = 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemComment';
    public const PROPERTY_ITEM_URI = 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemCommentItemUri';
    public const PROPERTY_AUTHOR_ID = 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemCommentAuthorId';
    public const PROPERTY_AUTHOR_LABEL = 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemCommentAuthorLabel';
    public const PROPERTY_BODY = 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemCommentBody';
    public const PROPERTY_CREATED_AT = 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemCommentCreatedAt';
    public const PROPERTY_EDITED = 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemCommentEdited';
    public const PROPERTY_RESOLVED = 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemCommentResolved';
}
