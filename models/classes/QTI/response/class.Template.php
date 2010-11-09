<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems\models\classes\QTI\response\class.Template.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 09.11.2010, 09:54:37 with ArgoUML PHP module 
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoItems
 * @subpackage models_classes_QTI_response
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
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 */
require_once('taoItems/models/classes/QTI/class.Data.php');

/**
 * include taoItems_models_classes_QTI_response_ResponseProcessing
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 */
require_once('taoItems/models/classes/QTI/response/interface.ResponseProcessing.php');

/* user defined includes */
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:00000000000023A1-includes begin
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:00000000000023A1-includes end

/* user defined constants */
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:00000000000023A1-constants begin
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:00000000000023A1-constants end

/**
 * Short description of class taoItems_models_classes_QTI_response_Template
 *
 * @access public
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoItems
 * @subpackage models_classes_QTI_response
 */
class taoItems_models_classes_QTI_response_Template
    extends taoItems_models_classes_QTI_Data
        implements taoItems_models_classes_QTI_response_ResponseProcessing
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute MATCH_CORRECT
     *
     * @access public
     * @var string
     */
    const MATCH_CORRECT = 'http://www.imsglobal.org/question/qti_v2p0/rptemplates/match_correct';

    /**
     * Short description of attribute MAP_RESPONSE
     *
     * @access public
     * @var string
     */
    const MAP_RESPONSE = 'http://www.imsglobal.org/question/qti_v2p0/rptemplates/map_response';

    /**
     * Short description of attribute MAP_RESPONSE_POINT
     *
     * @access public
     * @var string
     */
    const MAP_RESPONSE_POINT = 'http://www.imsglobal.org/question/qti_v2p0/rptemplates/map_response_point';

    /**
     * Short description of attribute uri
     *
     * @access protected
     * @var string
     */
    protected $uri = '';

    /**
     * Short description of attribute file
     *
     * @access protected
     * @var string
     */
    protected $file = '';

    // --- OPERATIONS ---

    /**
     * Short description of method process
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  Response response
     * @param  Outcome score
     * @return boolean
     */
    public function process( taoItems_models_classes_QTI_Response $response,  taoItems_models_classes_QTI_Outcome $score = null)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-5ae00f6b:12a36da0066:-8000:0000000000002422 begin
        // section 127-0-1-1-5ae00f6b:12a36da0066:-8000:0000000000002422 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getRule
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @return string
     */
    public function getRule()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-29d6c9d3:12bcdc75857:-8000:0000000000002A1B begin
        
        if( $this->uri == self::MATCH_CORRECT )
			$returnValue = taoItems_models_classes_Matching_Matching::MATCH_CORRECT;
    	
		else if ( $this->uri == self::MAP_RESPONSE )
			$returnValue = taoItems_models_classes_Matching_Matching::MAP_RESPONSE;
        
        // section 127-0-1-1-29d6c9d3:12bcdc75857:-8000:0000000000002A1B end

        return (string) $returnValue;
    }

    /**
     * Short description of method __construct
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param  string uri
     * @return mixed
     */
    public function __construct($uri)
    {
        // section 127-0-1-1-5ae00f6b:12a36da0066:-8000:0000000000002426 begin
        
    	if( $uri != self::MATCH_CORRECT && 
    		$uri != self::MAP_RESPONSE && 
    		$uri != self::MAP_RESPONSE_POINT ){
    		throw new Exception("Unknown response processing template $uri");
    	}
    	$this->uri = $uri;
    	
    	$this->file = BASE_PATH . '/models/classes/QTI/data/rptemplates/' . basename($this->uri). '.xml';
    	if(!file_exists($this->file)){
    		throw new Exception("Unable to load response processing template {$this->uri} in {$this->file}");
    	}
    	
    	parent::__construct(null);
    	
        // section 127-0-1-1-5ae00f6b:12a36da0066:-8000:0000000000002426 end
    }

    /**
     * Short description of method toQTI
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @return string
     */
    public function toQTI()
    {
        $returnValue = (string) '';

        // section 127-0-1-1--5fc6d28e:12aec61bbe9:-8000:00000000000025A8 begin
        
        $tplRenderer = new taoItems_models_classes_QTI_TemplateRenderer(
        		self::getTemplatePath() . '/qti.rptemplate.tpl.php', 
        		array('uri' => $this->uri)
        	);
        $returnValue = $tplRenderer->render();
        
        // section 127-0-1-1--5fc6d28e:12aec61bbe9:-8000:00000000000025A8 end

        return (string) $returnValue;
    }

    /**
     * Short description of method getUri
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @return string
     */
    public function getUri()
    {
        $returnValue = (string) '';

        // section 10-13-1-39-a5fad37:12c2fc3729c:-8000:00000000000049CC begin
		$returnValue = $this->uri;
        // section 10-13-1-39-a5fad37:12c2fc3729c:-8000:00000000000049CC end

        return (string) $returnValue;
    }

} /* end of class taoItems_models_classes_QTI_response_Template */

?>