<?php

/**
* Builds and save xml of an item using posted data
* @package Widgets.etesting.authoringItem
* @author Plichart Patrick <patrick.plichart@tudor.lu>
* @version 1.1
*/
require_once($_SERVER['DOCUMENT_ROOT']."/generis/core/view/generis_ConstantsOfGui.php");
class TAOsaveContent
{
	function TAOsaveContent()
	{
	}
	function isTextBox($string)
		{
			if ((strpos($string,"<textbox ")===0)) 
				{;}
				else
				{return false;}
			$string=str_replace("<textbox ","",$string);
			$length=strlen($string)-2;
			
			$suffix = substr($string,$length,2);
			
			if ($suffix!="/>") return false;
			


			return true;
		}
	function validateliteral($literal,$removetextboxes=false)
	{
		if (ACTIVATEMOODLE)
		{
				$problemboxes="";
				$literal=str_replace("\\","",$literal);
				$literal=str_replace("<strong>","<b>",$literal);
				$literal=str_replace("</strong>","</b>",$literal);
				$literal=str_replace("<STRONG>","<b>",$literal);
				$literal=str_replace("</STRONG>","</b>",$literal);
				if (!$removetextboxes)
				{
				$boxoccurences=array();
				preg_match_all("-\\-TEXTBOX[^\\-]*\\--",$literal,$boxoccurences);
				preg_match_all("-\\-MULTIMEDIA[^\\-]*\\--",$literal,$boxoccurences);
				$problemboxes="";
					if (is_array($boxoccurences))
					{
						 while(list($x,$value)=each($boxoccurences[0]))
							{
								if ($value!="0")
									{
											$problemboxes.="-".htmlentities($value)."-";
									}
							}
					}
					else {$problemboxes="";}
				
				$problemboxes=str_replace("&amp;quot;",'"',$problemboxes);
				}
				
				$literal =ereg_replace("--MULTIMEDIA[^-]*--" , "" , $literal );

				$literal = htmlspecialchars($literal,ENT_QUOTES,"utf-8");
				$literal = trim($literal);
				$literal.=$problemboxes;
				
		}

		
		return $literal;
	}
	

	function getOutput($ressource)
	{
		
		if(!isset($_SESSION["datalg"])){
			$_SESSION["datalg"] = $GLOBALS['lang'];
		}
		
		$instance = $ressource["instance"];
		$property = $ressource["property"];
		$output="";
			
		$item = new core_kernel_classes_Resource($instance);
		$label = $item->getLabel();
		$comment =  $item->comment;
		$script = $instance;

	
		$type="String";
		$ressource["tao:problem"]=str_replace("<p>&nbsp;</p>","",$ressource["tao:problem"]);
		$ressource["tao:problem"]=str_replace("<p>","",$ressource["tao:problem"]);
		$ressource["tao:problem"]=str_replace("<strong>","<b>",$ressource["tao:problem"]);
		$ressource["tao:problem"]=str_replace("</strong>","</b>",$ressource["tao:problem"]);
		$ressource["tao:problem"]=str_replace("<STRONG>","<b>",$ressource["tao:problem"]);
		$ressource["tao:problem"]=str_replace("</STRONG>","</b>",$ressource["tao:problem"]);
		$ressource["tao:problem"]=str_replace("</p>","",$ressource["tao:problem"]);
		$ressource["tao:problem"]=str_replace("<p />","<br>",$ressource["tao:problem"]);
		$ressource["tao:problem"]=str_replace("<img","<p><img",$ressource["tao:problem"]);
		$ressource["tao:problem"]=str_replace("&amp;logo=http","&logo=http",$ressource["tao:problem"]);
		$ressource["tao:problem"]=str_replace("&amp;isvisible=","&isvisible=",$ressource["tao:problem"]);
		$ressource["tao:problem"]=str_replace("&amp;delay=","&delay=",$ressource["tao:problem"]);
		$ressource["tao:problem"]=str_replace("&amp;nblisten=","&nblisten=",$ressource["tao:problem"]);
		$ressource["tao:problem"]=str_replace("&amp;allpause=","&allpause=",$ressource["tao:problem"]);
		$ressource["tao:problem"]=str_replace("&amp;allstop=","&allstop=",$ressource["tao:problem"]);
		
		
		$problem='<tao:PROBLEM lang="'.$_SESSION["datalg"].'" type="'.$type.'">'.trim($this->validateliteral($ressource["tao:problem"])).'</tao:PROBLEM>';

		
		
		$xml=$problem;
		
		$sizeofpronlem = strlen(trim($ressource["tao:problem"]));
		if ($sizeofpronlem==0) {;}
		else
		{
		
		$sizeofpronlem = 5 +(round($sizeofpronlem / 68) * 20);
		if ($sizeofpronlem>100) {$sizeofpronlem=100;}
		if ($sizeofpronlem<40) {$sizeofpronlem=23;}
		}
		
		if (isset($ressource["tao:inquiry"]))
		{
		$maxsize=0; //Contains top size of inquiry box among all inquiries
		
		foreach ($ressource["tao:inquiry"] as $a=>$b)
			{
			$listeners="";
			$orderInq=$a+1;
			$inquiry = '<tao:INQUIRY order="'.$orderInq.'">';
			
			if (strpos($b["question"],"http")==0)
				{$type="String";} else {$type="URL";}
			
			$question = '<tao:QUESTION lang="'.$_SESSION["datalg"].'" type="'.$type.'">'.$this->validateliteral($b["question"]).'</tao:QUESTION>';
			
			$proptype = str_replace('@',' ',$b["proposition type"]);
			$widgetopt = str_replace('@',' ',$b["widget"]);
			
			//Default value for widget to use in xul part
			$xmlwidget="radiogroup";
			if ($widgetopt == "FLASH Check Button") {$xmlwidget="checkbox";}
			if ($widgetopt == "FLASH Radio Button") {$xmlwidget="radio";
			
			$clistener =ereg_replace("--MULTIMEDIA[^-]*--" , "" , $this->validateliteral($b["question"]) ) ;
			//Link a listener of user's answer to the radiogroup
			$listeners.='<tao:ITEMBEHAVIOR tao:LISTENERNAME="Answered : '.$clistener.'" src="#{XPATH(/tao:ITEM/tao:INQUIRY[@order='.$orderInq.']/tao:INQUIRYDESCRIPTION/tao:HASPRESENTATIONLAYER/xul/box/box/radiogroup)}#"/>';
			}

			if ($widgetopt == "textbox") {$xmlwidget="textbox";}

			$propositionbox="";
			if ($xmlwidget=="radio")
				{$propositionbox='<radiogroup id="propositions_radiogroup">';}
			
			

			$inder=0;$order=1;$left=5;
			
			
			if (isset($b["proposition"]))
			{
			$factor =10; // RJa 20071030 20 -> 10
			$four =false;
			$nbPropositions = sizeOf($b["proposition"]);
			/*if four proposition , props are set left and write*/
			if ($nbPropositions==4) {$four =true;}
			foreach ($b["proposition"] as $ind=>$valu)
				{
					/*Search for inclusions of multimedia files into proposition */
					$occurences=Array();
					eregi("--MULTIMEDIA(.)*--",$valu["value"],$occurences) ;
					
					$propimages="";
					//factor is gap between propositions greater if propositions contains images
					$factor =10; // RJa 20071030 20 -> 10
					if (sizeOf($occurences)>0)
					{
						$factor =85;
						 while(list($x,$value)=each($occurences))
							{
								if ($value!="0")
								{
								$value=preg_replace("/\-\-MULTIMEDIA(.*?)url\=/","<image src=",$value);
								$value=str_replace("--","/>",$value);
								$propimages.=str_replace('\"','"',$value);
								}

							}
					 }
					$propimages=  substr($propimages,0,strlen($propimages)-1);
					if (($four) and ($factor > 10) and ($ind== 2)) {$left = 170;$inder=1;} // RJa 20071030 20 -> 10
					//added
					if ((isset($ressource["propositionleft".$a.$ind])) and ($ressource["propositionleft".$a.$ind]!="") and ($ressource["propositionleft".$a.$ind]!="0")){$lef=$ressource["propositionleft".$a.$ind];} else {$lef=$left;}
					
					if ((isset($ressource["propositiontop".$a.$ind])) and ($ressource["propositiontop".$a.$ind]!="")  and ($ressource["propositiontop".$a.$ind]!="0")){$to=$ressource["propositiontop".$a.$ind];} else {$to=$inder;}

/*
					if ($xmlwidget=="textbox")
					{
					$propositionbox.='						
					<textbox left="15" top="-5" width="700" wrap="true" height="80" value="'.validateliteral($valu["value"]).'" />
					';
					}
					else
					{
					$propositionbox.='
						<'.$xmlwidget.' id="proposition_'.$order.'_'.$xmlwidget.'"  left="'.$lef.'" top="'.$to.'" width="1000" selected="false" label="" >
									<textbox left="15" top="-5" width="760" height="40" style="border-style:none" value="'.validateliteral($valu["value"]).'" />
						</'.$xmlwidget.'>';
					}	

*/


					if ($xmlwidget=="textbox")
						//($this->isTextBox($valu["value"]))
						{		
								$propositionbox.='						
								<textbox id="fte" left="'.$lef.'" top="'.$to.'" width="680" wrap="true" height="130" value="'.$this->validateliteral($valu["value"]).'" />
								';
								/*
								$propositionbox.='
								<'.$xmlwidget.' id="proposition_'.$order.'_'.$xmlwidget.'" left="'.$lef.'" top="'.$to.'" width="1000" selected="false" label="">'.$valu["value"].'</'.$xmlwidget.'>';
								*/
						}
					else 
						{
							$propositionbox.='
								<'.$xmlwidget.' id="proposition_'.$order.'_'.$xmlwidget.'" left="'.$lef.'" top="'.$to.'" width="1000" selected="false" label="'.$this->validateliteral($valu["value"]).'">'.$propimages.'</'.$xmlwidget.'>';
						}
										
					if ($xmlwidget=="checkbox") {$listeners.='<tao:ITEMBEHAVIOR tao:LISTENERNAME="Answered : '.$this->validateliteral($b["question"]).' #{XPATH(/tao:ITEM/tao:INQUIRY[@order='.$orderInq.']/tao:INQUIRYDESCRIPTION/tao:LISTPROPOSITION/tao:PROPOSITION[@order='.$order.'])}#" src="#{XPATH(/tao:ITEM/tao:INQUIRY[@order='.$orderInq.']/tao:INQUIRYDESCRIPTION/tao:HASPRESENTATIONLAYER/xul/box/box/checkbox[@id=proposition_'.$order.'_'.$xmlwidget.'])}#"/>';}
					$inder=$inder+$factor;$order=$order+1;

				}

			if ($inder>$maxsize) {$maxsize=$inder;}
           	}                    
            if ($xmlwidget=="radio") {$propositionbox.='</radiogroup>';}
			/****/
			/*Search for inclusions of multimedia files into question */
			$myQuestion =  $b["question"];
			$mymatches = array();
			preg_match_all('/\<img(.*?)\>/', $myQuestion, $mymatches);
			if(isset($mymatches[0])){
				foreach($mymatches[0] as $amatch){
					$newmatch = str_replace("src", "url", $amatch);
					$newmatch = str_replace("<img", "--MULTIMEDIA", $newmatch);
					$newmatch = str_replace("/>", "--", $newmatch);
					$myQuestion = str_replace($amatch, $newmatch,  $myQuestion);
				}
			}

			eregi("--MULTIMEDIA(.)*--",$myQuestion,$occurences) ;
				$inqimages="";
				if (is_array($occurences))
				{
				 while(list($x,$value)=each($occurences))
					{
						if ($value!="0")
						{
							
						$value=preg_replace("/\-\-MULTIMEDIA(.*?)url\=/","<image src=",$value);
						$value=str_replace("--","/>",$value);
						$inqimages.=str_replace('&quot;','"',$value);
						

						}

					}
				 }
			$inqimages = substr($inqimages,0,strlen($inqimages)-1);
	
			$xulpropositions =  '<box id="propositions_box" left="10" top="13">
                        '.$propositionbox.'</box>'; // RJa 20071030 30 -> 13
			//ADDED INQUIRY POSITION
			
			if ((isset($ressource["inquiryleft".$a])) and ($ressource["inquiryleft".$a]!="")){$left=$ressource["inquiryleft".$a];} else {$left="0";}
			if ((isset($ressource["inquirytop".$a])) and ($ressource["inquirytop".$a]!="")){$top=$ressource["inquirytop".$a];} else {$top="0";}

		/*Subsidiary question management , ex. : confidence level question*/
		$subsidiaryquestion="";
		if ((isset($ressource["tao:subsidiaryquestion"])) and ($ressource["tao:subsidiaryquestion"]!="") )

				{
					$subsidiaryquestion.='<box top="'.$ressource["tao:subsidiaryquestiontop"].'" id="additionalQuestion_box" left="'.$ressource["tao:subsidiaryquestionleft"].'">';
					$subsidiaryquestion.='<label style="" top="" value="'.$this->validateliteral($ressource["tao:subsidiaryquestion"]).'" id="addQuestion_textbox" class="question" left="10"/>';
					
					$subsidiaryquestion.='<radiogroup id="addQuestion_prop_radiogroup" width="400">';
					
					if ((isset($ressource["tao:subsidiaryp1"])) and ($ressource["tao:subsidiaryp1"]!="") )
					{
					$subsidiaryquestion.='<radio top="20" selected="false" label="'.$this->validateliteral($ressource["tao:subsidiaryp1"]).'" id="addQuestion_prop_1_radio" width="400" left="5"/>';
					}
					if ((isset($ressource["tao:subsidiaryp2"])) and ($ressource["tao:subsidiaryp2"]!="") )
					{
					$subsidiaryquestion.='<radio top="40" selected="false" label="'.$this->validateliteral($ressource["tao:subsidiaryp2"]).'" id="addQuestion_prop_2_radio" width="400" left="5"/>';
					}
					if ((isset($ressource["tao:subsidiaryp3"])) and ($ressource["tao:subsidiaryp3"]!="") )
					{
					$subsidiaryquestion.='<radio top="60" selected="false" label="'.$this->validateliteral($ressource["tao:subsidiaryp3"]).'" id="addQuestion_prop_3_radio"  width="400" left="5"/>';
					}
					if ((isset($ressource["tao:subsidiaryp4"])) and ($ressource["tao:subsidiaryp4"]!="") )
					{
					$subsidiaryquestion.='<radio top="80" selected="false" label="'.$this->validateliteral($ressource["tao:subsidiaryp4"]).'" id="addQuestion_prop_4_radio" width="400" left="5"/>';
					}
					if ((isset($ressource["tao:subsidiaryp5"])) and ($ressource["tao:subsidiaryp5"]!="") )
					{
					$subsidiaryquestion.='<radio top="100" selected="false" label="'.$this->validateliteral($ressource["tao:subsidiaryp5"]).'" id="addQuestion_prop_5_radio" width="400" left="5"/>';
					}


					
					$subsidiaryquestion.='</radiogroup>';
					$subsidiaryquestion.='</box>';
				}



$callws='';
if (isset($ressource["wsdl"]) and (($ressource["wsdl"])!="")) 
{
		$callws = '
		<box top="22" id="proposal_box" left="0">
<textbox top="165" value="" height="80" width="700" multiline="true" wrap="true" id="proposal_textbox" left="0"/>
</box>
<button top="275" left="400" width="200" oncommand="!{WS({'.$ressource["wsdl"].'},{'.$ressource["service"].'},{GETVALUE(pSubmission=proposal_textbox)},{SETVALUE(compileResult_textbox=pResult)})}!" label="Submit Query" disabled="false" id="submitQuery_button"/>

<textbox top="325" value="" height="140" width="700" multiline="true" id="compileResult_textbox" left="0"/>

		';
}
$cquestion =ereg_replace("--MULTIMEDIA[^-]*--" , "" , $this->validateliteral($b["question"]) ) ;
			
			$xulinquiry='
			<tao:HASPRESENTATIONLAYER><xul>
                    <box id="inquiryContainer_box" left="0" top="0">
					<textbox id="question_textbox" wrap="true" style="borderStyle:none" readonly="true" width="700" height="45" left="'.$left.'" top="'.$top.'" class="question" value="'.$cquestion.'" />'.$callws.'
            			'.$inqimages.'
						'.$xulpropositions.'
                       
                    </box>
					'.$subsidiaryquestion.'
                </xul></tao:HASPRESENTATIONLAYER>
			
			';
			error_reporting("^E_NOTICE");
			switch ($b["evalrule"])
				{	
				case "MNF_1":{$evaluationrule="MNF_1";break;}
				default:{$evaluationrule="AND.swf";break;}
				}

			
			$inquiryDescription="
			<tao:INQUIRYDESCRIPTION><tao:PROPOSITIONTYPE>".$proptype."</tao:PROPOSITIONTYPE>
			<tao:WIDGET>".$widgetopt."</tao:WIDGET>
			<tao:PROPLISTENERS>$listeners</tao:PROPLISTENERS>
			<tao:ANSWERTYPE>Exclusive Vector</tao:ANSWERTYPE>
			<tao:EVALUATIONRULE>".$evaluationrule."</tao:EVALUATIONRULE>
			<tao:HASGUIDE>technicalID.hlp</tao:HASGUIDE>
			$xulinquiry
			";
			$listproposition="<tao:LISTPROPOSITION>";
			$hasanswer =array();
			if (isset($b["proposition"]))
				{
			$hasanswer=array();
			
			foreach ($b["proposition"] as $ind=>$valu)
				{
					$porder=$ind+1;
					if (strpos($valu["value"],"http")==0)
					{$type="String";} else {$type="URL";}

					if ($this->isTextBox($valu["value"])) {$aproposistiontext=htmlentities(htmlentities($valu["value"],ENT_QUOTES,"utf-8"));} else {$aproposistiontext=$this->validateliteral($valu["value"]);}

					$listproposition.='<tao:PROPOSITION lang="'.$_SESSION["datalg"].'" type="'.$type.'" Id="'.$porder.'" order="'.$porder.'" answer="0">'.$aproposistiontext.'</tao:PROPOSITION>';
					if (isset($valu["good"])) {$hasanswer[]="1";} else {$hasanswer[]="0";}
				}
				}
			
			$theanswer="";
			if (sizeof($hasanswer)>0)
				{
			foreach ($hasanswer as $e=>$r)
				{
					$theanswer.=$r;
				}
				} 
			
			$close="</tao:LISTPROPOSITION>
			<tao:HASANSWER>".$theanswer."</tao:HASANSWER>
			</tao:INQUIRYDESCRIPTION>
			</tao:INQUIRY>";

			$xml.=$inquiry.$question.$inquiryDescription.$listproposition.$close;
			
			}
		}
			$xml.="</tao:ITEM>";


				
				$prevandnextbuttons=65+$sizeofpronlem;
				$progressbarandimagetop=95+$sizeofpronlem;
				$containertop=110+$sizeofpronlem;

				
				$box=' <box id="inquiriesAccessButtons_box" left="0" top="0">';
				if (isset($ressource["tao:inquiry"]))
				{
				$order=1;
				$posbuttons=110;
				foreach ($ressource["tao:inquiry"] as $a=>$b)
				{
				$box.='
							<button id="inquiryAccessor_'.$order.'_button" left="'.$posbuttons.'" top="'.$prevandnextbuttons.'" label="'.$order.'" image="not_done.jpg" disabled="true" oncommand="tao_item.gotoInquiry('.$order.')"/>
					';
					$posbuttons=$posbuttons+55;
					$order=$order+1;
				}
				}
				$box.=  '</box>';
/****/
/*Search for inclusions of multimedia files into problem */

$tao_problem = $ressource["tao:problem"];

$mymatches = array();
	preg_match_all('/\<img(.*?)\>/', $tao_problem, $mymatches);
	if(isset($mymatches[0])){
		foreach($mymatches[0] as $amatch){
			$newmatch = str_replace("src", "url", $amatch);
			$newmatch = str_replace("<img", "--MULTIMEDIA", $newmatch);
			$newmatch = str_replace("/>", "--", $newmatch);
			$tao_problem = str_replace($amatch, $newmatch,  $tao_problem);
		}
	}

	$tao_problem =ereg_replace("--TEXTBOX[^-]*--" , "" ,$tao_problem ) ;
	eregi("--MULTIMEDIA(.)*--",$tao_problem, $occurences) ;

	$problemimages="";
	if (is_array($occurences))
	{
	 while(list($x,$value)=each($occurences))
		{
			if ($value!="0")
			{
				
			$value=preg_replace("/\-\-MULTIMEDIA(.*?)url\=/","<image src=",$value);
			$value=str_replace("--","/>",$value);

			$problemimages.=str_replace('&quot;','"',$value);
			}
		}
	 }
	  $problemimages=substr($problemimages,0,strlen($problemimages)-1);

$tao_problem =ereg_replace("--MULTIMEDIA[^-]*--" , "" ,$tao_problem ) ;


eregi("--TEXTBOX(.)*--",$tao_problem, $boxoccurences) ;

$problemboxes="";
	if (is_array($boxoccurences))
	{
	 while(list($x,$value)=each($boxoccurences))
		{
			if ($value!="0")
			{
				
			$value=str_replace("--TEXTBOX","<textbox",$value);
		$value=str_replace("--","/>",$value);
			$problemboxes.=str_replace('&quot;','"',$value);
			}
		}
	 }
	 $problemboxes=substr( $problemboxes,0,strlen( $problemboxes)-1);
$problemimages.=$problemboxes;



/*********************Includes the problem and its coordinates****************/
	
	if ($sizeofpronlem>0) {
	
	
	if ((isset($ressource["tao:problemleft"])) and ($ressource["tao:problemleft"]!="")){$left=$ressource["tao:problemleft"];} else {$left="45";}
	if ((isset($ressource["tao:problemtop"])) and ($ressource["tao:problemtop"]!="")){$top=$ressource["tao:problemtop"];} else 
		{if (isset($ressource["tao:showLabel"])) {$top="50";}else {$top="15";}}
	if ((isset($ressource["tao:problemwidth"])) and ($ressource["tao:problemwidth"]!="")){$width=$ressource["tao:problemwidth"];} else {$width="435";}
	if ((isset($ressource["tao:problemheight"])) and ($ressource["tao:problemheight"]!="")){$height=$ressource["tao:problemheight"];} else {$height=$sizeofpronlem;}
	
	

		if (!(isset($ressource["tao:inabox"])) or (($ressource["tao:inabox"])!="on"))
		{
			if($top <= 15 && !empty($problemimages)) $top = "50";
			$xulproblem='<label id="problem_textbox" left="'.$left.'" top="'.$top.'" multiline="true" wrap="true" value="'.$this->validateliteral($ressource["tao:problem"],true).'"/>'.$problemimages;
		}
		else
		{
			$xulproblem='<textbox id="problem_textbox" left="'.$left.'" top="'.$top.'" width="'.$width.'" height="'.$height.'" multiline="true" wrap="true" value="'.$this->validateliteral($ressource["tao:problem"],true).'"/>';//.$problemimages;
		}
	}
	else
	{$xulproblem="";}
/*******************************************************************************************/
/************************************Includes inquiries navigation widget***************/
error_reporting("^E_NOTICE");				
	if ((sizeof($ressource["tao:inquiry"]))>1)
					{
				
				if 
					(
					(!(isset($ressource["navtop"])))
					or
					($ressource["navtop"]=="")
					) 
					{$ressource["navtop"]=$prevandnextbuttons;}
				
				if ((!(isset($ressource["navleft"]))) or ($ressource["navleft"]=="")) {$ressource["navleft"]="45";}
				$ressource["navleftnext"]=$ressource["navleft"]+55;
				$ressource["navleftprogressbar"]=$ressource["navleftnext"]+60;

				if 
					(
					(!(isset($ressource["urlleft"])))
					or
					($ressource["urlleft"]=="")
					) 
					{$ressource["urlleft"]="http://www.tao.lu/middleware/itempics/default/left.swf";}
				
				if 
					(
					(!(isset($ressource["urlright"])))
					or
					($ressource["urlright"]=="")
					) 
					{$ressource["urlright"]="http://www.tao.lu/middleware/itempics/default/right.swf";}
				

				

				$inquiriesnav='<button id="prevInquiry_button" left="'.$ressource["navleft"].'" top="'.$ressource["navtop"].'" label="&lt;" image="inquiry_previous.jpg" url="'.$ressource["urlleft"].'" disabled="true" oncommand="tao_item.prevInquiry"/>
								<button id="nextInquiry_button" left="'.$ressource["navleftnext"].'" top="'.$ressource["navtop"].'" label="&gt;" image="inquiry_next.jpg" url="'.$ressource["urlright"].'" disabled="true" oncommand="tao_item.nextInquiry"/>
							   <!--<progressmeter id="item_progressmeter" left="'.$ressource["navleftprogressbar"].'" top="'.$ressource["navtop"].'" mode="determined" value="0"/>-->';

	
					}
					else
					{
					$inquiriesnav='';$containertop=$containertop-100;
					}

/****************************Includes Label And Comment or not*************************/
$labelandcomment ='';
if (isset($ressource["tao:showLabel"])) 
		{
				$labelandcomment .='
				<label id="itemLabel_label" left="10" top="10" class="Label" value="&lt;b&gt;&lt;u&gt;#{XPATH(/tao:ITEM/rdfs:LABEL)}#&lt;/u&gt;&lt;/b&gt;" style=\'\'/>
				';$containertop=$containertop+20;

		}
if (isset($ressource["tao:showComment"])) 
		{
				$labelandcomment .='
								<label id="itemComment_label" left="25" top="30" class="Comment" value="#{XPATH(/tao:ITEM/rdfs:COMMENT)}#"/>';
				$containertop=$containertop+20;
		}
/************************************************************************************/



				$xulITEM='
				<tao:ITEMPRESENTATION>
						<xul>
							<stylesheet id="item_stylesheet" src="./item.css"/>
							<box id="itemContainer_box" class="item">
								'.$labelandcomment.'
								'.$xulproblem.'
								'.$inquiriesnav.'
								<box id="inquiryContainer_box" left="5" top="100"/>
								
							</box>
						</xul>
					</tao:ITEMPRESENTATION>
			'; // RJa 20071030 '.$containertop.' -> 566    'cause 566 = 768 - 29 of header - 173 height required by new specs



		
		$xml=
		"<?xml version='1.0' encoding='UTF-8' ?>
		<tao:ITEM xmlns:rdf='http://www.w3.org/1999/02/22-rdf-syntax-ns#' rdf:ID=\"$script\" xmlns:tao='http://www.tao.lu/tao.rdfs#'
		 xmlns:rdfs='http://www.w3.org/TR/1999/PR-rdf-schema-19990303#'>
		<rdfs:LABEL lang=\"".$_SESSION["datalg"]."\">".strip_tags($label)."</rdfs:LABEL>
		<rdfs:COMMENT lang=\"".$_SESSION["datalg"]."\">".strip_tags($comment)."</rdfs:COMMENT>
		
		<tao:DISPLAYALLINQUIRIES>".$ressource["tao:displayAllInquiries"]."</tao:DISPLAYALLINQUIRIES>
		
		<tao:DURATION>".$ressource["tao:duration"]."</tao:DURATION>
		".$xulITEM."
		<tao:ITEMLISTENERS></tao:ITEMLISTENERS>".$xml;
		$xml = str_replace('<image src="listen.swf?','<image disabled="false" src="'.BASE_URL.'/models/ext/itemRuntime/listen.swf?',$xml);


		$_SESSION["Authoring"]=array($ressource["instance"]=> array($ressource["property"]=>""));		
		$xml = str_replace('\\\'','\'',$xml);		
		
		return $xml;
	
	}
}
?>