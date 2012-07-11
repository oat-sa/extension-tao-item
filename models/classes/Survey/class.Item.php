<?php
/**
 * manage the following type of item : SURVEY
 *
 * @author matteo
 */
class taoItems_models_classes_Survey_Item
{
	const NO_ERROR = 0;
	const DUPLICATE_ID_ERROR = 1;
	// STATIC PROPERTIES
	private static $singleton = false; // to avoid direct call of constructor
	private static $instances = array();
	// INSTANCE PROPERTIES
	private $resource;
	// CONSTANTS
	const VIEWS_FOLDER = '/surveyItem/';
	const QAT_TEST_PREFIX_URI = 'http://www.tao.lu/Ontologies/TAO.rdf#Surveys_test_';

	public function __construct(core_kernel_classes_Resource $resource)
	{
		// we block direct call of constructor
		if (!self::$singleton) {
			throw new Exception('Call of constructor is allowed only for singleton!');
		}
		$this->resource = $resource;
	}

	/**
	 * return the content of the item
	 * @return string (xml)
	 */
	public function getContent()
	{
		$itemService = self::getItemService();
		return $itemService->hasItemContent($this->resource) ? $itemService->getItemContent($this->resource) : NULL;
	}

	/**
	 *get the content and put the uri into
	 * @param string $uri
	 */
	public function getContentWithUri() {
		$content = $this->getContent();
		$pos = strpos($content, '</itemGroup>');
		return substr($content, 0, $pos) . '<uri>' . $this->resource->uriResource . '</uri>' . substr($content, $pos);
	}

	/**
	 * Render function to display the item
	 * @return string (xhtml)
	 *
	 * tmp actually just used for preview
	 */
	public function render()
	{
		// first get lang
		$service = tao_models_classes_ServiceFactory::get('tao_models_classes_UserService');
		$user = $service->getCurrentUser();
		$lang = (string) $user->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_USER_UILG))->getOnePropertyValue(new core_kernel_classes_Property(RDF_VALUE));

		// actually no specifique js or css so we return a flow
		// but if needed, can make a folder generate, html, js and css and put in into
		// and return just the url

//				$params = array(
//				'rtl' => $rtl, // rtl maybe in session or find by lang ???
//			'thousand' => $locale['thousand'],
//			'decimal' => $locale['decimal'],
//			'grouping' => $locale['grouping'],
//		);

		// create the compilator and get the transformation
		$compilator = new taoItems_models_classes_Survey_Compilator('preview', $lang);
		$content = $compilator->compile($this->getContent());

		$skeleton = DIR_VIEWS .  self::VIEWS_FOLDER . 'previewSkeleton.html';
		$xhtml = str_replace('{RELATIVE_PATH_TO_PREVIEW_BASE}', BASE_WWW . self::VIEWS_FOLDER, file_get_contents($skeleton));
		$xhtml = str_replace('{content}', $content, $xhtml);

		return $xhtml;
	}

	/**
	 * save item content
	 * @param string $xml
	 * @return boolean
	 */
	public function save($xml, $test)
	{
		$itemService = self::getItemService();
		$this->resource->setLabel(self::getLabelInXml($xml));
		$this->resource->editPropertyValues(new core_kernel_classes_Property(RDF_TYPE), $test->uriResource);
		return $itemService->setItemContent($this->resource, $xml);
	}

	/**
	 * check if item can be saved
	 * @param string $xml
	 * @return boolean
	 */
	public function checkBeforeSave()
	{
		// check duplicate label (cause actually label in tao == id in qat so unik)
		$items = self::getResources();
		$typeProperty = new core_kernel_classes_Property(RDF_TYPE);
		foreach ($items as $resource) {
			// if same label and different uri so throw exception else qat won't work correctly
			if($resource->getLabel() == $this->resource->getLabel() && $resource->uriResource != $this->resource->uriResource) {
				// new filter : unicity by test
				if($this->resource->getOnePropertyValue($typeProperty) == $resource->getOnePropertyValue($typeProperty)) {
					return self::DUPLICATE_ID_ERROR;
				}
			}
		}
	}

	/**
	 * delete the resource item
	 * @param bool $full
	 */
	public function delete($full = true)
	{
		return $this->resource->delete($full);
	}

	/**
	 * return the uri of the resource item
	 * @return string
	 */
	public function getUri()
	{
		return $this->resource->uriResource;
	}

	/**
	 * return the timesqtamp of the uri (after #) for unique folder creation
	 * @return string
	 */
	public function getUriTimestamp()
	{
		return substr($this->getUri(), strrpos($this->getUri(), "#") + 1);
	}

	/*	 * *******************
	 * * STATICS METHODS ***
	 * ******************* */

	/**
	 * item Servcice factory getter
	 * @return taoItems_models_classes_ItemsService
	 */
	public static function getItemService()
	{
		return tao_models_classes_ServiceFactory::get('taoItems_models_classes_ItemsService');
	}

	/**
	 * Singleton
	 *
	 * @param core_kernel_classes_Resource $resource
	 * @return taoItems_models_classes_Survey_Item
	 */
	public static function singleton(core_kernel_classes_Resource $resource)
	{
		// if no exisiting instance
		if (!array_key_exists($resource->uriResource, self::$instances)) {
			// allow creation
			self::$singleton = true;
			// create
			self::$instances[$resource->uriResource] = new self($resource);
			//block creation again
			self::$singleton = false;
		}
		//return the item
		return self::$instances[$resource->uriResource];
	}

	/**
	 * Static call for rendering an item
	 *
	 * @param core_kernel_classes_Resource $resource
	 * @return string
	 */
	public static function renderItem(core_kernel_classes_Resource $resource)
	{
		// get the item
		$item = self::singleton($resource);
		// call the item render function
		return $item->render();
	}

	/**
	 * save an item getting his uri in xml or create a new one noexistent
	 * @param string $xml
	 * @param array $params
	 * @return mixed
	 */
	public static function saveItem($xml)
	{
		$parsed = self::parseItemXml($xml);
		if ($parsed instanceof core_kernel_classes_Resource) {
			// get the item
			$item = self::singleton($parsed);
			// call the item render function
		} else {
			// create a new item with given params
			$item = self::singleton(self::createNewItem($parsed));
		}
		$test = self::getTest($xml);
		// remove uri
		$xml = self::removeUnwanted($xml);
		// check if save is ok
		$check = $item->checkBeforeSave();
		switch ($check) {
			case self::NO_ERROR:
				$returnValue = array(
						'uri' => $item->resource->uriResource,
						'savedContent' => $item->save($xml, $test)
				);
				break;
			// error message need to be filled in QAT (in the translation file)
			default:
				// return number for display error mesage
				$returnValue = array(
					'errorNumber' => $check,
					'errorInfo'	=>	array(
						'id' => $item->resource->getLabel()
					)
				);
				// delete resource created
				$item->delete();
				break;
		}
		return $returnValue;
	}

	/**
	 * remove unwanted content from xml
	 * @param string $xml
	 * @return string
	 */
	public static function removeUnwanted($xml)
	{
		// temp do it on string until found why removeChild render an bugged xml
		$pos = strpos($xml, '<uri>');
		if($pos !== false) {
			$xml = substr($xml, 0, $pos) . substr($xml, strpos($xml, '</uri>', $pos) + 6);
		}
		$pos = strpos($xml, '<test>');
		if($pos !== false) {
			$xml = substr($xml, 0, $pos) . substr($xml, strpos($xml, '</test>', $pos) + 7);
		}
		return $xml;
//		$dom = new DomDocument();
//		$dom->loadXML($xml);
////		$uri = $dom->getElementsByTagName('uri');
//		$xpath = new DOMXPath($dom);
//		$uri = $xpath->query('//uri');
//		// no uri in the content (used in case of bug to clean content)
//		if ($uri->length) {
//			foreach ($uri as $u) {
//				$dom->removeChild($u);
//			}
//		}
//		return $dom->saveXML($dom->getElementsByTagName('itemGroup')->item(0));
	}

	/**
	 * parse xml to get resourceUri or just label if new item
	 * @param string $xml
	 * @return mixed
	 */
	public static function parseItemXml($xml)
	{
		$uri = self::getXPath($xml, '//uri');
		// if resource existing
		if ($uri->length) {
			return new core_kernel_classes_Resource($uri->item(0)->nodeValue);
		} else {
			// else get his label to create it
			return self::getLabelInXml($xml);
		}
	}

	/**
	 * parse xml to get the test
	 * @param string $xml
	 * @return string
	 */
	public static function getTest($xml)
	{
		$test = self::getXPath($xml, '//test');
		if (!$test->length) {
			throw new Exception(__('No test in xml content'));
		} else {
			$cls = new core_kernel_classes_Class(self::QAT_TEST_PREFIX_URI . $test->item(0)->nodeValue);
			return $cls->exists() ? $cls: false;
		}
	}

	/**
	 * parse xml to get resourceUri or just label if new item
	 * @param string $xml
	 * @return mixed
	 */
	public static function getLabelInXml($xml)
	{
		$label = self::getXPath($xml, '//itemGroup/@id');
		if ($label->length) {
			return $label->item(0)->nodeValue;
		} else {
			// if no label, ERROR
			throw new Exception('Label not found for the item.');
		}
	}

	/**
	 * create an instance of a nex item survey
	 * @param type $label
	 */
	public static function createNewItem($label)
	{
		$cls = new core_kernel_classes_Class(TAO_ITEM_CLASS);
//		$label = self::getItemService()->createUniqueLabel($cls);
		$instance = self::getItemService()->createInstance($cls, $label);
		$instance->setPropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_PROPERTY), TAO_ITEM_MODEL_SURVEY);
		return $instance;
	}

	/**
	 * get the liste of itemSurvey
	 * @param array $opt
	 */
	public static function getResources($opt = array())
	{
		$cls = new core_kernel_classes_Class(TAO_ITEM_CLASS);
		$lst = $cls->searchInstances(array(TAO_ITEM_MODEL_PROPERTY => TAO_ITEM_MODEL_SURVEY));//, array('like' => false));
		return $lst;
	}

	/**
	 * 	Remove an item using his uri
	 * @param string $uri
	 * @return boolean
	 */
	public static function deleteItem($uri)
	{
		if (empty($uri) || !is_string($uri)) {
			throw new Exception('The given uri is empty or is not a string : ' . var_export($uri, true));
		}
		$item = self::singleton(new core_kernel_classes_Resource($uri));
		return $item->delete();
	}

	/*	 * ***********************
	 * usefull function maybe put in other place
	 */

	/**
	 * get the xpath for a given string xml
	 *
	 * @param string $xml
	 * @return Xpath
	 */
	public static function getXPath($xml, $query = false)
	{
		$dom = new DomDocument();
		$dom->loadXML($xml);
		$xpath = new DOMXPath($dom);
		if (!$query) {
			return $xpath;
		} else {
			return $xpath->query($query);
		}
	}

	/**
	 * Return a DOMElement in string
	 *
	 * @param DOMElement $elt
	 * @return string
	 */
	public static function DOMElementToString($elt)
	{
		$newdoc = new DOMDocument();
		$cloned = $elt->cloneNode(TRUE);
		$newdoc->appendChild($newdoc->importNode($cloned, TRUE));
		return $newdoc->saveXML();
	}
}
?>
