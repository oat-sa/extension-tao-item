<?php

function pr ($msg){
	echo '<pre>';
	print_r ($msg);
	echo '</pre>';
}

class QTIVariable {
    public function __construct ( $identifier, $type ) {
		$this->setIdentifier ($identifier);
        $this->setType($type);
        $this->cardinality = 'yeahh';
        $this->values = array();
    }
	// Get the QtiVariable type
	public function getType (){
        return $this->type;
    }
	// Set the QtiVariable type
	public function setType ($type){
        $this->type = $type;
    } 
	// Set the QtiVariable identifier
	public function getIdentifier (){
        return ($this->identifier);
    } 
	// Set the QtiVariable identifier
	public function setIdentifier ($identifier){
        $this->identifier = $identifier;
    } 
	// Match the QtiVar with another
	// != type -> return false
	// != cardinality -> return false
    public function match ($qtiVar) {
        // Check if the vars to match have the same type
        if ($this->type != $qtiVar->type) 
            return false;
        // Check if the vars to match have the same cardinality
        else 
            if ($this->cardinality != $qtiVar->cardinality) 
                return false;
		
        return _match($this->values, $qtiVar->values);
    }
//	// Map the QtiVar with a QtiMap
//	, map : function ( qtiMap ) {
//        // Check if the vars to map and the qtiMap have the same type
//        if ($this->type != qtiMap.type) 
//            return false;
//		// Check that the Qti var is well a QTIMap type
//		if (!qtiMap instanceof QTIMap)
//			return false;
//
//	    console.log('Map the QtiVar with a QtiMap');
//	    console.log(this);
//	    console.log(qtiMap);
//		
//		return qtiMap.map (this);
//	}
	// Set values of the QtiVariable
	public function setValues ($values){
        $this->values = $values;
    }
	// To JSON
//	, toJson : function () {
//		var str = "{ \
//			identifier:'"+$this->identifier+"' \
//			, type:'"+$this->type+"' \
//			, cardinality:'' \
//			, values:[";
//		var strValues = '';
//		for (var i in $this->values){
//			strValues += strValues.length>0?',':'';
//			strValues += "'"+$this->values[i]+"'";	
//		}
//		str += strValues + "] \
//		}";
//		return str;
//	}
}

class QTIString
	extends QTIVariable {
    public function __construct ( $identifier, $type, $values ) {
    	parent::__construct ($identifier, $type);
    	$this->setValues ($values);
    }
}

function QTIVariableFactory ( $identifier, $type, $values, $options=null ){
    $variable = null;
    
	// If the container type is a map
	if (isset ($options) && $options->containerType == 'map') {
		$variable = new QTIMap ($identifier, $type, $values);
	
	// If the container type is a variable
	} else {
	    switch ($type) {
//	        case 'directedpair':
//	            $variable = new QTIPair ($identifier, $type, $values, true);
//	            break;
//	        case 'pair':
//	            $variable = new QTIPair ($identifier, $type, $values);
//	            break;
	        default:
	            $variable = new QTIString ($identifier, $type, $values);
	    }	
	}
    
    return $variable;
}
function getVariable ($name){
	global $variables;
    return $variables[$name];
}
function getCorrect ($name){
	global $corrects;
    return $corrects[$name];
}
function match ($qtiVar1, $qtiVar2){
    return $qtiVar1->match($qtiVar2);
}

// Array match
// != vars' length -> return false
function _match ($var1, $var2, $ordered=false){
    $result = true;       
	
    // expression 1 & expression 2 must have the same cardinality
    if (count($var1) != count($var2)) 
        return false;
    
    // expression 1 & expression 2 must have the same type
    
    // match var1 & var2
    if ($ordered) {
        for ($i=0; $i<count($var1); $i++) {
            if ($var1[i] != $var2[i]) 
                $result = false;
        }
    }
    else {
    	for ($i=0; $i<count($var1); $i++) {
            $result2 = false;
            for ($j=0; $j<count($var2); $j++) {
                if ($var1[$i] == $var2[$j]) {
                    $result2 = true;
                    break;
                }
            }
            if (!$result2){
                $result = false;
				break;	
			}            
        }
    }
    return $result;
}


function unserializedQTIVariables ($serialized){
	$json = json_decode ($serialized);
	$qtiVariables = array ();
    foreach ($json as $jsonVar) {
        $qtiVariables[$jsonVar->identifier] = QTIVariableFactory($jsonVar->identifier, $jsonVar->type, $jsonVar->values);
    }
    return $qtiVariables;
}

$outcomes = array ();
//$correctsSerialized = '{"a":[1,2],"b":2,"c":3,"d":4,"e":5}';
$outcomesSerialized =  '[
	{"identifier":"SCORE5_1", "type":"float", "cardinality":"single", "values":[]}
	, {"identifier":"SCORE5_2", "type":"float", "cardinality":"single", "values":[]}
]';
$outcomes = unserializedQTIVariables ($outcomesSerialized);


$corrects = array ();
$correctsSerialized ='[
	{"identifier":"RESPONSE0_1", "type":"identifier", "cardinality":"single", "values":["ID_1"]} 
	, {"identifier":"RESPONSE1_1", "type":"identifier", "cardinality":"multiple", "values":["ID_1", "ID_2", "ID_3"]} 
	, {"identifier":"RESPONSE1_2", "type":"identifier", "cardinality":"multiple", "values":["ID_1", "ID_2", "ID_3"]} 
	, {"identifier":"RESPONSE1_3", "type":"identifier", "cardinality":"multiple", "values":["ID_1", "ID_2", "ID_3"]} 
	, {"identifier":"RESPONSE1_4", "type":"identifier", "cardinality":"multiple", "values":["ID_1", "ID_2"]} 
	, {"identifier":"RESPONSE1_5", "type":"pair", "cardinality":"multiple", "values":[["A", "B"], ["C", "D"]]} 
	, {"identifier":"RESPONSE2_1", "type":"pair", "cardinality":"single", "values":[["A", "B"]]} 
	, {"identifier":"RESPONSE3_1", "type":"pair", "cardinality":"multiple", "values":[["A", "B"], ["C", "D"], ["E", "F"]]} 
	, {"identifier":"RESPONSE6_1", "type":"directedpair", "cardinality":"multiple", "values":[["A", "B"], ["C", "D"], ["E", "F"]]} 
	, {"identifier":"RESPONSE7_1", "type":"boolean", "cardinality":"multiple", "values":[true, false, true, false]} 
	, {"identifier":"RESPONSE8_1", "type":"integer", "cardinality":"multiple", "values":[1, 2, 3, 4]} 
	, {"identifier":"RESPONSE9_1", "type":"float", "cardinality":"multiple", "values":[1.1, 2.2, 3.3, 4.4]} 
	, {"identifier":"RESPONSE10_1", "type":"string", "cardinality":"multiple", "values":["abc", "def", "ghi", "klm"]} 
]';
$corrects = unserializedQTIVariables ($correctsSerialized);

// Variables defined through user's interactions
$variables = array();
$variablesSerialized = '[
	{"identifier":"RESPONSE0_1", "type":"identifier", "cardinality":"single", "values":["ID_1"]} 
	, {"identifier":"RESPONSE0_2", "type":"identifier", "cardinality":"single", "values":["ID_0"]} 
	, {"identifier":"RESPONSE0_3", "type":"identifier", "cardinality":"single", "values":[]} 
	, {"identifier":"RESPONSE1_1", "type":"identifier", "cardinality":"multiple", "values":["ID_1", "ID_3", "ID_2"]} 
	, {"identifier":"RESPONSE1_2", "type":"identifier", "cardinality":"multiple", "values":["ID_0", "ID_1", "ID_2"]} 
	, {"identifier":"RESPONSE1_3", "type":"identifier", "cardinality":"multiple", "values":["ID_1", "ID_2"]} 
	, {"identifier":"RESPONSE1_4", "type":"identifier", "cardinality":"multiple", "values":["ID_1", "ID_2", "ID_3"]} 
	, {"identifier":"RESPONSE2_1", "type":"pair", "cardinality":"single", "values":[["A", "B"]]} 
	, {"identifier":"RESPONSE2_2", "type":"pair", "cardinality":"single", "values":[["A", "C"]]} 
	, {"identifier":"RESPONSE2_3", "type":"pair", "cardinality":"single", "values":[]} 
	, {"identifier":"RESPONSE2_4", "type":"pair", "cardinality":"single", "values":[["B", "A"]]} 
	, {"identifier":"RESPONSE3_1", "type":"pair", "cardinality":"multiple", "values":[["A", "B"], ["C", "D"], ["E", "F"]]} 
	, {"identifier":"RESPONSE3_2", "type":"pair", "cardinality":"multiple", "values":[["A", "B"], ["Z", "D"], ["E", "F"]]} 
	, {"identifier":"RESPONSE3_3", "type":"pair", "cardinality":"multiple", "values":[["A", "B"], ["Z", "D"]]} 
	, {"identifier":"RESPONSE3_4", "type":"pair", "cardinality":"multiple", "values":[["B", "A"], ["D", "C"], ["F", "E"]]} 
	, {"identifier":"RESPONSE4_1", "type":"pair", "cardinality":"multiple", "values":[["A", "B"], ["C", "D"], ["E", "F"]]} 
	, {"identifier":"RESPONSE4_2", "type":"pair", "cardinality":"multiple", "values":[["A", "B"], ["C", "D"], ["E", "F"], ["A", "B"], ["C", "D"], ["E", "F"]]} 
	, {"identifier":"RESPONSE4_3", "type":"pair", "cardinality":"multiple", "values":[]} 
	, {"identifier":"RESPONSE4_4", "type":"pair", "cardinality":"multiple", "values":[["A", "B"], ["C", "D"]]} 
	, {"identifier":"RESPONSE4_5", "type":"pair", "cardinality":"multiple", "values":[["B", "A"], ["D", "C"], ["F", "E"]]} 
	, {"identifier":"RESPONSE6_1", "type":"directedpair", "cardinality":"multiple", "values":[["B", "A"], ["D", "C"], ["F", "E"]]} 
	, {"identifier":"RESPONSE6_2", "type":"directedpair", "cardinality":"multiple", "values":[["A", "B"], ["C", "D"], ["E", "F"]]} 
	, {"identifier":"RESPONSE7_1", "type":"boolean", "cardinality":"multiple", "values":[true, false, true, false]} 
	, {"identifier":"RESPONSE8_1", "type":"integer", "cardinality":"multiple", "values":[1, 2, 3, 4]} 
	, {"identifier":"RESPONSE9_1", "type":"float", "cardinality":"multiple", "values":[1.1, 2.2, 3.3, 4.4]} 
	, {"identifier":"RESPONSE10_1", "type":"string", "cardinality":"multiple", "values":["abc", "def", "ghi", "klm"]} 
]';
$variables = unserializedQTIVariables ($variablesSerialized);

$rules = array();

$rules["match0_1"] = 'match(getVariable("RESPONSE0_1"), getCorrect("RESPONSE0_1"))';
$rules['match0_2'] = "match(getVariable('RESPONSE0_2'), getCorrect('RESPONSE0_1'))";
$rules['match0_3'] = "match(getVariable('RESPONSE0_3'), getCorrect('RESPONSE0_1'))";

$rules['match1_1'] = "match(getVariable('RESPONSE1_1'), getCorrect('RESPONSE1_1'))";
$rules['match1_2'] = "match(getVariable('RESPONSE1_2'), getCorrect('RESPONSE1_2'))";
$rules['match1_3'] = "match(getVariable('RESPONSE1_3'), getCorrect('RESPONSE1_3'))";
$rules['match1_4'] = "match(getVariable('RESPONSE1_4'), getCorrect('RESPONSE1_4'))";

$rules['match2_1'] = "match(getVariable('RESPONSE2_1'), getCorrect('RESPONSE2_1'))";
$rules['match2_2'] = "match(getVariable('RESPONSE2_2'), getCorrect('RESPONSE2_1'))";
$rules['match2_3'] = "match(getVariable('RESPONSE2_3'), getCorrect('RESPONSE2_1'))";
$rules['match2_4'] = "match(getVariable('RESPONSE2_4'), getCorrect('RESPONSE2_1'))";

$rules['match3_1'] = "match(getVariable('RESPONSE3_1'), getCorrect('RESPONSE3_1'))";
$rules['match3_2'] = "match(getVariable('RESPONSE3_2'), getCorrect('RESPONSE3_1'))";
$rules['match3_3'] = "match(getVariable('RESPONSE3_3'), getCorrect('RESPONSE3_1'))";
$rules['match3_4'] = "match(getVariable('RESPONSE3_4'), getCorrect('RESPONSE3_1'))";

$rules['map4_1'] = "mapResponse(getVariable('RESPONSE4_1'), getMap('RESPONSE4_1'))";
$rules['map4_2'] = "mapResponse(getVariable('RESPONSE4_2'), getMap('RESPONSE4_1'))";
$rules['map4_3'] = "mapResponse(getVariable('RESPONSE4_3'), getMap('RESPONSE4_1'))";
$rules['map4_4'] = "mapResponse(getVariable('RESPONSE4_4'), getMap('RESPONSE4_1'))";
$rules['map4_5'] = "mapResponse(getVariable('RESPONSE4_5'), getMap('RESPONSE4_1'))";

$rules['setoutcome5_1'] = "setOutcomeValue('SCORE5_1', [2])";
$rules['setoutcome5_2'] = "setOutcomeValue('SCORE5_2', [".$rules['map4_5']."])";

$rules['match6_1'] = "match(getVariable('RESPONSE6_1'), getCorrect('RESPONSE6_1'))";
$rules['match6_2'] = "match(getVariable('RESPONSE6_2'), getCorrect('RESPONSE6_1'))";

$rules['match7_1'] = "match(getVariable('RESPONSE7_1'), getCorrect('RESPONSE7_1'))";

$rules['match8_1'] = "match(getVariable('RESPONSE8_1'), getCorrect('RESPONSE8_1'))";

$rules['match9_1'] = "match(getVariable('RESPONSE9_1'), getCorrect('RESPONSE9_1'))";

$rules['match10_1'] = "match(getVariable('RESPONSE10_1'), getCorrect('RESPONSE10_1'))";

$rules['and11_1'] = "and(true, true)";
$rules['and11_2'] = "and(".$rules['match2_4'].", ".$rules['match6_2'].")";
$rules['and11_3'] = "and(".$rules['match6_1'].", ".$rules['match6_2'].")";

?> 