<?php
$lines = file('c:\xampp\apache\logs\error.log');
$last20 = array_slice($lines, -50);
foreach($last20 as $l) { echo $l; }
?>
