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
 * Copyright (c) 2018 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */

namespace oat\taoItems\model\render;

use League\Flysystem\AwsS3v3\AwsS3Adapter;
use oat\awsTools\AwsClient;
use oat\oatbox\service\ConfigurableService;

/**
 * Interface that define a post processing for item assets, CDN Signature, url modification ...
 *
 * @access public
 * @author Antoine Robin, <antoine@taotesting.com>
 * @package taoItems
 */
interface ItemAssetsReplacement
{

    const SERVICE_ID = 'taoItems/replacement';


    /**
     * Method that allow you to modify where the link to the asset to modify url, sign it, add version number ...
     * @param string $asset the compiled link to the asset
     * @return string the computed new link to the asset
     */
    public function postProcessAssets($asset);

}
