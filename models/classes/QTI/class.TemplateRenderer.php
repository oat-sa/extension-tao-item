<?php

error_reporting(E_ALL);

/**
 * This class is a simple "search and replace" PHP-Like template renderer. 
 * It parses a file with php short tags and replace the variables by the
 * in attributes
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
 * This class is a simple "search and replace" PHP-Like template renderer. 
 * It parses a file with php short tags and replace the variables by the
 * in attributes
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

    /**
     * Short description of attribute context
     *
     * @access protected
     * @var array
     */
    protected static $context = array();

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
     * Short description of method setContext
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  array parameters
     * @param  string prefix
     * @return mixed
     */
    public static function setContext($parameters, $prefix = '')
    {
        // section 127-0-1-1-3c043620:12bd493a38b:-8000:000000000000272E begin
        
    	self::$context = array();
    	
    	foreach($parameters as $key => $value){
    		self::$context[$prefix . $key] = $value;
    	}
    	
        // section 127-0-1-1-3c043620:12bd493a38b:-8000:000000000000272E end
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
        extract(self::$context);
        
        ob_start();
        
        include $this->file;
        
        $returnValue = ob_get_contents();
        
        ob_end_clean();
        
        // section 127-0-1-1-649cc98e:12ad7cf4ab2:-8000:00000000000025A5 end

        return (string) $returnValue;
    }

} /* end of class taoItems_models_classes_QTI_TemplateRenderer */

?>