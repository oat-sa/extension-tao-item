<?php
require('../../../includes/common.php');



require('TAOAuthoringGUI.php');
$TAOAuthoringGUI = new TAOAuthoringGUI($_GET['xml'], $_GET['instance']);
echo $TAOAuthoringGUI->getOutput();
?>