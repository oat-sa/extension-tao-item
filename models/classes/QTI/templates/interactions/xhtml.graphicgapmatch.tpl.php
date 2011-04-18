<div id="<?=$identifier?>" class="qti_widget qti_<?=$_type?>_interaction <?=$class?>">

	<?if(!empty($prompt)):?>
    	<p class="prompt"><?=$prompt?></p>
    <?endif?>

	<?=$data?>
</div>
<script type="text/javascript">
	qti_initParam["<?=$serial?>"] = <?=$rowOptions?>;
	qti_initParam["<?=$serial?>"]['id'] = "<?=$identifier?>";
	qti_initParam["<?=$serial?>"]['type'] = "qti_<?=$_type?>_interaction";
	<?if(isset($object['data'])):?>
	qti_initParam["<?=$serial?>"]['imagePath'] = "<?=$object['data']?>";
	<?endif?>
	<?if(isset($object['width'])):?>
	qti_initParam["<?=$serial?>"]['imageWidth'] = "<?=$object['width']?>";
	<?endif?>
	<?if(isset($object['height'])):?>
	qti_initParam["<?=$serial?>"]['imageHeight'] = "<?=$object['height']?>";
	<?endif?>
	qti_initParam["<?=$serial?>"]['matchMaxes'] = {
	<?$i=0;foreach($choices as $choice):?>
	<?=$choice->getIdentifier()?>: { 
		matchMax	: <?=($choice->getOption('matchMax') == '') ? 0 : $choice->getOption('matchMax')?>,
		matchGroup	: <?=($choice->getOption('matchGroup')) ? (is_array($choice->getOption('matchGroup'))) ? json_encode($choice->getOption('matchGroup')) : '["'.$choice->getOption('matchGroup').'"]' : "[]"?>,
		current		: "0"
	}<?=($i<count($choices)-1)?',':''?>
<?$i++;endforeach?>
<?foreach($groups as $group):?>
	,<?=$group->getIdentifier()?>: { 
		shape 		: "<?=$group->getOption('shape')?>",
		coords		: "<?=$group->getOption('coords')?>",
		matchMax	: <?=($group->getOption('matchMax') == '') ? 0 : $group->getOption('matchMax')?>,
		matchGroup	: <?=($group->getOption('matchGroup')) ? (is_array($group->getOption('matchGroup'))) ? json_encode($group->getOption('matchGroup')) :'["'.$group->getOption('matchGroup').'"]' : "[]"?>
	}
<?endforeach?>
	}
</script>