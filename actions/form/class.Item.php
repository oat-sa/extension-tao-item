<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/actions/form/class.Item.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 08.09.2010, 16:04:58 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoItems
 * @subpackage actions_form
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * Create a form from a  resource of your ontology. 
 * Each property will be a field, regarding it's widget.
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('tao/actions/form/class.Instance.php');

/* user defined includes */
// section 127-0-1-1-7c161ae7:12af1a41c59:-8000:0000000000002591-includes begin
// section 127-0-1-1-7c161ae7:12af1a41c59:-8000:0000000000002591-includes end

/* user defined constants */
// section 127-0-1-1-7c161ae7:12af1a41c59:-8000:0000000000002591-constants begin
// section 127-0-1-1-7c161ae7:12af1a41c59:-8000:0000000000002591-constants end

/**
 * Short description of class taoItems_actions_form_Item
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoItems
 * @subpackage actions_form
 */
class taoItems_actions_form_Item
    extends tao_actions_form_Instance
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method initForm
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return mixed
     */
    public function initForm()
    {
        // section 127-0-1-1-7c161ae7:12af1a41c59:-8000:0000000000002593 begin
        
    	parent::initForm();
    	
    	$actions = $this->form->getActions();
    	
    	if(!tao_helpers_Context::check('STANDALONE_MODE')){
			
    		//According to the status of the versioning
    		if(!GENERIS_VERSIONING_ENABLED){
	    		// Add content action
	    		$url = _url('itemContentIO', 'Items', 'taoItems', array(
	    			'uri'		=> tao_helpers_Uri::encode($this->instance->uriResource),
	    			'classUri'	=> tao_helpers_Uri::encode($this->clazz->uriResource)
	    		));
	    		
	    		$itemContentIOElt = tao_helpers_form_FormFactory::getElement('itemContentIO', 'Free');
				$itemContentIOElt->setValue("<a href='{$url}' class='nav' ><img src='".BASE_WWW."/img/text-xml.png' alt='xml' class='icon' /> ".__('Content')."</a>");
				$actions[] = $itemContentIOElt;
    		}else{
				// Add versioned content action
	    		$url = _url('itemVersionedContentIO', 'Items', 'taoItems', array(
	    			'uri'			=> tao_helpers_Uri::encode($this->instance->uriResource),
	    			'propertyUri'	=> tao_helpers_Uri::encode('http://www.tao.lu/Ontologies/TAOItem.rdf#ItemVersionedContent')
	    		));
			
	    		$itemVersionedContentIOElt = tao_helpers_form_FormFactory::getElement('itemVersionedContentIO', 'Free');
				$itemVersionedContentIOElt->setValue("<a href='{$url}' class='nav' ><img src='".BASE_WWW."/img/text-xml.png' alt='xml' class='icon' /> ".__('VersionedContent')."</a>");
				$actions[] = $itemVersionedContentIOElt;
			}
		
		}
		
		$this->form->setActions($actions, 'top');
		$this->form->setActions($actions, 'bottom');
    	
        // section 127-0-1-1-7c161ae7:12af1a41c59:-8000:0000000000002593 end
    }

} /* end of class taoItems_actions_form_Item */

?>