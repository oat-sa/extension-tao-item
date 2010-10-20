<responseDeclaration identifier="<?=$identifier?>" <?=$rowOptions?> >
    
    <?if(count($correctResponses) > 0):?>
        <correctResponse>
            <?foreach($correctResponses as $value):?>
            	<value><?=$value?></value>
            <?endforeach?>
        </correctResponse>
	<?endif?>
	
	<?if(count($mapping) > 0):?>
        <mapping defaultValue="<?=$mappingDefaultValue?>" <?=$mappingOptions?>>
            <?foreach($mapping as $key => $value):?>
            	<mapEntry mapKey="<?=$key?>" mappedValue="<?=$value?>"/>
            <?endforeach?>
        </mapping>
	<?endif?>
</responseDeclaration>