<responseDeclaration identifier="<?=$identifier?>" 
<?foreach($options as $key => $value):?>
   		<?=$key?>="<?=$value?>" 
    <?endforeach?> >
    
    <?if(count($correctResponses) > 0):?>
        <correctResponse>
            <?foreach($correctResponses as $value):?>
            	<value><?=$value?></value>
            <?endforeach?>
        </correctResponse>
	<?endif?>
	
	<?if(count($mapping) > 0):?>
        <mapping <?if($defaultMappingValue):?>defaultValue="<?=$defaultMappingValue?>"<?endif?>>
            <?foreach($mapping as $key => $value):?>
            	<mapEntry mapKey="<?=$key?>" mappedValue="<?=$value?>"/>
            <?endforeach?>
        </mapping>
	<?endif?>
</responseDeclaration>