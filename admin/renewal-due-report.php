<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('location:../index.php');
    exit;
}
include 'dbcon.php';

$days = isset($_GET['days']) ? (int)$_GET['days'] : 7;
if ($days < 1 || $days > 90) $days = 7;

$qry = "SELECT user_id, fullname, contact, services, paid_date, expiry_date, status
        FROM members
        WHERE status IN ('Active','Expired')
          AND expiry_date <= DATE_ADD(CURDATE(), INTERVAL $days DAY)
        ORDER BY expiry_date ASC";
$res = mysqli_query($con, $qry);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Renewal Due Report</title>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="../css/bootstrap.min.css" />
  <link rel="stylesheet" href="../css/matrix-style.css" />
  <link href="../font-awesome/css/all.css" rel="stylesheet" />
  <link rel="stylesheet" href="../css/premium-print.css" />
  <script src="../js/jquery.min.js"></script>
  <script src="../js/bootstrap.min.js"></script>
  <script src="../js/matrix.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
  <script>
  function generateRenewalPDF() {
      const element = document.querySelector('.premium-document');
      const opt = {
          margin:       [0.5, 0.3, 0.5, 0.3],
          filename:     'Renewal_Report_<?php echo date("Y-m-d"); ?>.pdf',
          image:        { type: 'jpeg', quality: 0.98 },
          html2canvas:  { scale: 2, useCORS: true },
          jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' },
          pagebreak:    { mode: ['avoid-all', 'css', 'legacy'] }
      };

      html2pdf().set(opt).from(element).save();
  }
  </script>
  <style>
    @media print { .d-print-none { display: none !important; } }
  </style>
</head>
<body>
<?php include 'includes/header-content.php'; ?>
<?php include 'includes/topheader.php'; ?>
<?php $page = 'renewal-due'; include 'includes/sidebar.php'; ?>
<div id="content">
  <div id="content-header">
    <div id="breadcrumb"><a href="index.php" class="tip-bottom"><i class="fas fa-home"></i> Home</a> <a href="#" class="current">Renewal Due Report</a></div>
  </div>
  <div class="container-fluid">
    <div class="premium-document">
      <div class="premium-header">
          <div class="premium-brand">
              <h1>M*A GYM</h1>
              <p>Mogadishu, Somalia</p>
              <p>Membership Renewals Division</p>
          </div>
          <div class="premium-meta">
              <h2>RENEWAL DUE REPORT</h2>
              <p><strong>Look-ahead Horizon:</strong> <?php echo $days; ?> Days</p>
              <p><strong>Generated:</strong> <?php echo date("d/m/Y"); ?></p>
          </div>
      </div>

      <div class="d-print-none" style="margin-bottom:20px; text-align:right;">
          <button class="btn btn-info" onclick="window.print();"><i class="fas fa-print"></i> Print</button>
          <button class="btn btn-success" onclick="generateRenewalPDF();"><i class="fas fa-file-pdf"></i> Download PDF</button>
      </div>

      <div class="widget-box" style="border:none; box-shadow:none;">
        <div class="widget-title d-print-none"><span class="icon"><i class="fas fa-filter"></i></span><h5>Filter Records</h5></div>
        <div class="widget-content nopadding">
          <form method="GET" class="form-inline d-print-none" style="padding:15px; background:#f8fafc; border-bottom:1px solid #e2e8f0;">
            <label>Due in next&nbsp;</label>
            <select name="days" class="span2">
              <option value="7" <?php if($days==7) echo 'selected'; ?>>7 days</option>
              <option value="15" <?php if($days==15) echo 'selected'; ?>>15 days</option>
              <option value="30" <?php if($days==30) echo 'selected'; ?>>30 days</option>
            </select>
            <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i> Filter</button>
          </form>

          <table class="premium-table">
            <thead>
                <tr>
                    <th>Member ID</th>
                    <th>Full Name</th>
                    <th>Contact</th>
                    <th>Service Type</th>
                    <th>Last Payment</th>
                    <th>Expiry Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($res && mysqli_num_rows($res) > 0): while($row=mysqli_fetch_assoc($res)): ?>
              <tr>
                <td><strong>#<?php echo $row['user_id']; ?></strong></td>
                <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                <td><?php echo htmlspecialchars($row['contact']); ?></td>
                <td><?php echo htmlspecialchars($row['services']); ?></td>
                <td><?php echo date('d/m/Y', strtotime($row['paid_date'])); ?></td>
                <td style="color:#e11d48; font-weight:bold;"><?php echo date('d/m/Y', strtotime($row['expiry_date'])); ?></td>
                <td>
                    <span class="label <?php echo ($row['status']=='Active') ? 'label-success' : 'label-important'; ?>">
                        <?php echo strtoupper($row['status']); ?>
                    </span>
                </td>
              </tr>
            <?php endwhile; else: ?>
              <tr><td colspan="7" style="text-align:center; padding:30px;">No renewal records found for the selected horizon.</td></tr>
            <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <div class="premium-signature" style="margin-top:50px;">
          <p>Verified By Gym Management</p>
          <p>Official Stamp Required</p>
      </div>
    </div>
  </div>
</div>
</body>
</html>
