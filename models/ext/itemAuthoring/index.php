<?php
session_start();
$_SESSION['xml'] = $_POST['xml'];
$_SESSION['instance'] = $_POST['instance'];
header("Location: /taoItems/Items/saveItemContent");
?>
