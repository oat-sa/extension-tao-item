<<?=$type?> identifier="<?=$identifier?>" <?=$rowOptions?> >
	<?if(isset($object['data'])):?>
     	<?if(trim($object_alt) == ''):?>
     		<object <?=$objectAttributes?> />
     	<?else:?>
    		<object <?=$objectAttributes?> ><?=$object_alt?></object>
    	<?endif?>
    <?endif?>
</<?=$type?>>
