<div id="<?=$identifier?>" class="qti_widget qti_<?=$_type?>_interaction">
	<?=$data?>
</div>	
<script type="text/javascript">
	qti_initParam["<?=$serial?>"] = {
		id 					: "<?=$identifier?>",
		type 				: "qti_<?=$_type?>_interaction",
		matchMaxes			: {
		<?$i=0;foreach($choices as $choice):?>
			<?=$choice->getIdentifier()?>: { 
				matchMax	: <?=($choice->getOption('matchMax') == '') ? 0 : $choice->getOption('matchMax')?>,
				current		: "0"
			}<?=($i<count($choices)-1)?',':''?>
		<?$i++;endforeach?>
		}
		<?foreach($options as $key => $value):?>
		, "<?=$key?>" : "<?=$value?>"
		<?endforeach?>
	};
</script>