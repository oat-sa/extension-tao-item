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
use oat\tao\model\media\MediaBrowser;
use oat\tao\model\media\MediaManagement;
use \oat\tao\helpers\FileUploadException;

/**
 * This helper class aims at formating the item content folder description
 *
 */
class taoItems_helpers_ResourceManager implements MediaBrowser, MediaManagement
{

    private $item;
    private $lang;

    public function __construct($data){
        $this->item = (isset($data['item'])) ? $data['item'] : null;
        $this->lang = (isset($data['lang'])) ? $data['lang'] : '';

    }
    
    public function getDirectory($relPath = '/', $acceptableMime = array(), $depth = 1) {
        $sysPath = $this->getSysPath($relPath);

        $label = substr($relPath,strrpos($relPath, '/') + 1);
        if(!$label){
            $label = 'local';
        }

        $data = array(
            'path' => $relPath,
            'label' => $label
        );

        if ($depth > 0 ) {
            $children = array();
            if (is_dir($sysPath)) {
                foreach (new DirectoryIterator($sysPath) as $fileinfo) {
                    if (!$fileinfo->isDot()) {
                        $subPath = rtrim($relPath, '/').'/'.$fileinfo->getFilename();
                        if ($fileinfo->isDir()) {
                            $children[] = $this->getDirectory($subPath, $acceptableMime, $depth - 1);
                        } else {
                            $file = $this->getFileInfo($subPath, $acceptableMime);
                            if(!is_null($file)){
                                $children[] = $file;
                            }
                        }
                    }
                }
            } else {
                common_Logger::w('"'.$sysPath.'" is not a directory');
            }
            $data['children'] = $children;
        }
        else{
                $data['url'] = _url('files', 'ItemContent', 'taoItems', array('uri' => $this->item->getUri(),'lang' => $this->lang, 'path' => $relPath));
        }
        return $data;
    }

    public function getFileInfo($relPath, $acceptableMime) {
        $file = null;

        $filename = basename($relPath);
        $dir = ltrim(dirname($relPath),'/');

        $sysPath = $this->getSysPath($dir.'/'.$filename);

        $mime = tao_helpers_File::getMimeType($sysPath);
        if((count($acceptableMime) == 0 || in_array($mime, $acceptableMime)) && file_exists($sysPath)){
            $file = array(
                'name' => basename($sysPath),
                'mime' => $mime,
                'size' => filesize($sysPath),
                'url' => _url('download', 'ItemContent', 'taoItems', array('uri' => $this->item->getUri(),'lang' => $this->lang, 'path' => $relPath))
            );
        }
        return $file;
    }

    public function download($filename){

        $sysPath = $this->getSysPath($filename);
        tao_helpers_Http::returnFile($sysPath);
    }

    public function upload($fileTmp, $fileName, $subPath)
    {

        try{
            $fileName = tao_helpers_File::getSafeFileName($fileName);

            $sysPath = $this->getSysPath($subPath.$fileName);

            if(!move_uploaded_file($fileTmp, $sysPath)){
                throw new common_exception_Error('Unable to move uploaded file');
            }

            $fileData = $this->getFileInfo('/'.$subPath.$fileName, array());
            return $fileData;

        } catch(FileUploadException $fe){

            return array( 'error' => $fe->getMessage());
        }
    }

    public function delete($filename)
    {
        $deleted = false;

        $sysPath = $this->getSysPath($filename);
        if(is_file($sysPath) && !is_dir($sysPath)){
            $deleted = unlink($sysPath);
        }

        return $deleted;
    }

    /**
     * @param $relPath
     * @return string
     * @throws common_exception_Error
     */
    private function getSysPath($relPath){
        $baseDir = taoItems_models_classes_ItemsService::singleton()->getItemFolder($this->item, $this->lang);

        $sysPath = $baseDir.ltrim($relPath, '/');
        if(!tao_helpers_File::securityCheck($sysPath)){
            throw new common_exception_Error(__('Your path contains error'));
        }

        return $sysPath;
    }
}
