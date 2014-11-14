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

/**
 * This helper class aims at formating the item content folder description
 *
 */
class taoItems_helpers_ResourceManager implements MediaBrowser
{

    private $item;
    private $lang;

    public function __construct($datas){
        $this->item = (isset($datas['item'])) ? $datas['item'] : null;
        $this->lang = (isset($datas['lang'])) ? $datas['lang'] : '';

    }
    
    public function getDirectory($relPath = '/', $acceptableMime = array(), $depth = 1) {

        $baseDir = taoItems_models_classes_ItemsService::singleton()->getItemFolder($this->item, $this->lang);
        $path = $baseDir.ltrim($relPath, '/');

        $label = substr($relPath,strrpos($relPath, '/') + 1);
        if(!$label){
            $label = 'local';
        }

        $data = array(
            'path' => 'local'.$relPath,
            'label' => $label
        );

        if ($depth > 0 ) {
            $children = array();
            if (is_dir($path)) {
                foreach (new DirectoryIterator($path) as $fileinfo) {
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
                common_Logger::w('"'.$path.'" is not a directory');
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
        $baseDir = taoItems_models_classes_ItemsService::singleton()->getItemFolder($this->item, $this->lang);
        $path = $baseDir.ltrim($relPath, '/');
        $mime = tao_helpers_File::getMimeType($path);

        if(count($acceptableMime) == 0 || in_array($mime, $acceptableMime)){
            $file = array(
                'name' => basename($path),
                'mime' => $mime,
                'size' => filesize($path),
                'url' => _url('download', 'ItemContent', 'taoItems', array('uri' => $this->item->getUri(),'lang' => $this->lang, 'path' => $relPath))
            );
        }
        return $file;
    }

    public function download($filename){

        return $filename;
    }
    
}
