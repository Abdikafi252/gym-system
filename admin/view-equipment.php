<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header('location:../index.php');
  exit;
}
include 'dbcon.php';
mysqli_query($con, "ALTER TABLE equipment ADD COLUMN IF NOT EXISTS photo VARCHAR(255) NULL");
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$res = mysqli_query($con, "SELECT * FROM equipment WHERE id='$id' LIMIT 1");
$row = $res ? mysqli_fetch_assoc($res) : null;
if (!$row) {
  header('location:equipment.php');
  exit;
}
$photo = !empty($row['photo']) ? '../img/equipment/' . $row['photo'] : '../img/dumbbell.png';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>View Equipment</title>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="../css/bootstrap.min.css" />
  <link rel="stylesheet" href="../css/bootstrap-responsive.min.css" />
  <link rel="stylesheet" href="../css/matrix-style.css" />
  <link rel="stylesheet" href="../css/matrix-media.css" />
  <link href="../font-awesome/css/all.css" rel="stylesheet" />
  <style>
    .equip-profile{max-width:980px;margin:20px auto;background:#fff;border-radius:24px;box-shadow:0 20px 50px rgba(0,0,0,.08);overflow:hidden;border:1px solid #e5e7eb;}
    .equip-hero{background:linear-gradient(135deg,#111827,#4338ca);padding:28px;color:#fff;display:flex;gap:22px;align-items:center;}
    .equip-hero img{width:150px;height:150px;border-radius:22px;object-fit:cover;border:4px solid rgba(255,255,255,.8);background:#fff;}
    .equip-name{font-size:28px;font-weight:800;margin:0 0 8px;}
    .equip-sub{opacity:.9;font-size:14px;}
    .equip-body{padding:24px;display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;}
    .info-box{background:#f8fafc;border:1px solid #e5e7eb;border-radius:16px;padding:16px;}
    .info-label{font-size:11px;font-weight:800;text-transform:uppercase;color:#6b7280;margin-bottom:6px;}
    .info-value{font-size:16px;font-weight:700;color:#111827;}
    .action-row{padding:0 24px 24px;display:flex;gap:10px;flex-wrap:wrap;}
    .action-row .btn{border-radius:999px;padding:10px 16px;font-weight:700;}
  </style>
</head>
<body>
<?php include 'includes/header-content.php'; ?>
<?php include 'includes/topheader.php'; ?>
<?php $page='list-equip'; include 'includes/sidebar.php'; ?>
<div id="content">
  <div id="content-header">
    <div id="breadcrumb"><a href="index.php" class="tip-bottom"><i class="fas fa-home"></i> Home</a> <a href="equipment.php">Equipment</a> <a href="#" class="current">View Equipment</a></div>
  </div>
  <div class="container-fluid">
    <div class="equip-profile">
      <div class="equip-hero">
        <img src="<?php echo htmlspecialchars($photo); ?>" alt="Equipment" onerror="this.src='../img/dumbbell.png'">
        <div>
          <h1 class="equip-name"><?php echo htmlspecialchars($row['name']); ?></h1>
          <div class="equip-sub"><?php echo htmlspecialchars($row['description']); ?></div>
        </div>
      </div>
      <div class="equip-body">
        <div class="info-box"><div class="info-label">Quantity</div><div class="info-value"><?php echo (int)$row['quantity']; ?></div></div>
        <div class="info-box"><div class="info-label">Total Cost</div><div class="info-value">$<?php echo number_format((float)$row['amount'],2); ?></div></div>
        <div class="info-box"><div class="info-label">Vendor</div><div class="info-value"><?php echo htmlspecialchars($row['vendor']); ?></div></div>
        <div class="info-box"><div class="info-label">Contact</div><div class="info-value"><?php echo htmlspecialchars($row['contact']); ?></div></div>
        <div class="info-box"><div class="info-label">Address</div><div class="info-value"><?php echo htmlspecialchars($row['address']); ?></div></div>
        <div class="info-box"><div class="info-label">Date of Purchase</div><div class="info-value"><?php echo htmlspecialchars($row['date']); ?></div></div>
      </div>
      <div class="action-row">
        <a class="btn btn-info" href="equipment.php"><i class="fas fa-arrow-left"></i> Back</a>
        <a class="btn btn-warning" href="edit-equipmentform.php?id=<?php echo (int)$row['id']; ?>"><i class="fas fa-edit"></i> Update</a>
        <a class="btn btn-danger" href="actions/delete-equipment.php?id=<?php echo (int)$row['id']; ?>" onclick="return confirm('Are you sure you want to delete this equipment?')"><i class="fas fa-trash"></i> Delete</a>
      </div>
    </div>
  </div>
</div>
</body>
</html>
