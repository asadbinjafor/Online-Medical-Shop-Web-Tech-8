<?php
include_once '../model/mydb.php';
include_once 'auth.php';
include_once 'customer_gate.php';

if(session_status() == PHP_SESSION_NONE){ session_start(); }

requireCustomerApi();

if(!isset($_POST["order_id"])){
    echo json_encode(array("success" => false, "message" => "Missing order ID"));
    exit();
}

$orderId = (int)$_POST["order_id"];
if($orderId <= 0){
    echo json_encode(array("success" => false, "message" => "Invalid order ID"));
    exit();
}

$mydb = new MyDB();
$conn = $mydb->createConn();

$orderResult = $mydb->getOrderWithItems($orderId, $_SESSION["user_id"], $conn);
if($orderResult->num_rows == 0){
    $mydb->closeConn($conn);
    echo json_encode(array("success" => false, "message" => "Order not found"));
    exit();
}

$order = $orderResult->fetch_assoc();
if($order["status"] !== "pending"){
    $mydb->closeConn($conn);
    echo json_encode(array("success" => false, "message" => "Only pending orders can be cancelled"));
    exit();
}

if($mydb->cancelCustomerOrder($orderId, $_SESSION["user_id"], $conn)){
    $mydb->closeConn($conn);
    echo json_encode(array("success" => true, "message" => "Order cancelled successfully", "status" => "cancelled"));
} else {
    $mydb->closeConn($conn);
    echo json_encode(array("success" => false, "message" => "Failed to cancel order"));
}
?>
