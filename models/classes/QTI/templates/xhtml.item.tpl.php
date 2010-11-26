<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>QTI Item <?=$identifier?></title>

	<!-- CSS -->
    <link rel="stylesheet" type="text/css" href="<?=$ctx_base_www?>js/QTI/css/qti.min.css" media="screen" />
	<!-- user CSS -->
	<?foreach($stylesheets as $stylesheet):?>
		<link rel="stylesheet" type="text/css" href="<?=$stylesheet['href']?>" media="<?=$stylesheet['media']?>" />
	<?endforeach?>
	
	<!-- LIB -->
	<script type="text/javascript" src="<?=$ctx_taobase_www?>js/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="<?=$ctx_taobase_www?>js/jquery-ui-1.8.4.custom.min.js"></script>
	<script type="text/javascript" src="<?=$ctx_taobase_www?>js/json2.js"></script>
	
	<!-- JS REQUIRED -->
	
	<script type="text/javascript" src="<?=$ctx_base_www?>js/taoApi/taoApi.min.js"></script>
	<script type="text/javascript" src="<?=$ctx_root_url?>/wfEngine/views/js/wfApi/wfApi.min.js"></script>
	<script type="text/javascript" src="<?=$ctx_base_www?>js/taoMatching/taoMatching.min.js"></script>
	<script type="text/javascript" src="<?=$ctx_base_www?>js/QTI/qti.min.js"></script>
	<!--  -->
	
	<script type="text/javascript">
		var qti_initParam  	= new Object();
		var matchingParam 	= new Object();
		var responseToId	= new Object();
	
		$(document).ready(function(){

			//check if the values have been saved in a context
			if(typeof(getRecoveryContext) == 'function'){
				for(serial in qti_initParam){
					if(qti_initParam[serial]['responseIdentifier']){
						try{
							responseToId[qti_initParam[serial]['responseIdentifier']] = qti_initParam[serial]['id'];
							qti_initParam[serial]['values'] = $.parseJSON(getRecoveryContext(qti_initParam[serial]['id']));
						} catch(parseException) { }
					}
				}
			}

			//initialize the QTI widgets
			qti_init(qti_initParam);

            // validation process - catch event after all interactions have collected their data
            $("#qti_validate").bind("click",function(){

            	if(typeof(matchingGetResponses) == 'function' && typeof(setAnsweredValues) == 'function'){
	            	//push the answered values 
	            	var responses = matchingGetResponses();
	            	var answeredValues = null;
	            	for(key in responses){
	                	if(answeredValues == null){
	                		answeredValues = new Object();
	                	}
	            		answeredValues[responses[key]['identifier']] = responses[key]['value'];
	
	            		//set the answered values in the context
	            		setRecoveryContext(responseToId[responses[key]['identifier']], JSON.stringify(responses[key]['value']));
	            	}
	            	if($.isPlainObject(answeredValues)){
	                	//set the answered values to the taoApi 
						setAnsweredValues(JSON.stringify(answeredValues));
	            	}
	            	
	                // Evaluate the user's responses
	                matchingEvaluate ();
            	}
            });
			
		});
	</script>
</head>
<body>
	<div class="qti_item">
		<h1><?=$options['title']?></h1>
	
		<?=$data?>
		
		<!-- validation button -->
		<a href="#" id="qti_validate">Validate</a>
	</div>
</body>
</html>
