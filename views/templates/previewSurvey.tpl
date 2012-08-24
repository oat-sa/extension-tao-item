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
<!--		<script type="text/javascript" src="--><?php //echo get_data('taoView') ?><!--/js/jquery.jqGrid-4.4.0/js/jquery.jqGrid.min.js"></script>-->
<!--		<link src="--><?php //echo get_data('taoView') ?><!--/js/jquery.jqGrid-4.4.0/css/ui.jqgrid.css" />-->

		<!--[if IE]>
			<link rel="stylesheet" type="text/css" href="<?php echo get_data('basePreview') ?>/css/survey/ie.css" media="screen"/>
		<![endif]-->
		<script type="text/javascript">

			/**
			 * Author: Rob Audenaerde
			 */
			function resetTableSizes (table, change, columnIndex)
			{
				//calculate new width;
				var tableId = table.attr('id');
				var myWidth = $('#'+tableId+' TR TH').get(columnIndex).offsetWidth;
				var newWidth = (myWidth+change)+'px';

				$('#'+tableId+' TR').each(function()
				{
					$(this).find('TD').eq(columnIndex).css('width',newWidth);
					$(this).find('TH').eq(columnIndex).css('width',newWidth);
				});
				resetSliderPositions(table);
			};

			function resetSliderPositions(table)
			{
				var tableId = table.attr('id');
				//put all sliders on the correct position
				table.find(' TR:first TH').each(function(index)
				{
					var td = $(this);
					var newSliderPosition = td.offset().left+td.outerWidth() ;
					$("#"+tableId+"_id"+(index+1)).css({  left:   newSliderPosition , height: table.height() + 'px'}  );
				});
			}

			function makeResizable(table)
			{
				//get number of columns
				var numberOfColumns = table.find('TR:first TH').size();

				//id is needed to create id's for the draghandles
				var tableId = table.attr('id');

				for (var i=0; i<=numberOfColumns; i++)
				{
					//enjoy this nice chain :)
					$('<div class="draghandle" id="'+tableId+'_id'+i+'" />').insertBefore(table).data('tableid', tableId).data('myindex',i).draggable(
						{ axis: "x",
							start: function ()
							{
								var tableId = ($(this).data('tableid'));
								$(this).toggleClass( "dragged" );
								//set the height of the draghandle to the current height of the table, to get the vertical ruler
								$(this).css({ height: $('#'+tableId).height() + 'px'} );
							},
							stop: function (event, ui)
							{
								var tableId = ($(this).data('tableid'));
								$( this ).toggleClass( "dragged" );
								var oldPos  = ($( this ).data("draggable").originalPosition.left);
								var newPos = ui.position.left;
								var index =  $(this).data("myindex");
								resetTableSizes($('#'+tableId), newPos-oldPos, index-1);
							}
						}
					);;
				};
				resetSliderPositions(table);

			}
			$(document).ready(function()
			{
//				$("table").each(function(index)
//				{
//					makeResizable($(this));
//				});

			});

		</script>
		<style>
			.draghandle.dragged
			{
				border-left: 1px solid #333;
			}

			.draghandle
			{
				position: absolute;
				z-index:5;
				width:5px;
				cursor:e-resize;
			}
		/*	TH
			{
				border-left: 1px solid #333;
				border-bottom: 1px solid #333;
			}*/
		</style>
	</head>
	<body>
		<?php echo get_data('content') ?>
	</body>
</html>