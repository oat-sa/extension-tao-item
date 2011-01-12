<?php
require_once dirname(__FILE__) . '/../../tao/test/TestRunner.php';
require_once dirname(__FILE__) . '/../includes/common.php';
//require_once dirname(__FILE__) . '/../models/classes/Matching/matching_api.php';

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
		
        // Point (in QTI Directed Pair) 
        $point1 = taoItems_models_classes_Matching_VariableFactory::create (json_decode('{"0":"102", "1":"113"}'));
        $this->assertNotNull ($point1);
        $this->assertEqual ($point1->getType(), 'tuple');
        $tmpValue = $point1->getValue();
        $this->assertEqual ($tmpValue[0]->getValue(), '102');
        $this->assertEqual ($tmpValue[1]->getValue(), '113');
        $this->assertTrue ($point1->match($point1));
  
        // Circle (in QTI Shape associated to area mapping)
        $circle1 = taoItems_models_classes_Matching_VariableFactory::create (json_decode('{"type":"circle", "center":{"0":102,"1":113},"hradius":8,"vradius":8}'), "circle");
        $this->assertNotNull ($circle1);
        $this->assertEqual ($circle1->getType(), 'circle');
        $this->assertEqual ($circle1->getHRadius(), 8);
        $this->assertEqual ($circle1->getVRadius(), 8);
        $this->assertEqual ($circle1->getCenter()->getType(), "tuple");
        $tmpValue = $circle1->getCenter()->getValue();
        $this->assertEqual ($tmpValue[0]->getValue(), 102);
        $this->assertEqual ($tmpValue[1]->getValue(), 113);
        // Check if a point is inside this shape
        $point = taoItems_models_classes_Matching_VariableFactory::create(json_decode('{"0":102, "1":113}'));
        $this->assertTrue($circle1->contains ($point)); 
        $point = taoItems_models_classes_Matching_VariableFactory::create(json_decode('{"0":102, "1":105}'));
        $this->assertTrue($circle1->contains ($point)); 
        $point = taoItems_models_classes_Matching_VariableFactory::create(json_decode('{"0":102, "1":104}'));
        $this->assertFalse($circle1->contains ($point));
        
        // Rect (in QTI Shape associated to area mapping)
        $rect1 = taoItems_models_classes_Matching_VariableFactory::create (json_decode('{"type":"rect", "points":[{"0":0,"1":0}, {"0":0,"1":1}, {"0":1,"1":1}, {"0":1,"1":0}]}'), "rect");
        $this->assertNotNull ($rect1);
        $this->assertEqual ($rect1->getType(), 'rect');
        $tmpValue = $rect1->getPoints ();
        list ($x, $y) = $tmpValue[2]->getValue();
        $this->assertEqual ($x->getValue(), 1);
        $this->assertEqual ($y->getValue(), 1);
        // Check if a point is inside this shape
        $point = taoItems_models_classes_Matching_VariableFactory::create(json_decode('{"0":0.55, "1":0.55}'));
        $this->assertTrue($rect1->contains($point));
        $point = taoItems_models_classes_Matching_VariableFactory::create(json_decode('{"0":0.2, "1":0.8}'));
        $this->assertTrue($rect1->contains($point));
        $point = taoItems_models_classes_Matching_VariableFactory::create(json_decode('{"0":0.9, "1":0.7}'));
        $this->assertTrue($rect1->contains($point));
        $point = taoItems_models_classes_Matching_VariableFactory::create(json_decode('{"0":0.99, "1":0.77}'));
        $this->assertTrue($rect1->contains($point));
        // Limit are not inside (sauf le zero)
        $point = taoItems_models_classes_Matching_VariableFactory::create(json_decode('{"0":0, "1":0}'));
        $this->assertTrue($rect1->contains($point));
        $point = taoItems_models_classes_Matching_VariableFactory::create(json_decode('{"0":0, "1":1}'));
        $this->assertFalse($rect1->contains($point));
        $point = taoItems_models_classes_Matching_VariableFactory::create(json_decode('{"0":1, "1":1}'));
        $this->assertFalse($rect1->contains($point));
        $point = taoItems_models_classes_Matching_VariableFactory::create(json_decode('{"0":1, "1":0}'));
        $this->assertFalse($rect1->contains($point));
            // Check if a point is not inside this shape
        $point = taoItems_models_classes_Matching_VariableFactory::create(json_decode('{"0":0, "1":2}'));
        $this->assertFalse($rect1->contains($point));
        $point = taoItems_models_classes_Matching_VariableFactory::create(json_decode('{"0":2, "1":1}'));
        $this->assertFalse($rect1->contains($point));
        $point = taoItems_models_classes_Matching_VariableFactory::create(json_decode('{"0":1, "1":2}'));
        $this->assertFalse($rect1->contains($point));
        $point = taoItems_models_classes_Matching_VariableFactory::create(json_decode('{"0":2, "1":0}'));
        $this->assertFalse($rect1->contains($point));
	}
	
    public function testOperatorCreateVariable (){
        $matching = new taoItems_models_classes_Matching_Matching();
        
        // Null
        $null = $matching->createVariable (null, null);
        $this->assertNotNull ($null);
        $this->assertTrue ($null instanceOf taoItems_models_classes_Matching_BaseTypeVariable);
        
        // String
        $str1 = $matching->createVariable (null, 'Driver A');
        $this->assertNotNull ($str1);
        $this->assertTrue ($str1 instanceOf taoItems_models_classes_Matching_BaseTypeVariable);
        $this->assertEqual ($str1->getType(), 'string');
        $this->assertTrue ($str1->match($str1));
        
        // Float
        $dbl1 = $matching->createVariable (null, 3.1415);
        $this->assertNotNull ($dbl1);
        $this->assertTrue ($dbl1 instanceOf taoItems_models_classes_Matching_BaseTypeVariable);
        $this->assertEqual ($dbl1->getType(), 'double');
        $this->assertTrue ($dbl1->match($dbl1));
        
        // Integer
        $int1 = $matching->createVariable (null, 123456);
        $this->assertNotNull ($int1);
        $this->assertTrue ($int1 instanceOf taoItems_models_classes_Matching_BaseTypeVariable);
        $this->assertEqual ($int1->getType(), 'integer');
        $this->assertTrue ($int1->match($int1));
        
        // Boolean
        $bool1 = $matching->createVariable (null, true);
        $this->assertNotNull ($bool1);
        $this->assertTrue ($bool1 instanceOf taoItems_models_classes_Matching_BaseTypeVariable);
        $this->assertEqual ($bool1->getType(), 'boolean');
        $this->assertTrue ($bool1->match($bool1));
        
        // List
        $list1 = $matching->createVariable (null, json_decode('["TAO", "Test Assisté par Ordinateur"]'));
        $this->assertNotNull ($list1);
        $this->assertTrue ($list1 instanceOf taoItems_models_classes_Matching_List);
        $this->assertEqual ($list1->getType(), 'list');
        $this->assertTrue ($list1->match($list1));
        
        $list2 = $matching->createVariable (Array("type"=>"list"), "TAO", "Test Assisté par Ordinateur");
        $this->assertNotNull ($list2);
        $this->assertTrue ($list2 instanceOf taoItems_models_classes_Matching_List);
        $this->assertEqual ($list2->getType(), 'list');
        $this->assertTrue ($list2->match($list2));
        
        $this->assertTrue ($list1->match($list2));
        
        $list3 = $matching->createVariable (null, Array("TAO", "Test Assisté par Ordinateur"));
        $this->assertNotNull ($list3);
        $this->assertTrue ($list3 instanceOf taoItems_models_classes_Matching_List);
        $this->assertEqual ($list3->getType(), 'list');
        $this->assertTrue ($list3->match($list3));
        
        $this->assertTrue ($list2->match($list3));

        // Tuple
        $tuple1 = $matching->createVariable(json_decode('{"type":"tuple"}')
            , $matching->createVariable (json_decode('{"type":"string"}'), "DriverC")
            , $matching->createVariable (json_decode('{"type":"string"}'), "DriverB")
            , $matching->createVariable (json_decode('{"type":"string"}'), "DriverA"));
        $this->assertNotNull ($tuple1);
        $this->assertTrue ($tuple1 instanceOf taoItems_models_classes_Matching_Tuple);
        $this->assertEqual ($tuple1->getType(), 'tuple');
        $this->assertTrue ($tuple1->match($tuple1));
        
        $tuple2 = $matching->createVariable(Array("type"=>"tuple")
            , $matching->createVariable (Array ("type"=>"string"), "DriverC")
            , $matching->createVariable (Array ("type"=>"string"), "DriverB")
            , $matching->createVariable (Array ("type"=>"string"), "DriverA"));
        $this->assertNotNull ($tuple2);
        $this->assertTrue ($tuple2 instanceOf taoItems_models_classes_Matching_Tuple);
        $this->assertEqual ($tuple2->getType(), 'tuple');
        $this->assertTrue ($tuple2->match($tuple2));
        
        $this->assertTrue ($tuple1->match($tuple2));
        
        $tuple3 = $matching->createVariable(Array("type"=>"tuple")
            , "DriverC"
            , "DriverB"
            , "DriverA");
        $this->assertNotNull ($tuple3);
        $this->assertTrue ($tuple3 instanceOf taoItems_models_classes_Matching_Tuple);
        $this->assertEqual ($tuple3->getType(), 'tuple');
        $this->assertTrue ($tuple3->match($tuple3));
        
        $this->assertTrue ($tuple2->match($tuple3));
    }

    public function testOperatorGT (){
        $matching = new taoItems_models_classes_Matching_Matching();
        $this->assertTrue ($matching->gt (null, 3, 2));
        $this->assertTrue ($matching->gt (null, 3.14, 1.66));
        $this->assertFalse($matching->gt (null, 3, 3));
        $this->assertFalse($matching->gt (null, 2, 3));
        try {
            $matching->gt (null, Array(3), 2);
            $this->fail("Exception was expected.");
        } catch (Exception $e) {
             $this->pass();
        }
    }

    public function testOperatorLT (){
        $matching = new taoItems_models_classes_Matching_Matching();
        $this->assertTrue ($matching->lt (null, 2, 3));
        $this->assertTrue ($matching->lt (null, 1.66, 3.14));
        $this->assertFalse ($matching->lt (null, 2, 2));
        $this->assertFalse ($matching->lt (null, 3, 2));
        try {
            $matching->gt (null, 2, Array(3));
            $this->fail("Exception was expected.");
        } catch (Exception $e) {
             $this->pass();
        }
    }

    public function testOperatorGTE (){
        $matching = new taoItems_models_classes_Matching_Matching();
        $this->assertTrue ($matching->gte (null, 3, 2));
        $this->assertTrue ($matching->gte (null, 3.14, 1.66));
        $this->assertTrue($matching->gte (null, 3, 3));
        $this->assertFalse($matching->gte (null, 2, 3));
        try {
            $matching->gte (null, Array(3), 2);
            $this->fail("Exception was expected.");
        } catch (Exception $e) {
             $this->pass();
        }
    }

    public function testOperatorLTE (){
        $matching = new taoItems_models_classes_Matching_Matching();
        $this->assertTrue ($matching->lte (null, 2, 3));
        $this->assertTrue ($matching->lte (null, 1.66, 3.14));
        $this->assertTrue ($matching->lte (null, 2, 2));
        $this->assertFalse ($matching->lte (null, 3, 2));
        try {
            $matching->gte (null, 2, Array(3));
            $this->fail("Exception was expected.");
        } catch (Exception $e) {
             $this->pass();
        }
    }

    public function testOperatorProduct (){
        $matching = new taoItems_models_classes_Matching_Matching();
        $this->assertEqual ($matching->product (null, 2, 3), 6);
        $this->assertEqual ($matching->product (null, $matching->createVariable(null, 2), 3), 6);
        $this->assertEqual ($matching->product (null, $matching->createVariable(null, 2), 3, null), null);
        $this->assertEqual ($matching->product (null, $matching->createVariable(null, 2), 3, 0), 0);
        try {
            $matching->product (null, 2, Array(3));
            $this->fail("Exception was expected.");
        } catch (Exception $e) {
             $this->pass();
        }
    }

    public function testOperatorSum(){
        $matching = new taoItems_models_classes_Matching_Matching();
        $this->assertEqual ($matching->sum (null, 2, 3), 5);
        $this->assertEqual ($matching->sum (null, $matching->createVariable(null, 2), 3), 5);
        $this->assertEqual ($matching->sum (null, $matching->createVariable(null, 2), 3, null), null);
        $this->assertEqual ($matching->sum (null, $matching->createVariable(null, 2), 3, 0), 5);
        try {
            $matching->sum (null, 2, Array(3));
            $this->fail("Exception was expected.");
        } catch (Exception $e) {
             $this->pass();
        }
    }

    public function testOperatorDivide (){
        $matching = new taoItems_models_classes_Matching_Matching();
        $this->assertEqual ($matching->divide (null, 3, 2), 1.5);
        $this->assertEqual ($matching->divide (null, $matching->createVariable(null, 3), 2), 1.5);
        $this->assertEqual ($matching->divide (null, $matching->createVariable(null, 3), null), null);
        $this->assertEqual ($matching->divide (null, $matching->createVariable(null, 3), 0), null);
        try {
            $matching->divide (null, 2, Array(3));
            $this->fail("Exception was expected.");
        } catch (Exception $e) {
             $this->pass();
        }
    }

    public function testOperatorSubstract (){
        $matching = new taoItems_models_classes_Matching_Matching();
        $this->assertEqual ($matching->subtract (null, 3, 2), 1);
        $this->assertEqual ($matching->subtract (null, $matching->createVariable(null, 3), 2), 1);
        $this->assertEqual ($matching->subtract (null, $matching->createVariable(null, 3), null), null);
        try {
            $matching->subtract (null, 2, Array(3));
            $this->fail("Exception was expected.");
        } catch (Exception $e) {
             $this->pass();
        }
    }

    public function testOperatorRound (){
        $matching = new taoItems_models_classes_Matching_Matching();
        $this->assertEqual ($matching->round (null, 3.4), 3);
        $this->assertEqual ($matching->round (null, $matching->createVariable(null, 3.5)), 4);
        $this->assertEqual ($matching->round (null, null), null);
        try {
            $matching->round (null, Array(3.5));
            $this->fail("Exception was expected.");
        } catch (Exception $e) {
             $this->pass();
        }
    }

    public function testOperatorIntegerDivide (){
        $matching = new taoItems_models_classes_Matching_Matching();
        $this->assertEqual ($matching->integerDivide (null, 3, 2.3), 1);
        $this->assertEqual ($matching->integerDivide (null, $matching->createVariable(null, 3), 2), 2);
        $this->assertEqual ($matching->integerDivide (null, $matching->createVariable(null, 3), null), null);
        $this->assertEqual ($matching->integerDivide (null, $matching->createVariable(null, 3), 0), null);
        try {
            $matching->integerDivide (null, 2, Array(3));
            $this->fail("Exception was expected.");
        } catch (Exception $e) {
             $this->pass();
        }
    }
    
    public function testOperatorNot (){
        $matching = new taoItems_models_classes_Matching_Matching();
        $this->assertEqual ($matching->not (null, true), false);
        $this->assertEqual ($matching->not (null, $matching->createVariable(null, false)), true);
        $this->assertEqual ($matching->not (null, null), null);
        try {
            $matching->not (null, Array(3));
            $this->fail("Exception was expected.");
        } catch (Exception $e) {
             $this->pass();
        }
    }
    
    public function testOperatorOr (){
        $matching = new taoItems_models_classes_Matching_Matching();
        $this->assertEqual ($matching->orExpression (null, true, true, true, true), true);
        $this->assertEqual ($matching->orExpression (null, false, false, false), false);
        $this->assertEqual ($matching->orExpression (null, false, false, true), true);
        $this->assertEqual ($matching->orExpression (null, $matching->createVariable(null, true), true, true), true);
        $this->assertEqual ($matching->orExpression (null, $matching->createVariable(null, false), true, true), true);
        $this->assertEqual ($matching->orExpression (null, $matching->createVariable(null, false)), false);
        $this->assertEqual ($matching->orExpression (null, null), null);
        try {
            $matching->orExpression (null, Array(3));
            $this->fail("Exception was expected.");
        } catch (Exception $e) {
             $this->pass();
        }
    }
    
    // AND OPERATOR
    public function testOperatorAnd (){
        $matching = new taoItems_models_classes_Matching_Matching();
        $this->assertEqual ($matching->andExpression (null, true, true, true, true), true);
        $this->assertEqual ($matching->andExpression (null, false, false, false), false);
        $this->assertEqual ($matching->andExpression (null, false, false, true), false);
        $this->assertEqual ($matching->andExpression (null, $matching->createVariable(null, true), true, true), true);
        $this->assertEqual ($matching->andExpression (null, $matching->createVariable(null, false), true, true), false);
        $this->assertEqual ($matching->andExpression (null, $matching->createVariable(null, false)), false);
        $this->assertEqual ($matching->andExpression (null, null), null);
        try {
            $matching->andExpression (null, Array(3));
            $this->fail("Exception was expected.");
        } catch (Exception $e) {
             $this->pass();
        }
    }
    
    // CONTAINS OPERATOR
    public function testOperatorContains () {
        $matching = new taoItems_models_classes_Matching_Matching();
        
        // List
        $this->assertTrue ($matching->contains (
            null
            , $matching->createVariable(null, json_decode('[1,2,3,4]'))
            , $matching->createVariable(null, json_decode('[1,2,3]'))
        ));
        // List
        $this->assertTrue ($matching->contains (
            null
            , $matching->createVariable(null, json_decode('[1,2,3,4]'))
            , $matching->createVariable(null, json_decode('3'))
        ));
        // List of List
        $this->assertTrue ($matching->contains (
            null
            , $matching->createVariable(null, json_decode('[1,2,3,4]'))
            , $matching->createVariable(null, json_decode('[3,4]'))
        ));
        // List of List
        $this->assertFalse ($matching->contains (
            json_decode('{"needleType":"custom"}')
            , $matching->createVariable(null, json_decode('[1,2,3,4]'))
            , $matching->createVariable(null, json_decode('[3]'))
        ));
        // List of List
        $this->assertTrue ($matching->contains (
            json_decode('{"needleType":"custom"}')
            , $matching->createVariable(null, json_decode('[1,2,3,4]'))
            , $matching->createVariable(null, json_decode('3'))
        ));
        // List of List
        $this->assertTrue ($matching->contains (
            json_decode('{"needleType":"custom"}')
            , $matching->createVariable(null, json_decode('[[1,2],[3,4]]'))
            , $matching->createVariable(null, json_decode('[1,2]'))
        ));
        // List of List
        $this->assertTrue ($matching->contains (
            json_decode('{"needleType":"custom"}')
            , $matching->createVariable(null, json_decode('[[1,2],[3,4]]'))
            , $matching->createVariable(null, json_decode('[3,4]'))
        ));
        // List of List
        $this->assertFalse ($matching->contains (
            json_decode('{"needleType":"custom"}')
            , $matching->createVariable(null, json_decode('[[1,2],[3,4]]'))
            , $matching->createVariable(null, json_decode('[4,5]'))
        ));
        // Tuple
        $this->assertFalse ($matching->contains (
            null
            , $matching->createVariable(null, json_decode('{"0":1, "1":2, "2":3, "3":4}'))
            , $matching->createVariable(null, json_decode('{"0":1, "3":4}'))
        ));
        // List of Tuple
        $this->assertTrue ($matching->contains (
            json_decode('{"needleType":"custom"}')
            , $matching->createVariable(null, json_decode('[{"0":1, "1":2}, {"0":3, "1":4}]'))
            , $matching->createVariable(null, json_decode('{"0":3, "1":4}'))
        ));
        // List of Tuple
        $this->assertFalse ($matching->contains (
            json_decode('{"needleType":"custom"}')
            , $matching->createVariable(null, json_decode('[{"0":1, "1":2}, {"0":3, "1":4}]'))
            , $matching->createVariable(null, json_decode('{"0":4, "1":4}'))
        ));
        // List of Tuple
        $this->assertFalse ($matching->contains (
            null
            , $matching->createVariable(null, json_decode('[{"0":1, "1":2}, {"0":3, "1":4}]'))
            , $matching->createVariable(null, json_decode('{"0":3, "1":4}'))
        ));
    }

    // RANDOM INTEGER OPERATOR
    public function testOperatorRandomInteger () {
        $matching = new taoItems_models_classes_Matching_Matching();
        $null = false;
        $correctRange = true;
        $i=0;
        while ($i<100 && !$null && $correctRange){
            $rand = $matching->randomInteger (Array('min'=>0,'max'=>9));
            $null = ($rand === null);
            $correctRange = $rand<=9 && $rand>=0;
            $i++;
        }
        $this->assertFalse ($null);
        $this->assertTrue ($correctRange);
    }
    
    // RANDOM FLOAT OPERATOR
    public function testOperatorRandomFloat () {
        $matching = new taoItems_models_classes_Matching_Matching();
        $null = false;
        $correctRange = true;
        $i=0;
        while ($i<100 && !$null && $correctRange){
            $rand = $matching->randomFloat (Array('min'=>0.0,'max'=>9.9));
            $null = ($rand === null);
            $correctRange = $rand<=9.9 && $rand>=0.0;
            $i++;
        }
        $this->assertFalse ($null);
        $this->assertTrue ($correctRange);
    }
        
	public function testTemplateResponseProcessingMatchCorrect (){
		matching_init ();
		matching_setRule (taoItems_models_classes_Matching_Matching::MATCH_CORRECT);
		matching_setCorrects (json_decode('[{"identifier":"RESPONSE", "value":"1"}]'));
		matching_setResponses (json_decode('[{"identifier":"RESPONSE", "value":"1"}]'));
		matching_setOutcomes (json_decode('[{"identifier":"SCORE", "type":"double"}]'));
		matching_evaluate ();
		$outcomes = matching_getOutcomes ();
		$this->assertEqual ($outcomes["SCORE"]["value"], 1);
		
		matching_init ();
		matching_setRule (taoItems_models_classes_Matching_Matching::MATCH_CORRECT);
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
		matching_setRule (taoItems_models_classes_Matching_Matching::MAP_RESPONSE);
		matching_setCorrects (json_decode('[{"identifier":"RESPONSE", "value":"Paris"}]'));
		matching_setResponses (json_decode('[{"identifier":"RESPONSE", "value":"paris"}]'));
		matching_setMaps (json_decode('[{"identifier":"RESPONSE", "value":[{"key":"Paris", "value":1}, {"key":"paris", "value":0.9}]}]'));
		matching_setOutcomes (json_decode('[{"identifier":"SCORE", "type":"double"}]'));
		matching_evaluate ();
		$outcomes = matching_getOutcomes ();
		$this->assertEqual ($outcomes["SCORE"]["value"], 0.9);
		
		// list list (multiple pair)
		matching_init ();
		matching_setRule (taoItems_models_classes_Matching_Matching::MAP_RESPONSE);
		matching_setCorrects (json_decode('[{"identifier":"RESPONSE", "value":[["A", "B"], ["C", "D"], ["E", "F"]]}]'));
		matching_setResponses (json_decode('[{"identifier":"RESPONSE", "value":[["A", "B"], ["C", "D"], ["E", "F"]]}]'));
		matching_setMaps (json_decode('[{"identifier":"RESPONSE", "value":[{"key":["A", "B"], "value":1}, {"key":["C", "D"], "value":0.5}, {"key":["E", "F"], "value":0.2}]}]'));
		matching_setOutcomes (json_decode('[{"identifier":"SCORE", "type":"double"}]'));
		matching_evaluate ();
		$outcomes = matching_getOutcomes ();
		$this->assertEqual ($outcomes["SCORE"]["value"], 1.7);
		
		// list list (multiple pair but with reversed result)
		matching_init ();
		matching_setRule (taoItems_models_classes_Matching_Matching::MAP_RESPONSE);
		matching_setCorrects (json_decode('[{"identifier":"RESPONSE", "value":[["A", "B"], ["C", "D"], ["E", "F"]]}]'));
		matching_setResponses (json_decode('[{"identifier":"RESPONSE", "value":[["B", "A"], ["D", "C"], ["F", "E"]]}]'));
		matching_setMaps (json_decode('[{"identifier":"RESPONSE", "value":[{"key":["A", "B"], "value":1}, {"key":["C", "D"], "value":0.5}, {"key":["E", "F"], "value":0.2}]}]'));
		matching_setOutcomes (json_decode('[{"identifier":"SCORE", "type":"double"}]'));
		matching_evaluate ();
		$outcomes = matching_getOutcomes ();
		$this->assertEqual ($outcomes["SCORE"]["value"], 1.7);
		
		// list tuple (multiple directedpair)
		matching_init ();
		matching_setRule (taoItems_models_classes_Matching_Matching::MAP_RESPONSE);
		matching_setCorrects (json_decode('[{"identifier":"RESPONSE", "value":[{"0":"A", "1":"B"}, {"0":"C", "1":"D"}, {"0":"E", "1":"F"}]}]'));
		matching_setResponses (json_decode('[{"identifier":"RESPONSE", "value":[{"0":"A", "1":"B"}, {"0":"C", "1":"D"}, {"0":"E", "1":"F"}]}]'));
		matching_setMaps (json_decode('[{"identifier":"RESPONSE", "value":[{"key":{"0":"A", "1":"B"}, "value":1}, {"key":{"0":"C", "1":"D"}, "value":0.5}, {"key":{"0":"E", "1":"F"}, "value":0.2}]}]'));
		matching_setOutcomes (json_decode('[{"identifier":"SCORE", "type":"double"}]'));
		matching_evaluate ();
		$outcomes = matching_getOutcomes ();
		$this->assertEqual ($outcomes["SCORE"]["value"], 1.7);

		// list tuple (multiple directedpair reversed pair)
		matching_init ();
		matching_setRule (taoItems_models_classes_Matching_Matching::MAP_RESPONSE);
		matching_setCorrects (json_decode('[{"identifier":"RESPONSE", "value":[{"0":"A", "1":"B"}, {"0":"C", "1":"D"}, {"0":"E", "1":"F"}]}]'));
		matching_setResponses (json_decode('[{"identifier":"RESPONSE", "value":[{"0":"B", "1":"A"}, {"0":"D", "1":"C"}, {"0":"F", "1":"E"}]}]'));
		matching_setMaps (json_decode('[{"identifier":"RESPONSE", "value":[{"key":{"0":"A", "1":"B"}, "value":1}, {"key":{"0":"C", "1":"D"}, "value":0.5}, {"key":{"0":"E", "1":"F"}, "value":0.2}]}]'));
		matching_setOutcomes (json_decode('[{"identifier":"SCORE", "type":"double"}]'));
		matching_evaluate ();
		$outcomes = matching_getOutcomes ();
		$this->assertEqual ($outcomes["SCORE"]["value"], 0);
	}

    // ***********************************************
    // * TEST ON QTI ITEM
    // ***********************************************
    public function testMatch () {
        $parameters = array(
            'root_url' => ROOT_URL,
            'base_www' => BASE_WWW,
            'taobase_www' => TAOBASE_WWW,
            'delivery_server_mode' => true,
        	'raw_preview'	=> false,
        	'debug'			=> false
        );
        taoItems_models_classes_TemplateRenderer::setContext($parameters, 'ctx_');
        
        //check if samples are loaded
        $file = dirname(__FILE__).'/samples/choice.xml';
        $item = $this->qtiService->loadItemFromFile ($file);
        //$itemSerial = $item->getSerial ();
        //$item = null;
        //$item = $this->qtiService->getItemBySerial ($itemSerial);
                
        $matching_data = $item->getMatchingData ();
        matching_init ();
        matching_setRule ($matching_data['rule']);
        matching_setCorrects ($matching_data['corrects']);
        matching_setMaps ($matching_data['maps']);
        matching_setOutcomes ($matching_data['outcomes']);
        matching_setResponses (json_decode('[{"identifier":"RESPONSE", "value":"ChoiceA"}]'));
        matching_evaluate ();
        $outcomes = matching_getOutcomes ();
        $this->assertEqual ($outcomes["SCORE"]["value"], 1);
        
        //echo $item->toXHTML();
        //echo '<script type="application/Javascript">$(document).ready(function(){ TAO_MATCHING.engine.url = "/taoItems/Matching/evaluateDebug?item_path='.urlencode($file).'"; });</script>';
    }

    public function testCustomMatch () {
        $parameters = array(
            'root_url' => ROOT_URL,
            'base_www' => BASE_WWW,
            'taobase_www' => TAOBASE_WWW,
            'delivery_server_mode' => true,
        	'raw_preview'	=> false,
        	'debug'			=> false
        );
        taoItems_models_classes_TemplateRenderer::setContext($parameters, 'ctx_');
        
        //check if samples are loaded
        $file = dirname(__FILE__).'/samples/custom_rule/custom_match_choice.xml';
        $item = $this->qtiService->loadItemFromFile ($file);
        
        $matching_data = $item->getMatchingData ();
        matching_init ();
        matching_setRule ($matching_data['rule']);
        matching_setCorrects ($matching_data['corrects']);
        matching_setMaps ($matching_data['maps']);
        matching_setOutcomes ($matching_data['outcomes']);
        matching_setResponses (json_decode('[{"identifier":"RESPONSE", "value":"ChoiceA"}]'));
        matching_evaluate ();
        $outcomes = matching_getOutcomes ();
        $this->assertEqual ($outcomes["SCORE"]["value"], 1);
        
        //echo $item->toXHTML();
        //echo '<script type="application/Javascript">$(document).ready(function(){ TAO_MATCHING.engine.url = "/taoItems/Matching/evaluateDebug?item_path='.urlencode($file).'"; });</script>';
    }

    public function testCustomMapResponse () {
        $parameters = array(
            'root_url' => ROOT_URL,
            'base_www' => BASE_WWW,
            'taobase_www' => TAOBASE_WWW,
            'delivery_server_mode' => true,
        	'raw_preview'	=> false,
        	'debug'			=> false
        );
        taoItems_models_classes_TemplateRenderer::setContext($parameters, 'ctx_');
        
        //check if samples are loaded
        $file = dirname(__FILE__).'/samples/custom_rule/custom_map_response_choice_multiple.xml';
        $item = $this->qtiService->loadItemFromFile ($file);
        
        $matching_data = $item->getMatchingData ();
        matching_init ();
        matching_setRule ($matching_data['rule']);
        matching_setCorrects ($matching_data['corrects']);
        matching_setMaps ($matching_data['maps']);
        matching_setOutcomes ($matching_data['outcomes']);
        matching_setResponses (json_decode('[{"identifier":"RESPONSE", "value":["H", "O"]}]'));
        matching_evaluate ();
        $outcomes = matching_getOutcomes ();
        $this->assertEqual ($outcomes["SCORE"]["value"], 2);
       
        //echo $item->toXHTML();
        //echo '<script type="application/Javascript">$(document).ready(function(){ TAO_MATCHING.engine.url = "/taoItems/Matching/evaluateDebug?item_path='.urlencode($file).'"; });</script>';
    }

    public function testCustomOrderPartialScoring () {
        $parameters = array(
            'root_url' => ROOT_URL,
            'base_www' => BASE_WWW,
            'taobase_www' => TAOBASE_WWW,
            // Tmp matching context
            'delivery_server_mode' => true,
        	'raw_preview'	=> false,
        	'debug'			=> false
        );
        taoItems_models_classes_TemplateRenderer::setContext($parameters, 'ctx_');
        
        //check if samples are loaded
        $file = dirname(__FILE__).'/samples/custom_rule/custom_order_partial_scoring.xml';
        $item = $this->qtiService->loadItemFromFile ($file);
                
        $matching_data = $item->getMatchingData ();
        matching_init ();
        matching_setRule ($matching_data['rule']);
        matching_setCorrects ($matching_data['corrects']);
        matching_setMaps ($matching_data['maps']);
        matching_setOutcomes ($matching_data['outcomes']);
        matching_setResponses (json_decode('[{"identifier":"RESPONSE", "value":{"0":"DriverC", "1":"DriverB", "2":"DriverA"}}]'));
        matching_evaluate ();
        $outcomes = matching_getOutcomes ();
        $this->assertEqual ($outcomes["SCORE"]["value"], 1);

      //echo $item->toXHTML();
      //echo '<script type="application/Javascript">$(document).ready(function(){ TAO_MATCHING.engine.url = "/taoItems/Matching/evaluateDebug?item_path='.urlencode($file).'"; });</script>';
    }

    public function testCustomMultiplePartialScoring () {
        $parameters = array(
            'root_url' => ROOT_URL,
            'base_www' => BASE_WWW,
            'taobase_www' => TAOBASE_WWW,
            'delivery_server_mode' => false,
            'raw_preview'   => false,
        	'debug'			=> false
        );
        taoItems_models_classes_TemplateRenderer::setContext($parameters, 'ctx_');
        
        //check if samples are loaded
        $file = dirname(__FILE__).'/samples/custom_rule/custom_multiple_partial_scoring.xml';
        $item = $this->qtiService->loadItemFromFile ($file);
        
        $matching_data = $item->getMatchingData ();
        
        matching_init ();
        matching_setRule ($matching_data['rule']);
        matching_setCorrects ($matching_data['corrects']);
        matching_setMaps ($matching_data['maps']);
        matching_setOutcomes ($matching_data['outcomes']);
        matching_setResponses (json_decode('[{"identifier":"RESPONSE", "value":["CO","GO","SH"]}]'));
        matching_evaluate ();
        $outcomes = matching_getOutcomes ();
        $this->assertEqual ($outcomes["SCORE"]["value"], 2);
       
        //echo $item->toXHTML();
        //echo '<script type="application/Javascript">$(document).ready(function(){ TAO_MATCHING.engine.url = "/taoItems/Matching/evaluateDebug?item_path='.urlencode($file).'"; });</script>';
    }

    public function testSelectPoint () {
        $parameters = array(
            'root_url' => ROOT_URL,
            'base_www' => BASE_WWW,
            'taobase_www' => TAOBASE_WWW,
            'delivery_server_mode' => false,
            'raw_preview'   => false,
        	'debug'			=> false
        );
        taoItems_models_classes_TemplateRenderer::setContext($parameters, 'ctx_');
        
        //check if samples are loaded
        $file = dirname(__FILE__).'/samples/select_point.xml';
        $item = $this->qtiService->loadItemFromFile ($file);
        
        $matching_data = $item->getMatchingData ();
        
        matching_init ();
        matching_setRule ($matching_data['rule']);
        matching_setCorrects ($matching_data['corrects']);
        matching_setMaps ($matching_data['maps']);
        matching_setAreaMaps ($matching_data['areaMaps']);
        matching_setOutcomes ($matching_data['outcomes']);
        matching_setResponses (json_decode('[{"identifier":"RESPONSE", "value":{"0":102, "1":113}}]'));
        matching_evaluate ();
        $outcomes = matching_getOutcomes ();
        $this->assertEqual ($outcomes["SCORE"]["value"], 1);
    }

    public function testCustomAllRules () {
        $parameters = array(
            'root_url' => ROOT_URL,
            'base_www' => BASE_WWW,
            'taobase_www' => TAOBASE_WWW,
            'delivery_server_mode' => false,
        	'raw_preview'	=> false,
        	'debug'			=> false
        );
        taoItems_models_classes_TemplateRenderer::setContext($parameters, 'ctx_');
        
        //check if samples are loaded
        $file = dirname(__FILE__).'/samples/custom_rule/custom_all_rules.xml';
        $item = $this->qtiService->loadItemFromFile ($file);
        
        $matching_data = $item->getMatchingData ();
        
        matching_init ();
        matching_setRule ($matching_data['rule']);
        //echo 'rule : ';
        //var_dump($matching_data['rule']);
        //echo 'corrects : ';
        //var_dump($matching_data['corrects']);
        matching_setCorrects ($matching_data['corrects']);
        //matching_setMaps ($matching_data['maps']);
        matching_setOutcomes ($matching_data['outcomes']);
        //matching_setResponses (json_decode('[{"identifier":"RESPONSE", "value":["CO","GO","SH"]}]'));
        //xdebug_start_trace('/tmp/test.txt', XDEBUG_TRACE_HTML);
        matching_evaluate ();
        //xdebug_stop_trace();
        $outcomes = matching_getOutcomes ();
        //echo 'outcomes : ';
        //var_dump($outcomes);
        $this->assertEqual ($outcomes["SCORE_INT"]["value"], 1);
       
        //echo $item->toXHTML();
        //echo '<script type="application/Javascript">$(document).ready(function(){ TAO_MATCHING.engine.url = "/taoItems/Matching/evaluateDebug?item_path='.urlencode($file).'"; });</script>';
    }

    // Test an item with a large coverage of the rules' subset
    public function testShakespear () {
        $parameters = array(
            'root_url' => ROOT_URL,
            'base_www' => BASE_WWW,
            'taobase_www' => TAOBASE_WWW,
            'delivery_server_mode' => false,
        	'raw_preview'	=> false,
        	'debug'			=> false
        );
        taoItems_models_classes_TemplateRenderer::setContext($parameters, 'ctx_');
        
        //check if samples are loaded
        $file = dirname(__FILE__).'/samples/custom_rule/shakespeare.xml';
        $item = $this->qtiService->loadItemFromFile ($file);
        
        $matching_data = $item->getMatchingData ();
        matching_init ();
        matching_setRule ($matching_data['rule']);
        matching_setCorrects ($matching_data['corrects']);
        matching_setOutcomes ($matching_data['outcomes']);
        matching_setMaps ($matching_data['maps']);
        matching_setResponses (json_decode ('[
                {"identifier":"response_1", "value":"choice_1"}
                ,{"identifier":"response_2","value":"choice_4"}
                ,{"identifier":"response_3","value":"poet"}
                ,{"identifier":"response_4","value":"Bard of Avon"}
                ,{"identifier":"response_5","value":[{"0":"group_1","1":"choice_7"},{"0":"group_2","1":"choice_8"},{"0":"group_3","1":"choice_9"},{"0":"group_4","1":"choice_10"}]}
                ,{"identifier":"response_6","value":["choice_12","choice_13"]}
            ]')
        );
        matching_evaluate ();
        $outcomes = matching_getOutcomes ();
        $this->assertEqual ($outcomes["SCORE"]["value"], 6);


        //echo $item->toXHTML();
        //echo '<script type="application/Javascript">$(document).ready(function(){ TAO_MATCHING.engine.url = "/taoItems/Matching/evaluateDebug?item_path='.urlencode($file).'"; });</script>';
    }

    // Test that the ParserFactory figure out the type of Response Processing for an item
    public function testPatternSeeker () {
        $file = dirname(__FILE__).'/samples/choice.xml';
        $item = $this->qtiService->loadItemFromFile ($file);
        $this->assertTrue ($item->getResponseProcessing() instanceOf taoItems_models_classes_QTI_response_TemplatesDriven);
        //echo'<pre>';print_r(htmlentities($item->toQTI()));echo'</pre>';
        
        $file = dirname(__FILE__).'/samples/custom_rule/custom_match_choice.xml';
        $item = $this->qtiService->loadItemFromFile ($file);
        // CHECK that choice.xml produces a TemplateDriven Response Processing 
        $this->assertTrue ($item->getResponseProcessing() instanceOf taoItems_models_classes_QTI_response_Custom);
        //echo'<pre>';print_r(htmlentities($item->toQTI()));echo'</pre>';
        
        $file = dirname(__FILE__).'/samples/custom_rule/custom_order_partial_scoring.xml';
        $item = $this->qtiService->loadItemFromFile ($file);
        // CHECK that choice.xml produces a TemplateDriven Response Processing 
        $this->assertTrue ($item->getResponseProcessing() instanceOf taoItems_models_classes_QTI_response_Custom);
        //echo'<pre>';print_r(htmlentities($item->toQTI()));echo'</pre>';
        
        //check if samples are loaded
        $file = dirname(__FILE__).'/samples/custom_rule/shakespeare.xml';
        $item = $this->qtiService->loadItemFromFile ($file);
        $this->assertTrue ($item->getResponseProcessing() instanceOf taoItems_models_classes_QTI_response_TemplatesDriven);
        //echo'<pre>';print_r(htmlentities($item->toQTI()));echo'</pre>';
    }

}

?>