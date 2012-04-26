<?php

error_reporting(E_ALL);

/**
 * Helper to build the Interaction Response Processing Forms
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @subpackage helpers_qti
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 127-0-1-1-b1084d2:136c9f75e99:-8000:0000000000003926-includes begin
// section 127-0-1-1-b1084d2:136c9f75e99:-8000:0000000000003926-includes end

/* user defined constants */
// section 127-0-1-1-b1084d2:136c9f75e99:-8000:0000000000003926-constants begin
// section 127-0-1-1-b1084d2:136c9f75e99:-8000:0000000000003926-constants end

/**
 * Helper to build the Interaction Response Processing Forms
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @subpackage helpers_qti
 */
class taoItems_helpers_qti_InteractionAuthoring
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method getIRPData
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Item item
     * @param  Interaction interaction
     * @return array
     */
    public static function getIRPData( taoItems_models_classes_QTI_Item $item,  taoItems_models_classes_QTI_Interaction $interaction)
    {
        $returnValue = array();

        // section 127-0-1-1-b1084d2:136c9f75e99:-8000:0000000000003928 begin
		$responseProcessing = $item->getResponseProcessing();
		$response = $interaction->getResponse();
		
		if ($responseProcessing instanceof taoItems_models_classes_QTI_response_TemplatesDriven) {
			// templates driven:
			common_Logger::d('template: '.$responseProcessing->getTemplate($response));
			$isMappingMode = in_array($responseProcessing->getTemplate($response), array(
				QTI_RESPONSE_TEMPLATE_MAP_RESPONSE, QTI_RESPONSE_TEMPLATE_MAP_RESPONSE_POINT
			));
			if ($isMappingMode) {
				$returnValue = self::getMapingRPData($item, $interaction);
			} else {
				$returnValue = self::getCorrectRPData($item, $interaction);
			}
			
		} elseif ($responseProcessing instanceof taoItems_models_classes_QTI_response_Composite){
			
			// composite processing
			$irp = $responseProcessing->getInteractionResponseProcessing($interaction->getResponse());
			switch (get_class($irp)) {
				case 'taoItems_models_classes_QTI_response_interactionResponseProcessing_None' :
					$returnValue = self::getManualRPData($item, $interaction);
					break;
				case 'taoItems_models_classes_QTI_response_interactionResponseProcessing_MatchCorrectTemplate' :
					$returnValue = self::getCorrectRPData($item, $interaction);
					break;
				case 'taoItems_models_classes_QTI_response_interactionResponseProcessing_MapResponseTemplate' :
				case 'taoItems_models_classes_QTI_response_interactionResponseProcessing_MapResponsePointTemplate' :
					$returnValue = self::getMapingRPData($item, $interaction);
					break;
			}
			
		} else {
			$xhtmlForms[] = '<b>'
				.__('The response form is not available for the selected response processing.<br/>')
				.'</b>';
		}
        // section 127-0-1-1-b1084d2:136c9f75e99:-8000:0000000000003928 end

        return (array) $returnValue;
    }

    /**
     * Short description of method getManualRPData
     *
     * @access private
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Item item
     * @param  Interaction interaction
     * @return core_kernel_classes_Array
     */
    private static function getManualRPData( taoItems_models_classes_QTI_Item $item,  taoItems_models_classes_QTI_Interaction $interaction)
    {
        $returnValue = null;

        // section 127-0-1-1-b1084d2:136c9f75e99:-8000:000000000000392C begin
        $irp = $item->getResponseProcessing()->getInteractionResponseProcessing($interaction->getResponse());
        $outcome = null;
		foreach ($item->getOutcomes() as $outcomeCandidate) {
			if ($outcomeCandidate == $irp->getOutcome()) {
				$outcome = $outcomeCandidate;
				break; 
			}
		}
		if (is_null($outcome)) {
			throw new common_exception_Error('No outcome definied for interaction '.$interaction->getIdentifier());
		}
		$manualForm = new taoItems_actions_QTIform_ManualProcessing($interaction, $item->getResponseProcessing(), $outcome);
		if (!is_null($manualForm)) {
			$xhtmlForms[] = $manualForm->getForm()->render();
		}
		$returnValue = array(
			'displayGrid'	=> false,
			'forms'			=> $xhtmlForms
		);
        // section 127-0-1-1-b1084d2:136c9f75e99:-8000:000000000000392C end

        return $returnValue;
    }

    /**
     * Short description of method getMapingRPData
     *
     * @access private
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Item item
     * @param  Interaction interaction
     * @return array
     */
    private static function getMapingRPData( taoItems_models_classes_QTI_Item $item,  taoItems_models_classes_QTI_Interaction $interaction)
    {
        $returnValue = array();

        // section 127-0-1-1-b1084d2:136c9f75e99:-8000:000000000000392E begin
        $responseProcessing = $item->getResponseProcessing();
        $service = taoItems_models_classes_QtiAuthoringService::singleton();
		$columnModel = $service->getInteractionResponseColumnModel($interaction, $item->getResponseProcessing(), true);
		$responseData = $service->getInteractionResponseData($interaction);
		
		$mappingForm = new taoItems_actions_QTIform_Mapping($interaction, $item->getResponseProcessing());
		if (!is_null($mappingForm)) {
			$forms = array($mappingForm->getForm()->render());
		} else {
			common_Logger::w('Could not load qti mapping form', array('QTI', 'TAOITEMS'));
			$forms = array();
		}
		$returnValue = array(
			'displayGrid'	=> true,
			'data'			=> $responseData,
			'colModel'		=> $columnModel,
			'setResponseMappingMode' => true,
			'forms'			=> $forms
		);
        // section 127-0-1-1-b1084d2:136c9f75e99:-8000:000000000000392E end

        return (array) $returnValue;
    }

    /**
     * Short description of method getCorrectRPData
     *
     * @access private
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Item item
     * @param  Interaction interaction
     * @return array
     */
    private static function getCorrectRPData( taoItems_models_classes_QTI_Item $item,  taoItems_models_classes_QTI_Interaction $interaction)
    {
        $returnValue = array();

        // section 127-0-1-1-b1084d2:136c9f75e99:-8000:0000000000003930 begin
        $service = taoItems_models_classes_QtiAuthoringService::singleton();
		$columnModel = $service->getInteractionResponseColumnModel($interaction, $item->getResponseProcessing(), false);
		$responseData = $service->getInteractionResponseData($interaction);
		$returnValue = array(
			'displayGrid'	=> true,
			'data'			=> $responseData,
			'colModel'		=> $columnModel,
			'setResponseMappingMode' => false,
			'forms'			=> array()
		);
        // section 127-0-1-1-b1084d2:136c9f75e99:-8000:0000000000003930 end

        return (array) $returnValue;
    }

} /* end of class taoItems_helpers_qti_InteractionAuthoring */

?>