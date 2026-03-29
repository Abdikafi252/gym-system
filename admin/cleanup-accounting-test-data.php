// --- ACCOUNTING CLEANUP: Remove orphaned journal_entries and transactions_log ---
include_once __DIR__ . '/includes/accounting_engine.php';

// Remove journal_entries for payment_history rows that no longer exist
$orphans = mysqli_query($con, "SELECT id, source_id FROM journal_entries WHERE source_type='payment_history'");
if ($orphans) {
    while ($row = mysqli_fetch_assoc($orphans)) {
        $ph_id = (int)$row['source_id'];
        $je_id = (int)$row['id'];
        $exists = mysqli_query($con, "SELECT 1 FROM payment_history WHERE id='$ph_id' LIMIT 1");
        if (!$exists || mysqli_num_rows($exists) == 0) {
            // Delete all accounting records for this orphan
            acc_delete_source_postings($con, 'payment_history', $ph_id);
            echo "Deleted orphaned accounting for payment_history id=$ph_id (journal_entry id=$je_id)<br>\n";
        }
    }
}

// Remove journal_entries for owner_capital_contributions rows that no longer exist
$orphans = mysqli_query($con, "SELECT id, source_id FROM journal_entries WHERE source_type='owner_capital'");
if ($orphans) {
    while ($row = mysqli_fetch_assoc($orphans)) {
        $oc_id = (int)$row['source_id'];
        $je_id = (int)$row['id'];
        $exists = mysqli_query($con, "SELECT 1 FROM owner_capital_contributions WHERE id='$oc_id' LIMIT 1");
        if (!$exists || mysqli_num_rows($exists) == 0) {
            acc_delete_source_postings($con, 'owner_capital', $oc_id);
            echo "Deleted orphaned accounting for owner_capital id=$oc_id (journal_entry id=$je_id)<br>\n";
        }
    }
}
// --- END ACCOUNTING CLEANUP ---
<?php
// Run this script ONCE to clean test and invalid accounting data from your database.
// Make sure you have a backup before running!

include '../dbcon.php';

$queries = [
    // Remove test rows from owner_capital_contributions
    "DELETE FROM owner_capital_contributions WHERE contribution_date = '2026-03-17' AND amount = 1.00 AND notes = '03/17/2027'",
    // Remove test journal entries
    "DELETE FROM journal_entries WHERE entry_date = '2026-03-17' AND memo = '03/17/2027'",
    // Remove test journal lines
    "DELETE FROM journal_lines WHERE journal_entry_id IN (SELECT id FROM journal_entries WHERE entry_date = '2026-03-17' AND memo = '03/17/2027')",
    // Remove test transactions_log rows
    "DELETE FROM transactions_log WHERE txn_date = '2026-03-17' AND debit = 1.00 AND credit = 1.00 AND description = '03/17/2027'",
    // Remove test equipment rows
    "DELETE FROM equipment WHERE date = '2026-03-17' AND amount = 1.00",
    // Remove test payment_history rows
    "DELETE FROM payment_history WHERE paid_date = '2026-03-17' AND paid_amount = 1.00"
];

$success = true;
foreach ($queries as $q) {
    if (!mysqli_query($con, $q)) {
        echo "Error: " . mysqli_error($con) . "<br>";
        $success = false;
    }
}

if ($success) {
    echo "Test and invalid accounting data cleaned successfully.";
} else {
    echo "Some errors occurred during cleanup. Check above messages.";
}
?>
