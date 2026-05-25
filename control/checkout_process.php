<?php
include_once '../model/mydb.php';
include_once 'auth.php';
include_once 'customer_gate.php';

if(session_status() == PHP_SESSION_NONE){ session_start(); }

$mydb = new MyDB();
$conn = $mydb->createConn();
tryRememberLogin($mydb, $conn);

requireCustomer();

$errors         = array();
$shippingAddress = "";
$showInvoice    = false;

$userResult = $mydb->getUserById($_SESSION["user_id"], $conn);
$user       = $userResult->fetch_assoc();

$cartItems = $mydb->getCartItems($_SESSION["user_id"], $conn);
$cartCount = $mydb->getCartCount($_SESSION["user_id"], $conn);

if($cartCount == 0){
    header("Location: ../view/cart.php");
    exit();
}

$totalAmount = 0;
$itemsArray  = array();
while($item = $cartItems->fetch_assoc()){
    $item["subtotal"] = $item["quantity"] * $item["price"];
    $totalAmount += $item["subtotal"];
    $itemsArray[] = $item;
}

$shippingAddress = $user["address"];

if(isset($_POST["confirm_address"])){
    $shippingAddress = trim($_POST["shipping_address"] ?? "");

    if($shippingAddress == ""){
        $errors["shipping_address"] = "Shipping address is required";
    }

    foreach($itemsArray as $item){
        if($item["quantity"] > $item["availability"]){
            $errors["stock"] = $item["name"] . " has only " . $item["availability"] . " units in stock";
            break;
        }
    }

    if(empty($errors)){
        $_SESSION["shipping_address"] = $shippingAddress;
        $showInvoice = true;
    }
}
?>
