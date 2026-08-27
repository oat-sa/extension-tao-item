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
 * Copyright (c) 2016-2020 (original work) Open Assessment Technologies SA
 */

namespace oat\taoItems\model;

use core_kernel_classes_Class as RdfClass;
use core_kernel_classes_Property as RdfProperty;
use core_kernel_classes_Resource as RdfResource;
use oat\generis\model\GenerisRdf;
use oat\oatbox\service\ConfigurableService;
use taoItems_models_classes_ItemsService;

/**
 * Category management service.
 * How to expose RDF properties to categorize them.
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
class CategoryService extends ConfigurableService
{
    public const SERVICE_ID = 'taoItems/Category';

    public const ITEM_CLASS_URI  = 'http://www.tao.lu/Ontologies/TAOItem.rdf#Item';
    public const EXPOSE_PROP_URI = 'http://www.tao.lu/Ontologies/TAOItem.rdf#ExposeCategory';

    public static $supportedWidgetUris = [
        'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#TextBox',
        'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#CheckBox',
        'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#RadioBox',
        'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#ComboBox',
        'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#TreeBox'
    ];

    public static $excludedPropUris = [
        'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemModel'
    ];

    /**
     * @var taoItems_models_classes_ItemsService
     */
    protected $itemService;

    /**
     * Get the categories link to the list of items in parameter.
     * Theses categories come from a configurable list of properties.
     * The category label is also set in a configurable list
     *
     * @param RdfResource[] $items the list of items
     *
     * @return array of categories for specified items
     *               ['itemUri' => ['CATEGORY1', 'CATEGORY2']]
     */
    public function getItemsCategories(array $items)
    {
        $categories = [];

        foreach ($items as $item) {
            $itemCategories = $this->getItemCategories($item);
            if (count($itemCategories) > 0) {
                $categories[$item->getUri()] = $itemCategories;
            }
        }

        return $categories;
    }

    /**
     * Get the categories of an item
     *
     * @param RdfResource $item the item
     *
     * @return string[] the list of categories
     */
    public function getItemCategories(RdfResource $item)
    {
        $categories = [];

        foreach ($item->getTypes() as $class) {
            $eligibleProperties = array_filter($this->getElligibleProperties($class), [$this, 'doesExposeCategory']);
            $propertiesValues   = $item->getPropertiesValues(array_keys($eligibleProperties));

            foreach ($propertiesValues as $propertyValues) {
                foreach ($propertyValues as $value) {
                    if ($value instanceof RdfResource) {
                        $normalizedIdentifier = self::sanitizeCategoryName($value->getLabel());
                    } else {
                        $normalizedIdentifier = self::sanitizeCategoryName((string)$value);
                    }

                    if ($normalizedIdentifier !== null) {
                        $categories[] = $normalizedIdentifier;
                    }
                }
            }
        }

        return $categories;
    }

    /**
     * Get exposed property values that cannot be used as automatic categories.
     *
     * @param RdfResource $item the item
     *
     * @return array<string, array{label: string, values: string[]}>
     *     invalid values indexed by property URI
     */
    public function getInvalidCategoryValues(RdfResource $item)
    {
        $invalidValues = [];

        foreach ($item->getTypes() as $class) {
            $eligibleProperties = array_filter(
                $this->getElligibleProperties($class),
                [$this, 'doesExposeCategory']
            );
            $propertiesValues = $item->getPropertiesValues(
                array_keys($eligibleProperties)
            );
            $propertyLabels = [];
            foreach ($eligibleProperties as $property) {
                $propertyLabels[$property->getUri()] = $property->getLabel();
            }

            foreach ($propertiesValues as $propertyUri => $propertyValues) {
                foreach ($propertyValues as $value) {
                    $rawValue = $value instanceof RdfResource
                        ? $value->getLabel()
                        : (string) $value;

                    if (trim($rawValue) !== ''
                        && self::sanitizeCategoryName($rawValue) === null
                    ) {
                        if (!isset($invalidValues[$propertyUri])) {
                            $invalidValues[$propertyUri] = [
                                'label' => $propertyLabels[$propertyUri]
                                    ?? $propertyUri,
                                'values' => [],
                            ];
                        }
                        $invalidValues[$propertyUri]['values'][] = $rawValue;
                    }
                }
            }
        }

        return $invalidValues;
    }

    /**
     * Normalize an exposed property value for automatic category generation.
     *
     * Only safe normalization is applied. Invalid identifiers are rejected instead
     * of being altered into a different category.
     *
     * @param string $value the input value
     *
     * @return string|null
     */
    public static function sanitizeCategoryName($value)
    {
        $output = preg_replace('/\s+/', '-', trim($value));
        $output = mb_strtolower($output);

        if (!self::isValidCategoryName($output)) {
            return null;
        }

        return $output;
    }

    /**
     * Check whether a category identifier follows the same contract as manual
     * category entry in test authoring.
     *
     * @param string $value the normalized value
     *
     * @return bool
     */
    public static function isValidCategoryName($value)
    {
        return is_string($value)
            && preg_match(
                '/^[a-zA-Z_][a-zA-Z0-9_-]*$/',
                $value
            ) === 1;
    }

    /**
     * Get the properties from a class that can be exposed
     *
     * @param RdfClass $class the $class
     *
     * @return RdfProperty[] the list of eligible properties
     */
    public function getElligibleProperties(RdfClass $class)
    {
        $properties = $this->getItemService()->getClazzProperties($class, new RdfClass(self::ITEM_CLASS_URI));

        return array_filter(
            $properties,
            static function (RdfProperty $property) {
                if (in_array($property->getUri(), self::$excludedPropUris, true)) {
                    return false;
                }

                $widget = $property->getWidget();

                return null !== $widget && in_array($widget->getUri(), self::$supportedWidgetUris, true);
            }
        );
    }

    /**
     * Check if a property is exposed
     *
     * @param RdfProperty $property the property to check
     *
     * @return bool true if exposed
     */
    public function doesExposeCategory(RdfProperty $property)
    {
        $exposeProperty = new RdfProperty(self::EXPOSE_PROP_URI);
        $expose = $property->getOnePropertyValue($exposeProperty);

        return !is_null($expose) && $expose->getUri() === GenerisRdf::GENERIS_TRUE;
    }

    /**
     * Expose or not a property
     *
     * @param RdfProperty $property the property to check
     * @param bool        $value    true if exposed
     *
     * @return void
     */
    public function exposeCategory(RdfProperty $property, $value)
    {
        $exposeProperty = new RdfProperty(self::EXPOSE_PROP_URI);

        if ($value == true) {
            $property->setPropertyValue($exposeProperty, GenerisRdf::GENERIS_TRUE);
        } else {
            $property->removePropertyValue($exposeProperty, GenerisRdf::GENERIS_TRUE);
        }
    }

    /**
     * Service getter and initializer.
     *
     * @return taoItems_models_classes_ItemsService the service
     */
    public function getItemService()
    {
        if (is_null($this->itemService)) {
            $this->itemService = taoItems_models_classes_ItemsService::singleton();
        }
        return $this->itemService;
    }

    /**
     * Service setter
     *
     * @param taoItems_models_classes_ItemsService $itemService the service
     *
     * @return void
     */
    public function setItemService(taoItems_models_classes_ItemsService $itemService)
    {
        $this->itemService = $itemService;
    }
}
