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
use League\Flysystem\FileNotFoundException;
use oat\oatbox\filesystem\FileSystemService;
use oat\oatbox\service\ServiceManager;
use oat\tao\model\media\MediaManagement;
use oat\tao\model\service\Directory;
use oat\taoQtiItem\model\qti\Item;
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
     * @return Item
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
     * @param string $parentLink
     * @param array $acceptableMime
     * @param int $depth
     * @return array
     * @throws \common_Exception
     * @throws \tao_models_classes_FileNotFoundException
     * @throws common_exception_Error
     */
    public function getDirectory($parentLink = '', $acceptableMime = array(), $depth = 1)
    {

        if (! tao_helpers_File::securityCheck($parentLink)) {
            throw new common_exception_Error(__('Your path contains error'));
        }

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

        if ($depth <= 0) {
            $data['parent'] = $parentLink;
            return $data;
        }

        $children = array();

        /** @var Directory $directory */
        $itemDirectory = taoItems_models_classes_ItemsService::singleton()->getItemDirectory($this->item, $this->lang);
        $subDirectory = null;

        if ($parentLink == '/') {
             $directory = $itemDirectory;
        } elseif ($itemDirectory->hasDirectory($parentLink)) {
            $directory = $itemDirectory->getDirectory($parentLink);
        } else {
            throw new \FileNotFoundException($parentLink);
        }

        foreach($directory->getFlyIterator() as $content) {
            if (! $content->exists()) {
                throw new \tao_models_classes_FileNotFoundException($content->getRelativePath());
            }

            if ($directory->hasFile($content->getRelativePath())) {
                try {
                    $fileInfo = $content->getFileInfo();
                    if (empty($acceptableMime) || in_array($fileInfo['mime'], $acceptableMime)) {
                        $children[] = $fileInfo;
                    }
                } catch (FileNotFoundException $e) {
                    \common_Logger::i('Unable to retrieve fie information ("' . $e->getFile() . '")');
                }
            } elseif ($directory->hasDirectory($content->getRelativePath())) {
                $children[] = $this->getDirectory($directory->getDirectory($content)->getRelativePath());
            }
        }

        $data['children'] = $children;
        return $data;
    }

    /**
     * (non-PHPdoc)
     * @see \oat\tao\model\media\MediaBrowser::getFileInfo
     */
    public function getFileInfo($link)
    {
        \common_Logger::i($link);
        /** @var \oat\tao\model\service\File $file */
        $file = $this->getFile($link);
        $this->getMetadata($file);

    }

    protected function getMetadata(\oat\tao\model\service\File $file)
    {
        if (! $file->exists()) {
            throw new \tao_models_classes_FileNotFoundException($file->getPath());
        }

        return $file->getFileInfo();
    }

    /**
     * (non-PHPdoc)
     * @see \oat\tao\model\media\MediaBrowser::download
     */
    public function download($link)
    {
        $file = $this->getFile($link);
        if (! $file->exists()) {
            throw new \common_Exception('File to be copied does not exist ("' . $link . '").');
        }

        $tmpDir = \tao_helpers_File::createTempDir();
        if (! is_dir($tmpDir . $file->getDirname())) {
            mkdir($tmpDir . $file->getDirname(), 0755, true);
        }

        $tmpFile = $tmpDir . $file->getPath();

        if (($resource = fopen($tmpFile, 'w')) !== false) {
            stream_copy_to_stream($file->readStream(), $resource);
            fclose($resource);
            return $tmpFile;
        }

        throw new \common_Exception('Unable to write "' . $link . '" into tmp folder("' . $tmpFile . '").');
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
        $link = '/'.ltrim($parent, '/') . $fileName;
        if (! \tao_helpers_File::securityCheck($link, true)) {
            throw new \common_Exception('Unsecured filename "'.$link.'"');
        }

        return $this->writeFile($link, $source)->getFileInfo();
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

        $repository = taoItems_models_classes_ItemsService::singleton()->getDefaultFileSource();
        $filesystem = ServiceManager::getServiceManager()
            ->get(FileSystemService::SERVICE_ID)
            ->getFileSystem($repository->getUri());

        $directory = new Directory($filesystem);
        return $directory->spawnFile(ltrim(ltrim($link, '/'), '\\'));
    }

    private function writeFile($file ,$content)
    {
        $itemDirectory = taoItems_models_classes_ItemsService::singleton()->getItemDirectory($this->item, $this->lang);

        $filename = ltrim(ltrim($file, '/'), '\\');
        $resource = fopen($content, 'r');

        if ($itemDirectory->hasFile($filename)) {
            $writeSuccess = $itemDirectory->updateStream($filename, $resource);
        } else {
            $writeSuccess = $itemDirectory->writeStream($filename, $resource);
        }
        fclose($resource);

        if (! $writeSuccess) {
            throw new \common_Exception('Unable to write file ("' . $filename. '")');
        }

        return $itemDirectory->getFile($filename);
    }

}
