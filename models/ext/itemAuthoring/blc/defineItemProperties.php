<?php if (!(isset($_SESSION))) {session_start();}
header('Content-Type: text/html; charset=utf-8');
?>

<!--/Added by ppl -->



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">

	<head>
		<title>BLC Authoring</title>
		<link rel="stylesheet" href="../../../GenerisUI/CSS/getCSS.php" type="text/css" media="screen" />
		<link rel="stylesheet" href="blc.css" type="text/css" media="screen" />
		<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=UTF-8">
	</head>
	<body class=paneIframe style=margin:2%>

<?php
/*
 * Created on 21 sept. 07
 * 
 */
  
require_once "./BlcAuthoring.php";
 
if(!isset($doc))
{
	
	$xml = $_SESSION["OldXml"];
	
	$doc = getDomDocument($xml);
	$_SESSION["blcDomXml"] = $xml;
}
	
	
	if(isset($validate) && $validate  )
	{
	/*--Added by ppl */
	/*
	echo $_SESSION["OldXml"]."<br/>";
	echo $_SESSION["ModelnsProperty"]."<br/>";
	echo $_SESSION["ModelnsInstance"]."<br />";
	echo $xml;*/
	
	include("../../../taoAPI.php");
	
	

	$result=editPropertyValuesforInstance($_SESSION["session"],array($_SESSION["ModelnsInstance"]),array($_SESSION["ModelnsProperty"]),array($_SESSION["datalg"]),array($xml));

	
	//$_SESSION["OldXml"] = $xml;
	/*--/Added by ppl */
	echo "XML File Saved";
	
	
	
	}
?>
		<h1 class=Title>BLC Authoring</h1>
		<form action="./valideItemProperty.php" method="post">
		<p>
			<label for="item_title">Item Title</label>
			<input type="text" name="item_title" id="item_title" value="<?php echo getMediaValue($doc,getAttributeValue($doc,"assessmentItem","title")) ; ?>" />
		</p>
		<p>
			<label for="item_stimulus">Item Stimulus</label>
			<input type="text" name="item_stimulus" id="item_stimulus" value="<?php echo getMediaValue($doc,getAttributeValue($doc,"itenQuestion","value")) ; ?>" />
		</p>
		<p>
			<label for="item_prompt">Item Prompt</label>
			<input type="text" name="item_prompt" id="item_prompt" value="<?php echo getMediaValue($doc, getAttributeValue($doc,"itemPrompt","value")) ; ?>" />
		</p>
		<p>
			<label for="numberConcepts">Concepts Number</label>
			<input type="text" name="number_concepts" id="number_concepts" value="<?php echo  getAttributeValue($doc,"concepts","numberConcepts") ; ?>" />
			
		</p>
		
		



<?php

	$resource_count =1 ;
	$concept_count = 1;
	$relation_count = 1;
	$resources_lists = $doc->getElementsByTagName( "resourceList");
	
?>
		<p>
			<input type="submit" name="resources_lists" value="Ressources Lists" />
			<input type="submit" name="concepts_relations" value="Concepts and Relations" />		
		</p>
		
<?php
	
	if($flag_resources_list)
	{
	
		
		foreach($resources_lists as $resources_list)
		{
			
			$ressources_list_type = $resources_list->getAttribute("type");
			$list_title = "Ressource";
			switch($ressources_list_type){
				case "concept" : $list_title = "Concept";  break;
				case "relation" : $list_title = "Relation";  break;
				case "partition" : $list_title = "Partition"; break;
				
			}
		
?>
			
		<hr/>
		<h2 class=Title><?php echo $list_title."s"; ?> List</h2>
<?php
		$resources_list_id = $resources_list->getAttribute("identifier"); 
		$resources = $resources_list->getElementsByTagName("resource");
		
		
			foreach ($resources as $resource)
			{
				$resource_count++;
				$resource_id = $resource->getAttribute("identifier");
				$resource_value = getMediaValue($doc, $resource->nodeValue);
		
?>
		<p>
			<label  for="resource"><?php echo $list_title; ?></label>
			<input type="hidden" name="resource_list_id<?php echo $resource_count;?>" value="<?php echo $resources_list_id;?>" />
			<input type="hidden" name="resource_id<?php echo $resource_count;?>" value="<?php echo $resource_id;?>" />
			<input type="text" name="resource<?php echo $resource_count;?>" id="resource" value="<?php echo $resource_value; ?>" />
			<a href="./valideItemProperty.php?action=deleteResource&id=<?php echo $resource_id."&rlid=".$resources_list_id; ?>"><img src="./supprimer.gif" alt="supprimer" border=0 title="supprimer" /></a>	
		</p>

		
<?php		
				
			}
?>
		<p>
			<label  for="new_resource">New <?php echo $list_title; ?></label>
			<input type="hidden" name="new_resource_list_id<?php echo $resource_count;?>" value="<?php echo $resources_list_id;?>" />
			<input type="hidden" name="new_resource_list_type<?php echo $resource_count;?>" value="<?php echo $ressources_list_type;?>" />
			<input type="text" name="new_resource<?php echo $ressources_list_type.$resource_count;?>" value=""/>
			<input type="submit" name="add_resource" value="Add Resource" />
		</p>
<?php
		}

	}
	else if($flag_concepts_relations)
	{	
		
?>

	<hr/>
	<h2 class=Title>Pre-Defined Relations and Concepts</h2>
<?php
		
	
	 $concepts = $doc->getElementsByTagName("concept");
	
	 foreach($concepts as $concept)
	 {	
		
		$concept_id = $concept->getAttribute("identifier");
		$concept_value = getMediaValue($doc,$concept->nodeValue);
		
?>	
	<p>
			<label for="concept">Pre defined concept</label>
			<input type="hidden" name="concept_id<?php echo $concept_count;?>" value="<?php echo $concept_id;?>" />
			<input type="text" name="concept<?php echo $concept_count;?>" value="<?php echo $concept_value;?>" />
			<a href="./valideItemProperty.php?action=deleteConcept&id=<?php echo $concept_id; ?>"><img src="./supprimer.gif" alt="supprimer" border=0 title="supprimer" /></a>	
	</p>

		

<?php
	 	$concept_count ++;
	 }
?>
		<p>
			<label  for="new_concept">New concept</label>
			<input type="text" name="new_concept<?php echo $concept_count;?>" />
			<input type="submit" name="add_defined_concept" value="Add defined concept" />
		</p>
	<hr/>

	<table>
		<thead>
			<tr>
				<th>Concept Source</th>
				<th>Relation</th>
				<th>Direction</th>
				<th>Concept Destination</th>
			</tr>
		<thead>
		<tbody>
			
<?php
	 	$relations = $doc->getElementsByTagName("relation");
	 	
	 	foreach($relations as $relation)
	 	{
	 		$relation_id = $relation->getAttribute("identifier");
	 		$relation_value = getMediaValue($doc,$relation->nodeValue);
	 		$relation_source = $relation->getAttribute("source");
	 		$relation_dest = $relation->getAttribute("destination");
	 		$relation_dir = $relation->getAttribute("direction");

?> 	

			<tr>
				<th>
				<input type="hidden" name="relation_id<?php echo $relation_count;?>" value="<?php echo $relation_id;?>" />
				<select name="source_concept<?php echo $relation_count;?>" id="source_concept<?php echo $relation_count;?>" size="1"> 
						<option value=""></option>
<?php
	
			
			
			 foreach ($concepts as $concept)
			 {
			 	$concept_id = $concept->getAttribute("identifier");
			 	$concept_value = getMediaValue($doc, $concept->nodeValue);
			 	
			 		

?>	

						<option value="<?php echo $concept_id;?>" <?php if($relation_source === $concept_id)  echo "selected=\"true\"";?>><?php echo $concept_value;?></option> 
 	
<?php	 

				}
?>
				</select>
					
				</th>
				<th>
					<input type="text" name="relation<?php echo $relation_count;?>" value="<?php echo $relation_value;?>" />
				
				</th>
				<th>
					<select name="direction<?php echo $relation_count;?>" id="direction<?php echo $relation_count;?>" size="1"> 
						<option value=""></option>
						<option value="out" <?php if($relation_dir === "out") echo "selected=\"true\"";?>>=></option> 
						<option value="in" <?php if($relation_dir === "in") echo "selected=\"true\"";?>><=</option>
						<option value="both" <?php if($relation_dir === "both") echo "selected=\"true\"";?>><=></option>			
					</select>
				
				</th>
				<th>
				
					<select name="dest_concept<?php echo $relation_count;?>" id="dest_concept<?php echo $relation_count;?>" size="1"> 
					<option value=""></option>
<?php
	
				$selected = false;
				
				 foreach ($concepts as $concept)
				 {
				 	$concept_id = $concept->getAttribute("identifier");
				 	$concept_value = getMediaValue($doc, $concept->nodeValue);
			 
?>	
						
						<option value="<?php echo $concept_id;?>" <?php if($relation_dest === $concept_id) echo "selected=\"true\"";?>><?php echo $concept_value;?></option> 
 	
<?php	 

				}
?>
				</select>
					
				</th>
				<th>
					<a href="./valideItemProperty.php?action=deleteRelation&id=<?php echo $relation_id; ?>"><img src="./supprimer.gif" alt="supprimer" border=0 title="supprimer"  /></a>	
				</th>
			</tr>
	
<?php	 
			$relation_count++;
		}
?>			
			<tr>
			
			</tr>
			
			<tr>
				<th>
				
				<select name="source_concept<?php echo $relation_count;?>" id="source_concept<?php echo $relation_count;?>" size="1"> 
				<option value=""></option>
<?php
	
	
	
	 foreach ($concepts as $concept)
	 {
	 	$concept_id = $concept->getAttribute("identifier");
	 	$concept_value = getMediaValue($doc, $concept->nodeValue);
?>	
						
						<option value="<?php echo $concept_id;?>"><?php echo $concept_value;?></option> 
 	
<?php	 

	}
?>
						
				</select>
					
				</th>
				<th>
					<input type="text" name="new_relation<?php echo $relation_count;?>" value="" />
				</th>
				<th>
					<select name="direction<?php echo $relation_count;?>" id="direction<?php echo $relation_count;?>" size="1"> 
						<option value=""> </option>
						<option value="out">=></option> 
						<option value="in"><=</option>
						<option value="both"><=></option>			
					</select>
				</th>

				<th>
					<select name="dest_concept<?php echo $relation_count;?>" id="dest_concept<?php echo $relation_count;?>" size="1"> 
					<option value=""></option>
<?php
	
	
	
	 foreach ($concepts as $concept)
	 {
	 	$concept_id = $concept->getAttribute("identifier");
	 	$concept_value = getMediaValue($doc, $concept->nodeValue);
?>	

						<option value="<?php echo $concept_id;?>"><?php echo $concept_value;?></option> 
 	
<?php	 

	}
?>
						
				</select>
				</th>
				<th>
				</th>
				<th>
					<input type="submit" name="create_relation" value="Add new relation" />
				</th>
			</tr>
			
		</tbody>
	</table>
	
			
<?php	
	
	}	
	

?>	
	
	<input type="hidden" name="resource_count" value="<?php echo $resource_count;?>" />		
	<input type="hidden" name="concept_count" value="<?php echo $concept_count;?>" />
	<input type="hidden" name="relation_count" value="<?php echo $relation_count;?>" />				
		
		<hr/>
		<p>
			<input type="submit" name="validate" value="Validate" />	
		</p>

		</form>
			
<?php	
	
		
	

?>
	</body>
</html>
	