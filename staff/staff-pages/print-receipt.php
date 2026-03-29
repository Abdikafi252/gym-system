<?php
session_start();
include "dbcon.php";

if (!isset($_SESSION['user_id'])) {
    header("location:../../index.php");
    exit();
}

if (!isset($_GET['id'])) {
    die("Please select a receipt.");
}

$history_id = intval($_GET['id']);

// Fetch the receipt from history
$hist_qry = "SELECT * FROM payment_history WHERE id='$history_id'";
$hist_res = mysqli_query($con, $hist_qry);
$hist_row = mysqli_fetch_array($hist_res);

if (!$hist_row) {
    die("Receipt not found.");
}

$invoice_no = !empty($hist_row['invoice_no']) ? $hist_row['invoice_no'] : ('GMS_' . $hist_row['user_id'] . date('Ym', strtotime($hist_row['paid_date'])) . $hist_row['id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>M*A GYM - POS Receipt</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="../../font-awesome/css/all.css" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Courier+Prime:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f4f4f4; font-family: 'Courier Prime', monospace; margin: 0; padding: 20px; display: flex; justify-content: center; }
        .thermal-receipt { width: 320px; background: #fff; padding: 15px; color: #000; margin: 0 auto; box-sizing: border-box; border: 1px solid #eee; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .thermal-header { text-align: center; margin-bottom: 10px; }
        .thermal-header h2 { margin: 0; font-size: 18px; }
        .thermal-header p { margin: 2px 0; font-size: 12px; }
        .thermal-divider { border-top: 1px dashed #000; margin: 10px 0; }
        .thermal-row { display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 3px; }
        .thermal-table { width: 100%; font-size: 12px; border-collapse: collapse; margin: 10px 0; }
        .thermal-table th { border-top: 1px dashed #000; border-bottom: 1px dashed #000; padding: 5px 0; text-align: left; }
        .thermal-table td { padding: 5px 0; }
        .thermal-total-row { display: flex; justify-content: space-between; font-size: 14px; font-weight: bold; border-top: 1px dashed #000; padding-top: 5px; margin-top: 5px; }
        .thermal-footer { text-align: center; margin-top: 15px; font-size: 11px; border-top: 1px dashed #000; padding-top: 10px; }
        
        @media print {
            body { background: #fff; padding: 0; }
            .thermal-receipt { border: none; box-shadow: none; width: 100%; max-width: 320px; }
            .d-print-none { display: none !important; }
            @page { size: 80mm auto; margin: 0; }
        }
    </style>
</head>
<body>
    <div class="receipt-wrapper">
        <div id="print-area" class="thermal-receipt">
            <div class="thermal-header">
                <h2>M*A GYM</h2>
                <p>Busley, Bondheere, Mogadishu</p>
                <p>Tel: 252-610-000-000</p>
            </div>

            <div class="thermal-divider"></div>

            <div class="thermal-row">
                <span>Invoice #:</span>
                <span><?php echo $invoice_no; ?></span>
            </div>
            <div class="thermal-row">
                <span>Date:</span>
                <span><?php echo date("Y-m-d H:i", strtotime($hist_row['paid_date'])); ?></span>
            </div>
            <div class="thermal-row">
                <span>Member:</span>
                <span><?php echo htmlspecialchars($hist_row['fullname']); ?></span>
            </div>

            <div class="thermal-divider"></div>

            <table class="thermal-table">
                <thead>
                    <tr>
                        <th>SERVICE / PLAN</th>
                        <th style="text-align:right">AMT</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <?php echo htmlspecialchars($hist_row['services']); ?><br>
                            <small>(<?php echo $hist_row['plan']; ?> Month/s)</small>
                        </td>
                        <td style="text-align:right">$<?php echo number_format((float)$hist_row['paid_amount'], 2); ?></td>
                    </tr>
                </tbody>
            </table>

            <div class="thermal-total-row">
                <span>TOTAL PAID:</span>
                <span>$<?php echo number_format((float)$hist_row['paid_amount'], 2); ?></span>
            </div>

            <div class="thermal-footer">
                <p>*** RE-PRINTED RECEIPT ***</p>
                <p>Official Receipt - Power by M*A</p>
                <p><?php echo date("d/m/Y H:i:s"); ?></p>
            </div>
        </div>
        
        <div class="text-center d-print-none" style="text-align:center; margin-top: 20px;">
            <button type="button" class="btn btn-danger" onclick="window.print()" style="padding:10px 20px; background:#dc3545; color:#fff; border:none; border-radius:4px; cursor:pointer;">[ PRINT SLIP ]</button>
            <button type="button" class="btn btn-primary" onclick="generatePOSPDF('POS_<?php echo $invoice_no; ?>')" style="padding:10px 20px; background:#007bff; color:#fff; border:none; border-radius:4px; cursor:pointer; margin-left:10px;">[ DOWNLOAD PDF ]</button>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        function generatePOSPDF(filename) {
            var element = document.getElementById('print-area');
            var opt = {
                margin:       0,
                filename:     filename + '.pdf',
                image:        { type: 'jpeg', quality: 1 },
                html2canvas:  { scale: 2, useCORS: true },
                jsPDF:        { unit: 'in', format: [3.15, 6], orientation: 'portrait' }
            };
            html2pdf().from(element).set(opt).save();
        }
    </script>
</body>
</html>
