<?php
if (!(isset($_SESSION))) {session_start();} 
header('Content-Type: text/html; charset=utf-8');
/*
 * Created on 2 oct. 07
 * 
 */

require_once "./BlcAuthoring.php";

if(!isset($doc))
{
	
	$doc = getDomDocument($_SESSION["blcDomXml"]);
	
}



$flag_resources_list = false;
$flag_concepts_relations = false;
$validate = false;

if(!empty($_GET))
{
		if($_GET["action"]=="deleteRelation")
		{
			$flag_concepts_relations = true;
			deleteRelation($doc,$_GET["id"]);			
		}
		if($_GET["action"]=="deleteConcept")
		{
			$flag_concepts_relations = true;
			deleteConcept($doc,$_GET["id"]);			
		}
		if($_GET["action"]=="deleteResource")
		{
			$flag_resources_list = true;
			deleteResource($doc,$_GET["id"],$_GET["rlid"]);
		}	
}


if(!empty($_POST))
{
 	
//     Debug
    
    echo '<pre>';
    print_r($_POST);
    echo '</pre>';



    if(!empty($_POST["resource_count"]))
    {
    	$resource_count = $_POST["resource_count"];
    }
     if(!empty($_POST["concept_count"]))
    {
    	$concept_count = $_POST["concept_count"];

    }
      if(!empty($_POST["concept_count"]))
    {
    	$relation_count = $_POST["relation_count"];

    }

    if(!empty($_POST["resources_lists"]))
    {
    	$flag_resources_list = true;
    }
    
	validateXpathPost($doc,"assessmentItem","title",$_POST["item_title"]);
	validateXpathPost($doc,"itenQuestion","value",$_POST["item_stimulus"]);
	validateXpathPost($doc,"itemPrompt","value",$_POST["item_prompt"]);

	if(!empty($_POST["number_concepts"]))
	{
		if(getAttributeValue($doc,"concepts","numberConcepts") !=$_POST["number_concepts"])
		{
			setAttributeValue($doc,"concepts","numberConcepts",$_POST["number_concepts"]);
		}	
	}
	if(!empty($_POST["add_resource"]) || !empty($_POST["validate"]))
	{  	
		for($i=1;$i<=$resource_count;$i++)
		{
			$flag_ressources_list = true; 
			$resource_id = $_POST["resource_id".$i]; 
			$resource_list_id = $_POST["resource_list_id".$i]; 
			$form_value = $_POST["resource".$i]; 
			modifyResourceValue($doc,$resource_id,$resource_list_id,$form_value);
			
			if(!empty($_POST["new_resource".$i])){
				$new_resource_type = $_POST["new_resource_list_type".$i];
				$new_resource_list_id = $_POST["new_resource_list_id".$i];
				$new_resource = $_POST["new_resource".$ressources_list_type.$i]; 
				createResourceValue($doc,$new_resource_list_id,$new_resource);
			}
		}
	}

	if(!empty($_POST["concepts_relations"]))
	{
		$flag_concepts_relations = true;
	}
	if(!empty($_POST["add_defined_concept"]))
	{
		$flag_concepts_relations = true;
		
		for($i=1;$i<=$concept_count;$i++)
		{
			$concept_id = $_POST["concept_id".$i]; 
			$form_value = $_POST["concept".$i]; 			
			modifyConceptValue($doc,$concept_id,$form_value);
			
			if(!empty($_POST["new_concept".$i])){
				$new_concept = $_POST["new_concept".$i]; 
				createConceptValue($doc,$new_concept);
			}
		}
	}
	if(!empty($_POST["create_relation"])){
		$flag_concepts_relations = true;
		for($i=1;$i<=$relation_count;$i++)
		{
				$source = $_POST["source_concept$i"]; 
				$destination = $_POST["dest_concept$i"];
				$direction = $_POST["direction$i"];
				$relation_id = $_POST["relation_id$i"];
				$relation_value = $_POST["relation$i"];
				modifyRelationValue($doc,$relation_id,$source,$destination,$direction,$relation_value);
		
			if(!empty($_POST["new_relation".$i])){
				$new_relation_value = $_POST["new_relation$i"];
				createRelation($doc,$new_relation_value,$source,$destination,$direction);
			}
		}
	}


	if(!empty($_POST["validate"])){
		$validate = true;
	}

	
	
}
$doc->save("./test.xml");
$xml = saveDomDocument($doc);
$_SESSION["blcDomXml"] = $xml;

include('defineItemProperties.php');

?>
