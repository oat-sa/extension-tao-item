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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */

/**
 * Generic item compiler
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoItems
 * @subpackage models_classes
 */
class taoItems_models_classes_ItemCompiler extends tao_models_classes_Compiler
{
    /**
     * 
     * @param core_kernel_file_File $destinationDirectory
     * @throws common_Exception
     * @return tao_models_classes_service_ServiceCall
     */
    public function compile(core_kernel_file_File $destinationDirectory) {
        $item = $this->getResource();
        $itemService = taoItems_models_classes_ItemsService::singleton();
        if (! $itemService->isItemModelDefined($item)) {
            throw new common_Exception('Item ' . $item->getUri() . ' has no item model during compilation');
        }
        
        $langs = $item->getUsedLanguages(new core_kernel_classes_Property(TAO_ITEM_CONTENT_PROPERTY));
        foreach ($langs as $compilationLanguage) {
        	$compiledFolder = $destinationDirectory->getAbsolutePath(). DIRECTORY_SEPARATOR . $compilationLanguage . DIRECTORY_SEPARATOR;
        	if(!is_dir($compiledFolder)){
        		mkdir($compiledFolder);
        	}
        	$itemService = taoItems_models_classes_ItemsService::singleton();
        	$itemService->deployItem($item, $compilationLanguage, $compiledFolder);
        }
        return $this->createService($item, $destinationDirectory);
    }
    
    protected function createService(core_kernel_classes_Resource $item, $destinationDirectory) {
        $service = new tao_models_classes_service_ServiceCall(new core_kernel_classes_Resource(INSTANCE_SERVICE_ITEMRUNNER));
        $service->addInParameter(new tao_models_classes_service_ConstantParameter(
            new core_kernel_classes_Resource(INSTANCE_FORMALPARAM_ITEMPATH),
            $destinationDirectory
        ));
        $service->addInParameter(new tao_models_classes_service_ConstantParameter(
            new core_kernel_classes_Resource(INSTANCE_FORMALPARAM_ITEMURI),
            $item
        ));
        
        return $service;        
    }
}