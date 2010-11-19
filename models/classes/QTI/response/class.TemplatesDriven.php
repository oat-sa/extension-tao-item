<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/models/classes/QTI/response/class.TemplatesDriven.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 19.11.2010, 15:35:22 with ArgoUML PHP module 
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI_response
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include taoItems_models_classes_QTI_response_ResponseProcessing
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 */
require_once('taoItems/models/classes/QTI/response/class.ResponseProcessing.php');

/**
 * include taoItems_models_classes_QTI_response_Rule
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 */
require_once('taoItems/models/classes/QTI/response/interface.Rule.php');

/* user defined includes */
// section 127-0-1-1-703c736:12c63695364:-8000:0000000000002BF2-includes begin
// section 127-0-1-1-703c736:12c63695364:-8000:0000000000002BF2-includes end

/* user defined constants */
// section 127-0-1-1-703c736:12c63695364:-8000:0000000000002BF2-constants begin
// section 127-0-1-1-703c736:12c63695364:-8000:0000000000002BF2-constants end

/**
 * Short description of class
 *
 * @access public
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI_response
 */
class taoItems_models_classes_QTI_response_TemplatesDriven
    extends taoItems_models_classes_QTI_response_ResponseProcessing
        implements taoItems_models_classes_QTI_response_Rule
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method getRule
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return string
     */
    public function getRule()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-3397f61e:12c15e8566c:-8000:0000000000002AFF begin
        // section 127-0-1-1-3397f61e:12c15e8566c:-8000:0000000000002AFF end

        return (string) $returnValue;
    }

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return mixed
     */
    public function __construct()
    {
        // section 127-0-1-1-703c736:12c63695364:-8000:0000000000002BF9 begin
        // section 127-0-1-1-703c736:12c63695364:-8000:0000000000002BF9 end
    }

    /**
     * Short description of method buildQTI
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string uri
     * @param  array options
     * @return string
     */
    public function buildQTI($uri, $options)
    {
        $returnValue = (string) '';

        // section 127-0-1-1-703c736:12c63695364:-8000:0000000000002BF7 begin
        
        if (!isset ($options) || !is_array($options)){
            $options = array();
        }
        
        $templateName = substr($uri, strrpos($uri, '/')+1);
        $matchingTemplate  = dirname(__FILE__).'/rpTemplate/qti.'.$templateName.'.tpl.php';

        $tplRenderer = new taoItems_models_classes_QTI_TemplateRenderer($matchingTemplate, $options);    
        $returnValue = $tplRenderer->render();
        
        // section 127-0-1-1-703c736:12c63695364:-8000:0000000000002BF7 end

        return (string) $returnValue;
    }

    /**
     * Short description of method isSupportedTemplate
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string uri
     * @return taoItems_models_classes_Matching_bool
     */
    public static function isSupportedTemplate($uri)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-703c736:12c63695364:-8000:0000000000002BFD begin
        
        $mythoMap = Array (
            'http://www.imsglobal.org/question/qti_v2p0/rptemplates/match_correct'
            , 'http://www.imsglobal.org/question/qti_v2p0/rptemplates/map_response'
        );
        
        $returnValue = in_array($uri, $mythoMap);
        
        // section 127-0-1-1-703c736:12c63695364:-8000:0000000000002BFD end

        return (bool) $returnValue;
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

        // section 127-0-1-1-703c736:12c63695364:-8000:0000000000002C06 begin
        
        /*foreach ($responsesToTpl as $responseIdentifier=>$templateUri){
            $returnValue .= $this->buildQTI (
                $templateUri
                , Array('outcomeIdentifier'=>'SCORE', 'responseIdentifier'=>$responseIdentifier)
            );
        }*/
        
        // section 127-0-1-1-703c736:12c63695364:-8000:0000000000002C06 end

        return (string) $returnValue;
    }

} /* end of class taoItems_models_classes_QTI_response_TemplatesDriven */

?>