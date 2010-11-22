<div id="<?=$identifier?>" class="qti_widget qti_<?=$_type?>_interaction">
	<div class="qti_<?=$_type?>_container">
		<?if(!empty($prompt)):?>
	    	<p class="prompt"><?=$prompt?></p>
	    <?endif?>
	<?=$data?>
	</div>
</div>	
<script type="text/javascript">
	qti_initParam["<?=$serial?>"] = {
		id 					: "<?=$identifier?>",
		type 				: "qti_<?=$_type?>_interaction",
		responseIdentifier 	: "<?=$options['responseIdentifier']?>",
		maxAssociations		: <?=$options['maxAssociations']?>,
		responseBaseType	: "<?=$options['responseBaseType']?>",
		matchMaxes			: {
		<?$i=0;foreach($choices as $choice):?>
			<?=$choice->getIdentifier()?>: { 
				matchMax	: <?=($choice->getOption('matchMax') == '') ? 0 : $choice->getOption('matchMax')?>,
				matchGroup	: <?=($choice->getOption('matchGroup')) ? (is_array($choice->getOption('matchGroup'))) ? json_encode($choice->getOption('matchGroup')) : '["'.$choice->getOption('matchGroup').'"]' : "[]"?>,
				current		: "0"
			}<?=($i<count($choices)-1)?',':''?>
		<?$i++;endforeach?>
		}
	};
</script>