<?php

require (dirname(__FILE__).'/../models/classes/Matching/matching_api.php');

/**
 * Items Controller provide actions performed from url resolution
 * 
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoItems
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class Matching extends Items {
	
	public function evaluate () {
		$params = json_decode($_POST['params']);
		$responses = json_decode($_POST['data']);
		
		$file = dirname(__FILE__).'/../test/samples/'.$params->interactionType.'.xml';
		
		$qtiParser = new taoItems_models_classes_QTI_Parser($file);
		$qtiParser->validate();
		if (!$qtiParser->isValid()) echo 'file not valid';
		$item = $qtiParser->load();
		
		$rule = $item->getResponseProcessing ()->getRule();
		
		$corrects = Array ();
		$maps = Array ();
		$interactions = $item->getInteractions();
		foreach ($interactions as $interaction){
			array_push ($corrects, $interaction->getResponse ()->correctToJSON());
			array_push ($maps, $interaction->getResponse ()->mapToJSON());
		}
		
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
