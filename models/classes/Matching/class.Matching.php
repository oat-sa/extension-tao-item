<?php

error_reporting(E_ALL);

/**
 * Matching provides a full API based on the QTI matching model to
 * score items or whatever.
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_Matching
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * A special class used to create a mapping from a source set of 
 * point values to a target set of float values. When mapping 
 * containers the result is the sum of the mapped values from
 * the target set
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 */
require_once('taoItems/models/classes/Matching/class.AreaMap.php');

/**
 * A special class used to create a mapping from a source set of 
 * any baseType to a single float.
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 */
require_once('taoItems/models/classes/Matching/class.Map.php');

/**
 * Variable is an abstract class which is the representation 
 * of all the variables managed by the system
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 */
require_once('taoItems/models/classes/Matching/class.Variable.php');

/**
 * The class variable factory provide to developpers a set 
 * of usefull functions arround the variables creation process
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
 * Matching provides a full API based on the QTI matching model to
 * score items or whatever.
 *
 * @access public
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_Matching
 */
class taoItems_models_classes_Matching_Matching
{
    // --- ASSOCIATIONS ---
    // generateAssociationEnd :     // generateAssociationEnd :     // generateAssociationEnd :     // generateAssociationEnd :     // generateAssociationEnd : 

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

    /**
     * Short description of attribute areaMaps
     *
     * @access protected
     * @var array
     */
    protected $areaMaps = array();

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
            , 'contains'=>array()
            , 'createVariable'=>array()
            , 'divide'=>array()
			, 'equal'=>array()
            , 'if'=>array('native'=>true)
            , 'integerDivide'=>array()
			, 'isNull'=>array()
			, 'getCorrect'=>array()
			, 'getMap'=>array()
            , 'getResponse'=>array()
            , 'getVariable'=>array()
            , 'gt'=>array()
            , 'gte'=>array()
            , 'lt'=>array()
            , 'lte'=>array()
            , 'mapResponse'=>array()
            , 'mapResponsePoint'=>array()
            , 'match'=>array()
            , 'not'=>array()
            , 'or'=>array('mappedFunction'=>'orExpression')
            , 'randomFloat'=>array()
            , 'randomInteger'=>array()
            , 'round'=>array()
            , 'setOutcomeValue'=>array()
            , 'subtract'=>array()
            , 'sum'=>array()
		);
        // section 127-0-1-1--5c70894a:12bb048b221:-8000:0000000000002AB1 end
    }

    /**
     * Short description of method checkOptions
     *
     * @access private
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  options
     * @return object
     */
    private function checkOptions(   $options)
    {
        $returnValue = null;

        // section 127-0-1-1-7aeba85b:12c2bdfd93f:-8000:0000000000002B47 begin
        
        // If json encoded string given
        if (gettype($options) == 'string') {
            $options = json_decode ($options, true);
        }
        // Else object given
        else if (gettype($options) == 'object'){
            $options = (array) $options;
        }
        // If null
        else if ($options == null) {
            $options = Array();
        }
        
        $returnValue = $options;
        
        // section 127-0-1-1-7aeba85b:12c2bdfd93f:-8000:0000000000002B47 end

        return $returnValue;
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
     * Short description of method setAreaMaps
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  data
     * @return mixed
     */
    public function setAreaMaps(   $data)
    {
        // section 127-0-1-1--1f4c3271:12ce9f13e78:-8000:0000000000002CD6 begin
        
        if (gettype($data) != 'array'){
            throw new Exception ('taoItems_models_classes_Matching_Matching::setAreaMaps is waiting on an array, a '.gettype($data).' is given. '.$data);
        }

        foreach ($data as $key=>$map){
            try {
                $var =  new taoItems_models_classes_Matching_AreaMap ($map->value);
                
                if (isset ($this->areaMaps[$map->identifier]))
                    throw new Exception ('taoItems_models_classes_Matching_Matching::setMaps a map variable with the identifier '.$map->identifier.' exists yet');

                $this->areaMaps[$map->identifier] = $var;
            }
            
            catch (Exception $e){
                throw $e;
            }
        }
        
        // section 127-0-1-1--1f4c3271:12ce9f13e78:-8000:0000000000002CD6 end
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
					throw new Exception ('taoItems_models_classes_Matching_Matching::setReponses a response variable with the identifier '.$response->identifier.' exists yet');

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
     * The and operator takes one or more sub-expressions 
     * each with a base-type of boolean and single cardinality. 
     * The result is a single boolean which is true if all
     * sub-expressions are true and false if any of them 
     * are false.
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
        $returnValue = null;
        $options = $this->checkOptions ($options);

        // for each arguments (which are expressions) 
        for ($i = 1; $i < func_num_args(); ++$i) {
            $subExp = func_get_arg($i);
            $matchingSubExp = taoItems_models_classes_Matching_VariableFactory::toBooleanBaseType ($subExp);
            
            if ($matchingSubExp == null) {
                throw new Exception ("TtaoItems_models_classes_Matching_Matching::or an error occured : The expression passed [".$matchingSubExp."] to the operator has to be a valid boolean expression with single cardinality");
            } else {
                if ($matchingSubExp->isNull()){
                    $returnValue = null;
                    break;
                }else{
                    if ($returnValue === null){
                        $returnValue = $matchingSubExp->getValue();
                    } else {
                        $returnValue = $returnValue && $matchingSubExp->getValue();
                    }
                }
            }
        }
        // section 127-0-1-1--5c70894a:12bb048b221:-8000:0000000000002AA3 end

        return (bool) $returnValue;
    }

    /**
     * Create a variable functions of the arguments.
     * Create either scalar or container from the value :
     * createVariable (null, 3.1415);
     * createVariable (null, Array ("TAO", "Test Assisté par Ordinateur"))
     * Create container following the options.type and the arguments of the
     * createVariable (Array("type"=>"list"), "TAO", "Test Assisté par
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  array options
     * @return taoItems_models_classes_Matching_Variable
     */
    public function createVariable($options)
    {
        $returnValue = null;

        // section 127-0-1-1-554f2bd6:12c176484b7:-8000:0000000000002B21 begin
        $options = $this->checkOptions ($options);
        
        // Type undefined, we are in the case of baseTypeVariable creation (cardinality single)
        if (!isset($options['type'])) {
            $returnValue = taoItems_models_classes_Matching_VariableFactory::create (func_get_arg(1));
        }
        else 
        {
            switch ($options['type']){
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
                    throw new Exception ('taoItems_models_classes_Matching_Matching::createVariable : type unknown ['.$options['type'].']');
            }  
        }
        // section 127-0-1-1-554f2bd6:12c176484b7:-8000:0000000000002B21 end

        return $returnValue;
    }

    /**
     * The contains function takes two sub-expressions. The 
     * first one has a cardinality  (either list or tuple). 
     * The second one could have any base type and could 
     * have the same cardinality than the first expression or 
     * it could have a single cardinality.
     * The result is a single boolean with a value of true if 
     * the container given by the first sub-expression 
     * contains the value given by the second sub-expression
     * and false if it doesn't. Note that the contains operator
     * works differently depending on the cardinality of the 
     * two sub-expressions. 
     *
     * For unordered containers the values are compared
     * without regard for ordering, for example, [A,B,C] 
     * contains [C,A]. Note that [A,B,C] does not contain 
     * [B,B] but that [A,B,B,C] does. For ordered containers 
     * the second sub-expression must be a strict 
     * sub-sequence within the first. In other words, [A,B,C] 
     * does not contain [C,A] but it does contain [B,C].
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  options
     * @param  expr1
     * @param  expr2
     * @return taoItems_models_classes_Matching_bool
     */
    public function contains(   $options,    $expr1,    $expr2)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-4b0fb01e:12c5430948f:-8000:0000000000002BDC begin
        
        $options = $this->checkOptions ($options);
        
        if (!($expr1 instanceOf taoItems_models_classes_Matching_Collection)) {
            throw new Exception ("TtaoItems_models_classes_Matching_Matching::contains an error occured : The operator requires as first argument an expression of type Collection");
        }
        
        $returnValue = $expr1->contains($expr2, $options);
        
        // section 127-0-1-1-4b0fb01e:12c5430948f:-8000:0000000000002BDC end

        return (bool) $returnValue;
    }

    /**
     * The divide operator takes 2 sub-expressions which both 
     * have single cardinality and numerical base-types. The
     * result is a single float that corresponds to the first 
     * expression divided by the second expression. If either
     * of the sub-expressions is NULL then the operator 
     * results in NULL.
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  options
     * @param  expr1
     * @param  expr2
     */
    public function divide(   $options,    $expr1,    $expr2)
    {
        $returnValue = null;

        // section 127-0-1-1-196de192:12c30421176:-8000:0000000000002B7D begin
        
        $matchingExpr1 = taoItems_models_classes_Matching_VariableFactory::toNumericBaseType ($expr1);
        // IF the first expression is not a numerical base type
        if ($matchingExpr1 == null) {
            throw new Exception ("TtaoItems_models_classes_Matching_Matching::divide an error occured : The first expression passed [".$expr1."] to the operator has to be a valid numerical expression with single cardinality");
        }

        $matchingExpr2 = taoItems_models_classes_Matching_VariableFactory::toNumericBaseType ($expr2);
        // IF the second expression is not a numerical base type
        if ($matchingExpr2 == null) {
            throw new Exception ("TtaoItems_models_classes_Matching_Matching::divide an error occured : The second expression passed [".$expr2."] to the operator has to be a valid numerical expression with single cardinality");
        }
        
        if ($matchingExpr1->isNull() || $matchingExpr2->isNull() || $matchingExpr2->getValue()===0){
            $returnValue = null;
        } else {
            $returnValue = $matchingExpr1->getValue() / $matchingExpr2->getValue();
        }
                
        // section 127-0-1-1-196de192:12c30421176:-8000:0000000000002B7D end

        return $returnValue;
    }

    /**
     * The equal operator takes two sub-expressions which must 
     * both have single cardinality and have a numerical base-type. 
     * The result is a single boolean with a value of true if the two 
     * expressions are numerically equal and false if they are not.
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
        
        $expr1Value = null;
        $expr2Value = null;
        
        // The first expr is a Base Type Variable
        if ($expr1 instanceof taoItems_models_classes_Matching_BaseTypeVariable){
            $expr1Value = $expr1->getValue();
        }
        // The first expression is not a scalar
        else if (!is_scalar ($expr1)) {
            throw new Exception('taoItems_models_classes_Matching_Matching::equal : the first argument must be a scalar');
        }
        else {
            $expr1Value = $expr1;
        }
        
        // The second expr is a Base Type Variable
        if ($expr2 instanceof taoItems_models_classes_Matching_BaseTypeVariable){
            $expr2Value = $expr2->getValue();
        }
        // The second expr is not a scalar
        else if (!is_scalar($expr2)) {
            throw new Error('taoItems_models_classes_Matching_Matching::equal : the second argument must be a scalar');
        }
        else {
            $expr2Value = $expr2;
        }
        
        if ($expr1Value!=null && $expr2Value!=null)         
            $returnValue = $expr1Value == $expr2Value;
        
        // section 127-0-1-1--5c70894a:12bb048b221:-8000:0000000000002AA7 end

        return (bool) $returnValue;
    }

    /**
     * Get a correct variable from its identifier
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string id
     * @return taoItems_models_classes_Matching_Variable
     */
    public function getCorrect($id)
    {
        $returnValue = null;

        // section 127-0-1-1--5e9b2cb2:12c47296979:-8000:0000000000002BDE begin
        
        if (isset($this->corrects[$id])){
            $returnValue = $this->corrects[$id];
        }
        
        // section 127-0-1-1--5e9b2cb2:12c47296979:-8000:0000000000002BDE end

        return $returnValue;
    }

    /**
     * Get a mapping variable from its identifier
     *
     * @access protected
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string id
     * @param  string type
     * @return taoItems_models_classes_Matching_Map
     */
    protected function getMap($id, $type = null)
    {
        $returnValue = null;

        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:00000000000028EA begin
        
        $targetArray = $this->maps;
        if (isset($type) && $type=="area"){
            $targetArray = $this->areaMaps;
        }
        
        if (isset($targetArray[$id])){
        	$returnValue = $targetArray[$id];
        }
        
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
     * Get a variable from its identifier
     *
     * @access protected
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string id
     * @return taoItems_models_classes_Matching_Variable
     */
    protected function getVariable($id)
    {
        $returnValue = null;

        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:00000000000028E8 begin
        
        if (isset($this->responses[$id])){
            $returnValue = $this->responses[$id];
        }
        else if (isset($this->outcomes[$id])) {
            $returnValue = $this->outcomes[$id];
        }
        
        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:00000000000028E8 end

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
     * The gt operator takes two sub-expressions which must 
     * both have single cardinality and have a numerical 
     * base-type. The result is a single boolean with a value 
     * of true if the first expression is numerically greater 
     * than the second and false if it is less than or equal
     * to the second.
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  options
     * @param  expr1
     * @param  expr2
     * @return boolean
     */
    public function gt(   $options,    $expr1,    $expr2)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-d88aba0:12c2bef8126:-8000:0000000000002B4A begin
        
        $matchingExpr1 = taoItems_models_classes_Matching_VariableFactory::toNumericBaseType ($expr1);
        // IF the first expression is not a numerical base type
        if ($matchingExpr1 == null) {
            throw new Exception ("TtaoItems_models_classes_Matching_Matching::gt an error occured : The first expression passed [".$expr1."] to the operator has to be a valid numerical expression with single cardinality");
        }

        $matchingExpr2 = taoItems_models_classes_Matching_VariableFactory::toNumericBaseType ($expr2);
        // IF the second expression is not a numerical base type
        if ($matchingExpr2 == null) {
            throw new Exception ("TtaoItems_models_classes_Matching_Matching::gt an error occured : The second expression passed [".$expr2."] to the operator has to be a valid numerical expression with single cardinality");
        }
        
        if ($matchingExpr1->getValue() > $matchingExpr2->getValue()) {
            $returnValue = true;
        }
        
        // section 127-0-1-1-d88aba0:12c2bef8126:-8000:0000000000002B4A end

        return (bool) $returnValue;
    }

    /**
     * The gte operator takes two sub-expressions which must 
     * both have single cardinality and have a numerical base-type.
     * The result is a single boolean with a value of true if the first
     * expression is numerically less than or equal to the second
     * and false if it is greater than the second.
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  options
     * @param  expr1
     * @param  expr2
     * @return boolean
     */
    public function gte(   $options,    $expr1,    $expr2)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-196de192:12c30421176:-8000:0000000000002B6B begin
        
        $matchingExpr1 = taoItems_models_classes_Matching_VariableFactory::toNumericBaseType ($expr1);
        // IF the first expression is not a numerical base type
        if ($matchingExpr1 == null) {
            throw new Exception ("TtaoItems_models_classes_Matching_Matching::gt an error occured : The first expression passed [".$expr1."] to the operator has to be a valid numerical expression with single cardinality");
        }

        $matchingExpr2 = taoItems_models_classes_Matching_VariableFactory::toNumericBaseType ($expr2);
        // IF the second expression is not a numerical base type
        if ($matchingExpr2 == null) {
            throw new Exception ("TtaoItems_models_classes_Matching_Matching::gt an error occured : The second expression passed [".$expr2."] to the operator has to be a valid numerical expression with single cardinality");
        }
        
        if ($matchingExpr1->getValue() >= $matchingExpr2->getValue()) {
            $returnValue = true;
        }
        
        // section 127-0-1-1-196de192:12c30421176:-8000:0000000000002B6B end

        return (bool) $returnValue;
    }

    /**
     * The integer divide operator takes 2 sub-expressions which
     * both have single cardinality and base-type integer. The result
     * is the single integer that corresponds to the first expression
     * (x) divided by the second expression (y) rounded down to 
     * the greatest integer (i) such that i<=(x/y).
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  options
     * @param  expr1
     * @param  expr2
     * @return int
     */
    public function integerDivide(   $options,    $expr1,    $expr2)
    {
        $returnValue = (int) 0;

        // section 127-0-1-1-7e272ec4:12c307f74c9:-8000:0000000000002B8A begin
        
        $returnValue = $this->round (null, $this->divide ($options, $expr1, $expr2));
        
        // section 127-0-1-1-7e272ec4:12c307f74c9:-8000:0000000000002B8A end

        return (int) $returnValue;
    }

    /**
     * The isNull operator takes a sub-expression with 
     * any base-type and cardinality. The result is a single 
     * boolean with a value of true if the sub-expression is 
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
     * The lt operator takes two sub-expressions which must both
     * have single cardinality and have a numerical base-type. The
     * result is a single boolean with a value of true if the first
     * expression is numerically less than the second and false if 
     * t is greater than or equal to the second.
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  options
     * @param  expr1
     * @param  expr2
     * @return boolean
     */
    public function lt(   $options,    $expr1,    $expr2)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-d88aba0:12c2bef8126:-8000:0000000000002B4F begin
        
        $matchingExpr1 = taoItems_models_classes_Matching_VariableFactory::toNumericBaseType ($expr1);
        // IF the first expression is not a numerical base type
        if ($matchingExpr1 == null) {
            throw new Exception ("TtaoItems_models_classes_Matching_Matching::lt an error occured : The first expression passed [".$expr1."] to the operator has to be a valid numerical expression with single cardinality");
        }

        $matchingExpr2 = taoItems_models_classes_Matching_VariableFactory::toNumericBaseType ($expr2);
        // IF the second expression is not a numerical base type
        if ($matchingExpr2 == null) {
            throw new Exception ("TtaoItems_models_classes_Matching_Matching::lt an error occured : The second expression passed [".$expr2."] to the operator has to be a valid numerical expression with single cardinality");
        }
        
        if ($matchingExpr1->getValue() < $matchingExpr2->getValue()) {
            $returnValue = true;
        }
        
        // section 127-0-1-1-d88aba0:12c2bef8126:-8000:0000000000002B4F end

        return (bool) $returnValue;
    }

    /**
     * The lte operator takes two sub-expressions which must both
     * have single cardinality and have a numerical base-type. The
     * result is a single boolean with a value of true if the first
     * expression is numerically less than or equal to the second
     * and false if it is greater than the second.
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  options
     * @param  expr1
     * @param  expr2
     * @return boolean
     */
    public function lte(   $options,    $expr1,    $expr2)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-196de192:12c30421176:-8000:0000000000002B70 begin
        
        $matchingExpr1 = taoItems_models_classes_Matching_VariableFactory::toNumericBaseType ($expr1);
        // IF the first expression is not a numerical base type
        if ($matchingExpr1 == null) {
            throw new Exception ("TtaoItems_models_classes_Matching_Matching::lt an error occured : The first expression passed [".$expr1."] to the operator has to be a valid numerical expression with single cardinality");
        }

        $matchingExpr2 = taoItems_models_classes_Matching_VariableFactory::toNumericBaseType ($expr2);
        // IF the second expression is not a numerical base type
        if ($matchingExpr2 == null) {
            throw new Exception ("TtaoItems_models_classes_Matching_Matching::lt an error occured : The second expression passed [".$expr2."] to the operator has to be a valid numerical expression with single cardinality");
        }
        
        if ($matchingExpr1->getValue() <= $matchingExpr2->getValue()) {
            $returnValue = true;
        }
        
        // section 127-0-1-1-196de192:12c30421176:-8000:0000000000002B70 end

        return (bool) $returnValue;
    }

    /**
     * This expression looks up the value of a response 
     * Variable and then transforms it using the associated 
     * mapping, which must have been declared. The result 
     * is a single float. If the response variable has single 
     * cardinality then the value returned is simply the 
     * mapped target value from the map. If the response 
     * variable has single or multiple cardinality then the 
     * value returned is the sum of the mapped target values. 
     * This expression cannot be applied to variables of record cardinality.
     *
     * For example, if a mapping associates the identifiers 
     * {A,B,C,D} with the values {0,1,0.5,0} respectively 
     * then mapResponse will map the single value 'C' to the 
     * numeric value 0.5 and the set of values {C,B} to the 
     * value 1.5.
     *
     * If a container contains multiple instances of the same 
     * value then that value is counted once only. To continue 
     * the example above {B,B,C} would still map to 1.5 and 
     * not 2.5.
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
        
        if (!isset($map)){
            throw new Exception ("taoItems_models_classes_Matching_Matching::mapResponse error : the first argument [taoItems_models_classes_Matching_Variable] does not exist");
        }
        else if (!isset($expr)){
            throw new Exception ("taoItems_models_classes_Matching_Matching::mapResponse error : the second argument [taoItems_models_classes_Matching_Variable] does not exist");
        }
        
        $returnValue = $map->map ($expr);
        
        // section 127-0-1-1--5c70894a:12bb048b221:-8000:0000000000002A9F end

        return (float) $returnValue;
    }

    /**
     * This expression looks up the value of a response 
     * Variable that must be of base-type point , and 
     * transforms it using the associated areaMapping. 
     * The transformation is similar to mapResponse 
     * except that the points are tested
     * against each area in turn. When mapping containers 
     * each area can be mapped once only. For example, if the
     * candidate identified two points that both fall in the same
     * area then the mappedValue is still added to the
     * calculated total just once.
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  array options
     * @param  AreaMap map
     * @param  Variable expr
     * @return double
     */
    public function mapResponsePoint($options,  taoItems_models_classes_Matching_AreaMap $map,  taoItems_models_classes_Matching_Variable $expr)
    {
        $returnValue = (float) 0.0;

        // section 127-0-1-1--1f4c3271:12ce9f13e78:-8000:0000000000002CD9 begin

        $options = $this->checkOptions ($options);
        
        if (!isset($map)){
            throw new Exception ("taoItems_models_classes_Matching_Matching::mapResponsePoint error : the first argument [taoItems_models_classes_Matching_AreaMap] does not exist");
        }
        else if (!isset($expr)){
            throw new Exception ("taoItems_models_classes_Matching_Matching::mapResponsePoint error : the second argument [taoItems_models_classes_Matching_Variable] does not exist");
        }
        
        $returnValue = $map->map ($expr);
        
        // section 127-0-1-1--1f4c3271:12ce9f13e78:-8000:0000000000002CD9 end

        return (float) $returnValue;
    }

    /**
     * The match operator takes two sub-expressions 
     * which must both have the same type and cardinality. 
     * The result is a single boolean with a value of true if 
     * the two expressions represent the same value and 
     * false if they do not.
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
        
        //echo '<pre>';print_r($expr1);echo '</pre>';
        //echo '<pre>';print_r($expr2);echo '</pre>';
        
        if (!isset($expr1)){
            throw new Exception ("taoItems_models_classes_Matching_Matching::match error : the first argument does not exist");
        }
        else if (!isset($expr2)){
            throw new Exception ("taoItems_models_classes_Matching_Matching::match error : the second argument does not exist");
        }

        if ($expr1->getType() != $expr2->getType()) { 
        	$returnValue = false;
    	} else {
        	$returnValue = $expr1->match($expr2);
        }
        
        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:000000000000291D end

        return (bool) $returnValue;
    }

    /**
     * The not operator takes a single sub-expression with a
     * base-type of boolean and single cardinality. The result is a
     * single boolean with a value obtained by the logical negation
     * of the sub-expression's value.
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  options
     * @param  expr
     * @return boolean
     */
    public function not(   $options,    $expr)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-7e272ec4:12c307f74c9:-8000:0000000000002B93 begin
        
        $matchingExpr = taoItems_models_classes_Matching_VariableFactory::toBooleanBaseType ($expr);
        if ($matchingExpr == null){
            throw new Exception ("TtaoItems_models_classes_Matching_Matching::not an error occured : The expression passed [".$expr."] to the operator has to be a valid boolean expression with single cardinality");
        } else {
            $matchingExprValue = $matchingExpr->getValue();
            if ($matchingExprValue !== null){
                $returnValue = ! $matchingExprValue;
            }
        }
        
        // section 127-0-1-1-7e272ec4:12c307f74c9:-8000:0000000000002B93 end

        return (bool) $returnValue;
    }

    /**
     * The or operator takes one or more sub-expressions 
     * each with a base-type of boolean and single cardinality. 
     * The result is a single boolean which is true if any of 
     * the sub-expressions are true and false if all of them 
     * are false. If one or more sub-expressions are NULL 
     * and all the others are false then the operator also results 
     * in NULL.
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  options
     * @return boolean
     */
    public function orExpression(   $options)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-7e272ec4:12c307f74c9:-8000:0000000000002B9C begin
        
        $options = $this->checkOptions ($options);
        
        // for each arguments (which are expressions) 
        for ($i = 1; $i < func_num_args(); ++$i) {
            $subExp = func_get_arg($i);
            $matchingSubExp = taoItems_models_classes_Matching_VariableFactory::toBooleanBaseType ($subExp);
            
            if ($matchingSubExp == null) {
                throw new Exception ("TtaoItems_models_classes_Matching_Matching::or an error occured : The expression passed [".$matchingSubExp."] to the operator has to be a valid boolean expression with single cardinality");
            } else {
                if ($matchingSubExp->isNull()){
                    $returnValue = null;
                    break;
                }else{
                    if ($returnValue === null){
                        $returnValue = $matchingSubExp->getValue();
                    } else {
                        $returnValue = $returnValue || $matchingSubExp->getValue();
                    }
                }
            }
        }
        
        // section 127-0-1-1-7e272ec4:12c307f74c9:-8000:0000000000002B9C end

        return (bool) $returnValue;
    }

    /**
     * The product operator takes 1 or more sub-expressions which
     * all have single cardinality and have numerical base-types.
     * The result is a single float or, if all sub-expressions are of
     * integer type, a single integer that corresponds to the 
     * product of the numerical values of the sub-expressions.
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  options
     */
    public function product(   $options)
    {
        $returnValue = null;

        // section 127-0-1-1-196de192:12c30421176:-8000:0000000000002B75 begin
        
        $returnValue = null;
        $options = $this->checkOptions ($options);
                
        // for each arguments (which are expressions) 
        for ($i = 1; $i < func_num_args(); ++$i) {
            $subExp = func_get_arg($i);
            
            $matchingSubExp = taoItems_models_classes_Matching_VariableFactory::toNumericBaseType ($subExp);
            // If the sub expression is not a numerical base type variable
            if ($matchingSubExp == null){
                throw new Exception ("TtaoItems_models_classes_Matching_Matching::product an error occured : The [".$i."] expression passed [".$subExp."] to the operator has to be a valid numerical expression with single cardinality");
            } 
            // If the sub expression value is null
            else if ($matchingSubExp->isNull()){
                $returnValue = null;
                break;
            } 
            // Else compute
            else {
                // first pass
                if ($returnValue==null){
                    $returnValue = $matchingSubExp->getValue();
                } 
                else {
                    $returnValue *= $matchingSubExp->getValue();
                }
            }
        }
        
        return $returnValue;
        
        // section 127-0-1-1-196de192:12c30421176:-8000:0000000000002B75 end

        return $returnValue;
    }

    /**
     * Selects a random integer from the specified range [min,max] 
     * satisfying min + step * n for some integer n. For example, 
     * with min=2, max=11 and step=3 the values {2,5,8,11} are possible.
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  options
     * @return int
     */
    public function randomInteger(   $options)
    {
        $returnValue = (int) 0;

        // section 127-0-1-1-4b0fb01e:12c5430948f:-8000:0000000000002BE2 begin
        
        $returnValue = rand ($options['min'], $options['max']);
        
        // section 127-0-1-1-4b0fb01e:12c5430948f:-8000:0000000000002BE2 end

        return (int) $returnValue;
    }

    /**
     * Selects a random float from the specified range [min,max].
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  options
     * @return double
     */
    public function randomFloat(   $options)
    {
        $returnValue = (float) 0.0;

        // section 127-0-1-1-4b0fb01e:12c5430948f:-8000:0000000000002BE5 begin
        
        $returnValue = $options['min']+lcg_value()*(abs($options['max']-$options['min']));
        
        // section 127-0-1-1-4b0fb01e:12c5430948f:-8000:0000000000002BE5 end

        return (float) $returnValue;
    }

    /**
     * The round operator takes a single sub-expression which
     * must have single cardinality and base-type float. The
     * result is a value of base-type integer formed by rounding
     * the value of the sub-expression. The result is the integer
     * n for all input values in the range [n-0.5,n+0.5). In other
     * words, 6.8 and 6.5 both round up to 7, 6.49 rounds down
     * to 6 and -6.5 rounds up to -6.
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  options
     * @param  expr
     */
    public function round(   $options,    $expr)
    {
        $returnValue = null;

        // section 127-0-1-1-7e272ec4:12c307f74c9:-8000:0000000000002B8F begin
        $options = $this->checkOptions($options);
        
        if ($expr == null) {
            $returnValue == null;
        }
        else{
            $matchingExpr = taoItems_models_classes_Matching_VariableFactory::toNumericBaseType ($expr);
            // IF the expression is not a numerical base type
            if ($matchingExpr == null) {
                throw new Exception ("TtaoItems_models_classes_Matching_Matching::round an error occured : The expression passed [".$expr."] to the operator has to be a valid numerical expression with single cardinality");
            }else{
                if ($matchingExpr->isNull()){
                    $returnValue = null;
                }else {
                    $precision = 0; 
                    if (isset($options['precision'])){
                        $precision = $options['precision'];
                    }
                    $returnValue = round ($matchingExpr->getValue(), $precision);
                }
            }
        }
        
        // section 127-0-1-1-7e272ec4:12c307f74c9:-8000:0000000000002B8F end

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
        if ($value instanceof taoItems_models_classes_Matching_Variable){
            $outcome->setValue ($value->getValue());
        }
        else {
            //if (taoItems_models_classes_Matching_BaseTypeVariable::isValidValue ($value)){
                $outcome->setValue ($value);
            //}else{
            //    throw new Exception ('taoItems_models_classes_Matching_Matching::setOutcomeValue error : unable to set a value of this type ['.gettype($value).']');
            //}
        }
        
        // section 127-0-1-1--58a488d5:12baaa39fdd:-8000:0000000000002927 end
    }

    /**
     * The subtract operator takes 2 sub-expressions which all have
     * single cardinality and numerical base-types. The result is a
     * single float or, if both sub-expressions are of integer type, a
     * single integer that corresponds to the first value minus the
     * second.
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  options
     * @param  expr1
     * @param  expr2
     */
    public function subtract(   $options,    $expr1,    $expr2)
    {
        $returnValue = null;

        // section 127-0-1-1-196de192:12c30421176:-8000:0000000000002B78 begin
        
        $matchingExpr1 = taoItems_models_classes_Matching_VariableFactory::toNumericBaseType ($expr1);
        // IF the first expression is not a numerical base type
        if ($matchingExpr1 == null) {
            throw new Exception ("TtaoItems_models_classes_Matching_Matching::substract an error occured : The first expression passed [".$expr1."] to the operator has to be a valid numerical expression with single cardinality");
        }

        $matchingExpr2 = taoItems_models_classes_Matching_VariableFactory::toNumericBaseType ($expr2);
        // IF the second expression is not a numerical base type
        if ($matchingExpr2 == null) {
            throw new Exception ("TtaoItems_models_classes_Matching_Matching::substract an error occured : The second expression passed [".$expr2."] to the operator has to be a valid numerical expression with single cardinality");
        }
        
        if ($matchingExpr1->isNull() || $matchingExpr2->isNull()){
            $returnValue = null;
        } else {
            $returnValue = $matchingExpr1->getValue() - $matchingExpr2->getValue();
        }
        
        // section 127-0-1-1-196de192:12c30421176:-8000:0000000000002B78 end

        return $returnValue;
    }

    /**
     * The sum operator takes 1 or more sub-expressions which all
     * have single cardinality and have numerical base-types. The
     * result is a single float or, if all sub-expressions are of integer
     * type, a single integer that corresponds to the sum of the
     * numerical values of the sub-expressions.
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  options
     */
    public function sum(   $options)
    {
        $returnValue = null;

        // section 127-0-1-1-196de192:12c30421176:-8000:0000000000002B87 begin
        
        $options = $this->checkOptions ($options);
                
        // for each arguments (which are expressions) 
        for ($i = 1; $i < func_num_args(); ++$i) {
            $subExp = func_get_arg($i);
            
            $matchingSubExp = taoItems_models_classes_Matching_VariableFactory::toNumericBaseType ($subExp);
            // If the sub expression is not a numerical base type variable
            if ($matchingSubExp == null){
                throw new Exception ("TtaoItems_models_classes_Matching_Matching::sum an error occured : The [".$i."] expression passed [".$subExp."] to the operator has to be a valid numerical expression with single cardinality");
            } 
            // If the sub expression value is null
            else if ($matchingSubExp->isNull()){
                $returnValue = null;
                break;
            } 
            // Else compute
            else {
                $returnValue += $matchingSubExp->getValue();
            }
        }
        
        // section 127-0-1-1-196de192:12c30421176:-8000:0000000000002B87 end

        return $returnValue;
    }

} /* end of class taoItems_models_classes_Matching_Matching */

?>