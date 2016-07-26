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
     * Get a array representing the tree of directory
     *
     * @see \oat\tao\model\media\MediaBrowser::getDirectory
     * @param string $parentLink
     * @param array $acceptableMime
     * @param int $depth
     * @return array
     * @throws \FileNotFoundException
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
        $subDirectory = null;

        if ($parentLink == '/') {
             $directory = $this->getItemDirectory();
        } elseif ($this->getItemDirectory()->hasDirectory($parentLink)) {
            $directory = $this->getItemDirectory()->getDirectory($parentLink);
        } else {
            throw new \FileNotFoundException($parentLink);
        }

        foreach($directory->getIterator() as $content) {
            if (! $content->exists()) {
                throw new \tao_models_classes_FileNotFoundException($content->getRelativePath());
            }

            if ($content->isFile()) {
                try {
                    $fileInfo = $content->getFileInfo();
                    if (empty($acceptableMime) || in_array($fileInfo['mime'], $acceptableMime)) {
                        $children[] = $fileInfo;
                    }
                } catch (FileNotFoundException $e) {
                    \common_Logger::i('Unable to retrieve fie information ("' . $e->getFile() . '")');
                }
            } elseif ($content->isDir()) {
                $children[] = $content->getRelativePath();
            }
        }

        $data['children'] = $children;
        return $data;
    }

    /**
     * Return file info regarding a file
     *
     * @see \oat\tao\model\media\MediaBrowser::getFileInfo
     * @param string $link
     * @return array
     * @throws \tao_models_classes_FileNotFoundException
     * @throws common_exception_Error
     */
    public function getFileInfo($link)
    {
        /** @var \oat\tao\model\service\File $file */
        $file = $this->getFile($link);
        if (! $file->exists()) {
            throw new \tao_models_classes_FileNotFoundException($file->getPath());
        }
        return $file->getFileInfo();

    }

    /**
     * Copy file content to temp file. Path is return to be downloaded
     *
     * @see \oat\tao\model\media\MediaBrowser::download
     * @param string $link
     * @return string
     * @throws \common_Exception
     * @throws \tao_models_classes_FileNotFoundException
     * @throws common_exception_Error
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

    /**
     * Return filename of given path
     *
     * @see \oat\tao\model\media\MediaBrowser::getBaseName
     * @param string $link
     * @return string
     */
    public function getBaseName($link)
    {
        return basename($link);
    }

    /**
     * Add content to file
     *
     * @see \oat\tao\model\media\MediaManagement::add
     * @param string $source
     * @param string $fileName
     * @param string $parent
     * @return array
     * @throws \common_Exception
     * @throws \tao_models_classes_FileNotFoundException
     */
    public function add($source, $fileName, $parent)
    {
        $link = '/'.ltrim($parent, '/') . $fileName;
        if (! \tao_helpers_File::securityCheck($link, true)) {
            throw new \common_Exception('Unsecured filename "'.$link.'"');
        }

        $filename = ltrim(ltrim($link, '/'), '\\');
        $resource = fopen($source, 'r');

        if ($this->getItemDirectory()->hasFile($filename)) {
            $writeSuccess = $this->getItemDirectory()->updateStream($filename, $resource);
        } else {
            $writeSuccess = $this->getItemDirectory()->writeStream($filename, $resource);
        }
        fclose($resource);

        if (! $writeSuccess) {
            throw new \common_Exception('Unable to write file ("' . $filename. '")');
        }

        return $this->getItemDirectory()->getFile($filename)->getFileInfo();
    }

    /**
     * Delete the file located at $link
     *
     * @see \oat\tao\model\media\MediaManagement::delete
     * @param $link
     * @return bool
     * @throws \tao_models_classes_FileNotFoundException
     * @throws common_exception_Error
     */
    public function delete($link)
    {
        return $this->getFile($link)->delete();
    }

    /**
     * Return file stream of file located at $link
     *
     * @see tao/models/classes/media/MediaBrowser.php:getFileStream
     * @param string $link
     * @return Stream
     * @throws \tao_models_classes_FileNotFoundException
     * @throws common_exception_Error
     */
    public function getFileStream($link)
    {
        return $this->getFile($link)->readStream();
    }

    /**
     * Get file object associated to $link, search in main item directory or specific item directory
     *
     * @param $link
     * @return \oat\tao\model\service\File
     * @throws \common_Exception
     * @throws \tao_models_classes_FileNotFoundException
     * @throws common_exception_Error
     */
    private function getFile($link)
    {
        if(!tao_helpers_File::securityCheck($link)){
            throw new common_exception_Error(__('Your path contains error'));
        }

        $service = taoItems_models_classes_ItemsService::singleton();
        $repository = $service->getDefaultFileSource();
        $filesystem = ServiceManager::getServiceManager()
            ->get(FileSystemService::SERVICE_ID)
            ->getFileSystem($repository->getUri());

        $directory = new Directory($filesystem);
        $filename = ltrim(ltrim($link, '/'), '\\');


        if ($directory->hasFile($filename)) {
            return $directory->getFile($filename);
        } elseif ($this->getItemDirectory()->hasFile($filename)) {
            return $this->getItemDirectory()->getFile($filename);
        } else {
            throw new \tao_models_classes_FileNotFoundException($filename);
        }

    }

    /**
     * Get item directory based on $this->item & $this->lang
     *
     * @return Directory
     * @throws \common_Exception
     */
    private function getItemDirectory()
    {
        return taoItems_models_classes_ItemsService::singleton()->getItemDirectory($this->item, $this->lang);
    }

}
