<?php
/**
* Generates a form to edit an item
* @package Widgets.etesting.authoringItem
* @author Plichart Patrick <patrick.plichart@tudor.lu>
* @version 1.1
*/
error_reporting("^E_NOTICE");
require_once($_SERVER['DOCUMENT_ROOT']."/generis/core/view/generis_ConstantsOfGui.php");	   
require_once($_SERVER['DOCUMENT_ROOT']."/generis/core/view/generis_utils.php");	
include_once($_SERVER['DOCUMENT_ROOT']."/generis/core/view/lg/".strtoupper($GLOBALS['lang']).".php");	

class TAOAuthoringGUI {
	
	protected $instance;
	protected $localXmlFile;

	/**
	 * cosntructor
	 * @param object $localXmlFile
	 * @param object $instance
	 * @return 
	 */	
	function __construct($localXmlFile, $instance){
		$this->instance = $instance;
		$this->localXmlFile = $localXmlFile;
	}
	
	/**
	 * load xml with an http request
	 * @return thee xml data
	 */
	private function loadXml(){
		$output = '';
		if(!empty($this->localXmlFile)){
			session_write_close();
			$curlHandler = curl_init();
			$url = $this->localXmlFile;
			if(!preg_match("/&$/", $url)){
				$url .= '&';
			}
			$url .= 'session_id=' . session_id();
			curl_setopt($curlHandler, CURLOPT_URL, $url);
			
			//if there is an http auth, it's mandatory to connect with curl
			if(USE_HTTP_AUTH){
				curl_setopt($curlHandler, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            	curl_setopt($curlHandler, CURLOPT_USERPWD, USE_HTTP_USER.":".USE_HTTP_PASS);
			}
			curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, 1);
			
			//to keep the session
			curl_setopt($curlHandler, CURLOPT_COOKIE, session_name(). '=' . $_COOKIE[session_name()] . '; path=/'); 
			$output = curl_exec($curlHandler);
			curl_close($curlHandler);  
		}
		return $output;
	}  
	
	function getIfUrl($string)
	{
		$r="^http://([_a-zA-Z0-9])+(\.[_a-zA-Z0-9])+(\.fr)|(\.com)|(\.org)|(\.net)";
		if (eregi($r, $Url))
		    return (1); //valide
		else
		    return (0); //non valide
	}
	
	function getInquiries($val,$values)
	{	
	
		$indexes = $this->getInquiriesindexes($val,$values);
		
		/*For each inquiries*/
		foreach ($indexes as $back=>$tab)
			{
				$start = $tab["start"];
				$end = $tab["end"];
				
				$order = $values[$theIndex]["attributes"]["ORDER"];
				

				$propositionindexes = $this->getPropositionindexes($start,$values);
				$propositions=array();
				foreach ($propositionindexes[0] as $keyi=>$vali)
					{
					$x=$values[$vali]["attributes"];
					$x["value"]=$values[$vali]["value"];
					$propositions[]=$x;
					}
				
				while ($start!=$end)
					{
						if ($values[$start]["tag"]=="TAO:ANSWERTYPE") {$answertype=$values[$start]["value"];}
						if ($values[$start]["tag"]=="TAO:INQUIRY") {
							$order = $values[$start]["attributes"]["ORDER"];
							$coords = $values[$start]["attributes"]["COORDS"];}
						if ($values[$start]["tag"]=="TAO:QUESTION") {$questiontype = $values[$start]["attributes"]["TYPE"];}
						
						if ($values[$start]["tag"]=="TAO:PROPOSITIONTYPE") {$propositionType = $values[$start]["value"];}
						if ($values[$start]["tag"]=="TAO:QUESTION") {$question = $values[$start]["value"];}
						if ($values[$start]["tag"]=="TAO:WIDGET") {$widget = $values[$start]["value"];}
						if ($values[$start]["tag"]=="TAO:ANSWERTYPE") {$answertype = $values[$start]["value"];}
						if ($values[$start]["tag"]=="TAO:EVALUATIONRULE") {$evaluationRule = $values[$start]["value"];}
						if ($values[$start]["tag"]=="TAO:HASGUIDE") {$hasGuide = $values[$start]["value"];}
						
						if ($values[$start]["tag"]=="TAO:HASANSWER") {$hasanswer = $values[$start]["value"];}
						$start++;
					}
					$inquiries[]=array("ORDER" => $order, "TYPE" => $questiontype,"QUESTION" => $question,"PROPOSITIONTYPE" => $propositionType,"WIDGET"=> $widget,"ANSWERTYPE" => $answertype, "EVALUATIONRULE" => $evaluationRule,"HASGUIDE"=>$hasGuide,"LISTPROPOSITION" => $propositions,"HASANSWER"=>$hasanswer);
			}
			
			
			return $inquiries;


	}

	function getInquiriesindexes($val,$values)
	{
		
		$i=-1;
		$indexes=array();
		foreach($val as $key => $val)
		{
			if ((($values[$val]["type"])=="open") and (($values[$val]["tag"])=="TAO:INQUIRY"))
			{
				$i++;
				$theIndex=$val;
				$theIndex++;
				$indexes[$i]["start"]=$theIndex-1;
				while 
				(
					(isset($values[$theIndex]))
					and
					!(
						(
							(($values[$theIndex]["type"])=="close")
							and
							(($values[$theIndex]["tag"])=="TAO:INQUIRY")
						)
					)
				)
				{$theIndex++;} 
				$indexes[$i]["end"]=$theIndex-1;$i++;
			}

		}
		
		return($indexes);
	}

	function getPropositionindexes($i,$values)
	{
	$prop=array();
	while 
		
	(
		(isset($values[$i])) 
		and
		(!
			(	
				($values[$i]["tag"]=="TAO:INQUIRY") and ($values[$i]["type"]=="close")
			) 
		)

	)
		{
			if ($values[$i]["tag"]=="TAO:PROPOSITION") {$last=$i;$prop[]=$i;}
			$i++;
		}
		return array($prop,$last);
	}


	function parseXML($xml)
	{
		
		$xml_parser=xml_parser_create("UTF-8");
		
	
		$items=array();$y=0;
		$xml=str_replace("&#180;","'",$xml);
		$xml =trim($xml);	
		$xml=str_replace("&Acirc;"," ",$xml);
		$xml=str_replace("&nbsp;"," ",$xml);
		/*TO DO instructions below really need refactoring */
		//MRE hack
		$xml=str_replace("� ",utf8_encode("� "),$xml);
		$xml=str_replace("� ",utf8_encode(" "),$xml);
		//hack related to the problem of sound inclusion with no escaped & , it happened at the cll, i didnt reproduced this problem

		$xml=str_replace("&logo=http","&amp;logo=http",$xml);
		$xml=str_replace("&isvisible=","&amp;isvisible=",$xml);
		$xml=str_replace("&delay=","&amp;delay=",$xml);
		
		$xml=str_replace("&nblisten=","&amp;nblisten=",$xml);
		$xml=str_replace("&allpause=","&amp;allpause=",$xml);
		$xml=str_replace("&allstop=","&amp;allstop=",$xml);

		$xml=ereg_replace('src="[^">]*>',"/>",$xml);
		$xml=ereg_replace('----MULTIMEDIA[^>]*--',"--",$xml);

		error_reporting("^E_NOTICE");
		xml_parse_into_struct($xml_parser, $xml, $values, $tags);
	
	
		 foreach ($tags as $key=>$val)
		 {	
			if ($key == "TAO:DISPLAYALLINQUIRIES")
				 { 
					foreach ($val as $x=>$theIndex)
					 {	
						
							$struct["TAO:DISPLAYALLINQUIRIES"]=$values[$theIndex]["value"];
													 
					 }
				 }

			if ($key == "TAO:PROBLEM")
				 { 
					foreach ($val as $x=>$theIndex)
					 {	
						$struct["TAO:PROBLEM"] = $values[$theIndex]["value"];
					 }
				 }
			
			if ($key == "TAO:DURATION")
				 { 
					foreach ($val as $x=>$theIndex)
					 {	
						$struct["TAO:DURATION"] = $values[$theIndex]["value"];
					 }
				 }
			
			
			if ($key == "TAO:INQUIRY")
				 { 
					$inquiries =array();
					$struct["INQUIRIES"] = $this->getInquiries($val,$values);
					
				 }
			//ADDED
			if ($key == "LABEL")
				 { $problemisalabel=FALSE;
					foreach ($val as $x=>$theIndex)
					 {	
						
						if ($values[$theIndex]["attributes"]["ID"]=="addQuestion_textbox") 
						 {
							$struct["TAO:SUBSIDIARYQUESTION"]=$values[$theIndex]["attributes"]["VALUE"];}
						
						if ($values[$theIndex]["attributes"]["ID"]=="itemLabel_label")
						 {
						 $struct["showLabel"] = "CHECKED";$y++;$displaylabel=true;
						 
						 
						 }

						 if ($values[$theIndex]["attributes"]["ID"]=="itemComment_label")
						 {
						 $struct["showComment"] = "CHECKED";$y++;
						 
						 
						 }

						
						if ($values[$theIndex]["attributes"]["ID"]=="problem_textbox")
						 {
						 $struct["problemeleft"] = $values[$theIndex]["attributes"]["LEFT"];
						 $struct["problemetop"]  = $values[$theIndex]["attributes"]["TOP"];
						
						 $problemisalabel=true;
						 }
						 if ($values[$theIndex]["attributes"]["ID"]=="question_textbox")
						 {
						 if ($problemisalabel) {$p=$x-$y-1;} else {$p=$x-$y;}
						 $inquiryleftstring = "inquiryleft".$p;
						 $inquirytopstring = "inquirytop".$p;
						 $struct[$inquiryleftstring] = $values[$theIndex]["attributes"]["LEFT"];
						 $struct[$inquirytopstring]  = $values[$theIndex]["attributes"]["TOP"];
						 
						 }


					 }
				 }
			if ($key == "TEXTBOX")
				 { 
					foreach ($val as $x=>$theIndex)
					 {	
						if ($values[$theIndex]["attributes"]["ID"]=="fte")
						 {
							
							$struct["fteleft"][] = $values[$theIndex]["attributes"]["LEFT"];
						    $struct["ftetop"][]  = $values[$theIndex]["attributes"]["TOP"];
						    $struct["fteheight"][] = $values[$theIndex]["attributes"]["HEIGHT"];
						   $struct["ftewidth"][]  = $values[$theIndex]["attributes"]["WIDTH"];
						 }

						if ($values[$theIndex]["attributes"]["ID"]=="problem_textbox")
						 {
						$struct["problemeleft"] = $values[$theIndex]["attributes"]["LEFT"];
						 $struct["problemetop"]  = $values[$theIndex]["attributes"]["TOP"];
						  $struct["problemeheight"] = $values[$theIndex]["attributes"]["HEIGHT"];
						 $struct["problemewidth"]  = $values[$theIndex]["attributes"]["WIDTH"];
						 		  
						 }

						 if ($values[$theIndex]["attributes"]["ID"]=="question_textbox")
						 {
						if (!$displaylabel)
							{	
								if ($problemisalabel) {$p=$x-$y;}
								else
								{$p=$x-$y-1;}
							} 
							else 
								{
								if ($problemisalabel) {$p=$x-$y+1;}
								else
								{$p=$x-$y;}
								}
						 $inquiryleftstring = "inquiryleft".$p;
						 $inquirytopstring = "inquirytop".$p;
						
						 $struct[$inquiryleftstring] = $values[$theIndex]["attributes"]["LEFT"];
						 $struct[$inquirytopstring]  = $values[$theIndex]["attributes"]["TOP"];
						 }
					 }
				 }

			if ($key == "RADIO")
				 { 
					foreach ($val as $x=>$theIndex)
					 {	
						if (!((strpos($values[$theIndex]["attributes"]["ID"],"proposition"))===false))
						 {
								$struct["propositionsradioleft"][]=$values[$theIndex]["attributes"]["LEFT"];
								$struct["propositionsradiotop"][]=$values[$theIndex]["attributes"]["TOP"];
								
						 }

						 if ($values[$theIndex]["attributes"]["ID"]=="addQuestion_prop_1_radio") 
							 $struct["TAO:SUBSIDIARYP1"]=$values[$theIndex]["attributes"]["LABEL"];
						 if ($values[$theIndex]["attributes"]["ID"]=="addQuestion_prop_2_radio") 
							 $struct["TAO:SUBSIDIARYP2"]=$values[$theIndex]["attributes"]["LABEL"];
						 if ($values[$theIndex]["attributes"]["ID"]=="addQuestion_prop_3_radio") 
							 $struct["TAO:SUBSIDIARYP3"]=$values[$theIndex]["attributes"]["LABEL"];
						 if ($values[$theIndex]["attributes"]["ID"]=="addQuestion_prop_4_radio") 
							 $struct["TAO:SUBSIDIARYP4"]=$values[$theIndex]["attributes"]["LABEL"];
						  if ($values[$theIndex]["attributes"]["ID"]=="addQuestion_prop_5_radio") 
							 $struct["TAO:SUBSIDIARYP5"]=$values[$theIndex]["attributes"]["LABEL"];
					 }
				 }
			
			if ($key == "CHECKBOX")
				 { 
					foreach ($val as $x=>$theIndex)
					 {	
						if (!((strpos($values[$theIndex]["attributes"]["ID"],"proposition"))===false))
						 {
								$struct["propositionscheckleft"][]=$values[$theIndex]["attributes"]["LEFT"];
								$struct["propositionschecktop"][]=$values[$theIndex]["attributes"]["TOP"];
								
						 }
					 }
				 }
			if ($key == "BOX")
				 { 
					foreach ($val as $x=>$theIndex)
					 {	
						if ($values[$theIndex]["attributes"]["ID"]=="additionalQuestion_box") 
						 {
							$struct["TAO:SUBSIDIARYQUESTIONTOP"]=$values[$theIndex]["attributes"]["TOP"];
							$struct["TAO:SUBSIDIARYQUESTIONLEFT"]=$values[$theIndex]["attributes"]["LEFT"];
						 }
					 }
				 }
		




			if ($key == "BUTTON")
				 { 
					foreach ($val as $x=>$theIndex)
					 {	

						if ($values[$theIndex]["attributes"]["ID"]=="submitQuery_button") 
						 {
								$callws =$values[$theIndex]["attributes"]["ONCOMMAND"];
								$wsdl=substr($callws,6,strpos($callws,"}")-6);
								$struct["wsdl"]=$wsdl;
								$service=substr($callws,strpos($callws,"}")+3,strpos($callws,"},{GETVALUE")-(strpos($callws,"}")+3));
								$struct["service"]=$service;


						 }

						if (!((strpos($values[$theIndex]["attributes"]["ID"],"prevInquiry_button"))===false))
						 {
								$struct["navleft"]=$values[$theIndex]["attributes"]["LEFT"];
								$struct["navtop"]=$values[$theIndex]["attributes"]["TOP"];
								$struct["urlleft"]=$values[$theIndex]["attributes"]["URL"];
								
						 }
						 if (!((strpos($values[$theIndex]["attributes"]["ID"],"nextInquiry_button"))===false))
						 {
								
								$struct["urlright"]=$values[$theIndex]["attributes"]["URL"];
								
						 }
					 }
				 }

			
					 
		 }
	
	xml_parser_free($xml_parser);
	

	return $struct;
	}

	function getOutput(){
		
		$SCRIPT='';
		$output='';
		
		$instance = $this->instance;
		$property = 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemContent';
		error_reporting(E_ALL);
		$_SESSION["ClassInd"]=$instance;
		$xml = $this->loadXml();
		
		
		$struct = $this->parseXML($xml);
		
		
		$uri = tao_helpers_Uri::encode($instance);
		$item = new core_kernel_classes_Resource($instance);
		$type = $item->getUniquePropertyValue(new core_kernel_classes_Property('http://www.w3.org/1999/02/22-rdf-syntax-ns#type'));
		$classUri = tao_helpers_Uri::encode($type->uriResource);
		$previewUri = "/taoItems/Items/preview?uri=$uri&classUri=$classUri";

		$chechedinabox="";
			if ((strpos($xml,'<textbox id="problem_textbox"'))===false) {
					$chechedinabox="";
			} 
			else {
				$chechedinabox="CHECKED";
			}
			$showLastQuestion = false;
		//Change number of questions 
			if (isset($_SESSION["nbinq"])) 
			{ 
				$nb = $_SESSION["nbinq"];
				while ($nb>0) {
					$nb--;
					$struct["INQUIRIES"][]=array();
				}
				unset($_SESSION["nbinq"]);
			}
			//print_r($_SESSION);
			if (isset($_SESSION["AddInquiry"])) {
				unset($_SESSION["AddInquiry"]);
				$struct["INQUIRIES"][]=array();
				$showLastQuestion = true;
			}
			if (isset($_SESSION["removeInquiry"]))
			{
				$keys = array_keys($_SESSION["removeInquiry"]);
				unset($struct["INQUIRIES"][$keys[0]]);
				$struct["INQUIRIES"] = array_values($struct["INQUIRIES"]);
				$showLastQuestion = true;
				unset($_SESSION["removeInquiry"]);
			}

		$item=getButtonimage(PREVIEW,false);	
		$output.='<html><head>';
		$output.='<script type="text/javascript">
						var _editor_url="/generis/core/view/HTMLArea-3.0-rc1/";
						var _editor_lang="en";
				</script>';
		$output.='<script type="text/javascript" src="/generis/core/view/HTMLArea-3.0-rc1/htmlarea.js"></script>';
		$output.='<script type="text/javascript" src="/filemanager/views/js/fmRunner.js"></script>';
		$output.='<link rel="stylesheet" type="text/css" href="/generis/core/view/HTMLArea-3.0-rc1/htmlarea.css" />';
		$output.='<link rel="stylesheet" type="text/css" href="/generis/core/view/CSS/generis_default.css" />';
		$output.='<style type="text/css">input[type=button],input[type=submit]{cursor:pointer; padding:4px; font-weight:bold;}</style>';
		$output.='</head><body>';
		$output.='
		<FORM id="myQCMForm" enctype="multipart/form-data" action=index.php name=newressource target=_top method=post><input type=hidden name=MAX_FILE_SIZE value=2000000>
		<input type=hidden name=Authoring['.$instance.']['.$property."] />
		<SCRIPT LANGUAGE=\"Javascript1.2\">
		function phighlight(zelement)
		{
			document.getElementById('PContent').style.visibility='hidden';		
		";
		error_reporting("^E_NOTICE");
		foreach ($struct["INQUIRIES"] as $p=>$v)
				{
					$nm=$p+1;//index of inquiry(for user)
				$output.="document.getElementById('Q".$nm."Content').style.visibility='hidden';
				";
				}
		$output.="
		
		document.getElementById('PrStyle').style.visibility='hidden';
		document.getElementById('template').style.visibility='hidden';
		document.getElementById(zelement).style.visibility='visible';
		}
		function refreshQCM(){
			var myForm = document.getElementById('myQCMForm');
			if(myForm){
				myForm.action = '';
				myForm.target = '';
			}
		}
		function fullScreen(url){
			window.open(url, 'tao', 'width=800,height=600,menubar=no,toolbar=no');
		}
		</SCRIPT>
		";
		$output.="<input type=hidden name='instance' value='$instance'>";
		$output.="<input type=hidden name=itemcontent[instance] value=$instance>";
		$output.="<input type=hidden name=itemcontent[property] value=$property>";
		$output.='<span style=position:absolute;left:0px;z-index: 99999999;overflow:visible;>
		<input type=button onClick="phighlight(\'PContent\');" value="'.PROBCONT.'" />
		
		 <input type=button onClick="phighlight(\'PrStyle\');"  value="'.PARAMETERS.'" />
		';
		foreach ($struct["INQUIRIES"] as $p=>$v)
				{
					$nm=$p+1;//index of inquiry(for user)
				$output.=' <input type=button onClick="phighlight(\'Q'.$nm.'Content\');" value="'.QUESTION.' '.$nm.'" />';
				}
		$output.='
		<input type=button onClick="phighlight(\'template\');" value="Template" />
		<input type=button onClick="FmRunner.load();" value="File Manager" />
		<input type=submit  name=AddInquiry onclick="refreshQCM();" value="'.ADDAQUESTION.'" />
		<input type=button  onClick="fullScreen(\''.$previewUri.'\'); return false;" name=saveContent value="'.PREVIEW.'" />
		<input type=submit onclick="refreshQCM();" value="'.APPLY.'">
		<input type=submit value="Save and close">
		</span><br /><br />';



		error_reporting("^E_NOTICE");
		$output.='<A NAME="PContent" class=mainpane style="font-family:verdana;font-size:10;" id="PContent">';
				$output.='<input '.$chechedinabox.' type=checkbox id=pbminabox name=itemcontent[tao:inabox] >'.INABOX.' <br> ';
				
				$output.=LEFT.'<input type=text name=itemcontent[tao:problemleft] id=leftproblem size=2 value='.$struct["problemeleft"] .'>'.TOP.'<input type=text size=2 name=itemcontent[tao:problemtop] id=topproblem value='.$struct["problemetop"] .'>'.WIDTH.'<input type=text name=itemcontent[tao:problemwidth] size=2 id=problemwidth value='.$struct["problemewidth"] .'>'.HEIGHT.'<input type=text  size=2 name=itemcontent[tao:problemheight] id=problemheight value='.$struct["problemeheight"] .'>';
				$output.='<TEXTAREA NAME="itemcontent[tao:problem]" COLS=80 ROWS=25>'.$struct["TAO:PROBLEM"].'</TEXTAREA>';
				
				$output.=''.ADD.' <input size=2 type=textbox name=nbinq> '.NBQUESTIONS.SAND.' <input size=2 type=textbox name=nbprop> '.NBPROPOSITIONS.'';
		
		$output.='</A>';


		//echo $struct["TAO:DISPLAYALLINQUIRIES"];
		if ($struct["TAO:DISPLAYALLINQUIRIES"]=="on") $displayall="checked"; else $displayall="";
		
		$output.='<A NAME="PrStyle" class=mainpane style="visibility:hidden;font-family:verdana;font-size:10;" id=PrStyle>';
				$output.='<b>'.ITEMPARAMS.'</b><br /><br />
							'.DURATION.' : <input type=text name=itemcontent[tao:duration] value='.$struct["TAO:DURATION"].'><br /><br />
				'.SHOWLABEL.'<input '.$struct["showLabel"].' type=checkbox name=itemcontent[tao:showLabel]><br /><br />
				'.SHOWCOMMENT.'<input '.$struct["showComment"].' type=checkbox name=itemcontent[tao:showComment]><br /><br />
				Display All inquiries <input '.$displayall.' type=checkbox name=itemcontent[tao:displayAllInquiries]><br /><br />				
				<br />';
				$output.='<br />
							'.SUBSIDIARYQUESTION.' : <input type=text size=35 name=itemcontent[tao:subsidiaryquestion] value="'.$struct["TAO:SUBSIDIARYQUESTION"].'"><br /><br />'.TOP.'<input type=text size=2 name=itemcontent[tao:subsidiaryquestiontop] value='.$struct["TAO:SUBSIDIARYQUESTIONTOP"].'>'.LEFT.'<input type=text size=2 name=itemcontent[tao:subsidiaryquestionleft] value='.$struct["TAO:SUBSIDIARYQUESTIONLEFT"].'><br /><br />	<input type=text size=50 name=itemcontent[tao:subsidiaryp1] value="'.$struct["TAO:SUBSIDIARYP1"].'"><br>	<input type=text size=50 name=itemcontent[tao:subsidiaryp2] value="'.$struct["TAO:SUBSIDIARYP2"].'">	<br>
							<input type=text size=50 name=itemcontent[tao:subsidiaryp3] value="'.$struct["TAO:SUBSIDIARYP3"].'">	<br><input type=text size=50 name=itemcontent[tao:subsidiaryp4] value="'.$struct["TAO:SUBSIDIARYP4"].'"><br><input type=text size=50 name=itemcontent[tao:subsidiaryp5] value="'.$struct["TAO:SUBSIDIARYP5"].'"><br>';
				$output.= '<br />Navigation<br />'.NAVTOP.'<input size=2 type=text name=itemcontent[navtop] id=navtop value='.$struct["navtop"] .'><br />
								'.NAVLEFT.'<input size=2 type=text name=itemcontent[navleft] id=navleft value='.$struct["navleft"] .'><br />
								'.URLLEFT.'</td><td><input size=50 type=text name=itemcontent[urlleft] value='.$struct["urlleft"] .'><br />
								'.URLRIGHT.'<input size=50 type=text name=itemcontent[urlright] value='.$struct["urlright"] .'><br />';
		
				
		
		$output.='</A>';
		
		$output.='<A NAME="template" class=template style="visibility:hidden;font-family:verdana;font-size:10;" id=template>';
		$output.= '
				<script>
		function template2()
		{
			
			//alert(document.forms[0].elements[4].name);
			document.getElementById("pbminabox").checked=true;
			
			document.getElementById("leftproblem").value=0;
			document.getElementById("topproblem").value=0;
			document.getElementById("problemwidth").value=800;
			document.getElementById("problemheight").value=600;
			document.getElementById("navleft").value=550;
			document.getElementById("navtop").value=500;

			idinquiry=0;
			
			while (idinquiry<=10)
			{
				try
				{
				idone="leftinquiry"+idinquiry;
				idtwo="topinquiry"+idinquiry;
				document.getElementById(idone).value=50;
				document.getElementById(idtwo).value=100;
				
				idproposition=0;
				top=0;
				while (idproposition<=10)
				{
					try
					{
					idpropone="propositionleft"+idinquiry+idproposition;
					idproptwo="propositiontop"+idinquiry+idproposition;
					top=idproposition*20+100;
					document.getElementById(idpropone).value=10;
					document.getElementById(idproptwo).value=top;

					idproposition=idproposition+1; 
					}
					catch(err)
						{
						idproposition=idproposition+1; 
						}
				}
				idinquiry=idinquiry+1;
				}
				catch(err)
						{
						idinquiry=idinquiry+1; 
						}

			}
			alert(\'Coordinates changed !\');
		}
		

		function template1()
		{
			
			document.getElementById("pbminabox").checked=true;
			document.getElementById("leftproblem").value=0;
			document.getElementById("topproblem").value=0;
			document.getElementById("problemwidth").value=800;
			document.getElementById("problemheight").value=600;
			document.getElementById("navleft").value=550;
			document.getElementById("navtop").value=500;

			idinquiry=0;
			alert(\'Coordinates changed !\');
			while (idinquiry<=10)
			{
				try
				{
					idone="leftinquiry"+idinquiry;
					idtwo="topinquiry"+idinquiry;
					document.getElementById(idone).value=50;
					document.getElementById(idtwo).value=100;
					
					idproposition=0;
					top=0;
					while (idproposition<=10)
					{
						try
						{
						idpropone="propositionleft"+idinquiry+idproposition;
						idproptwo="propositiontop"+idinquiry+idproposition;
						left=idproposition*250;
						document.getElementById(idpropone).value=left;
						document.getElementById(idproptwo).value=100;

						idproposition=idproposition+1; 
						}
						catch(err)
						{
						idproposition=idproposition+1; 
						}
					}
					idinquiry=idinquiry+1;
				}
				catch(err)
				{
				idinquiry=idinquiry+1;
				}

			}
			
		}
		

		function template3()
		{
			
			//alert(document.forms[0].elements[4].name);
			document.getElementById("pbminabox").checked=true;
			document.getElementById("leftproblem").value=0;
			document.getElementById("topproblem").value=0;
			document.getElementById("problemwidth").value=800;
			document.getElementById("problemheight").value=600;
			document.getElementById("navleft").value=550;
			document.getElementById("navtop").value=500;

			idinquiry=0;
			
			while (idinquiry<=10)
			{
				try
				{
				idone="leftinquiry"+idinquiry;
				idtwo="topinquiry"+idinquiry;
				document.getElementById(idone).value=50;
				document.getElementById(idtwo).value=100;
				
				idproposition=0;
				top=0;
				while (idproposition<=10)
				{
					try
					{
					idpropone="propositionleft"+idinquiry+idproposition;
					idproptwo="propositiontop"+idinquiry+idproposition;
					
							
								
						
					if (idproposition % 2 == 0)
					{
					document.getElementById(idpropone).value=10;
					top=idproposition*20+100;
					}
					else
					{
					document.getElementById(idpropone).value=300;
					top=(idproposition-1)*20+100;
					}
					document.getElementById(idproptwo).value=top;

					idproposition=idproposition+1; 
					}
					catch(err)
						{
						idproposition=idproposition+1; 
						}
				}
				idinquiry=idinquiry+1;
				}
				catch(err)
						{
						idinquiry=idinquiry+1; 
						}

			}
			alert(\'Coordinates changed !\');
		}



		</script>
		
		
			<br /><br /><br />Template :<br /><input type=radio onClick=template1(); name=template><img width=220 src=/generis/core/view/icons/template01.gif /><input type=radio name=template onClick=template2();><img width=220 src=/generis/core/view/icons/template02.gif /><input type=radio name=template onClick=template3();><img width=220 src=/generis/core/view/icons/template03.gif /><br />';
		$output.='</A>';

		foreach ($struct["INQUIRIES"] as $p=>$v){
			$nm=$p+1;//index of inquiry(for user)
			$output.='<A NAME="Q'.$nm.'Content" class=mainpane style="visibility:hidden;font-family:verdana;font-size:10;" id="Q'.$nm.'Content"><br>';
			$output.='<input type=submit name=removeInquiry['.$p.'] value=Remove&nbsp;this&nbsp;question onclick="refreshQCM();"><br />';
			$inquiryleftstring = "inquiryleft".$p;$inquirytopstring = "inquirytop".$p;	$proptype[0]="";$proptype[1]="";
			if ($v["PROPOSITIONTYPE"]=="Exclusive Choice") {$proptype[0]="SELECTED";$proptype[1]="";$proptype[2]="";}
			if ($v["PROPOSITIONTYPE"]=="Multiple Choice") {$proptype[1]="SELECTED";$proptype[0]="";$proptype[2]="";}
			if ($v["PROPOSITIONTYPE"]=="Text") {$proptype[2]="SELECTED";$proptype[1]="";$proptype[0]="";}
			$widgetopt[0]="";$widgetopt[1]="";
			
			if ($v["WIDGET"]=="FLASH Radio Button") {$relevantarrayleft= "propositionsradioleft";$relevantarraytop= "propositionsradiotop";$widgetopt[0]="SELECTED";$widgetopt[1]="";$widgetopt[2]="";}
			if ($v["WIDGET"]=="FLASH Check Button") {$relevantarrayleft= "propositionscheckleft";$relevantarraytop= "propositionschecktop";	$widgetopt[1]="SELECTED";$widgetopt[0]="";$widgetopt[2]="";}
			

			if ($v["WIDGET"]=="textbox") {
				
				$relevantarrayleft= "fteleft";$relevantarraytop= "ftetop";	$widgetopt[1]="";$widgetopt[0]="";$widgetopt[2]="SELECTED";}

			$evrule[0]="";$evrule[1]="SELECTED";$evrule[2]="";
			if ($v["EVALUATIONRULE"]=="AND.swf") {$evrule[0]="";$evrule[1]="SELECTED";}
			if ($v["EVALUATIONRULE"]=="STRAND.swf") {$evrule[1]="";$evrule[0]="SELECTED";}
			if ($v["EVALUATIONRULE"]=="MNF_1") {$evrule[2]="SELECTED";$evrule[1]="";}

			$output.='<span class=slighltylright  >'.LEFT.'<input size=2 type=text id=leftinquiry'.$p.' name=itemcontent['.$inquiryleftstring.'] value='.$struct[$inquiryleftstring] .'><br />'.TOP.'
			<input size=2 type=text id=topinquiry'.$p.' name=itemcontent['.$inquirytopstring.'] value='.$struct[$inquirytopstring] .'><br>
			<SELECT NAME="itemcontent[tao:inquiry]['.$p.'][proposition type]">
				<option value=Exclusive@Choice '.$proptype[0].'>Exclusive Choice
				<option value=Multiple@Choice '.$proptype[1].'>Multiple Choice
				<option value=Text '.$proptype[2].'>Text input
				</select><br>'.WIDGET.'<i> * Combo/Text input is not implemented yet * </i><SELECT NAME="itemcontent[tao:inquiry]['.$p.'][widget]">				
				<option value=FLASH@Radio@Button '.$widgetopt[0].'>RadioButton
				<option value=FLASH@Check@Button '.$widgetopt[1].'>CheckBox
				<option value=textbox '.$widgetopt[2].'>TextBox (*NEW)
				<option value=FLASH@Radio@Button>ComboBox
				<option value=FLASH@Radio@Button>ListBox
				</select><br>
				'.EVALUATIONRULE.'
			 <SELECT  DISABLED NAME="itemcontent[tao:inquiry]['.$p.'][evalrule]">
				
				<option value=AND.swf '.$evrule[0].'>Compare 2 strings<option value=AND.swf '.$evrule[1].'>Compare 2 vectors (EXACT)<option value=MNF_1 '.$evrule[2].'>Compare 2 vectors(At least one/No fault)</select><br />
				
				wsdl : <input size=50 type=text name=itemcontent[wsdl] value='.$struct["wsdl"] .'><br /> Service : <input size=10 type=text name=itemcontent[service] value='.$struct["service"] .'><br>
				
			</span>
			<TEXTAREA NAME=itemcontent[tao:inquiry]['.$p.'][question] COLS=80 ROWS=14 >'.$v["QUESTION"].'</TEXTAREA>

				<input type=submit name=AddProp['.$p.'] value=Add&nbsp;a&nbsp;Proposition onclick="refreshQCM();"><br /> <br />
			';
				//echo $v["QUESTION"];

			$hansw = IntegerToArray($v["HASANSWER"]);
			foreach ($hansw as $a=>$b){
				if ($v["HASANSWER"][$a]=="1") {
					$answer[$a]="CHECKED";
				} 
				else {
					$answer[$a]="";
				} 
			}
			if (isset($_SESSION["AddProp"][$p])) {
				$v["LISTPROPOSITION"][]=array();
				unset($_SESSION["AddProp"]);
			}
			if (isset($_SESSION["nbprop"])) {
				$nb = $_SESSION["nbprop"];
				while ($nb>0) {
					$nb--;
					$v["LISTPROPOSITION"][]=array();
				}
			}

			if (isset($v["LISTPROPOSITION"])){
				if (isset($_SESSION["removeProposition"][$p])){
					$keys = array_keys($_SESSION["removeProposition"][$p]);
					unset($v["LISTPROPOSITION"][$keys[0]]);
					$v["LISTPROPOSITION"] = array_values($v["LISTPROPOSITION"]);
					unset($_SESSION["removeProposition"]);
				}
				foreach ($v["LISTPROPOSITION"] as $a=>$b){
					if (($hansw[$a])==0) {
						$ch="";
					} 
					else {
						$ch="CHECKED";
					}
					$num=$a+1;
							
					$form='itemcontent[tao:inquiry]['.$p.'][proposition]['.$a.'][value]';
					$propositionleftstring = "propositionleft".$p.$a;
					$propositiontopstring = "propositiontop".$p.$a;
					
					$output.='<br /><br />Proposition '.$num.' '.LEFT.'<input size=2 type=text name=itemcontent['.$propositionleftstring.'] id='.$propositionleftstring.' value='.array_shift($struct[$relevantarrayleft]).'>
					'.TOP.'<input size=2 type=text name=itemcontent['.$propositiontopstring.'] id='.$propositiontopstring.' value='.array_shift($struct[$relevantarraytop]).'> '.CORRECTANSWER.' <input type=Checkbox name=itemcontent[tao:inquiry]['.$p.'][proposition]['.$a.'][good] '.$ch.'>
					
					';
					$output.=''.$script.'&nbsp;

					<input type=submit name=removeProposition['.$p.']['.$a.'] value=Remove&nbsp;this&nbsp;proposition onclick="refreshQCM();"><br>
					<TEXTAREA NAME="itemcontent[tao:inquiry]['.$p.'][proposition]['.$a.'][value]" COLS=80 ROWS=10>'.$b["value"].'</TEXTAREA>
									
					';
				}
			}
			$output.='</A>';
		}
		
		$output.='</form>';
		$output.='<script language="javascript" type="text/javascript" defer="1">
			HTMLArea.replaceAll();';
		if($showLastQuestion){
			$last = count($struct["INQUIRIES"]);
			$output.="phighlight('Q{$last}Content');";
		}
		$output.='</script>';
		$output.='</body><html>';
			
		return $output;
	}
	   
}
?>