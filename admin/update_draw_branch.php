<?php
require 'dbcon.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try { mysqli_query($con, "ALTER TABLE owner_draw ADD COLUMN branch_id INT(11) NOT NULL DEFAULT 0"); echo "Added branch_id to owner_draw.\n"; } catch (Exception $e) {}

echo "Done.\n";
?>
