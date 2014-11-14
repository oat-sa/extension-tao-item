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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA;
 *               
 * 
 */

use oat\tao\helpers\FileUploadException;
 
/**
 * Items Content Controller provide access to the files of an item
 *
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoItems
 */
class taoItems_actions_ItemContent extends tao_actions_CommonModule
{

    private function getImplementationClass($identifier){
        $implementations = array(
            'local' => 'taoItems_helpers_ResourceManager',
            'mediamanager' => 'oat\taoMediaManager\helpers\MediaManagerBrowser'
        );
        if(array_key_exists($identifier,$implementations)){
            return $implementations[$identifier];
        }
        return 'taoItems_helpers_ResourceManager';
    }

    /**
     * Returns a json encoded array describign a directory
     * 
     * @throws common_exception_MissingParameter
     * @return string
     */
    public function files() {
        if (!$this->hasRequestParameter('uri')) {
            throw new common_exception_MissingParameter('uri', __METHOD__);
        }
        $itemUri = $this->getRequestParameter('uri');
        $item = new core_kernel_classes_Resource($itemUri);
        
        if (!$this->hasRequestParameter('lang')) {
            throw new common_exception_MissingParameter('lang', __METHOD__);
        }
        $itemLang = $this->getRequestParameter('lang');

        $subPath = $this->hasRequestParameter('path') ? $this->getRequestParameter('path') : '/';
        $depth = $this->hasRequestParameter('depth') ? $this->getRequestParameter('depth') : 1;
       
        //build filters
        $filters = array();
        if($this->hasRequestParameter('filters')){
            $filterParameter = $this->getRequestParameter('filters');
            if(!empty($filterParameter)){
                if(preg_match('/\/\*/', $filterParameter)){
                    common_Logger::w('Stars mime type are not yet supported, filter "'. $filterParameter . '" will fail');
                }
                $filters = array_map('trim', explode(',', $filterParameter));
            }
        } 

        $identifier = substr($subPath, 0, strpos($subPath, '/'));
        $subPath = substr($subPath, strpos($subPath, '/'));
        if(strlen($subPath) === 0){
            $subPath = '/';
        }

        $clazz = $this->getImplementationClass($identifier);
        $resourceManager = new $clazz(array('item'=>$item, 'lang'=>$itemLang));
        $data = $resourceManager->getDirectory($subPath, $filters, $depth);

        echo json_encode($data);
    }
    
    /**
     * Returns whenever or not a file exists at the indicated path
     * 
     * @throws common_exception_MissingParameter
     */
    public function fileExists() {
        if (!$this->hasRequestParameter('uri')) {
            throw new common_exception_MissingParameter('uri', __METHOD__);
        }
        $itemUri = $this->getRequestParameter('uri');
        $item = new core_kernel_classes_Resource($itemUri);
        
        if (!$this->hasRequestParameter('lang')) {
            throw new common_exception_MissingParameter('lang', __METHOD__);
        }
        $itemLang = $this->getRequestParameter('lang');
        
        if (!$this->hasRequestParameter('path')) {
            throw new common_exception_MissingParameter('path', __METHOD__);
        }
        $baseDir = taoItems_models_classes_ItemsService::singleton()->getItemFolder($item, $itemLang);
        $path = $this->getRequestParameter('path');
        $safeName = dirname($path).'/'.tao_helpers_File::getSafeFileName(basename($path));
        $fileExists = file_exists($baseDir.$safeName); 
        
        echo json_encode(array(
        	'exists' => $fileExists
        ));
    }   
     
    /**
     * Upload a file to the item directory
     * 
     * @throws common_exception_MissingParameter
     */
    public function upload() {
        if (!$this->hasRequestParameter('uri')) {
            throw new common_exception_MissingParameter('uri', __METHOD__);
        }
        $itemUri = $this->getRequestParameter('uri');
        $item = new core_kernel_classes_Resource($itemUri);
        
        if (!$this->hasRequestParameter('lang')) {
            throw new common_exception_MissingParameter('lang', __METHOD__);
        }
        $itemLang = $this->getRequestParameter('lang');
        
        if (!$this->hasRequestParameter('path')) {
            throw new common_exception_MissingParameter('path', __METHOD__);
        }
    
        //as upload may be called multiple times, we remove the session lock as soon as possible
        session_write_close();
       
		//TODO path traversal and null byte poison check ? 
        $baseDir = taoItems_models_classes_ItemsService::singleton()->getItemFolder($item, $itemLang);
        $relPath = trim($this->getRequestParameter('path'), '/');
        $relPath = empty($relPath) ? '' : $relPath.'/';
      
        try{ 
            $file = tao_helpers_Http::getUploadedFile('content');
            
            $baseDir = taoItems_models_classes_ItemsService::singleton()->getItemFolder($item, $itemLang);
            $fileName = tao_helpers_File::getSafeFileName($file['name']);
            
            if(!move_uploaded_file($file["tmp_name"], $baseDir.$relPath.$fileName)){
                throw new common_exception_Error('Unable to move uploaded file');
            } 
            
            $fileData = taoItems_helpers_ResourceManager::buildFile($item, $itemLang, $relPath.$fileName);
            echo json_encode($fileData);    

        } catch(FileUploadException $fe){
            
            echo json_encode(array( 'error' => $fe->getMessage()));    
        }
    }

    /**
     * Download a file to the item directory* 
     * @throws common_exception_MissingParameter
     */
    public function download() {
        if (!$this->hasRequestParameter('uri')) {
            throw new common_exception_MissingParameter('uri', __METHOD__);
        }
        $itemUri = $this->getRequestParameter('uri');
        $item = new core_kernel_classes_Resource($itemUri);
        
        if (!$this->hasRequestParameter('lang')) {
            throw new common_exception_MissingParameter('lang', __METHOD__);
        }
        $itemLang = $this->getRequestParameter('lang');
        
        if (!$this->hasRequestParameter('path')) {
            throw new common_exception_MissingParameter('path', __METHOD__);
        }
        
        $baseDir = taoItems_models_classes_ItemsService::singleton()->getItemFolder($item, $itemLang);
        $path = $baseDir.ltrim($this->getRequestParameter('path'), '/');
        
        tao_helpers_Http::returnFile($path);
    }
    
    /**
     * Delete a file from the item directory
     * 
     * @throws common_exception_MissingParameter
     */
    public function delete() {

        $deleted = false;

        if (!$this->hasRequestParameter('uri')) {
            throw new common_exception_MissingParameter('uri', __METHOD__);
        }
        $itemUri = $this->getRequestParameter('uri');
        $item = new core_kernel_classes_Resource($itemUri);
        
        if (!$this->hasRequestParameter('lang')) {
            throw new common_exception_MissingParameter('lang', __METHOD__);
        }
        $itemLang = $this->getRequestParameter('lang');
        
        if (!$this->hasRequestParameter('path')) {
            throw new common_exception_MissingParameter('path', __METHOD__);
        }
        
        $baseDir = taoItems_models_classes_ItemsService::singleton()->getItemFolder($item, $itemLang);
        $path = $baseDir.ltrim($this->getRequestParameter('path'), '/');

        //TODO path traversal and null byte poison check ? 
        if(is_file($path) && !is_dir($path)){
            $deleted = unlink($path);
        } 
        echo json_encode(array('deleted' => $deleted));
    }
}
