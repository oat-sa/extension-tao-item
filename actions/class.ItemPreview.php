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
?>
<?php
/**
 * experimental preview API 
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoItems
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class taoItems_actions_ItemPreview extends tao_actions_Api {

	public function index(){
		$this->setData('preview', false);
		$this->setData('previewMsg', __("Not yet available"));

		$item = new core_kernel_classes_Resource(tao_helpers_Uri::decode($this->getRequestParameter('uri')));

		$itemService = taoItems_models_classes_ItemsService::singleton();
		if ($itemService->hasItemContent($item) && $itemService->isItemModelDefined($item)) {
			$this->setData('preview', true);

			$options = array(
				'uri'		=>	tao_helpers_Uri::encode($item->getUri()),
				'context'	=> false,
				'match'		=> 'client'
			);

			if ($this->hasSessionAttribute('previewOpts')) {
				$options = array_merge($options, $this->getSessionAttribute('previewOpts'));
			}

			//create the options form
			$formContainer = new taoItems_actions_form_PreviewOptions($options);
			$myForm = $formContainer->getForm();
			if ($myForm->isSubmited()) {
				if ($myForm->isValid()) {
					$previewOpts = $myForm->getValues();
					$options = array_merge($options, $previewOpts);
					$this->setSessionAttribute('previewOpts', $previewOpts);
				}
			}
			$this->setData('optionsForm', $myForm->render());

			$this->setData('instanceUri', tao_helpers_Uri::encode($item->getUri(), false));

			//this is this url that will contains the preview
			//@see taoItems_actions_LegacyPreviewApi
			$this->setData('previewUrl', $this->getPreviewUrl($item));
		}

		$previewTitle = __('Preview');
		if ($this->hasRequestParameter('previewTitle')) {
			$previewTitle = $this->getRequestParameter('previewTitle');
		}
		$this->setData('previewTitle', $previewTitle);

		$this->setData('uri', tao_helpers_Uri::encode($item->getUri()));

		$this->setView('previewItemRunner.tpl');
	}

	private function insertPreviewConsole($item, $html) {
		
		//we parse the DOM of the item (it must be well formed and valid)
		$doc = new DOMDocument();
		(DEBUG_MODE)?@$doc->loadHTML($html):$doc->loadHTML($html);

		//inject the apis
		$headNodes = $doc->getElementsByTagName('head');

		foreach ($headNodes as $headNode) {
			/*
			$initScriptElt = $doc->createElement('script');
			$initScriptElt->setAttribute('type', 'text/javascript');
			$initScriptElt->setAttribute('src', BASE_WWW.'js/legacyApi/taoLegacyApi.min.js');
			
			$headNode->appendChild($initScriptElt);
			*/
			//we inject too the preview-console
			$previewScriptElt = $doc->createElement('script');
			$previewScriptElt->setAttribute('type', 'text/javascript');
			$previewScriptElt->setAttribute('src', BASE_WWW.'js/preview-console.js');
			$headNode->appendChild($previewScriptElt);
			break;
		}

		/*
		 * Render of the item by printing the HTML,
		 * so be carefull with the URLs inside the item
		 */
		return $doc->saveHTML();
	}
	
	public function getPreviewUrl($item, $options = array()) {
		
		$dom = new DOMDocument('1.0', TAO_DEFAULT_ENCODING);
    	if (!$dom->loadHTML($this->getRenderedItem($item))){
    		$msg = "An error occured while loading the XML content of the rendered item.";
    		throw new taoItems_models_classes_ItemModelException($msg);
    	}
		$isLegacyItem = taoItems_helpers_Xhtml::hasScriptElements($dom, '/taoApi/i');
    	
		$code = base64_encode($item->getUri());
		unset($options['uri'], $options['classUri']);
		if ($isLegacyItem) {
			common_Logger::i('Legacy API found');
			return _url('render/'.$code.'/index.php', 'LegacyPreviewApi', 'taoItems', $options);
		} else {
			common_Logger::i('Item API 2.0 assumed');
			return _url('render/'.$code.'/index.php', 'ItemPreview', 'taoItems', $options);
		}
	}
	
	public function render() {
		// @TODO Copy/past of resolver in need of refactoring
		$rootUrlPath	= parse_url(ROOT_URL, PHP_URL_PATH);
		$absPath		= parse_url('/'.ltrim($_SERVER['REQUEST_URI'], '/'), PHP_URL_PATH);
		if (substr($absPath, 0, strlen($rootUrlPath)) != $rootUrlPath ) {
			throw new ResolverException('Request Uri '.$request.' outside of TAO path '.ROOT_URL);
		}
		$relPath		= substr($absPath, strlen($rootUrlPath));
		list($extension, $module, $action, $codedUri, $path) = explode('/', $relPath, 5);;
		$uri = base64_decode($codedUri);
		$item = new core_kernel_classes_Resource($uri);
		if ($path == 'index.php') {
			$this->renderItem($item);
		} else {
			$this->renderResource($item, $path);
		}
	}
	
	private function getRenderedItem($item) {
		$itemModel = $item->getOnePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_PROPERTY));
		$impl = taoItems_models_classes_ItemsService::singleton()->getItemModelImplementation($itemModel);
		if (is_null($impl)) {
			throw new common_Exception('preview not supported for this item type '.$itemModel->getUri());
		}
		return $impl->render($item);
	}
	
	private function renderItem($item) {
		echo $this->insertPreviewConsole($item, $this->getRenderedItem($item));
	}
	
	private function renderResource($item, $path) {
		$folder = taoItems_models_classes_ItemsService::singleton()->getItemFolder($item);
		$filename = $folder.$path;
		if (file_exists($filename)) {
			$mimeType = tao_helpers_File::getMimeType($filename);
			header('Content-Type: '.$mimeType); 
			echo file_get_contents($filename);
		} else {
			throw new tao_models_classes_FileNotFoundException($filename);
		}
	}
}
?>