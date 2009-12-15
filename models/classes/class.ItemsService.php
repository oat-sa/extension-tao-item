<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - taoItems/models/classes/class.ItemsService.php
 *
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 07.12.2009, 11:49:01 with ArgoUML PHP module 
 * (last revised $Date: 2009-04-11 21:57:46 +0200 (Sat, 11 Apr 2009) $)
 *
 * @author Bertrand Chevrier, <chevrier.bertrand@gmail.com>
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
 * @author Bertrand Chevrier, <chevrier.bertrand@gmail.com>
 */
require_once('tao/models/classes/class.Service.php');

/* user defined includes */
// section 10-13-1-45-792423e0:12398d13f24:-8000:00000000000017BE-includes begin
// section 10-13-1-45-792423e0:12398d13f24:-8000:00000000000017BE-includes end

/* user defined constants */
// section 10-13-1-45-792423e0:12398d13f24:-8000:00000000000017BE-constants begin
// section 10-13-1-45-792423e0:12398d13f24:-8000:00000000000017BE-constants end

/**
 * Short description of class taoItems_models_classes_ItemsService
 *
 * @access public
 * @author Bertrand Chevrier, <chevrier.bertrand@gmail.com>
 * @package taoItems
 * @subpackage models_classes
 */
class taoItems_models_classes_ItemsService
    extends tao_models_classes_Service
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute itemClass
     *
     * @access protected
     * @var Class
     */
    protected $itemClass = null;

    /**
     * Short description of attribute itemsOntologies
     *
     * @access protected
     * @var array
     */
    protected $itemsOntologies = array('http://www.tao.lu/Ontologies/TAOItem.rdf');

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Bertrand Chevrier, <chevrier.bertrand@gmail.com>
     * @return void
     */
    public function __construct()
    {
        // section 10-13-1-45--20a3dc13:1239ebd775d:-8000:0000000000001897 begin
		
		parent::__construct();
		$this->itemClass			= new core_kernel_classes_Class( TAO_ITEM_CLASS );
		$this->loadOntologies($this->itemsOntologies);
		
        // section 10-13-1-45--20a3dc13:1239ebd775d:-8000:0000000000001897 end
    }

    /**
     * access to the top level Item class
     *
     * @access public
     * @author Bertrand Chevrier, <chevrier.bertrand@gmail.com>
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
     * Short description of method isItemClass
     *
     * @access public
     * @author Bertrand Chevrier, <chevrier.bertrand@gmail.com>
     * @param  Class clazz
     * @return boolean
     */
    public function isItemClass( core_kernel_classes_Class $clazz)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-4cd2d1f1:124910fbd83:-8000:0000000000001AD2 begin
		
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
     * Short description of method getItem
     *
     * @access public
     * @author Bertrand Chevrier, <chevrier.bertrand@gmail.com>
     * @param  string identifier
     * @param  Class itemClazz
     * @param  string mode
     * @return core_kernel_classes_Resource
     */
    public function getItem($identifier,  core_kernel_classes_Class $itemClazz = null, $mode = 'uri')
    {
        $returnValue = null;

        // section 10-13-1-45-792423e0:12398d13f24:-8000:0000000000001815 begin
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
     * Short description of method getItems
     *
     * @access public
     * @author Bertrand Chevrier, <chevrier.bertrand@gmail.com>
     * @param  array options
     * @return core_kernel_classes_ContainerCollection
     */
    public function getItems($options = array())
    {
        $returnValue = null;

        // section 10-13-1-45-792423e0:12398d13f24:-8000:00000000000017B5 begin
		
		$returnValue = $this->itemClass->getInstances();
		
        // section 10-13-1-45-792423e0:12398d13f24:-8000:00000000000017B5 end

        return $returnValue;
    }

    /**
     * Short description of method createItem
     *
     * @access public
     * @author Bertrand Chevrier, <chevrier.bertrand@gmail.com>
     * @param  string itemModel
     * @param  string itemContent
     * @return core_kernel_classes_Resource
     */
    public function createItem($itemModel = null, $itemContent = '')
    {
        $returnValue = null;

        // section 10-13-1-45--20a3dc13:1239ebd775d:-8000:000000000000186A begin
		
		if(is_string($itemModel)){
			$itemModel = $this->getItemModel(
				$itemModel, 
				(strpos($itemModel, '#') === false) ? 'label' : 'uri'
			);
		}
		if( ! $itemModel instanceof core_kernel_classes_Resource || is_null($itemModel) ){
			throw new Exception("itemModel instance is a mandatory property to create a new item");
		}
		
		$itemInstance = core_kernel_classes_ResourceFactory::create(
			$this->itemClass,
			'item_' . ($this->itemClass->getInstances()->count() + 1),
			'item created from ' . get_class($this) . ' the '. date('Y-m-d h:i:s') 
		);
		
		$itemInstance->setPropertyValue(
			$this->itemModelProperty,
			$itemModel->uriRessource
		);
		
		$itemInstance->setPropertyValue(
			$this->itemContentProperty,
			$itemContent
		);
		
		$returnValue = $itemInstance;
		
        // section 10-13-1-45--20a3dc13:1239ebd775d:-8000:000000000000186A end

        return $returnValue;
    }

    /**
     * Short description of method deleteItem
     *
     * @access public
     * @author Bertrand Chevrier, <chevrier.bertrand@gmail.com>
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
     * Short description of method deleteItemClass
     *
     * @access public
     * @author Bertrand Chevrier, <chevrier.bertrand@gmail.com>
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
     * Short description of method setDefaultItemContent
     *
     * @access public
     * @author Bertrand Chevrier, <chevrier.bertrand@gmail.com>
     * @param  Resource item
     * @return core_kernel_classes_Resource
     */
    public function setDefaultItemContent( core_kernel_classes_Resource $item)
    {
        $returnValue = null;

        // section 127-0-1-1-c213658:12568a3be0b:-8000:0000000000001CE9 begin
		
		try{
			$itemContent = $item->getUniquePropertyValue(new core_kernel_classes_Property(TAO_ITEM_CONTENT_PROPERTY));
			$itemModel = $item->getUniquePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_PROPERTY));
			if($itemContent instanceof core_kernel_classes_Literal && $itemModel instanceof core_kernel_classes_Resource){
				$content = (string)$itemContent;
				if($itemModel->uriResource == TAO_ITEM_MODEL_WATERPHENIX && trim($content) == ''){
					$content = file_get_contents(TAO_ITEM_AUTHORING_TPL_FILE);
					$content = str_replace('{ITEM_URI}', $item->uriResource, $content);
					
					$item = $this->bindProperties($item, array(
						TAO_ITEM_CONTENT_PROPERTY => $content
					));
				}
			}
		}
		catch(Exception $e){
		}
		$returnValue = $item;
		
        // section 127-0-1-1-c213658:12568a3be0b:-8000:0000000000001CE9 end

        return $returnValue;
    }

    /**
     * Short description of method getAuthoringFileUriByItem
     *
     * @access public
     * @author Bertrand Chevrier, <chevrier.bertrand@gmail.com>
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
     * Short description of method getAuthoringFileItemByUri
     *
     * @access public
     * @author Bertrand Chevrier, <chevrier.bertrand@gmail.com>
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
     * Short description of method getAuthoringFile
     *
     * @access public
     * @author Bertrand Chevrier, <chevrier.bertrand@gmail.com>
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

} /* end of class taoItems_models_classes_ItemsService */

?>