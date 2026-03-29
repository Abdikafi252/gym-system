<?php
// Script: update_owner_capital_cash_source.php
// Purpose: Update transactions_log Cash Debit source to 'equipment' for owner capital entries

include 'dbcon.php';

// Find all owner_capital entries where account_code = 1000 (Cash) and debit > 0
$sql = "SELECT id, txn_date, source_table, source_id, account_code, debit, credit FROM transactions_log WHERE source_table='owner_capital' AND account_code='1000' AND debit > 0";
$res = mysqli_query($con, $sql);

$count = 0;
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $id = (int)$row['id'];
        // Update source_table to 'equipment'
        $update = mysqli_query($con, "UPDATE transactions_log SET source_table='equipment' WHERE id=$id");
        if ($update) {
            $count++;
        }
    }
}

echo "Updated $count owner capital cash debit entries to equipment source.";
