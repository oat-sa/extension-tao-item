<?php
/**
 * Matching API interface.
 * Provides functions to manage the communication with a the TAO matching engine from an XHTML item.
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package taoItems
 *
 */


function pr ($msg){
	echo '<pre>';
	print_r ($msg);
	echo '</pre>';
}

/**
 * The tao matching object
 */
$taoMatching = null;

/////////////////////
// TAO Matching //
///////////////////

/**
 * Init the TAO Matching Engine
 * @return {bool}
 */
function matching_init () {
	global $taoMatching;
	$taoMatching = new taoItems_models_classes_Matching_Matching ();
}

/**
 * @return {bool}
 */
function matching_evaluate () {
	global $taoMatching;
	return $taoMatching->evaluate ();
}

/**
 * @return {bool}
 */
function matching_getOutcomes () {
	global $taoMatching;
	return $taoMatching->getJSonOutcomes ();
}

/**
 * @return {bool}
 */
function matching_setCorrects ($data) {
	global $taoMatching;
	return $taoMatching->setCorrects (json_decode($data));
}

/**
 * @return {bool}
 */
function matching_setMaps ($data) {
	global $taoMatching;
	return $taoMatching->setMaps (json_decode($data));
}

/**
 * @return {bool}
 */
function matching_setOutcomes ($data) {
	global $taoMatching;
	return $taoMatching->setOutcomes (json_decode($data));
}

/**
 * @return {bool}
 */
function matching_setResponses ($data) {
	global $taoMatching;
	return $taoMatching->setResponses (json_decode($data));
}


/**
 * @return {bool}
 */
function matching_setRule ($rule) {
	global $taoMatching;
	$taoMatching->setRule ($rule);
}

// temporary testing fufnction
function matching_getRule () {
	global $taoMatching;
	return $taoMatching->getRule ();
}

?>
