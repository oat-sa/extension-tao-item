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
 * Compiles a test and item
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoDelivery
 * @subpackage models_classes
 */
abstract class taoItems_models_classes_Compiler extends tao_models_classes_Service
{
    /**
     * 
     * @param core_kernel_classes_Resource $item
     * @param string $destination
     * @return tao_models_classes_service_ServiceCall
     */
    public function compileItem(core_kernel_classes_Resource $item, $destination) {
        $itemService = taoItems_models_classes_ItemsService::singleton();
        if (! $itemService->isItemModelDefined($item)) {
            throw new common_Exception('Item ' . $item->getUri() . ' has no item model during compilation');
        }
        
        $langs = $item->getUsedLanguages(new core_kernel_classes_Property(TAO_ITEM_CONTENT_PROPERTY));
        foreach ($langs as $compilationLanguage) {
        	$compiledFolder = $destination. $compilationLanguage. DIRECTORY_SEPARATOR;
        	if(!is_dir($compiledFolder)){
        		mkdir($compiledFolder);
        	}
        	$itemService = taoItems_models_classes_ItemsService::singleton();
        	$itemService->deployItem($item, $compilationLanguage, $compiledFolder);
        	//$compilationResult[] = $this->deployItem($item, $compilationLanguage, $compiledFolder);
        }
        $service = new tao_models_classes_service_ServiceCall(new core_kernel_classes_Resource(INSTANCE_SERVICE_ITEMRUNNER));
        $service->addInParameter(new tao_models_classes_service_ConstantParameter(
            new core_kernel_classes_Resource(INSTANCE_FORMALPARAM_ITEMPATH),
            $compiledFolder
        ));
        return $service;
    }
    
    protected function createSubDirectory(core_kernel_file_File $directory, core_kernel_classes_Resource $resource)
    {
        $subDirectory = substr($resource->getUri(), strpos($resource->getUri(), '#') + 1);
        $relPath = $directory->getRelativePath().DIRECTORY_SEPARATOR.$subDirectory;
        $absPath = $directory->getAbsolutePath().DIRECTORY_SEPARATOR.$subDirectory;
    
        if (! is_dir($absPath)) {
            if (! mkdir($absPath)) {
                throw new taoItems_models_classes_CompilationFailedException('Could not create sub directory \'' . $absPath . '\'');
            }
        }
    
        return $directory->getFileSystem()->createFile('', $relPath);
    }
}