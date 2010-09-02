<?php

error_reporting(E_ALL);

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

require_once('tao/models/classes/class.GenerisService.php');

/**
 * Service methods to manage the QTI authoring business
 *
 * @access public
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoItems
 * @subpackage models_classes
 */
class taoItems_models_classes_QtiAuthoringService
    extends tao_models_classes_GenerisService
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * The RDFS top level item class
     *
     * @access protected
     * @var Class
     */
    protected $itemClass = null;

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return void
     */
    public function __construct()
    {
		parent::__construct();
    }
	
	/**
     * This method creates a new item object to be used as the data container of the qtiAuthoring tool
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return taoItems_models_classes_QTI_Item
     */
	public function createNewItem($itemUri){
		
		$returnValue = null;
		// $qtiService = tao_models_classes_ServiceFactory::get("taoItems_models_classes_QTI_Service");
		
		$itemId = tao_helpers_Uri::getUniqueId($itemUri);
		if(empty($itemId)){
			throw new Exception('wrong format of itemUri given');
		}else{
			$itemId = 'qti_item_'.$itemId;
			$returnValue = new taoItems_models_classes_QTI_Item($itemId, array());
		}
		
		return $returnValue;
	}
	
	/**
     * Returns the item data after replacing the interaction tags with the element identifier of the authoring tool
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return taoItems_models_classes_QTI_Item
     */
	public function getItemData(taoItems_models_classes_QTI_Item $item){
		$itemData = $item->getData();
		
		//insert the interaction tags:
		foreach($item->getInteractions() as $interaction){
			//replace the interactions by a identified tag with the authoring elements
			$pattern = "/{{$interaction->getId()}}/";
			$itemData = preg_replace($pattern, $this->getInteractionTag($interaction), $itemData, 1);
		}
		
		return $itemData;
	}
	
	public function getInteractionTag(taoItems_models_classes_QTI_Interaction $interaction){
		$returnValue = '';
		// $returnValue .= "<input type='button' id='{$interaction->getId()}' class='qti_interaction_link' value='{$interaction->getType()} Interaction'/>";
		$returnValue .= "<input type='button' id='{$interaction->getId()}' class='qti_interaction_link' value='{$interaction->getType()} Interaction'/>";
		
		return $returnValue;
	}
	
	public function saveItemData(taoItems_models_classes_QTI_Item $item, $itemData){
		if(!is_null($item)){
			
			//clean the interactions' editing elements:
			foreach($item->getInteractions() as $interaction){
				//replace the interactions by a identified tag with the authoring elements
				$pattern0 = $this->getInteractionTag($interaction);
				// $pattern = "/{$pattern0}/";
				// $itemData = preg_replace($pattern, "{{$interaction->getId()}}", $itemData, 1);
				$count = 0;
				$itemData = str_replace($pattern0, "{{$interaction->getId()}}", $itemData, $count);
			}
			
			//item saved in session:
			$item->setData($itemData);
		}
	}
	
	/**
     * This method creates a new item object to be used as the data container of the qtiAuthoring tool
     *
     * @access public
     * @param  taoItems_models_classes_QTI_Item item
	 * @param  string interactionType
     * @return taoItems_models_classes_QTI_Interaction
     */
	public function addInteraction(taoItems_models_classes_QTI_Item $item, $interactionType){
		
		$returnValue = null;
		
		$authorizedInteractions = array(
			'choice',
			'match',
			'gap',
			'hottext',
			'graphicassociate',
			'graphicgapmatch'
		);
		
		$interactionType = strtolower($interactionType);
		
		if(!is_null($item) && in_array($interactionType, $authorizedInteractions)){
			//create interaction:
			$interaction = new taoItems_models_classes_QTI_Interaction($interactionType);
			
			//add to the item object:
			$item->addInteraction($interaction);
			// $item->setData($itemData);
			
			$returnValue = $interaction;
		}
		
		return $returnValue;
	}
	
	
	public function addChoice(taoItems_models_classes_QTI_Interaction $interaction, $data='', $name='', $value=''){
		
		$returnValue = null;
		
		if(!is_null($interaction)){
			//create a new choice:
			//determine the type of choice automatically?
			$choice = new taoItems_models_classes_QTI_Choice(null);
			if(!empty($data)){
				$choice->setData($data);
			}
		
			$interaction->addChoice($choice);
			
			$returnValue = $choice;
		}
		
		return $returnValue;
	}
	
	public function editChoiceData(taoItems_models_classes_QTI_Choice $choice, $data=''){
		if(!is_null($choice)){
			$choice->setdata($data);
		}
	}
	
	public function deleteInteraction(taoItems_models_classes_QTI_Item $item, taoItems_models_classes_QTI_Interaction $interaction){
		//add specific method in the item class: deleteInteraction??
		$item->removeInteraction($interaction);
	}
	
	public function deleteChoice(taoItems_models_classes_QTI_Interaction $interaction, taoItems_models_classes_QTI_Choice $choice){
		//add specific method in the interaction class: deleteChoice??
		$interaction->removeChoice($choice);
	}
	
    /**
     * define the content of item to be inserted by default (to prevent null
     * after creation)
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource item
     * @return core_kernel_classes_Resource
     */
    public function setDefaultItemContent( core_kernel_classes_Resource $item)
    {
        $returnValue = null;

        // section 127-0-1-1-c213658:12568a3be0b:-8000:0000000000001CE9 begin
		
		try{
			$itemContent = $item->getOnePropertyValue(new core_kernel_classes_Property(TAO_ITEM_CONTENT_PROPERTY));//it is ok if it is null
			$itemModel = $item->getUniquePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_PROPERTY));
			
			if($itemModel instanceof core_kernel_classes_Resource){
				$content = (string)$itemContent;
				if(trim($content) == ''){
					switch($itemModel->uriResource){
						
						case TAO_ITEM_MODEL_WATERPHENIX:
							$content = file_get_contents(TAO_ITEM_HAWAI_TPL_FILE);
							$content = str_replace('{ITEM_URI}', $item->uriResource, $content);
							
							$item = $this->bindProperties($item, array(
								TAO_ITEM_CONTENT_PROPERTY => $content
							));
							break;
						case TAO_ITEM_MODEL_CAMPUS:
							$content = file_get_contents(TAO_ITEM_CAMPUS_TPL_FILE);
							$content = str_replace('{ITEM_URI}', $item->uriResource, $content);
							
							$item = $this->bindProperties($item, array(
								TAO_ITEM_CONTENT_PROPERTY => $content
							));
							break;
					}
				}
			}
		}
		catch(Exception $e){}
		$returnValue = $item;
		
        // section 127-0-1-1-c213658:12568a3be0b:-8000:0000000000001CE9 end

        return $returnValue;
    }

    /**
     * Get the file linked to an item
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string itemUri
     * @return string
     */
    public function getAuthoringFileUriByItem($itemUri)
    {
        $returnValue = (string) '';

        // section 127-0-1-1-188be92e:12507f7441c:-8000:0000000000001B79 begin
		
		if(strlen($itemUri) > 0){
			$returnValue = TAO_ITEM_AUTHORING_BASE_URI.'/'.tao_helpers_Uri::encode($itemUri).'.xml';			
		}
        // section 127-0-1-1-188be92e:12507f7441c:-8000:0000000000001B79 end

        return (string) $returnValue;
    }

    /**
     * get the item uri linked to the given file
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string uri
     * @return string
     */
    public function getAuthoringFileItemByUri($uri)
    {
        $returnValue = (string) '';

        // section 127-0-1-1-188be92e:12507f7441c:-8000:0000000000001B7D begin
		if(strlen($uri) > 0){
			if(file_exists($uri)){
				$returnValue = tao_helpers_Uri::decode(
					str_replace(TAO_ITEM_AUTHORING_BASE_URI.'/', '',
						str_replace('.xml', '', $uri)
					)
				);
			}
		}
        // section 127-0-1-1-188be92e:12507f7441c:-8000:0000000000001B7D end

        return (string) $returnValue;
    }

    /**
     * Get the file linked to an item
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string itemUri
     * @return string
     */
    public function getAuthoringFile($itemUri)
    {
        $returnValue = (string) '';

        // section 127-0-1-1-34d7bcb9:1250bcb34b1:-8000:0000000000001B6E begin
		$uri = $this->getAuthoringFileUriByItem($itemUri);
		
		if(!file_exists($uri)){
			file_put_contents($uri, '<?xml version="1.0" encoding="utf-8" ?>');
		}
		$returnValue = $uri;
		
        // section 127-0-1-1-34d7bcb9:1250bcb34b1:-8000:0000000000001B6E end

        return (string) $returnValue;
    }

    /**
     * Service to get the temporary authoring file
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string itemUri
     * @param  boolean fallback
     * @return string
     */
    public function getTempAuthoringFile($itemUri, $fallback = false)
    {
        $returnValue = (string) '';

        // section 127-0-1-1-5249fce9:12694acf215:-8000:0000000000001E84 begin
		
		if(strlen($itemUri) > 0){
			$returnValue = TAO_ITEM_AUTHORING_BASE_URI.'/tmp_'.tao_helpers_Uri::encode($itemUri).'.xml';	
			if(!file_exists($returnValue)){
				if($fallback){	//fallback in case of error otheerwise create  the file
					$returnValue = $this->getAuthoringFile($itemUri);
				}
			}
		}
		
        // section 127-0-1-1-5249fce9:12694acf215:-8000:0000000000001E84 end

        return (string) $returnValue;
    }
	
	public function setInteractionId(taoItems_models_classes_QTI_Interaction $interaction, $newId){
		if(!is_null($interaction) && !empty($newId)){
			try{
				echo 'bbb';
				$interaction->setId($newId);
			}catch(InvalidArgumentException $e){
				var_dump($_SESSION);
				throw new Exception('the given interaction id already exists');
			}
		}
	}
		
	public function setChoiceId(taoItems_models_classes_QTI_Choice $choice, $newId){
		if(!is_null($choice) && !empty($newId)){
			try{
				$choice->setId($newId);
			}catch(InvalidArgumentException $e){
				throw new Exception('the given choice id already exists');
			}
		}
	}
	
	public function setOptions(taoItems_models_classes_QTI_Data $data, $newOptions=array()){
		
		if(!is_null($data) && !empty($newOptions)){
		
			$options = array();
		
			foreach($newOptions as $key=>$value){
				if(is_array($value)){
					if(count($value)==1 && isset($value[0])){
						$options[$key] = $value[0];
					}else if(count($value)>1){
						$options[$key] = array();
						foreach($value as $val){
							$options[$key][] = $val;
						}
					}
				}else{
					$options[$key] = $value;
				}
			}
			
			$data->setOptions($options);
		}
		
	}
	
	public function setData(taoItems_models_classes_QTI_Data $qtiObject, $data = ''){
		$qtiObject->setData($data);
	}
} /* end of class taoItems_models_classes_QtiAuthoringService */

?>