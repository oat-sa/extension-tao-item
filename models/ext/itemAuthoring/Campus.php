<?php
/**
 * @package itemmodels.Campus
 * TAO Authoring for Campus items
 * @author Plichart Patrick <patrick.plichart@tudor.lu>
*  @version 1.1
*/

require('../../../../generis/common/inc.extension.php');
require('../../../includes/common.php');

header('Content-Type: text/html; charset=UTF-8');
require_once($_SERVER['DOCUMENT_ROOT']."/generis/core/view/generis_utils.php");	
$_SESSION["extendedselectedExtendedValues"]=array();
if (isset($_POST["enonce"])) {
	$_SESSION["Identity"] = basename($_SERVER["PHP_SELF"]);
}
$identity = $_SESSION["Identity"];
//Define new identity

$instance = $_GET['instance'];
$property = 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemContent';
$lg = $GLOBALS['lang'];


error_reporting("^E_NOTICE");
$struct = array();
if(isset($_GET['xml'])){
	$struct = parseXml(tao_helpers_Request::load($_GET['xml'], true));
}
if (isset($_POST["saveItem"])){
	$struct = parseXml(saveItem());
}

$output='
<html>
	<head>
		<link href="/generis/core/view/CSS/generis_default.css" type=text/css rel=stylesheet>
		<link rel="stylesheet" type="text/css" href="/generis/core/view/HTMLArea-3.0-rc1/htmlarea.css" />
		<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=UTF-8">
		<script type="text/javascript">
			var _editor_url = "/generis/core/view/HTMLArea-3.0-rc1/";
			var _editor_lang = "en";
		</script>
		<script type="text/javascript" src="/generis/core/view/HTMLArea-3.0-rc1/htmlarea2.js"></script>
		<style type="text/css">
			input[type=button],input[type=submit]{cursor:pointer; padding:4px; font-weight:bold;}
		</style>
		<script type="text/javascript">
			HTMLArea.loadPlugin("TableOperations");
			HTMLArea.loadPlugin("SpellChecker");
			HTMLArea.loadPlugin("CSS");
			HTMLArea.loadPlugin("ContextMenu");
		</script>
	</head>
	<body>
		<FORM action="'.$identity.'" method=post>
			<input type=hidden name=Authoring['.$instance.']['.$property.']>
			<input type=hidden name=instance value='.$instance.'>
			<input type=hidden name=property value='.$property.'>
			<input type=hidden name=campuslg  value='.$lg.'>
		';
$struct["enonce"]= str_replace("\\","",$struct["enonce"]);
$output.='<textarea COLS=101 ROWS=18 name=enonce>'.$struct["enonce"].'</textarea>
		<br><br>
		<table><tr><td valign=top>';

if (sizeOf($struct["values"]) > 0 )
{
$output.="<center><table border=0 class=\"divLoginbox\" cellpadding=5 cellspacing=0><tr><td class=\"divLoginboxHeader\" style=\"border-bottom: #9c9c9c 1px solid;\" align=\"center\">Label</td><td class=\"divLoginboxHeader\" style=\"border-bottom: #9c9c9c 1px solid;\" align=\"center\">NumericValue</td><td class=\"divLoginboxHeader\" style=\"border-bottom: #9c9c9c 1px solid;\" align=\"center\">Unit related</td></tr>";


foreach ($struct["values"] as $key=>$val)
	{
		$val = str_replace("<i>","",$val);
		$val = str_replace("</i>","",$val);
		$val = str_replace(" ","",$val);
		$random=rand(0,65535);
		
		$keyextendedValue = getExtendedValueKey($val,$struct["extendedValues"]);
		
		$output.='<tr><td class="divSideboxEntry">'.$val.'</td><td class="divSideboxEntry">
		<input type=hidden name=values['.$random.'][Label] value='.$val.'>

		<input type=text size=2 name="values['.$random.'][Numeric]" value="'.$struct["extendedValues"][$keyextendedValue]["TAO:NUMERIC"].'" autocomplete="off"></td><td class="divSideboxEntry">';
		$output.='<SELECT name=values['.$random.'][relatedUnit]>';
		$struct["units"]=array_unique($struct["units"]);
		$i = 1;
		foreach ($struct["units"] as $key2=>$val2)
		{
			$checked="";
			$val2 = str_replace("<b>","",$val2);
			$val2 = str_replace("</b>","",$val2);
			$val2 = str_replace(" ","",$val2);
			
			if(isset($struct["extendedUnits"][trim($val2)]["TAO:ID"])){
				$unitId = $struct["extendedUnits"][trim($val2)]["TAO:ID"];
			 }
			 else{
			 	$unitId = $i;
			 }
			
			if ($struct["extendedValues"][$keyextendedValue]["TAO:RELATEDUNIT"] == $unitId) {
				$checked="selected='selected'";
			}
			$output.='<option '.$checked.' value='.$unitId.'>'.$val2.'</option>';
			$i++;
		}
		$output.= '</SELECT>';
		$output.="</td></tr>";

	}
$output.="</table>";
}
$output.="</td><td width=5%></td><td valign=top>";


if (sizeof($struct["units"])>0){
	$output.="<center><table border='0' class='divLoginbox' cellpadding='5' cellspacing='0' ><tr><td class='divLoginboxHeader' style='border-bottom: #9c9c9c 1px solid;' align='center'>Label</td><td class=\"divLoginboxHeader\" style=\"border-bottom: #9c9c9c 1px solid;\" align=\"center\">Unit&nbsp;ID</td><td class=\"divLoginboxHeader\" style=\"border-bottom: #9c9c9c 1px solid;\" align=\"center\">subClassOf</td></tr>";
	$i=1;
	foreach ($struct["units"] as $key=>$val){
		$val = str_replace("<b>","",$val);
		$val = str_replace("</b>","",$val);
		$val = str_replace(" ","",$val);
		$random=rand(0,65535);
		
		 if(isset($struct["extendedUnits"][trim($val)]["TAO:ID"])){
			$unitId = $struct["extendedUnits"][trim($val)]["TAO:ID"];
		 }
		 else{
		 	$unitId = $i;
		 }
		$output.='<tr>
		<td class="divSideboxEntry">'.$val.'</td>
		<td class="divSideboxEntry">
			<input type=hidden name="units['.$random.'][Label]" value="'.$val.'">
			<input type=text size=2 name="units['.$random.'][ID]" value="'.$unitId.'" autocomplete="off">
		</td>
		<td class="divSideboxEntry">';
		if (isset($struct["extendedUnits"])){
			$output.='<SELECT name=units['.$random.'][subClassOf]><option></option>';
			foreach ($struct["units"] as $key2=>$val2)
			{
				$checked="";
				$val2 = str_replace("<b>","",$val2);
				$val2 = str_replace("</b>","",$val2);
				$val2 = str_replace(" ","",$val2);
				
				if ($struct["extendedUnits"][$val]["TAO:SUBCLASSOF"] == getUnitId($struct,trim($val2))) {
					$checked="selected='selected'";
				}
				$output.='<option '.$checked.' value='.getUnitId($struct,trim($val2)).'>'.$val2.'</option>';
			}
			$output.= '</SELECT>';
		}
		$output.="</td></tr>";
		$i++;
	}
	$output.="</table>";
}


$output.="</td><td width=5%></td><td valign=top><div align=top>";
if (sizeof($struct["extendedUnits"])>0)
{
$output.="<table border=0 class=\"divLoginbox\" cellpadding=5 cellspacing=0><tr><td colspan=2 class=\"divLoginboxHeader\" style=\"border-bottom: #9c9c9c 1px solid;\" align=\"center\">Correct&nbsp;answer&nbsp;</td></tr><tr><td class=\"divSideboxEntry\"><input type=text size=2 name=answer[value] value='".$struct["answervalue"]."' autocomplete='off'></td><td class=\"divSideboxEntry\"><SELECT name=answer[unit]>";

foreach ($struct["units"] as $key2=>$val2)
		{
			$checked="";
			$val2 = str_replace("<b>","",$val2);
			$val2 = str_replace("</b>","",$val2);
			$val2 = str_replace(" ","",$val2);
			
			if (
				
			
					$struct["answerunit"]
						==
					getUnitId($struct,trim($val2))
				
			
				) {$checked="selected";}
			$output.='<option '.$checked.' value='.getUnitId($struct,trim($val2)).'>'.$val2.'</option>';
		}

$output.="</td></tr></table>";
}
$output.="</td></tr></table><div align=center>";
$output.='<br><br><input type=submit name=saveItem value=saveItem style="border: 1px solid black;"></div>';

$output.='</FORM>';
$output.='<script language="javascript" type="text/javascript" defer="1">HTMLArea.replaceAll();</script>';
$output.='</body></html>';

echo $output;

function getUnitId($struct,$label){
		return $struct["extendedUnits"][$label]["TAO:ID"];
}
function getExtendedValueKey($label,$structextvalues)
	{
		foreach ($structextvalues as $key=>$val)
			{
				if (($val["TAO:LABEL"])==$label)
				{
					if (in_array($key,$_SESSION["extendedselectedExtendedValues"])) {;} else
					{
						$_SESSION["extendedselectedExtendedValues"][]=$key;
						return $key;
					}
				}
			}
	}

function saveItem()
{
	$xml = buildXml();
	$item = new core_kernel_classes_Resource($_POST["instance"]);
	$itemService = tao_models_classes_ServiceFactory::get('Items');
	$itemService->setItemContent($item, $xml);
	return $xml;	
}
function buildXml()
{
	error_reporting("^E_NOTICE");
	$item = new core_kernel_classes_Resource($_POST["instance"]);
	$xmlHeader="<tao:ITEM xmlns:rdf='http://www.w3.org/1999/02/22-rdf-syntax-ns#' rdf:ID=\"".$item->uriResource."\" xmlns:tao='http://www.tao.lu/tao.rdfs' xmlns:rdfs='http://www.w3.org/2000/01/rdf-schema#'>
	<rdfs:LABEL lang=\"".$GLOBALS['lang']."\">".$item->getLabel()."</rdfs:LABEL>
	<rdfs:COMMENT lang=\"".$GLOBALS['lang']."\">".$item->getComment()."</rdfs:COMMENT>
	
	";
	$xmlfooter="
	</tao:ITEM>
	";


	$xml="<?xml version='1.0' encoding='UTF-8' ?>";
	$_POST["enonce"]=str_replace("\'","'",$_POST["enonce"]);
	$xml.=$xmlHeader."<TAO:CENONCE>".htmlspecialchars($_POST["enonce"])."</TAO:CENONCE>
	";
	
	$xml.="<TAO:ENONCE>".htmlspecialchars(strip_tags($_POST["enonce"]))."</TAO:ENONCE>
	";
	
	//merge literals for same numeriv values
	$postedarrayvalues=Array();
	if (isset($_POST["values"])){
		foreach ($_POST["values"] as $key=>$val)
		{
			$Value = $val["Label"];
			$Numeric = $val["Numeric"];
			$relatedUnit = $val["relatedUnit"];
			
			$postedarrayvalues[$val["Numeric"]]["TAO:LABEL"][]=$Value;
			
			$postedarrayvalues[$val["Numeric"]]["TAO:RELATEDUNIT"][]=$relatedUnit;

		}
	}
	foreach ($postedarrayvalues as $key=>$val){
			$xml.="
				<TAO:VALUE>
					<TAO:NUMERIC>".$key."</TAO:NUMERIC>
					";
			foreach ($val["TAO:LABEL"] as $key2=>$val2){
				$xml.="<TAO:LABEL>".$val2."</TAO:LABEL>
					";
			}
			foreach ($val["TAO:RELATEDUNIT"] as $key2=>$val2){
				if(!empty($val2)){
					$xml.="<TAO:RELATEDUNIT>".$val2."</TAO:RELATEDUNIT>";
				}
			}
				$xml.="
				</TAO:VALUE>
				";
	
	}
	$postedarrayunits=array();
	if (isset($_POST["units"])){
		foreach ($_POST["units"] as $key=>$val){
			if(isset($val["ID"])){
				if(isset($val["Label"])){
					$postedarrayunits[$val["ID"]]["TAO:LABEL"][] = $val["Label"];
				}
				if(isset($val["subClassOf"])){
					$postedarrayunits[$val["ID"]]["TAO:SUBCLASSOF"][] = $val["subClassOf"];
				}
			}
		}
	}
	foreach ($postedarrayunits as $key=>$val)
	{
			$xml.="
				<TAO:UNIT>
					<TAO:ID>".$key."</TAO:ID>
					";
			if(isset($val["TAO:LABEL"])){
				if(is_array($val["TAO:LABEL"])){
					foreach ($val["TAO:LABEL"] as $key2=>$val2){
						$xml.="<TAO:LABEL>".$val2."</TAO:LABEL>";
					}
				}
			}
			if(isset($val["TAO:SUBCLASSOF"])){
				if(is_array($val["TAO:SUBCLASSOF"])){
					foreach ($val["TAO:SUBCLASSOF"] as $key2=>$val2){
						$xml.="<TAO:SUBCLASSOF>".$val2."</TAO:SUBCLASSOF>";
					}
				}
			}
				$xml.="
				</TAO:UNIT>
				";
	
	}
	$value = '';
	$unit = '';
	if(isset($_POST["answer"])){
		if(isset($_POST["answer"]["value"])){
			$value = $_POST["answer"]["value"];
		}
		if(isset($_POST["answer"]["unit"])){
			$unit = $_POST["answer"]["unit"];
		}
	}
	$xml.="
	<TAO:ANSWER>
	<TAO:ANSWERNUMERIC>{$value}</TAO:ANSWERNUMERIC>
	<TAO:ANSWERRELATEDUNIT>{$unit}</TAO:ANSWERRELATEDUNIT>
	</TAO:ANSWER>";
	

	(isset($_POST["campuslg"])) ? $uilselected = $_POST["campuslg"] : $uilselected = $GLOBALS['lang'];
			
	$xml.='<TAO:LABELS>
		<TAO:LABEL key="variableNameHeader">Comment</TAO:LABEL>
		<TAO:LABEL key="variableValueHeader">Value</TAO:LABEL>
		<TAO:LABEL key="variableUnitHeader">Unit</TAO:LABEL>
		<TAO:LABEL key="loadFileErr">The XML file could not be loaded.</TAO:LABEL>
		<TAO:LABEL key="testValueTitle">Testing the value.</TAO:LABEL>
		<TAO:LABEL key="testValueOK">OK</TAO:LABEL>
		<TAO:LABEL key="testValueErr01">You didn\'t select a value in the text.</TAO:LABEL>
		<TAO:LABEL key="testValueErr02">The selected value does not match the unit.</TAO:LABEL>
		<TAO:LABEL key="testValueErr03">You selected values AND units in the text.</TAO:LABEL>
		<TAO:LABEL key="testValueErr04">You selected more tha one value simultaneously.</TAO:LABEL>
		<TAO:LABEL key="testUnitTitle">Testing the unit.</TAO:LABEL>
		<TAO:LABEL key="testUnitOK">OK</TAO:LABEL>		
		<TAO:LABEL key="testUnitErr01">You didn\'t select a unit in the text.</TAO:LABEL>
		<TAO:LABEL key="testUnitErr02">The selected unit does not match the value.</TAO:LABEL>
		<TAO:LABEL key="testUnitErr03">You selected values AND units in the text.</TAO:LABEL>
		<TAO:LABEL key="testSolEventLabel">Testing the intermediate result</TAO:LABEL>		
		<TAO:LABEL key="testSolErr01">Please, choose another unit !</TAO:LABEL>
		<TAO:LABEL key="testSolErr02">Wrong result.</TAO:LABEL>
		<TAO:LABEL key="testSolOK">Correct !</TAO:LABEL>
		<TAO:LABEL key="testFinalEventLabel">Testing the end result</TAO:LABEL>		
		<TAO:LABEL key="testFinalErr">Wrong result. Please try again !</TAO:LABEL>		
		<TAO:LABEL key="testFinalOK">Congratulations ! You\'ve got it right !</TAO:LABEL>
		<TAO:LABEL key="biggerValueError">The selected value is bigger than the number line ; please extend it !</TAO:LABEL>
		<TAO:LABEL key="smallerValueError">The selected value is smaller than the number line ; please change it !</TAO:LABEL>
		<TAO:LABEL key="unitError">Unit does not match</TAO:LABEL>
		<TAO:LABEL key="addValueOK">OK</TAO:LABEL>
		<TAO:LABEL key="addValueEventLabel">The first number will be added to the number line.</TAO:LABEL>
		<TAO:LABEL key="addSecondValueEventLabel">The second number will be added to the number line.</TAO:LABEL>
		<TAO:LABEL key="addOperationEventLabel">Choose a calculation method.</TAO:LABEL>
		<TAO:LABEL key="scaleChangeErr01">The lowest value cannot be higher than the maximum: please change the number line</TAO:LABEL>
		<TAO:LABEL key="scaleChangeErr02">The selected range does not include any of the values present on the number line.</TAO:LABEL>
		<TAO:LABEL key="scaleChangeOK">OK</TAO:LABEL>
		<TAO:LABEL key="scaleChangeEventLabel">Modifying the number line</TAO:LABEL>
		<TAO:LABEL key="messageWindowTitle">Message from CAMPUS</TAO:LABEL>
		<TAO:LABEL key="messageWindowText">Message :</TAO:LABEL>
		<TAO:LABEL key="messageWindowButton">OK !</TAO:LABEL>	
		<TAO:LABEL key="variableChoiceWindowTitle">Choosing a value</TAO:LABEL>
		<TAO:LABEL key="variableChoiceWindowText">Please choose :</TAO:LABEL>
		<TAO:LABEL key="variableChoiceWindowButton">Select</TAO:LABEL>
		<TAO:LABEL key="testSolWindowTitle">Entering the solution</TAO:LABEL>
		<TAO:LABEL key="solChoiceWindowValue">Value :</TAO:LABEL>
		<TAO:LABEL key="solChoiceWindowUnit">Unit :</TAO:LABEL>
		<TAO:LABEL key="solChoiceWindowButton">OK !</TAO:LABEL>
		<TAO:LABEL key="scaleChoiceWindowTitle">Choosing a scale</TAO:LABEL>
		<TAO:LABEL key="scaleChoiceWindowButton">OK !</TAO:LABEL>			
		<TAO:LABEL key="scaleChoiceWindowText">Range of the number line :</TAO:LABEL>
		<TAO:LABEL key="scaleChoiceWindowMin">Min</TAO:LABEL>
		<TAO:LABEL key="scaleChoiceWindowMax">Max</TAO:LABEL>
		<TAO:LABEL key="errorWindowTitle">CAMPUS Error !</TAO:LABEL>
		<TAO:LABEL key="errorWindowButton">OK !</TAO:LABEL>			
		<TAO:LABEL key="errorWindowText">Message</TAO:LABEL>	
		<TAO:LABEL key="WindowTitle">Number line</TAO:LABEL>
		<TAO:LABEL key="btnValider">Confirm</TAO:LABEL>
		<TAO:LABEL key="btnScale">Change scale</TAO:LABEL>			
		<TAO:LABEL key="btnReset">Cancel all</TAO:LABEL>	
		<TAO:LABEL key="btnResetNext">New step</TAO:LABEL>
		<TAO:LABEL key="operationTxt">Calculation method</TAO:LABEL>
		<TAO:LABEL key="recapWindowTitle">Calculation list</TAO:LABEL>
		<TAO:LABEL key="finalChoiceWindowTitle">Final result</TAO:LABEL>
		<TAO:LABEL key="finalChoiceWindowText">Choose the final result !</TAO:LABEL>
		<TAO:LABEL key="finalChoiceWindowButton">Select</TAO:LABEL>				
	</TAO:LABELS>';

	return $xml.$xmlfooter;

}

function parseXml($xml)
{	
	error_reporting("^E_NOTICE");
	$struct=Array();
	$xml_parser=xml_parser_create("UTF-8");
	xml_parse_into_struct($xml_parser, $xml, $values, $tags);
	
		foreach ($tags as $key=>$val)
		 {error_reporting("^E_NOTICE");
			if ($key == "TAO:CENONCE")
				 { 
					foreach ($val as $x=>$theIndex)
					 {	
						$struct["enonce"] = $values[$theIndex]["value"];
					 }
				 }
			
			if ($key == "TAO:VALUE")
				 { 
					$struct["extendedValues"]=getValues($val,$values);
				 }
			if ($key == "TAO:UNIT")
				 { 
					$struct["extendedUnits"]=getUnits($val,$values);
				 }
			if ($key == "TAO:ANSWERNUMERIC")
				 { foreach ($val as $x=>$theIndex)
					 {
					$struct["answervalue"]=$values[$theIndex]["value"];
					 }
				 }
			if ($key == "TAO:ANSWERRELATEDUNIT")
				 { 
				foreach ($val as $x=>$theIndex)
					 {
					$struct["answerunit"]=$values[$theIndex]["value"];
					 }
				 }
			
		 }
	
	
	preg_match_all("#<b>[^<]*</b>#",$struct["enonce"],$units);
	preg_match_all("#<i>[^<]*</i>#",$struct["enonce"],$values);
	$struct["units"] = $units;
	$struct["values"] = $values;
	$struct["values"]=$struct["values"][0];
	$struct["units"]=$struct["units"][0];
	
	return $struct;
}
function getValues($val,$values)
	{
		error_reporting("^E_NOTICE"); // prevent unset xml values reporting notices
		$indexes = getValuesindexes($val,$values);
		/*For each TAO:VALUE*/
		foreach ($indexes as $back=>$tab)
			{
				
				$start = $tab["start"];
				$end = $tab["end"];
				
				$taolabel=array();
				$relatedUnit=Array();
				
				while ($start!=$end)
					{
						if ($values[$start]["tag"]=="TAO:NUMERIC") {$taonumeric=$values[$start]["value"];}
						
						if ($values[$start]["tag"]=="TAO:LABEL") {$taolabel[] = $values[$start]["value"];}
						
						if ($values[$start]["tag"]=="TAO:RELATEDUNIT") {$relatedUnit[] = $values[$start]["value"];}
						$start++;
						
					}
					$i=0;
					
					foreach ($taolabel as $key=>$val)
						{
					$extendedValues[]=array("TAO:NUMERIC" => $taonumeric, "TAO:LABEL" => $taolabel[$i],"TAO:RELATEDUNIT" => $relatedUnit[$i]);
					$i++;
						}
						
			}
			
			return $extendedValues;


	}

function getValuesindexes($val,$values)
	{
		
		$i=-1;$indexes=Array();
		foreach($val as $key => $val)
		{
			if ((($values[$val]["type"])=="open") and (($values[$val]["tag"])=="TAO:VALUE"))
			{
				$i++;
				$theIndex=$val;
				$theIndex++;
				$indexes[$i]["start"]=$theIndex-1;
				while 
				(!
					(
						(
							(($values[$theIndex]["type"])=="close")
							and
							(($values[$theIndex]["tag"])=="TAO:VALUE")
						)
					)
				)
				{$theIndex++;} 
				$indexes[$i]["end"]=$theIndex-1;$i++;
			}

		}
		return($indexes);
	}
function getUnitsindexes($val,$values)
	{
		
		$i=-1;$indexes=Array();
		foreach($val as $key => $val)
		{
			if ((($values[$val]["type"])=="open") and (($values[$val]["tag"])=="TAO:UNIT"))
			{
				$i++;
				$theIndex=$val;
				$theIndex++;
				$indexes[$i]["start"]=$theIndex-1;
				while 
				(!
					(
						(
							(($values[$theIndex]["type"])=="close")
							and
							(($values[$theIndex]["tag"])=="TAO:UNIT")
						)
					)
				)
				{$theIndex++;} 
				$indexes[$i]["end"]=$theIndex-1;$i++;
			}

		}
		return($indexes);
	}
function getUnits($val,$values)
	{	error_reporting("^E_NOTICE"); // prevent unset xml values reporting notices
		$indexes = getUnitsindexes($val,$values);
		/*For each TAO:VALUE*/
		foreach ($indexes as $back=>$tab)
			{
				
				$start = $tab["start"];
				$end = $tab["end"];
				
				$taolabel=array();
				$relatedUnit=Array();
				while ($start!=$end)
					{
						if ($values[$start]["tag"]=="TAO:ID") {$taonumeric=$values[$start]["value"];}
						
						if ($values[$start]["tag"]=="TAO:LABEL") {$taolabel[] = $values[$start]["value"];}
						
						if ($values[$start]["tag"]=="TAO:SUBCLASSOF") {$relatedUnit[] = $values[$start]["value"];}
						$start++;
						
					}
					$i=0;
					foreach ($taolabel as $key=>$val)
						{
					$extendedValues[$val]=array("TAO:ID" => $taonumeric, "TAO:LABEL" => $taolabel[$i],"TAO:SUBCLASSOF" => $relatedUnit[$i]);
					$i++;
						}
						
			}
			
			return $extendedValues;
	}
?>