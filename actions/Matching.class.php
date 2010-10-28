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
class Matching extends Items {
	
	public function evaluate () {
		$params = json_decode($_POST['params'], true);
		$responses = json_decode($_POST['data']);
		
		// the xml QTI item
		$file = $params['tmp_item_path'];
		
		// parse the xml to extract data
		$qtiParser = new taoItems_models_classes_QTI_Parser($file);
		$item = $qtiParser->load();
		
		// Get the rule
		$rule = $item->getResponseProcessing ()->getRule();
		
		// Get the correct responses (correct variables and map variables)
		$corrects = Array ();
		$maps = Array ();
		$interactions = $item->getInteractions();
		foreach ($interactions as $interaction){
		    $correctJSON = $interaction->getResponse ()->correctToJSON();
            if ($correctJSON != null)
            {
                array_push ($corrects, $correctJSON);   
            }
            
            $mapJson = $interaction->getResponse ()->mapToJSON();
            if ($mapJson != null) {
                array_push ($maps, $mapJson);   
            }
		}
		
		// Get the outcome variables
		$outcomes = Array ();
		$outcomesTmp = $item->getOutcomes ();
		foreach ($outcomesTmp as $outcome){
			array_push ($outcomes, $outcome->toJSON());
		}
		
		matching_init ();
		matching_setRule ($rule);
		matching_setMaps ($maps);
		matching_setCorrects ($corrects);
		matching_setResponses ($responses);
		matching_setOutcomes ($outcomes);
		matching_evaluate ();
		
		$returValue = matching_getOutcomes ();
		echo json_encode ($returValue);
	}
}
?>
