<?php

session_start();
//the isset function to check username is already loged in and stored on the session
if(!isset($_SESSION['user_id'])){
header('location:../index.php');	
}

if(isset($_GET['id'])){
    $id = (int)$_GET['id'];
    include 'dbcon.php';
    require_once __DIR__ . '/../includes/accounting_engine.php';

    // Branch protection
    $isStaffManager = (isset($_SESSION['designation']) && $_SESSION['designation'] == 'Manager');
    $sessBranch = isset($_SESSION['branch_id']) ? (int)$_SESSION['branch_id'] : 0;
    $branch_where = ($isStaffManager && $sessBranch > 0) ? " AND branch_id = $sessBranch " : "";

    // Verify ownership before deleting
    $check = mysqli_query($con, "SELECT id FROM equipment WHERE id=$id $branch_where");
    if (mysqli_num_rows($check) == 0) {
        die("Access denied or equipment not found.");
    }

    // Delete accounting records for this equipment
    acc_delete_source_postings($con, 'equipment', (string)$id);
    $qry = "DELETE FROM equipment WHERE id=$id";
    $result = mysqli_query($con, $qry);
    if ($result) {
        echo "DELETED";
        header('Location:../remove-equipment.php');
    } else {
        echo "ERROR!!";
    }
}
?>
