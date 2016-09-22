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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */

use oat\tao\model\search\tokenizer\ResourceTokenizer;

/**
 * Item content tokenizer.
 *
 * @author Joel Bout <joel@taotesting.com>
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 * @author Camille Moyon <camille@taotesting.com>
 */
class taoItems_models_classes_search_ItemContentTokenizer implements ResourceTokenizer
{
    use \oat\generis\model\OntologyAwareTrait;

    /**
     * Find item model tokenizer and send request to it to extract tokens
     *
     * @param core_kernel_classes_Resource $resource
     * @return array
     */
    public function getStrings(\core_kernel_classes_Resource $resource)
    {
        $tokenizer = $this->getItemContentTokenizer($resource);
        if (is_null($tokenizer)) {
            return [];
        }
        return $tokenizer->getStrings($resource);
    }

    /**
     * Get item content tokenizer associated to $resource e.q. item model
     * If not return null
     *
     * @param core_kernel_classes_Resource $resource
     * @return ResourceTokenizer|null
     */
    protected function getItemContentTokenizer(core_kernel_classes_Resource $resource)
    {
        try {
            /** @var core_kernel_classes_Resource $model */
            $model = $resource->getUniquePropertyValue($this->getProperty(TAO_ITEM_MODEL_PROPERTY));
            if ($model->exists()) {
                /** @var core_kernel_classes_Resource $tokenizer */
                $tokenizer = $model->getUniquePropertyValue($this->getProperty(INDEX_PROPERTY_TOKENIZER));
                if ($tokenizer->exists()) {
                    $tokenizerClass = $tokenizer->getUniquePropertyValue($this->getProperty("http://www.tao.lu/Ontologies/TAO.rdf#TokenizerClass"));
                    if ($tokenizerClass instanceof core_kernel_classes_Literal) {
                        $impl = new $tokenizerClass->literal();
                        if ($impl instanceof ResourceTokenizer) {
                            return $impl;
                        }
                    }
                }
            }

        }
        catch (core_kernel_classes_EmptyProperty $e) {}
        catch (core_kernel_classes_MultiplePropertyValuesException $e) {}

        return null;
    }
}
