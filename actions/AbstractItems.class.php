<?php
abstract class AbstractItems extends Module {
	
	/**
	 * @var tao_models_classes_Service $service
	 */
	protected $service;

	public function __construct(){
		//the service is initialized by default
		$this->service = tao_models_classes_ServiceFactory::get('Items');
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
				$this->forward('ItemsEditor', 'index');
			}
		}
		
		$this->setData('formTitle', 'Edit Item');
		$this->setData('myForm', $myForm->render());
		$this->setView('form.tpl');;
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
	
	
	/*
	 * conveniance methods
	 */
	
	/**
	 * get the instancee of the current item regarding the 'uri' and 'classUri' request parameters
	 * @return core_kernel_classes_Resource the item instance
	 */
	protected function getCurrentItem(){
		$uri = tao_helpers_Uri::decode($this->getRequestParameter('uri'));
		$classUri = tao_helpers_Uri::decode($this->getRequestParameter('classUri'));
		if(is_null($uri) || empty($uri) || is_null($classUri) || empty($classUri)){
			throw new Exception("No valid uri found");
		}
		
		$item = $this->service->getItem($uri, new core_kernel_classes_Class($classUri));
		if(is_null($item)){
			throw new Exception("No item found for the uri {$uri}");
		}
		
		return $item;
	}
	
	/**
	 * get the current item class regarding the classUri' request parameter
	 * @return core_kernel_classes_Class the item class
	 */
	protected function getCurrentClass(){
		$classUri = tao_helpers_Uri::decode($this->getRequestParameter('classUri'));
		if(is_null($classUri) || empty($classUri)){
			throw new Exception("No valid uri found");
		}
		
		return  new core_kernel_classes_Class($classUri);
	}
}
?>