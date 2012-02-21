<?php

error_reporting(E_ALL);

/**
 * The QTI's interactions are the way the user interact with the system. The
 * will be rendered into widgets to enable the user to answer to the item.
 *
 * @author firstname and lastname of author, <author@example.org>
 * @package taoItems
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10247
 * @subpackage models_classes_QTI
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * A choice is a kind of interaction's proposition.
 *
 * @author firstname and lastname of author, <author@example.org>
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10254
 */
require_once('taoItems/models/classes/QTI/class.Choice.php');

/**
 * The QTI_Data class represent the abstract model for all the QTI objects.
 * It contains all the attributes of the different kind of QTI objects.
 * It manages the identifiers and serial creation.
 * It provides the serialisation and persistance methods.
 * And give the interface for the rendering.
 *
 * @author firstname and lastname of author, <author@example.org>
 */
require_once('taoItems/models/classes/QTI/class.Data.php');

/**
 * A group is an concept to enable choice logical grouping (ordering).
 * It use when there is distinct choices groups in an interaction.
 *
 * @author firstname and lastname of author, <author@example.org>
 */
require_once('taoItems/models/classes/QTI/class.Group.php');

/**
 * The QTI_Item object represent the assessmentItem.
 * It's the main QTI object, it contains all the other objects and is the main
 * point
 * to render a complete item.
 *
 * @author firstname and lastname of author, <author@example.org>
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#section10042
 */
require_once('taoItems/models/classes/QTI/class.Item.php');

/**
 * A response is on object associated to an interactino containing which are the
 * response into the interaction choices and the score regarding the answers
 *
 * @author firstname and lastname of author, <author@example.org>
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10074
 */
require_once('taoItems/models/classes/QTI/class.Response.php');

/* user defined includes */
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:0000000000002341-includes begin
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:0000000000002341-includes end

/* user defined constants */
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:0000000000002341-constants begin
// section 127-0-1-1--56c234f4:12a31c89cc3:-8000:0000000000002341-constants end

/**
 * The QTI's interactions are the way the user interact with the system. The
 * will be rendered into widgets to enable the user to answer to the item.
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 * @package taoItems
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10247
 * @subpackage models_classes_QTI
 */
class taoItems_models_classes_QTI_Interaction
    extends taoItems_models_classes_QTI_Data
{
    // --- ASSOCIATIONS ---
    // generateAssociationEnd :     // generateAssociationEnd :     // generateAssociationEnd :     // generateAssociationEnd :

    // --- ATTRIBUTES ---

    /**
     * The choices associated to the interactions
     *
     * @access protected
     * @var array
     */
    protected $choices = array();

    /**
     * The response of the interaction
     *
     * @access protected
     * @var Response
     */
    protected $response = null;

    /**
     * The choices' groups of the interactions
     * (give a grouped and ordered view of the choices)
     *
     * @access protected
     * @var array
     */
    protected $groups = array();

    /**
     * Interaction stimulus data
     *
     * @access protected
     * @var string
     */
    protected $prompt = '';

    /**
     * Media object, used in graphic interactions
     *
     * @access protected
     * @var array
     */
    protected $object = array();

    // --- OPERATIONS ---

    /**
     * Instantiate a new interaction of the given type
     * If the id is null, a unique identifier is generated
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  string type
     * @param  string id
     * @param  array options
     * @return mixed
     */
    public function __construct($type, $id = null, $options = array())
    {
        // section 127-0-1-1-25600304:12a5c17a5ca:-8000:0000000000002488 begin

    	parent::__construct($id, $options);

    	$this->type = $type;

        // section 127-0-1-1-25600304:12a5c17a5ca:-8000:0000000000002488 end
    }

    /**
     * Set the list of choices
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  array choices
     * @return mixed
     */
    public function setChoices($choices)
    {
        // section 127-0-1-1--4be859a6:12a33452171:-8000:00000000000023EE begin

    	$this->choices = array();
    	foreach($choices as $choice){
    		$this->addChoice($choice);
    	}

        // section 127-0-1-1--4be859a6:12a33452171:-8000:00000000000023EE end
    }

    /**
     * Get the interaction's choices
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return array
     */
    public function getChoices()
    {
        $returnValue = array();

        // section 127-0-1-1--4be859a6:12a33452171:-8000:00000000000023F1 begin

        $returnValue = $this->choices;

        // section 127-0-1-1--4be859a6:12a33452171:-8000:00000000000023F1 end

        return (array) $returnValue;
    }

    /**
     * Get a choice identified by the serial
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  string serial
     * @return taoItems_models_classes_QTI_Choice
     */
    public function getChoice($serial)
    {
        $returnValue = null;

        // section 127-0-1-1--4be859a6:12a33452171:-8000:00000000000023F3 begin

        if(!empty($serial)){
        	if(array_key_exists($serial, $this->choices)){
        		$returnValue = $this->choices[$serial];
        	}
        }

        // section 127-0-1-1--4be859a6:12a33452171:-8000:00000000000023F3 end

        return $returnValue;
    }

    /**
     * Add a choice to the interaction
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  Choice choice
     * @return mixed
     */
    public function addChoice( taoItems_models_classes_QTI_Choice $choice)
    {
        // section 127-0-1-1--4be859a6:12a33452171:-8000:00000000000023F6 begin

    	if(!is_null($choice)){
    		$this->choices[$choice->getSerial()] = $choice;
    	}

        // section 127-0-1-1--4be859a6:12a33452171:-8000:00000000000023F6 end
    }

    /**
     * Remove a choice from the interaction
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  Choice choice
     * @return boolean
     */
    public function removeChoice( taoItems_models_classes_QTI_Choice $choice)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--398d1ef5:12acc40a46b:-8000:0000000000002545 begin

		if(!is_null($choice)){
    		if(isset($this->choices[$choice->getSerial()])){
    			foreach($this->getGroups() as $group){
					$group->removeChoice($choice);
				}
				$choice->destroy();
				unset($this->choices[$choice->getSerial()]);

				//remove the choice from the interaction data:
				$data = $this->getData();
				$data = str_replace("{{$choice->getSerial()}}", '', $data);
				$this->setData($data);

    			$returnValue = true;
    		}

    	}

        // section 127-0-1-1--398d1ef5:12acc40a46b:-8000:0000000000002545 end

        return (bool) $returnValue;
    }

    /**
     * Shuffle the order of the choices
     *
     * @access protected
     * @author firstname and lastname of author, <author@example.org>
     * @return string
     */
    protected function shuffleChoices()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-4fc32daa:12b6736cfe7:-8000:00000000000025B2 begin

        $returnValue = $this->data;

        //get choices order
        $matchs = array();
        $max = preg_match_all("/{choice_[a-z0-9]*}/", $returnValue, $matchs);
        if($max > 0){
        	$ordered = $matchs[0];

        	//get the choices which are fixed
        	$fixed = array();
        	foreach($ordered as $index => $choiceSerial){
        		$serial = preg_replace(array("/^{/","/}$/"), '', $choiceSerial);
        		$choice = $this->choices[$serial];
        		if($choice->getOption('fixed')){
        			$fixed[] = $index;
        		}
        	}

        	//shuffle them
        	$shuffled = array();
        	foreach($ordered as $index => $choice){
        		do {
        			$key = mt_rand(0, $max * 10);
        		} while(array_key_exists($key, $shuffled));
	        	$shuffled[$key] = $choice;
        	}
        	ksort($shuffled);
        	$i = 0;
        	foreach($shuffled as $sKey => $sChoice){
        		if($i != $sKey){
        			$shuffled[$i] = $sChoice;
        			unset($shuffled[$sKey]);
        		}
        		$i++;
        	}

        	//replace the fixed choices
        	foreach($fixed as $index){
        		$tmpChoice = $shuffled[$index];
        		$tmpIndexes = array_keys($shuffled, $ordered[$index]);
        		$tmpIndex = $tmpIndexes[0];
        		$shuffled[$tmpIndex] = $tmpChoice;
        		$shuffled[$index] = $ordered[$index];
        	}

        	foreach($shuffled as $i => $sChoice){
        		$returnValue = str_replace($ordered[$i], "{{$i}}", $returnValue);
        	}
        	foreach($shuffled as $i => $sChoice){
        		$returnValue = str_replace("{{$i}}", $sChoice, $returnValue);
        	}
        }

        // section 127-0-1-1-4fc32daa:12b6736cfe7:-8000:00000000000025B2 end

        return (string) $returnValue;
    }

    /**
     * Get the interaction's groups
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return array
     */
    public function getGroups()
    {
        $returnValue = array();

        // section 127-0-1-1-7bfc492a:12ad2946c72:-8000:0000000000002544 begin

        $returnValue  = $this->groups;

        // section 127-0-1-1-7bfc492a:12ad2946c72:-8000:0000000000002544 end

        return (array) $returnValue;
    }

    /**
     * Define the interaction's groups
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  array groups
     * @return mixed
     */
    public function setGroups($groups)
    {
        // section 127-0-1-1-4b2a2e4c:12b61a11fd4:-8000:00000000000025AF begin

    	$this->groups = array();
    	foreach($groups as $group){
    		$this->addGroup($group);
    	}

        // section 127-0-1-1-4b2a2e4c:12b61a11fd4:-8000:00000000000025AF end
    }

    /**
     * Add a  group to the interaction
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  Group group
     * @return mixed
     */
    public function addGroup( taoItems_models_classes_QTI_Group $group)
    {
        // section 127-0-1-1--56a89d8b:12ad288b4f1:-8000:0000000000002546 begin

    	$this->groups[$group->getSerial()] = $group;

        // section 127-0-1-1--56a89d8b:12ad288b4f1:-8000:0000000000002546 end
    }

    /**
     * Remove a group from the interaction
     * If recursive is set to true, it will remove the group's choices
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  Group group
     * @param  boolean recursive
     * @return boolean
     */
    public function removeGroup( taoItems_models_classes_QTI_Group $group, $recursive = false)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--56a89d8b:12ad288b4f1:-8000:000000000000254D begin

    	if(!is_null($group)){

    		if(isset($this->groups[$group->getSerial()])){

    			if($recursive){
    				foreach($group->getChoices() as $choice){
    					$this->removeChoice($choice);
    				}
    			}
    			$group->destroy();
    			unset($this->groups[$group->getSerial()]);

				//remove the group from the interaction data:
				$data = $this->getData();
				$data = str_replace("{{$group->getSerial()}}", '', $data);
				$this->setData($data);

    			$returnValue = true;
    		}

    	}

        // section 127-0-1-1--56a89d8b:12ad288b4f1:-8000:000000000000254D end

        return (bool) $returnValue;
    }

    /**
     * Get the response linked to the interaction
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return taoItems_models_classes_QTI_Response
     */
    public function getResponse()
    {
        $returnValue = null;

        // section 127-0-1-1--4be859a6:12a33452171:-8000:00000000000023F9 begin
        $returnValue = $this->response;
        // section 127-0-1-1--4be859a6:12a33452171:-8000:00000000000023F9 end

        return $returnValue;
    }

    /**
     * Define the interaction's response
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  Response response
     * @return mixed
     */
    public function setResponse( taoItems_models_classes_QTI_Response $response)
    {
        // section 127-0-1-1--4be859a6:12a33452171:-8000:00000000000023FB begin
    	$this->response = $response;
    	$this->options['responseIdentifier'] = $response->getIdentifier();

        // section 127-0-1-1--4be859a6:12a33452171:-8000:00000000000023FB end
    }

    /**
     * Get the prompt data
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return string
     */
    public function getPrompt()
    {
        $returnValue = (string) '';

        // section 127-0-1-1--424d5b00:12ad69af5de:-8000:0000000000002573 begin

        $returnValue = $this->prompt;

        // section 127-0-1-1--424d5b00:12ad69af5de:-8000:0000000000002573 end

        return (string) $returnValue;
    }

    /**
     * Define the prompt data
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  string text
     * @return mixed
     */
    public function setPrompt($text)
    {
        // section 127-0-1-1--424d5b00:12ad69af5de:-8000:0000000000002575 begin
        $tidy = new tidy();
		$text = $tidy->repairString (
			$text,
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

    	$this->prompt = $text;

        // section 127-0-1-1--424d5b00:12ad69af5de:-8000:0000000000002575 end
    }

    /**
     * Retrieve the interaction cardinality
     * (single, multiple or ordered)
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  boolean numeric
     * @return mixed
     */
    public function getCardinality($numeric = false)
    {
        $returnValue = null;

        // section 10-13-1-39-5cb6de7e:12baf74d2b5:-8000:0000000000002983 begin
		//get maximum possibility:
		switch(strtolower($this->type)){
			case 'choice':
			case 'hottext':
			case 'hotspot':
			case 'selectpoint':
			case 'positionobject':{
				$max = intval($this->getOption('maxChoices'));
				if($numeric) $returnValue = $max;
				else $returnValue = ($max==1)?'single':'multiple';//default=1
				break;
			}
			case 'associate':
			case 'match':
			case 'graphicassociate':{
				$max = intval($this->getOption('maxAssociations'));
				if($numeric) $returnValue = $max;
				else $returnValue = ($max==1)?'single':'multiple';//default=1
				break;
			}
			case 'extendedtext':{
				//maxStrings + order or not?
				$cardinality = $this->getOption('cardinality');
				if($cardinality == 'ordered'){
					if($numeric) $returnValue = 0;//meaning, infinite
					else $returnValue = $cardinality;
					break;
				}
				$max = intval($this->getOption('maxStrings'));
				if($numeric) $returnValue = $max;
				else $returnValue = ($max>1)?'multiple':'single';//optional
				break;
			}
			case 'gapmatch':{
				//count the number of gap, i.e. "groups" in the interaction:
				$max = count($this->getGroups());
				if($numeric) $returnValue = $max;
				else $returnValue = ($max>1)?'multiple':'single';
				break;
			}
			case 'graphicgapmatch':{
				//strange that the standard always specifies "multiple":
				$returnValue = 'multiple';
				break;
			}
			case 'order':
			case 'graphicorder':{
				$returnValue = ($numeric)?1:'ordered';
				break;
			}
			case 'inlinechoice':
			case 'textentry':
			case 'slider':
			case 'upload':
			case 'endattempt':{
				$returnValue = ($numeric)?1:'single';
				break;
			}
			default:{
				throw new Exception("the current interaction type \"{$this->type}\" is not available yet");
			}
		}
        // section 10-13-1-39-5cb6de7e:12baf74d2b5:-8000:0000000000002983 end

        return $returnValue;
    }

    /**
     * Get the interaction base type:
     * integer, string, identifier, pair, directedPair
     * float, boolean or point
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return string
     */
    public function getBaseType()
    {
        $returnValue = (string) '';

        // section 10-13-1-39-5cb6de7e:12baf74d2b5:-8000:0000000000002985 begin
		switch(strtolower($this->type)){
			case 'choice':
			case 'order':
			case 'inlinechoice':
			case 'hottext':
			case 'hotspot':
			case 'graphicorder':{
				$returnValue = 'identifier';
				break;
			}
			case 'associate':
			case 'graphicassociate':{
				$returnValue = 'pair';
				break;
			}
			case 'match':
			case 'gapmatch':
			case 'graphicgapmatch':{
				$returnValue = 'directedPair';
				break;
			}
			case 'textentry':
			case 'extendedtext':{
				$returnValue = 'string';
				$authorizedBaseType = array('string', 'integer', 'float');
				$response = $this->getResponse();
				if(!is_null($response)){
					$baseType = strtolower($this->getOption('baseType'));
					if(in_array($baseType, $authorizedBaseType)){
						$returnValue = $baseType;
					}
				}
				break;
			}
			case 'slider':{
				$returnValue = 'float';//default: float
				$authorizedBaseType = array('integer', 'float');
				$response = $this->getResponse();
				if(!is_null($response)){
					$baseType = strtolower($this->getOption('baseType'));
					if(in_array($baseType, $authorizedBaseType)){
						$returnValue = $baseType;
					}
				}
				break;
			}
			case 'upload':{
				$returnValue = 'file';
				break;
			}
			case 'endattempt':{
				$returnValue = 'boolean';
				break;
			}
			case 'selectpoint':
			case 'positionobject':{
				$returnValue = 'point';
				break;
			}
			default:{
				throw new Exception("the current interaction type \"{$this->type}\" is currently not available yet");
			}

		}
        // section 10-13-1-39-5cb6de7e:12baf74d2b5:-8000:0000000000002985 end

        return (string) $returnValue;
    }

    /**
     * Short description of method setObject
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  array objectData
     * @return mixed
     */
    public function setObject($objectData = array())
    {
        // section 10-13-1-39--20891d2c:12c9bf67a55:-8000:0000000000002C18 begin

		foreach($objectData as $key=>$value){
			$this->object[$key] = $value;
		}

        // section 10-13-1-39--20891d2c:12c9bf67a55:-8000:0000000000002C18 end
    }

    /**
     * Short description of method getObject
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return array
     */
    public function getObject()
    {
        $returnValue = array();

        // section 10-13-1-39--20891d2c:12c9bf67a55:-8000:0000000000002C1B begin
		$returnValue = $this->object;
        // section 10-13-1-39--20891d2c:12c9bf67a55:-8000:0000000000002C1B end

        return (array) $returnValue;
    }

    /**
     * Check if the interaction is a block or an inline interaction
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return boolean
     */
    public function isBlock()
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-3c3a6340:12c7365218a:-8000:00000000000028E5 begin

        $returnValue = in_array(strtolower($this->type), array(
        	'associate',
        	'choice',
        	'order',
        	'match',
			'extendedtext',
        	'gapmatch',
        	'hottext',
			'hotspot',
        	'selectpoint',
			'graphicassociate',
			'graphicorder',
			'graphicgapmatch',
        	'upload',
			'slider'
        ));

        // section 127-0-1-1-3c3a6340:12c7365218a:-8000:00000000000028E5 end

        return (bool) $returnValue;
    }

    /**
     * Check if the interaction is graphical
     * (use of images/SVG as working area)
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return boolean
     */
    public function isGraphic()
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--30b98426:12f25041f87:-8000:0000000000002F03 begin

         $returnValue = in_array(strtolower($this->type), array(
        	'selectpoint',
			'graphicassociate',
			'graphicorder',
			'graphicgapmatch',
         	'hotspot'
         ));

        // section 127-0-1-1--30b98426:12f25041f87:-8000:0000000000002F03 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method toXHTML
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return string
     */
    public function toXHTML()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-25600304:12a5c17a5ca:-8000:0000000000002495 begin

   		//check first if there is a template for the given type
        $template = self::getTemplatePath() . 'interactions/xhtml.' .strtolower($this->type) . '.tpl.php';
        if(!file_exists($template)){
        	 //else get the general template
        	 $template = self::getTemplatePath() . 'xhtml.interaction.tpl.php';
        }

        $variables 	= $this->extractVariables();
        $variables['rowOptions'] = json_encode($this->options);

        $variables['class'] = '';
        if(isset($this->options['class'])){
        	$variables['class'] = $this->options['class'];
        }

		//change from camelCase to underscore_case the type of the interaction to be used in the JS
		$variables['_type']	= strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $this->type));

		//suffle the choices for the runtime if defined in the QTI
		if($this->getOption('shuffle') === true){
			$variables['data'] = $this->shuffleChoices();
		}

   		switch($this->type){
   			case 'associate':
   			case 'choice':
   			case 'order':
   			case 'gapMatch':
   				$variables['data'] = preg_replace("/({choice_[a-z0-9]*}(.*){choice_[a-z0-9]*})|({choice_[a-z0-9]*})/mi", "<ul class='qti_choice_list'>$0</ul>", $variables['data']);
   				break;
   			case 'hotspot':
       		case 'graphicOrder':
       		case 'graphicAssociate':
       		case 'graphicGapMatch':
   				$variables['data'] = preg_replace("/({choice_[a-z0-9]*}(.*){choice_[a-z0-9]*})|({choice_[a-z0-9]*})/mi", "<ul class='qti_{$variables['_type']}_spotlist'>$0</ul>", $variables['data']);
   				break;
   		}

   		//build back the choices in the data variable
   		if(count($this->getGroups()) > 0){
   			foreach($this->getGroups() as $group){
   				common_Logger::i("The group is ".get_class($group));
				$variables['data'] = preg_replace("/{".$group->getSerial()."}/", $group->toXHTML(), $variables['data']);
			}
			foreach($this->getChoices() as $choice){
				$variables['data'] = preg_replace("/{".$choice->getSerial()."}/", $choice->toXHTML(), $variables['data']);
			}

			//create the matchGroup from the choice list
			if($this->type == 'gapMatch'){
				foreach($this->getGroups() as $group){
					$matchGroup = array();
					foreach($group->getChoices() as $choiceSerial){
						foreach($this->getChoices() as $choice){
							if($choice->getSerial() == $choiceSerial){
								$matchGroup[] = $choice->getIdentifier();
								break;
							}
						}
					}
					if(count($matchGroup) > 0){
						$group->setOption('matchGroup', $matchGroup);
					}
				}
			}
   		}
   		else{
   			foreach($this->getChoices() as $choice){
				$variables['data'] = preg_replace("/{".$choice->getSerial()."}/", $choice->toXHTML(), $variables['data']);
			}
   		}

   		// Give to the template the response base type linked to this interaction
   		// @todo check if this information is not yet available
		$response = $this->getResponse ();
		if ($response != null){
			$variables['options']['responseBaseType'] = $response->getBaseType();
		}

        $tplRenderer = new taoItems_models_classes_TemplateRenderer($template, $variables);
      	$returnValue = $tplRenderer->render();

        // section 127-0-1-1-25600304:12a5c17a5ca:-8000:0000000000002495 end

        return (string) $returnValue;
    }

    /**
     * Short description of method toQTI
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return string
     */
    public function toQTI()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-25600304:12a5c17a5ca:-8000:0000000000002497 begin

        //check first if there is a template for the given type
        $template = self::getTemplatePath() . 'interactions/qti.' .strtolower($this->type) . '.tpl.php';
        if(!file_exists($template)){
        	 $template = self::getTemplatePath() . 'qti.interaction.tpl.php';
        }

        $variables 	= $this->extractVariables();
        $variables['rowOptions'] 	= $this->xmlizeOptions();

   		//build back the choices in the data variable
   		if(count($this->getGroups()) > 0){
   			//create the matchGroup from the choice list
			if($this->type == 'gapMatch' || $this->type == 'graphicGapMatch'){
				foreach($this->getGroups() as $group){
					$matchGroup = array();
					foreach($group->getChoices() as $choiceSerial){
						foreach($this->getChoices() as $choice){
							if($choice->getSerial() == $choiceSerial){
								$matchGroup[] = $choice->getIdentifier();
								break;
							}
						}
					}
					if(count($matchGroup) > 0){
						$group->setOption('matchGroup', $matchGroup);
					}
				}
			}
   			foreach($this->getGroups() as $group){
				$variables['data'] = preg_replace("/{".$group->getSerial()."}/", $group->toQti(), $variables['data']);
			}
   		}

		foreach($this->getChoices() as $choice){
			$variables['data'] = preg_replace("/{".$choice->getSerial()."}/", $choice->toQti(), $variables['data']);
		}

		//object tag used in the graphic interactions
		if(count($this->object) > 0){
			(isset($this->object['_alt'])) ? $_alt = $this->object['_alt'] : $_alt = '';
			$objectAttributes = '';
			foreach($this->object as $key => $value){
				if($key != '_alt'){
					$objectAttributes .= "{$key} = '{$value}' ";
				}
			}
			if(!isset($this->object['type'])){
				$objectAttributes.= " type='' ";	//type attr mandatory
			}
			$variables['object_alt'] = $_alt;
			$variables['objectAttributes'] = $objectAttributes;
		}

		//parse and render the template
		$tplRenderer = new taoItems_models_classes_TemplateRenderer($template, $variables);
		$returnValue = $tplRenderer->render();

        // section 127-0-1-1-25600304:12a5c17a5ca:-8000:0000000000002497 end

        return (string) $returnValue;
    }

    /**
     * Short description of method toForm
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return tao_helpers_form_Form
     */
    public function toForm()
    {
        $returnValue = null;

        // section 127-0-1-1-25600304:12a5c17a5ca:-8000:0000000000002499 begin

		$interactionFormClass = 'taoItems_actions_QTIform_interaction_'.ucfirst(strtolower($this->getType())).'Interaction';
		if(!class_exists($interactionFormClass)){
			throw new Exception("the class {$interactionFormClass} does not exist");
		}else{
			$formContainer = new $interactionFormClass($this, $this->getChoices());//include choices or not...
			$myForm = $formContainer->getForm();
			$returnValue = $myForm;
		}


        // section 127-0-1-1-25600304:12a5c17a5ca:-8000:0000000000002499 end

        return $returnValue;
    }

    /**
     * Short description of method canRenderTesttakerResponse
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return boolean
     */
    public function canRenderTesttakerResponse()
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--7ddc6625:1358a866f6a:-8000:000000000000382F begin
        $returnValue = in_array(strtolower($this->type), array('extendedtext'));
        // section 127-0-1-1--7ddc6625:1358a866f6a:-8000:000000000000382F end

        return (bool) $returnValue;
    }

    /**
     * Short description of method renderTesttakerResponseXHTML
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  responses
     * @return string
     */
    public function renderTesttakerResponseXHTML($responses)
    {
        $returnValue = (string) '';

        // section 127-0-1-1--7ddc6625:1358a866f6a:-8000:000000000000382D begin
        // test if XHTMLPreview supported
        if (!$this->canRenderTesttakerResponse()) {
        	throw new common_exception_Error('XHTMLPreview not implemented for '.$this->type);
        }
        //@todo implementation of Preview
        /*$returnValue = $this->toXHTML().'Answers: ';
        foreach ($responses as $response) {
        	if (is_string($response)) {
        		$returnValue .= $response.'<br>';
        	} elseif (is_array($response)) {
        		$returnValue .= 'array<br>';
        	} else
        		$returnValue .= get_class($response).'<br>';
        }*/


				//check first if there is a template for the given type
        $template = self::getTemplatePath() . 'interactions/TesttakersResponse/xhtml.' .strtolower($this->type) . '.tpl.php';
        if(!file_exists($template)){
        	 //else get the general template
        	 $template = self::getTemplatePath() . 'xhtml.interaction.tpl.php';
        }

        $variables 	= $this->extractVariables();
        $variables['rowOptions'] = json_encode($this->options);
				$variables['testtakerResponses'] = $responses;

        $variables['class'] = '';
        if(isset($this->options['class'])){
        	$variables['class'] = $this->options['class'];
        }

				//change from camelCase to underscore_case the type of the interaction to be used in the JS
				$variables['_type']	= strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $this->type));
/*
				//suffle the choices for the runtime if defined in the QTI
				if($this->getOption('shuffle') === true){
					$variables['data'] = $this->shuffleChoices();
				}
*/
				switch($this->type){
					case 'associate':
					case 'choice':
					case 'order':
					case 'gapMatch':
						$variables['data'] = preg_replace("/({choice_[a-z0-9]*}(.*){choice_[a-z0-9]*})|({choice_[a-z0-9]*})/mi", "<ul class='qti_choice_list'>$0</ul>", $variables['data']);
						break;
					case 'hotspot':
						case 'graphicOrder':
						case 'graphicAssociate':
						case 'graphicGapMatch':
						$variables['data'] = preg_replace("/({choice_[a-z0-9]*}(.*){choice_[a-z0-9]*})|({choice_[a-z0-9]*})/mi", "<ul class='qti_{$variables['_type']}_spotlist'>$0</ul>", $variables['data']);
						break;
				}
/*
				//build back the choices in the data variable
				if(count($this->getGroups()) > 0){
					foreach($this->getGroups() as $group){
					$variables['data'] = preg_replace("/{".$group->getSerial()."}/", $group->toXHTML(), $variables['data']);
				}
				foreach($this->getChoices() as $choice){
					$variables['data'] = preg_replace("/{".$choice->getSerial()."}/", $choice->toXHTML(), $variables['data']);
				}

				//create the matchGroup from the choice list
				if($this->type == 'gapMatch'){
					foreach($this->getGroups() as $group){
						$matchGroup = array();
						foreach($group->getChoices() as $choiceSerial){
							foreach($this->getChoices() as $choice){
								if($choice->getSerial() == $choiceSerial){
									$matchGroup[] = $choice->getIdentifier();
									break;
								}
							}
						}
						if(count($matchGroup) > 0){
							$group->setOption('matchGroup', $matchGroup);
						}
					}
				}
				}
				else{
					foreach($this->getChoices() as $choice){
						$variables['data'] = preg_replace("/{".$choice->getSerial()."}/", $choice->toXHTML(), $variables['data']);
					}
				}
*/
				// Give to the template the response base type linked to this interaction
				// @todo check if this information is not yet available
				$responseBase = $this->getResponse();
				if ($responseBase != null) {
					$variables['options']['responseBaseType'] = $responseBase->getBaseType();
				}

        $tplRenderer = new taoItems_models_classes_TemplateRenderer($template, $variables);
      	$returnValue = $tplRenderer->render();


        // section 127-0-1-1--7ddc6625:1358a866f6a:-8000:000000000000382D end

        return (string) $returnValue;
    }

    /**
     * Short description of method destroy
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return mixed
     */
    public function destroy()
    {
        // section 127-0-1-1--7c75b87:1355db19059:-8000:00000000000037B5 begin
		//delete response:
    	if(!is_null($this->getResponse())){
			$this->getResponse()->destroy();
		}

		//delete choices:
		foreach($this->getChoices() as $choice){
			$choice->destroy();
		}

		//delete groups:
		foreach($this->getGroups() as $group){
			$group->destroy();
		}
        parent::destroy();
        // section 127-0-1-1--7c75b87:1355db19059:-8000:00000000000037B5 end
    }

} /* end of class taoItems_models_classes_QTI_Interaction */

?>