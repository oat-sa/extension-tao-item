<?php

error_reporting(E_ALL);

/**
 * TAO -
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 24.01.2012, 17:13:08 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI_response_interactionResponseProcessing
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('taoItems/models/classes/QTI/response/interactionResponseProcessing/class.Template.php');

/* user defined includes */
// section 127-0-1-1-786830e4:134f066fb13:-8000:0000000000009008-includes begin
// section 127-0-1-1-786830e4:134f066fb13:-8000:0000000000009008-includes end

/* user defined constants */
// section 127-0-1-1-786830e4:134f066fb13:-8000:0000000000009008-constants begin
// section 127-0-1-1-786830e4:134f066fb13:-8000:0000000000009008-constants end

/**
 * Short description of class
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI_response_interactionResponseProcessing
 */
class taoItems_models_classes_QTI_response_interactionResponseProcessing_MatchCorrectTemplate
    extends taoItems_models_classes_QTI_response_interactionResponseProcessing_Template
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute CLASS_ID
     *
     * @access public
     * @var string
     */
    const CLASS_ID = 'correct';

    // --- OPERATIONS ---

    /**
     * Short description of method getRule
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function getRule()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-786830e4:134f066fb13:-8000:0000000000009011 begin
        $returnValue = 'if(match(null, '.
        	'getResponse("'.$this->getResponseIdentifier().'"), '.
        	'getCorrect("'.$this->getResponseIdentifier().'"))) '.
        	'setOutcomeValue("'.$this->getOutcomeIdentifier().'", 1); '.
        	'else setOutcomeValue("'.$this->getOutcomeIdentifier().'", 0);';
        // section 127-0-1-1-786830e4:134f066fb13:-8000:0000000000009011 end

        return (string) $returnValue;
    }

    /**
     * Short description of method toQTI
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function toQTI()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-4c0a0972:134fa47975d:-8000:0000000000003628 begin
        $returnValue = '<responseCondition>
		    <responseIf>
		        <match>
		            <variable identifier="'.$this->getResponseIdentifier().'" />
		            <correct identifier="'.$this->getResponseIdentifier().'" />
		        </match>
		        <setOutcomeValue identifier="'.$this->getOutcomeIdentifier().'">
	                <baseValue baseType="integer">1</baseValue>
		        </setOutcomeValue>
		    </responseIf>
		</responseCondition>';
        // section 127-0-1-1-4c0a0972:134fa47975d:-8000:0000000000003628 end

        return (string) $returnValue;
    }

} /* end of class taoItems_models_classes_QTI_response_interactionResponseProcessing_MatchCorrectTemplate */

?>