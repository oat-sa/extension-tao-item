<?php

/**
 * actions for managing survey items
 *
 * @author Melis Matteo
 * @package taoItems
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 *
 */
class taoItems_actions_SurveyItem extends taoItems_actions_Items
{
	/**
	 *  save an item into tao
	 */
	public function save()
	{
		$xml = $GLOBALS['HTTP_RAW_POST_DATA'];
		$returnValue = taoItems_models_classes_Survey_Item::saveItem($xml);
		echo json_encode($returnValue);
	}

	/**
	 *  delete an item into tao
	 */
	public function delete()
	{
		$uri = $this->getRequestParameter('uri');
		$returnValue = taoItems_models_classes_Survey_Item::deleteItem($uri);
		echo json_encode($returnValue);
	}

	/**
	 *  PHP XSL transformation used for item render and qat ui render
	 */
	public function transformXSL()
	{
		$xml = html_entity_decode($this->getRequestParameter('xml')); // string XML
		$xsl = html_entity_decode($this->getRequestParameter('xsl')); // string XSL
//		var_dump($xml);die;
//		var_dump($xsl);die;
		$params = $this->getRequestParameter('params');
		$params = $params ? $params : array();
		// first get lang
		$service = tao_models_classes_ServiceFactory::get('tao_models_classes_UserService');
		$user = $service->getCurrentUser();
		$lang = (string) $user->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_USER_UILG))->getOnePropertyValue(new core_kernel_classes_Property(RDF_VALUE));
		// create the compilator and get the transformation
		$compilator = new taoItems_models_classes_Survey_Compilator($xsl, $lang, $params);
		echo $compilator->compile($xml);
	}

}
?>
