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

	/**
	 * create a te:porary item, display his content and removeIt
	 * @return type
	 */
	public function preview() {
		$dir = ROOT_PATH . '/taoItems/data/surveyItems/';
		if(!is_dir($dir)) {
			mkdir($dir);
		}
		if(is_null($this->getRequestParameter('xml'))) {
			//display preview generated
			$content = file_get_contents($dir . $this->getRequestParameter('file'));
			$this->setData('content', $content);
			$this->setData('basePreview', ROOT_URL . '/taoItems/views/surveyItem/');
			$this->setView('../surveyItem/preview.tpl');
			return;
		}
		// generate preview in file
		$xml = html_entity_decode($this->getRequestParameter('xml')); // string XML
		$md5 = md5($xml);
		if(file_exists($dir.$md5)) {
			echo $md5;
			return ;
		}
		$parsed = taoItems_models_classes_Survey_Item::parseItemXml($xml);
		if ($keep = $parsed instanceof core_kernel_classes_Resource) {
			// get the item
			$item = taoItems_models_classes_Survey_Item::singleton($parsed);
			// call the item render function
		} else {
			$return = taoItems_models_classes_Survey_Item::saveItem($xml);
			if(!isset($return['uri'])) {
				throw new Exception(__('Error while rendering item'));
			}
			$item = taoItems_models_classes_Survey_Item::renderItem(new core_kernel_classes_Resource($return['uri']));
		}
//		$file = tempnam($dir, 'SurveyPreview_');
		file_put_contents($dir . $md5, $item->preRender());
		echo json_encode($md5);
//		// call the item render function
//		$render = $item->render();
		if(!$keep) {
			$item->delete();
		}
	}

}
?>
