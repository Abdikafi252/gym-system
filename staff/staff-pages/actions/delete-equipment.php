<?php
session_start();
//the isset function to check username is already loged in and stored on the session
if(!isset($_SESSION['user_id'])){
header('location:../index.php');	
}

if(isset($_GET['id'])){
$id=$_GET['id'];

include 'dbcon.php';


$branch_id = $_SESSION['branch_id'];
$qry="delete from equipment where id=$id AND branch_id='$branch_id'";
$result=mysqli_query($con,$qry);

if($result){
    echo"DELETED";
    header('Location:../remove-equipment.php');
}else{
    echo"ERROR!!";
}
}
?>
