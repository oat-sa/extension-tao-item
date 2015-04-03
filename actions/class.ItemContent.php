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


/**
 * Items Content Controller provide access to the files of an item
 *
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoItems
 */
class taoItems_actions_ItemContent extends tao_actions_CommonModule
{

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

        $data = array();
        $browser = \oat\taoItems\model\ItemMediaRetrieval::getBrowserImplementation($subPath, $options);
        $mediaInfo = \oat\taoItems\model\ItemMediaRetrieval::getLinkAndIdentifier($subPath);
        if($browser !== false){
            $data = $browser->getDirectory($mediaInfo['link'], $filters, $depth);
        }


        echo json_encode($data);
    }
    
    /**
     * Returns whenever or not a file exists at the indicated path
     * 
     * @throws common_exception_MissingParameter
     */
    public function fileExists() {
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
        $path = $this->getRequestParameter('path');


        $fileExists = false;
        $browser = \oat\taoItems\model\ItemMediaRetrieval::getBrowserImplementation($path, $options);
        $mediaInfo = \oat\taoItems\model\ItemMediaRetrieval::getLinkAndIdentifier($path);
        if($browser !== false){
            $fileInfo = $browser->getFileInfo($mediaInfo['link'], array());
            if(!is_null($fileInfo)){
                $fileExists = true;
            }
        }
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


        $relPath = '';
        if($this->hasRequestParameter('relPath')){
            $relPath = $this->getRequestParameter('relPath');
        }

        //if the string contains something else than letters, numbers or / throw an exception
//        if(!preg_match('#^$|^[\w\/\-\._]+$#', $relPath)){
//            throw new InvalidArgumentException('The request parameter is invalid');
//        }

        $filedata = false;

        $management = \oat\taoItems\model\ItemMediaRetrieval::getManagementImplementation($relPath, $options);
        $mediaInfo = \oat\taoItems\model\ItemMediaRetrieval::getLinkAndIdentifier($relPath);
        if($management !== false){

            $file = tao_helpers_Http::getUploadedFile('content');
            if (!is_uploaded_file($file['tmp_name'])) {
                throw new common_exception_Error('Non uploaded file "'.$file['tmp_name'].'" returned from tao_helpers_Http::getUploadedFile()');
            }
            $filedata = $management->add($file['tmp_name'], $file['name'], $mediaInfo['link']);
        }
        else{
            throw new common_exception_Error('Can\'t find resource manager with identifier' . $mediaInfo['link']);
        }

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



        $browser = \oat\taoItems\model\ItemMediaRetrieval::getBrowserImplementation($this->getRequestParameter('path'), $options);
        $mediaInfo = \oat\taoItems\model\ItemMediaRetrieval::getLinkAndIdentifier($this->getRequestParameter('path'));
        if($browser !== false){
            $filePath = $browser->download($mediaInfo['link']);
            \tao_helpers_Http::returnFile($filePath);
        }
    }
    
    /**
     * Delete a file from the item directory
     * 
     * @throws common_exception_MissingParameter
     */
    public function delete() {

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

        $deleted = false;

        $mediaManagement = \oat\taoItems\model\ItemMediaRetrieval::getManagementImplementation($this->getRequestParameter('path'), $options);
        $mediaInfo = \oat\taoItems\model\ItemMediaRetrieval::getLinkAndIdentifier($this->getRequestParameter('path'));
        if($mediaManagement !== false){
            $deleted = $mediaManagement->delete($mediaInfo['link']);
        }

        echo json_encode(array('deleted' => $deleted));
    }
}
