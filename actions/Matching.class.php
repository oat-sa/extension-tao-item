<?php

require (dirname(__FILE__).'/../models/classes/Matching/matching_api.php');

/**
 * Matching Controller provide actions to match an item
 * 
 * @author Cédric Alfonsi, <taosupport@tudor.lu>
 * @package taoItems
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @todo return the result of the evaluation if the test/delivery ... has been configured to return the outcomes
 */
class Matching extends Api {
	
    /**
     * Evaluate user's reponses 
     * @todo Check if the data sent by the user are compliant with our standart (and secure) 
     * @public
     */
	public function evaluate () 
	{
        $returnValue = array();
        
        if($this->hasRequestParameter('token') && $this->hasRequestParameter('data')){
            $token = $this->getRequestParameter('token');
            if($this->authenticate($token)){

            	$this->itemService = tao_models_classes_ServiceFactory::get("taoItems_models_classes_ItemsService");
            	
                $env = $this->getExecutionEnvironment();
                $itemURI = $env[TAO_ITEM_CLASS]['uri'];
                
                $itemMatchingData = $this->itemService->getMatchingData (new core_kernel_classes_Resource($itemURI));
                
                matching_init ();
				matching_setRule ($itemMatchingData["rule"]);
				matching_setMaps ($itemMatchingData["maps"]);
				matching_setCorrects ($itemMatchingData["corrects"]);
				matching_setResponses (json_decode($_POST['data']));
				matching_setOutcomes ($itemMatchingData["outcomes"]);
				matching_evaluate ();
				
				$returnValue = matching_getOutcomes ();
            }
        }
		
		echo json_encode ($returnValue);
	}

    /**
     * TESTING TESTING TESTING TESTING
     * Evaluate user's reponses 
     * @public
     */
    public function evaluateDebug () {
        // Get parameters
        $item_path = $this->getRequestParameter('item_path');

        // Load the qti items service
        $this->qtiService = tao_models_classes_ServiceFactory::get("taoItems_models_classes_QTI_Service");
        
        // get the item
        $item = $this->qtiService->loadItemFromFile ($item_path);

        // Get matching data
        $itemMatchingData = $item->getMatchingData ();

        matching_init ();
        matching_setRule ($itemMatchingData["rule"]);
        matching_setMaps ($itemMatchingData["maps"]);
        matching_setCorrects ($itemMatchingData["corrects"]);
        matching_setResponses (json_decode($_POST['data']));
        matching_setOutcomes ($itemMatchingData["outcomes"]);
        matching_evaluate ();

        $returnValue = matching_getOutcomes ();
        echo json_encode ($returnValue);
    }

    /**
     * TESTING TESTING TESTING TESTING
     * Get Item DATA Matching 
     * @public
     */
    public function getItemMatchingDataDebug () {
        $item_path =  urldecode($this->getRequestParameter('item_path'));

        // Load the qti items service
        $this->qtiService = tao_models_classes_ServiceFactory::get("taoItems_models_classes_QTI_Service");
        
        // get the item
        $item = $this->qtiService->loadItemFromFile ($item_path);

        // Get matching data
        $itemMatchingData = $item->getMatchingData ();

        echo json_encode ($itemMatchingData);
    }

}
?>
