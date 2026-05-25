<?php

include_once '../model/mydb.php';
include_once 'auth.php';
include_once 'admin_gate.php';

if(session_status() == PHP_SESSION_NONE){ session_start(); }

header("Content-Type: application/json");

if($_SESSION["role"] !== "admin"){
    echo json_encode(array("success" => false, "message" => "Unauthorized"));
    exit();
}

if(!isset($_POST["order_id"]) || !isset($_POST["status"])){
    echo json_encode(array("success" => false, "message" => "Missing parameters"));
    exit();
}

$orderId = (int)$_POST["order_id"];
$status  = trim($_POST["status"]);

if(!in_array($status, array("accepted", "rejected"))){
    echo json_encode(array("success" => false, "message" => "Invalid status value"));
    exit();
}

if($orderId <= 0){
    echo json_encode(array("success" => false, "message" => "Invalid order ID"));
    exit();
}

$mydb   = new MyDB();
$conn   = $mydb->createConn();
$result = $mydb->getOrderById($orderId, $conn);

if($result->num_rows == 0){
    $mydb->closeConn($conn);
    echo json_encode(array("success" => false, "message" => "Order not found"));
    exit();
}

$order = $result->fetch_assoc();
if($order["status"] !== "pending"){
    $mydb->closeConn($conn);
    echo json_encode(array("success" => false, "message" => "Only pending orders can be updated"));
    exit();
}

if($mydb->updateOrderStatus($orderId, $status, $conn)){
    if($status === "rejected"){
        $orderItems = $mydb->getOrderItems($orderId, $conn);
        while($item = $orderItems->fetch_assoc()){
            $mydb->increaseStock($item["medicine_id"], $item["quantity"], $conn);
        }
    }
    $mydb->closeConn($conn);
    echo json_encode(array("success" => true, "message" => "Order " . $status, "status" => $status));
} else {
    $mydb->closeConn($conn);
    echo json_encode(array("success" => false, "message" => "Database error. Please try again."));
}
?>
