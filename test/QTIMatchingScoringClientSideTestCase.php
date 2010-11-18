<?php

require_once dirname(__FILE__) . '/../../generis/common/inc.extension.php';
require_once dirname(__FILE__) . '/../includes/common.php';
define ('PATH_SAMPLE', dirname(__FILE__).'/samples/');
?>

<!DOCTYPE html>
<html>
<head>
	<title>QUnit Test Suite</title>
	<link rel="stylesheet" href="../../tao/test/qunit/qunit.css" type="text/css" media="screen">
	<!--<script type="text/javascript" src="https://getfirebug.com/firebug-lite.js"></script>-->
	<script type="application/javascript" src='../../tao/views/js/jquery-1.4.2.min.js'></script>
	<script type="application/javascript" src="../../tao/test/qunit/qunit.js"></script>
	<script type="application/javascript" src="../views/js/taoMatching/src/class.Matching.js"></script>
	<script type="application/javascript" src="../views/js/taoMatching/src/class.MatchingRemote.js"></script>
	<script type="application/javascript" src="../views/js/taoMatching/src/class.VariableFactory.js"></script>
	<script type="application/javascript" src="../views/js/taoMatching/src/class.Variable.js"></script>
	<script type="application/javascript" src="../views/js/taoMatching/src/class.BaseTypeVariable.js"></script>
	<script type="application/javascript" src="../views/js/taoMatching/src/class.Collection.js"></script>
	<script type="application/javascript" src="../views/js/taoMatching/src/class.List.js"></script>
	<script type="application/javascript" src="../views/js/taoMatching/src/class.Tuple.js"></script>
	<script type="application/javascript" src="../views/js/taoMatching/src/class.Map.js"></script>
	<script type="application/javascript" src="../views/js/taoMatching/src/matching_constant.js"></script>
	<script type="application/javascript" src="../views/js/taoMatching/src/matching_api.js"></script>
	
	<!-- -------------------------------------------------------------------------
	QTI DATA
	--------------------------------------------------------------------------->
	
	<script type="application/javascript">
        var testToRun = '*';
        //var testToRun = "Remote Matching : Shakespear.xml";
        
        var testUnitFct = test;
        var asynctestUnitFct = asyncTest;
        test = function (label, func) {
            if (testToRun == "*"){
                testUnitFct (label, func);
            } else if (testToRun == label){
                testUnitFct (label, func);
            }
        }
        asyncTest = function (label, func) {
            if (testToRun == "*"){
                asynctestUnitFct (label, func);
            } else if (testToRun == label){
                asynctestUnitFct (label, func);
            }
        }

		test("Test the VariableFactory (integer)", function() {
			var int1 = TAO_MATCHING.VariableFactory.create (1);
			var int2 = TAO_MATCHING.VariableFactory.create (2);
			var nullVar = TAO_MATCHING.VariableFactory.create (null);
			ok (int1 != null, 'Variable not null');
			equals (int1.getType(), 'number', 'Right type');
			equals (int1.getValue(), 1, 'Right value');
			ok (int1.match(int1), 'Match itself');
			ok (!int1.match(int2), 'Does not match a different integer variable');
			ok (int1.equal(int1), 'It is equal to itself');
			ok (!int1.equal(int2), 'It is not equal to a different integer variable');
			ok (!int1.equal(nullVar), 'It is not equal to a null variable');
		});
		
		test("Test the VariableFactory (float)", function() {
			var dbl1 = TAO_MATCHING.VariableFactory.create (3.14);
			var dbl2 = TAO_MATCHING.VariableFactory.create (3.0);
			var nullVar = TAO_MATCHING.VariableFactory.create (null);
			ok (dbl1 != null, 'Variable not null');
			equals (dbl1.getType(), 'number', 'Right type');
			equals (dbl1.getValue(), 3.14, 'Right value');
			ok (dbl1.match(dbl1), 'Match itself');
			ok (!dbl1.match(dbl2), 'Does not match a different float variable');
			ok (dbl1.equal(dbl1), 'It is equal to itself');
			ok (!dbl1.equal(dbl2), 'It is not equal to a different float variable');
			ok (!dbl1.equal(nullVar), 'It is not equal to a null variable');
		});
		
		test("Test the VariableFactory (boolean)", function() {
			var bool1 = TAO_MATCHING.VariableFactory.create (3.14);
			var bool2 = TAO_MATCHING.VariableFactory.create (3.0);
			var nullVar = TAO_MATCHING.VariableFactory.create (null);
			ok (bool1 != null, 'Variable not null');
			equals (bool1.getType(), 'number', 'Right type');
			equals (bool1.getValue(), 3.14, 'Right value');
			ok (bool1.match(bool1), 'Match itself');
			ok (!bool1.match(bool2), 'Does not match a different boolean variable');
			ok (bool1.equal(bool1), 'It is equal to itself');
			ok (!bool1.equal(bool2), 'It is not equal to a different boolean variable');
			ok (!bool1.equal(nullVar), 'It is not equal to a null variable');
		});
		
		test("Test the VariableFactory (string)", function() {
			var str1 = TAO_MATCHING.VariableFactory.create ('TAO');
			var str2 = TAO_MATCHING.VariableFactory.create ('it\'s so powerfull');
			var nullVar = TAO_MATCHING.VariableFactory.create (null);
			ok (str1 != null, 'Variable not null');
			equals (str1.getType(), 'string', 'Right type');
			equals (str1.getValue(), 'TAO', 'Right value');
			ok (str1.match(str1), 'Match itself');
			ok (!str1.match(str2), 'Does not match a different string variable');
			ok (str1.equal(str1), 'It is equal to itself');
			ok (!str1.equal(str2), 'It is not equal to a different string variable');
			ok (!str1.equal(nullVar), 'It is not equal to a null variable');
		});
		
		test("Test the VariableFactory (list [in QTI Pair])", function() {
			var list1 = TAO_MATCHING.VariableFactory.create (["TAO", "Test Assisté par Ordinateur"]);
			var list2 = TAO_MATCHING.VariableFactory.create (["CBA", "Computer Based Assessment"]);
			var list3 = TAO_MATCHING.VariableFactory.create (["CBA"]);
			var list4 = TAO_MATCHING.VariableFactory.create (["CBA", "Computer Based Assessment", "yeah"]);
			var list5 = TAO_MATCHING.VariableFactory.create ([]);
			var varNull = TAO_MATCHING.VariableFactory.create (null);
			ok (list1 != null, 'Variable not null');
			equals (list1.getType(), 'list', 'Right type');
			var tmpValue = list1.getValue();
			equals (tmpValue[0].getValue(),  'TAO', 'Right value');
			equals (tmpValue[1].getValue(), 'Test Assisté par Ordinateur', 'Right value');
			ok (list1.match(list1), 'Match itself');
			ok (!list1.match(list2), 'Does not match a different list');
			ok (!list1.match(list3), 'Does not match a different list');
			ok (!list1.match(list4), 'Does not match a different list');
			ok (!list1.match(list5), 'Does not match a different list');
		});
		
		test("Test the VariableFactory (tuple [in QTI DirectedPair])", function() {
			var tuple1 = TAO_MATCHING.VariableFactory.create ({"0":"TAO", "1":"it\'s so powerfull"});
			var tuple2 = TAO_MATCHING.VariableFactory.create ({"0":"Yeahhh", "1":"it\'s true"});
			var tuple3 = TAO_MATCHING.VariableFactory.create ({"0":"TAO"});
			var tuple4 = TAO_MATCHING.VariableFactory.create ({"0":"TAO", "1":"it\'s so powerfull", "2":"yeah"});
			ok (tuple1 != null, 'Variable not null');
			equals (tuple1.getType(), 'tuple', 'Right type');
			var tmpValue = tuple1.getValue ();
			equals (tmpValue[0].getValue(), 'TAO', 'Right value');
			equals (tmpValue[1].getValue(), 'it\'s so powerfull', 'Right value');
			ok (tuple1.match(tuple1), 'Match itself');
			ok (!tuple1.match(tuple2), 'Does not match a different tuple');
			ok (!tuple1.match(tuple3), 'Does not match a different tuple');
			ok (!tuple1.match(tuple4), 'Does not match a different tuple');
		});
		
		test("Test the VariableFactory (list of list [in QTI Multiple Pair])", function() {
			var listList1 = TAO_MATCHING.VariableFactory.create ([["A", "B"], ["C", "D"], ["E", "F"]]);
			var listList2 = TAO_MATCHING.VariableFactory.create ([["B", "A"], ["D", "C"], ["F", "E"]]);
			var listList3 = TAO_MATCHING.VariableFactory.create ([["A", "B"], ["C", "D"]]);
			var listList4 = TAO_MATCHING.VariableFactory.create ([["A", "B"], ["C", "D"], ["E", "F"], ["G", "H"]]);
			var listList5 = TAO_MATCHING.VariableFactory.create ([]);
			var listList6 = TAO_MATCHING.VariableFactory.create ([["A", "B"], ["C", "D"], ["E", "F"], ["A", "B"], ["C", "D"], ["E", "F"]]);
			ok (listList1 != null, 'Variable not null');
			equals (listList1.getType(), 'list', 'Right type');
			ok (listList1.match(listList1), 'Match itself');
			ok (listList1.match(listList2), 'Match a different list with the same content but inversed');
			ok (!listList1.match(listList3), 'Does not match a different list');
			ok (!listList1.match(listList4), 'Does not match a different list');
			ok (!listList1.match(listList5), 'Does not match a different list');
		});
		
		test("Test the VariableFactory (list of tuple [in QTI Multiple DirectedPair])", function() {
			var listTuple1 = TAO_MATCHING.VariableFactory.create ([{"0":"A", "1":"B"}, {"0":"C", "1":"D"}, {"0":"E", "1":"F"}]);
			var listTuple2 = TAO_MATCHING.VariableFactory.create ([{"0":"B", "1":"A"}, {"0":"D", "1":"C"}, {"0":"F", "1":"E"}]);
			var listTuple3 = TAO_MATCHING.VariableFactory.create ([{"0":"A", "1":"B"}, {"0":"C", "1":"D"}]);
			var listTuple4 = TAO_MATCHING.VariableFactory.create ([{"0":"A", "1":"B"}, {"0":"C", "1":"D"}, {"0":"E", "1":"F"}, {"0":"G", "1":"H"}]);
			var listTuple5 = TAO_MATCHING.VariableFactory.create ([]);
			var listTuple6 = TAO_MATCHING.VariableFactory.create ([{"0":"A", "1":"B"}, {"0":"C", "1":"D"}, {"0":"E", "1":"F"}, {"0":"A", "1":"B"}, {"0":"C", "1":"D"}, {"0":"E", "1":"F"}]);
			ok (listTuple1 != null, 'Variable not null');
			equals (listTuple1.getType(), 'list', 'Right type');
			ok (listTuple1.match(listTuple1), 'Match itself');
			ok (!listTuple1.match(listTuple2), 'Does not match a different listTuple');
			ok (!listTuple1.match(listTuple3), 'Does not match a different listTuple');
			ok (!listTuple1.match(listTuple4), 'Does not match a different listTuple');
			ok (!listTuple1.match(listTuple5), 'Does not match a different listTuple');
		});
		
		test("map list of list [in QTI list of pair]", function() {
			var map1 = new TAO_MATCHING.Map ([{"key":["A", "B"], "value":1}, {"key":["C", "D"], "value":0.5}, {"key":["E", "F"], "value":0.2}]);
			var listList1 = TAO_MATCHING.VariableFactory.create ([["A", "B"], ["C", "D"], ["E", "F"]]);
			var listList2 = TAO_MATCHING.VariableFactory.create ([["B", "A"], ["D", "C"], ["F", "E"]]);
			var listList3 = TAO_MATCHING.VariableFactory.create ([["A", "B"], ["C", "D"]]);
			var listList4 = TAO_MATCHING.VariableFactory.create ([["A", "B"], ["C", "D"], ["E", "F"], ["G", "H"]]);
			var listList5 = TAO_MATCHING.VariableFactory.create ([]);
			var listList6 = TAO_MATCHING.VariableFactory.create ([["A", "B"], ["C", "D"], ["E", "F"], ["A", "B"], ["C", "D"], ["E", "F"]]);
			equals (map1.map(listList1), 1.7, 'Right mapping');
			equals (map1.map(listList2), 1.7, 'Right mapping');
			equals (map1.map(listList3), 1.5, 'Right mapping');
			equals (map1.map(listList4), 1.7, 'Right mapping');
			equals (map1.map(listList5), 0.0, 'Right mapping');
			equals (map1.map(listList6), 1.7, 'Right mapping');
		});
		
		test("map list of tuple [in QTI list of directedpair]", function() {
			var map2 = new TAO_MATCHING.Map ([{"key":{"0":"A", "1":"B"}, "value":1}, {"key":{"0":"C", "1":"D"}, "value":0.5}, {"key":{"0":"E", "1":"F"}, "value":0.2}]);
			var listTuple1 = TAO_MATCHING.VariableFactory.create ([{"0":"A", "1":"B"}, {"0":"C", "1":"D"}, {"0":"E", "1":"F"}]);
			var listTuple2 = TAO_MATCHING.VariableFactory.create ([{"0":"B", "1":"A"}, {"0":"D", "1":"C"}, {"0":"F", "1":"E"}]);
			var listTuple3 = TAO_MATCHING.VariableFactory.create ([{"0":"A", "1":"B"}, {"0":"C", "1":"D"}]);
			var listTuple4 = TAO_MATCHING.VariableFactory.create ([{"0":"A", "1":"B"}, {"0":"C", "1":"D"}, {"0":"E", "1":"F"}, {"0":"G", "1":"H"}]);
			var listTuple5 = TAO_MATCHING.VariableFactory.create ([]);
			var listTuple6 = TAO_MATCHING.VariableFactory.create ([{"0":"A", "1":"B"}, {"0":"C", "1":"D"}, {"0":"E", "1":"F"}, {"0":"A", "1":"B"}, {"0":"C", "1":"D"}, {"0":"E", "1":"F"}]);
			equals (map2.map(listTuple1), 1.7, 'Right mapping');
			equals (map2.map(listTuple2), 0.0, 'Right mapping');
			equals (map2.map(listTuple3), 1.5, 'Right mapping');
			equals (map2.map(listTuple4), 1.7, 'Right mapping');
			equals (map2.map(listTuple5), 0.0, 'Right mapping');
			equals (map2.map(listTuple6), 1.7, 'Right mapping');
		});
	
		test("template response processing match_correct", function() {
			var matching_param = {
				data : {
					corrects 	: [{"identifier":"RESPONSE", "value":"1"}]
					, outcomes	: [{"identifier":"SCORE", "type":"double"}]
					, rule		: TAO_MATCHING.RULE.MATCH_CORRECT
				}
			};
			matchingInit (matching_param);
			matchingSetResponses ([{"identifier":"RESPONSE", "value":"1"}]);
			matchingEvaluate ();
			outcomes = matchingGetOutcomes ();
			equals (outcomes["SCORE"]["value"], 1, 'Expected Score');
			
			matching_param = {
				data : {
					corrects 	: [{"identifier":"RESPONSE", "value":[{"0":"A", "1":"B"}, {"0":"C", "1":"D"}, {"0":"E", "1":"F"}]}]
					, outcomes	: [{"identifier":"SCORE", "type":"double"}]
					, rule		: TAO_MATCHING.RULE.MATCH_CORRECT
				}
			};
			matchingInit (matching_param);
			matchingSetResponses ([{"identifier":"RESPONSE", "value":[{"0":"A", "1":"B"}, {"0":"C", "1":"D"}, {"0":"E", "1":"F"}]}]);
			matchingEvaluate ();
			outcomes = matchingGetOutcomes ();
			equals (outcomes["SCORE"]["value"], 1, 'Expected Score');
		});

		test("template response processing mapResponse", function() {
			// string
			var matching_param = {
				data : {
					corrects 	: [{"identifier":"RESPONSE", "value":"Paris"}]
					, outcomes	: [{"identifier":"SCORE", "type":"double"}]
					, maps		: [{"identifier":"RESPONSE", "value":[{"key":"Paris", "value":1}, {"key":"paris", "value":0.9}]}]
					, rule		: TAO_MATCHING.RULE.MAP_RESPONSE
				}
			};
			matchingInit (matching_param);
			matchingSetResponses ([{"identifier":"RESPONSE", "value":"paris"}]);
			matchingEvaluate ();
			outcomes = matchingGetOutcomes ();
			equals (outcomes["SCORE"]["value"],  0.9, 'Expected Score');
			
			// list list (multiple pair)
			var matching_param = {
				data : {
					corrects 	: [{"identifier":"RESPONSE", "value":[["A", "B"], ["C", "D"], ["E", "F"]]}]
					, outcomes	: [{"identifier":"SCORE", "type":"double"}]
					, maps		: [{"identifier":"RESPONSE", "value":[{"key":["A", "B"], "value":1}, {"key":["C", "D"], "value":0.5}, {"key":["E", "F"], "value":0.2}]}]
					, rule		: TAO_MATCHING.RULE.MAP_RESPONSE
				}
			};
			matchingInit (matching_param);
			matchingSetResponses ([{"identifier":"RESPONSE", "value":[["A", "B"], ["C", "D"], ["E", "F"]]}]);
			matchingEvaluate ();
			outcomes = matchingGetOutcomes ();
			equals (outcomes["SCORE"]["value"],  1.7, 'Expected Score');
						
			// list list (multiple pair but with reversed result)
			var matching_param = {
				data : {
					corrects 	: [{"identifier":"RESPONSE", "value":[["A", "B"], ["C", "D"], ["E", "F"]]}]
					, outcomes	: [{"identifier":"SCORE", "type":"double"}]
					, maps		: [{"identifier":"RESPONSE", "value":[{"key":["A", "B"], "value":1}, {"key":["C", "D"], "value":0.5}, {"key":["E", "F"], "value":0.2}]}]
					, rule		: TAO_MATCHING.RULE.MAP_RESPONSE
				}
			};
			matchingInit (matching_param);
			matchingSetResponses ([{"identifier":"RESPONSE", "value":[["B", "A"], ["D", "C"], ["F", "E"]]}]);
			matchingEvaluate ();
			outcomes = matchingGetOutcomes ();
			equals (outcomes["SCORE"]["value"],  1.7, 'Expected Score');
			
			// list tuple (multiple directedpair)
			var matching_param = {
				data : {
					corrects 	: [{"identifier":"RESPONSE", "value":[{"0":"A", "1":"B"}, {"0":"C", "1":"D"}, {"0":"E", "1":"F"}]}]
					, outcomes	: [{"identifier":"SCORE", "type":"double"}]
					, maps		: [{"identifier":"RESPONSE", "value":[{"key":{"0":"A", "1":"B"}, "value":1}, {"key":{"0":"C", "1":"D"}, "value":0.5}, {"key":{"0":"E", "1":"F"}, "value":0.2}]}]
					, rule		: TAO_MATCHING.RULE.MAP_RESPONSE
				}
			};
			matchingInit (matching_param);
			matchingSetResponses ([{"identifier":"RESPONSE", "value":[{"0":"A", "1":"B"}, {"0":"C", "1":"D"}, {"0":"E", "1":"F"}]}]);
			matchingEvaluate ();
			outcomes = matchingGetOutcomes ();
			equals (outcomes["SCORE"]["value"],  1.7, 'Expected Score');
	
			// list tuple (multiple directedpair reversed pair)
			var matching_param = {
				data : {
					corrects 	: [{"identifier":"RESPONSE", "value":[{"0":"A", "1":"B"}, {"0":"C", "1":"D"}, {"0":"E", "1":"F"}]}]
					, outcomes	: [{"identifier":"SCORE", "type":"double"}]
					, maps		: [{"identifier":"RESPONSE", "value":[{"key":{"0":"A", "1":"B"}, "value":1}, {"key":{"0":"C", "1":"D"}, "value":0.5}, {"key":{"0":"E", "1":"F"}, "value":0.2}]}]
					, rule		: TAO_MATCHING.RULE.MAP_RESPONSE
				}
			};
			matchingInit (matching_param);
			matchingSetResponses ([{"identifier":"RESPONSE", "value":[{"0":"B", "1":"A"}, {"0":"D", "1":"C"}, {"0":"F", "1":"E"}]}]);
			matchingEvaluate ();
			outcomes = matchingGetOutcomes ();
			equals (outcomes["SCORE"]["value"],  0, 'Expected Score');
		});

        test("test operator : and]", function() {
            // Simple true/true
            var matching_param = {
                data : {
                    outcomes  : [{"identifier":"SCORE", "type":"double"}]
                    , rule      : 'if (and(null, true, true)){ setOutcomeValue("SCORE", 1); } else { setOutcomeValue("SCORE", 0); }'
                }
            };
            matchingInit (matching_param);
            matchingEvaluate ();
            outcomes = matchingGetOutcomes ();
            equals (outcomes["SCORE"]["value"],  1, 'Expected score');
            
            // and on a single matching
            var matching_param = {
                data : {
                    corrects    : [{"identifier":"RESPONSE", "value":[{"0":"A", "1":"B"}, {"0":"C", "1":"D"}, {"0":"E", "1":"F"}]}]
                    , outcomes  : [{"identifier":"SCORE", "type":"double"}]
                    , rule      : 'if ( and (null, match(null, getResponse("RESPONSE"), getCorrect("RESPONSE")))){ setOutcomeValue("SCORE", 1); } else { setOutcomeValue("SCORE", 0); }'
                }
            };
            matchingInit (matching_param);
            matchingSetResponses ([{"identifier":"RESPONSE", "value":[{"0":"A", "1":"B"}, {"0":"C", "1":"D"}, {"0":"E", "1":"F"}]}]);
            matchingEvaluate ();
            outcomes = matchingGetOutcomes ();
            equals (outcomes["SCORE"]["value"],  1, 'Expected score');
            
            // and with a true and matching
            var matching_param = {
                data : {
                    corrects    : [{"identifier":"RESPONSE", "value":[{"0":"A", "1":"B"}, {"0":"C", "1":"D"}, {"0":"E", "1":"F"}]}]
                    , outcomes  : [{"identifier":"SCORE", "type":"double"}]
                    , rule      : 'if ( true, true, and (null, match(null, getResponse("RESPONSE"), getCorrect("RESPONSE")))){ setOutcomeValue("SCORE", 1); } else { setOutcomeValue("SCORE", 0); }'
                }
            };
            matchingInit (matching_param);
            matchingSetResponses ([{"identifier":"RESPONSE", "value":[{"0":"A", "1":"B"}, {"0":"C", "1":"D"}, {"0":"E", "1":"F"}]}]);
            matchingEvaluate ();
            outcomes = matchingGetOutcomes ();
            equals (outcomes["SCORE"]["value"],  1, 'Expected score');
        });

        // CREATE VARIABLE OPERATOR (the operator used to map the baseValue QTI Expression)
        test("test operator : createVariable (*) (QTI basevalue)", function() {
            var matching = new TAO_MATCHING.Matching ();
            
            // String
            var str1 = matching.createVariable(null, 'Driver A');
            ok (str1 != null, 'Create a not null String Variable');
            equals (str1.getType(), 'string', 'String Variable get the right type');
            ok (str1.match(str1), 'String Variable matches itself');
            var str2 = matching.createVariable(null, 'Driver B');
            ok (!str1.match(str2), 'String Variable does not match a different String Variable');
            
            // Float
            var float1 = matching.createVariable(null, 3.1415);
            ok (float1 != null, 'Create a not null Float Variable');
            equals (float1.getType(), 'number', 'Float Variable get the right type');
            ok (float1.match(float1), 'Float Variable matches itself');
            var float2 = matching.createVariable(null, 3.0);
            ok (!str1.match(str2), 'Float Variable does not match a different Float Variable');
            
            // Integer
            var int1 = matching.createVariable(null, 123456);
            ok (int1 != null, 'Create a not null Integer Variable');
            equals (int1.getType(), 'number', 'Integer Variable get the right type');
            ok (int1.match(int1), 'Integer Variable matches itself');
            var int2 = matching.createVariable(null, 6543210);
            ok (!int1.match(int2), 'Integer Variable does not match a different Integer Variable');
            
            // Boolean
            var bool1 = matching.createVariable(null, true);
            ok (bool1 != null, 'Create a not null Boolean Variable');
            equals (bool1.getType(), 'boolean', 'Boolean Variable get the right type');
            ok (bool1.match(bool1), 'Boolean Variable matches itself');
            var bool2 = matching.createVariable(null, false);
            ok (!int1.match(bool2), 'Boolean Variable does not match a different Boolean Variable');         
            
            // List
            var list1 = matching.createVariable (null, ["TAO", "Test Assisté par Ordinateur"]);
            equals (list1.getType(), 'list', 'List Variable get the right type');
            ok (list1.match(list1), 'List Variable  matches itself');
            var list2 = matching.createVariable (null, ["CBA", "Computer Based Assessment"]);
            ok (!list1.match(list2), 'List Variable does not match a different List Variable');
            
            // Tuple
            var tuple1 = matching.createVariable({"type":"tuple"}, matching.createVariable ({"type":"string"}, "DriverC"), matching.createVariable ({"type":"string"}, "DriverB"), matching.createVariable ({"type":"string"}, "DriverA"));
            ok (tuple1 != null, 'Create a not null Tuple Variable');
            equals (tuple1.getType(), 'tuple', 'Tuple Variable get the right type');
            ok (tuple1.match(tuple1), 'Tuple Variable matches itself');
            var tuple2 = matching.createVariable({"type":"tuple"}, matching.createVariable ({"type":"string"}, "DriverA"), matching.createVariable ({"type":"string"}, "DriverB"), matching.createVariable ({"type":"string"}, "DriverC"));
            ok (!tuple1.match(tuple2), 'Tuple Variable does not match a different Tuple Variable');          
        });
        
        // GT OPERATOR
        test("test operator : gt", function() {
            var matching = new TAO_MATCHING.Matching ();
            ok (matching.gt (null, 3, 2), 'Expected value');
            ok (matching.gt (null, 3.14, 1.66), 'Expected value');
            ok (!matching.gt (null, 3, 3), 'Expected value');
            ok (!matching.gt (null, 2, 3), 'Expected value');
            try {
                matching.gt (null, [3], 2)
                ok (false, 'Expect exception');
            }catch (e){
                ok (true, 'Expected exception');
            }
        });
        
        // LT OPERATOR
        test("test operator : lt", function() {
            var matching = new TAO_MATCHING.Matching ();
            ok (matching.lt (null, 2, 3), 'Expected value');
            ok (matching.lt (null, 1.66, 3.14), 'Expected value');
            ok (!matching.lt (null, 3, 3), 'Expected value');
            ok (!matching.lt (null, 3, 2), 'Expected value');
            try {
                matching.lt (null, [2], 3)
                ok (false, 'Expect exception');
            }catch (e){
                ok (true, 'Expected exception');
            }
        });
        
        // GTE OPERATOR
        test("test operator : gte", function() {
            var matching = new TAO_MATCHING.Matching ();
            ok (matching.gte (null, 3, 2), 'Expected value');
            ok (matching.gte (null, 3.14, 1.66), 'Expected value');
            ok (matching.gte (null, 3, 3), 'Expected value');
            ok (!matching.gte (null, 2, 3), 'Expected value');
            try {
                matching.gte (null, [3], 2)
                ok (false, 'Expect exception');
            }catch (e){
                ok (true, 'Expected exception');
            }
        });
        
        // LTE OPERATOR
        test("test operator : lte", function() {
            var matching = new TAO_MATCHING.Matching ();
            ok (matching.lte (null, 2, 3), 'Expected value');
            ok (matching.lte (null, 1.66, 3.14), 'Expected value');
            ok (matching.lte (null, 3, 3), 'Expected value');
            ok (!matching.lte (null, 3, 2), 'Expected value');
            try {
                matching.lte (null, [2], 3)
                ok (false, 'Expect exception');
            }catch (e){
                ok (true, 'Expected exception');
            }
        });
        
        // SUM OPERATOR
        test("test operator : sum", function() {
            var matching = new TAO_MATCHING.Matching ();
            equals (matching.sum (null, 2, 3), 5, 'Expected value');
            equals (matching.sum (null, matching.createVariable(null, 2), 3), 5, 'Expected value');
            equals (matching.sum (null, matching.createVariable(null, 2), null), null, 'Expected value');
        });
        
        // SUBSTRACT OPERATOR
        test("test operator : subtract", function() {
            var matching = new TAO_MATCHING.Matching ();
            equals (matching.subtract (null, 3, 2), 1, 'Expected value');
            equals (matching.subtract (null, matching.createVariable(null, 3), 2), 1, 'Expected value');
            equals (matching.subtract (null, matching.createVariable(null, 3), null), null, 'Expected value');
        });
        
        // PRODUCT OPERATOR
        test("test operator : product", function() {
            var matching = new TAO_MATCHING.Matching ();
            equals (matching.product (null, 3, 2), 6, 'Expected value');
            equals (matching.product (null, matching.createVariable(null, 3), 6), 18, 'Expected value');
            equals (matching.product (null, matching.createVariable(null, 3), null), null, 'Expected value');
        });
        
        // DIVIDE OPERATOR
        test("test operator : divide", function() {
            var matching = new TAO_MATCHING.Matching ();
            equals (matching.divide (null, 3, 2), 1.5, 'Expected value');
            equals (matching.divide (null, matching.createVariable(null, 3), 6), 0.5, 'Expected value');
            equals (matching.divide (null, matching.createVariable(null, 3), null), null, 'Expected value');
            equals (matching.divide (null, matching.createVariable(null, 3), 0), null, 'Expected value');
        });
        
        // ROUND OPERATOR
        test("test operator : round", function() {
            var matching = new TAO_MATCHING.Matching ();
            equals (matching.round (null, 3.4), 3, 'Expected value');
            equals (matching.round (null, matching.createVariable(null, 3.5)), 4, 'Expected value');
            equals (matching.round (null, null), null, 'Expected value');
        });
        
        // INTEGER DIVIDE OPERATOR
        test("test operator : integerDivide", function() {
            var matching = new TAO_MATCHING.Matching ();
            equals (matching.integerDivide (null, 3, 2.3), 1, 'Expected value');
            equals (matching.integerDivide (null, matching.createVariable(null, 3), 2), 2, 'Expected value');
            equals (matching.integerDivide (null, matching.createVariable(null, 3), null), null, 'Expected value');
            equals (matching.integerDivide (null, matching.createVariable(null, 3), 0), null, 'Expected value');
        });
        
        // NOT OPERATOR
        test("test operator : not", function() {
            var matching = new TAO_MATCHING.Matching ();
            equals (matching.not (null, true), false, 'Expected value');
            equals (matching.not (null, matching.createVariable(null, false)), true, 'Expected value');
            equals (matching.not (null, null), null, 'Expected value');
        });
        
        // OR OPERATOR
        test("test operator : or", function() {
            var matching = new TAO_MATCHING.Matching ();
            equals (matching.or (null, true), true, 'Expected value');
            equals (matching.or (null, false), false, 'Expected value');
            equals (matching.or (null, true, false), true, 'Expected value');
            equals (matching.or (null, matching.createVariable(null, false)), false, 'Expected value');
            equals (matching.or (null, matching.createVariable(null, true)), true, 'Expected value');
            equals (matching.or (null, matching.createVariable(null, true, false)), true, 'Expected value');
            equals (matching.or (null, matching.createVariable(null, false), false), false, 'Expected value');
            equals (matching.or (null, null), null, 'Expected value');
        });
        
        // CONTAINS OPERATOR
        test("test operator : contains", function() {
            var matching = new TAO_MATCHING.Matching ();
            
            // List
            equals (matching.contains (null
                , matching.createVariable(null, [1,2,3,4])
                , matching.createVariable(null, [1,2,3]))
            , true, 'Expected value');
            // List
            equals (matching.contains (null
                , matching.createVariable(null, [1,2,3,4])
                , matching.createVariable(null, 3))
            , true, 'Expected value');
            // List of list
            equals (matching.contains ({"needleType":"custom"}
                , matching.createVariable(null, [[1,2],[3,4]])
                , matching.createVariable(null, [3,4]))
            , true, 'Expected value');
            // List of list
            equals (matching.contains ({"needleType":"custom"}
                , matching.createVariable(null, [[1,2],[3,4]])
                , matching.createVariable(null, [3]))
            , false, 'Expected value');
            // List of list
            equals (matching.contains (null
                , matching.createVariable(null, [[1,2],[3,4]])
                , matching.createVariable(null, 3))
            , false, 'Expected value');
            // List of list
            equals (matching.contains ({"needleType":"custom"}
                , matching.createVariable(null, [[1,2],[3,4]])
                , matching.createVariable(null, [4,5]))
            , false, 'Expected value');
            // Tuple
            equals (matching.contains (null
                , matching.createVariable(null, {0:1, 1:2, 2:3, 3:4})
                , matching.createVariable(null, {0:1, 3:4}))
            , true, 'Expected value');
            // List of tuple
            equals (matching.contains ({"needleType":"custom"}
                , matching.createVariable(null, [{0:1, 1:2},{0:3, 1:4}])
                , matching.createVariable(null, {0:3, 1:4}))
            , true, 'Expected value');
        });
        
        // RANDOM INTEGER OPERATOR
        test("test operator : randomInteger", function() {
            var matching = new TAO_MATCHING.Matching ();
            var isNull = false;
            var correctRange = true;
            var i =0;
            
            while (!isNull && correctRange && i<100) {
                var rand = matching.randomInteger ({"min":0, "max":9});
                isNull = rand === null;
                correctRang = rand>=0 && rand<=9;
                i++;
            }
            
            ok (!isNull, 'Random value not null ['+rand+']');
            ok (correctRange, 'Random value >= min & <= max');
        });
        
        // RANDOM FLOAT OPERATOR
        test("test operator : randomInteger", function() {
            var matching = new TAO_MATCHING.Matching ();
            var isNull = false;
            var correctRange = true;
            var i =0;
            
            while (!isNull && correctRange && i<100) {
                var rand = matching.randomFloat ({"min":0.00, "max":9.99});
                isNull = rand === null;
                correctRang = rand>=0.00 && rand<=9.99;
                i++;
            }
            
            ok (!isNull, 'Random value not null ['+rand+']');
            ok (correctRange, 'Random value >= min & <= max');
        });
        
        // REMOTE MATCHING CHOICE.XML
        asyncTest ('Remote Matching : Choice.xml', function () {
            var matching_param = {
                "url" : "<?=ROOT_URL?>/taoItems/Matching/evaluateDebug"
                , "params" : {
                    "item_path" : '<?=PATH_SAMPLE?>choice.xml'
                }
                , "options" : {
                    "evaluateCallback" : function (outcomes) {
                        equals (outcomes.SCORE.value, 1, 'Expected Value');
                        start();
                    }
                }
            };
            matchingInit (matching_param);
            matchingSetResponses ([{"identifier":"RESPONSE", "value":"ChoiceA"}]);
            matchingEvaluate ();
        });

        // REMOTE MATCHING ASSOCIATE.XML
        asyncTest ('Remote Matching : Associate.xml', function () {
            matching_param = {
                "url" : "<?=ROOT_URL?>/taoItems/Matching/evaluateDebug"
                , "params" : {
                    "item_path" : '<?=PATH_SAMPLE?>associate.xml'
                }
                , "options" : {
                    "evaluateCallback" : function (outcomes) {
                        equals (outcomes.SCORE.value, 4, 'Expected Value');
                        start();
                    }
                }
            };
            matchingInit (matching_param);
            matchingSetResponses ([{"identifier":"RESPONSE", "value":[['A', 'P'], ['C','M'], ['D','L']]}]);
            matchingEvaluate ();
        });

        // REMOTE MATCHING CHOICE MULTIPLE.XML
        asyncTest ('Remote Matching : ChoiceMultiple.xml', function () {
            matching_param = {
                "url" : "<?=ROOT_URL?>/taoItems/Matching/evaluateDebug"
                , "params" : {
                    "item_path" : '<?=PATH_SAMPLE?>choice_multiple.xml'
                }
                , "options" : {
                    "evaluateCallback" : function (outcomes) {
                        equals (outcomes.SCORE.value, 2, 'Expected Value');
                        start();
                    }
                }
            };
            matchingInit (matching_param);
            matchingSetResponses ([{"identifier":"RESPONSE", "value":['H', 'O']}]);
            matchingEvaluate ();
        });

        // REMOTE MATCHING GAP MATCH.XML
        asyncTest ('Remote Matching : GapMatch.xml', function () {
            matching_param = {
                "url" : "<?=ROOT_URL?>/taoItems/Matching/evaluateDebug"
                , "params" : {
                    "item_path" : '<?=PATH_SAMPLE?>gap_match.xml'
                }
                , "options" : {
                    "evaluateCallback" : function (outcomes) {
                        equals (outcomes.SCORE.value, 3, 'Expected Value');
                        start();
                    }
                }
            };
            matchingInit (matching_param);
            matchingSetResponses ([{"identifier":"RESPONSE", "value":[{"0":"W" , "1":"G1"}, {"0":"Su" , "1":"G2"}]}]);
            matchingEvaluate ();
        });

        // REMOTE MATCHING MATCH.XML
        asyncTest ('Remote Matching : Match.xml', function () {
            matching_param = {
                "url" : "<?=ROOT_URL?>/taoItems/Matching/evaluateDebug"
                , "params" : {
                    "item_path" : '<?=PATH_SAMPLE?>match.xml'
                }
                , "options" : {
                    "evaluateCallback" : function (outcomes) {
                        equals (outcomes.SCORE.value, 3, 'Expected Value');
                        start();
                    }
                }
            };
            matchingInit (matching_param);
            matchingSetResponses ([{ "identifier": "RESPONSE", "value": [{ "0": "C", "1": "R" }, {"0": "D", "1": "M" }, { "0": "L", "1": "M" }, { "0": "P", "1": "T" }] }]);
            matchingEvaluate ();
        });

        // REMOTE MATCHING ORDER.XML
        asyncTest ('Remote Matching : Order.xml', function () {
            matching_param = {
                "url" : "<?=ROOT_URL?>/taoItems/Matching/evaluateDebug"
                , "params" : {
                    "item_path" : '<?=PATH_SAMPLE?>order.xml'
                }
                , "options" : {
                    "evaluateCallback" : function (outcomes) {
                        equals (outcomes.SCORE.value, 1, 'Expected Value');
                        start();
                    }
                }
            };
            matchingInit (matching_param);
            matchingSetResponses ([{"identifier":"RESPONSE", "value":{"0":"DriverC", "1":"DriverA", "2":"DriverB"}}]);
            matchingEvaluate ();
        });

        // REMOTE MATCHING HOTTEXT.XML
        asyncTest ('Remote Matching : Hottext.xml', function () {
            matching_param = {
                "url" : "<?=ROOT_URL?>/taoItems/Matching/evaluateDebug"
                , "params" : {
                    "item_path" : '<?=PATH_SAMPLE?>hottext.xml'
                }
                , "options" : {
                    "evaluateCallback" : function (outcomes) {
                        equals (outcomes.SCORE.value, 1, 'Expected Value');
                        start();
                    }
                }
            };
            matchingInit (matching_param);
            matchingSetResponses ([{"identifier":"RESPONSE", "value":"2"}]);
            matchingEvaluate ();
        });

        // REMOTE MATCHING INLINE CHOICE.XML
        asyncTest ('Remote Matching : InlineChoice.xml', function () {
            matching_param = {
                "url" : "<?=ROOT_URL?>/taoItems/Matching/evaluateDebug"
                , "params" : {
                    "item_path" : '<?=PATH_SAMPLE?>inline_choice.xml'
                }
                , "options" : {
                    "evaluateCallback" : function (outcomes) {
                        equals (outcomes.SCORE.value, 1, 'Expected Value');
                        start();
                    }
                }
            };
            matchingInit (matching_param);
            matchingSetResponses ([{"identifier":"RESPONSE", "value":"Y"}]);
            matchingEvaluate ();
        });

        // REMOTE MATCHING TEXT ENTRY.xml
        asyncTest ('Remote Matching : TextEntry.xml', function () {
            matching_param = {
                "url" : "<?=ROOT_URL?>/taoItems/Matching/evaluateDebug"
                , "params" : {
                    "item_path" : '<?=PATH_SAMPLE?>text_entry.xml'
                }
                , "options" : {
                    "evaluateCallback" : function (outcomes) {
                        equals (outcomes.SCORE.value, 0.5, 'Expected Value');
                        start();
                    }
                }
            };
            matchingInit (matching_param);
            matchingSetResponses ([{"identifier":"RESPONSE", "value":"york"}]);
            matchingEvaluate ();
        });

        // REMOTE MATCHING TEXT ORKNEY1.xml
        asyncTest ('Remote Matching : Orkney1.xml', function () {
            matching_param = {
                "url" : "<?=ROOT_URL?>/taoItems/Matching/evaluateDebug"
                , "params" : {
                    "item_path" : '<?=PATH_SAMPLE?>orkney1.xml'
                }
                , "options" : {
                    "evaluateCallback" : function (outcomes) {
                        equals (outcomes.SCORE.value, 1, 'Expected Value');
                        start();
                    }
                }
            };
            matchingInit (matching_param);
            matchingSetResponses ([{"identifier":"RESPONSE", "value":"T"}]);
            matchingEvaluate ();
        });

        // REMOTE MATCHING CUSTOM MATCH .xml
        asyncTest ('Remote Matching : CustomMatch.xml', function () {
            matching_param = {
                "url" : "<?=ROOT_URL?>/taoItems/Matching/evaluateDebug"
                , "params" : {
                    "item_path" : '<?=PATH_SAMPLE?>custom_rule/custom_match_choice.xml'
                }
                , "options" : {
                    "evaluateCallback" : function (outcomes) {
                        equals (outcomes.SCORE.value, 1, 'Expected Value');
                        start();
                    }
                }
            };
            matchingInit (matching_param);
            matchingSetResponses ([{"identifier":"RESPONSE", "value":"ChoiceA"}]);
            matchingEvaluate ();
        });

        // REMOTE PARSING / CLIENT MATCHING CUSTOM MATCH .xml
        asyncTest("Remote Parsing / Client Matching : Custom Match", function(){
            var item_path = '<?=PATH_SAMPLE?>custom_rule/custom_match_choice.xml';
            var url = "<?=ROOT_URL?>/taoItems/Matching/getItemMatchingDataDebug?item_path="+encodeURIComponent(item_path);

            $.ajax ({
                url : url
                , type : 'GET'
                , async : true
                , dataType : 'json'
                , success   : function (data){
                    var matching_param = {
                        "data" : data
                        , "options" : {
                            "evaluateCallback" : function (outcomes) {
                                equals (outcomes.SCORE.value, 1, 'Expected Value');
                                start();
                            }
                        }
                    };
                    matchingInit (matching_param);
                    matchingSetResponses ([{"identifier":"RESPONSE", "value":"ChoiceA"}]);
                    matchingEvaluate ();
                }
            });            
        });

        // REMOTE MATCHING CUSTOM MAP RESPONSE .xml
        asyncTest ('Remote Matching : CustomMapResponse.xml', function () {
            matching_param = {
                "url" : "<?=ROOT_URL?>/taoItems/Matching/evaluateDebug"
                , "params" : {
                    "item_path" : '<?=PATH_SAMPLE?>custom_rule/custom_map_response_choice_multiple.xml'
                }
                , "options" : {
                    "evaluateCallback" : function (outcomes) {
                        equals (outcomes.SCORE.value, 2, 'Expected Value');
                        start();
                    }
                }
            };
            matchingInit (matching_param);
            matchingSetResponses ([{"identifier":"RESPONSE", "value":["H", "O"]}]);
            matchingEvaluate ();
        });

        // REMOTE PARSING / CLIENT MATCHING MAP RESPONSE .xml
        asyncTest("Remote Parsing / Client Matching : Custom Map Response", function(){
            var item_path = '<?=PATH_SAMPLE?>custom_rule/custom_map_response_choice_multiple.xml';
            var url = "<?=ROOT_URL?>/taoItems/Matching/getItemMatchingDataDebug?item_path="+encodeURIComponent(item_path);

            $.ajax ({
                url : url
                , type : 'GET'
                , async : true
                , dataType : 'json'
                , success   : function (data){
                    var matching_param = {
                        "data" : data
                        , "options" : {
                            "evaluateCallback" : function (outcomes) {
                                equals (outcomes.SCORE.value, 2, 'Expected Value');
                                start();
                            }
                        }
                    };
                    matchingInit (matching_param);
                    matchingSetResponses ([{"identifier":"RESPONSE", "value":["H", "O"]}]);
                    matchingEvaluate ();
                }
            });            
        });

        // REMOTE MATCHING CUSTOM PARTIAL SCORING.xml
        asyncTest ('Remote Matching : CustomPartialScoring.xml', function () {
            matching_param = {
                "url" : "<?=ROOT_URL?>/taoItems/Matching/evaluateDebug"
                , "params" : {
                    "item_path" : '<?=PATH_SAMPLE?>custom_rule/custom_order_partial_scoring.xml'
                }
                , "options" : {
                    "evaluateCallback" : function (outcomes) {
                        equals (outcomes.SCORE.value, 1 , 'Expected Value');
                        start();
                    }
                }
            };
            matchingInit (matching_param);
            matchingSetResponses ([{"identifier":"RESPONSE", "value":{"0":"DriverC", "1":"DriverB", "2":"DriverA"}}]);
            matchingEvaluate ();
        });

        // REMOTE PARSING / CLIENT MATCHING PARTIAL SCORING .xml
        asyncTest("Remote Parsing / Client Matching : Custom Partial Scoring", function(){
            var item_path = '<?=PATH_SAMPLE?>custom_rule/custom_order_partial_scoring.xml';
            var url = "<?=ROOT_URL?>/taoItems/Matching/getItemMatchingDataDebug?item_path="+encodeURIComponent(item_path);

            $.ajax ({
                url : url
                , type : 'GET'
                , async : true
                , dataType : 'json'
                , success   : function (data){
                    var matching_param = {
                        "data" : data
                        , "options" : {
                            "evaluateCallback" : function (outcomes) {
                                equals (outcomes.SCORE.value, 1, 'Expected Value');
                                start();
                            }
                        }
                    };
                    matchingInit (matching_param);
                    matchingSetResponses ([{"identifier":"RESPONSE", "value":{"0":"DriverC", "1":"DriverB", "2":"DriverA"}}]);
                    matchingEvaluate ();
                }
            });            
        });

        // REMOTE PARSING / CLIENT MATCHING PARTIAL all rules.xml
        asyncTest("Remote Parsing / Client Matching : Custom All Rules", function(){
            var item_path = '<?=PATH_SAMPLE?>custom_rule/custom_all_rules.xml';
            var url = "<?=ROOT_URL?>/taoItems/Matching/getItemMatchingDataDebug?item_path="+encodeURIComponent(item_path);

            $.ajax ({
                url : url
                , type : 'GET'
                , async : true
                , dataType : 'json'
                , success   : function (data){
                    var matching_param = {
                        "data" : data
                        , "options" : {
                            "evaluateCallback" : function (outcomes) {
                                equals (outcomes['SCORE_INT'].value, 1, "Expected value");
                                start();
                            }
                        }
                    };
                    matchingInit (matching_param);
                    matchingEvaluate ();
                }
            });            
        });

        // REMOTE MATCHING SHAKESPEAR.xml
        asyncTest ('Remote Matching : Shakespear.xml', function () {
            matching_param = {
                "url" : "<?=ROOT_URL?>/taoItems/Matching/evaluateDebug"
                , "params" : {
                    "item_path" : '<?=PATH_SAMPLE?>custom_rule/shakespeare.xml'
                }
                , "options" : {
                    "evaluateCallback" : function (outcomes) {
                        equals (outcomes.SCORE.value, 6 , 'Expected Value');
                        start();
                    }
                }
            };

            matchingInit (matching_param);
            matchingSetResponses ([
                {"identifier":"response_1", "value":"choice_1"}
                ,{"identifier":"response_2","value":"choice_4"}
                ,{"identifier":"response_3","value":"poet"}
                ,{"identifier":"response_4","value":"Bard of Avon"}
                ,{"identifier":"response_5","value":[{"0":"group_1","1":"choice_7"},{"0":"group_2","1":"choice_8"},{"0":"group_3","1":"choice_9"},{"0":"group_4","1":"choice_10"}]}
                ,{"identifier":"response_6","value":["choice_12","choice_13"]}
            ]);
            matchingEvaluate ();
        });

        // REMOTE PARSING / CLIENT MATCHING SHAKESPEAR .xml
        /*asyncTest("Remote Parsing / Client Matching : Custom Partial Scoring", function(){
            var item_path = '<?=PATH_SAMPLE?>custom_rule/shakespear.xml';
            var url = "<?=ROOT_URL?>/taoItems/Matching/getItemMatchingDataDebug?item_path="+encodeURIComponent(item_path);

            $.ajax ({
                url : url
                , type : 'GET'
                , async : true
                , dataType : 'json'
                , success   : function (data){
                    var matching_param = {
                        "data" : data
                        , "options" : {
                            "evaluateCallback" : function (outcomes) {
                                equals (outcomes.SCORE.value, 1, 'Expected Value');
                                start();
                            }
                        }
                    };
                    matchingInit (matching_param);
                    matchingSetResponses ([
                        {"identifier":"response_1", "value":"choice_1"}
                        , {"identifier":"response_2", "value":"choice_4"}
                    ]);
                    matchingEvaluate ();
                }
            });            
        });*/

	</script>
	
</head>
<body>
	<h1 id="qunit-header">QUnit Test Suite</h1>
	<h2 id="qunit-banner"></h2>
	<div id="qunit-testrunner-toolbar"></div>
	<h2 id="qunit-userAgent"></h2>
	<ol id="qunit-tests"></ol>
	<div id="qunit-fixture">test markup</div>
</body>
</html>
