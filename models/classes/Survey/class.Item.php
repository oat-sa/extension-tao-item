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
	protected static $singleton = false; // to avoid direct call of constructor
	protected static $instances = array();
	// INSTANCE PROPERTIES
	protected $resource;
	// CONSTANTS
	const VIEWS_FOLDER = '/surveyItem/';

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
	 * Render function to display an item by passing xml
	 * @return string (xhtml)
	 *
	 */
	public static function preRender($xml)
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
		return $compilator->compile($xml);
	}

	/**
	 * Render function to display the item
	 * @return string (xhtml)
	 *
	 */
	public function render()
	{
		$content = self::preRender($this->getContent());
		$skeleton = DIR_VIEWS .  self::VIEWS_FOLDER . 'previewSkeleton.html';
		$xhtml = str_replace('{RELATIVE_PATH_TO_PREVIEW_BASE}', BASE_WWW . self::VIEWS_FOLDER, file_get_contents($skeleton));
		$xhtml = str_replace('{content}', $content, $xhtml);

		return $xhtml;
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
	 * Static call for rendering an item
	 *
	 * @param core_kernel_classes_Resource $resource
	 * @return string
	 */
	public static function renderItemMemory($xml)
	{
		// get the item
		$item = self::singleton($resource);
		// call the item render function
		return $item->render();
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
