<<?=$type?>Interaction <?=$rowOptions?>>
    
    <?if(!empty($prompt)):?>
    	<prompt><?=$prompt?></prompt>
    <?endif?>
     <?if(count($object) > 0):?>
    	<object <?=$objectAttributes?> ><?=$object_alt?></object>
    <?endif?>

	<?=$data?>
       
</<?=$type?>Interaction>
