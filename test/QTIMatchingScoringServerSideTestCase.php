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
	public function setUp(){		
		TestRunner::initTest();
		$this->qtiService = tao_models_classes_ServiceFactory::get("taoItems_models_classes_QTI_Service");
	}
	
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
		$tmpValue = $tpl1->getValue();
		$this->assertEqual ($tmpValue[0]->getValue(), 'TAO');
		$this->assertEqual ($tmpValue[1]->getValue(), 'it\'s so powerfull');
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
		$tmpValue = $list1->getValue();
		$this->assertEqual ($tmpValue[0]->getValue(), 'TAO');
		$this->assertEqual ($tmpValue[1]->getValue(), 'Test Assisté par Ordinateur');
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
		$listTpl6 = taoItems_models_classes_Matching_VariableFactory::create (json_decode('[{"0":"A", "1":"B"}, {"0":"C", "1":"D"}, {"0":"E", "1":"F"}, {"0":"A", "1":"B"}, {"0":"C", "1":"D"}, {"0":"E", "1":"F"}]'));
		$this->assertNotNull ($listTpl1);
		$this->assertEqual ($listTpl1->getType(), 'list');
		$tmpValue = $listTpl1->getValue();
		$tmpValue2 = $tmpValue[0]->getValue();
		$this->assertEqual ($tmpValue2[0]->getValue(), 'A');
		$this->assertEqual ($tmpValue2[1]->getValue(), 'B');
		$this->assertTrue ($listTpl1->match($listTpl1));
		$this->assertFalse ($listTpl1->match($listTpl2));
		$this->assertFalse ($listTpl1->match($listTpl3));
		$this->assertFalse ($listTpl1->match($listTpl4));
		$this->assertFalse ($listTpl1->match($listTpl5));
		$this->assertFalse ($listTpl1->match($listTpl6));
		
		// list of list (in QTI Multiple Pair)
		$listList1 = taoItems_models_classes_Matching_VariableFactory::create (json_decode('[["A", "B"], ["C", "D"], ["E", "F"]]'));
		$listList2 = taoItems_models_classes_Matching_VariableFactory::create (json_decode('[["B", "A"], ["D", "C"], ["F", "E"]]'));
		$listList3 = taoItems_models_classes_Matching_VariableFactory::create (json_decode('[["A", "B"], ["C", "D"]]'));
		$listList4 = taoItems_models_classes_Matching_VariableFactory::create (json_decode('[["A", "B"], ["C", "D"], ["E", "F"], ["G", "H"]]'));
		$listList5 = taoItems_models_classes_Matching_VariableFactory::create (json_decode('[]'));
		$listList6 = taoItems_models_classes_Matching_VariableFactory::create (json_decode(''));
		$listList7 = taoItems_models_classes_Matching_VariableFactory::create (json_decode('[["A", "B"], ["C", "D"], ["E", "F"], ["A", "B"], ["C", "D"], ["E", "F"]]'));
		$this->assertNotNull ($listList1);
		$this->assertEqual ($listList1->getType(), 'list');
		$tmpValue = $listList1->getValue();
		$tmpValue2 = $tmpValue[0]->getValue();
		$this->assertEqual ($tmpValue2[0]->getValue(), 'A');
		$this->assertEqual ($tmpValue2[1]->getValue(), 'B');		
		$this->assertTrue ($listList1->match($listList1));
		$this->assertTrue ($listList1->match($listList2));
		$this->assertFalse($listList1->match($listList3));
		$this->assertFalse ($listList1->match($listList4));
		$this->assertFalse ($listList1->match($listList5));
		//$this->assertFalse ($listList1->match($listList6)); // Unable to match a List with another type of variable. The Matching engine will make the control
		$this->assertFalse ($listList1->match($listList7));
		
		// Map List
		$map1 = new taoItems_models_classes_Matching_Map (json_decode('[{"key":["A", "B"], "value":1}, {"key":["C", "D"], "value":0.5}, {"key":["E", "F"], "value":0.2}]'));
		$this->assertEqual ($map1->map ($listList1), 1.7);
		$this->assertEqual ($map1->map ($listList2), 1.7);
		$this->assertEqual ($map1->map ($listList3), 1.5);
		$this->assertEqual ($map1->map ($listList4), 1.7);
		$this->assertEqual ($map1->map ($listList5), 0.0);
		$this->assertEqual ($map1->map ($listList7), 1.7);
		
		// Map Tuple
		$map2 = new taoItems_models_classes_Matching_Map (json_decode('[{"key":{"0":"A", "1":"B"}, "value":1}, {"key":{"0":"C", "1":"D"}, "value":0.5}, {"key":{"0":"E", "1":"F"}, "value":0.2}]'));
		$this->assertEqual ($map2->map ($listTpl1), 1.7);
		$this->assertEqual ($map2->map ($listTpl2), 0.0);
		$this->assertEqual ($map2->map ($listTpl3), 1.5);
		$this->assertEqual ($map2->map ($listTpl4), 1.7);
		$this->assertEqual ($map2->map ($listTpl5), 0.0);
		$this->assertEqual ($map2->map ($listTpl6), 1.7);
		
		// Map List BasicType
		$listStr1 = taoItems_models_classes_Matching_VariableFactory::create (json_decode('["TAO", "Test assisté par ordinateur", "CBA in english"]'));
		$listStr2 = taoItems_models_classes_Matching_VariableFactory::create (json_decode('["TAO", "Test assisté par ordinateur"]'));
		$map3 = new taoItems_models_classes_Matching_Map (json_decode('[{"key":"TAO", "value":1}, {"key":"Test assisté par ordinateur", "value":0.5}, {"key":"CBA in english", "value":0.2}]'));
		$this->assertEqual ($map3->map ($listStr1), 1.7);
		$this->assertEqual ($map3->map ($listStr2), 1.5);
	}
	
	public function testTemplateResponseProcessingMatchCorrect (){
		matching_init ();
		matching_setRule ("if(match(getResponse('RESPONSE'), getCorrect('RESPONSE'))) setOutcomeValue('SCORE', 1); else setOutcomeValue('SCORE', 0);");
		matching_setCorrects (json_decode('[{"identifier":"RESPONSE", "value":"1"}]'));
		matching_setResponses (json_decode('[{"identifier":"RESPONSE", "value":"1"}]'));
		matching_setOutcomes (json_decode('[{"identifier":"SCORE", "type":"double"}]'));
		matching_evaluate ();
		$outcomes = matching_getOutcomes ();
		$this->assertEqual ($outcomes["SCORE"]["value"], 1);
		
		matching_init ();
		matching_setRule ("if(match(getResponse('RESPONSE'), getCorrect('RESPONSE'))) setOutcomeValue('SCORE', 1); else setOutcomeValue('SCORE', 0);");
		matching_setCorrects (json_decode('[{"identifier":"RESPONSE", "value":[{"0":"A", "1":"B"}, {"0":"C", "1":"D"}, {"0":"E", "1":"F"}]}]'));
		matching_setResponses (json_decode('[{"identifier":"RESPONSE", "value":[{"0":"A", "1":"B"}, {"0":"C", "1":"D"}, {"0":"E", "1":"F"}]}]'));
		matching_setOutcomes (json_decode('[{"identifier":"SCORE", "type":"double"}]'));
		matching_evaluate ();
		$outcomes = matching_getOutcomes ();
		$this->assertEqual ($outcomes["SCORE"]["value"], 1);
	}

	public function testTemplateResponseProcessingMapResponse (){
		// string
		matching_init ();
		matching_setRule ("if (isNull(getResponse('RESPONSE'))) { setOutcomeValue('SCORE', 0); } else { setOutcomeValue('SCORE', mapResponse(getMap('RESPONSE'), getResponse('RESPONSE'))); }");
		matching_setCorrects (json_decode('[{"identifier":"RESPONSE", "value":"Paris"}]'));
		matching_setResponses (json_decode('[{"identifier":"RESPONSE", "value":"paris"}]'));
		matching_setMaps (json_decode('[{"identifier":"RESPONSE", "value":[{"key":"Paris", "value":1}, {"key":"paris", "value":0.9}]}]'));
		matching_setOutcomes (json_decode('[{"identifier":"SCORE", "type":"double"}]'));
		matching_evaluate ();
		$outcomes = matching_getOutcomes ();
		$this->assertEqual ($outcomes["SCORE"]["value"], 0.9);
		
		// list list (multiple pair)
		matching_init ();
		matching_setRule ("if (isNull(getResponse('RESPONSE'))) { setOutcomeValue('SCORE', 0); } else { setOutcomeValue('SCORE', mapResponse(getMap('RESPONSE'), getResponse('RESPONSE'))); }");
		matching_setCorrects (json_decode('[{"identifier":"RESPONSE", "value":[["A", "B"], ["C", "D"], ["E", "F"]]}]'));
		matching_setResponses (json_decode('[{"identifier":"RESPONSE", "value":[["A", "B"], ["C", "D"], ["E", "F"]]}]'));
		matching_setMaps (json_decode('[{"identifier":"RESPONSE", "value":[{"key":["A", "B"], "value":1}, {"key":["C", "D"], "value":0.5}, {"key":["E", "F"], "value":0.2}]}]'));
		matching_setOutcomes (json_decode('[{"identifier":"SCORE", "type":"double"}]'));
		matching_evaluate ();
		$outcomes = matching_getOutcomes ();
		$this->assertEqual ($outcomes["SCORE"]["value"], 1.7);
		
		// list list (multiple pair but with reversed result)
		matching_init ();
		matching_setRule ("if (isNull(getResponse('RESPONSE'))) { setOutcomeValue('SCORE', 0); } else { setOutcomeValue('SCORE', mapResponse(getMap('RESPONSE'), getResponse('RESPONSE'))); }");
		matching_setCorrects (json_decode('[{"identifier":"RESPONSE", "value":[["A", "B"], ["C", "D"], ["E", "F"]]}]'));
		matching_setResponses (json_decode('[{"identifier":"RESPONSE", "value":[["B", "A"], ["D", "C"], ["F", "E"]]}]'));
		matching_setMaps (json_decode('[{"identifier":"RESPONSE", "value":[{"key":["A", "B"], "value":1}, {"key":["C", "D"], "value":0.5}, {"key":["E", "F"], "value":0.2}]}]'));
		matching_setOutcomes (json_decode('[{"identifier":"SCORE", "type":"double"}]'));
		matching_evaluate ();
		$outcomes = matching_getOutcomes ();
		$this->assertEqual ($outcomes["SCORE"]["value"], 1.7);
		
		// list tuple (multiple directedpair)
		matching_init ();
		matching_setRule ("if (isNull(getResponse('RESPONSE'))) { setOutcomeValue('SCORE', 0); } else { setOutcomeValue('SCORE', mapResponse(getMap('RESPONSE'), getResponse('RESPONSE'))); }");
		matching_setCorrects (json_decode('[{"identifier":"RESPONSE", "value":[{"0":"A", "1":"B"}, {"0":"C", "1":"D"}, {"0":"E", "1":"F"}]}]'));
		matching_setResponses (json_decode('[{"identifier":"RESPONSE", "value":[{"0":"A", "1":"B"}, {"0":"C", "1":"D"}, {"0":"E", "1":"F"}]}]'));
		matching_setMaps (json_decode('[{"identifier":"RESPONSE", "value":[{"key":{"0":"A", "1":"B"}, "value":1}, {"key":{"0":"C", "1":"D"}, "value":0.5}, {"key":{"0":"E", "1":"F"}, "value":0.2}]}]'));
		matching_setOutcomes (json_decode('[{"identifier":"SCORE", "type":"double"}]'));
		matching_evaluate ();
		$outcomes = matching_getOutcomes ();
		$this->assertEqual ($outcomes["SCORE"]["value"], 1.7);

		// list tuple (multiple directedpair reversed pair)
		matching_init ();
		matching_setRule ("if (isNull(getResponse('RESPONSE'))) { setOutcomeValue('SCORE', 0); } else { setOutcomeValue('SCORE', mapResponse(getMap('RESPONSE'), getResponse('RESPONSE'))); }");
		matching_setCorrects (json_decode('[{"identifier":"RESPONSE", "value":[{"0":"A", "1":"B"}, {"0":"C", "1":"D"}, {"0":"E", "1":"F"}]}]'));
		matching_setResponses (json_decode('[{"identifier":"RESPONSE", "value":[{"0":"B", "1":"A"}, {"0":"D", "1":"C"}, {"0":"F", "1":"E"}]}]'));
		matching_setMaps (json_decode('[{"identifier":"RESPONSE", "value":[{"key":{"0":"A", "1":"B"}, "value":1}, {"key":{"0":"C", "1":"D"}, "value":0.5}, {"key":{"0":"E", "1":"F"}, "value":0.2}]}]'));
		matching_setOutcomes (json_decode('[{"identifier":"SCORE", "type":"double"}]'));
		matching_evaluate ();
		$outcomes = matching_getOutcomes ();
		$this->assertEqual ($outcomes["SCORE"]["value"], 0);
	}

    public function testAndOperator () {
        matching_init ();
        matching_setRule ('if (and(true, true)){ setOutcomeValue("SCORE", 1); } else { setOutcomeValue("SCORE", 0); }');
        matching_setOutcomes (json_decode('[{"identifier":"SCORE", "type":"double"}]'));
        matching_evaluate ();
        $outcomes = matching_getOutcomes ();
        $this->assertEqual ($outcomes["SCORE"]["value"], 1);
        
        matching_init ();
        matching_setRule ('if ( and (match(getResponse("RESPONSE"), getCorrect("RESPONSE")))){ setOutcomeValue("SCORE", 1); } else { setOutcomeValue("SCORE", 0); }');
        matching_setCorrects (json_decode('[{"identifier":"RESPONSE", "value":[{"0":"A", "1":"B"}, {"0":"C", "1":"D"}, {"0":"E", "1":"F"}]}]'));
        matching_setResponses (json_decode('[{"identifier":"RESPONSE", "value":[{"0":"A", "1":"B"}, {"0":"C", "1":"D"}, {"0":"E", "1":"F"}]}]'));
        matching_setOutcomes (json_decode('[{"identifier":"SCORE", "type":"double"}]'));
        matching_evaluate ();
        $outcomes = matching_getOutcomes ();
        $this->assertEqual ($outcomes["SCORE"]["value"], 1);
        
        matching_init ();
        matching_setRule ('if ( and (true, match(getResponse("RESPONSE"), getCorrect("RESPONSE")))){ setOutcomeValue("SCORE", 1); } else { setOutcomeValue("SCORE", 0); }');
        matching_setCorrects (json_decode('[{"identifier":"RESPONSE", "value":[{"0":"A", "1":"B"}, {"0":"C", "1":"D"}, {"0":"E", "1":"F"}]}]'));
        matching_setResponses (json_decode('[{"identifier":"RESPONSE", "value":[{"0":"A", "1":"B"}, {"0":"C", "1":"D"}, {"0":"E", "1":"F"}]}]'));
        matching_setOutcomes (json_decode('[{"identifier":"SCORE", "type":"double"}]'));
        matching_evaluate ();
        $outcomes = matching_getOutcomes ();
        $this->assertEqual ($outcomes["SCORE"]["value"], 1);
    }

    public function testEqualOperator () {
        matching_init ();
        matching_setRule ('if (equal(true, true)){ setOutcomeValue("SCORE", 1); } else { setOutcomeValue("SCORE", 0); }');
        matching_setOutcomes (json_decode('[{"identifier":"SCORE", "type":"double"}]'));
        matching_evaluate ();
        $outcomes = matching_getOutcomes ();
        $this->assertEqual ($outcomes["SCORE"]["value"], 1);
        
        /*matching_init ();
        matching_setRule ('if ( equal (getResponse("RESPONSE"), getCorrect("RESPONSE"))){ setOutcomeValue("SCORE", 1); } else { setOutcomeValue("SCORE", 0); }');
        matching_setCorrects (json_decode('[{"identifier":"RESPONSE", "value":true'));
        matching_setResponses (json_decode('[{"identifier":"RESPONSE", "value":true'));
        //matching_setMaps (json_decode('[{"identifier":"RESPONSE", "value":[{"key":{"0":"A", "1":"B"}, "value":1}, {"key":{"0":"C", "1":"D"}, "value":0.5}, {"key":{"0":"E", "1":"F"}, "value":0.2}]}]'));
        matching_setOutcomes (json_decode('[{"identifier":"SCORE", "type":"double"}]'));
        matching_evaluate ();
        $outcomes = matching_getOutcomes ();
        $this->assertEqual ($outcomes["SCORE"]["value"], 1);*/
    }

    /*public function testInitMatchingEngine () {
        try {
            global $correctsStr, $responsesStr, $outcomesStr, $mapsStr, $rules;
            
            matching_init ();
            matching_setCorrects ($correctsStr);
            matching_setMaps ($mapsStr);
            matching_setResponses ($responsesStr);
            matching_setOutcomes ($outcomesStr);
            
            foreach ($rules as $rule){
                matching_setRule ($rule);
                matching_evaluate ();
            }

            $outcomes = matching_getOutcomes ();
            
        }catch (Exception $e){ 
            pr ($e->getMessage());
        }
    }*/

}

/*
$outcomesStr ='[ 
		{"identifier":"SCORE", "type":"double"} 
		, {"identifier":"SCORE5_1", "type":"double"} 
		, {"identifier":"SCORE5_2", "type":"double"} 
	]';

$mapsStr = '[{"identifier":"MUL_PAIR_1", "value":[{"key":["A", "B"], "value":1}, {"key":["C", "D"], "value":0.5}, {"key":["E", "F"], "value":0.2}]}]';

$correctsStr = '[
			{"identifier":"SNG_ID_1", "value":"ID_1"} 
			, {"identifier":"MUL_ID_1", "value":["ID_1", "ID_2", "ID_3"]} 
			, {"identifier":"MUL_ID_2", "value":["ID_1", "ID_2", "ID_3"]} 
			, {"identifier":"MUL_ID_3", "value":["ID_1", "ID_2", "ID_3"]} 
			, {"identifier":"MUL_ID_4", "value":["ID_1", "ID_2"]} 
			, {"identifier":"RESPONSE1_5", "value":[["A", "B"], ["C", "D"]]} 
			, {"identifier":"SNG_PAIR_1", "value":["A", "B"]} 
			, {"identifier":"MUL_PAIR_1", "value":[["A", "B"], ["C", "D"], ["E", "F"]]} 
			, {"identifier":"MUL_DPAIR_1", "value":[{"0":"A", "1":"B"}, {"0":"D", "1":"C"}, {"0":"E", "1":"F"}]} 
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
$rules['match0_1'] = "match(getResponse('SNG_ID_1'), getCorrect('SNG_ID_1'))";
$rules['match0_2'] = "match(getResponse('SNG_ID_2'), getCorrect('SNG_ID_1'))";
$rules['match0_3'] = "match(getResponse('SNG_ID_3'), getCorrect('SNG_ID_1'))";

$rules['match1_1'] = "match(getResponse('MUL_ID_1'), getCorrect('MUL_ID_1'))";
$rules['match1_2'] = "match(getResponse('MUL_ID_2'), getCorrect('MUL_ID_2'))";
$rules['match1_3'] = "match(getResponse('MUL_ID_3'), getCorrect('MUL_ID_3'))";
$rules['match1_4'] = "match(getResponse('MUL_ID_4'), getCorrect('MUL_ID_4'))";

$rules['match2_1'] = "match(getResponse('SNG_PAIR_1'), getCorrect('SNG_PAIR_1'))";
$rules['match2_2'] = "match(getResponse('SNG_PAIR_2'), getCorrect('SNG_PAIR_1'))";
$rules['match2_3'] = "match(getResponse('SNG_PAIR_3'), getCorrect('SNG_PAIR_1'))";
$rules['match2_4'] = "match(getResponse('SNG_PAIR_4'), getCorrect('SNG_PAIR_1'))";

$rules['match3_1'] = "match(getResponse('MUL_PAIR_1'), getCorrect('MUL_PAIR_1'))";
$rules['match3_2'] = "match(getResponse('MUL_PAIR_2'), getCorrect('MUL_PAIR_1'))";
$rules['match3_3'] = "match(getResponse('MUL_PAIR_3'), getCorrect('MUL_PAIR_1'))";
$rules['match3_4'] = "match(getResponse('MUL_PAIR_4'), getCorrect('MUL_PAIR_1'))";

$rules['map4_1'] = "mapResponse(getMap('MUL_PAIR_1'), getResponse('MUL_PAIR_1'))";
$rules['map4_2'] = "mapResponse(getMap('MUL_PAIR_1'), getResponse('MUL_PAIR_5'))";
$rules['map4_3'] = "mapResponse(getMap('MUL_PAIR_1'), getResponse('MUL_PAIR_6'))";
$rules['map4_4'] = "mapResponse(getMap('MUL_PAIR_1'), getResponse('MUL_PAIR_7'))";
$rules['map4_5'] = "mapResponse(getMap('MUL_PAIR_1'), getResponse('MUL_PAIR_4'))";

//$rules['setoutcome5_1'] = "setOutcomeValue('SCORE5_1', array(2))";
//$rules['setoutcome5_2'] = "setOutcomeValue('SCORE5_2', array(".$rules['map4_5']."))";

$rules['match6_1'] = "match(getResponse('MUL_DPAIR_1'), getCorrect('MUL_DPAIR_1'))";
$rules['match6_2'] = "match(getResponse('MUL_DPAIR_2'), getCorrect('MUL_DPAIR_1'))";

$rules['match7_1'] = "match(getResponse('MUL_BOOL_1'), getCorrect('MUL_BOOL_1'))";

$rules['match8_1'] = "match(getResponse('MUL_INT_1'), getCorrect('MUL_INT_1'))";

$rules['match9_1'] = "match(getResponse('MUL_FLOAT_1'), getCorrect('MUL_FLOAT_1'))";

$rules['match10_1'] = "match(getResponse('MUL_STR_1'), getCorrect('MUL_STR_1'))";

$rules['and11_1'] = "and(true, true)";
$rules['and11_2'] = "and(".$rules['match2_4'].", ".$rules['match6_2'].")";
$rules['and11_3'] = "and(".$rules['match6_1'].", ".$rules['match6_2'].")";
$rules['and11_4'] = "and(getResponse('SNG_BOOL_1'))";

$rules['equal12_1'] = "equal(getResponse('SNG_INT_1'), getResponse('SNG_INT_1'))";
$rules['equal12_2'] = "equal(getResponse('SNG_INT_1'), getResponse('SNG_INT_2'))";
$rules['equal12_3'] = "equal(2, 2)";
$rules['equal12_4'] = "equal(2, 3)";
$rules['equal12_5'] = "equal(getResponse('SNG_FLOAT_1'), getResponse('SNG_FLOAT_1'))";
$rules['equal12_6'] = "equal(getResponse('SNG_FLOAT_1'), getResponse('SNG_FLOAT_2'))";
$rules['equal12_7'] = "equal(2.2, 2.2)";
$rules['equal12_8'] = "equal(2.2, 3.3)";
*/
?>