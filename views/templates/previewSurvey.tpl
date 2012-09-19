<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=8" />
		<title>Survey Item Preview</title>
		<link rel="stylesheet" type="text/css" href="<?php echo get_data('basePreview') ?>/css/survey/locale.css" media="screen"/>
		<link rel="stylesheet" type="text/css" href="<?php echo get_data('basePreview') ?>/css/survey/reset.css" media="screen"/>
		<link rel="stylesheet" type="text/css" href="<?php echo get_data('basePreview') ?>/css/survey/item.css" media="screen"/>
		<link rel="stylesheet" type="text/css" href="<?php echo get_data('taoView') ?>/css/custom-theme/jquery-ui-1.8.22.custom.css" media="screen"/>
		<script type="text/javascript" src="<?php echo get_data('taoView') ?>/js/jquery-1.8.0.min.js"></script>
		<script type="text/javascript" src="<?php echo get_data('taoView') ?>/js/jquery-ui-1.8.23.custom.min.js"></script>
		<script type="text/javascript" src="<?php echo get_data('basePreview') ?>/js/sliderSurvey.js"></script>

		<!--		DATATABLE-->
		<script type="text/javascript" src="<?php echo get_data('basePreview') ?>/js/Datatable/jquery.dataTables.min.js"></script>
		<script type="text/javascript" src="<?php echo get_data('basePreview') ?>/js/Datatable/resizeColumn.js"></script>

		<!--		<script type="text/javascript" src="--><?php //echo get_data('taoView') ?><!--/js/jquery.jqGrid-4.4.0/js/jquery.jqGrid.min.js"></script>-->
<!--		<link src="--><?php //echo get_data('taoView') ?><!--/js/jquery.jqGrid-4.4.0/css/ui.jqgrid.css" />-->

		<!--[if IE]>
			<link rel="stylesheet" type="text/css" href="<?php echo get_data('basePreview') ?>/css/survey/ie.css" media="screen"/>
		<![endif]-->
		<script type="text/javascript">
			$(function () {
				var displayMode = "<?php echo get_data('ITEM_DISPLAY_MODE'); ?>";
				if(displayMode == 'preview') {
					// columnresizing
					$('.list_table, .horizontal_table, .matrix_table').dataTable({
						"bPaginate": false,
						"bLengthChange": false,
						"bFilter": false,
						"bSort": false,
						"bInfo": false,
						"bAutoWidth": false,
						"sDom": "Rlfrtip"
					});
					//css injection
					$('<style />')
						.html('.list_table th:hover,' +
							'.horizontal_table th:hover,' +
							'.matrix_table th:hover {' +
							'border-left: solid 1px black;' +
							'border-right : solid 1px black;' +
						'}').appendTo($('body'));
				}
				// for sliders
				$('.ui-slider-handle').append('<div class="cursorSlider"></div>');
			});
		</script>
	</head>
	<body>
		<?php echo get_data('content') ?>
	</body>
</html>