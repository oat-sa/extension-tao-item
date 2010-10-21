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
	qti_initParam["<?=$serial?>"] ['matchMaxes'] =	 {
		<?$i=0;foreach($choices as $choice):?>
			<?=$choice->getIdentifier()?>: { 
				matchMax	: <?=($choice->getOption('matchMax') == '') ? 0 : $choice->getOption('matchMax')?>,
				current		: "0"
			}<?=($i<count($choices)-1)?',':''?>
		<?$i++;endforeach?>
		};

	<?php if (isset($correct)) { ?>
	matching_param.corrects.push(<?=$correct?>);
	<?php } ?>
	
	<?php if (isset($map)) { ?>
	matching_param.maps.push(<?=$map?>);
	<?php } ?>
</script>