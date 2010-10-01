<div id="<?=$identifier?>" class="qti_<?=$_type?>_interaction">
	<div class="qti_<?=$_type?>_container">
	<?=$data?>
	</div>
</div>	
<script type="text/javascript">
	qti_initParam["<?=$serial?>"] = {
		id 					: "<?=$identifier?>",
		type 				: "qti_<?=$_type?>_interaction",
		responseIdentifier 	: "<?=$options['responseIdentifier']?>",
		maxAssociations		: <?=$options['maxAssociations']?>,
		maxMaxes			: {
		<?$i=0;foreach($choices as $choice):?>
			<?=$choice->getIdentifier()?>: { 
				matchMax	: <?=($choice->getOption('matchMax') == '') ? 0 : $choice->getOption('matchMax')?>,
				current		: "0"
			}
			<?=($i<count($choices)-1)?',':''?>
		<?$i++;endforeach?>
		}
	};
</script>