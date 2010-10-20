<assessmentItem 
	xmlns="http://www.imsglobal.org/xsd/imsqti_v2p0"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://www.imsglobal.org/xsd/imsqti_v2p0 imsqti_v2p0.xsd"
    identifier="<?=$identifier?>"
    <?=$rowOptions?> >
    
	<?=$response?>
	
<?foreach($outcomes as $outcome):?>
	<?=$outcome->toQTI()?>
<?endforeach?>
	
	<itemBody>
		<div><?=$data?></div>
	</itemBody>
	
<?if($responseProcessing):?>
	<?=$responseProcessing->toQTI()?>
<?endif?>

</assessmentItem>
