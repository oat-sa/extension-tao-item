<responseDeclaration identifier="<?=$identifier?>" <?=$rowOptions?> >
    <?if(isset($correctResponses) && count($correctResponses) > 0):?>
        <correctResponse>
            <?foreach($correctResponses as $value):?>
            	<value><?=$value?></value>
            <?endforeach?>
        </correctResponse>
	<?endif?>
	
	<?if(isset($mapping) && count($mapping) > 0):?>
        <mapping defaultValue="<?=echo isset($mappingDefaultValue)?floatval($mappingDefaultValue):0;?>" <?=$mappingOptions?>>
            <?foreach($mapping as $key => $value):?>
            	<mapEntry mapKey="<?=$key?>" mappedValue="<?=$value?>"/>
            <?endforeach?>
        </mapping>
	<?endif?>
	
	<?if(isset($areaMapping) && count($areaMapping) > 0):?>
        <areaMapping defaultValue="<?echo isset($areaMappingDefaultValue)?floatval($areaMappingDefaultValue):0;?>" <?=$areaMappingOptions?>>
            <?foreach($areaMapping as $areaMapEntry):?>
            	<areaMapEntry <?foreach($areaMapEntry as $key => $value):?><?=$key?>="<?=$value?>" <?endforeach?> />
            <?endforeach?>
        </areaMapping>
	<?endif?>
</responseDeclaration>
