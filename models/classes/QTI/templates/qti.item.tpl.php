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
	
	<?foreach($stylesheets as $stylesheet):?>
		<stylesheet href="<?=$stylesheet['href']?>" title="<?=$stylesheet['title']?>" media="<?=$stylesheet['media']?>" type="<?=$stylesheet['type']?>" />
	<?endforeach?>

	<itemBody>
		<div><?=$data?></div>
	</itemBody>
	
	<?=$renderedResponseProcessing?>

</assessmentItem>
