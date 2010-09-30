<div id="<?=$identifier?>" class="qti_<?=$_type?>_interaction">
	<?=$data?>
</div>	
<script type="text/javascript">
	qti_initParam["<?=$serial?>"] = {
		id : "<?=$identifier?>",
		type : "qti_<?=$_type?>_interaction"
		
	};
</script>