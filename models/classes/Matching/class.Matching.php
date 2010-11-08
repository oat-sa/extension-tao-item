<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/models/classes/Matching/class.Matching.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 05.11.2010, 14:12:26 with ArgoUML PHP module 
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

    /**
     * Short description of attribute MATCH_CORRECT
     *
     * @access public
     * @var string
     */
    const MATCH_CORRECT = 'if(match(null, getResponse("RESPONSE"), getCorrect("RESPONSE"))) setOutcomeValue("SCORE", 1); else setOutcomeValue("SCORE", 0);';

    /**
     * Short description of attribute MAP_RESPONSE
     *
     * @access public
     * @var string
     */
    const MAP_RESPONSE = 'if(isNull(null, getResponse("RESPONSE"))) { setOutcomeValue("SCORE", 0); } else { setOutcomeValue("SCORE", mapResponse(null, getMap("RESPONSE"), getResponse("RESPONSE"))); }';

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
            , 'createVariable'=>array()
			, 'equal'=>array()
			, 'if'=>array('native'=>true)
			, 'isNull'=>array()
			, 'getCorrect'=>array()
			, 'getMap'=>array()
			, 'getResponse'=>array()
			, 'mapResponse'=>array()
            , 'match'=>array()
            , 'not'=>array()
            , 'ordered'=>array()
            , 'setOutcomeValue'=>array()
		);
        // section 127-0-1-1--5c70894a:12bb048b221:-8000:0000000000002AB1 end
    }

    public function checkOptions ($options){
        // Decode the options, if it has been "json string encoded"
        if (gettype($options) == 'string') $options = json_decode ($options);
        else if ($options == null) $options = (object)Array();
        return (object) $options;
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
        
        $functionName = $matches[1];
		$whiteListedFunction = null;
        
        // Get the whitelisted function
        if (isset(taoItems_models_classes_Matching_Matching::$whiteFunctionsList[$matches[1]])){
            $whiteListedFunction = taoItems_models_classes_Matching_Matching::$whiteFunctionsList[$matches[1]];
        }
		
        // The function is white listed
        if (isset($whiteListedFunction))
        {
            // The function is a native php function
            if (isset($whiteListedFunction['native']) && $whiteListedFunction['native']){
                // Nothing
            }
            // Check if the function is present in the matching engine functions pool
            else {
                if (!method_exists ('taoItems_models_classes_Matching_Matching', $functionName)) {
                    // Check if the function has been mapped
                    if (isset($whiteListedFunction['mappedFunction'])){
                        // The function has been mapped but the function is not present in the matching engine functions pool
                        if (!method_exists ('taoItems_models_classes_Matching_Matching', $whiteListedFunction['mappedFunction'])) {
                            throw new Exception ('taoItems_models_classes_Matching_Matching::parseExpressionRule an error occured, the expression ['. $functionName .'] has been mapped to ['. $whiteListedFunction['mappedFunction'] .'] but is not yet instantiated');
                        }
                        $functionName = $whiteListedFunction['mappedFunction'];
                    }
                }
                // Map the function to use it from the matching engine
                $functionName = '$this->'.$functionName;
            }
		} else
        {
            throw new Exception ('taoItems_models_classes_Matching_Matching::parseExpressionRule an error occured, the expression ['. $functionName .'] is unknown ');
        }
        
		$returnValue = $functionName.' (';
		
        // section 127-0-1-1--5c70894a:12bb048b221:-8000:0000000000002AAB end

        return (string) $returnValue;
    }

    /**
     * Evaluate the matching rule
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     */
    public function evaluate()
    {
        $returnValue = null;

        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:00000000000028DA begin
        
        /*echo 'rule';
        pr ($this->rule);
    	echo 'corrects vars';
    	pr ($this->corrects);
    	echo 'responses vars';
    	pr ($this->responses);
        echo 'maps vars';
        pr ($this->maps);
        echo 'outcomes vars';
        pr ($this->outcomes);*/
        
        try {
			eval ($this->getRule());
		} catch (Exception $e) {
			throw new Exception ('an error occured during the evaluation of the rule : '.$e);
		}
		
        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:00000000000028DA end

        return $returnValue;
    }

    /**
     * Get the matching rule
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
     * Get the outcome in the defined JSON format
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
     * Set the corrects
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
     * Set the mappings
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
     * Set the outcomes
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
     * Set the responses
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
     * Set the matching rule
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
     * Get a correct variable from its identifier
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
     * Get a mapping variable from its identifier
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
     * Get an outcome variable from its identifier
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
     * Get a response variable from its identifier
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
     * The and operator takes one or more sub-expressions each with a base-type
     * boolean and single cardinality. The result is a single boolean which is
     * if all sub-expressions are true and false if any of them are false.
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  array options
     * @return boolean
     */
    public function andExpression($options)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--5c70894a:12bb048b221:-8000:0000000000002AA3 begin
        $options = $this->checkOptions ($options);
        
        $returnValue = true;
        
        // for each arguments (which are expressions) 
        for ($i = 1; $i < func_num_args(); ++$i) {
            $subExp = func_get_arg($i);
            $subExpValue = null;
            
            // QTIVariable sub-expression
            if ($subExp instanceof taoItems_models_classes_Matching_BaseTypeVariable){
                if ($subExp->getType() != 'boolean') { 
                    throw new Error('AND operator requires sub-expressions with single cardinality and boolean baseType');
                }
                $subExpValue = $subExp->getValue ();
            
            // ! Basic Boolean sub-expression
            }else if (!is_bool ($subExp)){
                throw new Error ('AND operator requires sub-expressions with single cardinality and boolean baseType');

            // Basic Boolean sub-expression
            }else{
                $subExpValue = $subExp;
            }
            
            $returnValue = $returnValue && $subExpValue;
        }
        
        // section 127-0-1-1--5c70894a:12bb048b221:-8000:0000000000002AA3 end

        return (bool) $returnValue;
    }

    /**
     * The equal operator takes two sub-expressions which must both have single
     * and have a numerical base-type. The result is a single boolean with a
     * of true if the two expressions are numerically equal and false if they
     * not.
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  array options
     * @param  expr1
     * @param  expr2
     * @return boolean
     */
    public function equal($options,    $expr1,    $expr2)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--5c70894a:12bb048b221:-8000:0000000000002AA7 begin
        $options = $this->checkOptions ($options);
        
        $allowedBasicTypes = array('integer', 'float', 'double');
        $subExp1Value = null;
        $subExp2Value = null;
        
        // The first sub-expression is QTIVariable
        if ($subExp1 instanceof taoItems_models_classes_Matching_BaseTypeVariable){
            if (!in_array($subExp1->getType(), $allowedBasicTypes))
                 throw new Exception('EQUAL operator error : the first argument must be numerical');
            $subExp1Value = $subExp1->getValue();
        }
        // The first expression is not an allowed basic type
        else if (!in_array(gettype($subExp1), $allowedBasicTypes)) {
            throw new Exception('EQUAL operator error : the first argument must be numerical');
        }
        else {
            $subExp1Value = $subExp1;
        }
        
        // The second sub-expression is QTIVariable
        if ($subExp2 instanceof taoItems_models_classes_Matching_BaseTypeVariable){
            if (!in_array($subExp2->getType(), $allowedQTITypes))
                 throw new Error('EQUAL operator error : the second argument must be numerical');
            $subExp2Value = $subExp2->values[0];
        }
        // The first expression is not an allowed basic type
        else if (!in_array(gettype($subExp2), $allowedBasicTypes)) {
            throw new Error('EQUAL operator error : the first second must be numerical');
        }
        
        else {
            $subExp2Value = $subExp2;
        }
        
        if ($subExp1Value!=null && $subExp2Value!=null)         
            $returnValue = $subExp1Value == $subExp2Value;
        
        // section 127-0-1-1--5c70894a:12bb048b221:-8000:0000000000002AA7 end

        return (bool) $returnValue;
    }

    /**
     * The isNull operator takes a sub-expression with any base-type and
     * The result is a single boolean with a value of true if the sub-expression
     * NULL and false otherwise.
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  array options
     * @param  Variable var
     * @return boolean
     */
    public function isNull($options,  taoItems_models_classes_Matching_Variable $var)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--40e88075:12bbb016df2:-8000:00000000000046A6 begin
        $options = $this->checkOptions ($options);
        
        $returnValue = $var->isNull();
        
        // section 127-0-1-1--40e88075:12bbb016df2:-8000:00000000000046A6 end

        return (bool) $returnValue;
    }

    /**
     * This expression looks up the value of a responseVariable and then
     * it using the associated mapping, which must have been declared. The
     * is a single float. If the response variable has single cardinality then
     * value returned is simply the mapped target value from the map. If the
     * variable has single or multiple cardinality then the value returned is
     * sum of the mapped target values. This expression cannot be applied to
     * of record cardinality.
     *
     * For example, if a mapping associates the identifiers {A,B,C,D} with the
     * {0,1,0.5,0} respectively then mapResponse will map the single value 'C'
     * the numeric value 0.5 and the set of values {C,B} to the value 1.5.
     *
     * If a container contains multiple instances of the same value then that
     * is counted once only. To continue the example above {B,B,C} would still
     * to 1.5 and not 2.5.
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  array options
     * @param  Map map
     * @param  Variable expr
     * @return double
     */
    public function mapResponse($options,  taoItems_models_classes_Matching_Map $map,  taoItems_models_classes_Matching_Variable $expr)
    {
        $returnValue = (float) 0.0;

        // section 127-0-1-1--5c70894a:12bb048b221:-8000:0000000000002A9F begin
        $options = $this->checkOptions ($options);
        $returnValue = $map->map ($expr);
        // section 127-0-1-1--5c70894a:12bb048b221:-8000:0000000000002A9F end

        return (float) $returnValue;
    }

    /**
     * The match operator takes two sub-expressions which must both have the
     * type and cardinality. The result is a single boolean with a value of true
     * the two expressions represent the same value and false if they do not.
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  array options
     * @param  expr1
     * @param  expr2
     * @return boolean
     */
    public function match($options,    $expr1,    $expr2)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:000000000000291D begin
        $options = $this->checkOptions ($options);
        
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
     * The not operator takes a single sub-expression with a base-type of
     * and single cardinality. The result is a single boolean with a value
     * by the logical negation of the sub-expression's value.
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  array options
     * @param  subExp
     * @return boolean
     */
    public function not($options,    $subExp)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--7cd26fee:12bf37febcd:-8000:0000000000002A4C begin
        $options = $this->checkOptions ($options);
        
        // QTIVariable sub-expression
        if ($subExp instanceof taoItems_models_classes_Matching_BaseTypeVariable){
            if ($subExp->getType() != 'boolean') { 
                throw new Exception ('taoItems_models_classes_Matching_Matching::not : NOT operator requires a sub-expression with single cardinality and boolean baseType');
            }
            $subExpValue = $subExp->getValue ();
        
        // ! Basic Boolean sub-expression
        }else if (!is_bool ($subExp)){
            throw new Exception ('taoItems_models_classes_Matching_Matching::not : NOT operator requires a sub-expression with single cardinality and boolean baseType');

        // Basic Boolean sub-expression
        }else{
            $subExpValue = $subExp;
        }
        
        $returnValue = !$subExpValue;
        
        // section 127-0-1-1--7cd26fee:12bf37febcd:-8000:0000000000002A4C end

        return (bool) $returnValue;
    }

    /**
     * Short description of method createVariable
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  array options
     * @return taoItems_models_classes_Matching_Tuple
     */
    public function createVariable($options)
    {
        $returnValue = null;

        // section 127-0-1-1-554f2bd6:12c176484b7:-8000:0000000000002B21 begin
        $options = $this->checkOptions ($options);
        
        // Type undefined, we are in the case of baseTypeVariable creation (cardinality single)
        if (!isset($options->type)) {
            $returnValue = taoItems_models_classes_Matching_VariableFactory::create (func_get_arg(1));
        }
        else 
        {
            switch ($options->type){
                // Create a BaseTypeVariable
                case 'integer':
                case 'float':
                case 'string':
                case 'boolean':
                    // In all the base type cases create a variable with the first found argument
                    $returnValue = taoItems_models_classes_Matching_VariableFactory::create (func_get_arg(1));
                    break;
                
                // Create a Tuple    
                case 'tuple':
                    $values = Array ();
                    $a = 0;
                    for ($i = 1; $i < func_num_args(); ++$i, ++$a) {
                        $values[$a] = func_get_arg($i);
                    }
                    $returnValue = taoItems_models_classes_Matching_VariableFactory::create ((object)$values);
                    break;
                    
                // Create a List
                case 'list':
                    $values = Array ();
                    $a = 0;
                    for ($i = 1; $i < func_num_args(); ++$i, ++$a) {
                        $values[$a] = func_get_arg($i);
                    }
                    $returnValue = taoItems_models_classes_Matching_VariableFactory::create ($values);
                    break;
                
                // Type unknown, throw an Exception
                case 'default':
                    throw new Exception ('taoItems_models_classes_Matching_Matching::createVariable : type unknown ['.$options->type.']');
            }  
        }
        // section 127-0-1-1-554f2bd6:12c176484b7:-8000:0000000000002B21 end

        return $returnValue;
    }

    /**
     * The setOutcomeValue sets the value of an outcomeVariable.
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
        
        // Get the outcome to update    	
        $outcome = $this->getOutcome ($id);
        
        // Update the value of the outcome
        if($outcome == null){
            throw new Exception ('taoItems_models_classes_Matching_Matching::setOutcomeValue error : the outcome value '.$id.' does not exist');
        }
        if ($value instanceof taoItems_models_classes_Matching_BaseTypeVariable){
            $outcome->setValue ($value->getValue());
        }
        else {
            if (taoItems_models_classes_Matching_VariableFactory::isValidBaseType ($value)){
                $outcome->setValue ($value);
            }else{
                throw new Exception ('taoItems_models_classes_Matching_Matching::setOutcomeValue error : unable to set a value of this type ['.gettype($value).']');
            }
        }
        
        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:0000000000002927 end
    }

} /* end of class taoItems_models_classes_Matching_Matching */

?>