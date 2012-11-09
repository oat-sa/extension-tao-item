<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/models/classes/Paperbased/class.Service.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 08.11.2012, 17:06:11 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_Paperbased
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * Service is the base class of all services, and implements the singleton
 * for derived services
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('tao/models/classes/class.Service.php');

/**
 * include taoItems_models_classes_itemModelService
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('taoItems/models/classes/interface.itemModelService.php');

/* user defined includes */
// section 10-30-1--78-7c71ec09:13ae0b6dbb2:-8000:0000000000003C22-includes begin
// section 10-30-1--78-7c71ec09:13ae0b6dbb2:-8000:0000000000003C22-includes end

/* user defined constants */
// section 10-30-1--78-7c71ec09:13ae0b6dbb2:-8000:0000000000003C22-constants begin
// section 10-30-1--78-7c71ec09:13ae0b6dbb2:-8000:0000000000003C22-constants end

/**
 * Short description of class taoItems_models_classes_Paperbased_Service
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_Paperbased
 */
class taoItems_models_classes_Paperbased_Service
    extends tao_models_classes_Service
        implements taoItems_models_classes_itemModelService
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method render
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource item
     * @return string
     */
    public function render( core_kernel_classes_Resource $item)
    {
        $returnValue = (string) '';

        // section 10-30-1--78-7c71ec09:13ae0b6dbb2:-8000:0000000000003C26 begin
		$downloadUrl = _url('downloadItemContent', 'items', null, array(
			'uri' 		=> tao_helpers_Uri::encode($item->getUri())
		));

		$templateRenderer = new taoItems_models_classes_TemplateRenderer(
			ROOT_PATH.'taoItems/views/templates/paperbased_download.tpl.php',
			array(
				'label'		=> $item->getLabel(),
				'downloadurl'	=> $downloadUrl
		));
		$returnValue	= $templateRenderer->render();
        // section 10-30-1--78-7c71ec09:13ae0b6dbb2:-8000:0000000000003C26 end

        return (string) $returnValue;
    }

} /* end of class taoItems_models_classes_Paperbased_Service */

?>