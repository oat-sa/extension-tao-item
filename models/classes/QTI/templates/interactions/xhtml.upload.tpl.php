<div id="<?=$identifier?>" class="qti_widget qti_<?=$_type?>_interaction <?=$class?>">

	<?if(!empty($prompt)):?>
    	<p class="prompt"><?=$prompt?></p>
    <?endif?>

	<form enctype="multipart/form-data">
		<input type='hidden' name='<?=$identifier?>_data' id='<?=$identifier?>_data' />
		<input type='file' name='<?=$identifier?>_uploader' id='<?=$identifier?>_uploader' />
	</form>
</div>
<script type="text/javascript">
	qti_initParam["<?=$serial?>"] = <?=$rowOptions?>;
	qti_initParam["<?=$serial?>"]['id'] = "<?=$identifier?>";
	qti_initParam["<?=$serial?>"]['type'] = "qti_<?=$_type?>_interaction";
	qti_initParam["<?=$serial?>"]['session_id'] = "<?=session_id()?>";
	<?if(isset($options['type'])):?>
	qti_initParam["<?=$serial?>"]['ext'] = "<?=tao_helpers_File::getExtention($options['type'])?>";
	<?endif?>
</script>