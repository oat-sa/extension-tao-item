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
 * Copyright (c) 2017 (original work) Open Assessment Technologies SA;
 *
 */

use \oat\generis\model\kernel\persistence\smoothsql\search\ComplexSearchService;
use \oat\generis\model\user\PasswordConstraintsService;
use \oat\oatbox\validator\ValidatorInterface;
use \Zend\ServiceManager\ServiceLocatorAwareTrait;
use \Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * Class tao_actions_form_RestItemForm
 *
 * Implementation of tao_actions_form_RestForm to manage generis item forms for edit and create
 */
class taoItems_actions_form_RestItemForm extends tao_actions_form_RestForm implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * Get editable properties
     *
     * @return array
     */
    protected function getClassProperties()
    {
        $properties = parent::getClassProperties();
        unset($properties[taoItems_models_classes_ItemsService::PROPERTY_ITEM_MODEL]);
        unset($properties[taoItems_models_classes_ItemsService::PROPERTY_ITEM_CONTENT]);
        unset($properties[taoItems_models_classes_ItemsService::PROPERTY_ITEM_CONTENT_SRC]);
        return $properties;
    }
}
