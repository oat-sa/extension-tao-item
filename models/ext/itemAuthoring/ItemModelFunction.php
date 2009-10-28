<?php

/**
* Retrieve itemModel and related swf file according to items definition in www.tao.lu/ontologies/TAOItem.rdf
*@param item URI of item
**/
function getItemModel($item)
	{
		$itemmodelURI = "";
		
		
		$modelitem =						calltoKernel('GetInstancePropertyValues',array($_SESSION["session"],array($item),array("http://www.tao.lu/Ontologies/TAOItem.rdf#ItemModel"),array("")));
						
		$executableswf =			calltoKernel('GetInstancePropertyValues',array($_SESSION["session"],$modelitem,array("http://www.tao.lu/Ontologies/TAOItem.rdf#SWFFile"),array("")));
		
		//*Old ditems made in tao v1 compatibility hack*/
		
		//if (strpos($modelitem[0],"#i20")!=false) return array("#i20","tao_item.swf");
		
		return array($modelitem[0],$executableswf[0]);
		

	}
?>