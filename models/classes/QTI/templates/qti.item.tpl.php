<assessmentItem 
	xmlns="http://www.imsglobal.org/xsd/imsqti_v2p0"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://www.imsglobal.org/xsd/imsqti_v2p0 imsqti_v2p0.xsd"
    identifier="<?=$identifier?>"
    <?foreach($options as $key => $value):?>
   		<?=$key?>="<?=$value?>" 
    <?endforeach?> 
    >
    
    <?=$data?>
    
</assessmentItem>
