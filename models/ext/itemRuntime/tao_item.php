<?php
/*
	
   
   
    

    
    
    
    

    
    
    

*/
/**
* @package Widgets.etesting.authoringItem
* @author Plichart Patrick <patrick.plichart@tudor.lu>
* @package etesting
* @version 1.1
*/

//sleep(2);

if (!(isset($_SESSION))) {session_start();}


if (strpos($_SESSION["ITEMpreview"],"!{WS({http://")==0){
	$flash = BASE_URL.'/models/ext/itemRuntime/kohs_passation.swf';
} 
else {
	$flash = BASE_URL.'/models/ext/itemRuntime/tao_sql.swf';
}
echo '
<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" width="800" height="600" id="tao_item" align="middle">
<param name="allowScriptAccess" value="sameDomain" />
<param name="movie" value="'.$flash.'?'.urlencode(BASE_URL.'/models/ext/itemRuntime/TAOgetItemPreview.php').'&xml='.urlencode(BASE_URL.'/models/ext/itemRuntime/TAOgetItemPreview.php').'" />
<param name="quality" value="high" />
<param name="bgcolor" value="#ffffff" />
<embed src="'.$flash.'?localXmlFile='.urlencode(BASE_URL.'/models/ext/itemRuntime/TAOgetItemPreview.php').'&xml='.urlencode(BASE_URL.'/models/ext/itemRuntime/TAOgetItemPreview.php').'" quality="high" bgcolor="#ffffff" width="800" height="600" name="tao_item" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
</object>';
?>