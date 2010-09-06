<outcomeDeclaration identifier="<?=$identifier?>" 
<?foreach($options as $key => $value):?>
   	<?=$key?>="<?=$value?>" 
<?endforeach?>
<?if(!$defaultValue):?> 
	/>
<?else:?>
	<defaultValue>
		<value><?=$defaultValue?></value>
	</defaultValue>
</outcomeDeclaration>
<?endif?>