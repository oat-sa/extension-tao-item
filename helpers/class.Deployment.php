<?php
/*  
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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

/**
 * Helper for the deployment of items
 * 
 * @access public
 * @author Bout Joel, <joel@taotesting.com>
 * @package taoItems
 * @subpackage helpers
 */
class taoItems_helpers_Deployment
{
	private static $defaultMedia = array("jpg","jpeg","png","gif","mp3",'swf','wma','wav', 'css', 'js');
	
	public static function copyResources($sourceFolder, $destination, $excludeFiles = array())
	{
		//copy the resources
		$exclude = array_merge($excludeFiles, array('.', '..'));
		foreach(scandir($sourceFolder) as $file) {
			if(!in_array($file, $exclude)) {
				common_Logger::i('Copy '.$sourceFolder . $file.' to '.$destination . $file);
				tao_helpers_File::copy(
					$sourceFolder . $file,
					$destination . $file,
					true
				);
			}
		}

	}
	
	public static function retrieveExternalResources($xhtml, $destination)
	{
		if(!file_exists($destination)){
			if (!mkdir($destination)) {
				throw new common_Exception('Folder '.$destination.' could not be created');
			}
		}

		$authorizedMedia = self::$defaultMedia;
		
		$mediaList = array();
		$expr = "/http[s]?:\/\/[^<'\"&?]+\.(".implode('|',$authorizedMedia).")/mi";
		preg_match_all($expr, $xhtml, $mediaList, PREG_PATTERN_ORDER);

		$uniqueMediaList = 	array_unique($mediaList[0]);
		
		foreach($uniqueMediaList as $mediaUrl){
			// This is a file that has to be stored in the item compilation folder itself...
			// I do not get why they are all copied. They are all there they were copied from the item module...
			// But I agree that remote resources (somewhere on the Internet) should be copied via curl.
			// So if the URL does not matches a place where the TAO server is, we curl the resource and store it.
			// FileManager files should be considered as remote resources to avoid 404 issues. Indeed, a backoffice
			// user might delete an image in the filemanager during a delivery campain. This is dangerous.
			$mediaPath = self::retrieveFile($mediaUrl, $destination);
			if(!empty($mediaPath) && $mediaPath !== false){
				$xhtml = str_replace($mediaUrl, basename($mediaUrl), $xhtml, $replaced);//replace only when copyFile is successful
			}
		}
		return $xhtml;
	}

	protected static function retrieveFile($url, $destination){
	
		$fileName = basename($url);
		//check file name compatibility: 
		//e.g. if a file with a common name (e.g. car.jpg, house.png, sound.mp3) already exists in the destination folder
		while(file_exists($destination.$fileName)){
			$lastDot = strrpos($fileName, '.');
			$fileName = substr($fileName, 0, $lastDot).'_'.substr($fileName, $lastDot);
		}
			
		// Since the file has not been downloaded yet, start downloading it using cUrl
		// Only if the resource is external to TAO or in the filemanager of the current instance.
		if(!preg_match('@^' . BASE_URL . '@', $url)){

			common_Logger::i('Downloading '.$url);
			set_time_limit(0);
			$fp = fopen ($destination.$fileName, 'w+');
			$curlHandler = curl_init();
			curl_setopt($curlHandler, CURLOPT_URL, $url);
			curl_setopt($curlHandler, CURLOPT_FILE, $fp);
			curl_setopt($curlHandler, CURLOPT_TIMEOUT, 50);
			curl_setopt($curlHandler, CURLOPT_FOLLOWLOCATION, true);
			
			//if there is an http auth on the local domain, it's mandatory to auth with curl
			if(USE_HTTP_AUTH){	
				$addAuth = false;
				$domains = array('localhost', '127.0.0.1', ROOT_URL);
				foreach($domains as $domain){
					if(preg_match("/".preg_quote($domain, '/')."/", $url)){
						$addAuth = true;
					}
				}
				if($addAuth){
					curl_setopt($curlHandler, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
					curl_setopt($curlHandler, CURLOPT_USERPWD, USE_HTTP_USER.":".USE_HTTP_PASS);
				}
			}
			curl_exec($curlHandler);
			curl_close($curlHandler);
			fclose($fp);
		} else{
			common_Logger::d('Skipped download of '.$url);
			return false;
		}
		
		return $destination.$fileName;
	}
    
}
