<?php
include_once '../model/mydb.php';
include_once 'auth.php';

if(session_status() == PHP_SESSION_NONE){ session_start(); }

$medicineId = (int)($_GET["id"] ?? 0);
if($medicineId <= 0){
    header("Location: ../view/Home.php");
    exit();
}

$mydb = new MyDB();
$conn = $mydb->createConn();
tryRememberLogin($mydb, $conn);

$medResult = $mydb->getMedicineById($medicineId, $conn);
if($medResult->num_rows == 0){
    $mydb->closeConn($conn);
    header("Location: ../view/Home.php");
    exit();
}

$medicine  = $medResult->fetch_assoc();
$cartCount = 0;
if(isset($_SESSION["user_id"]) && $_SESSION["role"] === "customer"){
    $cartCount = $mydb->getCartCount($_SESSION["user_id"], $conn);
}
?>
