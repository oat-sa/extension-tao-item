<?php
/*  
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
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
 * Short description of class taoItems_models_classes_Paperbased_Service
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_Paperbased
 */
class taoItems_models_classes_Paperbased_Service
	implements taoItems_models_classes_itemModel
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---
    /**
     * default constructor to ensure the implementation
     * can be instanciated
     */
    public function __construct() {
    }
    
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