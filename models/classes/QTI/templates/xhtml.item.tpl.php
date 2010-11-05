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
	<script type="text/javascript" src="<?=$ctx_taobase_www?>js/json2.js"></script>
	
	<!-- JS REQUIRED -->
	
	<script type="text/javascript" src="<?=$ctx_base_www?>js/taoApi/taoApi.min.js"></script>
    <script type="text/javascript" src="<?=$ctx_base_www?>js/taoMatching/src/class.Matching.js"></script>
    <script type="text/javascript" src="<?=$ctx_base_www?>js/taoMatching/src/class.MatchingRemote.js"></script>
    <script type="text/javascript" src="<?=$ctx_base_www?>js/taoMatching/src/class.VariableFactory.js"></script>
    <script type="text/javascript" src="<?=$ctx_base_www?>js/taoMatching/src/class.Variable.js"></script>
    <script type="text/javascript" src="<?=$ctx_base_www?>js/taoMatching/src/class.BaseTypeVariable.js"></script>
    <script type="text/javascript" src="<?=$ctx_base_www?>js/taoMatching/src/class.Collection.js"></script>
    <script type="text/javascript" src="<?=$ctx_base_www?>js/taoMatching/src/class.List.js"></script>
    <script type="text/javascript" src="<?=$ctx_base_www?>js/taoMatching/src/class.Tuple.js"></script>
    <script type="text/javascript" src="<?=$ctx_base_www?>js/taoMatching/src/class.Map.js"></script>
    <script type="text/javascript" src="<?=$ctx_base_www?>js/taoMatching/src/matching_constant.js"></script>
    <script type="text/javascript" src="<?=$ctx_base_www?>js/taoMatching/src/matching_api.js"></script>
	<!--<script type="text/javascript" src="<?=$ctx_base_www?>js/taoMatching/taoMatching.min.js"></script>-->
	<script type="text/javascript" src="<?=$ctx_base_www?>js/QTI/qti.min.js"></script>
	<script type="text/javascript">

		var myEvaluateCallbackFunction = function () {
			// Get the ouctomes
			var outcomes = matching_getOutcomes();
			console.log ('THE OUTCOME VALUE SCORE IS : '  + outcomes['SCORE']['value']);
			
			finish();
		};
	
		var qti_initParam  = new Object();
		var matching_param = new Object();
	
		$(document).ready(function(){
			matching_param= {
<?php if ($ctx_delivery_server_mode) { ?>
			"url" : "<?=$ctx_root_url.'/'.$matching['url']?>"
			, "params" : { "token" : getToken() }
<?php } else { ?>
			"data" : <?=json_encode($matching['data'])?>
<?php } ?>
			, "options" : {
				"evaluateCallback" : function () {
					myEvaluateCallbackFunction ();
				}
			}
			, "format" : "json"
		};

		
			qti_init(qti_initParam);
			matching_init(matching_param);
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
