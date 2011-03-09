<manifest 
	xmlns="http://www.imsglobal.org/xsd/imscp_v1p1" 
	xmlns:imsqti="http://www.imsglobal.org/xsd/imsqti_v2p0" 
	xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	xsi:schemaLocation="http://www.imsglobal.org/xsd/imscp_v1p1 imscp_v1p1.xsd http://www.imsglobal.org/xsd/imsqti_v2p0 imsqti_v2p0.xsd"
	identifier="<?=$manifestIdentifier?>"
>
    <organizations/>
    <resources>
        <resource identifier="<?=$qtiItem->getIdentifier()?>" type="imsqti_item_xmlv2p0" href="<?=$qtiFilePath?>">
            <metadata>
                <schema>IMS QTI Item</schema>
                <schemaversion>2.0</schemaversion>      
                <imsqti:qtiMetadata>
                    <imsqti:timeDependent><?=($qtiItem->getOption('timeDependent'))?'true':'false'?></imsqti:timeDependent>
                    <?foreach($qtiItem->getInteractions() as $interaction):?>
                    <imsqti:interactionType><?=$interaction->getType()?></imsqti:interactionType>
                     <?endforeach?>
                    <imsqti:feedbackType><?=($qtiItem->getOption('adaptive'))?'adaptive':'nonadaptive'?></imsqti:feedbackType>
                    <imsqti:solutionAvailable></imsqti:solutionAvailable>
                    <imsqti:toolName><?=$qtiItem->getOption('toolName')?></imsqti:toolName>
                    <imsqti:toolVersion><?=$qtiItem->getOption('toolVersion')?></imsqti:toolVersion>
                    <imsqti:toolVendor>TAO Initiative</imsqti:toolVendor>
                </imsqti:qtiMetadata>
            </metadata>
            <file href="<?=$qtiFilePath?>"/>
            <?foreach($medias as $media):?>
            <file href="<?=$media?>"/>
            <?endforeach?>
        </resource>
    </resources>
</manifest>
