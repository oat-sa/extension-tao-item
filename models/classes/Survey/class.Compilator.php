<?php

// When qat integrated to svn
class taoItems_models_classes_Survey_Compilator
{
	private $xsl;
	private $lang;
	private $key;
	private $params;
	// STATIC
	private static $styleSheets = array();

	public function __construct($xsl, $lang, $params = array())
	{
		// to have recurrent path in memory
		switch($xsl) {
			case 'preview':
				$xsl = DIR_VIEWS . '/xsl/index.xsl';
				break;
			default:
				break;
		}
		$this->xsl    = $xsl;
		$this->lang   = $lang;
		$this->params = $params;
		$this->setXslKey();
		$this->preparedXsl = $this->getXsl();
	}

	/**
	 *    return the xsl key used for lazy loading
	 */
	private function setXslKey()
	{
		$key = $this->xsl . $this->lang;
		if(sizeof($this->params)) {
			$p = array_keys($this->params);
			sort($p);
			$key = $key + implode('', $p);
		}
		$this->key = 'QAT_XSL_' . md5($key);
	}

	/**
	 *     kind of lazy loading for xsl stylesheet preparation on memory first
	 * @return string
	 */
	private function getXsl()
	{
		if(!array_key_exists($this->key, self::$styleSheets)) {
			self::$styleSheets[$this->key] = $this->getPreparedXsl();
		}
		return self::$styleSheets[$this->key];
	}

	/**
	 *     kind of lazy loading for xsl stylesheet preparation on filesystem
	 * @return string
	 */
	private function getPreparedXsl()
	{
		if(USE_CACHED_XSL) {
			try {
				$content = tao_models_classes_cache_FileCache::singleton()->get($this->key);
				self::$styleSheets[$this->key] = $content;
			} catch (Exception $e) {
				$xsl                           = $this->generateXsl();
				self::$styleSheets[$this->key] = $xsl;
				tao_models_classes_cache_FileCache::singleton()->put($xsl, $this->key);
			}
		} else {
			$xsl                           = $this->generateXsl();
			self::$styleSheets[$this->key] = $xsl;
		}
		return self::$styleSheets[$this->key];
	}

	/**
	 *     precompilation of the stylesheet
	 * @return string
	 */
	private function generateXsl()
	{
		if(!is_file($this->xsl)) {
			$xsl = $this->xsl;
			common_Logger::w('In flow mode, translations and image path changing can\'t be applied to include tag be careful in using it!');
		} else {
			$xsl = $this->getFullXsl($this->xsl);
		}
		$xsl                           = $this->translateXsl($xsl);
		$xsl                           = taoItems_helpers_Xslt::prepareXsl($xsl, $this->params)->saveXML();
		self::$styleSheets[$this->key] = $xsl;
		return self::$styleSheets[$this->key];
	}

	/**
	 * translate node textToTranslate to node text
	 * @param DOMDocument $xsl
	 * @return DOMDocument
	 */
	private function translateXsl($xsl)
	{
		//@TODO translate in an passed in param language and not in session language
		// first resolv includes and imports
		$xsl_dom = new DOMDocument();
		$xsl_dom->loadXML($xsl);
		// this have to be done after the include resolving
		$translations = $xsl_dom->getElementsByTagNameNS(taoItems_helpers_Xslt::XSLT_NS, "textToTranslate");
		for($i = 0; $i < $translations->length; '') {
			$oldnode = $translations->item(0);
			$newnode = $xsl_dom->createElementNS(taoItems_helpers_Xslt::XSLT_NS, "xsl:text", __($oldnode->nodeValue));
			$oldnode->parentNode->replaceChild($newnode, $oldnode);
		}
		return $xsl_dom->saveXML();
	}

	/**
	 *    resolv all includes in the xsl
	 */
	private function getFullXsl($file)
	{
		$xsl = file_get_contents($file);
		$dir = dirname($file);
		return $this->resolvIncludes($xsl, $dir);
	}

	private function resolvIncludes($xsl, $dir)
	{
		preg_match_all('/<xsl\:include.*\/>/', $xsl, $includes);
		$includes = array_shift($includes);
//		var_dump($includes);	die;
		foreach($includes as $include) {
			$ref     = substr($include, strpos($include, 'href=') + 5); //@TODO check if space is allowed faor attribut at left and right of =
			$quote   = substr($ref, 0, 1);
			$ref     = substr($ref, 1);
			$ref     = substr($ref, 0, strpos($ref, $quote));
			$path    = $dir . '/' . $ref;
			$content = $this->getStylesheetContent($path);

			$tmpDir = dirname($path);
//			var_dump($tmpDir);die;
			$xsl = str_replace($include, $this->resolvIncludes($content, $tmpDir), $xsl);
		}
		return $xsl;
	}

	/**
	 * @param DOMDocument $xsl_dom
	 * @param mixed       $clear
	 * @return DOMElement
	 * @throws Exception
	 */
	private function getStylesheetContent($path, $clear = array('xsl\:output'))
	{
		if(!is_file($path)) {
			throw new Exception(__('Missing included stylesheet : ' . $path));
		}
		$content = file_get_contents($path);
		// remove all from start until stylesheet tag included
		$content = preg_replace('/.*[^<xsl]*<xsl\:(stylesheet|transform)[^>]*>/', '', $content);
		// remove stylesheet end tag
		$content = preg_replace('/<\/xsl\:(stylesheet|transform)>/', '', $content);
//		var_dump($content);die;
		if($clear) {
			if(!is_array($clear)) {
				$clear = array($clear);
			}
			foreach($clear as $tag) {
				if(preg_match('/<\/' . $tag . '>/', $content)) {
					// remove tag : <tag>xxxx</tag>
					$content = preg_replace('/<' . $tag . '.*[^' . $tag . '>]' . $tag . '>/', '', $content);
				} else {
					// remove tag <tag*/>
					$content = preg_replace('/<' . $tag . '[^>]*>/', '', $content);
				}
			}
		}
		return trim($content);
	}

	public function compile($xml)
	{
		return taoItems_helpers_Xslt::transform($xml, $this->preparedXsl, $this->params);
	}

	//@TODO rtl things (revert css, revert image, etc ...) to do
}

?>
