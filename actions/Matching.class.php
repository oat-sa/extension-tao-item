<?php

require (dirname(__FILE__).'/../models/classes/Matching/matching_api.php');

/**
 * Matching Controller provide actions to match an item
 * 
 * @author Cédric Alfonsi, <taosupport@tudor.lu>
 * @package taoItems
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @todo integrate to TAO
 * @todo check if responses variables are compliant to the format
 * @todo return the result of the evaluation if the test/delivery ... has been configured to return the outcomes
 */
class Matching extends Api {
	
    /**
     * Evaluate user's reponses 
     * @public
     */
	public function evaluate () 
	{
        $itemMatchingData = array ();
        $responses = json_decode($_POST['data']);
        
        if($this->hasRequestParameter('token')){
            $token = $this->getRequestParameter('token');
            if($this->authenticate($token)){
                $this->itemService = tao_models_classes_ServiceFactory::get("taoItems_models_classes_ItemsService");
                $env = $this->getExecutionEnvironment();
                $itemURI = $env['TAO_ITEM_CLASS']['uri'];
                $item = $this->qtiService->getDataItemByRdfItem ($itemURI);
                $itemMatchingData = $this->itemService->getMatchingData ($itemURI);
            }
        }
        
		matching_init ();
		matching_setRule ($itemMatchingData["rule"]);
		matching_setMaps ($itemMatchingData["maps"]);
		matching_setCorrects ($itemMatchingData["corrects"]);
		matching_setResponses ($responses);
		matching_setOutcomes ($itemMatchingData["outcomes"]);
		matching_evaluate ();
		
		$returnValue = matching_getOutcomes ();
		echo json_encode ($returnValue);
	}

    /**
     * TESTING TESTING TESTING TESTING
     * Evaluate user's reponses 
     * @public
     */
    public function evaluateDebug () {
        // Get parameters
        $params = json_decode($_POST['params'], true);
        $responses = json_decode($_POST['data']);
        if (!isset($params['item_path']))
            $params['item_path'] = $_GET['item_path'];
        $file = strpos ('\\', $params['item_path']) != -1 ? urldecode($params['item_path']) : $params['item_path'];
        
        // Load the qti items service
        $this->qtiService = tao_models_classes_ServiceFactory::get("taoItems_models_classes_QTI_Service");
        
        // get the item
        $item = $this->qtiService->loadItemFromFile ($file);

        // Get matching data
        $itemMatchingData = $item->getMatchingData ();

        matching_init ();
        matching_setRule ($itemMatchingData["rule"]);
        matching_setMaps ($itemMatchingData["maps"]);
        matching_setCorrects ($itemMatchingData["corrects"]);
        matching_setResponses ($responses);
        matching_setOutcomes ($itemMatchingData["outcomes"]);
        matching_evaluate ();

        $returnValue = matching_getOutcomes ();
        echo json_encode ($returnValue);
    }

}
?>
