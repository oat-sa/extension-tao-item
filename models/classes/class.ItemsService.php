<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - taoItems\models\classes\class.ItemsService.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 14.09.2009, 15:14:23 with ArgoUML PHP module 
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
 *
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
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
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
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
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
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
     * Short description of attribute itemModelClass
     *
     * @access protected
     * @var Class
     */
    protected $itemModelClass = null;

    /**
     * Short description of attribute itemContentProperty
     *
     * @access protected
     * @var Property
     */
    protected $itemContentProperty = null;

    /**
     * Short description of attribute itemModelProperty
     *
     * @access protected
     * @var Property
     */
    protected $itemModelProperty = null;

    /**
     * Short description of attribute swfFileProperty
     *
     * @access protected
     * @var Property
     */
    protected $swfFileProperty = null;

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Bertrand Chevrier, <taosupport@tudor.lu>
     * @return void
     */
    public function __construct()
    {
        // section 10-13-1-45--20a3dc13:1239ebd775d:-8000:0000000000001897 begin
		
		$this->itemClass			= new core_kernel_classes_Class('http://www.tao.lu/Ontologies/TAOItem.rdf#Item');
		$this->itemModelClass		= new core_kernel_classes_Class('http://www.tao.lu/Ontologies/TAOItem.rdf#ItemModels');
		$this->itemContentProperty	= new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOItem.rdf#ItemContent');
		$this->itemModelProperty	= new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOItem.rdf#ItemModel');
		$this->swfFileProperty		= new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOItem.rdf#SWFFile');
		
        // section 10-13-1-45--20a3dc13:1239ebd775d:-8000:0000000000001897 end
    }

    /**
     * Short description of method createItem
     *
     * @access public
     * @author Bertrand Chevrier, <taosupport@tudor.lu>
     * @param  mixed itemModel
     * @param  string itemContent
     * @return core_kernel_classes_Resource
     */
    public function createItem( mixed $itemModel = null, $itemContent = '')
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
		
		$returnValue = $itemInstance
		
        // section 10-13-1-45--20a3dc13:1239ebd775d:-8000:000000000000186A end

        return $returnValue;
    }

    /**
     * Short description of method deleteItem
     *
     * @access public
     * @author Bertrand Chevrier, <taosupport@tudor.lu>
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
     * Short description of method getItem
     *
     * @access public
     * @author Bertrand Chevrier, <taosupport@tudor.lu>
     * @param  mixed identifier
     * @param  string mode
     * @return core_kernel_classes_Resource
     */
    public function getItem( mixed $identifier, $mode = 'uri')
    {
        $returnValue = null;

        // section 10-13-1-45-792423e0:12398d13f24:-8000:0000000000001815 begin
		
		$returnValue = $this->getOneResourceBy( $this->itemClass, $identifier, $mode);
		
        // section 10-13-1-45-792423e0:12398d13f24:-8000:0000000000001815 end

        return $returnValue;
    }

    /**
     * Short description of method getItemModel
     *
     * @access public
     * @author Bertrand Chevrier, <taosupport@tudor.lu>
     * @param  mixed identifier
     * @param  string mode
     * @return core_kernel_classes_Resource
     */
    public function getItemModel( mixed $identifier, $mode = 'uri')
    {
        $returnValue = null;

        // section 10-13-1-45--20a3dc13:1239ebd775d:-8000:000000000000189D begin
		
		$returnValue = $this->getOneResourceBy( $this->itemModelClass, $identifier, $mode, true);
		
        // section 10-13-1-45--20a3dc13:1239ebd775d:-8000:000000000000189D end

        return $returnValue;
    }

    /**
     * Short description of method getItems
     *
     * @access public
     * @author Bertrand Chevrier, <taosupport@tudor.lu>
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

} /* end of class taoItems_models_classes_ItemsService */

?>