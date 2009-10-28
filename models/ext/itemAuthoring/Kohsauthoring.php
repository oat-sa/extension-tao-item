<?php
/*
  
    
    
    

    
    
    

*/
/**
 * @package itemmodels.Kohs
 * TAO Authoring for kohs items
 * @author Plichart Patrick <patrick.plichart@tudor.lu>
* @version 1.1
*/



		
	$authoringSwfFile = "".$_SESSION["ext"]->httpLocation.$_SESSION["ext"]->widgets."itemAuthoring/kohs_authoring.swf";

	
	
		$instance=$_SESSION["ModelnsInstance"];
		$property=$_SESSION["ModelnsProperty"];
		$lg = $_SESSION["datalg"];
		switch ($lg)
			{
			case "FR":
			$submit = "Appliquer";break;
			case "EN":
			$submit = "Submit";break;
			default:
			$submit = "Appliquer";break;
			}
		echo '<div align="center">'."\n"
			.'<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" '."\n"
			.'codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" '."\n"
			.'width="500" height="400" id="kohs_authoring" align="middle">'."\n"
			.'<param name="allowScriptAccess" value="sameDomain" />'."\n"
			.'<param name="movie" value="'.$authoringSwfFile.'?xml=TAOgetItemPreview.php&instance='.$instance.'&property='.$property.'&str='.$submit.'" />'."\n"
			.'<param name="quality" value="high" />'."\n"
			.'<param name="bgcolor" value="#ffffff" />'."\n"
			.'<embed src="'.$authoringSwfFile.'?xml=TAOgetItemPreview.php&instance='.$instance.'&property='.$property.'&str='.$submit.'" '."\n"
			.'quality="high" bgcolor="#ffffff" width="500" height="400" '."\n"
			.'name="kohs_authoring" align="middle" allowScriptAccess="sameDomain" '."\n"
			.'type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />'."\n"
			.'</object>'."\n"
			.'</div>'."\n";
	
?>
