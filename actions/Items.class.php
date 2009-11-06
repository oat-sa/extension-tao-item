<?php
require_once('tao/actions/CommonModule.class.php');

/**
 * Items controller
 */
class Items extends CommonModule{

	public function __construct(){
		//the service is initialized by default
		$this->service = tao_models_classes_ServiceFactory::get('Items');
		$this->defaultData();
	}

	public function index(){
		
		if($this->getData('reload') == true){
			unset($_SESSION[SESSION_NAMESPACE]['uri']);
			unset($_SESSION[SESSION_NAMESPACE]['classUri']);
		}
		
		$context = Context::getInstance();
		$this->setData('content', "module: ". get_class($this) ." , action: " . $context->getActionName());
		$this->setView('index.tpl');
	}
	
	/**
	 * Render json data to populate the subject tree 
	 * 'modelType' must be in request parameter
	 */
	public function getItems(){
		
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		//render directly the json
		$highlightUri = '';
		if($this->hasSessionAttribute("showNodeUri")){
			$highlightUri = $this->getSessionAttribute("showNodeUri");
			unset($_SESSION[SESSION_NAMESPACE]["showNodeUri"]);
		} 
		echo json_encode( $this->service->toTree( $this->service->getItemClass() , true, true, $highlightUri) );
	}
	
	
	/**
	 * edit an item instance
	 */
	public function editItem(){
		$itemClass = $this->getCurrentClass();
		$item = $this->getCurrentItem();
		
		$myForm = tao_helpers_form_GenerisFormFactory::instanceEditor($itemClass, $item);
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				
				$this->service->bindProperties($item, $myForm->getValues());
				
				$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($item->uriResource));
				$this->setData('message', 'item saved');
				$this->setData('reload', true);
				$this->forward('Items', 'index');
			}
		}
		$item = $this->getCurrentItem();
		
		$this->setData('formTitle', 'Edit Item');
		$this->setData('myForm', $myForm->render());
		$this->setView('form_preview.tpl');
	}
	
	/**
	 * Edit a class
	 */
	public function editItemClass(){
		$myForm = tao_helpers_form_GenerisFormFactory::classEditor($this->getCurrentClass(), $this->service->getItemClass());
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				
				$classValues = array();
				$propertyValues = array();
				foreach($myForm->getValues() as $key => $value){
					if(preg_match("/^class_/", $key)){
						$classKey =  tao_helpers_Uri::decode(str_replace('class_', '', $key));
						$classValues[$classKey] =  tao_helpers_Uri::decode($value);
					}
					if(preg_match("/^property_/", $key)){
						if(isset($_POST[$key])){
							$key = str_replace('property_', '', $key);
							$propNum = substr($key, 0, 1 );
							$propKey = tao_helpers_Uri::decode(str_replace($propNum.'_', '', $key));
							$propertyValues[$propNum][$propKey] = tao_helpers_Uri::decode($value);
						}
						else{
							$key = str_replace('property_', '', $key);
							$propNum = substr($key, 0, 1 );
							if(!isset($propertyValues[$propNum])){
								$propertyValues[$propNum] = array();
							}
						}
					}
				}
				
				$clazz = $this->service->bindProperties($this->getCurrentClass(), $classValues);
				foreach($propertyValues as $propNum => $properties){
					if(isset($_POST['propertyUri'.$propNum]) && count($properties) == 0){
						
						//delete property mode
						foreach($clazz->getProperties() as $classProperty){
							if($classProperty->uriResource == tao_helpers_Uri::decode($_POST['propertyUri'.$propNum])){
								
								$classProperty->delete();
								break;
							}
						}
					}
					else{
						$this->service->bindProperties(new core_kernel_classes_Resource(tao_helpers_Uri::decode($_POST['propertyUri'.$propNum])), $properties);
					}
				}
				if($clazz instanceof core_kernel_classes_Resource){
					$this->setSessionAttribute("showNodeUri", tao_helpers_Uri::encode($clazz->uriResource));
				}
				$this->setData('message', 'class saved');
				$this->setData('reload', true);
				$this->forward('Items', 'index');
			}
		}
		
		$this->setData('formTitle', 'Edit a class');
		$this->setData('myForm', $myForm->render());
		$this->setView('form.tpl');
	}
	
	/**
	 * Add an item instance
	 */
	public function addItem(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$itemClass = $this->getCurrentClass();
		$instance = $this->service->createInstance($itemClass);
		if($instance instanceof core_kernel_classes_Resource){
			echo json_encode(array(
				'label'	=> $instance->getLabel(),
				'uri' 	=> tao_helpers_Uri::encode($instance->uriResource)
			));
		}
	}
	
	/**
	 * Sub Class
	 */
	public function addItemClass(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$subClass = $this->service->createSubClass($this->getCurrentClass());
		if($subClass instanceof core_kernel_classes_Class){
			echo json_encode(array(
				'label'	=> $subClass->getLabel(),
				'uri' 	=> tao_helpers_Uri::encode($subClass->uriResource)
			));
		}
	}
	
	
	/**
	 * delete an item or an item class
	 * called via ajax
	 */
	public function delete(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		$deleted = false;
		if($this->getRequestParameter('uri')){
			$deleted = $this->service->deleteItem($this->getCurrentItem());
		}
		else{
			$deleted = $this->service->deleteItemSubClazz($this->getCurrentClass());
		}
		echo json_encode(array('deleted'	=> $deleted));
	}
	
	/**
	 * duplicate an item instance by property copy
	 */
	public function cloneItem(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		$item = $this->getCurrentItem();
		$clazz = $this->getCurrentClass();
		
		$clone = $this->service->createInstance($clazz);
		if(!is_null($clone)){
			
			foreach($clazz->getProperties() as $property){
				foreach($item->getPropertyValues($property) as $propertyValue){
					$clone->setPropertyValue($property, $propertyValue);
				}
			}
			$clone->setLabel($item->getLabel()."'");
			echo json_encode(array(
				'label'	=> $clone->getLabel(),
				'uri' 	=> tao_helpers_Uri::encode($clone->uriResource)
			));
		}
	}
	
	public function getItemHistoryGrid(){
		$this->setData('grid', false);
		$item = $this->getCurrentItem();
		if(!is_null($item)){
			$this->setData('grid', true);
			$this->setData('dataUrl', _url('getItemHistoryData', 'Items'));
		}
		
		$this->setView('grid.tpl');
	}
	
	public function getItemHistoryData(){
		$data = array(
			'page'		=> '1',
			'total'		=> '1',
			'records' 	=> '3',
			'rows'		=> array(
				array(
					'id'	=> 1,
					'cell'	=> array("1","2007-10-06","Client 1", "test")
				),
				array(
					'id'	=> 2,
					'cell'	=> array("2","2007-10-06","Client 2", "test comments")
				),
				array(
					'id'	=> 3,
					'cell'	=> array("3","2007-10-04","Client 3", "test comments comments")
				),
			)
		);	
		echo json_encode($data);
	}
	
	/*
	 * TODO
	 */
	
	public function import(){
		$context = Context::getInstance();
		$this->setData('content', "module: ". get_class($this) ." , action: " . $context->getActionName());
		$this->setView('index.tpl');
	}
	
	public function export(){
		$context = Context::getInstance();
		$this->setData('content', "module: ". get_class($this) ." , action: " . $context->getActionName());
		$this->setView('index.tpl');
	}
	
	public function authoring(){
		$this->setData('content', "ITEM AUTHORING TOOL");
		$this->setView('index.tpl');
	}
	
	/*
	 * conveniance methods
	 */
	
	/**
	 * get the instancee of the current item regarding the 'uri' and 'classUri' request parameters
	 * @return core_kernel_classes_Resource the item instance
	 */
	protected function getCurrentItem(){
		$uri = tao_helpers_Uri::decode($this->getRequestParameter('uri'));
		if(is_null($uri) || empty($uri)){
			throw new Exception("No valid uri found");
		}
		
		$item = $this->service->getItem($uri, $this->getCurrentClass());
		if(is_null($item)){
			throw new Exception("No item found for the uri {$uri}");
		}
		
		return $item;
	}
	
}
?>