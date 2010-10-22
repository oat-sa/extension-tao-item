<div>
<?if(count(get_data('cssFiles'))):?>
	<?=__('Uploaded style files:')?>
	<ul id="cssFiles">
	<?foreach(get_data('cssFiles') as $file):?>
		<li rel="<?=$file['href']?>">
			<span class="cssFile-title"><?=$file['title']?></span>
			<a class="cssFile-delete" href="#">delete</a>
			/<a class="cssFile-download" href="<?=$file['downloadUrl']?>">download</a>
		</li>
	<?endforeach;?>
	</ul>
<?endif;?>
</div>

<div>
	<?=__('Upload new style sheet:')?>
	<?=get_data('myForm')?>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		$('a.cssFile-delete').click(function(){
			var $li = $(this).parent('li');
			if($li.length){
				myItem.deleteStyleSheet($li.attr('rel'));
			}
			return false;
		});
	});
</script>