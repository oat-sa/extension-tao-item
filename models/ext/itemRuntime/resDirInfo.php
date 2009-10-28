<?php

$counter=0;
$resultat="";
$tot_size=0;
$destinationDir = $_GET["path"] . "/resources/";
$d = dir($destinationDir);
while($entry=$d->read()) {
    if (($entry!=".")&&($entry!="..")){
      $full_entry = $destinationDir . $entry;
      $entry_size = filesize($full_entry);
      $tot_size += $entry_size;
//      $resultat = "f" . $counter . "=" . rawurlencode($entry) . "#" . $entry_size . "&" . $resultat;
      $resultat = "#" . $counter . "=" . rawurlencode($entry) . "&" . $resultat;
      $counter++;
    }
}
$resultat = $resultat . "TOTALSIZE=" . $tot_size;
echo "$resultat";

$d->close();

?>
