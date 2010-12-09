<?php

error_reporting(E_ALL);

/**
 * This container initialize the qti HotspotInteraction response form:
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package tao
 * @subpackage actions_form
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * This container initialize the HotspotInteraction response form.
 *
 * @access public
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package tao
 * @subpackage actions_form
 */
class taoItems_actions_QTIform_response_HotspotInteraction
    extends taoItems_actions_QTIform_response_Response
{
	
	public function initElements(){
		parent::setCommonElements();
    }
	
}

?>