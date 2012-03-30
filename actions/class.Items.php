<?php
/**
 * Items Controller provide actions performed from url resolution
 * 
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoItems
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class taoItems_actions_Items extends tao_actions_TaoModule
{
	
	/**
	 * constructor: initialize the service and the default data
	 * @return  Items
	 */
	public function __construct()
	{
		
		parent::__construct();
		
		//the service is initialized by default
		$this->service = taoItems_models_classes_ItemsService::singleton();
		$this->defaultData();
		$this->setData('modelDefined', false);
		
	}

	/**
	 * Override auth method
	 * @see TaoModule::_isAllowed
	 * @return boolean
	 */	
	protected function _isAllowed()
	{
		$context = Context::getInstance();
		if($context->getActionName() != 'getItemContent'){
			return parent::_isAllowed();
		}
		return true;
	}
	
	
/*
 * conveniance methods
 */
	
	/**
	 * get the instancee of the current item regarding the 'uri' and 'classUri' request parameters
	 * @return core_kernel_classes_Resource the item instance
	 */
	protected function getCurrentInstance()
	{
		$uri = tao_helpers_Uri::decode($this->getRequestParameter('uri'));
		if(is_null($uri) || empty($uri)){
			throw new Exception("No valid uri found");
		}
		$itemClass = $this->getCurrentClass();
		$item = $this->service->getItem($uri, $itemClass);
		if(is_null($item)){
			throw new Exception("No item found for the uri {$uri}");
		}
		
		$this->setData('uri', tao_helpers_Uri::encode($item->uriResource));
		$this->setData('classUri', tao_helpers_Uri::encode($itemClass->uriResource));
		
		return $item;
	}
	
	/**
	 * get the main class
	 * @return core_kernel_classes_Classes
	 */
	protected function getRootClass()
	{
		return $this->service->getItemClass();
	}
	
/*
 * controller actions
 */

	
	/**
	 * edit an item instance
	 */
	public function editItem()
	{
	
		$itemClass = $this->getCurrentClass();
		$item = $this->getCurrentInstance();
		
		$formContainer = new taoItems_actions_form_Item($itemClass, $item);
		$myForm = $formContainer->getForm();
		
		/*
		 * crapy way to add the status of the item model
		 * @todo set this in the taoItems_actions_form_Item
		 */
		$deprecatedOptions = array();
		$statusProperty = new core_kernel_classes_Property(TAO_ITEM_MODEL_STATUS_PROPERTY);
		$itemModelElt = $myForm->getElement(tao_helpers_Uri::encode(TAO_ITEM_MODEL_PROPERTY));
		$options = $itemModelElt->getOptions();
		foreach($options as $optUri => $optLabel){
			$model = new core_kernel_classes_Resource(tao_helpers_Uri::decode($optUri));
			$status = $model->getOnePropertyValue($statusProperty);
			$statusLabel = (!is_null($status))?trim($status->getLabel()):'';
			if(!empty($statusLabel)){
				$options[$optUri] = $optLabel . " ($statusLabel)";
			}
			if(!is_null($status)){
				if($status->uriResource == TAO_ITEM_MODEL_STATUS_DEPRECATED){
					$deprecatedOptions[] = $optUri;
				}
			}
		}
		$itemModelElt->setOptions($options);
		$this->setData('deprecatedOptions', json_encode($deprecatedOptions));
		
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				
				$properties = $myForm->getValues();
				unset($properties[TAO_ITEM_CONTENT_PROPERTY]);
				unset($properties['warning']);
				
				$item = $this->service->bindProperties($item, $properties);
				$item = $this->service->setDefaultItemContent($item);
				
				$this->setData('message', __('Item saved'));
				$this->setData('reload', true);
			}
		}
		
		$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($item->uriResource));
		
		$modelDefined = $this->service->isItemModelDefined($item);
		$isDeprecated =  $this->service->hasModelStatus($item, array(TAO_ITEM_MODEL_STATUS_DEPRECATED));
		if(!$modelDefined || $isDeprecated){
			$myForm->removeElement(tao_helpers_Uri::encode(TAO_ITEM_CONTENT_PROPERTY));
		}
		$this->setData('modelDefined', $modelDefined);
		$this->setData('isDeprecated', $isDeprecated);
		
		$this->setData('formTitle', __('Edit Item'));
		$this->setData('myForm', $myForm->render());
		
		$this->setView('item_form.tpl');
	}
	
	/**
	 * Edit the row item content: download and upload the item content from the XML format 
	 */
	public function itemContentIO()
	{
		
		$item = $this->getCurrentInstance();
		$itemClass = $this->getCurrentClass();
		
		//instantiate the item content form container
		$formContainer = new taoItems_actions_form_ItemContentIO($itemClass, $item);
		$myForm = $formContainer->getForm();
		
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				
				$data = $myForm->getValues();
				
				if(isset($data['file_import']['uploaded_file'])){
					
					$extension = 'xml';
					
					//get the Xml Schema regarding the item model
					$itemModel = $item->getUniquePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_PROPERTY));
					switch($itemModel->uriResource){
					 	case TAO_ITEM_MODEL_HAWAI: /**@todo add the black schema  */
					 		break;
					 	case TAO_ITEM_MODEL_QTI:
							$schema = BASE_PATH . '/models/classes/QTI/data/imsqti_v2p0.xsd';
							break;
					 	case TAO_ITEM_MODEL_XHTML:
					 		$extension = 'html';
					 		$schema = BASE_PATH . '/models/classes/data/xhtml/xhtml.xsd';
							break;
					 	default:
					 		$modelName = strtolower(trim($itemModel->getLabel()));
					 		$schema = BASE_PATH . "/models/classes/data/{$modelName}/{$modelName}.xsd";
							break;
						
					}
					
					//parse and validate the sent file
					$parser = new tao_models_classes_Parser($data['file_import']['uploaded_file'], array('extension' => $extension));
					
					//check if the valdiation should be skipped
					$validate = true;
					if(isset($data['disable_validation'])){
						if(in_array('on', $data['disable_validation'])){
							$validate = false;	
						}
					}
					if(!$validate){
						$parser->forceValidation();
					}
					 
					if(!empty($schema)){
						//run the validation
						$parser->validate($schema);	
					}
					
					if($parser->isValid()){
						//if the file is valid, we set it as the property of the item
						$this->service->setItemContent($item, file_get_contents($data['file_import']['uploaded_file']));
						$formContainer->addDownloadSection();
						
						$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($item->uriResource));
						$this->setData('message', __('Item content saved'));
						
					}
					
					//get the errors (is empty if the file is valid)  
					$this->setData('importErrors', $parser->getErrors());
				}
			}
		}
		
		$this->setData('formTitle', __('Manage item content'));
		$this->setData('myForm', $myForm->render());
		
		$this->setView('form_content.tpl');
	}
	
	/**
	 * Preview an item
	 * @return void
	 */
	public function preview()
	{
		
		$this->setData('preview', false);
		$this->setData('previewMsg', __("Not yet available"));
		
		$itemClass = $this->getCurrentClass();
		$item = $this->getCurrentInstance();
		
		if($this->service->hasItemContent($item) && $this->service->isItemModelDefined($item)){
			$this->setData('preview', true);
			
			$options = array(
				'uri'		=>	tao_helpers_Uri::encode($item->uriResource),
				'classUri'	=> 	tao_helpers_Uri::encode($itemClass->uriResource),
				'context'	=> false,
				'match'		=> 'client'
			);
			
			if(Session::hasAttribute('previewOpts')){
				$options = array_merge($options, Session::getAttribute('previewOpts'));
			}
			
			//create the options form
			$formContainer = new taoItems_actions_form_PreviewOptions($options);
			$myForm = $formContainer->getForm();
			if($myForm->isSubmited()){
				if($myForm->isValid()){
					$previewOpts = $myForm->getValues();
					$options = array_merge($options, $previewOpts);
					Session::setAttribute('previewOpts', $previewOpts);
				}
			}
			$this->setData('optionsForm', $myForm->render());
			
			$this->setData('instanceUri', tao_helpers_Uri::encode($item->uriResource, false));
			
			//this is this url that will contains the preview
			//@see taoItems_actions_PreviewApi
			$this->setData('previewUrl', _url('runner', 'PreviewApi', 'taoItems', $options));
		}
		
		$previewTitle = __('Preview');
		if($this->hasRequestParameter('previewTitle')){
			$previewTitle = $this->getRequestParameter('previewTitle');
		}
		$this->setData('previewTitle', $previewTitle);
		
		$this->setView('preview.tpl');
	}
	
	/**
	 * Display directly the content of the preview, outside any container
	 */
	public function fullScreenPreview()
	{
		
		$itemClass = $this->getCurrentClass();
		$item = $this->getCurrentInstance();
		
		$previewUrl = $this->getPreviewUrl($item, $itemClass);
		if(is_null($previewUrl)){
			echo  __("Not yet available");
		}
		else{
			$this->redirect($previewUrl);
		}
	}
	
	/**
	 * Get the Url with right options to run the preview
	 * @param core_kernel_classes_Resource $item
	 * @param core_kernel_classes_Class    $clazz
	 * @return string|null 
	 */
	protected function getPreviewUrl(core_kernel_classes_Resource $item, core_kernel_classes_Class $clazz)
	{
		
		$previewUrl = null;
				
		if($this->service->hasItemContent($item) && $this->service->isItemModelDefined($item)){
			
			$options = array(
				'uri'		=>	tao_helpers_Uri::encode($item->uriResource),
				'classUri'	=> 	tao_helpers_Uri::encode($clazz->uriResource),
				'context'	=> false,
				'match'		=> 'client'
			);
			if(Session::hasAttribute('previewOpts')){
				$options = array_merge($options, Session::getAttribute('previewOpts'));
			}
			
			$previewUrl =  _url('runner', 'PreviewApi', 'taoItems', $options);
		}
		
		return $previewUrl;
	}
	
	
	
	/**
	 * Edit a class
	 */
	public function editItemClass()
	{
		$clazz = $this->getCurrentClass();

		if($this->hasRequestParameter('property_mode')){
			$this->setSessionAttribute('property_mode', $this->getRequestParameter('property_mode'));
		}
		
		$myForm = $this->editClass($clazz, $this->service->getItemClass());
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				if($clazz instanceof core_kernel_classes_Resource){
					$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($clazz->uriResource));
				}
				$this->setData('message', __('Class saved'));
				$this->setData('reload', true);
			}
		}
		$this->setData('formTitle', __('Edit item class'));
		$this->setData('myForm', $myForm->render());
		$this->setView('form.tpl');
	}
	
	/**
	 * Sub Class
	 * @return void
	 */
	public function addItemClass()
	{
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$subClass = $this->service->createSubClass($this->getCurrentClass());
		if($subClass instanceof core_kernel_classes_Class){
			echo json_encode(array(
				'label'	=> $subClass->getLabel(),
				'uri' 	=> tao_helpers_Uri::encode($subClass->uriResource)
			));
		}
	}
	
	/**
	 * delete an item or an item class
	 * called via ajax
	 * @see TaoModule::delete
	 * @return void
	 */
	public function delete()
	{
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		$deleted = false;
		if($this->getRequestParameter('uri')){
			$deleted = $this->service->deleteItem($this->getCurrentInstance());
		}
		else{
			$deleted = $this->service->deleteItemClass($this->getCurrentClass());
		}
		echo json_encode(array('deleted'	=> $deleted));
	}
	
	/**
	 * @see TaoModule::translateInstance
	 * @return void
	 */
	public function translateInstance()
	{
		parent::translateInstance();
		$this->setView('form.tpl', false);
	}
	
	/**
	 * Display the Item.ItemContent property value. 
	 * It's used by the authoring runtime/tools to retrieve the content
	 * @return void 
	 */
	public function getItemContent()
	{
		
		$this->setContentHeader('text/xml');
		
		try{
			//output direclty the itemContent as XML
			$preview = false;
			if($this->hasRequestParameter('preview')){
				$preview = (bool) $this->getRequestParameter('preview');
			}
			print $this->service->getItemContent($this->getCurrentInstance(), $preview);
			
		}
		catch(Exception $e){
			//print an empty response
			print '<?xml version="1.0" encoding="utf-8" ?>';
			if(DEBUG_MODE){
				print '<exception><![CDATA[';
				print $e;
				print ']]></exception>';
			}
		}
		
		return;
	}
	
	/**
	 * Download the content of the item in parameter
	 */
	public function downloadItemContent()
	{
		
		$instance = $this->getCurrentInstance();
		if($this->service->isItemModelDefined($instance)){
			
        	$itemModel = $instance->getUniquePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_PROPERTY));
			$dataFile = $itemModel->getOnePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_DATAFILE_PROPERTY));
			
			$itemContent = $this->service->getItemContent($instance, false);
			$size = strlen($itemContent);
			
			$this->setContentHeader('text/xml');
			header("Content-Length: $size");
			header("Content-Disposition: attachment; filename=\"{$dataFile}\"");
			header("Expires: 0");
			header("Cache-Control: no-cache, must-revalidate");
			header("Pragma: no-cache");
			print $itemContent;
			return;
		}
	}
	
	/**
	 * Item Authoring tool loader action
	 * @return void
	 */
	public function authoring()
	{
		
		$this->setData('error', false);
		
		try{
			$item = $this->getCurrentInstance();
			$itemClass = $this->getCurrentClass();
			
			$itemModel = $item->getUniquePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_PROPERTY));
			if($itemModel instanceof core_kernel_classes_Resource){
				
				$authoring = $itemModel->getUniquePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_AUTHORING_PROPERTY));
				
                                if($authoring instanceof core_kernel_classes_Literal){
					
					//urlencode instead of tao_helpers_Uri::encode to be compatible with the swf authoring tools
					$itemContentUrlParam = array(
						'uri' => urlencode($item->uriResource), 
						'classUri' => urlencode($itemClass->uriResource)
					);
					$itemContentUrl = urlencode(_url('getItemContent', 'Items', 'taoItems', $itemContentUrlParam));
					
					if(preg_match("/\.swf$/", (string) $authoring)){
						$this->setData('type', 'swf');
					}
					if(preg_match("/\.php$/", (string) $authoring)){
						$this->setData('type', 'php');
					}
					if(preg_match("/taoItems\//", (string) $authoring)){
						$this->redirect(ROOT_URL.(string) $authoring.'?instance='.tao_helpers_Uri::encode($item->uriResource, false).'&STANDALONE_MODE='.intval(tao_helpers_Context::check('STANDALONE_MODE')));
					}

					$this->setData('authoringFile', BASE_URL.'/models/ext/itemAuthoring/'.(string) $authoring);
					$this->setData('itemContentUrl', $itemContentUrl);
					
				}
			}
			$this->setData('instanceUri', tao_helpers_Uri::encode($item->uriResource, false));
		
		}
		catch(Exception $e){
			$this->setData('error', true);
                        
                        //build clear error or warning message:
                        if(!empty($itemModel) && $itemModel instanceof core_kernel_classes_Resource){
                                $errorMsg = __('No item authoring tool available for the selected type of item: '.$itemModel->getLabel());
                        }else{
                                $errorMsg = __('No item type selected for the current item.')." {$item->getLabel()} ".__('Please select first the item type!');
                        }
                        $this->setData('errorMsg', $errorMsg);
		}
		$this->setView('authoring.tpl');
	}
	
	/**
	 * use the xml content in session and set it to the item
	 * forwarded to the index action 
	 * @return void
	 */
	public function saveItemContent()
	{
		
		$message = __('An error occured while saving the item');

		if(isset($_SESSION['instance']) && isset($_SESSION['xml'])){

			$item = $this->service->getItem($_SESSION['instance']);
			if($this->service->isItemModelDefined($item)){
				
				$itemContentSaved = false;

				//CTEST
				 if ($this->service->hasItemModel($item, array(TAO_ITEM_MODEL_CTEST))){
					isset($_SESSION["datalg"]) ? $lang = $_SESSION["datalg"] : $lang = $GLOBALS['lang'];
					$data = "<?xml version='1.0' encoding='UTF-8'?>
								<tao:ITEM xmlns:rdf='http://www.w3.org/1999/02/22-rdf-syntax-ns#' rdf:ID='{$item->uriResource}' xmlns:tao='http://www.tao.lu/tao.rdfs' xmlns:rdfs='http://www.w3.org/2000/01/rdf-schema#'>
									<rdfs:LABEL lang='{$lang}'>{$item->getLabel()}</rdfs:LABEL>
									<rdfs:COMMENT lang='{$lang}'>{$item->getComment()}</rdfs:COMMENT>
									{$_SESSION['xml']}
								</tao:ITEM>";
					$itemContentSaved = $this->service->setItemContent($item, $data);
				}
				//OTHERS
				else{
					$itemContentSaved = $this->service->setItemContent($item, $_SESSION['xml']);
				}
				
				if(!$itemContentSaved){
					$message = __('Item saving failed');
				}else{
					$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($item->uriResource));
					$message = __('Item successfully saved');
				}
			}
	
			if(tao_helpers_Context::check('STANDALONE_MODE')){
				$itemClass = $this->service->getClass($item);
				$this->redirect(_url('authoring', 'SaSItems', 'taoItems', array('uri' => tao_helpers_Uri::encode($item->uriResource).'&classUri='.tao_helpers_Uri::encode($itemClass->uriResource), 'classUri' => tao_helpers_Uri::encode($itemClass->uriResource), 'message' => urlencode($message))));
			}
			else{
				$this->redirect(_url('index', 'Main', 'tao', array('message' => urlencode($message))));
			}
		}
	}
	
	/**
	 * Load an item external media
	 * It prevents to get it direclty in the data folder that access is denied
	 *  
	 */
	public function getMediaResource()
	{
		
		if($this->hasRequestParameter('path')){
		
			$item = null;
			if($this->hasRequestParameter('uri') && $this->hasRequestParameter('classUri')){
				$item = $this->getCurrentInstance();
			}
			else if(Session::hasAttribute('uri') && Session::hasAttribute('classUri')){
				$classUri = tao_helpers_Uri::decode(Session::getAttribute('classUri'));
				if($this->service->isItemClass(new core_kernel_classes_Class($classUri))){
					$item = new core_kernel_classes_Resource(tao_helpers_Uri::decode(Session::getAttribute('uri')));
				}
			}
			if(!is_null($item)){
				
				$path = $this->getRequestParameter('path');
				if(!tao_helpers_File::securityCheck($path)){
					throw new Exception('Unauthorized path '.$path);
				}
				
				if(preg_match('/(.)+\/filemanager\/views\/data\//i', $path)){
					//check if the file is linked to the file manager
					$resource = preg_replace('/(.)+\/filemanager\/views\/data\//i', ROOT_PATH . '/filemanager/views/data/', $path);
				}else{
					$folder 	= $this->service->getItemFolder($item);
					$resource 	= tao_helpers_File::concat(array($folder, $path));
				}
				
				
				if(file_exists($resource)){
					
					$mimeType = tao_helpers_File::getMimeType($resource);
					
					//allow only images, video, flash (and css?)
					if(preg_match("/^(image|video|audio|application\/x-shockwave-flash)/", $mimeType)){
						
						header("Content-Type: $mimeType; charset utf-8");
						print trim(file_get_contents($resource));
					}
				}
			}
		}
	}
	
	/**
	 * Authoring File mappgin service:
	 * Send into the request the parameters id and/or uri or nothing.
	 * Must be called via Ajax. 
	 * Render json response {id: id, uri: uri}
	 * @return void
	 */
	public function getAuthoringFile()
	{
		
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		$itemUri 	= $this->getRequestParameter('id');
		$uriParam 	= $this->getRequestParameter('uri');
		
		$authoringFileData = array();
		
		if(!$uriParam){
			$authoringFileData = $this->service->getItemFolder($itemUri).'/black.xml';
			
		}
		
		echo json_encode($authoringFileData);
	}
	
	/**
	 * get the  BLACK/HAWAI  temporary authoring file
	 * @return void
	 */
	public function loadTempAuthoringFile()
	{
		header("Content-Type: text/xml; charset utf-8");
		if($this->hasRequestParameter('instance')){
			$uri = tao_helpers_Uri::decode($this->getRequestParameter('instance'));
			$item = new core_kernel_classes_Resource($uri);
			$itemFolder = $this->service->getItemFolder($item);
			if(is_dir($itemFolder)){
				$tmpFile = $itemFolder.'/tmp_black.xml';
				if(file_exists($tmpFile)){
					echo file_get_contents($tmpFile);
					return;
				}
			}
		}
		//print an empty response
		echo '<?xml version="1.0" encoding="utf-8" ?>';
	}
	
	/**
	 * save the BLACK/HAWAI temporary authoring file
	 * @return void
	 */
	public function saveTempAuthoringFile()
	{
		if($this->hasRequestParameter('instance')){
			$uri = tao_helpers_Uri::decode($this->getRequestParameter('instance'));
            $xml = $this->getRequestParameter('xml');
			$item = new core_kernel_classes_Resource($uri);
			$itemFolder = $this->service->getItemFolder($item);
			if(!is_dir($itemFolder)){
				mkdir($itemFolder);
			}
			if(is_dir($itemFolder)){
				file_put_contents($itemFolder.'/tmp_black.xml', html_entity_decode($xml));
				
			}
		}
	}
}
?>