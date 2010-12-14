<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/models/classes/QTI/class.Item.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 18.11.2010, 12:14:55 with ArgoUML PHP module 
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * The QTI_Data class represent the abstract model for all the QTI objects.
 * It contains all the attributes of the different kind of QTI objects.
 * It manages the identifiers and serial creation.
 * It provides the serialisation and persistance methods.
 * And give the interface for the rendering.
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 */
require_once('taoItems/models/classes/QTI/class.Data.php');

/**
 * include taoItems_models_classes_QTI_Interaction
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 */
require_once('taoItems/models/classes/QTI/class.Interaction.php');

/**
 * include taoItems_models_classes_QTI_Outcome
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 */
require_once('taoItems/models/classes/QTI/class.Outcome.php');

/* user defined includes */
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:000000000000233F-includes begin
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:000000000000233F-includes end

/* user defined constants */
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:000000000000233F-constants begin
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:000000000000233F-constants end

/**
 * Short description of class taoItems_models_classes_QTI_Item
 *
 * @access public
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI
 */
class taoItems_models_classes_QTI_Item
    extends taoItems_models_classes_QTI_Data
{
    // --- ASSOCIATIONS ---
    // generateAssociationEnd :     // generateAssociationEnd : 

    // --- ATTRIBUTES ---

    /**
     * Short description of attribute interactions
     *
     * @access protected
     * @var array
     */
    protected $interactions = array();

    /**
     * Short description of attribute responseProcessing
     *
     * @access protected
     * @var ResponseProcessing
     */
    protected $responseProcessing = null;

    /**
     * Short description of attribute outcomes
     *
     * @access protected
     * @var array
     */
    protected $outcomes = array();

    /**
     * Short description of attribute stylesheets
     *
     * @access protected
     * @var array
     */
    protected $stylesheets = array();

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string identifier
     * @param  array options
     * @return mixed
     */
    public function __construct($identifier = null, $options = array())
    {
        // section 127-0-1-1-2993bc96:12baebd89c3:-8000:000000000000264D begin
        
    	//override the tool options !
    	$options['toolName'] 	= TAO_NAME;
    	$options['toolVersion'] = TAO_VERSION;
    	
    	parent::__construct($identifier, $options);
    	
        // section 127-0-1-1-2993bc96:12baebd89c3:-8000:000000000000264D end
    }

    /**
     * Short description of method __sleep
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return array
     */
    public function __sleep()
    {
        $returnValue = array();

        // section 127-0-1-1--272f4da0:12a899718bf:-8000:00000000000024D9 begin
        
        $this->interactions = array_keys($this->interactions);
        
        $returnValue = parent::__sleep();
        
        // section 127-0-1-1--272f4da0:12a899718bf:-8000:00000000000024D9 end

        return (array) $returnValue;
    }

    /**
     * Short description of method __wakeup
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return mixed
     */
    public function __wakeup()
    {
        // section 127-0-1-1--272f4da0:12a899718bf:-8000:00000000000024DB begin
        
    	$interactionSerials = $this->interactions; 
    	$this->interactions = array();
    	foreach($interactionSerials as $serial){
    		if(Session::hasAttribute(self::PREFIX .$serial)){
    			$this->interactions[$serial] = unserialize(Session::getAttribute(self::PREFIX .$serial));
    		}
    	}
    	
    	parent::__wakeup();
    	
        // section 127-0-1-1--272f4da0:12a899718bf:-8000:00000000000024DB end
    }

    /**
     * Short description of method getInteractions
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return array
     */
    public function getInteractions()
    {
        $returnValue = array();

        // section 127-0-1-1--4be859a6:12a33452171:-8000:00000000000023D6 begin
        
        $returnValue = $this->interactions;
        
        // section 127-0-1-1--4be859a6:12a33452171:-8000:00000000000023D6 end

        return (array) $returnValue;
    }

    /**
     * Short description of method setInteractions
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  array interactions
     * @return mixed
     */
    public function setInteractions($interactions)
    {
        // section 127-0-1-1--4be859a6:12a33452171:-8000:00000000000023D8 begin
        
    	$this->interactions = array();
    	foreach($interactions as $interaction){
    		$this->addInteraction($interaction);
    	}
    	
        // section 127-0-1-1--4be859a6:12a33452171:-8000:00000000000023D8 end
    }

    /**
     * Short description of method getInteraction
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string serial
     * @return taoItems_models_classes_QTI_Interaction
     */
    public function getInteraction($serial)
    {
        $returnValue = null;

        // section 127-0-1-1--4be859a6:12a33452171:-8000:00000000000023DB begin
        
        if(!empty($serial)){
	        if(array_key_exists($serial, $this->interactions)){
	        	$returnValue = $this->interactions[$serial];
	        }
        }
        
        // section 127-0-1-1--4be859a6:12a33452171:-8000:00000000000023DB end

        return $returnValue;
    }

    /**
     * Short description of method addInteraction
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Interaction interaction
     * @return mixed
     */
    public function addInteraction( taoItems_models_classes_QTI_Interaction $interaction)
    {
        // section 127-0-1-1--4be859a6:12a33452171:-8000:00000000000023DF begin
        
    	if(!is_null($interaction)){
    		$this->interactions[$interaction->getSerial()] = $interaction;
    	}
    	
        // section 127-0-1-1--4be859a6:12a33452171:-8000:00000000000023DF end
    }

    /**
     * Short description of method removeInteraction
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Interaction interaction
     * @return boolean
     */
    public function removeInteraction( taoItems_models_classes_QTI_Interaction $interaction)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--398d1ef5:12acc40a46b:-8000:0000000000002538 begin
        
    	if(!is_null($interaction)){
    		if(isset($this->interactions[$interaction->getSerial()])){
				//delete interaction response if set:
				$response = $interaction->getResponse();
				if(!is_null($response)){
					$response->_remove();
				}
				
				//delete choices:
				foreach($interaction->getChoices() as $choice){
					$choice->_remove();
				}
				
				//delete groups:
				foreach($interaction->getGroups() as $group){
					$group->_remove();
				}
				
				//finally, delete the interaction:
    			$interaction->_remove();
    			unset($this->interactions[$interaction->getSerial()]);
    			$returnValue = true;
    		}
    	}
        
        // section 127-0-1-1--398d1ef5:12acc40a46b:-8000:0000000000002538 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getResponseProcessing
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return taoItems_models_classes_QTI_response_ResponseProcessing
     */
    public function getResponseProcessing()
    {
        $returnValue = null;

        // section 127-0-1-1--398d1ef5:12acc40a46b:-8000:000000000000253B begin
        
        $returnValue = $this->responseProcessing;
        
        // section 127-0-1-1--398d1ef5:12acc40a46b:-8000:000000000000253B end

        return $returnValue;
    }

    /**
     * Short description of method setResponseProcessing
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  rprocessing
     * @return mixed
     */
    public function setResponseProcessing($rprocessing)
    {
        // section 127-0-1-1--398d1ef5:12acc40a46b:-8000:000000000000253E begin
        
    	$this->responseProcessing = $rprocessing;    	
        
        // section 127-0-1-1--398d1ef5:12acc40a46b:-8000:000000000000253E end
    }

    /**
     * Short description of method setOutcomes
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  array outcomes
     * @return mixed
     */
    public function setOutcomes($outcomes)
    {
        // section 127-0-1-1--a2bd9f7:12ae6efc8e9:-8000:00000000000025A4 begin
        
    	foreach($outcomes as $outcome){
    		if( ! $outcome instanceof taoItems_models_classes_QTI_Outcome){
    			throw new InvalidArgumentException("wrong entry in outcomes list");
    		}
    		$this->outcomes[$outcome->getSerial()] = $outcome;
    	}
    	
        // section 127-0-1-1--a2bd9f7:12ae6efc8e9:-8000:00000000000025A4 end
    }

    /**
     * Short description of method getOutcomes
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return array
     */
    public function getOutcomes()
    {
        $returnValue = array();

        // section 127-0-1-1--a2bd9f7:12ae6efc8e9:-8000:00000000000025A7 begin
        
        $returnValue = $this->outcomes;
        
        // section 127-0-1-1--a2bd9f7:12ae6efc8e9:-8000:00000000000025A7 end

        return (array) $returnValue;
    }

    /**
     * Short description of method getOutcome
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string serial
     * @return taoItems_models_classes_QTI_Outcome
     */
    public function getOutcome($serial)
    {
        $returnValue = null;

        // section 127-0-1-1--a2bd9f7:12ae6efc8e9:-8000:00000000000025A9 begin
        
     	if(!empty($serial)){
	        if(array_key_exists($serial, $this->outcomes)){
	        	$returnValue = $this->outcomes[$serial];
	        }
        }
        
        // section 127-0-1-1--a2bd9f7:12ae6efc8e9:-8000:00000000000025A9 end

        return $returnValue;
    }

    /**
     * Short description of method removeOutcome
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Outcome outcome
     * @return boolean
     */
    public function removeOutcome( taoItems_models_classes_QTI_Outcome $outcome)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--a2bd9f7:12ae6efc8e9:-8000:00000000000025AC begin
        
	    if(!is_null($outcome)){
    		if(isset($this->outcomes[$outcome->getSerial()])){
    			$outcome->_remove();
    			unset($this->outcomes[$outcome->getSerial()]);
    			$returnValue = true;
    		}
    	}
    	
        // section 127-0-1-1--a2bd9f7:12ae6efc8e9:-8000:00000000000025AC end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getStylesheets
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return array
     */
    public function getStylesheets()
    {
        $returnValue = array();

        // section 127-0-1-1-8cf5183:12bce4ebee2:-8000:000000000000271E begin
        
        $returnValue = $this->stylesheets;
        
        // section 127-0-1-1-8cf5183:12bce4ebee2:-8000:000000000000271E end

        return (array) $returnValue;
    }

    /**
     * Short description of method setStylesheets
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  array stylesheets
     * @return mixed
     */
    public function setStylesheets($stylesheets)
    {
        // section 127-0-1-1-8cf5183:12bce4ebee2:-8000:0000000000002720 begin
		
        $this->stylesheets = array();
    	if(is_array($stylesheets)){
    		foreach($stylesheets as $stylesheet){
    			if(isset($stylesheet['href']) && isset($stylesheet['title'])){
    				$this->stylesheets[] = $stylesheet;
    			}
    		}
    	}
    	
        // section 127-0-1-1-8cf5183:12bce4ebee2:-8000:0000000000002720 end
    }

    /**
     * Short description of method toXHTML
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return string
     */
    public function toXHTML()
    {
        $returnValue = (string) '';

        // section 127-0-1-1--56c234f4:12a31c89cc3:-8000:0000000000002388 begin
        
        $template  = self::getTemplatePath() . '/xhtml.item.tpl.php';
        
    	//get the variables to use in the template
    	$variables = $this->extractVariables();
        
		$variables['hasUpload'] = false;    	
        $interactions = $this->getInteractions();
        foreach($interactions as $interaction){
			//build the interactions in the data variable
			$variables['data'] = preg_replace("/{".$interaction->getSerial()."}/", $interaction->toXHTML(), $variables['data']);
        	if($interaction->getType() == 'upload'){
        		$variables['hasUpload'] = true;   
        	}
        }
                
        // get Matching data
        $matchingData = $this->getMatchingData ();
		$variables['matching'] = array();
        $variables['matching']['data'] = $matchingData;
        $variables['matching']['url'] = "/taoDelivery/ResultDelivery/evaluate";
        $variables['matching']['params'] = array ();
        
        
        $tplRenderer = new taoItems_models_classes_QTI_TemplateRenderer($template, $variables);
      	$returnValue = $tplRenderer->render();
        
        // section 127-0-1-1--56c234f4:12a31c89cc3:-8000:0000000000002388 end

        return (string) $returnValue;
    }

    /**
     * Short description of method toQTI
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return string
     */
    public function toQTI()
    {
        $returnValue = (string) '';

        // section 127-0-1-1--56c234f4:12a31c89cc3:-8000:000000000000238A begin
        
        $template  = self::getTemplatePath() . '/qti.item.tpl.php';
    	$variables 	= $this->extractVariables(); 
		
		$variables['rowOptions'] = $this->xmlizeOptions();
    	
		$variables['response'] = '';
		$foundResponses = array();
		foreach($this->getInteractions() as $interaction){
			
			//build the interactions in the data variable
			$variables['data'] = preg_replace("/{".$interaction->getSerial()."}/", $interaction->toQti(), $variables['data']);
			
			//build the response
			$response = $interaction->getResponse();
			
			if(!is_null($response)){
				if(!in_array($response->getIdentifier(), $foundResponses)){
					$variables['response'] .= $response->toQTI();
					$foundResponses[] = $response->getIdentifier();
				}
			}
		}
        
        // render the responseProcessing
        $renderedResponseProcessing = '';
        $responseProcessing = $this->getResponseProcessing();
        if(isset($responseProcessing)){
            if($responseProcessing instanceOf taoItems_models_classes_QTI_response_TemplatesDriven){
                if (count($this->getInteractions()) == 1) {
                    foreach($this->getInteractions() as $interaction){
                        $responseProcessingToRender = new taoItems_models_classes_QTI_response_Template ($interaction->getResponse()->getHowMatch());
                        $renderedResponseProcessing = $responseProcessingToRender->toQTI();
                    }
                }
                else {
                    $renderedResponseProcessing .= "<responseProcessing>";
                    foreach($this->getInteractions() as $interaction){
                        $renderedResponseProcessing .= 
                            $responseProcessing->buildQTI(
                                $interaction->getResponse()->getHowMatch()
                                , Array(
                                    'responseIdentifier'=>$interaction->getResponse()->getIdentifier()
                                    , 'outcomeIdentifier'=>'SCORE'
                                )
                            );
                    }
                    $renderedResponseProcessing .= "</responseProcessing>";
                }
            } else {
                $renderedResponseProcessing = $responseProcessing->toQTI();
            } 
        }

        $variables['renderedResponseProcessing'] = $renderedResponseProcessing;
        
        $tplRenderer = new taoItems_models_classes_QTI_TemplateRenderer($template, $variables);

		//render and clean the xml	      
        $xmlElt = simplexml_load_string($tplRenderer->render());
		$returnValue = $xmlElt->asXml();
        
        // section 127-0-1-1--56c234f4:12a31c89cc3:-8000:000000000000238A end

        return (string) $returnValue;
    }

    /**
     * Short description of method toForm
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return tao_helpers_form_Form
     */
    public function toForm()
    {
        $returnValue = null;

        // section 127-0-1-1-25600304:12a5c17a5ca:-8000:0000000000002493 begin
		
		$formContainer = new taoItems_actions_QTIform_AssessmentItem($this);
		$returnValue = $formContainer->getForm();
		
        // section 127-0-1-1-25600304:12a5c17a5ca:-8000:0000000000002493 end

        return $returnValue;
    }

    /**
     * Short description of method getMatchingData
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return array
     */
    public function getMatchingData()
    {
        $returnValue = array();

        // section 127-0-1-1-554f2bd6:12c176484b7:-8000:0000000000002B2F begin
        
            $returnValue = array (
                "rule"      =>null,
                "corrects"  =>Array(),
                "maps"      =>Array(),
                "outcomes"  =>Array()
            );
            
            // BUILD the RP rule
            if(!is_null($this->getResponseProcessing ())){
                $responseProcessing = $this->getResponseProcessing ();
                // TEMPLATE DRIVEN RESPONSE PROCESSING
                if ( $responseProcessing instanceOf taoItems_models_classes_QTI_response_TemplatesDriven){
                    foreach ($this->getInteractions() as $interaction){
                        // Build the rule for each interaction functions of the associated template
                        $returnValue["rule"] .= $responseProcessing->buildRule (
                            $interaction->getResponse()->getHowMatch () 
                            , Array(
                                'responseIdentifier'=>$interaction->getResponse()->getIdentifier()
                                , 'outcomeIdentifier'=>'SCORE'
                            )
                        );
                    }
                } 
                // CUSTOM RESPONSE PROCESSING
                else {
                    $returnValue["rule"] = $this->getResponseProcessing()->getRule($this);
                }
            }
            
            // Get the correct responses (correct variables and map variables)
            $corrects = Array ();
            $maps = Array ();
            $interactions = $this->getInteractions();
            foreach ($interactions as $interaction){
            	if( $interaction->getResponse () != null){
	                $correctJSON = $interaction->getResponse ()->correctToJSON();
	                if ($correctJSON != null) {
	                    array_push ($returnValue["corrects"], $correctJSON);   
	                }
	                
	                $mapJson = $interaction->getResponse ()->mapToJSON();
	                if ($mapJson != null) {
	                    array_push ($returnValue["maps"], $mapJson);   
	                }
            	}
            }
            
            // Get the outcome variables
            $outcomes = $this->getOutcomes ();
            foreach ($outcomes as $outcome){
                array_push ($returnValue["outcomes"], $outcome->toJSON());
            }
        
        // section 127-0-1-1-554f2bd6:12c176484b7:-8000:0000000000002B2F end

        return (array) $returnValue;
    }

} /* end of class taoItems_models_classes_QTI_Item */

?>