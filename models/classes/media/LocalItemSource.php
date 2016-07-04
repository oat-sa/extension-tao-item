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
namespace oat\taoItems\model\media;

use common_exception_Error;
use oat\tao\model\media\MediaManagement;
use tao_helpers_File;
use taoItems_models_classes_ItemsService;
use DirectoryIterator;
use Slim\Http\Stream;
use League\Flysystem\File;
/**
 * This media source gives access to files that are part of the item
 * and are addressed in a relative way
 */
class LocalItemSource implements MediaManagement
{

    /**
     * @return \core_kernel_classes_Resource
     */
    private $item;
    
    private $lang;

    public function __construct($data){
        $this->item = (isset($data['item'])) ? $data['item'] : null;
        $this->lang = (isset($data['lang'])) ? $data['lang'] : '';

    }

    /**
     * @return \core_kernel_classes_Resource
     */
    public function getItem()
    {
        return $this->item;
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\tao\model\media\MediaBrowser::getDirectory
     */
    public function getDirectory($parentLink = '', $acceptableMime = array(), $depth = 1) {
        $sysPath = $this->getSysPath($parentLink);

        $label = rtrim($parentLink,'/');
        if(strrpos($parentLink, '/') !== false && substr($parentLink, -1) !== '/'){
            $label = substr($parentLink,strrpos($parentLink, '/') + 1);
            $parentLink = $parentLink.'/';
        }

        if(in_array($parentLink,array('','/'))){
            $label = $this->item->getLabel();
            $parentLink = '/';
        }

        $data = array(
            'path' => $parentLink,
            'label' => $label
        );

        if ($depth > 0 ) {
            $children = array();
            if (is_dir($sysPath)) {
                foreach (new DirectoryIterator($sysPath) as $fileinfo) {
                    if (!$fileinfo->isDot()) {
                        $subPath = rtrim($parentLink, '/').'/'.$fileinfo->getFilename();
                        if ($fileinfo->isDir()) {
                            $children[] = $this->getDirectory($subPath, $acceptableMime, $depth - 1);
                        } else {
                            $file = $this->getFileInfo($subPath, $acceptableMime);
                            if(!is_null($file) && (count($acceptableMime) == 0 || in_array($file['mime'], $acceptableMime))){
                                $children[] = $file;
                            }
                        }
                    }
                }
            } else {
                \common_Logger::w('"'.$sysPath.'" is not a directory');
            }
            $data['children'] = $children;
        }
        else{
                $data['parent'] = $parentLink;
        }
        return $data;
    }


    /**
     * (non-PHPdoc)
     * @see \oat\tao\model\media\MediaBrowser::getFileInfo
     */
    public function getFileInfo($link) {

        $file = $this->getFile($link);
        if ($file->exists()) {
            $file = array(
                'name' => basename($link),
                'uri' => $link,
                'mime' => $file->getMimetype(),
                'filePath' => $link,
                'size' => $file->getSize(),
            );
        } else {
            throw new \tao_models_classes_FileNotFoundException($link);
        }
        return $file;
    }

    /**
     * (non-PHPdoc)
     * @see \oat\tao\model\media\MediaBrowser::download
     */
    public function download($link){

        $tmpFile = \tao_helpers_File::createTempDir().$this->getBaseName($link);
        $source = $this->getFile($link)->readStream();
        $dest = fopen($tmpFile, 'w');
        
        while(($l=fread($source, 65536))) { 
            fwrite($dest, $l);
        }
        fclose($dest);
        return $tmpFile;
    }

    public function getBaseName($link)
    {
        return basename($link);
    }

    /**
     * (non-PHPdoc)
     * @see \oat\tao\model\media\MediaManagement::add
     */
    public function add($source, $fileName, $parent)
    {
        $link = '/'.ltrim($parent, '/').$fileName;
        if (!\tao_helpers_File::securityCheck($link, true)) {
            throw new \common_Exception('Unsecured filename "'.$link.'"');
        }
        
        $file = $this->getFile($link);
        
        $f = fopen($source, 'r');
        $writeSucces = $file->writeStream($f);
        fclose($f);
        
        if (!$writeSucces) {
            throw new \common_exception_Error('Unable to move file '.$source);
        }

        return array(
            'name' => basename($link),
            'uri' => $link,
            'mime' => \tao_helpers_File::getMimeType($source),
            'filePath' => $link,
            'size' => filesize($source),
        );
    }

    /**
     * (non-PHPdoc)
     * @see \oat\tao\model\media\MediaManagement::delete
     */
    public function delete($link)
    {
        return $this->getFile($link)->delete();
    }
    
    public function getFileStream($link)
    {
        return new Stream($this->getFile($link)->readStream());
    }

    /**
     * Returns the local path to the file
     *  
     * @deprecated only works on local filesystem
     * @param string $link
     * @throws common_exception_Error
     * @return string
     */
    private function getSysPath($link)
    {
        $baseDir = taoItems_models_classes_ItemsService::singleton()->getItemFolder($this->item, $this->lang);

        $sysPath = $baseDir.ltrim($link, '/');
        if(!tao_helpers_File::securityCheck($sysPath)){
            throw new common_exception_Error(__('Your path contains error'));
        }

        return $sysPath;
    }
    
    
    /**
     * 
     * @param string $parentLink
     * @throws common_exception_Error
     * @return \League\Flysystem\File
     */
    private function getFile($link)
    {
        if(!tao_helpers_File::securityCheck($link)){
            throw new common_exception_Error(__('Your path contains error'));
        }
        $dir = taoItems_models_classes_ItemsService::singleton()->getItemDirectory($this->item, $this->lang);
        return new File($dir->getFileSystem(), $dir->getPath().DIRECTORY_SEPARATOR.ltrim($link, DIRECTORY_SEPARATOR));
    }
}
