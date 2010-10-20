<div id="<?=$identifier?>" class="qti_widget qti_<?=$_type?>_interaction">

	<?if(!empty($prompt)):?>
    	<p class="prompt"><?=$prompt?></p>
    <?endif?>

	<?=$data?>
</div>
<script type="text/javascript">
	qti_initParam["<?=$serial?>"] = <?=$rowOptions?>;
	qti_initParam["<?=$serial?>"]['id'] = "<?=$identifier?>";
	qti_initParam["<?=$serial?>"]['type'] = "qti_<?=$_type?>_interaction";
</script>