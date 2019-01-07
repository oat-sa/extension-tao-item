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
 * Copyright (c) 2016-2018 (original work) Open Assessment Technologies SA
 *
 */

use oat\taoItems\model\CategoryService;
use oat\generis\model\OntologyAwareTrait;

/**
 * Category controller
 * How to expose RDF properties to categorize them.
 *
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 */
class taoItems_actions_Category extends tao_actions_CommonModule
{
    use OntologyAwareTrait;

    /**
     * Request
     *  taoItems/Category/getExposedsByClass?id=${classUri}
     * Response
     *  { success : bool, data : { propUri : exposed } }
     */
    public function getExposedsByClass()
    {
        $id = $this->getRequestParameter('id');

        if (is_null($id) || empty($id)) {
            return $this->returnBadParameter('The class URI is required');
        }

        $class = $this->getClass($id);

        $service = $this->getServiceLocator()->get(CategoryService::SERVICE_ID);

        $data = [];
        $properties = $service->getElligibleProperties($class);
        foreach ($properties as $property) {
            $data[$property->getUri()] = $service->doesExposeCategory($property);
        }

        return $this->returnSuccess($data);
    }

    /**
     * Request
     *  taoItems/Category/setExposed?id=${propUri}&expose=(true|false)
     * Response
     *  { success : bool, data : bool}
     */
    public function setExposed()
    {
        $id = $this->getRequestParameter('id');

        if (is_null($id) || empty($id)) {
            return $this->returnBadParameter('The property URI is required');
        }

        $exposed = $this->getRequestParameter('exposed');
        if (is_null($exposed) || empty($exposed) || ($exposed != 'true' && $exposed != 'false')) {
            return $this->returnBadParameter('The exposed value is missing or incorrect');
        }

        $service = $this->getServiceLocator()->get(CategoryService::SERVICE_ID);
        $service->exposeCategory(new \core_kernel_classes_Property($id), $exposed == 'true');

        return $this->returnSuccess(true);
    }

    /**
     * Format response when a wrong/missing parameter is sent
     *
     * @param string $message the error message
     *
     * @return void
     */
    private function returnBadParameter($message)
    {
        return $this->returnJson([
            'success'   => false,
            'errorCode' => 412,
            'errorMsg'  => $message,
            'version'   => TAO_VERSION
        ], 412);
    }

    /**
     * Format successful response
     *
     * @param mixed $data the response data
     *
     * @return void
     */
    private function returnSuccess($data)
    {
        return $this->returnJson([
            'success' => true,
            'data'    => $data,
            'version' => TAO_VERSION
        ]);
    }
}
