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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 *
 *
 */

namespace oat\taoItems\model;


use oat\tao\helpers\MediaRetrieval;

class ItemMediaRetrieval extends MediaRetrieval{


    public static function getBrowserImplementation($path, $options = array(), &$link = null){
        $browser =  parent::getBrowserImplementation($path, $options, $link);
        if($browser === false){
            $mediaInfo = self::getLinkAndIdentifier($path);
            $link = $mediaInfo['link'];
            if(self::isIdentifierValid($mediaInfo['identifier'])){
                return new ItemMediaSource($options);
            }
        }
        return $browser;
    }

    public static function getManagementImplementation($path, $options = array(), &$link = null){
        $impl = self::getBrowserImplementation($path, $options, $link);

        if(in_array('oat\tao\model\media\MediaManagement', class_implements($impl))){
            return $impl;
        }

        return false;
    }

    public static function isIdentifierValid($identifier){

        if($identifier === ''){
            return true;
        }
        return parent::isIdentifierValid($identifier);
    }

} 