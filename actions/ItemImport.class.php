<?php
require_once('tao/actions/Import.class.php');

/**
 * This controller provide the actions to import items 
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package tao
 * @subpackage action
 *
 */
class ItemImport extends Import {

	/**
	 * action to perform on a posted QTI file
	 * @param array $formValues the posted data
	 */
	protected function importQTIFile($formValues){
		$clazz = new core_kernel_classes_Class(tao_helpers_Uri::decode($this->getSessionAttribute('classUri')));
		
		var_dump($formValues['source']);
	}
}
?>
