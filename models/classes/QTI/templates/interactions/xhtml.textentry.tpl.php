<input type="text" id="<?=$identifier?>" name="<?=$identifier?>" class="qti_<?=$_type?>_interaction <?=$class?>"  />
<script type="text/javascript">
	qti_initParam["<?=$serial?>"] = <?=$rowOptions?>;
	qti_initParam["<?=$serial?>"]['id'] = "<?=$identifier?>";
	qti_initParam["<?=$serial?>"]['type'] = "qti_<?=$_type?>_interaction";
	<?if(isset($options['responseBaseType'])):?>
	qti_initParam["<?=$serial?>"]['baseType'] = "<?=$options['responseBaseType']?>";
	<?endif?>
</script>