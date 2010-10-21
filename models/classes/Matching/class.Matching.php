<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/models/classes/Matching/class.Matching.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 21.10.2010, 10:19:44 with ArgoUML PHP module 
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_Matching
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include taoItems_models_classes_Matching_Map
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 */
require_once('taoItems/models/classes/Matching/class.Map.php');

/**
 * include taoItems_models_classes_Matching_Variable
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 */
require_once('taoItems/models/classes/Matching/class.Variable.php');

/**
 * include taoItems_models_classes_Matching_VariableFactory
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 */
require_once('taoItems/models/classes/Matching/class.VariableFactory.php');

/* user defined includes */
// section 127-0-1-1--58a488d5:12baaa39fdd:-8000:00000000000028CF-includes begin
// section 127-0-1-1--58a488d5:12baaa39fdd:-8000:00000000000028CF-includes end

/* user defined constants */
// section 127-0-1-1--58a488d5:12baaa39fdd:-8000:00000000000028CF-constants begin
// section 127-0-1-1--58a488d5:12baaa39fdd:-8000:00000000000028CF-constants end

/**
 * Short description of class taoItems_models_classes_Matching_Matching
 *
 * @access public
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_Matching
 */
class taoItems_models_classes_Matching_Matching
{
    // --- ASSOCIATIONS ---
    // generateAssociationEnd :     // generateAssociationEnd :     // generateAssociationEnd :     // generateAssociationEnd : 

    // --- ATTRIBUTES ---

    /**
     * Short description of attribute corrects
     *
     * @access protected
     * @var Variable
     */
    protected $corrects = null;

    /**
     * Short description of attribute maps
     *
     * @access protected
     * @var Map
     */
    protected $maps = null;

    /**
     * Short description of attribute outcomes
     *
     * @access protected
     * @var Variable
     */
    protected $outcomes = null;

    /**
     * Short description of attribute responses
     *
     * @access protected
     * @var Variable
     */
    protected $responses = null;

    /**
     * Short description of attribute rule
     *
     * @access protected
     * @var string
     */
    protected $rule = '';

    /**
     * Short description of attribute whiteFunctionsList
     *
     * @access public
     * @var array
     */
    public static $whiteFunctionsList = array();

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return mixed
     */
    public function __construct()
    {
        // section 127-0-1-1--5c70894a:12bb048b221:-8000:0000000000002AB1 begin
		taoItems_models_classes_Matching_Matching::$whiteFunctionsList = array (
			'and'=>array('mappedFunction'=>'andExpression')
			, 'equal'=>array()
			, 'if'=>array('prefix'=>false)
			, 'isNull'=>array()
			, 'getCorrect'=>array()
			, 'getMap'=>array()
			, 'getResponse'=>array()
			, 'mapResponse'=>array()
			, 'match'=>array()
			, 'setOutcomeValue'=>array()
		);
        // section 127-0-1-1--5c70894a:12bb048b221:-8000:0000000000002AB1 end
    }

    /**
     * Short description of method parseExpressionRule
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  array matches
     * @return string
     */
    public static function parseExpressionRule($matches)
    {
        $returnValue = (string) '';

        // section 127-0-1-1--5c70894a:12bb048b221:-8000:0000000000002AAB begin
        
        $functionName = '';
		
		$whiteListedFunction = taoItems_models_classes_Matching_Matching::$whiteFunctionsList[$matches[1]];
		
		if (isset($whiteListedFunction)){
			// Check if the function is mapped
			if (isset($whiteListedFunction['mappedFunction'])){
				$functionName = $whiteListedFunction['mappedFunction'];
			}
			else{
				$functionName = $matches[1];
			}
			// Check if the function requires a prefix
			if (!isset($whiteListedFunction['prefix']) || $whiteListedFunction['prefix']){
				$functionName = '$this->'.$functionName;
			}
		} 
		else {
			throw new Exception ('taoItems_models_classes_Matching_Matching::parseExpressionRule an error occured : the following expression is unknown '.$matches[1]);
		}
		
		$returnValue = $functionName.' (';
		
        // section 127-0-1-1--5c70894a:12bb048b221:-8000:0000000000002AAB end

        return (string) $returnValue;
    }

    /**
     * Eval the stored response processing rule
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     */
    public function evaluate()
    {
        $returnValue = null;

        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:00000000000028DA begin
        
//    	echo 'corrects vars';
//    	pr ($this->corrects);
//    	echo 'responses vars';
//    	pr ($this->responses);
//    	echo 'outcomes vars';
//    	pr ($this->outcomes);
    	
        try {
			eval ($this->getRule());
		} catch (Exception $e) {
			throw new Exception ('an error occured during the evaluation of the rule : '.$e);
		}
		
        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:00000000000028DA end

        return $returnValue;
    }

    /**
     * Set the correct variables of the item
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  array data
     * @return mixed
     */
    public function setCorrects($data)
    {
        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:00000000000028DC begin
           	
    	if (gettype($data) != 'array')
    		throw new Exception ('taoItems_models_classes_Matching_Matching::setCorrects is waiting on an array, a '.gettype($data).' is given');

		foreach ($data as $key=>$correct){
			try {
				$var = taoItems_models_classes_Matching_VariableFactory::create ($correct->value);
				
				if (isset ($this->corrects[$correct->identifier]))
					throw new Exception ('taoItems_models_classes_Matching_Matching::setCorrects a correct variable with the identifier '.$correct->identifier.' exists yet');

				$this->corrects[$correct->identifier] = $var;
			} 
			catch (Exception $e)
			{
				throw $e;
			}
		}
    	
        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:00000000000028DC end
    }

    /**
     * Short description of method getJSonOutcomes
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     */
    public function getJSonOutcomes()
    {
        $returnValue = null;

        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:0000000000002932 begin
        
        $returnValue = Array ();
        
        foreach ($this->outcomes as $key=>$outcome){
        	$returnValue[$key] = array ();
        	$returnValue[$key]["identifier"] = $key;
        	$returnValue[$key]["value"] = $outcome->toJSon();
        }
        
        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:0000000000002932 end

        return $returnValue;
    }

    /**
     * Short description of method setMaps
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  data
     * @return mixed
     */
    public function setMaps(   $data)
    {
        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:0000000000002918 begin
        
        if (gettype($data) != 'array'){
    		throw new Exception ('taoItems_models_classes_Matching_Matching::setMaps is waiting on an array, a '.gettype($data).' is given. '.$data);
        }

		foreach ($data as $key=>$map){
			try {
				$var =  new taoItems_models_classes_Matching_Map ($map->value);
				
				if (isset ($this->maps[$map->identifier]))
					throw new Exception ('taoItems_models_classes_Matching_Matching::setMaps a map variable with the identifier '.$map->identifier.' exists yet');

				$this->maps[$map->identifier] = $var;
			}
			
			catch (Exception $e){
				throw $e;
			}
		}
    	
        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:0000000000002918 end
    }

    /**
     * Short description of method setOutcomes
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  array data
     * @return mixed
     */
    public function setOutcomes($data)
    {
        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:00000000000028DF begin
        
        if (gettype($data) != 'array'){
    		throw new Exception ('taoItems_models_classes_Matching_Matching::setOutcomes is waiting on an array, a '.gettype($data).' is given. '.$data);
        }

		foreach ($data as $key=>$outcome){
			try {
				$outcomeDefaultValue = isset($outcome->value) ? $outcome->value : null;
				$outcomeDefinedType = isset($outcome->type) ? $outcome->type : null;
				$var = taoItems_models_classes_Matching_VariableFactory::create ($outcomeDefaultValue, $outcomeDefinedType);
				
				if (isset ($this->responses[$outcome->identifier]))
					throw new Exception ('taoItems_models_classes_Matching_Matching::setReponses a correct variable with the identifier '.$outcome->identifier.' exists yet');

				$this->outcomes[$outcome->identifier] = $var;
			}
			
			catch (Exception $e){
				throw $e;
			}
		}    	
    	
        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:00000000000028DF end
    }

    /**
     * Short description of method setResponses
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  array data
     * @return mixed
     */
    public function setResponses($data)
    {
        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:00000000000028E2 begin
            	
        if (gettype($data) != 'array'){
    		throw new Exception ('taoItems_models_classes_Matching_Matching::setReponses is waiting on an array, a '.gettype($data).' is given. '.$data);
        }

		foreach ($data as $key=>$response){
			try {
				$var = taoItems_models_classes_Matching_VariableFactory::create ($response->value);
				
				if (isset ($this->responses[$response->identifier]))
					throw new Exception ('taoItems_models_classes_Matching_Matching::setReponses a correct variable with the identifier '.$response->identifier.' exists yet');

				$this->responses[$response->identifier] = $var;
			}
			
			catch (Exception $e){
				throw $e;
			}
		}
		
        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:00000000000028E2 end
    }

    /**
     * Short description of method setRule
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string rule
     * @return mixed
     */
    public function setRule($rule)
    {
        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:00000000000028E5 begin
        //implode ('|', array_keys(taoItems_models_classes_Matching_Matching::$whiteFunctionsList))
    	
    	$this->rule = preg_replace_callback (
				'/([a-zA-Z_\-1-9]*)[\s]*\(/'
				, 'taoItems_models_classes_Matching_Matching::parseExpressionRule'
				, $rule
			).';';

        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:00000000000028E5 end
    }

    /**
     * Get a correct variable, return null if the variable does not exist
     *
     * @access protected
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string id
     * @return taoItems_models_classes_Matching_Variable
     */
    protected function getCorrect($id)
    {
        $returnValue = null;

        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:00000000000028E8 begin
        
        if (isset($this->corrects[$id]))
        	$returnValue = $this->corrects[$id];
        
        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:00000000000028E8 end

        return $returnValue;
    }

    /**
     * Short description of method getMap
     *
     * @access protected
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string id
     * @return taoItems_models_classes_Matching_Map
     */
    protected function getMap($id)
    {
        $returnValue = null;

        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:00000000000028EA begin
        
        if (isset($this->maps[$id]))
        	$returnValue = $this->maps[$id];
        
        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:00000000000028EA end

        return $returnValue;
    }

    /**
     * Short description of method getOutcome
     *
     * @access protected
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string id
     * @return taoItems_models_classes_Matching_Variable
     */
    protected function getOutcome($id)
    {
        $returnValue = null;

        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:00000000000028EC begin
        
        if (isset($this->outcomes[$id]))
        	$returnValue = $this->outcomes[$id];
        
        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:00000000000028EC end

        return $returnValue;
    }

    /**
     * Short description of method getResponse
     *
     * @access protected
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string id
     * @return taoItems_models_classes_Matching_Variable
     */
    protected function getResponse($id)
    {
        $returnValue = null;

        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:00000000000028F2 begin
        
        if (isset($this->responses[$id]))
        	$returnValue = $this->responses[$id];
        
        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:00000000000028F2 end

        return $returnValue;
    }

    /**
     * Short description of method getRule
     *
     * @access protected
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return string
     */
    protected function getRule()
    {
        $returnValue = (string) '';

        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:00000000000028F5 begin
        
        $returnValue = $this->rule;
        
        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:00000000000028F5 end

        return (string) $returnValue;
    }

    /**
     * The match operator takes two sub-expressions which must both have the
     * type and cardinality. The result is a single boolean with a value of true
     * the two expressions represent the same value and false if they do not.
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  expr1
     * @param  expr2
     * @return boolean
     */
    public function match(   $expr1,    $expr2)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:000000000000291D begin
                
        if (!isset($expr1))
        	throw new Exception ("taoItems_models_classes_Matching_Matching::match error : the first argument does not exist");
        else if (!isset($expr2))
        	throw new Exception ("taoItems_models_classes_Matching_Matching::match error : the second argument does not exist");

        if ($expr1->getType() != $expr2->getType()) { 
        	$returnValue = false;
    	} else {
        	$returnValue = $expr1->match($expr2);
        }
        
        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:000000000000291D end

        return (bool) $returnValue;
    }

    /**
     * Set the value of an outcome variable
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string id
     * @param  value
     * @return mixed
     */
    public function setOutcomeValue($id,    $value)
    {
        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:0000000000002927 begin
            	
        $outcome = $this->getOutcome ($id);
        if($outcome == null)
        	throw new Exception ('taoItems_models_classes_Matching_Matching::setOutcomeValue error : the outcome value '.$id.' does not exist');
        $outcome->setValue ($value);
        
//		$outcome->setValue ($value);
		
        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:0000000000002927 end
    }

    /**
     * Short description of method mapResponse
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Map map
     * @param  Variable expr
     * @return double
     */
    public function mapResponse( taoItems_models_classes_Matching_Map $map,  taoItems_models_classes_Matching_Variable $expr)
    {
        $returnValue = (float) 0.0;

        // section 127-0-1-1--5c70894a:12bb048b221:-8000:0000000000002A9F begin
        
        $returnValue = $map->map ($expr);
        
        // section 127-0-1-1--5c70894a:12bb048b221:-8000:0000000000002A9F end

        return (float) $returnValue;
    }

    /**
     * Short description of method andExpression
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return boolean
     */
    public function andExpression()
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--5c70894a:12bb048b221:-8000:0000000000002AA3 begin
        // section 127-0-1-1--5c70894a:12bb048b221:-8000:0000000000002AA3 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method equal
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  expr1
     * @param  expr2
     * @return boolean
     */
    public function equal(   $expr1,    $expr2)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--5c70894a:12bb048b221:-8000:0000000000002AA7 begin
        // section 127-0-1-1--5c70894a:12bb048b221:-8000:0000000000002AA7 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method isNull
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Variable var
     * @return boolean
     */
    public function isNull( taoItems_models_classes_Matching_Variable $var)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--40e88075:12bbb016df2:-8000:00000000000046A6 begin
        
        $returnValue = $var->isNull();
        
        // section 127-0-1-1--40e88075:12bbb016df2:-8000:00000000000046A6 end

        return (bool) $returnValue;
    }

} /* end of class taoItems_models_classes_Matching_Matching */

?>