<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/scripts/class.MigrateUnversionedItems.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 07.05.2012, 15:53:18 with ArgoUML PHP module 
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

    /**
     * Short description of attribute items
     *
     * @access public
     * @var array
     */
    public $items = array();

    /**
     * Short description of attribute itemModels
     *
     * @access public
     * @var array
     */
    public $itemModels = array();

    /**
     * Short description of attribute itemService
     *
     * @access public
     * @var Service
     */
    public $itemService = null;

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
		$this->itemContentProperty = new core_kernel_classes_Property(TAO_ITEM_CONTENT_PROPERTY);
		$itemClass = new core_kernel_classes_Class(TAO_ITEM_CLASS);
		$items = $itemClass->getInstances(true);
		$this->itemModels = array();
		$this->items = array();
		
		self::out('generis default language : '.DEFAULT_LANG);
		foreach($items as $item){
			
			$itemModel = $this->itemService->getItemModel($item);
			if(!is_null($itemModel)){
				
				//lazy loading item model data:
				$itemModelLabel = '';
				$dataFile = '';
				if(isset($this->itemModels[$itemModel->uriResource])){
					$itemModelLabel = $this->itemModels[$itemModel->uriResource]['label'];
					$dataFile = $this->itemModels[$itemModel->uriResource]['file'];
				}else{
					$itemModelLabel = $itemModel->getLabel();
					$dataFile = $itemModel->getUniquePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_DATAFILE_PROPERTY))->literal;
					$this->itemModels[$itemModel->uriResource] = array(
						'label' => $itemModelLabel,
						'file' => $dataFile
					);
				}
				
				$this->setItemData($item, 'model', $itemModel);
				
				//migrate items with an item model only:
				self::out('migrating item '.$itemModelLabel.' : '.$item->getLabel(). ' ('.$item->uriResource.')', array('color'=>'light_cyan'));
				
				//switch from script parameters to one of these options:
				$this->migrateToNewItemPath($item);
//				$this->migrateToUnversionedItem($item);
//				$this->migrateToVersionedItem($item);
				
			}
			
		}
		
		
        // section 127-0-1-1--698399da:1370ca5efd2:-8000:00000000000039C4 end
    }

    /**
     * migrate item content location to the new one (from TAO2.1 to 2.2) :
     * taoItems/data/i123456 -> taoItems/data/i123456/EN
     *
     * @access protected
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource item
     * @return boolean
     */
    protected function migrateToNewItemPath( core_kernel_classes_Resource $item)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-4425969b:13726750fb5:-8000:00000000000039D0 begin
		
		//used languages:
		$model = $this->itemModels[$this->getItemData($item, 'model')];
		$itemModelLabel = $model['label'];
		$dataFile = $model['file'];
		$usedLanguages = $item->getUsedLanguages($this->itemContentProperty);
		$oldSourceFolder = substr($item->uriResource, strpos($item->uriResource, '#') + 1);
		$oldSourceFolder = ROOT_PATH . '/taoItems/data/' . $oldSourceFolder . '/';
		$propItemContent = new core_kernel_classes_Property(TAO_ITEM_CONTENT_PROPERTY);
		$propFilePath = new core_kernel_classes(PROPERTY_FILE_FILEPATH);
		switch ($itemModel->uriResource) {
			case TAO_ITEM_MODEL_QTI: {
					foreach ($usedLanguages as $usedLanguage) {

						//copy all item resources
						$destinationFolder = $this->itemService->getItemFolder($item, $usedLanguage);
						self::out('copying ' . $oldSourceFolder . ' to ' . $destinationFolder);

						//copy item start point:
						if ($usedLanguage == DEFAULT_LANG || $usedLanguage == '') {
							$source = $oldSourceFolder . $dataFile;
						} else {
							$source = $oldSourceFolder . $usedLanguage . '_' . $dataFile;
						}

						$destination = $destinationFolder . '/' . $dataFile;

						if (file_exists($source)) {

							self::out('copying ' . $source . ' to ' . $destination);
							continue;
							tao_helpers_File::copy($source, $destination);
							
							$content = file_get_contents($source);
							foreach ($item->getPropertyValuesByLg($propItemContent, $usedLanguage) as $file) {
								$file->editPropertyValues($propFilePath, $destinationFolder);
							}
							$this->itemService->setItemContent($item, $content, $usedLanguage);
						}
					}
					break;
				}
			case TAO_ITEM_MODEL_XHTML: {
					//copy all item resources
					$destination = $this->itemService->getItemFolder($item, $usedLanguage);
					self::out('copying ' . $oldSourceFolder . ' to ' . $destination);
					break;
				}
			default : {
					self::out('unknown item type : ' . $itemModel->uriResource);
				}
		}
		
		$returnValue = true;
		
        // section 127-0-1-1-4425969b:13726750fb5:-8000:00000000000039D0 end

        return (bool) $returnValue;
    }

    /**
     * version all tao items from items created in TAO 2.2 or migrated by the
     * 'migrateToNewItemPath' : e.g.
     * taoItems/data/i123456/EN -> generis/data/versioning/DEFAULT/i123465/itemContent/EN
     *
     * @access protected
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource item
     * @return boolean
     */
    protected function migrateToVersionedItem( core_kernel_classes_Resource $item)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-4425969b:13726750fb5:-8000:00000000000039D3 begin
		
		if(!helpers_Versioning::isEnabled()){
			self::out('generis versioning is not enabled');
			return $returnValue;
		}
		
		//copy item content folder to the versioned path
		//get old file content, set file content
		//commit changes
		
		//used languages:
		$model = $this->itemModels[$this->getItemData($item, 'model')];
		$itemModelLabel = $model['label'];
		$dataFile = $model['file'];
		$usedLanguages = $item->getUsedLanguages($this->itemContentProperty);
		
		$oldSourceFolder = substr($item->uriResource, strpos($item->uriResource, '#') + 1);
		$oldSourceFolder = ROOT_PATH . '/taoItems/data/' . $oldSourceFolder . '/';
		$propItemContent = new core_kernel_classes_Property(TAO_ITEM_CONTENT_PROPERTY);
		switch ($itemModel->uriResource) {
			case TAO_ITEM_MODEL_QTI:
			case TAO_ITEM_MODEL_XHTML:{
				foreach ($usedLanguages as $usedLanguage) {

					$destinationFolder = $this->itemService->getItemFolder($item, $usedLanguage) . '/' . $dataFile;

					//copy item start point
					if ($usedLanguage == DEFAULT_LANG || $usedLanguage == '') {
						$oldSourceFolder .= DEFAULT_LANG;
					} else {
						$oldSourceFolder .= $usedLanguage;
					}
					$source = $oldSourceFolder.'/'.$dataFile;
					$destination = $destinationFolder . '/' . $dataFile;

					self::out('versioning ' . $destinationFolder . ' to ' . $destinationFolder);
					if (file_exists($oldSourceFolder) && is_dir($oldSourceFolder)) {

						//first copy all source files from source to destination:
						self::out('copying ' . $oldSourceFolder . ' to ' . $destinationFolder);
						tao_helpers_File::copy($oldSourceFolder, $destinationFolder);

						//delete the old data file
						$content = file_get_contents($source);
						foreach ($item->getPropertyValuesByLg($propItemContent, $usedLanguage)->getIterator() as $file) {
							$file->delete(true);
						}

						//set the versioned file content
						$this->itemService->setItemContent($item, $content, $usedLanguage);
					}
				}
				break;
			}
			default : {
				self::out('unknown item type : ' . $itemModel->uriResource);
				return $returnValue;
			}
		}
		
		$returnValue = true;
		
        // section 127-0-1-1-4425969b:13726750fb5:-8000:00000000000039D3 end

        return (bool) $returnValue;
    }

    /**
     * unversion all tao items for items created in TAO 2.2 or migrated by the
     * 'migrateToNewItemPath' : e.g.
     * generis/data/versioning/DEFAULT/i123465/itemContent/EN -> taoItems/data/i123456/EN
     *
     * @access protected
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource item
     * @return boolean
     */
    protected function migrateToUnversionedItem( core_kernel_classes_Resource $item)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-4425969b:13726750fb5:-8000:00000000000039D6 begin
        // section 127-0-1-1-4425969b:13726750fb5:-8000:00000000000039D6 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method setItemData
     *
     * @access protected
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource item
     * @param  string key
     * @param  string value
     * @return boolean
     */
    protected function setItemData( core_kernel_classes_Resource $item, $key, $value)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-4425969b:13726750fb5:-8000:00000000000039D9 begin
		if(!isset($this->items[$item->uriResource])){
			$this->items[$item->uriResource] = array();
		}
		$this->items[$item->uriResource][$key] = $value;
		$returnValue = true;
		
        // section 127-0-1-1-4425969b:13726750fb5:-8000:00000000000039D9 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getItemData
     *
     * @access protected
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource item
     * @param  string key
     * @return mixed
     */
    protected function getItemData( core_kernel_classes_Resource $item, $key)
    {
        $returnValue = null;

        // section 127-0-1-1-4425969b:13726750fb5:-8000:00000000000039DE begin
		if(isset($this->items[$item->uriResource]) && $this->items[$item->uriResource][$key]){
			$returnValue = $this->items[$item->uriResource][$key];
		}
        // section 127-0-1-1-4425969b:13726750fb5:-8000:00000000000039DE end

        return $returnValue;
    }

} /* end of class taoItems_scripts_MigrateUnversionedItems */

?>