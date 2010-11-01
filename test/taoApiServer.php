<?php

switch($_GET['action']){

	case 'push':
		(isset($_POST['token'])) ? ($_POST['token'] == '7114e56cb3b9423314a425500afb41fc56183000') ? $saved = true : $saved = false : $saved = false;
		echo json_encode(array('saved' => $saved));
		break;
		
	case 'traceEvents':
		(isset($_POST['token'])) ? ($_POST['token'] == '7114e56cb3b9423314a425500afb41fc56183000') ? $saved = true : $saved = false : $saved = false;
		if($saved){
			if($saved = $saved && isset($_POST['events'])){
				$saved = $saved && count($_POST['events']) > 0;
			}
		}
		echo json_encode(array('saved' => $saved));
		break;
}
?>