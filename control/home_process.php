<?php
include_once '../model/mydb.php';
include_once 'auth.php';

if(session_status() == PHP_SESSION_NONE){
    session_start();
}

$mydb = new MyDB();
$conn = $mydb->createConn();
tryRememberLogin($mydb, $conn);

$categoryId   = isset($_GET["category_id"]) ? (int)$_GET["category_id"] : 0;
$categoryType = $_GET["type"] ?? "";
if($categoryType != "liquid" && $categoryType != "solid"){
    $categoryType = "";
}

$categories = $mydb->getCategories($conn);
$vendors    = $mydb->getVendors($conn);
$medicines  = $mydb->getMedicines($categoryId, $categoryType, $conn);
?>
