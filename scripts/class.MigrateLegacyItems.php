<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/scripts/class.MigrateLegacyItems.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 18.02.2011, 16:45:07 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoItems
 * @subpackage scripts
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include tao_scripts_Runner
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('tao/scripts/class.Runner.php');

/* user defined includes */
// section 127-0-1-1--39e3a8dd:12e33ba6c22:-8000:0000000000002D6C-includes begin
// section 127-0-1-1--39e3a8dd:12e33ba6c22:-8000:0000000000002D6C-includes end

/* user defined constants */
// section 127-0-1-1--39e3a8dd:12e33ba6c22:-8000:0000000000002D6C-constants begin
// section 127-0-1-1--39e3a8dd:12e33ba6c22:-8000:0000000000002D6C-constants end

/**
 * Short description of class taoItems_scripts_MigrateLegacyItems
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoItems
 * @subpackage scripts
 */
class taoItems_scripts_MigrateLegacyItems
    extends tao_scripts_Runner
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute item
     *
     * @access private
     * @var Resource
     */
    private $item = null;

    // --- OPERATIONS ---

    /**
     * Short description of method preRun
     *
     * @access protected
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return mixed
     */
    protected function preRun()
    {
        // section 127-0-1-1--5d5119d4:12e3924f2ec:-8000:0000000000002D81 begin
        
    if(isset($this->inputFormat['uri'])){
    		$this->item = $this->getResource($this->parameters['uri']);
    	}
    	if(is_null($this->item) && isset($this->parameters['addResource']) && $this->parameters['addResource'] === true){
    		$clazz = null;
    		if(isset($this->parameters['class'])){
    			$clazz = new core_kernel_classes_Class($this->parameters['class']);
    		}
    		$this->item = $this->createResource($clazz);
    	}
    	if ((isset($this->inputFormat['uri'])  || isset($this->parameters['addResource'])) && is_null($this->item )){
    		self::err("Unable to create/retrieve item");
    	}
    	
        // section 127-0-1-1--5d5119d4:12e3924f2ec:-8000:0000000000002D81 end
    }

    /**
     * Short description of method run
     *
     * @access protected
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return mixed
     */
    protected function run()
    {
        // section 127-0-1-1--39e3a8dd:12e33ba6c22:-8000:0000000000002D6E begin
        
    	$result = $this->qcm2Qti($this->parameters['input']);
    	if(isset($this->parameters['output'])){
    		file_put_contents($this->parameters['output'], $result);
    	}
    	
        // section 127-0-1-1--39e3a8dd:12e33ba6c22:-8000:0000000000002D6E end
    }

    /**
     * Short description of method createResource
     *
     * @access private
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Class clazz
     * @return core_kernel_classes_Resource
     */
    private function createResource( core_kernel_classes_Class $clazz = null)
    {
        $returnValue = null;

        // section 127-0-1-1--5d5119d4:12e3924f2ec:-8000:0000000000002D76 begin
        
        if(is_null($clazz)){
        	$clazz = new core_kernel_classes_Class(TAO_ITEM_CLASS);
        }
        $itemService = tao_models_classes_ServiceFactory::get("items");
        if($itemService->isItemClass($clazz)){
        	$returnValue = $clazz->createInstance();
        }
        // section 127-0-1-1--5d5119d4:12e3924f2ec:-8000:0000000000002D76 end

        return $returnValue;
    }

    /**
     * Short description of method getResource
     *
     * @access private
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string uri
     * @return core_kernel_classes_Resource
     */
    private function getResource($uri)
    {
        $returnValue = null;

        // section 127-0-1-1--5d5119d4:12e3924f2ec:-8000:0000000000002D7E begin
        
        $itemService = tao_models_classes_ServiceFactory::get("items");
     
        $returnValue = new core_kernel_classes_Resource($uri);
        
        $isItem = false;
        $types = $returnValue->getPropertyValuesCollection(new core_kernel_classes_Property(RDF_TYPE));
        foreach($types->getIterator() as $type){
        	if($itemService->isItemClass($type)){
        			 $isItem = true;
        			 break;
        	}
        }
        if(!$isItem){
        	$returnValue = null;
        }
        
        // section 127-0-1-1--5d5119d4:12e3924f2ec:-8000:0000000000002D7E end

        return $returnValue;
    }

    /**
     * Short description of method qcm2Qti
     *
     * @access private
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string inputFile
     * @return string
     */
    private function qcm2Qti($inputFile)
    {
        $returnValue = (string) '';

        // section 127-0-1-1--5d5119d4:12e3924f2ec:-8000:0000000000002D78 begin
        
	    try{
		    $dom = new DOMDocument();
		    $dom->load($inputFile);
	    	$xpath = new DOMXPath($dom);
		    	
		    //create default structure
		    $itemIdentifier = null;
		    if(!is_null($this->item)){
		    	$itemIdentifier = tao_helpers_Uri::getUniqueId($this->item->uriResource);
		    }
		    
		    $label = '';
		    foreach($xpath->query("rdfs:LABEL") as $labelNode){
		    	$label = $labelNode->nodeValue;
		    	break;
		    }
		    
		    $qtiItem = new taoItems_models_classes_QTI_Item($itemIdentifier, array('title' => $label));
			
			//add default responseProcessing:
			$qtiItem->setOutcomes(array(
				new taoItems_models_classes_QTI_Outcome('SCORE', array('baseType' => 'integer', 'cardinality' => 'single'))
			));
			$qtiItem->setResponseProcessing(
				new taoItems_models_classes_QTI_response_TemplatesDriven()
			);
		
	    	
	    	$inqueries = array();
	    	foreach($xpath->query("//tao:INQUIRY") as $inquery){
	    		$added = false;
	    		if($inquery->hasAttribute('order')){
	    			$order = (int)$inquery->getAttribute('order');
	    			if(!array_key_exists($order, $inqueries)){
	    				$inqueries[(int)$inquery->getAttribute('order')] = $inquery;
	    				$added = true;
	    			}
	    		}
	    		if(!$added){
	    			foreach(array_keys($inqueries) as $index){
	    				if(!isset($inqueries[$index + 1])){
	    					$inqueries[$index + 1] = $inquery; 
	    					break;
	    				}
	    			}
	    		}
	    	}
	    	ksort($inqueries);
	    	
	    	$interactions = array();
	    	foreach($inqueries as $inquery){
	    		
				$interaction  = new taoItems_models_classes_QTI_Interaction('choice');
	    		foreach($xpath->query("tao:QUESTION", $inquery) as $question){
	    			$interaction->setPrompt($question->nodeValue);
	    		}
	    		foreach($xpath->query("tao:PROPOSITIONTYPE") as $propositionType){
	    			if(preg_match("/^Exclusive/i", trim($propositionType->nodeValue))){
	    				$interaction->setOption("maxChoice", 1);
	    			}
	    			if(preg_match("/^Multiple/i", trim($propositionType->nodeValue))){
	    				$interaction->setOption("maxChoice", 0);
	    			}
	    		}
	    		
	    		
	    		$choices = array();
	    		foreach($xpath->query(".//tao:PROPOSITION", $inquery) as $proposition){
	    			$identifier = null;
	    			if($proposition->hasAttribute('Id')){
	    				$identifier = $proposition->getAttribute('Id');
	    			}
	    			
	    			$choice = new taoItems_models_classes_QTI_Choice($identifier);
		    		$choice->setType('simpleChoice');
		    		 
		    		$choice->setData($proposition->nodeValue);
		    		
	    			$added = false;
		    		if($proposition->getAttribute('order')){
		    			$order = (int)$proposition->getAttribute('order');
		    			if(!array_key_exists($order, $choices)){
		    				$choices[(int)$proposition->getAttribute('order')] = $choice;
		    				$added = true;
		    			}
		    		}
		    		if(!$added){
		    			foreach(array_keys($choices) as $index){
		    				if(!isset($choices[$index + 1])){
		    					$choices[$index + 1] = $choice; 
		    					break;
		    				}
		    			}
		    		}
	    		}
	    		ksort($choices);
	    		$interaction->setChoices($choices);
	    		
	    		$data = '';
	    		foreach($choices as $choice){
	    			$data .= '{'.$choice->getSerial().'}';	
	    		}
	    		$interaction->setData($data);
	    		$interactions[] = $interaction;
	    	}
	    	
	    	
	    	$data = '';
	    	foreach($xpath->query("tao:ITEMPRESENTATION") as $presentationNode){
	    		foreach($xpath->query("./xul/box[@id='itemContainer_box']", $presentationNode) as $containerNode){
	    			foreach($containerNode->childNodes as $child){
	    				
	    				switch(strtolower($child->nodeName)){
	    					case 'image':
	    						if($child->hasAttribute('src')){
	    							$data .= "<img src='".$child->getAttribute('src')."'";
									if($child->hasAttribute('width')){
										$data .= " width='".$child->getAttribute('width')."'";
									}  
	    							if($child->hasAttribute('height')){
										$data .= " height='".$child->getAttribute('height')."'";
									}  							
	    							$data .= " />";
	    						}
	    						break;
	    					case 'label':
	    						if($child->hasAttribute('value') && $child->hasAttribute('id') && strtolower($child->getAttribute('id')) == 'problem_textbox'){
	    							$multi = false;
	    							if($child->hasAttribute('multiline') && strtolower($child->getAttribute('multiline')) == 'true'){
	    								$multi = true;	
	    							}
	    							
	    							if($multi){
	    								$data .= "<div>";
	    							}
	    							else{
	    								$data .= "<span>";
	    							}
	    							
	    							$data .= html_entity_decode($child->getAttribute('value'));
									 							
	    							if($multi){
	    								$data .= "</div>";
	    							}
	    							else{
	    								$data .= "</span>";
	    							}
	    						}
	    						break;
	    					case 'box':
	    						if($child->hasAttribute('id') && strtolower($child->getAttribute('id')) == 'inquirycontainer_box'){
		    						foreach($interactions as $interaction){
							    		$data .= '{'.$interaction->getSerial().'}';	
							    	}
	    						}
	    						break;
	    				}
	    			}
	    		}
	    	}
	    	
	   	 	
	    	$qtiItem->setInteractions($interactions);
	    	$qtiItem->setData($data);
	    		
	    	
			(count($qtiItem->getInteractions())<=1) ? $responseId = 'RESPONSE' : $responseId = null;
			
			foreach($qtiItem->getInteractions() as $interaction){
				$response = new taoItems_models_classes_QTI_Response($responseId);
				$response->setHowMatch(QTI_RESPONSE_TEMPLATE_MATCH_CORRECT);
				$interaction->setResponse($response);
	    	}
	    	
	    	$returnValue = $qtiItem->toQTI();
    	}
    	catch(DomainException $de){
			self::err($de, true);    		
    	}
        
        // section 127-0-1-1--5d5119d4:12e3924f2ec:-8000:0000000000002D78 end

        return (string) $returnValue;
    }

} /* end of class taoItems_scripts_MigrateLegacyItems */

?>