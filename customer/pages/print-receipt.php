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
    die("Please select a receipt.");
}

$history_id = intval($_GET['id']);

$qry = "SELECT * FROM members WHERE user_id='$uid'";
$result = mysqli_query($con, $qry);
$member_row = mysqli_fetch_array($result);

if (!$member_row) {
    die("Your data could not be found. Please contact administration.");
}

$hist_qry = "SELECT * FROM payment_history WHERE id='$history_id' AND user_id='$uid'";
$hist_res = mysqli_query($con, $hist_qry);
$hist_row = mysqli_fetch_array($hist_res);

if (!$hist_row) {
    die("This receipt is not available or does not belong to you.");
}

$invoice_no = !empty($hist_row['invoice_no']) ? $hist_row['invoice_no'] : ('GMS_' . $hist_row['user_id'] . date('Ym', strtotime($hist_row['paid_date'])) . $hist_row['id']);
$verify_code = strtoupper(substr(hash('sha256', $invoice_no . '|' . $hist_row['paid_date'] . '|' . $hist_row['paid_amount']), 0, 12));
$verify_payload = rawurlencode("Invoice:$invoice_no|Verify:$verify_code|Member:" . $hist_row['user_id']);
$qr_url = "https://quickchart.io/qr?size=120&text=$verify_payload";

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>M*A GYM System - POS Receipt</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="../font-awesome/css/all.css" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Courier+Prime:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #333; /* Dark background to make receipt pop */
            font-family: 'Courier Prime', monospace;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
        }

        .receipt-container {
            width: 320px; /* 80mm thermal paper width */
            background: #fff;
            padding: 10px;
            color: #000;
            margin: 0 auto;
            box-sizing: border-box;
            box-shadow: 0 4px 15px rgba(0,0,0,0.5);
        }

        .header {
            text-align: center;
            margin-bottom: 12px;
        }

        .header h2 {
            margin: 0 0 5px 0;
            font-size: 24px;
            font-weight: 700;
        }

        .header p {
            margin: 0;
            font-size: 14px;
        }

        .divider {
            border-top: 1.5px dashed #000;
            margin: 12px 0;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            margin-bottom: 6px;
        }

        .items-table {
            width: 100%;
            font-size: 14px;
            border-collapse: collapse;
            margin: 12px 0;
        }

        .items-table th {
            border-top: 1.5px dashed #000;
            border-bottom: 1.5px dashed #000;
            padding: 6px 0;
            text-align: left;
            font-weight: 700;
        }
        
        .items-table th.right, .items-table td.right {
            text-align: right;
        }

        .items-table td {
            padding: 8px 0;
            vertical-align: top;
        }

        .totals {
            margin-top: 12px;
            border-top: 1.5px dashed #000;
            padding-top: 12px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .total-row.grand-total {
            font-size: 20px;
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1.5px dashed #000;
        }

        .qr-section {
            text-align: center;
            margin-top: 20px;
        }

        .qr-section img {
            width: 140px;
            height: 140px;
        }

        .qr-section p {
            font-size: 12px;
            margin: 8px 0 0 0;
            word-wrap: break-word;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            border-top: 1.5px dashed #000;
            padding-top: 12px;
        }

        .print-actions {
            text-align: center;
            margin-top: 20px;
        }

        .btn {
            font-family: 'Courier Prime', monospace;
            padding: 8px 15px;
            margin: 0 5px;
            cursor: pointer;
            border: 1px solid #000;
            background: #fff;
            font-weight: 700;
            font-size: 12px;
            border-radius: 4px;
            transition: all 0.2s;
        }

        .btn:hover {
            background: #000;
            color: #fff;
        }

        @media print {
            body {
                background-color: #fff;
                padding: 0;
                display: block;
            }
            .receipt-container {
                box-shadow: none;
                width: 100%;
                max-width: 320px;
                padding: 0;
                margin: 0;
            }
            .d-print-none {
                display: none !important;
            }
            @page {
                margin: 0;
                size: 80mm auto;
            }
        }
    </style>
</head>

<body>
    <div class="receipt-wrap">
        <div class="receipt-container" id="receipt">
            <div class="header">
                <h2>M*A GYM</h2>
                <p>Busley, Bondheere</p>
                <p>Mogadishu, Somalia</p>
                <p>Tel: 252-610-000-000</p>
            </div>

            <div class="divider"></div>

            <div class="info-row">
                <span>Receipt #:</span>
                <span><?php echo $invoice_no; ?></span>
            </div>
            <div class="info-row">
                <span>Date:</span>
                <span><?php echo date("Y-m-d H:i", strtotime($hist_row['paid_date'])); ?></span>
            </div>
            <div class="info-row">
                <span>Member:</span>
                <span><?php echo $hist_row['fullname']; ?></span>
            </div>
            <div class="info-row">
                <span>ID:</span>
                <span>PGC-<?php echo $hist_row['user_id']; ?></span>
            </div>

            <table class="items-table">
                <thead>
                    <tr>
                        <th>ITEM / PLAN</th>
                        <th class="right">AMT</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <?php echo $hist_row['services']; ?><br>
                            <small>(<?php echo $hist_row['plan']; ?> Month/s)</small>
                        </td>
                        <td class="right">$<?php echo number_format($hist_row['amount'] + $hist_row['discount_amount'], 2); ?></td>
                    </tr>
                </tbody>
            </table>

            <div class="totals">
                <div class="total-row">
                    <span>SUBTOTAL:</span>
                    <span>$<?php echo number_format($hist_row['amount'] + $hist_row['discount_amount'], 2); ?></span>
                </div>
                <?php if ($hist_row['discount_amount'] > 0) { ?>
                <div class="total-row">
                    <span>DISCOUNT:</span>
                    <span>-$<?php echo number_format($hist_row['discount_amount'], 2); ?></span>
                </div>
                <?php } ?>
                <div class="total-row grand-total">
                    <span>TOTAL PAID:</span>
                    <span>$<?php echo number_format($hist_row['paid_amount'], 2); ?></span>
                </div>
            </div>

            <div class="divider"></div>

            <div class="qr-section">
                <img src="<?php echo $qr_url; ?>" alt="QR Code">
                <p>VERIFY: <?php echo $verify_code; ?></p>
            </div>

            <div class="footer">
                <p>*** THANK YOU! ***</p>
                <p>For inquiries, contact support.</p>
                <p><?php echo date("d/m/Y H:i:s"); ?></p>
            </div>
        </div>
        
        <div class="print-actions d-print-none">
            <button class="btn" onclick="window.print()">[ PRINT ]</button>
            <button class="btn" onclick="generateThermalPDF('POS_<?php echo $invoice_no; ?>')">[ PDF ]</button>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        function generateThermalPDF(filename) {
            var element = document.getElementById('receipt');
            var opt = {
                margin:       0, // 0 margin for POS
                filename:     filename + '.pdf',
                image:        { type: 'jpeg', quality: 1 },
                html2canvas:  { scale: 2, useCORS: true },
                // Roughly 3.15 inches (80mm) by 7 inches, format array in inches [width, height]
                jsPDF:        { unit: 'in', format: [3.15, 12], orientation: 'portrait' } 
            };
            document.body.classList.add('generating-pdf');
            html2pdf().from(element).set(opt).save().then(() => {
                document.body.classList.remove('generating-pdf');
            });
        }
    </script>
</body>
</html>