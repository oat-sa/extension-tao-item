<?php
/**
 * This helper class aims at providing utility methods to deal
 * with XHTML documents.
 * 
 * @author Jerome Bogaerts <jerome@taotesting.com>
 *
 */
class taoItems_helpers_Xhtml
{
	/**
	 * Determines if a given DOMDocument contains <script> elements with
	 * a 'src' attribute value that matches a given PCRE pattern.
	 * 
	 * The pattern will be applied only on the base name found in the src attribute.
	 * 
	 * @param DOMDocument $dom A DOMDocument.
	 * @param string $pattern A PCRE pattern. 
	 * @return boolean Returns true if the requested script element(s) could be found, othewise false.
	 */
	public static function hasScriptElements(DOMDocument $dom, $pattern){
		return count(self::getScriptElements($dom, $pattern)) > 0;
	}
	
	/**
	 * Retrieve <script> elements in the given DOMDocument where the src attribute value
	 * matches a PCRE pattern.
	 * 
	 * The pattern will be applied only on the base name found in the src attribute.
	 * 
	 * @param DOMDocument $dom A DOMDocument
	 * @param string $pattern A PCRE pattern. 
	 * @return array An array of DOMNode. 
	 */
	public static function getScriptElements(DOMDocument $dom, $pattern){
		$xpath = new DOMXPath($dom);
		$elements = array();
		$scan = $xpath->query('/html/head/script');
		
		for ($i = 0; $i < $scan->length; $i++){
			$e = $scan->item($i);
			
			if ($e->hasAttribute('src')){
				$src = $e->getAttribute('src');
				$pathinfo = pathinfo($src);
				
				if (!empty($pathinfo['basename'])){
					$filename = $pathinfo['basename'];
					
					if (preg_match($pattern, $filename) === 1){
						$elements[] = $e;
					}
				}
			}
		}
		
		return $elements;
	}
	
	/**
	 * Remove <script> elements in a DOMDocument that have an src attribute value
	 * that matches a given PCRE pattern.
	 * 
	 * The pattern will be applied only on the base name found in the src attribute.
	 * 
	 * @param DOMDocument $dom A DOMDocument.
	 * @param string $pattern A PCRE pattern.
	 * @return int The number of found elements.
	 */
	public static function removeScriptElements(DOMDocument $dom, $pattern){
		$removed = 0;
		
		foreach (self::getScriptElements($dom, $pattern) as $e){
			if (!empty($e->parentNode)){
				$e->parentNode->removeChild($e);
				$removed++;
			}
		}
		
		return $removed;
	}
	
	/**
	 * Add a <script> element in the <head> element of a DOMDocument.
	 * 
	 * @param DOMDocument $dom A DOMDocument.
	 * @param string $src The value of the 'src' attribute.
	 * @param string $append (optional) Append or prepend the node to the <head> element.
	 * @param string $type (optional) The value of the 'type' attribute.
	 */
	public static function addScriptElement(DOMDocument $dom, $src, $append = true, $type = 'text/javascript'){
		$xpath = new DOMXPath($dom);
		$scan = $xpath->query('/html/head');
		
		$newNode = $dom->createElement('script');
		$newNode->setAttribute('type', $type);
		$newNode->setAttribute('src', $src);
		
		for ($i = 0; $i < $scan->length; $i++){
			$head = $scan->item($i);
			
			if ($append == true || $xpath->query('//script', $head)->length == 0){
				$head->appendChild($newNode);
			}
			else{
				$refNode = $xpath->query('//script', $head)->item(0);
				$head->insertBefore($newNode, $refNode);
			}
			
			break;
		}
	}
}
?>