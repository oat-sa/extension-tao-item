<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>QTI Item <?=$identifier?></title>

	<!-- CSS -->
	<link rel="stylesheet" type="text/css" href="<?=$ctx_base_www?>js/QTI/css/reset.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="<?=$ctx_base_www?>js/QTI/css/qti.css" media="screen" />
	
	<!-- user CSS -->
	<?foreach($stylesheets as $stylesheet):?>
		<link rel="stylesheet" type="text/css" href="<?=$stylesheet['href']?>" media="<?=$stylesheet['media']?>" />
	<?endforeach?>
	
	<!-- LIB -->
	<script type="text/javascript" src="<?=$ctx_taobase_www?>js/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="<?=$ctx_taobase_www?>js/jquery-ui-1.8.custom.min.js"></script>
	
	<!-- JS REQUIRED -->
	<script type="text/javascript" src="<?=$ctx_base_www?>js/QTI/src/Widget.js"></script>
	<script type="text/javascript" src="<?=$ctx_base_www?>js/QTI/src/ResultCollector.js"></script>
	<script type="text/javascript" src="<?=$ctx_base_www?>js/QTI/src/init.js"></script>
	<script type="text/javascript" src="<?=$ctx_base_www?>js/taoMatching/taoMatching.min.js"></script>
	<script type="text/javascript">

		var myEvaluateCallbackFunction = function () {
			// Get the ouctomes
			var outcomes = matching_getOutcomes();
			console.log ('THE OUTCOME VALUE SCORE IS : '  + outcomes['SCORE']['value']);
		}
	
		var qti_initParam  = new Object();
		var matching_param = {
<?php if ($matchingEngineServerSide) { ?>
			"url" : "<?=isset($url)?$url:'null'?>"
			, "params" : <?=isset($params)?$params:'null'?>
<?php } else { ?>
			"data" : {
				"outcomes" : <?=isset($outcomes)?$outcomes:'[]'?>
				, "corrects" : <?=isset($corrects)?$corrects:'[]'?>
				, "maps" : <?=isset($maps)?$maps:'[]'?>
				, "rule" : '<?=isset($rule)?$rule:'""'?>'
			}
<?php } ?>
			, "options" : {
				"evaluateCallback" : function () {
					myEvaluateCallbackFunction ();
				}
			}
			, "format" : "json"
		};

		$(document).ready(function(){
			qti_init(qti_initParam);
			matching_init (matching_param);	
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
