<?php
include 'dbcon.php';

echo "Starting transactions_log branch_id cleanup...\n";

$q = "UPDATE transactions_log tl
      JOIN journal_entries je ON tl.journal_entry_id = je.id
      SET tl.branch_id = je.branch_id
      WHERE tl.branch_id = 0 AND je.branch_id != 0";

if (mysqli_query($con, $q)) {
    echo "Fixed " . mysqli_affected_rows($con) . " transactions_log entries with missing branch_id.\n";
} else {
    echo "Error: " . mysqli_error($con) . "\n";
}

echo "Cleanup complete.\n";
?>
