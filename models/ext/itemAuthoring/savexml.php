<?php
/*
	
   
   
    

    
    
    
    

    
    
    

*/
/**
* Builds and save xml of an item using posted data
* @package Widgets.etesting.authoringItem
* @author Plichart Patrick <patrick.plichart@tudor.lu>
* @version 1.1
*/
//require_once("GUI_constants.php");		   
//require_once("functions.php");
if (!(isset($_SESSION)))
{
session_start();
}

include_once("../../GenerisUI/generis_ConstantsOfGui.php");
include_once("../../GenerisUI/generis_authenticate.php");
include_once("../../GenerisUI/generis_utils.php");


$instance = $_POST["itemcontent"]["instance"];
$property = $_POST["itemcontent"]["property"];
$xml=$_POST["xml"];
$output="";
		
		


//die();

	
$xml = str_replace('´',"'",$xml);	
$xml = str_replace("\\\'","'",$xml);	
$xml = str_replace("Â","",$xml);
$xml = str_replace("\\\"",'"',$xml);

$xml = str_replace("'",'&#180;',$xml);

$xml = str_replace("'",'&#180;',$xml);
//$xml = str_replace("\\\&#180;",'"',$xml);

$xml=$xml;

calltoKernel('removeSubjectPredicate',array($_SESSION["session"],$instance,$property));
	calltoKernel('setStatement',array($_SESSION["session"],$instance,$property,$xml,"l",$_SESSION["datalg"],"","r"));	

$_SESSION["Authoring"]=array($instance=> array($property=>""));		
$ressource=$_SESSION["Authoring"];




		
		$_SESSION["ITEMpreview"]=$xml;
		
	
						
						
		include_once("./TAOopenauthoring.php");

?>