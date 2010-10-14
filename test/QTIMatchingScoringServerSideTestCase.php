<?php
require_once dirname(__FILE__) . '/../../tao/test/TestRunner.php';
require_once dirname(__FILE__) . '/../includes/common.php';
require_once dirname(__FILE__) . '/../models/classes/Matching/matching_api.php';

/**
 * @author Cédric Alfonsi, <taosupport@tudor.lu>
 * @package taoItems
 * @subpackage test
 */
class QTIOMatchingScoringServerSideTestCase extends UnitTestCase {
	
	protected $qtiService;
	
	/**
	 * tests initialization
	 */
//	public function setUp(){
//		global $correctsStr, $responsesStr, $outcomesStr;
//		
//		TestRunner::initTest();
//		$this->qtiService = tao_models_classes_ServiceFactory::get("taoItems_models_classes_QTI_Service");
//	
//		try{
//			matching_init ();
//			matching_setCorrects ($correctsStr);
//			matching_setResponses ($responsesStr);
//			matching_setOutcomes ($outcomesStr);
//			$rule = "if(match(getResponse('SNG_ID_1'), getCorrect('SNG_ID_1'))){ setOutcomeValue('SCORE', 1); } else { setOutcomeValue('SCORE', 0); }";
//			matching_setRule ($rule);
//			matching_evaluate ();
//			$outcomes = matching_getOutcomes ();
//			pr (json_encode($outcomes));
//			
//		}catch (Exception $e){ 
//			pr ($e->getMessage());
//		}
//	}
	
	public function testVariables () {
		
		$null = taoItems_models_classes_Matching_VariableFactory::create (null);
		
		// Integer
		$int1 = taoItems_models_classes_Matching_VariableFactory::create (1);
		$int2 = taoItems_models_classes_Matching_VariableFactory::create (2);
		$this->assertNotNull ($int1);
		$this->assertEqual ($int1->getType(), 'integer');
		$this->assertEqual ($int1->getValue(), 1);
		$this->assertTrue ($int1->match($int1));
		$this->assertFalse ($int1->match($int2));
		$this->assertTrue ($int1->equal($int1));
		$this->assertFalse ($int1->equal($int2));
		$this->assertFalse ($int1->equal($null));

		// Double
		$dbl1 = taoItems_models_classes_Matching_VariableFactory::create (3.14);
		$dbl2 = taoItems_models_classes_Matching_VariableFactory::create (3.0);
		$this->assertNotNull ($dbl1);
		$this->assertEqual ($dbl1->getType(), 'double');
		$this->assertEqual ($dbl2->getType(), 'double');
		$this->assertEqual ($dbl1->getValue(), 3.14);
		$this->assertTrue ($dbl1->match($dbl1));
		$this->assertFalse ($dbl1->match($dbl2));
		$this->assertTrue ($dbl1->equal($dbl1));
		$this->assertFalse ($dbl1->equal($dbl2));
		$this->assertFalse ($dbl1->equal($null));

		// Boolean
		$bool1 = taoItems_models_classes_Matching_VariableFactory::create (true);
		$bool2 = taoItems_models_classes_Matching_VariableFactory::create (false);
		$this->assertNotNull ($bool1);
		$this->assertEqual ($bool1->getType(), 'boolean');
		$this->assertEqual ($bool1->getValue(), true);
		$this->assertTrue ($bool1->match($bool1));
		$this->assertFalse ($bool1->match($bool2));
		$this->assertTrue ($bool1->equal($bool1));
		$this->assertFalse ($bool1->equal($bool2));
		$this->assertFalse ($bool1->equal($null));

		// String
		$str1 = taoItems_models_classes_Matching_VariableFactory::create ('TAO');
		$str2 = taoItems_models_classes_Matching_VariableFactory::create ('it\'s so powerfull');
		$this->assertNotNull ($str1);
		$this->assertEqual ($str1->getType(), 'string');
		$this->assertEqual ($str1->getValue(), 'TAO');
		$this->assertTrue ($str1->match($str1));
		$this->assertFalse ($str1->match($str2));
		$this->assertTrue ($str1->equal($str1));
		$this->assertFalse ($str1->equal($str2));
		$this->assertFalse ($str1->equal($null));
		
		// Tuple (in QTI Directed Pair) 
		$tpl1 = taoItems_models_classes_Matching_VariableFactory::create (json_decode('{"0":"TAO", "1":"it\'s so powerfull"}'));
		$tpl2 = taoItems_models_classes_Matching_VariableFactory::create (json_decode('{"0":"Yeahhh", "1":"it\'s true"}'));
		$tpl3 = taoItems_models_classes_Matching_VariableFactory::create (json_decode('{"0":"TAO"}'));
		$tpl4 = taoItems_models_classes_Matching_VariableFactory::create (json_decode('{"0":"TAO", "1":"it\'s so powerfull", "2":"yeah"}'));
		$this->assertNotNull ($tpl1);
		$this->assertEqual ($tpl1->getType(), 'tuple');
		$this->assertTrue ($tpl1->match($tpl1));
		$this->assertFalse ($tpl1->match($tpl2));
		$this->assertFalse ($tpl1->match($tpl3));
		$this->assertFalse ($tpl1->match($tpl4));
		
		// list
		$list1 = taoItems_models_classes_Matching_VariableFactory::create (json_decode('["TAO", "Test Assisté par Ordinateur"]'));
		$list2 = taoItems_models_classes_Matching_VariableFactory::create (json_decode('["CBA", "Computer Based Assessment"]'));
		$list3 = taoItems_models_classes_Matching_VariableFactory::create (json_decode('["CBA"]'));
		$list4 = taoItems_models_classes_Matching_VariableFactory::create (json_decode('["CBA", "Computer Based Assessment", "yeah"]'));
		$this->assertNotNull ($list1);
		$this->assertEqual ($list1->getType(), 'list');
		$this->assertTrue ($list1->match($list1), 'the lists don\'t match');
		$this->assertFalse ($list1->match($list2));
		$this->assertFalse ($list1->match($list3));
		$this->assertFalse ($list1->match($list4));
		
		// list of tuple (in QTI Multiple Directed Pair)
		$listTpl1 = taoItems_models_classes_Matching_VariableFactory::create (json_decode('[{"0":"A", "1":"B"}, {"0":"C", "1":"D"}, {"0":"E", "1":"F"}]'));
		$listTpl2 = taoItems_models_classes_Matching_VariableFactory::create (json_decode('[{"0":"B", "1":"A"}, {"0":"D", "1":"C"}, {"0":"F", "1":"E"}]'));
		$listTpl3 = taoItems_models_classes_Matching_VariableFactory::create (json_decode('[{"0":"A", "1":"B"}, {"0":"C", "1":"D"}]'));
		$listTpl4 = taoItems_models_classes_Matching_VariableFactory::create (json_decode('[{"0":"A", "1":"B"}, {"0":"C", "1":"D"}, {"0":"E", "1":"F"}, {"0":"G", "1":"H"}]'));
		$listTpl5 = taoItems_models_classes_Matching_VariableFactory::create (json_decode('[]'));
		$this->assertNotNull ($listTpl1);
		$this->assertEqual ($listTpl1->getType(), 'list');
		$this->assertTrue ($listTpl1->match($listTpl1));
		$this->assertFalse ($listTpl1->match($listTpl2));
		$this->assertFalse ($listTpl1->match($listTpl3));
		$this->assertFalse ($listTpl1->match($listTpl4));
		$this->assertFalse ($listTpl1->match($listTpl5));
		
		// list of list (in QTI Multiple Pair)
		$listList1 = taoItems_models_classes_Matching_VariableFactory::create (json_decode('[["A", "B"], ["C", "D"], ["E", "F"]]'));
		$listList2 = taoItems_models_classes_Matching_VariableFactory::create (json_decode('[["B", "A"], ["D", "C"], ["F", "E"]]'));
		$listList3 = taoItems_models_classes_Matching_VariableFactory::create (json_decode('[["A", "B"], ["C", "D"]]'));
		$listList4 = taoItems_models_classes_Matching_VariableFactory::create (json_decode('[["A", "B"], ["C", "D"], ["E", "F"], ["G", "H"]]'));
		$listList5 = taoItems_models_classes_Matching_VariableFactory::create (json_decode('[]'));
		$listList6 = taoItems_models_classes_Matching_VariableFactory::create (json_decode(''));
		$this->assertNotNull ($listList1);
		$this->assertEqual ($listList1->getType(), 'list');
		$this->assertTrue ($listList1->match($listList1));
		$this->assertTrue ($listList1->match($listList2));
		$this->assertFalse($listList1->match($listList3));
		$this->assertFalse ($listList1->match($listList4));
		$this->assertFalse ($listList1->match($listList5));
		//$this->assertFalse ($listList1->match($listList6)); // Unable to match a List with another type of variable
	}
	
//	public function testFactorySingleIdentifierVarCreation () {
//		global $taoMatching;
//		$this->sendMessage ("Factory : create single identifier variable");
//		$correctsStr = '[{"identifier":"RESPONSE", "value":"ID_1"}]';
//		$responsesStr = '[{"identifier":"RESPONSE", "value":"ID_1"}]';
//		$outcomesStr = '[{"identifier":"SCORE", "type":"integer"}]';
//		$rule = "if(match(getResponse('RESPONSE'), getCorrect('RESPONSE'))){ setOutcomeValue('SCORE', 1); } else { setOutcomeValue('SCORE', 0); }";
		
//		$taoMatching->getCorrect ("SNG_ID_1");
//		$mySngIdVar = unserializedQTIVariables ($mySngIdVarStr);
//		
//		$this->assertIsA ($mySngIdVar, 'QTIVariable', 'The created variable from the following serialized json is well a QTIVariable');
//		$this->assertEqual ($mySngIdVar->getBaseType(), 'identifier', 'The baseType of the QTIVariable is ok');
//		$this->assertNotEqual ($mySngIdVar->getCardinality(), 'multiple', 'The cardinality of the QTIVariable is ok');
//		$this->assertTrue (_match($mySngIdVar->values, array ("ID_1")), 'The values of the QTIVariable is ok');
//	}
	
	/*
	public function testFactorySingleIdentifierVarCreation () {
		$this->sendMessage ("Factory : create single identifier variable");
		$mySngIdVarStr = '{"identifier":"SNG_ID_1", "baseType":"identifier", "cardinality":"single", "values":["ID_1"]}';
		$mySngIdVar = unserializedQTIVariables ($mySngIdVarStr);
		
		$this->assertIsA ($mySngIdVar, 'QTIVariable', 'The created variable from the following serialized json is well a QTIVariable');
		$this->assertEqual ($mySngIdVar->getBaseType(), 'identifier', 'The baseType of the QTIVariable is ok');
		$this->assertNotEqual ($mySngIdVar->getCardinality(), 'multiple', 'The cardinality of the QTIVariable is ok');
		$this->assertTrue (_match($mySngIdVar->values, array ("ID_1")), 'The values of the QTIVariable is ok');
	}
	
	public function testFactoryMultipleIdentifierVarCreation () {
		$this->sendMessage ("Factory : create multiple identifier variable");
		$myMulIdVarStr = '{"identifier":"MUL_ID_1", "baseType":"identifier", "cardinality":"multiple", "values":["ID_1", "ID_3", "ID_2"]}';
		$myMulIdVar = unserializedQTIVariables ($myMulIdVarStr);
		
		$this->assertIsA ($myMulIdVar, 'QTIVariable', 'The created variable from the following serialized json is well a QTIVariable');
		$this->assertEqual ($myMulIdVar->getBaseType(), 'identifier', 'The baseType of the QTIVariable is ok');
		$this->assertEqual ($myMulIdVar->getCardinality(), 'multiple', 'The cardinality of the QTIVariable is ok');
		$this->assertTrue (_match($myMulIdVar->values, array ("ID_1", "ID_3", "ID_2")), 'The values of the QTIVariable is ok');
	}
	
	public function testSetVarsAndMatchThem () {
		global $rules;
	
		$qtiMatching = new QTIMatching ();
	
		// Set and get User Variables
		$mySngIdVarStr = '[{"identifier":"SNG_ID_1", "baseType":"identifier", "cardinality":"single", "values":["ID_1"]}]';
		$qtiMatching->setResponses (unserializedQTIVariables ($mySngIdVarStr));
		$mySngIdVar = $qtiMatching->getVariable ('SNG_ID_1');
		$this->assertNotNull ($mySngIdVar);
		
		// Set and get Correct Variables
		$qtiMatching->setCorrects (unserializedQTIVariables ($mySngIdVarStr));
		$mySngIdVar_2 = $qtiMatching->getCorrect ('SNG_ID_1');
		$this->assertNotNull ($mySngIdVar_2);
		
		// Set and get Rule
		$qtiMatching->setRule ($rules['match0_1']);
		$rule = $qtiMatching->getRule ();
		$this->assertNotNull ($rule);
		
		// Eval the rule
		$this->assertTrue ($qtiMatching->evalResponseProcessing());
	}
	
	public function testMatchSingleIdentifier () {
		global $rules;
		
		$this->qtiMatching->setRule ($rules['match0_1']);
		$this->assertTrue ($this->qtiMatching->evalResponseProcessing());
		$this->qtiMatching->setRule ($rules['match0_2']);
		$this->assertFalse ($this->qtiMatching->evalResponseProcessing());
		$this->qtiMatching->setRule ($rules['match0_3']);
		$this->assertFalse ($this->qtiMatching->evalResponseProcessing());
	}
	
	public function testMatchMultipleIdentifier () {
		global $rules;
		
		$this->qtiMatching->setRule ($rules['match1_1']);
		$this->assertTrue ($this->qtiMatching->evalResponseProcessing());
		$this->qtiMatching->setRule ($rules['match1_2']);
		$this->assertFalse ($this->qtiMatching->evalResponseProcessing());
		$this->qtiMatching->setRule ($rules['match1_3']);
		$this->assertFalse ($this->qtiMatching->evalResponseProcessing());
		$this->qtiMatching->setRule ($rules['match1_4']);
		$this->assertFalse ($this->qtiMatching->evalResponseProcessing());
	}
	
	public function testMatchSinglePair () {
		global $rules;
		
		$this->qtiMatching->setRule ($rules['match2_1']);
		$this->assertTrue ($this->qtiMatching->evalResponseProcessing());
		$this->qtiMatching->setRule ($rules['match2_2']);
		$this->assertFalse ($this->qtiMatching->evalResponseProcessing());
		$this->qtiMatching->setRule ($rules['match2_3']);
		$this->assertFalse ($this->qtiMatching->evalResponseProcessing());
		$this->qtiMatching->setRule ($rules['match2_4']);
		$this->assertTrue ($this->qtiMatching->evalResponseProcessing());
	}
	
	public function testMatchMultiplePair () {
		global $rules;
		
		$this->qtiMatching->setRule ($rules['match3_1']);
		$this->assertTrue ($this->qtiMatching->evalResponseProcessing());
		$this->qtiMatching->setRule ($rules['match3_2']);
		$this->assertFalse ($this->qtiMatching->evalResponseProcessing());
		$this->qtiMatching->setRule ($rules['match3_3']);
		$this->assertFalse ($this->qtiMatching->evalResponseProcessing());
		$this->qtiMatching->setRule ($rules['match3_4']);
		$this->assertTrue ($this->qtiMatching->evalResponseProcessing());
	}
	
	public function testMatchMultipleDirectedair () {
		global $rules;
		
		$this->qtiMatching->setRule ($rules['match6_1']);
		$this->assertFalse ($this->qtiMatching->evalResponseProcessing());
		$this->qtiMatching->setRule ($rules['match6_2']);
		$this->assertTrue ($this->qtiMatching->evalResponseProcessing());
	}
	
	public function testMatchMultipleBoolean () {
		global $rules;
		
		$this->qtiMatching->setRule ($rules['match7_1']);
		$this->assertTrue ($this->qtiMatching->evalResponseProcessing());
	}
	
	public function testMatchMultipleInteger () {
		global $rules;
		
		$this->qtiMatching->setRule ($rules['match8_1']);
		$this->assertTrue ($this->qtiMatching->evalResponseProcessing());
	}
	
	public function testMatchMultipleFloat () {
		global $rules;
		
		$this->qtiMatching->setRule ($rules['match9_1']);
		$this->assertTrue ($this->qtiMatching->evalResponseProcessing());
	}
	
	public function testMatchMultipleString () {
		global $rules;
		 public function match( tao_actions_form_List $list)
		$this->qtiMatching->setRule ($rules['match10_1']);
		$this->assertTrue ($this->qtiMatching->evalResponseProcessing());
	}
	
	public function testMatchBaseTypeDiff () {
		global $rules;
		
		$this->qtiMatching->setRule ("match(getVariable('MUL_STR_1'), getVariable('MUL_FLOAT_1'))");
		$this->assertFalse ($this->qtiMatching->evalResponseProcessing());
		$this->qtiMatching->setRule ("match(getVariable('MUL_FLOAT_1'), getVariable('MUL_INT_1'))");
		$this->assertFalse ($this->qtiMatching->evalResponseProcessing());
		$this->qtiMatching->setRule ("match(getVariable('MUL_BOOL_1'), getVariable('MUL_INT_1'))");
		$this->assertFalse ($this->qtiMatching->evalResponseProcessing());
		$this->qtiMatching->setRule ("match(getVariable('MUL_DPAIR_1'), getVariable('MUL_BOOL_1'))");
		$this->assertError ($this->qtiMatching->evalResponseProcessing());
	}
	
	public function testMapMultiplePair () {
		global $rules;
		
		$this->qtiMatching->setRule ($rules['map4_1']);
		$this->assertEqual ($this->qtiMatching->evalResponseProcessing(), 1.6);
		$this->qtiMatching->setRule ($rules['map4_2']);
		$this->assertEqual ($this->qtiMatching->evalResponseProcessing(), 1.6);
		$this->qtiMatching->setRule ($rules['map4_3']);
		$this->assertEqual ($this->qtiMatching->evalResponseProcessing(), 0);
		$this->qtiMatching->setRule ($rules['map4_4']);
		$this->assertEqual ($this->qtiMatching->evalResponseProcessing(), 1.5);
		$this->qtiMatching->setRule ($rules['map4_5']);
		$this->assertEqual ($this->qtiMatching->evalResponseProcessing(), 1.6);
	}
	
	public function testSetOutcomeOperator () {
		global $rules;
		
		$this->qtiMatching->setRule ($rules['setoutcome5_1']);
		$this->qtiMatching->evalResponseProcessing();
		$score = $this->qtiMatching->getOutcome ("SCORE5_1");
		$this->assertEqual ($score->values[0], 2);
		
		$this->qtiMatching->setRule ($rules['setoutcome5_2']);
		$this->qtiMatching->evalResponseProcessing();
		$score = $this->qtiMatching->getOutcome ("SCORE5_2");
		$this->assertEqual ($score->values[0], 1.6);
	}
	
	public function testAndOperator () {
		global $rules;
		
		$this->qtiMatching->setRule ($rules['and11_1']);
		$this->assertTrue ($this->qtiMatching->evalResponseProcessing());
		$this->qtiMatching->setRule ($rules['and11_2']);
		$this->assertTrue ($this->qtiMatching->evalResponseProcessing());
		$this->qtiMatching->setRule ($rules['and11_3']);
		$this->assertFalse ($this->qtiMatching->evalResponseProcessing());
		$this->qtiMatching->setRule ($rules['and11_4']);
		$this->assertTrue ($this->qtiMatching->evalResponseProcessing());
	}
	
	public function testEqualOperator () {
		global $rules;
		
		$this->qtiMatching->setRule ($rules['equal12_1']);
		$this->assertTrue ($this->qtiMatching->evalResponseProcessing());
		$this->qtiMatching->setRule ($rules['equal12_2']);
		$this->assertFalse ($this->qtiMatching->evalResponseProcessing());
		$this->qtiMatching->setRule ($rules['equal12_3']);
		$this->assertTrue ($this->qtiMatching->evalResponseProcessing());
		$this->qtiMatching->setRule ($rules['equal12_4']);
		$this->assertFalse ($this->qtiMatching->evalResponseProcessing());
		$this->qtiMatching->setRule ($rules['equal12_5']);
		$this->assertTrue ($this->qtiMatching->evalResponseProcessing());
		$this->qtiMatching->setRule ($rules['equal12_6']);
		$this->assertFalse ($this->qtiMatching->evalResponseProcessing());
		$this->qtiMatching->setRule ($rules['equal12_7']);
		$this->assertTrue ($this->qtiMatching->evalResponseProcessing());
		$this->qtiMatching->setRule ($rules['equal12_8']);
		$this->assertFalse ($this->qtiMatching->evalResponseProcessing());
	}*/
	
	public function testIsNullOperator () {
		global $rules;
	}

}

$outcomesStr ='[ 
		{"identifier":"SCORE", "type":"double"} 
		, {"identifier":"SCORE5_1", "type":"double"} 
		, {"identifier":"SCORE5_2", "type":"double"} 
	]';

$mapsStr = '[{"identifier":"MUL_PAIR_1", "baseType":"pair", "cardinality":"multiple", "value":[[["A", "B"], 1], [["C", "D"], 0.5], [["E", "F"], 0.1]]}]';

$correctsStr = '[
			{"identifier":"SNG_ID_1", "value":"ID_1"} 
			, {"identifier":"MUL_ID_1", "value":["ID_1", "ID_2", "ID_3"]} 
			, {"identifier":"MUL_ID_2", "value":["ID_1", "ID_2", "ID_3"]} 
			, {"identifier":"MUL_ID_3", "value":["ID_1", "ID_2", "ID_3"]} 
			, {"identifier":"MUL_ID_4", "value":["ID_1", "ID_2"]} 
			, {"identifier":"RESPONSE1_5", "value":[["A", "B"], ["C", "D"]]} 
			, {"identifier":"SNG_PAIR_1", "value":["A", "B"]} 
			, {"identifier":"MUL_PAIR_1", "value":[["A", "B"], ["C", "D"], ["E", "F"]]} 
			, {"identifier":"MUL_DPAIR_1", "value":[["A", "B"], ["C", "D"], ["E", "F"]]} 
			, {"identifier":"MUL_BOOL_1", "value":[true, false, true, false]} 
			, {"identifier":"MUL_INT_1", "value":[1, 2, 3, 4]} 
			, {"identifier":"MUL_FLOAT_1", "value":[1.1, 2.2, 3.3, 4.4]} 
			, {"identifier":"MUL_STR_1", "value":["abc", "def", "ghi", "klm"]} 
		]';

$responsesStr = '[ 
			  {"identifier":"SNG_ID_1", "value":"ID_1"} 
			, {"identifier":"SNG_ID_2", "value":"ID_0"} 
			, {"identifier":"SNG_ID_3", "value":null} 
			, {"identifier":"MUL_ID_1", "value":["ID_1", "ID_3", "ID_2"]} 
			, {"identifier":"MUL_ID_2", "value":["ID_0", "ID_1", "ID_2"]} 
			, {"identifier":"MUL_ID_3", "value":["ID_1", "ID_2"]} 
			, {"identifier":"MUL_ID_4", "value":["ID_1", "ID_2", "ID_3"]} 
			, {"identifier":"SNG_PAIR_1", "value":["A", "B"]} 
			, {"identifier":"SNG_PAIR_2", "value":["A", "C"]} 
			, {"identifier":"SNG_PAIR_3", "value":null}
			, {"identifier":"SNG_PAIR_4", "value":[["B", "A"]]} 
			, {"identifier":"MUL_PAIR_1", "value":[["A", "B"], ["C", "D"], ["E", "F"]]} 
			, {"identifier":"MUL_PAIR_2", "value":[["A", "B"], ["Z", "D"], ["E", "F"]]} 
			, {"identifier":"MUL_PAIR_3", "value":[["A", "B"], ["Z", "D"]]} 
			, {"identifier":"MUL_PAIR_4", "value":[["B", "A"], ["D", "C"], ["F", "E"]]} 
			, {"identifier":"MUL_PAIR_5", "value":[["A", "B"], ["C", "D"], ["E", "F"], ["A", "B"], ["C", "D"], ["E", "F"]]} 
			, {"identifier":"MUL_PAIR_6", "value":null} 
			, {"identifier":"MUL_PAIR_7", "value":[["A", "B"], ["C", "D"]]} 
			, {"identifier":"MUL_DPAIR_1", "value":[{"0":"A", "1":"B"}, {"0":"D", "1":"C"}, {"0":"E", "1":"F"}]} 
			, {"identifier":"MUL_DPAIR_2", "value":[{"0":"A", "1":"B"}, {"0":"C", "1":"D"}, {"0":"E", "1":"F"}]} 
			, {"identifier":"MUL_BOOL_1", "value":[true, false, true, false]} 
			, {"identifier":"MUL_INT_1", "value":[1, 2, 3, 4]} 
			, {"identifier":"MUL_FLOAT_1", "value":[1.1, 2.2, 3.3, 4.4]} 
			, {"identifier":"MUL_STR_1", "value":["abc", "def", "ghi", "klm"]} 
			, {"identifier":"SNG_BOOL_1", "value":true} 
			, {"identifier":"SNG_INT_1", "value":2} 
			, {"identifier":"SNG_INT_2", "value":3} 
			, {"identifier":"SNG_FLOAT_1", "value":2.2} 
			, {"identifier":"SNG_FLOAT_2", "value":3.3} 
]';

$rules = array ();
$rules['match0_1'] = "match(getVariable('SNG_ID_1'), getCorrect('SNG_ID_1'))";
$rules['match0_2'] = "match(getVariable('SNG_ID_2'), getCorrect('SNG_ID_1'))";
$rules['match0_3'] = "match(getVariable('SNG_ID_3'), getCorrect('SNG_ID_1'))";

$rules['match1_1'] = "match(getVariable('MUL_ID_1'), getCorrect('MUL_ID_1'))";
$rules['match1_2'] = "match(getVariable('MUL_ID_2'), getCorrect('MUL_ID_2'))";
$rules['match1_3'] = "match(getVariable('MUL_ID_3'), getCorrect('MUL_ID_3'))";
$rules['match1_4'] = "match(getVariable('MUL_ID_4'), getCorrect('MUL_ID_4'))";

$rules['match2_1'] = "match(getVariable('SNG_PAIR_1'), getCorrect('SNG_PAIR_1'))";
$rules['match2_2'] = "match(getVariable('SNG_PAIR_2'), getCorrect('SNG_PAIR_1'))";
$rules['match2_3'] = "match(getVariable('SNG_PAIR_3'), getCorrect('SNG_PAIR_1'))";
$rules['match2_4'] = "match(getVariable('SNG_PAIR_4'), getCorrect('SNG_PAIR_1'))";

$rules['match3_1'] = "match(getVariable('MUL_PAIR_1'), getCorrect('MUL_PAIR_1'))";
$rules['match3_2'] = "match(getVariable('MUL_PAIR_2'), getCorrect('MUL_PAIR_1'))";
$rules['match3_3'] = "match(getVariable('MUL_PAIR_3'), getCorrect('MUL_PAIR_1'))";
$rules['match3_4'] = "match(getVariable('MUL_PAIR_4'), getCorrect('MUL_PAIR_1'))";

$rules['map4_1'] = "mapResponse(getVariable('MUL_PAIR_1'), getMap('MUL_PAIR_1'))";
$rules['map4_2'] = "mapResponse(getVariable('MUL_PAIR_5'), getMap('MUL_PAIR_1'))";
$rules['map4_3'] = "mapResponse(getVariable('MUL_PAIR_6'), getMap('MUL_PAIR_1'))";
$rules['map4_4'] = "mapResponse(getVariable('MUL_PAIR_7'), getMap('MUL_PAIR_1'))";
$rules['map4_5'] = "mapResponse(getVariable('MUL_PAIR_4'), getMap('MUL_PAIR_1'))";

$rules['setoutcome5_1'] = "setOutcomeValue('SCORE5_1', array(2))";
$rules['setoutcome5_2'] = "setOutcomeValue('SCORE5_2', array(".$rules['map4_5']."))";

$rules['match6_1'] = "match(getVariable('MUL_DPAIR_1'), getCorrect('MUL_DPAIR_1'))";
$rules['match6_2'] = "match(getVariable('MUL_DPAIR_2'), getCorrect('MUL_DPAIR_1'))";

$rules['match7_1'] = "match(getVariable('MUL_BOOL_1'), getCorrect('MUL_BOOL_1'))";

$rules['match8_1'] = "match(getVariable('MUL_INT_1'), getCorrect('MUL_INT_1'))";

$rules['match9_1'] = "match(getVariable('MUL_FLOAT_1'), getCorrect('MUL_FLOAT_1'))";

$rules['match10_1'] = "match(getVariable('MUL_STR_1'), getCorrect('MUL_STR_1'))";

$rules['and11_1'] = "and(true, true)";
$rules['and11_2'] = "and(".$rules['match2_4'].", ".$rules['match6_2'].")";
$rules['and11_3'] = "and(".$rules['match6_1'].", ".$rules['match6_2'].")";
$rules['and11_4'] = "and(getVariable('SNG_BOOL_1'))";

$rules['equal12_1'] = "equal(getVariable('SNG_INT_1'), getVariable('SNG_INT_1'))";
$rules['equal12_2'] = "equal(getVariable('SNG_INT_1'), getVariable('SNG_INT_2'))";
$rules['equal12_3'] = "equal(2, 2)";
$rules['equal12_4'] = "equal(2, 3)";
$rules['equal12_5'] = "equal(getVariable('SNG_FLOAT_1'), getVariable('SNG_FLOAT_1'))";
$rules['equal12_6'] = "equal(getVariable('SNG_FLOAT_1'), getVariable('SNG_FLOAT_2'))";
$rules['equal12_7'] = "equal(2.2, 2.2)";
$rules['equal12_8'] = "equal(2.2, 3.3)";
?>