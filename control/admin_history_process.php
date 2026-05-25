<?php
include_once '../model/mydb.php';
include_once 'admin_gate.php';

if(session_status() == PHP_SESSION_NONE){ session_start(); }
requireAdmin();

$mydb   = new MyDB();
$conn   = $mydb->createConn();
$orders = $mydb->getAcceptedOrders($conn);

$orderData = array();
while($order = $orders->fetch_assoc()){
    $items = $mydb->getOrderItems($order["id"], $conn);
    $order["items"] = array();
    while($item = $items->fetch_assoc()){
        $order["items"][] = $item;
    }
    $orderData[] = $order;
}

$mydb->closeConn($conn);
?>
