<div id="<?=$identifier?>" class="qti_widget qti_<?=$_type?>_interaction <?=$class?>">

	<?if(!empty($prompt)):?>
    	<p class="prompt"><?=$prompt?></p>
    <?endif?>

	<?=$data?>
	
	 <div class="link_counter">0</div>
     <div class="sub_counter"></div>
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
	qti_initParam["<?=$serial?>"]['type'] = "qti_<?=$_type?>_interaction";
	qti_initParam["<?=$serial?>"]['graphicAssociateChoices'] = {
	<?$i=0; foreach($choices as $choice):?>
		<?=$choice->getIdentifier()?>: { 
			shape 		: "<?=$choice->getOption('shape')?>",
			coords		: "<?=$choice->getOption('coords')?>",
			matchMax	: "<?=$choice->getOption('matchMax')?>"
		}<?=($i<count($choices)-1)?',':''?>
	<?$i++;endforeach?>
	}
</script>
