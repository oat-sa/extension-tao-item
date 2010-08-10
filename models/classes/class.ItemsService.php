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
		
		try{
			$itemContent = $item->getOnePropertyValue(new core_kernel_classes_Property(TAO_ITEM_CONTENT_PROPERTY));//it is ok if it is null
			$itemModel = $item->getUniquePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_PROPERTY));
			
			if($itemModel instanceof core_kernel_classes_Resource){
				$content = (string)$itemContent;
				if(trim($content) == ''){
					switch($itemModel->uriResource){
						
						case TAO_ITEM_MODEL_WATERPHENIX:
							$content = file_get_contents(TAO_ITEM_HAWAI_TPL_FILE);
							$content = str_replace('{ITEM_URI}', $item->uriResource, $content);
							
							$item = $this->bindProperties($item, array(
								TAO_ITEM_CONTENT_PROPERTY => $content
							));
							break;
						case TAO_ITEM_MODEL_CAMPUS:
							$content = file_get_contents(TAO_ITEM_CAMPUS_TPL_FILE);
							$content = str_replace('{ITEM_URI}', $item->uriResource, $content);
							
							$item = $this->bindProperties($item, array(
								TAO_ITEM_CONTENT_PROPERTY => $content
							));
							break;
					}
				}
			}
		}
		catch(Exception $e){}
		$returnValue = $item;
		
        // section 127-0-1-1-c213658:12568a3be0b:-8000:0000000000001CE9 end

        return $returnValue;
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

} /* end of class taoItems_models_classes_ItemsService */

?>