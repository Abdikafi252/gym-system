<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('location:../index.php');
    exit;
}

include 'dbcon.php';

$where = "WHERE 1=1";

if (!empty($_GET['branch_id'])) {
    $branch_id = mysqli_real_escape_string($con, $_GET['branch_id']);
    $where .= " AND ph.branch_id='$branch_id'";
}

if (!empty($_GET['month'])) {
    $month = mysqli_real_escape_string($con, $_GET['month']);
    $where .= " AND MONTH(ph.paid_date)='$month'";
}

if (!empty($_GET['year'])) {
    $year = mysqli_real_escape_string($con, $_GET['year']);
    $where .= " AND YEAR(ph.paid_date)='$year'";
}

if (!empty($_GET['search'])) {
    $search = mysqli_real_escape_string($con, $_GET['search']);
    $where .= " AND (ph.fullname LIKE '%$search%' OR ph.services LIKE '%$search%')";
}

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=admin-payment-history-' . date('Ymd-His') . '.csv');

$output = fopen('php://output', 'w');
fputcsv($output, ['Invoice', 'Branch', 'Member', 'Service', 'Plan (Months)', 'Paid Date', 'Expiry Date', 'Paid Amount', 'Discount', 'Total Amount', 'Recorded By']);

$qry = "SELECT ph.*, b.branch_name FROM payment_history ph LEFT JOIN branches b ON b.id = ph.branch_id $where ORDER BY ph.paid_date DESC, ph.id DESC";
$res = mysqli_query($con, $qry);

if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $invoice = !empty($row['invoice_no']) ? $row['invoice_no'] : ('GMS_' . $row['user_id'] . date('Ym', strtotime($row['paid_date'])) . $row['id']);
        fputcsv($output, [
            $invoice,
            $row['branch_name'],
            $row['fullname'],
            $row['services'],
            $row['plan'],
            $row['paid_date'],
            $row['expiry_date'],
            $row['paid_amount'],
            $row['discount_amount'],
            $row['amount'],
            $row['recorded_by']
        ]);
    }
}

fclose($output);
exit;
