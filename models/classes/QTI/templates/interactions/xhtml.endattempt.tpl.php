<input type="button" id="<?=$identifier?>" name="<?=$identifier?>" value="<?=__('End Attempt')?>" class="qti_<?=$_type?>_interaction <?=$class?>"  />
<input type="hidden" id="<?=$identifier?>_data" name="<?=$identifier?>_data" value="0"/>
<script type="text/javascript">
	qti_initParam["<?=$serial?>"] = <?=$rowOptions?>;
	qti_initParam["<?=$serial?>"]['id'] = "<?=$identifier?>";
	qti_initParam["<?=$serial?>"]['type'] = "qti_<?=$_type?>_interaction";
</script>
