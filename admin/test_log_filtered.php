<?php
$lines = file('c:\xampp\apache\logs\error.log');
$last = array_slice($lines, -300);
foreach($last as $l) { 
    if(strpos($l, 'equipment.php') !== false || strpos($l, 'expenses.php') !== false || strpos($l, 'attendance.php') !== false || strpos($l, 'accounting') !== false) {
        echo $l; 
    }
}
?>
