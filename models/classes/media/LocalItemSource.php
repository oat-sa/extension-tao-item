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
    public function getDirectory($parentLink = '', $acceptableMime = array(), $depth = 1)
    {

        $link = ltrim($parentLink, '/');
        if (! tao_helpers_File::securityCheck($link)) {
            throw new common_exception_Error(__('Your path contains error'));
        }

        $label = rtrim($parentLink,'/');
        if(strrpos($parentLink, '/') !== false && substr($parentLink, -1) !== '/'){
            $label = substr($parentLink,strrpos($parentLink, '/') + 1);
            $parentLink = $parentLink.'/';
        }

        \common_Logger::i($parentLink);
        if(in_array($parentLink,array('','/'))){
            $label = $this->item->getLabel();
            $parentLink = '/';
        }
        \common_Logger::i($parentLink);

        $data = array(
            'path' => $parentLink,
            'label' => $label
        );

        if ($depth > 0) {
            $children = array();

            $directory = taoItems_models_classes_ItemsService::singleton()->getItemDirectory($this->item, $this->lang);
            $subDirectory = null;

            if ($parentLink == '/') {
                $subDirectory = $directory;
            } elseif ($directory->hasDirectory($link)) {
                $subDirectory = $directory->getDirectory($link);
            }

            if (! is_null($subDirectory)) {
                foreach ($subDirectory->getDirectoryIterator() as $fileInfo) {
                    $subPath = $subDirectory->getRelativePath() . $fileInfo;
                    if ($subDirectory->hasDirectory($fileInfo)) {
                        $children[] = $this->getDirectory($subPath, $acceptableMime, $depth - 1);
                    } else {
                        $file = $this->getFileInfo($subPath, $acceptableMime);
                        if (! is_null($file)
                            && (count($acceptableMime) == 0 || in_array($file['mime'], $acceptableMime))
                        ) {
                            $children[] = $file;
                        }
                    }
                }
            } else {
                \common_Logger::w('"'.$link.'" is not a directory');
            }

            $data['children'] = $children;
        } else {
            $data['parent'] = $parentLink;
        }
        \common_Logger::i(print_r($data, true));
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
            \common_Logger::i('test');
            throw new \tao_models_classes_FileNotFoundException($link);
        }
        return $file;
    }

    /**
     * (non-PHPdoc)
     * @see \oat\tao\model\media\MediaBrowser::download
     */
    public function download($link)
    {
        \common_Logger::i($link);
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

        if (! $this->writeFile($link, $source)) {
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
     * @param string $link
     * @throws common_exception_Error
     * @return \League\Flysystem\File
     */
    private function getFile($link)
    {
        if(!tao_helpers_File::securityCheck($link)){
            throw new common_exception_Error(__('Your path contains error'));
        }
        $dir = taoItems_models_classes_ItemsService::singleton()->getItemDirectory($this->item, $this->lang);
        \common_Logger::i($link);
        return $dir->spawnFile(ltrim(ltrim($link, '/'), '\\'));
    }

    private function writeFile($file ,$content)
    {
        $dir = taoItems_models_classes_ItemsService::singleton()->getItemDirectory($this->item, $this->lang);

        $fileName = ltrim(ltrim($file, '/'), '\\');
        $resource = fopen($content, 'r');

        if ($dir->hasFile($fileName)) {
            $writeSuccess = $dir->updateStream($fileName, $resource);
        } else {
            $writeSuccess = $dir->writeStream($fileName, $resource);
        }

        fclose($resource);
        return $writeSuccess;
    }

}
