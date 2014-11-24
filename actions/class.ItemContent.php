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

    private function getBrowserImplementationClass($identifier){

        if($identifier === 'local'){
            return 'taoItems_helpers_ResourceManager';
        }
        return \oat\tao\model\media\MediaSource::getMediaBrowserSource($identifier);
    }

    private function getManagementImplementationClass($identifier){

        if($identifier === 'local'){
            return 'taoItems_helpers_ResourceManager';
        }
        return \oat\tao\model\media\MediaSource::getMediaManagementSource($identifier);
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

        $options = array('item'=>$item, 'lang'=>$itemLang);

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

        $clazz = $this->getBrowserImplementationClass($identifier);
        $resourceManager = new $clazz($options);
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
        //as upload may be called multiple times, we remove the session lock as soon as possible
        session_write_close();
        $options = array();
        if ($this->hasRequestParameter('uri')) {
            $itemUri = $this->getRequestParameter('uri');
            $item = new core_kernel_classes_Resource($itemUri);
            $options['item'] = $item;
        }

        if ($this->hasRequestParameter('lang')) {
            $itemLang = $this->getRequestParameter('lang');
            $options['lang'] = $itemLang;
        }

        if (!$this->hasRequestParameter('path')) {
            throw new common_exception_MissingParameter('path', __METHOD__);
        }


        $relPath = trim($this->getRequestParameter('path'), '/');
        if($this->hasRequestParameter('relPath')){
            $relPath = trim($this->getRequestParameter('relPath'), '/');
        }
        if(!strpos($relPath, '/')){
            $identifier = $relPath;
            $subPath = '/';
        }
        else{
            $identifier = substr($relPath, 0, strpos($relPath, '/'));
            $subPath = substr($relPath, strpos($relPath, '/') + 1);
        }
        $subPath = empty($subPath) ? '' : $subPath.'/';

        $clazz = $this->getManagementImplementationClass($identifier);
        $mediaManagement = new $clazz($options);

        $file = tao_helpers_Http::getUploadedFile('content');
        $filedata = $mediaManagement->upload($file,$subPath);

        echo json_encode($filedata);
    }

    /**
     * Download a file to the item directory* 
     * @throws common_exception_MissingParameter
     */
    public function download() {
        $options = array();
        if ($this->hasRequestParameter('uri')) {
            $itemUri = $this->getRequestParameter('uri');
            $item = new core_kernel_classes_Resource($itemUri);
            $options['item'] = $item;
        }

        if ($this->hasRequestParameter('lang')) {
            $itemLang = $this->getRequestParameter('lang');
            $options['lang'] = $itemLang;
        }

        if (!$this->hasRequestParameter('path')) {
            throw new common_exception_MissingParameter('path', __METHOD__);
        }

        $identifier = substr($this->getRequestParameter('path'), 0, strpos($this->getRequestParameter('path'), '/'));
        $subPath = substr($this->getRequestParameter('path'), strpos($this->getRequestParameter('path'), '/'));
        if(strlen($subPath) === 0){
            $subPath = '/';
        }

        $clazz = $this->getBrowserImplementationClass($identifier);
        $mediaBrowser = new $clazz($options);

        $mediaBrowser->download($subPath);
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
        $relPath = ltrim($this->getRequestParameter('path'), '/');
        $relPath = substr($relPath, strpos($relPath, '/'));
        $path = $baseDir.$relPath;

        //TODO path traversal and null byte poison check ? 
        if(is_file($path) && !is_dir($path)){
            $deleted = unlink($path);
        } 
        echo json_encode(array('deleted' => $deleted));
    }
}
