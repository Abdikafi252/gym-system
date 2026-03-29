<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('location:../index.php');
    exit;
}
include 'dbcon.php';
require_once __DIR__ . '/includes/accounting_engine.php';

$page = 'debug_owner_capital_journal';
$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

$entries = mysqli_query($con, "SELECT je.id, je.entry_date, je.period_year, je.memo, je.source_type, je.source_id, jl.account_id, coa.code, coa.name, jl.debit, jl.credit FROM journal_entries je LEFT JOIN journal_lines jl ON jl.journal_entry_id = je.id LEFT JOIN chart_of_accounts coa ON coa.id = jl.account_id WHERE je.source_type='owner_capital' AND je.period_year = $year ORDER BY je.id DESC");

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Debug Owner Capital Journal</title>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="../css/bootstrap.min.css" />
  <link rel="stylesheet" href="../css/bootstrap-responsive.min.css" />
  <link rel="stylesheet" href="../css/matrix-style.css" />
  <link rel="stylesheet" href="../css/matrix-media.css" />
  <link href="../font-awesome/css/all.css" rel="stylesheet" />
</head>
<body>
<?php include 'includes/header-content.php'; ?>
<?php include 'includes/topheader.php'; ?>
<?php include 'includes/sidebar.php'; ?>
<div id="content">
  <div class="container-fluid">
    <h1>Owner Capital Journal Entries (Debug)</h1>
    <table class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>Entry ID</th>
          <th>Date</th>
          <th>Period Year</th>
          <th>Memo</th>
          <th>Source ID</th>
          <th>Account Code</th>
          <th>Account Name</th>
          <th>Debit</th>
          <th>Credit</th>
        </tr>
      </thead>
      <tbody>
        <?php
        if ($entries && mysqli_num_rows($entries) > 0) {
          while ($row = mysqli_fetch_assoc($entries)) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($row['id']) . '</td>';
            echo '<td>' . htmlspecialchars($row['entry_date']) . '</td>';
            echo '<td>' . htmlspecialchars($row['period_year']) . '</td>';
            echo '<td>' . htmlspecialchars($row['memo']) . '</td>';
            echo '<td>' . htmlspecialchars($row['source_id']) . '</td>';
            echo '<td>' . htmlspecialchars($row['code']) . '</td>';
            echo '<td>' . htmlspecialchars($row['name']) . '</td>';
            echo '<td>' . number_format((float)$row['debit'], 2) . '</td>';
            echo '<td>' . number_format((float)$row['credit'], 2) . '</td>';
            echo '</tr>';
          }
        } else {
          echo '<tr><td colspan="9">No owner capital journal entries found for this year.</td></tr>';
        }
        ?>
      </tbody>
    </table>
  </div>
</div>
<script src="../js/jquery.min.js"></script><script src="../js/bootstrap.min.js"></script><script src="../js/matrix.js"></script>
</body>
</html>
