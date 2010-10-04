<div class="qti_widget qti_<?=$_type?>_interaction">
	<?=$data?>
	
	<?if(isset($options['maxStrings']) && ($response->getOption('cardinality') == 'multiple' || $response->getOption('cardinality') == 'ordered')):?>
		
		<div id="<?=$identifier?>">
		<?for($i = 0; $i < $options['maxStrings']; $i++):?>
			<input id="<?=$identifier?>_<?=$i?>" name="<?=$identifier?>_<?=$i?>" /><br />
		<?endfor?>
		</div>
		
	<?else:?>
		<textarea id="<?=$identifier?>" name="<?=$identifier?>" ></textarea>
	<?endif?>
	<script type="text/javascript">
		qti_initParam["<?=$serial?>"] = {
			id : "<?=$identifier?>",
			type : "qti_<?=$_type?>_interaction"
			<?foreach($options as $key => $value):?>
				, "<?=$key?>" : "<?=$value?>"
			<?endforeach?>
		};
	</script>
</div>