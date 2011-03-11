<?php

error_reporting(E_ALL);

/**
 * Service methods to manage the Items business models using the RDF API.
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoItems
 * @subpackage models_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * The Service class is an abstraction of each service instance. 
 * Used to centralize the behavior related to every servcie instances.
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('tao/models/classes/class.GenerisService.php');

/* user defined includes */
// section 10-13-1-45-792423e0:12398d13f24:-8000:00000000000017BE-includes begin

require_once (dirname(__FILE__).'/Matching/matching_api.php');

// section 10-13-1-45-792423e0:12398d13f24:-8000:00000000000017BE-includes end

/* user defined constants */
// section 10-13-1-45-792423e0:12398d13f24:-8000:00000000000017BE-constants begin
// section 10-13-1-45-792423e0:12398d13f24:-8000:00000000000017BE-constants end

/**
 * Service methods to manage the Items business models using the RDF API.
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoItems
 * @subpackage models_classes
 */
class taoItems_models_classes_ItemsService
    extends tao_models_classes_GenerisService
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * The RDFS top level item class
     *
     * @access protected
     * @var Class
     */
    protected $itemClass = null;

    /**
     * the instance of the itemModel property
     *
     * @access protected
     * @var Property
     */
    protected $itemModelProperty = null;

    /**
     * the instance of the itemContent property
     *
     * @access public
     * @var Property
     */
    public $itemContentProperty = null;

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return void
     */
    public function __construct()
    {
        // section 10-13-1-45--20a3dc13:1239ebd775d:-8000:0000000000001897 begin
		
		parent::__construct();
		$this->itemClass			= new core_kernel_classes_Class( TAO_ITEM_CLASS );
		$this->itemModelProperty	= new core_kernel_classes_Property(TAO_ITEM_MODEL_PROPERTY);
		$this->itemContentProperty	= new core_kernel_classes_Property(TAO_ITEM_CONTENT_PROPERTY);
		
        // section 10-13-1-45--20a3dc13:1239ebd775d:-8000:0000000000001897 end
    }

    /**
     * get an item subclass by uri. 
     * If the uri is not set, it returns the  item class (the top level class.
     * If the uri don't reference an item subclass, it returns null
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string uri
     * @return core_kernel_classes_Class
     */
    public function getItemClass($uri = '')
    {
        $returnValue = null;

        // section 127-0-1-1--5cd530d7:1249feedb80:-8000:0000000000001AE4 begin
		
		
		if(empty($uri) && !is_null($this->itemClass)){
			$returnValue= $this->itemClass;
		}
		else{
			$clazz = new core_kernel_classes_Class($uri);
			if($this->isItemClass($clazz)){
				$returnValue = $clazz;
			}
		}
		
        // section 127-0-1-1--5cd530d7:1249feedb80:-8000:0000000000001AE4 end

        return $returnValue;
    }

    /**
     * check if the class is a or a subclass of an Item
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Class clazz
     * @return boolean
     */
    public function isItemClass( core_kernel_classes_Class $clazz)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-4cd2d1f1:124910fbd83:-8000:0000000000001AD2 begin
		
		if($this->itemClass->uriResource == $clazz->uriResource){
			return true;
		}
		
		foreach($clazz->getParentClasses(true) as $parent){
			
			if($parent->uriResource == $this->itemClass->uriResource){
				$returnValue = true;
				break;
			}
		}
		
        // section 127-0-1-1-4cd2d1f1:124910fbd83:-8000:0000000000001AD2 end

        return (bool) $returnValue;
    }

    /**
     * get an item
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string identifier
     * @param  Class itemClazz
     * @param  string mode
     * @return core_kernel_classes_Resource
     */
    public function getItem($identifier,  core_kernel_classes_Class $itemClazz = null, $mode = 'uri')
    {
        $returnValue = null;

        // section 10-13-1-45-792423e0:12398d13f24:-8000:0000000000001815 begin
	
		if(is_null($itemClazz) && $mode == 'uri'){
			try{
				$resource = new core_kernel_classes_Resource($identifier);
				$itemType = $resource->getUniquePropertyValue(new core_kernel_classes_Property( RDF_TYPE ));
				$itemClazz = new core_kernel_classes_Class($itemType->uriResource);
			}
			catch(Exception $e){}
		}
		if(is_null($itemClazz)){
			$itemClazz = $this->itemClass;
		}
		if($itemClazz->uriResource != $this->itemClass->uriResource){
			
			if(!$this->isItemClass($itemClazz)){
				throw new Exception("The item class is not a valid item sub class");
			}
			
		}
		$returnValue = $this->getOneInstanceBy( $itemClazz, $identifier, $mode);
		
		
        // section 10-13-1-45-792423e0:12398d13f24:-8000:0000000000001815 end

        return $returnValue;
    }

    /**
     * delete an item
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource item
     * @return boolean
     */
    public function deleteItem( core_kernel_classes_Resource $item)
    {
        $returnValue = (bool) false;

        // section 10-13-1-45-792423e0:12398d13f24:-8000:00000000000017BB begin
		
		if(!is_null($item)){
			
			$itemFolder = $this->getItemFolder($item);
			if(is_dir($itemFolder)){
				tao_helpers_File::remove($itemFolder, true);
			}
			$runtimeFolder = $this->getRuntimeFolder($item);
			if(is_dir($runtimeFolder)){
				tao_helpers_File::remove($runtimeFolder, true);
			}
			
			$returnValue = $item->delete();
			
		}
		
        // section 10-13-1-45-792423e0:12398d13f24:-8000:00000000000017BB end

        return (bool) $returnValue;
    }

    /**
     * delete an item class or subclass
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Class clazz
     * @return boolean
     */
    public function deleteItemClass( core_kernel_classes_Class $clazz)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-4cd2d1f1:124910fbd83:-8000:0000000000001ACF begin
		
		if(!is_null($clazz)){
			if($this->isItemClass($clazz)){
				$returnValue = $clazz->delete();
			}
		}
        // section 127-0-1-1-4cd2d1f1:124910fbd83:-8000:0000000000001ACF end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getItemFolder
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource item
     * @return string
     */
    public function getItemFolder( core_kernel_classes_Resource $item)
    {
        $returnValue = (string) '';

        // section 127-0-1-1-2473cce:12c31050806:-8000:0000000000002880 begin
        
        if(!is_null($item)){
        	$folderName = substr($item->uriResource, strpos($item->uriResource, '#') + 1);
        	$returnValue = ROOT_PATH . '/taoItems/data/' . $folderName;
        }
        
        // section 127-0-1-1-2473cce:12c31050806:-8000:0000000000002880 end

        return (string) $returnValue;
    }

    /**
     * Short description of method getRuntimeFolder
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource item
     * @return string
     */
    public function getRuntimeFolder( core_kernel_classes_Resource $item)
    {
        $returnValue = (string) '';

        // section 127-0-1-1--2174cec8:12c311b88e7:-8000:0000000000002883 begin
        
    	if(!is_null($item)){
        	$folderName = substr($item->uriResource, strpos($item->uriResource, '#') + 1);
        	$returnValue = ROOT_PATH . '/taoItems/views/runtime/' . $folderName;
        }
        
        // section 127-0-1-1--2174cec8:12c311b88e7:-8000:0000000000002883 end

        return (string) $returnValue;
    }

    /**
     * define the content of item to be inserted by default (to prevent null
     * after creation)
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource item
     * @return core_kernel_classes_Resource
     */
    public function setDefaultItemContent( core_kernel_classes_Resource $item)
    {
        $returnValue = null;

        // section 127-0-1-1-c213658:12568a3be0b:-8000:0000000000001CE9 begin
		
        $content = '';
  
		$itemContent = $item->getOnePropertyValue($this->itemContentProperty);
		if(is_null($itemContent) && $this->isItemModelDefined($item)){
			if($this->hasItemModel($item, array(TAO_ITEM_MODEL_HAWAI))){
				$content = file_get_contents(TAO_ITEM_HAWAI_TPL_FILE);
				$content = str_replace('{ITEM_URI}', $item->uriResource, $content);
				$this->setItemContent($item, $content);
			}
			if($this->hasItemModel($item, array(TAO_ITEM_MODEL_CAMPUS))){
				$content = file_get_contents(TAO_ITEM_CAMPUS_TPL_FILE);
				$content = str_replace('{ITEM_URI}', $item->uriResource, $content);
				$this->setItemContent($item, $content);
			}
		}
		
		$returnValue = $item;
		
        // section 127-0-1-1-c213658:12568a3be0b:-8000:0000000000001CE9 end

        return $returnValue;
    }

    /**
     * Enables you to get the content of an item, 
     * usually an xml string
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource item
     * @param  boolean preview
     * @param  string lang
     * @return string
     */
    public function getItemContent( core_kernel_classes_Resource $item, $preview = false, $lang = '')
    {
        $returnValue = (string) '';

        // section 127-0-1-1-61b30d97:12ba603bd1d:-8000:00000000000025EA begin
        
        if(!is_null($item)){
        	
        	$itemContent = null;
        	
        	if(empty($lang)){
    			$itemContent = $item->getOnePropertyValue($this->itemContentProperty);
        	}
        	else{
        		$itemContents = $item->getPropertyValuesByLg($this->itemContentProperty, $lang);
        		if($itemContents->count() > 0){
        			$itemContent = $itemContents->get(0);
        		}
        	}
        	
			if(!is_null($itemContent) && $this->isItemModelDefined($item)){
				
				if(core_kernel_classes_File::isFile($itemContent)){
					if($preview && $this->hasItemModel($item, array(TAO_ITEM_MODEL_HAWAI))){
						$tmpFile = $this->getItemFolder($item).'/tmp_black.xml';
						if(file_exists($tmpFile)){
							$returnValue = file_get_contents($tmpFile);
						}
					}
					else{
						$file = new core_kernel_classes_File($itemContent->uriResource);
						$returnValue = file_get_contents($file->getAbsolutePath());
					}
				}
			}
        }
			
        // section 127-0-1-1-61b30d97:12ba603bd1d:-8000:00000000000025EA end

        return (string) $returnValue;
    }

    /**
     * Check if the item has an itemContent Property
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource item
     * @param  string lang
     * @return boolean
     */
    public function hasItemContent( core_kernel_classes_Resource $item, $lang = '')
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--380e02a0:12ba9a8eb52:-8000:00000000000025F6 begin
        
        if(!is_null($item)){
        	if(empty($lang)){
        		$returnValue = !is_null($item->getOnePropertyValue($this->itemContentProperty));
        	}
        	else{
		        $itemContents = $item->getPropertyValuesByLg($this->itemContentProperty, $lang);
		        $returnValue = ($itemContents->count() > 0);
        	}
        }
        
        // section 127-0-1-1--380e02a0:12ba9a8eb52:-8000:00000000000025F6 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method setItemContent
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource item
     * @param  string content
     * @param  string lang
     * @return boolean
     */
    public function setItemContent( core_kernel_classes_Resource $item, $content, $lang = '')
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-2473cce:12c31050806:-8000:000000000000287C begin
        
        if(!is_null($item)){
        	
        	if($this->isItemModelDefined($item)){

        		if($this->hasItemContent($item, $lang)){
        			
        			$itemContent = null;
		        	if(empty($lang)){
		    			$itemContent = $item->getOnePropertyValue($this->itemContentProperty);
		        	}
		        	else{
		        		$itemContents = $item->getPropertyValuesByLg($this->itemContentProperty, $lang);
		        		if($itemContents->count() > 0){
		        			$itemContent = $itemContents->get(0);
		        		}
		        	}
        			if(core_kernel_classes_File::isFile($itemContent)){
        				$file = new core_kernel_classes_File($itemContent->uriResource);
        				if(file_put_contents($file->getAbsolutePath(), $content) > 0){
        					 $returnValue = true;
        				}
        			}
        		}
        		else{
        		
	        		$itemModel = $item->getUniquePropertyValue($this->itemModelProperty);
	        		$dataFile = $itemModel->getOnePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_DATAFILE_PROPERTY));
	        		$itemDir = $this->getItemFolder($item);
	        		if(!is_dir($itemDir)){
	        			mkdir($itemDir);
	        		}
	        		if(empty($lang)){
	        			$file = core_kernel_classes_File::create($dataFile, $itemDir .'/');
	        			$item->setPropertyValue($this->itemContentProperty, $file->uriResource);
	        		}
	        		else{
	        			$file = core_kernel_classes_File::create($lang.'_'.$dataFile, $itemDir .'/');
	        			$item->setPropertyValueByLg($this->itemContentProperty, $file->uriResource, $lang);
	        		} 
        			if(file_put_contents($file->getAbsolutePath(), $content) > 0){
        				 $returnValue = true;
        			}
        		}	
        	}
        }
        
        // section 127-0-1-1-2473cce:12c31050806:-8000:000000000000287C end

        return (bool) $returnValue;
    }

    /**
     * Check if the Item has on of the itemModel property in the models array
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource item
     * @param  array models the list of URI of the itemModel to check
     * @return boolean
     */
    public function hasItemModel( core_kernel_classes_Resource $item, $models)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-49582216:12ba4862c6b:-8000:00000000000025DF begin
        
        if(!is_null($item)){
    		try{
        		$itemModel = $item->getUniquePropertyValue($this->itemModelProperty);
	        	if($itemModel instanceof core_kernel_classes_Resource){
	        		if(in_array($itemModel->uriResource, $models)){
	        			$returnValue = true;
	        		}
	        	}
        	}
        	catch(common_Exception $ce){
        		$returnValue = false;
        	}
        }
        // section 127-0-1-1-49582216:12ba4862c6b:-8000:00000000000025DF end

        return (bool) $returnValue;
    }

    /**
     * Check if the itemModel has been defined for that item
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource item
     * @return boolean
     */
    public function isItemModelDefined( core_kernel_classes_Resource $item)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--380e02a0:12ba9a8eb52:-8000:00000000000025F3 begin
        
    	if(!is_null($item)){
    		
    		$model = $item->getOnePropertyValue($this->itemModelProperty);
    	 	if ($model instanceof core_kernel_classes_Literal){
    			if(strlen((string)$model) > 0){
    				$returnValue = true;
    			}
    		}
    		else if(!is_null($model)){
				$returnValue = true;
    		}
    		
		}
        
        // section 127-0-1-1--380e02a0:12ba9a8eb52:-8000:00000000000025F3 end

        return (bool) $returnValue;
    }

    /**
     * Get the runtime associated to the item model.
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource item
     * @return core_kernel_classes_Resource
     */
    public function getModelRuntime( core_kernel_classes_Resource $item)
    {
        $returnValue = null;

        // section 127-0-1-1--380e02a0:12ba9a8eb52:-8000:00000000000025F9 begin
        
        if(!is_null($item)){
        	try{
        		$itemModel = $item->getUniquePropertyValue($this->itemModelProperty);
				if(!is_null($itemModel)){
	        		$returnValue = $itemModel->getOnePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_RUNTIME_PROPERTY));
				}
			}
        	catch(common_Exception $ce){}
        }
        
        // section 127-0-1-1--380e02a0:12ba9a8eb52:-8000:00000000000025F9 end

        return $returnValue;
    }

    /**
     * Short description of method hasModelStatus
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource item
     * @param  array status
     * @return boolean
     */
    public function hasModelStatus( core_kernel_classes_Resource $item, $status)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--203e680b:12cfebcad50:-8000:00000000000029C2 begin
        
    	if(!is_null($item)){
    		if(!is_array($status) && is_string($status)){
    			$status = array($status);
    		}
    		try{
        		$itemModel = $item->getUniquePropertyValue($this->itemModelProperty);
        		if($itemModel instanceof core_kernel_classes_Resource){
	        		$itemModelStatus = $itemModel->getUniquePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_STATUS_PROPERTY));
        			if(in_array($itemModelStatus->uriResource, $status)){
	        			$returnValue = true;
	        		}
	        	}
        	}
        	catch(common_Exception $ce){
        		$returnValue = false;
        	}
        }
        
        // section 127-0-1-1--203e680b:12cfebcad50:-8000:00000000000029C2 end

        return (bool) $returnValue;
    }

    /**
     * Deploy the item in parameter
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource item
     * @param  string path
     * @param  string url
     * @param  array parameters
     * @return boolean
     */
    public function deployItem( core_kernel_classes_Resource $item, $path, $url = '', $parameters = array())
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-61b30d97:12ba603bd1d:-8000:00000000000025EE begin
        
        if(!is_null($item)){
        	
        	//parameters that could not be rewrited
        	if(!isset($parameters['root_url']))		{ $parameters['root_url'] 		= ROOT_URL; }
        	if(!isset($parameters['base_www']))		{ $parameters['base_www'] 		= BASE_WWW; }
        	if(!isset($parameters['taobase_www']))	{ $parameters['taobase_www'] 	= TAOBASE_WWW; }
        	if(!isset($parameters['debug']))		{ $parameters['debug'] 			= false; }
        	if(!isset($parameters['raw_preview']))	{ $parameters['raw_preview'] 	= false; }
        	
        	taoItems_models_classes_TemplateRenderer::setContext($parameters, 'ctx_');
        	
        	$deployableItems = array(
        		TAO_ITEM_MODEL_KOHS,
        		TAO_ITEM_MODEL_CTEST,
        		TAO_ITEM_MODEL_HAWAI,
        		TAO_ITEM_MODEL_QTI,
        		TAO_ITEM_MODEL_XHTML
        	);
        	
        	if($this->hasItemModel($item, $deployableItems)){
        		
        		$itemFolder = dirname($path);

        		$output = '';
        		
        		if($this->hasItemModel($item, array(TAO_ITEM_MODEL_QTI))){
	        		
        			//for the QTI Item
	        		$qtiService = tao_models_classes_ServiceFactory::get('taoItems_models_classes_QTI_Service');
	        		$qtiItem = $qtiService->getDataItemByRdfItem($item);
	        	
	        		if(!is_null($qtiItem)) {
	        			$output = $qtiService->renderItem($qtiItem);
	        		}
        		}
        		else if($this->hasItemModel($item, array(TAO_ITEM_MODEL_KOHS, TAO_ITEM_MODEL_CTEST, TAO_ITEM_MODEL_HAWAI))){
        			
        			$uri 		= tao_helpers_Uri::encode($item->uriResource);
        			$clazz 		= $this->getClass($item);
        			$clazzUri	= tao_helpers_Uri::encode($clazz->uriResource);
        			
        			
	        		if($this->hasItemModel($item, array(TAO_ITEM_MODEL_HAWAI))){
						$itemContent = $this->getItemContent($item, true);
					}
					else{
						$itemContent = $this->getItemContent($item, false);
					}
        			
					$dataFile = $itemFolder.'/data.xml';
					file_put_contents($dataFile, $itemContent);
					
        			$variables = array(
        				'label' 		=> $item->getLabel(),
        				'uri'			=> $uri,
        				'runtime'		=> BASE_URL . '/models/ext/itemRuntime/'. $this->getModelRuntime($item),
        				'contentUrl'	=> urlencode(str_replace(ROOT_PATH, ROOT_URL, $dataFile))
        			);
        			$templateRenderer = new taoItems_models_classes_TemplateRenderer(ROOT_PATH.'/taoItems/views/templates/swf_container_ref.tpl.php', $variables);
        			$output	= $templateRenderer->render();
        			
        		}
        		else{
        			$output	= $this->getItemContent($item);
        		}
        		
        		//replace relative paths to resources by absolute uris to help the compilator
				$matches = array();
        		if(preg_match_all("/(href|src|data|\['imagePath'\])\s*=\s*[\"\'](.+?)[\"\']/is", $output, $matches) > 0){
					if(isset($matches[2])){
						
						foreach($matches[2] as $relUri){
							if($relUri != '#' && !preg_match("/^http/", $relUri) ){
							
								if(preg_match('/(.)+\/filemanager\/views\/data\//i', $relUri)){
									//check if the file is contained in the file manager
									$absoluteUri = preg_replace('/(.)+\/filemanager\/views\/data\//i', ROOT_URL . '/filemanager/views/data/', $relUri);
								}else{
									$absoluteUri = dirname($url) . '/' . preg_replace(array("/^\./", "/^\//"), '', $relUri);
								}
								
								$output = str_replace($relUri, $absoluteUri, $output);
							}
						}
					}
				}
				
        		if(file_put_contents($path, $output)){
        			$returnValue = true;
        		}
        		
        		if($returnValue){
        		
        			$itemFileName = '';
	        		$itemModel = $item->getOnePropertyValue($this->itemModelProperty);
		        	if(!is_null($itemModel)){
		        		$itemFileName = (string)$itemModel->getOnePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_DATAFILE_PROPERTY));
		        	}
        			
        			//copy the resources
        			$sourceFolder = $this->getItemFolder($item);
        			foreach(scandir($sourceFolder) as $file){
        				if($file != basename($path) && $file != $itemFileName &&$file != '.' && $file != '..'){
        					tao_helpers_File::copy($sourceFolder.'/'.$file, $itemFolder.'/'.$file, true);
        				}
        			}
        			
	        		//copy the event.xml if not present
	        		if(!file_exists($itemFolder.'/events.xml')){
	        			$eventXml = file_get_contents(ROOT_PATH.'/taoItems/data/events_ref.xml');
	        			if(is_string($eventXml) && !empty($eventXml)){
	        				$eventXml = str_replace('{ITEM_URI}', $item->uriResource, $eventXml);
	        				@file_put_contents($itemFolder.'/events.xml', $eventXml);
	        			}
	        		}
        		}
			}
        }
        
        // section 127-0-1-1-61b30d97:12ba603bd1d:-8000:00000000000025EE end

        return (bool) $returnValue;
    }

    /**
     * Get the file linked to an item
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string itemUri
     * @return string
     */
    public function getAuthoringFileUriByItem($itemUri)
    {
        $returnValue = (string) '';

        // section 127-0-1-1-188be92e:12507f7441c:-8000:0000000000001B79 begin
		
		if(strlen($itemUri) > 0){
			$returnValue = TAO_ITEM_AUTHORING_BASE_URI.'/'.tao_helpers_Uri::encode($itemUri).'.xml';			
		}
        // section 127-0-1-1-188be92e:12507f7441c:-8000:0000000000001B79 end

        return (string) $returnValue;
    }

    /**
     * get the item uri linked to the given file
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string uri
     * @return string
     */
    public function getAuthoringFileItemByUri($uri)
    {
        $returnValue = (string) '';

        // section 127-0-1-1-188be92e:12507f7441c:-8000:0000000000001B7D begin
		if(strlen($uri) > 0){
			if(file_exists($uri)){
				$returnValue = tao_helpers_Uri::decode(
					str_replace(TAO_ITEM_AUTHORING_BASE_URI.'/', '',
						str_replace('.xml', '', $uri)
					)
				);
			}
		}
        // section 127-0-1-1-188be92e:12507f7441c:-8000:0000000000001B7D end

        return (string) $returnValue;
    }

    /**
     * Get the file linked to an item
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string itemUri
     * @return string
     */
    public function getAuthoringFile($itemUri)
    {
        $returnValue = (string) '';

        // section 127-0-1-1-34d7bcb9:1250bcb34b1:-8000:0000000000001B6E begin
		$uri = $this->getAuthoringFileUriByItem($itemUri);
		
		if(!file_exists($uri)){
			file_put_contents($uri, '<?xml version="1.0" encoding="utf-8" ?>');
		}
		$returnValue = $uri;
		
        // section 127-0-1-1-34d7bcb9:1250bcb34b1:-8000:0000000000001B6E end

        return (string) $returnValue;
    }

    /**
     * Service to get the temporary authoring file
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string itemUri
     * @param  boolean fallback
     * @return string
     */
    public function getTempAuthoringFile($itemUri, $fallback = false)
    {
        $returnValue = (string) '';

        // section 127-0-1-1-5249fce9:12694acf215:-8000:0000000000001E84 begin
		
		if(strlen($itemUri) > 0){
			$returnValue = TAO_ITEM_AUTHORING_BASE_URI.'/tmp_'.tao_helpers_Uri::encode($itemUri).'.xml';	
			if(!file_exists($returnValue)){
				if($fallback){	//fallback in case of error otheerwise create  the file
					$returnValue = $this->getAuthoringFile($itemUri);
				}
			}
		}
		
        // section 127-0-1-1-5249fce9:12694acf215:-8000:0000000000001E84 end

        return (string) $returnValue;
    }

    /**
     * Service to get the matching data of an item
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource itemRdf
     * @return array
     */
    public function getMatchingData( core_kernel_classes_Resource $itemRdf)
    {
        $returnValue = array();

        // section 127-0-1-1-554f2bd6:12c176484b7:-8000:0000000000002B26 begin

        if(!is_null($itemRdf)){
        	// If QTI Item
        	if($this->hasItemModel($itemRdf, array(TAO_ITEM_MODEL_QTI))){
        
            	$qtiService = tao_models_classes_ServiceFactory::get("taoItems_models_classes_QTI_Service");
            	$item = $qtiService->getDataItemByRdfItem ($itemRdf);
           	 	$returnValue = $item->getMatchingData ();
        	}
        }
        
        // section 127-0-1-1-554f2bd6:12c176484b7:-8000:0000000000002B26 end

        return (array) $returnValue;
    }

    /**
     * Service to evaluate an item
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource itemRdf
     * @param  responses
     * @return array
     */
    public function evaluate( core_kernel_classes_Resource $itemRdf,    $responses)
    {
        $returnValue = array();

        // section 127-0-1-1-3d6b7ea7:12c3643ac5e:-8000:0000000000002BB9 begin
               
         if(!is_null($itemRdf)){
            // If QTI Item
            if($this->hasItemModel($itemRdf, array(TAO_ITEM_MODEL_QTI))){
                
                $itemMatchingData = $this->getMatchingData ($itemRdf);
                
                matching_init ();
                matching_setRule ($itemMatchingData["rule"]);
                matching_setAreaMaps ($itemMatchingData["areaMaps"]);
                matching_setMaps ($itemMatchingData["maps"]);
                matching_setCorrects ($itemMatchingData["corrects"]);
                matching_setResponses ($responses);
                matching_setOutcomes ($itemMatchingData["outcomes"]);
                
                try {
                    // Evaluate the user's response
                    matching_evaluate ();
                    // get the outcomes
                    $outcomes = matching_getOutcomes ();
                    
                    // Check if outcomes are scalar
                    try {
                        foreach ($outcomes as $outcome) {
                            if (! is_scalar($outcome['value'])){
                                throw new Exception ('taoItems_models_classes_ItemsService::evaluate outcomes are not scalar');
                            }
                        }
                        $returnValue = $outcomes;
                    } catch (Exception $e) { }
                } catch (Exception $e) { }
            }
        }
        
        // section 127-0-1-1-3d6b7ea7:12c3643ac5e:-8000:0000000000002BB9 end

        return (array) $returnValue;
    }

    /**
     * Short description of method cloneInstance
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource instance
     * @param  Class clazz
     * @return core_kernel_classes_Resource
     */
    public function cloneInstance( core_kernel_classes_Resource $instance,  core_kernel_classes_Class $clazz = null)
    {
        $returnValue = null;

        // section 127-0-1-1--721a46fd:12ca1f35467:-8000:000000000000290E begin
        
   		$returnValue = $this->createInstance($clazz);
		if(!is_null($returnValue)){
			
			$itemFolder = $this->getItemFolder($instance);
			$fileNameProp = new core_kernel_classes_Property(PROPERTY_FILE_FILENAME);
			
			foreach($clazz->getProperties(true) as $property){
				
				if($property->uriResource == RDFS_TYPE){
					continue;
				}
				
				$range = $property->getRange();
				
				if($range->uriResource == CLASS_GENERIS_FILE){
					
					foreach($instance->getPropertyValuesCollection($property)->getIterator() as $propertyValue){
						if(core_kernel_classes_File::isFile($propertyValue)){
							$file = new core_kernel_classes_File($propertyValue->uriResource);
							$relPath = basename($file->getAbsolutePath());
							if(!empty($relPath)){
								$newPath = tao_helpers_File::concat(array($this->getItemFolder($returnValue), $relPath));
								tao_helpers_File::copy(dirname($file->getAbsolutePath()), dirname($newPath), true);
								if(file_exists($newPath)){
									$newFile = core_kernel_classes_File::create((string)$file->getOnePropertyValue($fileNameProp), dirname($newPath).'/');
									$returnValue->setPropertyValue($this->itemContentProperty, $newFile->uriResource);
								}
							}
						}
					}
				}
				else{
					foreach($instance->getPropertyValues($property) as $propertyValue){
						$returnValue->setPropertyValue($property, $propertyValue);
					}
				}
			}
			$label = $instance->getLabel();
			$cloneLabel = "$label bis";
			if(preg_match("/bis/", $label)){
				$cloneNumber = (int)preg_replace("/^(.?)*bis/", "", $label);
				$cloneNumber++;
				$cloneLabel = preg_replace("/bis(.?)*$/", "", $label)." bis $cloneNumber" ;
			}
			
			$returnValue->setLabel($cloneLabel);
		}
        
        // section 127-0-1-1--721a46fd:12ca1f35467:-8000:000000000000290E end

        return $returnValue;
    }

} /* end of class taoItems_models_classes_ItemsService */

?>