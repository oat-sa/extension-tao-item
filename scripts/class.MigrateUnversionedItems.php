<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/scripts/class.MigrateUnversionedItems.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 03.05.2012, 14:47:30 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package taoItems
 * @subpackage scripts
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include tao_scripts_Runner
 *
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 */
require_once('tao/scripts/class.Runner.php');

/* user defined includes */
// section 127-0-1-1--698399da:1370ca5efd2:-8000:00000000000039C1-includes begin
// section 127-0-1-1--698399da:1370ca5efd2:-8000:00000000000039C1-includes end

/* user defined constants */
// section 127-0-1-1--698399da:1370ca5efd2:-8000:00000000000039C1-constants begin
// section 127-0-1-1--698399da:1370ca5efd2:-8000:00000000000039C1-constants end

/**
 * Short description of class taoItems_scripts_MigrateUnversionedItems
 *
 * @access public
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package taoItems
 * @subpackage scripts
 */
class taoItems_scripts_MigrateUnversionedItems
    extends tao_scripts_Runner
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method preRun
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return mixed
     */
    public function preRun()
    {
        // section 127-0-1-1--698399da:1370ca5efd2:-8000:00000000000039C2 begin
        // section 127-0-1-1--698399da:1370ca5efd2:-8000:00000000000039C2 end
    }

    /**
     * Short description of method run
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return mixed
     */
    public function run()
    {
        // section 127-0-1-1--698399da:1370ca5efd2:-8000:00000000000039C4 begin
		
		$this->itemService = taoItems_models_classes_ItemsService::singleton();
		$itemClass = new core_kernel_classes_Class(TAO_ITEM_CLASS);
		$itemContentProperty	= new core_kernel_classes_Property(TAO_ITEM_CONTENT_PROPERTY);
		$items = $itemClass->getInstances(true);
		$itemModels = array();
		self::out('generis default language : '.DEFAULT_LANG);
		foreach($items as $item){
			
			$itemModel = $this->itemService->getItemModel($item);
			if(!is_null($itemModel)){
				
				//lazy loading item model data:
				$itemModelLabel = '';
				$dataFile = '';
				if(isset($itemModels[$itemModel->uriResource])){
					$itemModelLabel = $itemModels[$itemModel->uriResource]['label'];
					$dataFile = $itemModels[$itemModel->uriResource]['file'];
				}else{
					$itemModelLabel = $itemModel->getLabel();
					$dataFile = $itemModel->getUniquePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_DATAFILE_PROPERTY))->literal;
					$itemModels[$itemModel->uriResource] = array(
						'label' => $itemModelLabel,
						'file' => $dataFile
					);
				}
				
				//migrate items with an item model only:
				self::out('migrating item '.$itemModelLabel.' : '.$item->getLabel(). ' ('.$item->uriResource.')');
				self::out($dataFile);
				
				//used languages:
				$usedLanguages = $item->getUsedLanguages($itemContentProperty);
				
				switch($itemModel->uriResource){
					case TAO_ITEM_MODEL_QTI:{
						foreach($usedLanguages as $usedLanguage){
							
						}
						break;
					}
					case TAO_ITEM_MODEL_XHTML:{
						
						break;
					}
					case TAO_ITEM_MODEL_KOHS:
					case TAO_ITEM_MODEL_CTEST:{
					
						break;
					}
					case TAO_ITEM_MODEL_HAWAI:
					case TAO_ITEM_MODEL_QCM:
					case TAO_ITEM_MODEL_CAMPUS:{
						self::out('item type deprecated : '.$itemModelLabel.' ('.$itemModel->uriResource.')');
						break;
					}
					default :{
						self::out('unknown item type : '.$itemModel->uriResource);
					}
				}
				
			}
			
		}
		
		
        // section 127-0-1-1--698399da:1370ca5efd2:-8000:00000000000039C4 end
    }

} /* end of class taoItems_scripts_MigrateUnversionedItems */

?>