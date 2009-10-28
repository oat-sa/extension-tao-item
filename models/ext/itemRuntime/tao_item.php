<?php
/*
	
   
   
    

    
    
    
    

    
    
    

*/
/**
* @package Widgets.etesting.authoringItem
* @author Plichart Patrick <patrick.plichart@tudor.lu>
* @package etesting
* @version 1.1
*/

sleep(2);

if (!(isset($_SESSION))) {session_start();}


if (strpos($_SESSION["ITEMpreview"],"!{WS({http://")==0)
					{$flash="tao_item.swf";} else {$flash="tao_sql.swf";}

$output= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>


<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>tao_item</title>
</head>
<body bgcolor="#ffffff">
<!--url  s used in the movie-->
<!--text used in the movie-->
<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" width="800" height="600" id="tao_item" align="middle">
<param name="allowScriptAccess" value="sameDomain" />
<param name="movie" value="'.$flash.'?localXmlFile=TAOgetItemPreview.php" />
<param name="quality" value="high" />
<param name="bgcolor" value="#ffffff" />
<embed src="'.$flash.'?localXmlFile=TAOgetItemPreview.php" quality="high" bgcolor="#ffffff" width="800" height="600" name="tao_item" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
</object>

</body>
</html>';
echo $output;
?>