<?php
include '../control/cart_process.php';
include_once '../control/app.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cart - MediShop</title>
    <link rel="stylesheet" type="text/css" href="../css/task3_style.css">
</head>
<body>

<nav>
    <div class="nav-inner">
        <a class="nav-brand" href="Home.php">MediShop</a>
        <div class="nav-links">
            <a href="Home.php">Home</a>
            <a href="cart.php">Cart (<span id="navCartCount"><?php echo $cartCount; ?></span>)</a>
            <a href="customer_orders.php">My Orders</a>
            <a href="profile.php">Profile</a>
            <a href="../control/logout_process.php">Logout</a>
            <span class="nav-user-info">Hi, <?php echo htmlspecialchars($_SESSION["name"]); ?></span>
        </div>
    </div>
</nav>

<div class="page-header">
    <h1>My Cart</h1>
    <p>Review your items before checkout</p>
</div>

<div class="main-container">

    <div id="cartMsg"></div>

    <?php if(empty($itemsArray)){ ?>
        <div class="empty-cart">
            <h2>Your cart is empty</h2>
            <p>Browse medicines and add items to your cart</p>
            <a class="btn-primary" href="Home.php">Browse Medicines</a>
        </div>
    <?php } else { ?>
        <div class="cart-table-wrapper">
            <table class="cart-table" id="cartTable">
                <thead>
                    <tr>
                        <th>Medicine</th>
                        <th>Vendor</th>
                        <th>Price (BDT)</th>
                        <th>Quantity</th>
                        <th>Subtotal (BDT)</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($itemsArray as $item){ ?>
                        <tr id="cart-row-<?php echo $item["id"]; ?>">
                            <td><?php echo htmlspecialchars($item["name"]); ?></td>
                            <td><?php echo htmlspecialchars($item["vendor_name"]); ?></td>
                            <td><?php echo number_format($item["price"], 2); ?></td>
                            <td>
                                <div class="qty-controls">
                                    <button class="qty-btn" onclick="updateCart(<?php echo $item['id']; ?>, <?php echo $item['quantity'] - 1; ?>, <?php echo $item['availability']; ?>)">−</button>
                                    <span class="qty-value" id="qty-<?php echo $item["id"]; ?>"><?php echo $item["quantity"]; ?></span>
                                    <button class="qty-btn" onclick="updateCart(<?php echo $item['id']; ?>, <?php echo $item['quantity'] + 1; ?>, <?php echo $item['availability']; ?>)">+</button>
                                </div>
                                <small class="stock-info">Stock: <?php echo $item["availability"]; ?></small>
                            </td>
                            <td id="subtotal-<?php echo $item["id"]; ?>"><?php echo number_format($item["subtotal"], 2); ?></td>
                            <td>
                                <button class="btn-remove" onclick="removeFromCart(<?php echo $item['id']; ?>)">Remove</button>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="cart-total-label">Total:</td>
                        <td colspan="2" class="cart-total-value" id="cartTotal">BDT <?php echo number_format($totalAmount, 2); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="cart-actions">
            <a class="btn-secondary" href="Home.php">Continue Shopping</a>
            <a class="btn-primary" href="checkout.php">Proceed to Checkout</a>
        </div>
    <?php } ?>

</div>

<script src="../js/task3_script.js"></script>
</body>
</html>
<?php $mydb->closeConn($conn); ?>
