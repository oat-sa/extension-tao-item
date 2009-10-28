<?php
/*
	
   
   
    

    
    
    
    

    
    
    

*/
/**
* Generates a form to edit an item
* @package Widgets.etesting.authoringItem
* @author Plichart Patrick <patrick.plichart@tudor.lu>
* @version 1.1
*/
error_reporting(E_ALL);

	
	function getXML($idinstance,$idproperty)
	{
		error_reporting("^E_NOTICE");
		
		$result = calltoKernel('getInstanceDescription',array($_SESSION["session"],array($idinstance),array("")));
		
		$_SESSION["label"]=$result["pDescription"]["label"];
		
		foreach ($result["pDescription"]["PropertiesValues"][0] as $key=>$val)
		{
			if ($val["PropertyKey"]==$idproperty) {$xml=$val["PropertyValue"];  
			
			return $xml;}
		}
	}

	

	
		
		$SCRIPT='';
		$output='';
		
		foreach ($ressource as $key=>$val)
			{
				$instance=$key;
				foreach ($val as $keyu=>$valu)
				{$property=$keyu;}
			}
		
		$_SESSION["ClassInd"]=$instance;
		$xml = getXML($instance,$property);
		
		
			
		
		
		$output.='<FORM enctype="multipart/form-data" action="http://'.$_SERVER["HTTP_HOST"].'/generis/core/widgets/ItemModels/savexml.php" name=newressource method=post><input type=hidden name=MAX_FILE_SIZE value=2000000>';
		
		
		
		$output.='<TEXTAREA NAME="xml" COLS=100 ROWS=50>'.str_replace("&amp;#180;","'",htmlspecialchars($xml)).'</TEXTAREA>';
		
		
		$apply="http://".$_SERVER["HTTP_HOST"].'/Generis/generis/GenerisWidgets/ItemModels/apply.jpg';
		
		$item="http://".$_SERVER["HTTP_HOST"].'/Generis/generis/GenerisWidgets/ItemModels/preview.jpg';
		
		$output.="<input type=hidden name=itemcontent[instance] value=$instance>";
		$output.="<input type=hidden name=itemcontent[property] value=$property>";
		$output.="<tr><td colspan=4><input type=image src=$apply name=saveContent><a href='http://".$_SERVER["HTTP_HOST"].'/Generis/generis/GenerisWidgets/itempreview/tao_item.php?i='.substr($instance,2).'&PHPSESSID='.session_id().' target=_BLANK><img border=0 src='.$item.'></a></i></td></tr>';
		$output.='</table></td></tr>';
		$output.=TABLEFOOTER;
		$output.='</form>';
		

		echo $output;

	



?>