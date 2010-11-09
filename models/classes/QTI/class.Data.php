<?php

error_reporting(E_ALL);

/**
 * The QTI_Data class represent the abstract model for all the QTI objects.
 * It contains all the attributes of the different kind of QTI objects.
 * It manages the identifiers and serial creation.
 * It provides the serialisation and persistance methods.
 * And give the interface for the rendering.
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include taoItems_models_classes_QTI_Exportable
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('taoItems/models/classes/QTI/interface.Exportable.php');

/* user defined includes */
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:00000000000022FE-includes begin
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:00000000000022FE-includes end

/* user defined constants */
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:00000000000022FE-constants begin
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:00000000000022FE-constants end

/**
 * The QTI_Data class represent the abstract model for all the QTI objects.
 * It contains all the attributes of the different kind of QTI objects.
 * It manages the identifiers and serial creation.
 * It provides the serialisation and persistance methods.
 * And give the interface for the rendering.
 *
 * @abstract
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI
 */
abstract class taoItems_models_classes_QTI_Data
        implements taoItems_models_classes_QTI_Exportable
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * It repesents the  QTI  identifier. 
     * It's a unique string. 
     * It can be generated if it hasn't been set.
     *
     * @access protected
     * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10541
     * @var string
     */
    protected $identifier = '';

    /**
     * The serial number is a INTERNAL auto-generated unique key to identify
     * the instance.
     * It has no consequence on the in/output format and is generated again at
     * new instanciation and is kept during the persisting session.
     *
     * @access protected
     * @var string
     */
    protected $serial = '';

    /**
     * Short description of attribute type
     *
     * @access protected
     * @var string
     */
    protected $type = '';

    /**
     * represents the element data as a document with {tag} to place the
     * elements.
     *
     * @access protected
     * @var string
     */
    protected $data = '';

    /**
     * the options of the element
     *
     * @access protected
     * @var array
     */
    protected $options = array();

    /**
     * It defines if the instance should be kept after destruction
     *
     * @access public
     * @var boolean
     */
    public static $persist = true;

    /**
     * String prefix used in session, keys and ids management
     *
     * @access public
     * @var string
     */
    const PREFIX = 'qti_';

    /**
     * Short description of attribute templatesPath
     *
     * @access protected
     * @var string
     */
    protected static $templatesPath = '';

    /**
     * Short description of attribute _instances
     *
     * @access public
     * @var array
     */
    public static $_instances = array();

    // --- OPERATIONS ---

    /**
     * Short description of method toXHTML
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return string
     */
    public function toXHTML()
    {
        $returnValue = (string) '';

        // section 127-0-1-1--3f707dcb:12af06fca53:-8000:0000000000004159 begin
        
        $clazz 	= strtolower(get_class($this));
    	$type 	= substr($clazz, strpos($clazz, 'qti_') + 4);
    	
        $template  	= self::getTemplatePath() . '/xhtml.'.$type.'.tpl.php';
    	$variables 	= $this->extractVariables(); 
    	
    	$variables['rowOptions'] = $this->xmlizeOptions();
		
        $tplRenderer = new taoItems_models_classes_QTI_TemplateRenderer($template, $variables);
        $returnValue = $tplRenderer->render();
        
        // section 127-0-1-1--3f707dcb:12af06fca53:-8000:0000000000004159 end

        return (string) $returnValue;
    }

    /**
     * Short description of method toQTI
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return string
     */
    public function toQTI()
    {
        $returnValue = (string) '';

        // section 127-0-1-1--3f707dcb:12af06fca53:-8000:000000000000415B begin
        
        $clazz 	= strtolower(get_class($this));
    	$type 	= substr($clazz, strpos($clazz, 'qti_') + 4);
    	
        $template  	= self::getTemplatePath() . '/qti.'.$type.'.tpl.php';
    	$variables 	= $this->extractVariables(); 
		
        $tplRenderer = new taoItems_models_classes_QTI_TemplateRenderer($template, $variables);
        $returnValue = $tplRenderer->render();
        
        // section 127-0-1-1--3f707dcb:12af06fca53:-8000:000000000000415B end

        return (string) $returnValue;
    }

    /**
     * Short description of method toForm
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return tao_helpers_form_Form
     */
    public function toForm()
    {
        $returnValue = null;

        // section 127-0-1-1--3f707dcb:12af06fca53:-8000:000000000000415D begin
        // section 127-0-1-1--3f707dcb:12af06fca53:-8000:000000000000415D end

        return $returnValue;
    }

    /**
     * The constructor initialize the instance with the given identifier (if
     * a human readable identifier will be created)
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string identifier
     * @param  array options
     * @return mixed
     */
    public function __construct($identifier = null, $options = array())
    {
        // section 127-0-1-1--56c234f4:12a31c89cc3:-8000:0000000000002318 begin
        
    	$this->createSerial();
    	
    	self::$_instances[] = $this->serial;
    	
    	try{
    		$this->setIdentifier($identifier);
    	}
    	catch(InvalidArgumentException $iae){
    		$this->createIdentifier();
    	}
    	
    	$this->options = $options;
		
		//$debug = debug_backtrace();
    	//error_log('creating '.get_class($this).':'.$this->identifier.'-'.$this->serial.' '.json_encode($debug[0]).' '.serialize($this));
        // section 127-0-1-1--56c234f4:12a31c89cc3:-8000:0000000000002318 end
    }

    /**
     * if the persistance is set to on, the instance is saved, just before the
     * Be carefull to the assignment in the loops!!!
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return mixed
     */
    public function __destruct()
    {
        // section 127-0-1-1--272f4da0:12a899718bf:-8000:00000000000024CF begin
       
	    	
    		if(self::$persist){
	    		//The instance is serialized and saved in the session me before the destruction 
	    		Session::setAttribute(self::PREFIX . $this->serial, serialize($this));
	        }
	        else{
	        	//clean session
	        	if(!empty($this->serial)){
	        		Session::removeAttribute(self::PREFIX . $this->serial);
	        	}
	        	if(!empty($this->id) && !is_null($this->id)){
	        		$ids = Session::getAttribute(self::PREFIX . 'identifiers');
	        		if(is_array($ids)){
		    			if(isset($ids[$this->identifier])){
	        				unset($ids[$this->identifier]);
		    				Session::setAttribute(self::PREFIX . 'identifiers', $ids);
		    			}
	        		}
	        	}
	        	foreach(self::$_instances as $key => $serial){
	        		if($serial == $this->serial){
	        			unset(self::$_instances[$key]);
	        			break;
	        		}
	        	}
	        }
	        
        
        // section 127-0-1-1--272f4da0:12a899718bf:-8000:00000000000024CF end
    }

    /**
     * Gives the list of attributes to serialize by reflection.
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return array
     */
    public function __sleep()
    {
        $returnValue = array();

        // section 127-0-1-1--272f4da0:12a899718bf:-8000:00000000000024D4 begin

        $reflection = new ReflectionClass($this);
		foreach($reflection->getProperties() as $property){
			if(!$property->isStatic()){
				$returnValue[] = $property->getName();
			}
		}
		
        // section 127-0-1-1--272f4da0:12a899718bf:-8000:00000000000024D4 end

        return (array) $returnValue;
    }

    /**
     * Short description of method __wakeup
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return mixed
     */
    public function __wakeup()
    {
        // section 127-0-1-1--272f4da0:12a899718bf:-8000:00000000000024D7 begin
        
    	if(!in_array($this->serial, self::$_instances)){
    		self::$_instances[] = $this->serial;
    	}
    	
        // section 127-0-1-1--272f4da0:12a899718bf:-8000:00000000000024D7 end
    }

    /**
     * Enable or disable the persistance mode.
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  boolean enabled
     * @return mixed
     */
    public static function setPersistance($enabled)
    {
        // section 127-0-1-1--272f4da0:12a899718bf:-8000:00000000000024F6 begin
        
    	self::$persist = (bool)$enabled;
    	
        // section 127-0-1-1--272f4da0:12a899718bf:-8000:00000000000024F6 end
    }

    /**
     * Short description of method getTemplatePath
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return string
     */
    public static function getTemplatePath()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-649cc98e:12ad7cf4ab2:-8000:0000000000002580 begin
        
        if(empty(self::$templatesPath)){
        	self::$templatesPath = BASE_PATH . '/models/classes/QTI/templates/';
        }
        $returnValue = self::$templatesPath;
        
        // section 127-0-1-1-649cc98e:12ad7cf4ab2:-8000:0000000000002580 end

        return (string) $returnValue;
    }

    /**
     * get the serial number
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return string
     */
    public function getSerial()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-59bfe477:12ad17bec82:-8000:0000000000002548 begin
        
    	if(is_null($this->serial) || empty($this->serial)){
        	$this->createSerial();
        }
        $returnValue = $this->serial;
        
        // section 127-0-1-1-59bfe477:12ad17bec82:-8000:0000000000002548 end

        return (string) $returnValue;
    }

    /**
     * get the identifier
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return string
     */
    public function getIdentifier()
    {
        $returnValue = (string) '';

        // section 127-0-1-1--56c234f4:12a31c89cc3:-8000:0000000000002320 begin
        
        if(is_null($this->identifier) || empty($this->identifier)){
        	$this->createIdentifier();
        }
        
        $returnValue = $this->identifier;
        
        // section 127-0-1-1--56c234f4:12a31c89cc3:-8000:0000000000002320 end

        return (string) $returnValue;
    }

    /**
     * Set a unique identifier.
     * If the parameter already exists a InvalidArgumentException is thrown.
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string id
     * @return mixed
     */
    public function setIdentifier($id)
    {
        // section 127-0-1-1--398d1ef5:12acc40a46b:-8000:000000000000250F begin
    	
    	if(empty($id) || is_null($id)){
    		throw new InvalidArgumentException("Id should be set");
    	}
    	
    	$idsKey = self::PREFIX . 'identifiers';
    	
    	$ids = array();
        if(Session::hasAttribute($idsKey)){
    		$ids = Session::getAttribute($idsKey);
    		if(!is_array($ids)){
    			$ids = array($ids);
    		}
    	}
    	if(in_array($id, $ids)){
    		throw new InvalidArgumentException("Id $id is already in use");
    	}
		
    	if(!empty($this->identifier)){
			$index = array_search($this->identifier, $ids);
			if($index !== false){
				unset($ids[$index]);
			}
    	}
		
    	$ids[] = $id;
    	Session::setAttribute($idsKey, $ids);
    	$this->identifier = $id;
    	
        // section 127-0-1-1--398d1ef5:12acc40a46b:-8000:000000000000250F end
    }

    /**
     * Create a unique identifier, based on the kind of instance.
     *
     * @access protected
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return mixed
     */
    protected function createIdentifier()
    {
        // section 127-0-1-1--56c234f4:12a31c89cc3:-8000:0000000000002328 begin
        
    	$idsKey = self::PREFIX . 'identifiers';
    	$ids = array();
        if(Session::hasAttribute($idsKey)){
    		$ids = Session::getAttribute($idsKey);
    		if(!is_array($ids)){
    			$ids = array($ids);
    		}
    	}
    	
    	$clazz = strtolower(get_class($this));
    	$prefix = substr($clazz, strpos($clazz, 'qti_') + 4).'_';
    		
    	$index = 1;
    	do {
    		$exist = false;
    		$id = $prefix . $index;
    		if(in_array($id, $ids)){
    			$exist = true;
    			$index++;
    		}
    	} while($exist);
    		
    	$ids[] = $id;
    	Session::setAttribute($idsKey, $ids);
    	
    	$this->identifier = $id;
    	
        // section 127-0-1-1--56c234f4:12a31c89cc3:-8000:0000000000002328 end
    }

    /**
     * create a unique serial number
     *
     * @access protected
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return mixed
     */
    protected function createSerial()
    {
        // section 127-0-1-1-59bfe477:12ad17bec82:-8000:0000000000002556 begin
        
    	$clazz  = strtolower(get_class($this));
    	$prefix = substr($clazz, strpos($clazz, 'qti_') + 4).'_';
    	$this->serial = str_replace('.', '', uniqid($prefix, true));
    	
        // section 127-0-1-1-59bfe477:12ad17bec82:-8000:0000000000002556 end
    }

    /**
     * Short description of method getType
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return string
     */
    public function getType()
    {
        $returnValue = (string) '';

        // section 127-0-1-1--182be7ee:12ad75ec1c8:-8000:00000000000025C5 begin
        
        $returnValue = $this->type;
        
        // section 127-0-1-1--182be7ee:12ad75ec1c8:-8000:00000000000025C5 end

        return (string) $returnValue;
    }

    /**
     * Short description of method setType
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string type
     * @return mixed
     */
    public function setType($type)
    {
        // section 127-0-1-1--182be7ee:12ad75ec1c8:-8000:00000000000025C7 begin
        
    	$this->type = $type;
    	
        // section 127-0-1-1--182be7ee:12ad75ec1c8:-8000:00000000000025C7 end
    }

    /**
     * get the data
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return string
     */
    public function getData()
    {
        $returnValue = (string) '';

        // section 127-0-1-1--56c234f4:12a31c89cc3:-8000:000000000000232A begin
        
        $returnValue = $this->data;
        
        // section 127-0-1-1--56c234f4:12a31c89cc3:-8000:000000000000232A end

        return (string) $returnValue;
    }

    /**
     * set the data
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string data
     * @return mixed
     */
    public function setData($data)
    {
        // section 127-0-1-1--56c234f4:12a31c89cc3:-8000:000000000000232C begin
        
    	$this->data = $data;
    	
        // section 127-0-1-1--56c234f4:12a31c89cc3:-8000:000000000000232C end
    }

    /**
     * get the options
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return array
     */
    public function getOptions()
    {
        $returnValue = array();

        // section 127-0-1-1--56c234f4:12a31c89cc3:-8000:000000000000232F begin
        
        $returnValue = $this->options;
        
        // section 127-0-1-1--56c234f4:12a31c89cc3:-8000:000000000000232F end

        return (array) $returnValue;
    }

    /**
     * set the options
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  array options
     * @return mixed
     */
    public function setOptions($options)
    {
        // section 127-0-1-1--56c234f4:12a31c89cc3:-8000:0000000000002331 begin
        
    	$this->options = $options;
    	
        // section 127-0-1-1--56c234f4:12a31c89cc3:-8000:0000000000002331 end
    }

    /**
     * get an options by it's name
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string name
     * @return mixed
     */
    public function getOption($name)
    {
        $returnValue = null;

        // section 127-0-1-1--56c234f4:12a31c89cc3:-8000:0000000000002334 begin
        
        if(array_key_exists($name, $this->options)){
        	$returnValue = $this->options[$name];
        }
        
        // section 127-0-1-1--56c234f4:12a31c89cc3:-8000:0000000000002334 end

        return $returnValue;
    }

    /**
     * set an option
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string name
     * @param  string value
     * @return mixed
     */
    public function setOption($name, $value)
    {
        // section 127-0-1-1--56c234f4:12a31c89cc3:-8000:0000000000002337 begin
        
    	$this->options[$name] = $value;
    	
        // section 127-0-1-1--56c234f4:12a31c89cc3:-8000:0000000000002337 end
    }

    /**
     * This method enables you to build a string of attributes for an xml node
     * from the instance options and regarding the option type
     *
     * @access protected
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  array formalOpts
     * @param  boolean recursive
     * @return string
     */
    protected function xmlizeOptions($formalOpts = array(), $recursive = false)
    {
        $returnValue = (string) '';

        // section 127-0-1-1-79105b43:12bc86e4da2:-8000:00000000000026D9 begin
        
        (!$recursive) ? $options = $this->options : $options = $formalOpts;
        
        
        foreach($options as $key => $value){
        	if(is_string($value) || is_numeric($value)){
        		$returnValue .= " $key = '$value' ";
        	}
        	if(is_bool($value)){
        		$returnValue .= " $key = '".(($value)?'true':'false')."' ";
        	}
        	if(is_array($value)){
        		if(count($value) > 0){
        			$keys = array_keys($value);
        			if(is_int($keys[0])){	//repeat the attribute key
		        		foreach($value as $subkey => $subvalue){
		        			$returnValue .= " $key = '$subvalue' ";
		        		}
        			}
        			else{
        				$returnValue .= $this->xmlizeOptions($value, true);
        			}
        		}
        	}
        }
        
        // section 127-0-1-1-79105b43:12bc86e4da2:-8000:00000000000026D9 end

        return (string) $returnValue;
    }

    /**
     * This method enables you to extract the attributes 
     * of the current instances to an associative array
     *
     * @access protected
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return array
     */
    protected function extractVariables()
    {
        $returnValue = array();

        // section 127-0-1-1-79105b43:12bc86e4da2:-8000:00000000000026DB begin
        
    	$reflection = new ReflectionClass($this);
		foreach($reflection->getProperties() as $property){
			if(!$property->isStatic()){
				$returnValue[$property->getName()] = $this->{$property->getName()};
			}
		}
        
        // section 127-0-1-1-79105b43:12bc86e4da2:-8000:00000000000026DB end

        return (array) $returnValue;
    }

} /* end of abstract class taoItems_models_classes_QTI_Data */

?>