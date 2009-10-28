<?php
/*
	
   
   
    

    
    
    
    

    
    
    

*/
/**
 * @package itemmodels.Campus
 * TAO Authoring for Campus items
 * @author Plichart Patrick <patrick.plichart@tudor.lu>
* @version 1.1
*/
include("UIlgs.php");
$generis_location = "http://".$_SERVER["HTTP_HOST"]."/generis/";
error_reporting("^E_NOTICE");
header('Content-Type: text/html; charset=UTF-8');

include_once("../../GenerisUI/generis_utils.php");
include_once("../../../generis/generis/generis/GenerisUI/generis_utils.phpp");

if (!(isset($_SESSION))) {session_start();}
$_SESSION["extendedselectedExtendedValues"]=array();
if (isset($_POST["enonce"])) {$_SESSION["Identity"] = basename($_SERVER["PHP_SELF"]);}




$identity = $_SESSION["Identity"];

//Define new identity



$instance=$_SESSION["ModelnsInstance"];
$property=$_SESSION["ModelnsProperty"];
$lg = $_SESSION["datalg"];

error_reporting("^E_NOTICE");
saveItem();

$struct = loadItem($instance,$property);

$output='<head>

<LINK media=screen href="../../GenerisUI/CSS/generis_default.css" 
type=text/css rel=stylesheet>
<LINK media=screen href="./CSS/generis_default.css" 
type=text/css rel=stylesheet>
<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=UTF-8">
<script type="text/javascript">
  _editor_url = "'.$generis_location.'generis/generis/GenerisUI/HTMLArea-3.0-rc1/";
  _editor_lang = "en";
</script>





<script type="text/javascript" src="'.$generis_location.'generis/generis/GenerisUI/HTMLArea-3.0-rc1/htmlarea2.js"></script>

<script type="text/javascript">
      HTMLArea.loadPlugin("TableOperations");
      HTMLArea.loadPlugin("SpellChecker");
      HTMLArea.loadPlugin("CSS");
      HTMLArea.loadPlugin("ContextMenu");
</script></head><body class=paneIframe>

';
		

$output.='<FORM action="'.$identity.'" method=post>';
$output.='<input type=hidden name=Authoring['.$instance.']['.$property.']>
		<input type=hidden name=instance value='.$instance.'>
		<input type=hidden name=property value='.$property.'>
		';
$struct["enonce"]= str_replace("\\","",$struct["enonce"]);
$output.='User interface language for this item : <input type=radio name=campuslg CHECKED value=FR> FR';
$output.='<textarea COLS=90 ROWS=18 name=enonce>'.$struct["enonce"].'</textarea><br><br>';
$output.="<table><tr><td valign=top>";

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

		<input type=text size=2 name=values['.$random.'][Numeric] value='.$struct["extendedValues"][$keyextendedValue]["TAO:NUMERIC"].'></td><td class="divSideboxEntry">';
		$output.='<SELECT name=values['.$random.'][relatedUnit]>';
		$struct["units"]=array_unique($struct["units"]);
		foreach ($struct["units"] as $key2=>$val2)
		{
			$checked="";
			$val2 = str_replace("<b>","",$val2);
			$val2 = str_replace("</b>","",$val2);
			$val2 = str_replace(" ","",$val2);
			
			if (
				$struct["extendedValues"][$keyextendedValue]["TAO:RELATEDUNIT"]
				==
				getUnitId($struct,trim($val2))
				
			
				) {$checked="selected";}
			
			$output.='<option '.$checked.' value='.getUnitId($struct,trim($val2)).'>'.$val2.'</option>';
		}
		$output.= '</SELECT>';
		$output.="</td></tr>";

	}
$output.="</table>";
}
$output.="</td><td width=5%></td><td valign=top>";


if (sizeof($struct["units"])>0)
{
$output.="<center><table border=0 class=\"divLoginbox\" cellpadding=5 cellspacing=0 ><tr><td class=\"divLoginboxHeader\" style=\"border-bottom: #9c9c9c 1px solid;\" align=\"center\">Label</td><td class=\"divLoginboxHeader\" style=\"border-bottom: #9c9c9c 1px solid;\" align=\"center\">Unit&nbsp;ID</td><td class=\"divLoginboxHeader\" style=\"border-bottom: #9c9c9c 1px solid;\" align=\"center\">subClassOf</td></tr>";
foreach ($struct["units"] as $key=>$val)
	{
		$val = str_replace("<b>","",$val);
		$val = str_replace("</b>","",$val);
		$val = str_replace(" ","",$val);
		$random=rand(0,65535);
		$output.='<tr><td class="divSideboxEntry">'.$val.'</td><td class="divSideboxEntry"><input type=hidden name=units['.$random.'][Label] value='.$val.'><input type=text size=2 name=units['.$random.'][ID] value='.$struct["extendedUnits"][$val]["TAO:ID"].'></td><td class="divSideboxEntry">';
		if (isset($struct["extendedUnits"]))
			{
		$output.='<SELECT name=units['.$random.'][subClassOf]><option></option>';
		
		
		foreach ($struct["units"] as $key2=>$val2)
		{
			$checked="";
			$val2 = str_replace("<b>","",$val2);
			$val2 = str_replace("</b>","",$val2);
			$val2 = str_replace(" ","",$val2);
			if (
				
			
					$struct["extendedUnits"][$val]["TAO:SUBCLASSOF"]
						==
					getUnitId($struct,trim($val2))
				
			
				) {$checked="selected";}
			$output.='<option '.$checked.' value='.getUnitId($struct,trim($val2)).'>'.$val2.'</option>';
		}
		$output.= '</SELECT>';
			}
		$output.="</td></tr>";

	}
$output.="</table>";
}


$output.="</td><td width=5%></td><td valign=top><div align=top>";
if (sizeof($struct["extendedUnits"])>0)
{
$output.="<table border=0 class=\"divLoginbox\" cellpadding=5 cellspacing=0><tr><td colspan=2 class=\"divLoginboxHeader\" style=\"border-bottom: #9c9c9c 1px solid;\" align=\"center\">Correct&nbsp;answer&nbsp;</td></tr><tr><td class=\"divSideboxEntry\"><input type=text size=2 name=answer[value] value=".$struct["answervalue"]."></td><td class=\"divSideboxEntry\"><SELECT name=answer[unit]>";

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
$output.="</td></tr></table><div align=right>";
$output.='<br><br><input type=submit name=saveItem value=saveItem style="border: 1px solid silver;"></div>';

$output.='</FORM>';
$output.='<script language="javascript" type="text/javascript" defer="1">HTMLArea.replaceAll();</script>';

		





echo $output;

function getUnitId($struct,$label)
	{
		/*
		foreach ($struct["extendedUnits"] as $key=>$val)
			{
				
			}
		*/
		
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
function loadItem($instance,$property)
{
	$instance = $instance;
	$idressource = $instance."-".$property.$_SESSION["datalg"];
	
	if (!(isset($_SESSION["actualXML"][$idressource])))
	{
			
			$iDescr = calltoKernel('getInstanceDescription',array($_SESSION["session"],array($instance),array("")));
			
			$x = $iDescr["pDescription"];
						
						//print_r($x["PropertiesValues"]);
						foreach ($x["PropertiesValues"][0] as $ii => $pvalue)
							{
								
								if ($pvalue["PropertyKey"] == $property)
								{	
									$xml = $pvalue["PropertyValue"];
								}
							}
	}
	else
	{
		$xml=$_SESSION["actualXML"][$idressource];
	}
	
	$struct=array();
	
	$struct=parseXml($xml);
	
	return $struct;
}

function saveItem()
{
if (isset($_POST["saveItem"]))
	{
	
	$xml = buildXml();
	$instance = $_POST["instance"];
	
	
	$result=	calltoKernel('editPropertyValuesforInstance',array($_SESSION["session"],array($instance),array($_POST["property"]),array($_SESSION["datalg"]),array($xml)));
	
	$idressource = $instance."-".$_POST["property"].$_SESSION["datalg"];
	$_SESSION["actualXML"][$idressource] = $xml;
	$x= fopen("exemple.xml","wb");
	fwrite($x,$xml);
	fclose($x);

	}
}
function buildXml()
{
	
	$xmlHeader="<tao:ITEM xmlns:rdf='http://www.w3.org/1999/02/22-rdf-syntax-ns#' rdf:ID=\"".$_POST["instance"]."\" xmlns:tao='http://www.tao.lu/tao.rdfs' xmlns:rdfs='http://www.w3.org/2000/01/rdf-schema#'>
	<rdfs:LABEL lang=\"".$_SESSION["datalg"]."\">".$_POST["instance"]."</rdfs:LABEL>
	<rdfs:COMMENT lang=\"".$_SESSION["datalg"]."\">".$_POST["instance"]."</rdfs:COMMENT>
	
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
	if (isset($_POST["values"]))
	{
	foreach ($_POST["values"] as $key=>$val)
		{
			$Value = $val["Label"];
			$Numeric = $val["Numeric"];
			$relatedUnit = $val["relatedUnit"];
			
			$postedarrayvalues[$val["Numeric"]]["TAO:LABEL"][]=$Value;
			
			$postedarrayvalues[$val["Numeric"]]["TAO:RELATEDUNIT"][]=$relatedUnit;

		}
	}
	foreach ($postedarrayvalues as $key=>$val)
	{
			$xml.="
				<TAO:VALUE>
					<TAO:NUMERIC>".$key."</TAO:NUMERIC>
					";
			foreach ($val["TAO:LABEL"] as $key2=>$val2)
				{
				$xml.="<TAO:LABEL>".$val2."</TAO:LABEL>
					";

				}
			foreach ($val["TAO:RELATEDUNIT"] as $key2=>$val2)
				{
				$xml.="<TAO:RELATEDUNIT>".$val2."</TAO:RELATEDUNIT>";

				}
				$xml.="
				</TAO:VALUE>
				";
	
	}
	$postedarrayunits=array();
	if (isset($_POST["units"]))
	{
	foreach ($_POST["units"] as $key=>$val)
		{
			$Value = $val["Label"];
			$ID = $val["ID"];
			$subClassOf = $val["subClassOf"];
			
			$postedarrayunits[$val["ID"]]["TAO:LABEL"][]=$Value;
			
			$postedarrayunits[$val["ID"]]["TAO:SUBCLASSOF"][]=$subClassOf;

		}
	}
	foreach ($postedarrayunits as $key=>$val)
	{
			$xml.="
				<TAO:UNIT>
					<TAO:ID>".$key."</TAO:ID>
					";
			foreach ($val["TAO:LABEL"] as $key2=>$val2)
				{
				$xml.="<TAO:LABEL>".$val2."</TAO:LABEL>
					";

				}
			foreach ($val["TAO:SUBCLASSOF"] as $key2=>$val2)
				{
				$xml.="<TAO:SUBCLASSOF>".$val2."</TAO:SUBCLASSOF>";

				}
				$xml.="
				</TAO:UNIT>
				";
	
	}
	$xml.="
	<TAO:ANSWER>
	<TAO:ANSWERNUMERIC>".$_POST["answer"]["value"]."</TAO:ANSWERNUMERIC>
	<TAO:ANSWERRELATEDUNIT>".$_POST["answer"]["unit"]."</TAO:ANSWERRELATEDUNIT>
	</TAO:ANSWER>";
	

			$uilselected = $_POST["campuslg"];
			
			$xml.=LGFR;

	return $xml.$xmlfooter;

}

function parseXml($xml)
{	
	error_reporting("^E_NOTICE");
	$struct=Array();
	$xml_parser=xml_parser_create("UTF-8");
	xml_parse_into_struct($xml_parser, $xml, $values, $tags);
	//echo xml_error_string(xml_get_error_code($xml_parser));
	//print_r($tags);
	//print_r($values);
	
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
/*
$i=0;
					foreach ($val as $x=>$theIndex)
					 {	
						if ($values[$theIndex]["type"]=="open")
						 {
							$i++;
						 }
						 if ($values[$theIndex]["tag"]=="TAO:NUMERIC")
						 {
							$struct["extendedValues"][$i]["TAO:NUMERIC"]=$values[$theIndex]["value"];
						 }
						 if ($values[$theIndex]["tag"]=="TAO:LABEL")
						 {
							$struct["extendedValues"][$i]["TAO:LABEL"][]=$values[$theIndex]["value"];
						 }
						 if ($values[$theIndex]["tag"]=="TAO:RELATEDUNIT")
						 {
							$struct["extendedValues"][$i]["TAO:RELATEDUNIT"][]=$values[$theIndex]["value"];
						 }
						
					 }
*/
?>
