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
use oat\oatbox\filesystem\Directory;
use oat\oatbox\filesystem\File;
use oat\tao\model\media\MediaManagement;
use Psr\Http\Message\StreamInterface;
use tao_helpers_File;
use taoItems_models_classes_ItemsService;
use Slim\Http\Stream;

/**
 * This media source gives access to files that are part of the item
 * and are addressed in a relative way
 */
class LocalItemSource implements MediaManagement
{

    private $item;
    
    private $lang;

    protected $itemService;

    public function __construct($data)
    {
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
            $label = $this->getItem()->getLabel();
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

        /** @var \oat\oatbox\filesystem\Directory $directory */
        $itemDirectory = $this->getItemDirectory();
        if ($parentLink != '/') {
            $directory = $itemDirectory->getDirectory($parentLink);
        } else {
            $directory = $itemDirectory;
        }

        $iterator = $directory->getFlyIterator();
        foreach ($iterator as $content) {
            if ($content instanceof Directory) {
                $children[] = $this->getDirectory($itemDirectory->getRelPath($content), $acceptableMime, $depth - 1);
            } else {
                $fileInfo = $this->getInfoFromFile($content);
                if (empty($acceptableMime) || in_array($fileInfo['mime'], $acceptableMime)) {
                    $children[] = $fileInfo;
                }
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
        return $this->getInfoFromFile($this->getFile($link));

    }

    protected function getInfoFromFile(File $file)
    {
        $link = $this->getItemDirectory()->getRelPath($file);
        return array(
            'name'     => $file->getBasename(),
            'uri'      => $link,
            'mime'     => $file->getMimeType(),
            'filePath' => $link,
            'size'     => $file->getSize(),
        );
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

        $relPath = $this->getItemDirectory()->getRelPath($file);

        $tmpDir = rtrim(\tao_helpers_File::createTempDir(), '/');
        $tmpFile = $tmpDir . $relPath;
        if (! is_dir(dirname($tmpFile))) {
            mkdir(dirname($tmpFile), 0755, true);
        }

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
        if (! \tao_helpers_File::securityCheck($fileName, true)) {
            throw new \common_Exception('Unsecured filename "'.$fileName.'"');
        }

        if (($resource = fopen($source, 'r')) === false) {
            throw new \common_Exception('Unable to read content of file ("' . $source . '")');
        }

        $file = $this->getItemDirectory()->getDirectory($parent)->getFile($fileName);
        $writeSuccess = $file->put($resource);
        fclose($resource);

        if (! $writeSuccess) {
            throw new \common_Exception('Unable to write file ("' . $fileName . '")');
        }

        return $this->getInfoFromFile($file);
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
     * @return StreamInterface
     * @throws \tao_models_classes_FileNotFoundException
     * @throws common_exception_Error
     */
    public function getFileStream($link)
    {
        return $this->getFile($link)->readPsrStream();
    }

    /**
     * Get file object associated to $link, search in main item directory or specific item directory
     *
     * @param $link
     * @return File
     * @throws \common_Exception
     * @throws \tao_models_classes_FileNotFoundException
     * @throws common_exception_Error
     */
    private function getFile($link)
    {
        if(!tao_helpers_File::securityCheck($link)){
            throw new common_exception_Error(__('Your path contains error'));
        }

        $file = $this->getItemDirectory()->getFile($link);
        if ($file->exists()) {
            return $file;
        }
        throw new \tao_models_classes_FileNotFoundException($link);
    }

    /**
     * Get item directory based on $this->item & $this->lang
     *
     * @return \oat\oatbox\filesystem\Directory
     * @throws \common_Exception
     */
    protected function getItemDirectory()
    {
        if (! $this->itemService) {
            $this->itemService = taoItems_models_classes_ItemsService::singleton();
        }
        return $this->itemService->getItemDirectory($this->item, $this->lang);
    }
}
