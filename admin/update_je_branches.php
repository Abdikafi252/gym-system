<?php
require 'dbcon.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try { mysqli_query($con, "ALTER TABLE journal_entries ADD COLUMN branch_id INT(11) NOT NULL DEFAULT 0"); echo "Added branch_id to journal_entries.\n"; } catch (Exception $e) {}

// Retroactively set branch_id for journal entries
mysqli_query($con, "UPDATE journal_entries je JOIN payment_history ph ON je.source_type='payment_history' AND je.source_id = ph.id SET je.branch_id = ph.branch_id WHERE ph.branch_id IS NOT NULL");
mysqli_query($con, "UPDATE journal_entries je JOIN expenses ex ON je.source_type='expense' AND je.source_id = ex.id SET je.branch_id = ex.branch_id WHERE ex.branch_id IS NOT NULL");
mysqli_query($con, "UPDATE journal_entries je JOIN equipment eq ON je.source_type='equipment' AND je.source_id = eq.id SET je.branch_id = eq.branch_id WHERE eq.branch_id IS NOT NULL");
mysqli_query($con, "UPDATE journal_entries je JOIN owner_capital_contributions occ ON je.source_type='owner_capital' AND je.source_id = occ.id SET je.branch_id = occ.branch_id WHERE occ.branch_id IS NOT NULL");

echo "Updated existing journal entries with branch_ids.\n";
?>
