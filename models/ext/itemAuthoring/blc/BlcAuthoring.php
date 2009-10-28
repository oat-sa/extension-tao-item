<?php

/*
 * Created on 3 oct. 07
 * 
 */
$debug=false;
if ($debug) {error_reporting(E_ALL);echo __FILE__;echo __LINE__;echo "<br />";}
function saveDomDocument($domDoc){
	return $domDoc->saveXml();
}
 if ($debug) {error_reporting(E_ALL);echo __FILE__;echo __LINE__;echo "<br />";}
function getDomDocument($xml)
{
	$doc = new DOMDocument();
	if($xml==""){
		$doc->load("./empty_blc.xml");
		return  $doc;
	}
	$doc->loadXml($xml);
//	$doc->load("./empty_blc_conceptRelation.xml");
	
	return $doc;
} 
 
 if ($debug) {error_reporting(E_ALL);echo __FILE__;echo __LINE__;echo "<br />";}
 function getAttributeValue($domDoc,$tagName,$attributeName) 
{
	$value = "";
	if($domDoc->getElementsByTagName($tagName)->length == 1)
	{
		$value = $domDoc->getElementsByTagName($tagName)->item(0)->getAttribute($attributeName);
	}
	return $value;
}
if ($debug) {error_reporting(E_ALL);echo __FILE__;echo __LINE__;echo "<br />";}

function isXpathValue($domDoc,$value){
	if(strpos($value,"xpath://")=== false)
	{
		return false;
	}
	return true;
}
if ($debug) {error_reporting(E_ALL);echo __FILE__;echo __LINE__;echo "<br />";}
function validateXpathPost($domDoc,$tagName,$attributeName,$formValue)
{
	$xpathValue = "xpath:///tao:ITEM/tao:content/tao:media[@id=";
	if($formValue != null)
	{
		$xmlValue = getAttributeValue($domDoc,$tagName,$attributeName);
		
		if(isXpathValue($domDoc,$xmlValue))
		{
			if( getMediaValue($domDoc,$xmlValue) != $formValue )
				{
					setMediaValue($domDoc,$xmlValue,$formValue);
				}
		}		
		else
		{
			setXpathAttributeValue($domDoc,$tagName,$attributeName,$formValue,$xpathValue);
		
		}
	}
}
if ($debug) {error_reporting(E_ALL);echo __FILE__;echo __LINE__;echo "<br />";}
function getMediaValue($domDoc,$value)
{
	if(!isXpathValue($domDoc,$value))
	{
		return $value;
	}
	else{
		$xpath = new DOMXPath($domDoc);	
		$query = substr($value,strpos($value,"xpath://")+7);
		$content = $domDoc->getElementsByTagName("content")->item(0);
		$entry = $xpath->evaluate($query,$content);
		if($entry->item(0) !=null)
		{
		 	return $entry->item(0)->nodeValue;
		}
		return 0;
	}
}
if ($debug) {error_reporting(E_ALL);echo __FILE__;echo __LINE__;echo "<br />";}
function setMediaValue($domDoc,$xmlValue,$formValue)
{
	$start = strpos($xmlValue,"=");
	$end =strpos($xmlValue,"]");
	$id= substr($xmlValue,$start+1,($end-1)-$start);
	$content = $domDoc->getElementsByTagName("content")->item(0);	 
	$xpathStrng ="//tao:ITEM/tao:content/tao:media[@id=";
	$query = $xpathStrng.$id."]";
	$xpath = new DOMXPath($domDoc);	
	$entry = $xpath->evaluate($query,$content);
	$entry->item(0)->nodeValue=$formValue;
}
if ($debug) {error_reporting(E_ALL);echo __FILE__;echo __LINE__;echo "<br />";}
function createMediaValue($domDoc,$formValue,$id)
{
		$content = $domDoc->getElementsByTagName("content")->item(0);
		$node = $domDoc->createElementNS("http://www.tao.lu/tao.rdfs#","media",$formValue);
		$content->appendChild($node);
		$node->setAttribute("id",$id);
}

if ($debug) {error_reporting(E_ALL);echo __FILE__;echo __LINE__;echo "<br />";}
function setAttributeValue($domDoc,$tagName,$attributeName,$value)
{
	$domDoc->getElementsByTagName($tagName)->item(0)->setAttribute($attributeName,$value);
}
if ($debug) {error_reporting(E_ALL);echo __FILE__;echo __LINE__;echo "<br />";}
function setXpathAttributeValue($domDoc,$tagName,$attributeName,$value,$xpath)
{
		$id=getID();
		$xpathValue = $xpath.$id."]";
		setAttributeValue($domDoc,$tagName,$attributeName,$xpathValue);
		createMediaValue($domDoc,$value,$id);
}
if ($debug) {error_reporting(E_ALL);echo __FILE__;echo __LINE__;echo "<br />";}

function getID(){
	$Asec = explode(" ", microtime());
   	$Amicro = explode(".", $Asec[0]);
   	return ($Asec[1].substr($Amicro[1], 0, 4));

	
}
if ($debug) {error_reporting(E_ALL);echo __FILE__;echo __LINE__;echo "<br />";}
function deleteResource($domDoc,$resourceId,$resourcesListId)
{
	$nodeToDelete = getResource($domDoc,$resourceId,$resourcesListId);
	if($nodeToDelete != null){
		$nodeToDelete->parentNode->removeChild($nodeToDelete); 
	}
}
if ($debug) {error_reporting(E_ALL);echo __FILE__;echo __LINE__;echo "<br />";}
function deleteConcept($domDoc,$conceptId)
{
	$nodeToDelete = getConcept($domDoc,$conceptId);
	if($nodeToDelete != null){
		$nodeToDelete->parentNode->removeChild($nodeToDelete); 
	}
}
if ($debug) {error_reporting(E_ALL);echo __FILE__;echo __LINE__;echo "<br />";}
function deleteRelation($domDoc,$relationId)
{
	$nodeToDelete = getRelation($domDoc,$relationId);
	if($nodeToDelete != null){
		$nodeToDelete->parentNode->removeChild($nodeToDelete); 
	}
}
if ($debug) {error_reporting(E_ALL);echo __FILE__;echo __LINE__;echo "<br />";}
function getResource($domDoc,$resourceId,$resourcesListId)
{
 	$xpath = new DOMXPath($domDoc);	
 	$business = $domDoc->getElementsByTagName("business")->item(0);	
 	$query = "//tao:resourcesLists/tao:resourceList[@identifier=\"".$resourcesListId."\"]/tao:resource[@identifier=\"".$resourceId."\"]" ;
	$entry = $xpath->evaluate($query,$business);
	return $entry->item(0);
}
if ($debug) {error_reporting(E_ALL);echo __FILE__;echo __LINE__;echo "<br />";}
function getConcept($domDoc,$conceptId)
{
 	$xpath = new DOMXPath($domDoc);	
 	$business = $domDoc->getElementsByTagName("business")->item(0);	
 	$query = "//tao:concept[@identifier=\"".$conceptId."\"]" ;
	$entry = $xpath->evaluate($query,$business);
	return $entry->item(0);	
}
if ($debug) {error_reporting(E_ALL);echo __FILE__;echo __LINE__;echo "<br />";}
function getRelation($domDoc,$relationId)
{
 	$xpath = new DOMXPath($domDoc);	
 	$business = $domDoc->getElementsByTagName("business")->item(0);	
 	$query = "//tao:relation[@identifier=\"".$relationId."\"]" ;
	$entry = $xpath->evaluate($query,$business);
	return $entry->item(0);	
}
if ($debug) {error_reporting(E_ALL);echo __FILE__;echo __LINE__;echo "<br />";}
function modifyNodeValue($domDoc,$nodeToModify,$formValue)
{
	$xmlValue = $nodeToModify->nodeValue;
	if(isXpathValue($domDoc,$xmlValue))
	{
		if( getMediaValue($domDoc,$xmlValue) != $formValue)
		{
			setMediaValue($domDoc,$xmlValue,$formValue);
		}
	}
}
if ($debug) {error_reporting(E_ALL);echo __FILE__;echo __LINE__;echo "<br />";}
function modifyResourceValue($domDoc,$resourceId,$resourcesListId,$formValue)
{
	$nodeToModify = getResource($domDoc,$resourceId,$resourcesListId);
	if($nodeToModify !=null)
		modifyNodeValue($domDoc,$nodeToModify,$formValue);
}
if ($debug) {error_reporting(E_ALL);echo __FILE__;echo __LINE__;echo "<br />";}
function modifyConceptValue($domDoc,$conceptId,$formValue)
{
	$nodeToModify = getConcept($domDoc,$conceptId);
	if($nodeToModify !=null)
		modifyNodeValue($domDoc,$nodeToModify,$formValue);
}
if ($debug) {error_reporting(E_ALL);echo __FILE__;echo __LINE__;echo "<br />";}
function modifyRelationValue($domDoc,$relationId,$sourceId,$destId,$direction,$formValue)
{
	$nodeToModify = getRelation($domDoc,$relationId);
	if($nodeToModify !=null)
	{
		modifyNodeValue($domDoc,$nodeToModify,$formValue);
		$nodeToModify->setAttribute("source",$sourceId);
		$nodeToModify->setAttribute("destination",$destId);
		$nodeToModify->setAttribute("direction",$direction);
	}
}
if ($debug) {error_reporting(E_ALL);echo __FILE__;echo __LINE__;echo "<br />";}
function getResourceList($domDoc,$resourcesListId){
	
 	$xpath = new DOMXPath($domDoc);	
 	$business = $domDoc->getElementsByTagName("business")->item(0);	
	$query = "//tao:resourcesLists/tao:resourceList[@identifier=\"".$resourcesListId."\"]";
	$entry = $xpath->evaluate($query,$business);
	return $entry->item(0);
}
if ($debug) {error_reporting(E_ALL);echo __FILE__;echo __LINE__;echo "<br />";}
function createResourceValue($domDoc,$resourcesListId,$formValue)
{
	$id = getID();
	$resourcesList = getResourceList($domDoc,$resourcesListId);
	$xmlValue = "xpath:///tao:ITEM/tao:content/tao:media[@id=".$id."]" ;
	$newResource = $domDoc->createElementNS("http://www.tao.lu/tao.rdfs#","resource");
	$cdata = $newResource->ownerDocument->createCDATASection($xmlValue);
	$newResource->appendChild($cdata);
	$newResource->setAttribute("identifier",getID());
	$resourcesList->appendChild($newResource);
	createMediaValue($domDoc,$formValue,$id);

}
if ($debug) {error_reporting(E_ALL);echo __FILE__;echo __LINE__;echo "<br />";}
function createRelation($domDoc,$formValue,$sourceId,$destinationId,$direction)
{
	$id = getID();
	$relations = $domDoc->getElementsByTagName("relations")->item(0);
	$newRelation = $domDoc->createElementNS("http://www.tao.lu/tao.rdfs#","relation");	
	$xmlValue = "xpath:///tao:ITEM/tao:content/tao:media[@id=".$id."]" ;
	$cdata = $newRelation->ownerDocument->createCDATASection($xmlValue);
	$newRelation->appendChild($cdata);
	$newRelation->setAttribute("identifier",getID());
	$newRelation->setAttribute("source",$sourceId);
	$newRelation->setAttribute("destination",$destinationId);
	$newRelation->setAttribute("direction",$direction);
	$relations->appendChild($newRelation);
	createMediaValue($domDoc,$formValue,$id);
	
}
if ($debug) {error_reporting(E_ALL);echo __FILE__;echo __LINE__;echo "<br />";}
function createConceptValue($domDoc,$formValue)
{
	$id = getID();
	$concepts = $domDoc->getElementsByTagName("concepts")->item(0);
	$xmlValue = "xpath:///tao:ITEM/tao:content/tao:media[@id=".$id."]" ;
	$newConcept = $domDoc->createElementNS("http://www.tao.lu/tao.rdfs#","concept");
	$cdata = $newConcept->ownerDocument->createCDATASection($xmlValue);
	$newConcept->appendChild($cdata);
	$newConcept->setAttribute("identifier",getID());
	$concepts->appendChild($newConcept);
	createMediaValue($domDoc,$formValue,$id);
}
if ($debug) {error_reporting(E_ALL);echo __FILE__;echo __LINE__;echo "<br />";}
?>