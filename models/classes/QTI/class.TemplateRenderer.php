<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/models/classes/QTI/class.TemplateRenderer.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 03.09.2010, 15:59:05 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 127-0-1-1-649cc98e:12ad7cf4ab2:-8000:0000000000002592-includes begin
// section 127-0-1-1-649cc98e:12ad7cf4ab2:-8000:0000000000002592-includes end

/* user defined constants */
// section 127-0-1-1-649cc98e:12ad7cf4ab2:-8000:0000000000002592-constants begin
// section 127-0-1-1-649cc98e:12ad7cf4ab2:-8000:0000000000002592-constants end

/**
 * Short description of class taoItems_models_classes_QTI_TemplateRenderer
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI
 */
class taoItems_models_classes_QTI_TemplateRenderer
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute file
     *
     * @access protected
     * @var string
     */
    protected $file = '';

    /**
     * Short description of attribute variables
     *
     * @access protected
     * @var array
     */
    protected $variables = array();

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string templatePath
     * @param  array variables
     * @return mixed
     */
    public function __construct($templatePath, $variables = array())
    {
        // section 127-0-1-1-649cc98e:12ad7cf4ab2:-8000:00000000000025A1 begin
        
    	if(file_exists($templatePath)){
    		if(is_readable($templatePath) && preg_match("/\.tpl\.php$/", basename($templatePath))){
    			$this->file = $templatePath;
    		}
    	}
    	if(empty($this->file)){
    		throw new InvalidArgumentException("Unable to load the template file from $templatePath");
    	}
		if(!tao_helpers_File::securityCheck($this->file)){
			throw new Exception("Security warning: $templatePath is not safe.");
		}
    	
    	
    	$this->variables = $variables;
    	
    	
        // section 127-0-1-1-649cc98e:12ad7cf4ab2:-8000:00000000000025A1 end
    }

    /**
     * Short description of method render
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return string
     */
    public function render()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-649cc98e:12ad7cf4ab2:-8000:00000000000025A5 begin
        
        //extract in the current context the array: 'key' => 'value'  to $key = 'value';
        extract($this->variables);
        
        ob_start();
        
        include $this->file;
        
        $returnValue = ob_get_contents();
        
        ob_end_clean();
        
        // section 127-0-1-1-649cc98e:12ad7cf4ab2:-8000:00000000000025A5 end

        return (string) $returnValue;
    }

} /* end of class taoItems_models_classes_QTI_TemplateRenderer */

?>