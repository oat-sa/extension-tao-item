<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/scripts/class.MigrateLegacyItems.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 17.02.2011, 15:00:00 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoItems
 * @subpackage scripts
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include tao_scripts_Runner
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('tao/scripts/class.Runner.php');

/* user defined includes */
// section 127-0-1-1--39e3a8dd:12e33ba6c22:-8000:0000000000002D6C-includes begin
// section 127-0-1-1--39e3a8dd:12e33ba6c22:-8000:0000000000002D6C-includes end

/* user defined constants */
// section 127-0-1-1--39e3a8dd:12e33ba6c22:-8000:0000000000002D6C-constants begin
// section 127-0-1-1--39e3a8dd:12e33ba6c22:-8000:0000000000002D6C-constants end

/**
 * Short description of class taoItems_scripts_MigrateLegacyItems
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoItems
 * @subpackage scripts
 */
class taoItems_scripts_MigrateLegacyItems
    extends tao_scripts_Runner
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method run
     *
     * @access protected
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return mixed
     */
    protected function run()
    {
        // section 127-0-1-1--39e3a8dd:12e33ba6c22:-8000:0000000000002D6E begin
        
    	try{
	    	$dom = new DOMDocument();
	    	$dom->load($this->parameters['input']);
    	
	    	$inqueries = array();
	    	
	    	$xpath = new DOMXPath($dom);
	    	foreach($xpath->query("//tao:INQUIRY") as $inquery){
	    		$added = false;
	    		if($inquery->getAttribute('order')){
	    			$order = (int)$inquery->getAttribute('order');
	    			if(!array_key_exists($order, $inqueries)){
	    				$inqueries[(int)$inquery->getAttribute('order')] = $inquery;
	    				$added = true;
	    			}
	    		}
	    		if(!$added){
	    			$inqueries[max(array_keys($inqueries)) + 1] = $inquery;
	    		}
	    	}
	    	print_r($inqueries);
    	}
    	catch(DomainException $de){
			self::err($de, true);    		
    	}
    	
        // section 127-0-1-1--39e3a8dd:12e33ba6c22:-8000:0000000000002D6E end
    }

} /* end of class taoItems_scripts_MigrateLegacyItems */

?>