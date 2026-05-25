<?php
include_once '../model/mydb.php';
include_once 'auth.php';
session_start();

$mydb = new MyDB();
$conn = $mydb->createConn();
tryRememberLogin($mydb, $conn);

if(!isset($_SESSION["user_id"])){
    header("Location: ../view/login.php");
    exit();
}

$result = $mydb->getUserById($_SESSION["user_id"], $conn);

if($result->num_rows == 0){
    header("Location: ../control/logout_process.php");
    exit();
}

$user           = $result->fetch_assoc();
$name           = $user["name"];
$email          = $user["email"];
$role           = $user["role"];
$address        = $user["address"];
$phone          = $user["phone"];
$profilePicture = $user["profile_picture"];
?>
