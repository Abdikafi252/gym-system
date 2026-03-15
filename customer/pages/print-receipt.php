<?php
session_start();
include "dbcon.php";
include "session.php";

if (!isset($_SESSION['user_id'])) {
    header("location:../index.php");
    exit();
}

$uid = $_SESSION['user_id'];

if (!isset($_GET['id'])) {
    die("Fadlan dooro rasiid.");
}

$history_id = intval($_GET['id']);

$qry = "SELECT * FROM members WHERE user_id='$uid'";
$result = mysqli_query($con, $qry);
$member_row = mysqli_fetch_array($result);

if (!$member_row) {
    die("Xogtaada lama heli karo. Fadlan la xiriir Maamulka.");
}

$hist_qry = "SELECT * FROM payment_history WHERE id='$history_id' AND user_id='$uid'";
$hist_res = mysqli_query($con, $hist_qry);
$hist_row = mysqli_fetch_array($hist_res);

if (!$hist_row) {
    die("Rasiidkan lama heli karo ama adiga iska ma lihid.");
}

$invoice_no = !empty($hist_row['invoice_no']) ? $hist_row['invoice_no'] : ('GMS_' . $hist_row['user_id'] . date('Ym', strtotime($hist_row['paid_date'])) . $hist_row['id']);
$verify_code = strtoupper(substr(hash('sha256', $invoice_no . '|' . $hist_row['paid_date'] . '|' . $hist_row['paid_amount']), 0, 12));
$verify_payload = rawurlencode("Invoice:$invoice_no|Verify:$verify_code|Member:" . $hist_row['user_id']);
$qr_url = "https://quickchart.io/qr?size=120&text=$verify_payload";

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>M * A GYM System - Print Receipt</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../css/bootstrap.min.css" />
    <link rel="stylesheet" href="../css/bootstrap-responsive.min.css" />
    <link href="../font-awesome/css/all.css" rel="stylesheet" />
    <style>
        body {
            background-color: #f6f6f6;
            font-family: 'Open Sans', sans-serif;
            -webkit-font-smoothing: antialiased;
            margin: 0;
            padding: 0;
        }

        .receipt-container {
            max-width: 600px;
            margin: 40px auto;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            padding: 30px;
        }

        .header {
            text-align: center;
            padding-bottom: 25px;
            border-bottom: 2px dashed #e2e8f0;
            margin-bottom: 25px;
        }

        .header h2 {
            margin: 0;
            color: #0f172a;
            font-weight: 900;
        }

        .header p {
            margin: 5px 0 0 0;
            color: #64748b;
            font-size: 14px;
        }

        .invoice-details {
            margin: 10px 0 20px 0;
            display: flex;
            justify-content: space-between;
        }

        .member-info {
            background: #f8fafc;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            color: #0f172a;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
        }

        .items-table th {
            border-bottom: 2px solid #e2e8f0;
            padding: 10px 0;
            text-align: left;
            color: #64748b;
        }

        .items-table th.right {
            text-align: right;
        }

        .items-table td {
            padding: 15px 0;
            border-bottom: 1px solid #f1f5f9;
            color: #0f172a;
            font-weight: 600;
        }

        .items-table td.right {
            text-align: right;
        }

        .items-table td.light {
            color: #475569;
            font-weight: normal;
        }

        .items-table td.discount {
            color: #ca8a04;
        }

        .items-table td.total-label {
            padding: 20px 0 0 0;
            font-size: 18px;
            font-weight: 900;
            border-bottom: none;
        }

        .items-table td.total-value {
            padding: 20px 0 0 0;
            font-size: 22px;
            font-weight: 900;
            color: #16a34a;
            border-bottom: none;
        }

        .footer-note {
            text-align: center;
            padding-top: 40px;
            color: #94a3b8;
            font-size: 13px;
        }

        .verify-box {
            margin-top: 18px;
            border: 1px dashed #94a3b8;
            border-radius: 10px;
            padding: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .stamp-box {
            width: 120px;
            height: 120px;
            border: 2px dashed #334155;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #334155;
            font-size: 11px;
            font-weight: 800;
            transform: rotate(-10deg);
        }

        .print-btn-container {
            text-align: center;
            padding-top: 30px;
        }

        .btn-print {
            background: #2563eb;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            padding: 12px 30px;
            font-size: 16px;
            cursor: pointer;
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);
            transition: all 0.2s;
        }

        .btn-print:hover {
            background: #1d4ed8;
            box-shadow: 0 6px 8px -1px rgba(37, 99, 235, 0.3);
        }

        @media print {
            body {
                background: #fff;
            }

            .receipt-container {
                box-shadow: none;
                border: none;
                margin: 0;
                padding: 0;
            }

            .d-print-none {
                display: none !important;
            }
        }
    </style>
</head>

<body>
    <div class="receipt-container">
        <div class="header">
            <h2>M * A GYM</h2>
            <p>Busley, Bondheere, Mogadishu</p>
        </div>

        <div style="overflow: hidden; margin-bottom: 20px;">
            <div style="float:left; color:#475569;">
                <strong style="color:#0f172a;">Rasiid #<?php echo $invoice_no; ?></strong><br>
                Taariikhda: <?php echo date("F j, Y", strtotime($hist_row['paid_date'])); ?>
            </div>
            <div style="float:right;">
                <span class="badge" style="background:#dcfce7; color:#16a34a; padding:5px 12px; border-radius:20px; font-weight:bold; font-size:12px; display:inline-block;"><i class="fas fa-check-circle"></i> PAID</span>
            </div>
        </div>

        <div class="member-info">
            <b>Xubinta: <?php echo $hist_row['fullname']; ?></b> <br>
            ID: PGC-<?php echo $hist_row['user_id']; ?>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Adeegga</th>
                    <th class="right">Qorshaha</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo $hist_row['services']; ?></td>
                    <td class="right"><?php echo $hist_row['plan']; ?> Bilood</td>
                </tr>
                <tr>
                    <td class="light">Lacagta Guud</td>
                    <td class="right light">$<?php echo $hist_row['amount'] + $hist_row['discount_amount']; ?></td>
                </tr>
                <?php if ($hist_row['discount_amount'] > 0) { ?>
                    <tr>
                        <td class="discount">Sicir-dhimis (Discount)</td>
                        <td class="right discount">-$<?php echo $hist_row['discount_amount']; ?></td>
                    </tr>
                <?php } ?>
                <tr>
                    <td class="total-label">Wadarta La Bixiyay</td>
                    <td class="right total-value">$<?php echo $hist_row['paid_amount']; ?></td>
                </tr>
            </tbody>
        </table>

        <div class="verify-box">
            <div>
                <div style="font-size:11px;color:#64748b;font-weight:700;">Verification Code</div>
                <div style="font-size:16px;font-weight:900;color:#0f172a;letter-spacing:1px;"><?php echo $verify_code; ?></div>
                <div style="font-size:11px;color:#64748b;">Invoice: <?php echo $invoice_no; ?></div>
            </div>
            <div>
                <img src="<?php echo $qr_url; ?>" alt="Verification QR" style="width:80px;height:80px;border:1px solid #e2e8f0;border-radius:8px;">
            </div>
            <div class="stamp-box">M*A GYM<br>VERIFIED</div>
        </div>

        <div class="footer-note">
            <i class="fas fa-heart" style="color:#ef4444;"></i> Waad ku mahadsantahay inaad nala macaamisho!
        </div>

        <div class="print-btn-container d-print-none">
            <button class="btn-print" onclick="window.print()"><i class="fas fa-print"></i> Daabac Rasiidka</button>
        </div>
    </div>
</body>

</html>