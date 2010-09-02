<?php

error_reporting(E_ALL);

/**
 * TAO - taoItems/models/classes/QTI/class.Data.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 02.09.2010, 11:51:52 with ArgoUML PHP module 
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
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:00000000000022FE-includes begin
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:00000000000022FE-includes end

/* user defined constants */
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:00000000000022FE-constants begin
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:00000000000022FE-constants end

/**
 * Short description of class taoItems_models_classes_QTI_Data
 *
 * @abstract
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI
 */
abstract class taoItems_models_classes_QTI_Data
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute id
     *
     * @access protected
     * @var string
     */
    protected $id = '';

    /**
     * Short description of attribute serial
     *
     * @access protected
     * @var string
     */
    protected $serial = '';

    /**
     * Short description of attribute data
     *
     * @access protected
     * @var string
     */
    protected $data = '';

    /**
     * Short description of attribute options
     *
     * @access protected
     * @var array
     */
    protected $options = array();

    /**
     * Short description of attribute persist
     *
     * @access public
     * @var boolean
     */
    public static $persist = true;

    /**
     * Short description of attribute PREFIX
     *
     * @access public
     * @var string
     */
    const PREFIX = 'qti_';

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string id
     * @param  array options
     * @return mixed
     */
    public function __construct($id = null, $options = array())
    {
        // section 127-0-1-1--56c234f4:12a31c89cc3:-8000:0000000000002318 begin
        
    	$this->createSerial();
    	
    	try{
    		$this->setId($id);
    	}
    	catch(InvalidArgumentException $iae){
    		$this->createId();
    	}
    	
    	$this->options = $options;
    	
        // section 127-0-1-1--56c234f4:12a31c89cc3:-8000:0000000000002318 end
    }

    /**
     * Short description of method __destruct
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return mixed
     */
    public function __destruct()
    {
        // section 127-0-1-1--272f4da0:12a899718bf:-8000:00000000000024CF begin
        
    	if(self::$persist){
    		Session::setAttribute($this->serial, serialize($this));
        }
        else{
        	if(!empty($this->serial)){
        		Session::removeAttribute($this->serial);
        	}
        	if(!empty($this->id) && !is_null($this->id)){
        		$ids = Session::getAttribute('qti-ids');
        		if(is_array($ids)){
	    			if(isset($ids[$this->id])){
        				unset($ids[$this->id]);
	    				Session::setAttribute('qti-ids', $ids);
	    			}
        		}
        	}
        }
        
        // section 127-0-1-1--272f4da0:12a899718bf:-8000:00000000000024CF end
    }

    /**
     * Short description of method __sleep
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
        // section 127-0-1-1--272f4da0:12a899718bf:-8000:00000000000024D7 end
    }

    /**
     * Short description of method setPersistance
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
     * Short description of method getSerial
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
     * Short description of method getId
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return string
     */
    public function getId()
    {
        $returnValue = (string) '';

        // section 127-0-1-1--56c234f4:12a31c89cc3:-8000:0000000000002320 begin
        
        if(is_null($this->id) || empty($this->id)){
        	$this->createId();
        }
        $this->createSerial();
        
        $returnValue = $this->id;
        
        // section 127-0-1-1--56c234f4:12a31c89cc3:-8000:0000000000002320 end

        return (string) $returnValue;
    }

    /**
     * Short description of method setId
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string id
     * @return mixed
     */
    public function setId($id)
    {
        // section 127-0-1-1--398d1ef5:12acc40a46b:-8000:000000000000250F begin
    	
    	if(empty($id) || is_null($id)){
    		throw new InvalidArgumentException("Id should be set");
    	}
    	
    	$ids = array();
        if(Session::hasAttribute('qti-ids')){
    		$ids = Session::getAttribute('qti-ids');
    		if(!is_array($ids)){
    			$ids = array($ids);
    		}
    	}
    	if(in_array($id, $ids)){
    		throw new InvalidArgumentException("Id $id is already in use");
    	}
    	if(!empty($this->id)){
    		unset($ids[$this->id]);
    	}
    	
    	$ids[] = $id;
    	Session::setAttribute('qti-ids', $ids);
    	$this->id = $id;
    	
        // section 127-0-1-1--398d1ef5:12acc40a46b:-8000:000000000000250F end
    }

    /**
     * Short description of method createId
     *
     * @access protected
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return mixed
     */
    protected function createId()
    {
        // section 127-0-1-1--56c234f4:12a31c89cc3:-8000:0000000000002328 begin
        
    	$ids = array();
        if(Session::hasAttribute('qti-ids')){
    		$ids = Session::getAttribute('qti-ids');
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
    	Session::setAttribute('qti-ids', $ids);
    	
    	$this->id = $id;
    	
        // section 127-0-1-1--56c234f4:12a31c89cc3:-8000:0000000000002328 end
    }

    /**
     * Short description of method createSerial
     *
     * @access protected
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return mixed
     */
    protected function createSerial()
    {
        // section 127-0-1-1-59bfe477:12ad17bec82:-8000:0000000000002556 begin
        
    	$clazz  = strtolower(get_class($this));
    	$prefix = substr($clazz, strpos($clazz, 'qti_')).'_';
    	$this->serial = str_replace('.', '', uniqid($prefix, true));
    	
        // section 127-0-1-1-59bfe477:12ad17bec82:-8000:0000000000002556 end
    }

    /**
     * Short description of method getData
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
     * Short description of method setData
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
     * Short description of method getOptions
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
     * Short description of method setOptions
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
     * Short description of method getOption
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string name
     * @return string
     */
    public function getOption($name)
    {
        $returnValue = (string) '';

        // section 127-0-1-1--56c234f4:12a31c89cc3:-8000:0000000000002334 begin
        
        if(array_key_exists($name, $this->options)){
        	$returnValue = $this->options[$name];
        }
        
        // section 127-0-1-1--56c234f4:12a31c89cc3:-8000:0000000000002334 end

        return (string) $returnValue;
    }

    /**
     * Short description of method setOption
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
     * Short description of method toXHTML
     *
     * @abstract
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return string
     */
    public abstract function toXHTML();

    /**
     * Short description of method toQTI
     *
     * @abstract
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return string
     */
    public abstract function toQTI();

    /**
     * Short description of method toForm
     *
     * @abstract
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return tao_helpers_form_Form
     */
    public abstract function toForm();

} /* end of abstract class taoItems_models_classes_QTI_Data */

?>