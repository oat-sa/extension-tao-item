<?php
require_once dirname(__FILE__) . '/tao_matching.php';

function pr ($msg){
	echo '<pre>';
	print_r ($msg);
	echo '</pre>';
}

/** QTI Matching API */
class QTIMatching extends TAOMatching{
}

?> 