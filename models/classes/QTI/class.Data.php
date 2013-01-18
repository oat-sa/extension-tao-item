<?php

error_reporting(E_ALL);

/**
 * The QTI_Data class represent the abstract model for all the QTI objects.
 * It contains all the attributes of the different kind of QTI objects.
 * It manages the identifiers and serial creation.
 * It provides the serialisation and persistance methods.
 * And give the interface for the rendering.
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include common_cache_PartitionedCachable
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 */
require_once('common/cache/class.PartitionedCachable.php');

/**
 * By implementing the exportable interface, the object must export it's data to
 * formats defined here.
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
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
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI
 */
abstract class taoItems_models_classes_QTI_Data
    extends common_cache_PartitionedCachable
        implements taoItems_models_classes_QTI_Exportable
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute IDENTIFIERS_KEY
     *
     * @access private
     * @var string
     */
    const IDENTIFIERS_KEY = 'qti_i';

    /**
     * Short description of attribute templatesPath
     *
     * @access protected
     * @var string
     */
    protected static $templatesPath = '';

    /**
     * Short description of attribute persist
     *
     * @access public
     * @var boolean
     */
    public static $persist = false;

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

    // --- OPERATIONS ---

    /**
     * Export the data in XHTML format
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
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
		
        $tplRenderer = new taoItems_models_classes_TemplateRenderer($template, $variables);
        $returnValue = $tplRenderer->render();
        // section 127-0-1-1--3f707dcb:12af06fca53:-8000:0000000000004159 end

        return (string) $returnValue;
    }

    /**
     * EXport the data in the QTI XML format
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
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
		
        $tplRenderer = new taoItems_models_classes_TemplateRenderer($template, $variables);
        $returnValue = $tplRenderer->render();
        
        // section 127-0-1-1--3f707dcb:12af06fca53:-8000:000000000000415B end

        return (string) $returnValue;
    }

    /**
     * EXport the data into TAO's objects Form
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return tao_helpers_form_xhtml_Form
     */
    public function toForm()
    {
        $returnValue = null;

        // section 127-0-1-1--3f707dcb:12af06fca53:-8000:000000000000415D begin
        // section 127-0-1-1--3f707dcb:12af06fca53:-8000:000000000000415D end

        return $returnValue;
    }

    /**
     * Short description of method setPersistence
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  boolean enabled
     * @return mixed
     */
    public static function setPersistence($enabled)
    {
        // section 127-0-1-1--18485ef3:13542665222:-8000:00000000000065B5 begin
    	self::$persist = (bool)$enabled;
		if (!$enabled) {
			taoItems_models_classes_QTI_QTISessionCache::singleton()->purge();
		}
        // section 127-0-1-1--18485ef3:13542665222:-8000:00000000000065B5 end
    }

    /**
     * Short description of method getTemplatePath
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    public static function getTemplatePath()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-649cc98e:12ad7cf4ab2:-8000:0000000000002580 begin
        
        if(empty(self::$templatesPath)){
        	self::$templatesPath = ROOT_PATH . '/taoItems/models/classes/QTI/templates/';
        }
        $returnValue = self::$templatesPath;
        
        // section 127-0-1-1-649cc98e:12ad7cf4ab2:-8000:0000000000002580 end

        return (string) $returnValue;
    }

    /**
     * The constructor initialize the instance with the given identifier (if
     * a human readable identifier will be created)
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string identifier
     * @param  array options
     * @return mixed
     */
    public function __construct($identifier = null, $options = array())
    {
        // section 127-0-1-1--56c234f4:12a31c89cc3:-8000:0000000000002318 begin
    	try{
    		$this->setIdentifier($identifier);
    	}
    	catch(InvalidArgumentException $iae){
			$prefix = isset($options['identifierPrefix'])?(string)$options['identifierPrefix']:'';
    		$this->createIdentifier($prefix);
    	}
    	$this->options = $options;
        parent::__construct($identifier);
        // section 127-0-1-1--56c234f4:12a31c89cc3:-8000:0000000000002318 end
    }

    /**
     * get the identifier
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
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
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string id
     * @param  boolean unique
     * @return mixed
     */
    public function setIdentifier($id, $unique = true)
    {
        // section 127-0-1-1--398d1ef5:12acc40a46b:-8000:000000000000250F begin
    	if(empty($id) || is_null($id)){
    		throw new InvalidArgumentException("Id should be set");
    	}
    	
    	$ids = array();
        if(Session::hasAttribute(self::IDENTIFIERS_KEY)){
    		$ids = Session::getAttribute(self::IDENTIFIERS_KEY);
    		if(!is_array($ids)){
    			$ids = array($ids);
    		}
    	}
    	if($unique){
	    	if(in_array($id, $ids)){
	    		common_Logger::w("Tried to set non unique identifier ".$id, array('TAOITEMS', 'QTI'));
	    		throw new InvalidArgumentException("The identifier \"{$id}\" is already in use");
	    	}
    	}
		
    	if(!empty($this->identifier)){
			$index = array_search($this->identifier, $ids);
			if($index !== false){
				unset($ids[$index]);
			}
    	}
		
    	$ids[] = $id;
    	
    	Session::setAttribute(self::IDENTIFIERS_KEY, $ids);
    	$this->identifier = $id;
    	
        // section 127-0-1-1--398d1ef5:12acc40a46b:-8000:000000000000250F end
    }

    /**
     * Create a unique identifier, based on the kind of instance.
     *
     * @access protected
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string prefix
     * @return mixed
     */
    protected function createIdentifier($prefix = '')
    {
        // section 127-0-1-1--56c234f4:12a31c89cc3:-8000:0000000000002328 begin
        
    	$ids = array();
        if(Session::hasAttribute(self::IDENTIFIERS_KEY)){
    		$ids = Session::getAttribute(self::IDENTIFIERS_KEY);
    		if(!is_array($ids)){
    			$ids = array($ids);
    		}
    	}
    	
    	$clazz = strtolower(get_class($this));
		
		if(empty($prefix)){
			$prefix = substr($clazz, strpos($clazz, 'qti_') + 4).'_';
		}else{
			$prefix = preg_replace('/[^a-zA-Z0-9_]/', '_', $prefix);
			$prefix = preg_replace('/(_)+/', '_', $prefix);
			$prefix .= '_';
		}
    		
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
    	Session::setAttribute(self::IDENTIFIERS_KEY, $ids);
    	
    	$this->identifier = $id;
        // section 127-0-1-1--56c234f4:12a31c89cc3:-8000:0000000000002328 end
    }

    /**
     * Short description of method getType
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
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
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
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
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
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
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string data
     * @param  boolean cleanup
     * @return mixed
     */
    public function setData($data, $cleanup = true)
    {
        // section 127-0-1-1--56c234f4:12a31c89cc3:-8000:000000000000232C begin
        
    	if ($cleanup){
			$tidy = new tidy();
			$data = $tidy->repairString (
				$data,
				array(
					'output-xhtml' => true,
					'numeric-entities' => true,
					'show-body-only' => true,
					'quote-nbsp' => false,
					'indent' => 'auto',
					'preserve-entities' => false,
					'quote-ampersand' => true,
					'uppercase-attributes' => false,
					'uppercase-tags' => false
				),
				'UTF8'
			);
    	}
		
    	$this->data = $data;
    	
        // section 127-0-1-1--56c234f4:12a31c89cc3:-8000:000000000000232C end
    }

    /**
     * Short description of method getDataXHTML
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    public function getDataXHTML()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-57545382:12c3b5a4400:-8000:0000000000002BD3 begin
        
        // Embedd in a root node
        $dataXHTML = '<div>'.$this->getData ().'</div>';
        
        $dom = new DOMDocument ();
        $dom->loadXML ($dataXHTML);
        
        $xhtmlTagsName = Array ('feedbackInline');
        if ($dom != false) {
            foreach ($xhtmlTagsName as $tagName){
                $elts = $dom->getElementsByTagName ($tagName);
                foreach ($elts as $elt){
                    //var_dump ($elt->nodeValue);
                    $elt->insertBefore ($elt->cloneNode());
                    break;
                }
            }
        }
        
        $dataXHTML = $dom->saveXML();
        
        //$libDom = dom_import_simplexml($xml);
        //var_dump ($dom);
        
        //$dom = new DOMDocument();
        //$libDom = $dom->importNode($xml, true);
        /*var_dump ($libDom);
        
        
        if ($xml != false) {
            foreach ($xhtmlTagsName as $tagName){
                $elts = $xml->xpath ($tagName);
                foreach ($elts as $elt){
                    var_dump ($elt);
                }
            }
        }*/
        
        $returnValue = $dataXHTML;
        
        // section 127-0-1-1-57545382:12c3b5a4400:-8000:0000000000002BD3 end

        return (string) $returnValue;
    }

    /**
     * Short description of method setDataXHTML
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string data
     * @return mixed
     */
    public function setDataXHTML($data)
    {
        // section 127-0-1-1-57545382:12c3b5a4400:-8000:0000000000002BD5 begin
        // section 127-0-1-1-57545382:12c3b5a4400:-8000:0000000000002BD5 end
    }

    /**
     * get the options
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
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
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
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
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string name
     * @return mixed
     */
    public function getOption($name)
    {
        $returnValue = null;

        // section 127-0-1-1--56c234f4:12a31c89cc3:-8000:0000000000002334 begin
        
        if(array_key_exists($name, $this->options)){
        	$returnValue = $this->options[$name];
        	if(is_string($this->options[$name])){
        		if($this->options[$name] == 'true'){
        			$returnValue = true;
        		}
        		if($this->options[$name] == 'false'){
        			$returnValue = false;
        		}
        	}
        }
        
        // section 127-0-1-1--56c234f4:12a31c89cc3:-8000:0000000000002334 end

        return $returnValue;
    }

    /**
     * set an option
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
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
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
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
				// str_replace is unicode safe...
        		$returnValue .= " $key = '" . str_replace(array('&', '<', '>', '\'', '"'), array('&amp;', '&lt;', '&gt;', '&apos;', '&quot;'), $value) . "' ";
        	}
        	if(is_bool($value)){
        		$returnValue .= " $key = '".(($value)?'true':'false')."' ";
        	}
        	if(is_array($value)){
        		if(count($value) > 0){
        			$keys = array_keys($value);
        			if(is_int($keys[0])){	//repeat the attribute key
		        		$returnValue .= " $key = '".implode(' ',array_values($value))."' ";
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
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return array
     */
    protected function extractVariables()
    {
        $returnValue = array();

        // section 127-0-1-1-79105b43:12bc86e4da2:-8000:00000000000026DB begin
        
    	$reflection = new ReflectionClass($this);
		foreach($reflection->getProperties() as $property){
			if(!$property->isStatic() && !$property->isPrivate()){
				$returnValue[$property->getName()] = $this->{$property->getName()};
			}
		}
        
        // section 127-0-1-1-79105b43:12bc86e4da2:-8000:00000000000026DB end

        return (array) $returnValue;
    }

    /**
     * create a unique serial number
     *
     * @access protected
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    protected function buildSerial()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-59bfe477:12ad17bec82:-8000:0000000000002556 begin
    	$clazz  = strtolower(get_class($this));
    	$prefix = substr($clazz, strpos($clazz, 'qti_') + 4).'_';
    	$returnValue = str_replace('.', '', uniqid($prefix, true));
        // section 127-0-1-1-59bfe477:12ad17bec82:-8000:0000000000002556 end

        return (string) $returnValue;
    }

    /**
     * Short description of method getCache
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return common_cache_Cache
     */
    public function getCache()
    {
        $returnValue = null;

        // section 127-0-1-1--18485ef3:13542665222:-8000:00000000000065B3 begin
        if (self::$persist) {
        	$returnValue = taoItems_models_classes_QTI_QTISessionCache::singleton();
        }
        // section 127-0-1-1--18485ef3:13542665222:-8000:00000000000065B3 end

        return $returnValue;
    }

    /**
     * Short description of method destroy
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return mixed
     */
    public function destroy()
    {
        // section 127-0-1-1-40168e54:135573066b9:-8000:000000000000374D begin
        common_Logger::d('Destroying in QTIAuthoring: '.$this->getSerial());
		if(!empty($this->identifier) && !is_null($this->identifier)){
			$ids = Session::getAttribute(self::IDENTIFIERS_KEY);
			if(is_array($ids)){
				if(in_array($this->identifier, $ids)){
					$key = array_search($this->identifier, $ids);
					if($key !== false){
						unset($ids[$key]);
						sort($ids);
						Session::setAttribute(self::IDENTIFIERS_KEY, $ids);
					}
				}
			}
		}
		parent::_remove();
        // section 127-0-1-1-40168e54:135573066b9:-8000:000000000000374D end
    }

} /* end of abstract class taoItems_models_classes_QTI_Data */

?>