<?php

error_reporting(E_ALL);

/**
 * TAO -
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 24.01.2012, 17:16:26 with ArgoUML PHP module 
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
 * include taoItems_models_classes_QTI_response_Composite
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('taoItems/models/classes/QTI/response/class.Composite.php');

/**
 * include taoItems_models_classes_QTI_response_Rule
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('taoItems/models/classes/QTI/response/interface.Rule.php');

/* user defined includes */
// section 127-0-1-1-786830e4:134f066fb13:-8000:0000000000003597-includes begin
// section 127-0-1-1-786830e4:134f066fb13:-8000:0000000000003597-includes end

/* user defined constants */
// section 127-0-1-1-786830e4:134f066fb13:-8000:0000000000003597-constants begin
// section 127-0-1-1-786830e4:134f066fb13:-8000:0000000000003597-constants end

/**
 * Short description of class
 *
 * @abstract
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI_response_interactionResponseProcessing
 */
abstract class taoItems_models_classes_QTI_response_interactionResponseProcessing_InteractionResponseProcessing
        implements taoItems_models_classes_QTI_response_Rule
{
    // --- ASSOCIATIONS ---
    // generateAssociationEnd : 

    // --- ATTRIBUTES ---

    /**
     * Short description of attribute SCORE_PREFIX
     *
     * @access private
     * @var string
     */
    const SCORE_PREFIX = 'SCORE_';

    /**
     * Short description of attribute responseIdentifier
     *
     * @access private
     * @var string
     */
    private $responseIdentifier = '';

    /**
     * Short description of attribute scoreIdentifier
     *
     * @access private
     * @var string
     */
    private $scoreIdentifier = '';

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

        // section 127-0-1-1-3397f61e:12c15e8566c:-8000:0000000000002AFF begin
        throw new common_Exception('Missing getRule implementation for '.get_class($this), array('TAOITEMS', 'QTI', 'HARD'));
        // section 127-0-1-1-3397f61e:12c15e8566c:-8000:0000000000002AFF end

        return (string) $returnValue;
    }

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string responseIdentifier
     * @param  string scoreIdentifier
     * @return mixed
     */
    public function __construct($responseIdentifier, $scoreIdentifier = null)
    {
        // section 127-0-1-1-4c0a0972:134fa47975d:-8000:00000000000035E5 begin
        $this->responseIdentifier = $responseIdentifier;
        $this->scoreIdentifier = is_null($scoreIdentifier) ? self::SCORE_PREFIX.$responseIdentifier : $scoreIdentifier;
        // section 127-0-1-1-4c0a0972:134fa47975d:-8000:00000000000035E5 end
    }

    /**
     * Short description of method getResponseIdentifier
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function getResponseIdentifier()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-4c0a0972:134fa47975d:-8000:00000000000035FE begin
        return $this->responseIdentifier;
        // section 127-0-1-1-4c0a0972:134fa47975d:-8000:00000000000035FE end

        return (string) $returnValue;
    }

    /**
     * Short description of method getOutcomeIdentifier
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function getOutcomeIdentifier()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-4c0a0972:134fa47975d:-8000:00000000000035FC begin
        return $this->scoreIdentifier;
        // section 127-0-1-1-4c0a0972:134fa47975d:-8000:00000000000035FC end

        return (string) $returnValue;
    }

    /**
     * Short description of method generateOutcomeDefinition
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return taoItems_models_classes_QTI_Outcome
     */
    public function generateOutcomeDefinition()
    {
        $returnValue = null;

        // section 127-0-1-1-4c0a0972:134fa47975d:-8000:0000000000003623 begin
        $returnValue = new taoItems_models_classes_QTI_Outcome($this->getOutcomeIdentifier(), array('baseType' => 'integer', 'cardinality' => 'single'));
        // section 127-0-1-1-4c0a0972:134fa47975d:-8000:0000000000003623 end

        return $returnValue;
    }

    /**
     * Short description of method toQTI
     *
     * @abstract
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public abstract function toQTI();

} /* end of abstract class taoItems_models_classes_QTI_response_interactionResponseProcessing_InteractionResponseProcessing */

?>