<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('location:../../index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['staff_id'])) {
    include '../dbcon.php';
    require_once '../includes/db_helper.php';
    require_once '../includes/accounting_engine.php';

    $staff_id = (int)$_POST['staff_id'];
    $amount = (float)$_POST['amount'];
    $payment_date = date('Y-m-d');
    $admin_user = $_SESSION['username'] ?? 'Admin';

    // 1. Fetch staff details for the memo
    $staff = safe_fetch_assoc($con, "SELECT fullname, branch_id FROM staffs WHERE user_id=?", "i", [$staff_id]);
    if (!$staff) {
        header('location:../payroll.php?error=Staff not found');
        exit;
    }

    $memo = "Salary Payment - " . $staff['fullname'] . " (" . date('M Y') . ")";

    // 2. Create Journal Entry: Dr Salaries Payable (2100), Cr Cash (1000)
    $lines = [
        ['account_code' => '2100', 'debit' => $amount, 'credit' => 0, 'line_memo' => $memo],
        ['account_code' => '1000', 'debit' => 0, 'credit' => $amount, 'line_memo' => $memo]
    ];

    $journal_entry_id = acc_create_entry($con, $payment_date, $memo, 'payroll', $staff_id, $lines, 0, (int)($staff['branch_id'] ?? 0), 0, $admin_user);

    if ($journal_entry_id) {
        // 3. Clear Debt Tracker or Update it (In full accrual, we just need to make sure the accounting matches)
        // Since we are now using Account 2100 for balance, we don't necessarily need to DELETE from payroll_accruals 
        // if we want to keep history there, but it's cleaner to keep it for "Pending" items only.
        // However, if we leave them, the balance query (SUM Credit-Debit) will work correctly.
        // Let's keep it consistent: we can either delete or keep. 
        // If we keep it, we need to make sure it doesn't get synced again. 
        // My sync checks 'journal_entry_id'.
        
        // Actually, in full accrual, it's better to keep the accrual row but linked to the JE.
        // When we PAY, we don't delete the accrual row, we just add a payment entry to the ledger.

        // 4. Record in payroll table (History)
        $sql = "INSERT INTO payroll (staff_id, amount, payment_date, journal_entry_id) VALUES (?, ?, ?, ?)";
        $result = safe_query($con, $sql, "idsi", [$staff_id, $amount, $payment_date, $journal_entry_id]);

        if ($result) {
            header('location:../payroll.php?success=1');
        } else {
            header('location:../payroll.php?error=Failed to record payroll record');
        }
    } else {
        header('location:../payroll.php?error=Failed to create accounting entry');
    }
} else {
    header('location:../payroll.php');
}
?>
