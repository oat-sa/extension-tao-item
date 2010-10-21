<div>
	<div id="form-title" class="ui-widget-header ui-corner-top ui-state-default">
		<?=get_data('formTitle')?>
	</div>
	<div id="form-container" class="ui-widget-content ui-corner-bottom">
		<?=get_data('myForm')?>
	</div>
</div>

<div>
	<ul id="cssFiles">
	<?foreach(get_data('cssFiles') as $file):?>
		<li rel="<?=$file['href']?>">
			<span><?=$file['name']?></span>
			<a class="cssFile-delete">delete</a>
			/<a class="cssFile-download">download</a>
		</li>
	<?endforeach;?>
	</ul>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		$('#cssFiles > li > a.cssFile-delete').click(function(){
			var $ul = $('#qtiAuthoring_processingEditor_formContainer').parent('ul');
			if($ul.length){
				myItem.deleteStyleSheet($ul.attr('id'));
			}
		});
		
		$('#cssFiles > li > a.cssFile-download').click(function(){
			var $ul = $('#qtiAuthoring_processingEditor_formContainer').parent('ul');
			if($ul.length){
				myItem.getStyleSheet($ul.attr('id'));
			}
		});
	});
</script>