<?php
include_once '../model/mydb.php';
include_once 'auth.php';
include_once 'customer_gate.php';

if(session_status() == PHP_SESSION_NONE){ session_start(); }

requireCustomerApi();

if(!isset($_POST["medicine_id"]) || !isset($_POST["quantity"])){
    echo json_encode(array("success" => false, "message" => "Missing parameters"));
    exit();
}

$medicineId = (int)$_POST["medicine_id"];
$quantity   = (int)$_POST["quantity"];

if($medicineId <= 0){
    echo json_encode(array("success" => false, "message" => "Invalid medicine"));
    exit();
}
if($quantity <= 0){
    echo json_encode(array("success" => false, "message" => "Quantity must be at least 1"));
    exit();
}

$mydb = new MyDB();
$conn = $mydb->createConn();

$medResult = $mydb->getMedicineById($medicineId, $conn);
if($medResult->num_rows == 0){
    $mydb->closeConn($conn);
    echo json_encode(array("success" => false, "message" => "Medicine not found"));
    exit();
}

$medicine = $medResult->fetch_assoc();
if($quantity > $medicine["availability"]){
    $mydb->closeConn($conn);
    echo json_encode(array("success" => false, "message" => "Only " . $medicine["availability"] . " units available"));
    exit();
}

if($mydb->addToCart($_SESSION["user_id"], $medicineId, $quantity, $conn)){
    $cartCount = $mydb->getCartCount($_SESSION["user_id"], $conn);
    $mydb->closeConn($conn);
    echo json_encode(array("success" => true, "message" => "Added to cart", "cart_count" => $cartCount));
} else {
    $mydb->closeConn($conn);
    echo json_encode(array("success" => false, "message" => "Failed to add to cart"));
}
?>
