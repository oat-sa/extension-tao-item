<input type="text" id="<?=$identifier?>" name="<?=$identifier?>" class="qti_<?=$_type?>_interaction"  />
<script type="text/javascript">
	qti_initParam["<?=$serial?>"] = <?=$rowOptions?>;
	qti_initParam["<?=$serial?>"]['id'] = "<?=$identifier?>";
	qti_initParam["<?=$serial?>"]['type'] = "qti_<?=$_type?>_interaction";
	
	<?php if (isset($correct)) { ?>
	matching_param.corrects.push(<?=$correct?>);
	<?php } ?>
	
	<?php if (isset($map)) { ?>
	matching_param.maps.push(<?=$map?>);
	<?php } ?>
</script>