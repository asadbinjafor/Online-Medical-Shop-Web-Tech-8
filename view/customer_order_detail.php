<?php
include '../control/customer_order_detail_process.php';
include_once '../control/app.php';

$statusMessages = array(
    "pending"   => "Your order is waiting for admin approval.",
    "accepted"  => "Your order has been accepted and is being processed.",
    "rejected"  => "Your order was rejected by the admin. Stock has been restored.",
    "cancelled" => "You cancelled this order before admin review. Stock has been restored."
);
$currentMsg = $statusMessages[$order["status"]] ?? "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order #<?php echo $order["id"]; ?> - MediShop</title>
    <link rel="stylesheet" type="text/css" href="../css/task3_style.css">
    <link rel="stylesheet" type="text/css" href="../css/task4_style.css">
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
    <h1>Order #<?php echo $order["id"]; ?></h1>
    <p>Order details and tracking</p>
</div>

<div class="main-container">

    <div id="orderDetailMsg"></div>

    <div class="card order-detail-card">
        <div class="order-detail-header">
            <span class="order-status-badge status-<?php echo htmlspecialchars($order["status"]); ?>" id="orderStatusBadge">
                <?php echo ucfirst(htmlspecialchars($order["status"])); ?>
            </span>
            <div class="order-detail-actions">
                <a class="btn-secondary" href="order_invoice.php?order_id=<?php echo $order["id"]; ?>" target="_blank">Print Invoice</a>
                <a class="btn-secondary" href="customer_orders.php">Back to Orders</a>
            </div>
        </div>

        <div class="status-timeline">
            <?php
            $st = $order["status"];
            $step1 = in_array($st, array("pending","accepted","rejected","cancelled")) ? "active" : "";
            $step2 = ($st === "accepted") ? "active" : "";
            $step3 = in_array($st, array("rejected","cancelled")) ? "active" : "";
            ?>
            <div class="timeline-step <?php echo $step1; ?>">
                <div class="timeline-dot"></div>
                <div class="timeline-label">Pending</div>
                <div class="timeline-desc">Order placed, awaiting admin approval</div>
            </div>
            <div class="timeline-step <?php echo $step2; ?>">
                <div class="timeline-dot"></div>
                <div class="timeline-label">Accepted</div>
                <div class="timeline-desc">Admin approved your order</div>
            </div>
            <div class="timeline-step <?php echo $step3; ?>">
                <div class="timeline-dot"></div>
                <div class="timeline-label"><?php echo $st === "cancelled" ? "Cancelled" : "Rejected / Cancelled"; ?></div>
                <div class="timeline-desc"><?php echo $st === "cancelled" ? "Cancelled by you" : ($st === "rejected" ? "Rejected by admin" : "—"); ?></div>
            </div>
        </div>

        <div class="status-message-box">
            <strong>Status:</strong> <?php echo htmlspecialchars($currentMsg); ?>
        </div>

        <div class="order-details">
            <p><strong>Order Date:</strong> <?php echo date("d M Y, h:i A", strtotime($order["order_date"])); ?></p>
            <p><strong>Shipping Address:</strong> <?php echo htmlspecialchars($order["shipping_address"]); ?></p>
            <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($order["payment_method"] ?? "—"); ?></p>
            <?php if($payment){ ?>
                <p><strong>Transaction ID:</strong> <?php echo htmlspecialchars($payment["transaction_id"] ?? "—"); ?></p>
                <p><strong>Payment Date:</strong> <?php echo date("d M Y, h:i A", strtotime($payment["payment_date"])); ?></p>
            <?php } ?>
        </div>

        <div class="cart-table-wrapper">
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Medicine</th>
                        <th>Vendor</th>
                        <th>Qty</th>
                        <th>Unit Price (BDT)</th>
                        <th>Subtotal (BDT)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($item = $orderItems->fetch_assoc()){ ?>
                        <tr>
                            <td>
                                <a href="medicine_detail.php?id=<?php echo $item["medicine_id"]; ?>">
                                    <?php echo htmlspecialchars($item["medicine_name"]); ?>
                                </a>
                            </td>
                            <td><?php echo htmlspecialchars($item["vendor_name"]); ?></td>
                            <td><?php echo $item["quantity"]; ?></td>
                            <td><?php echo number_format($item["unit_price"], 2); ?></td>
                            <td><?php echo number_format($item["quantity"] * $item["unit_price"], 2); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="cart-total-label">Total:</td>
                        <td class="cart-total-value">BDT <?php echo number_format($order["total_amount"], 2); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="order-detail-footer" id="orderActionArea">
            <?php if($order["status"] === "pending"){ ?>
                <button type="button" class="btn-remove" onclick="cancelOrder(<?php echo $order["id"]; ?>)">Cancel Order</button>
            <?php } elseif(in_array($order["status"], array("accepted", "rejected"))){ ?>
                <button type="button" class="btn-primary" onclick="reorderPurchase(<?php echo $order["id"]; ?>)">Reorder</button>
            <?php } ?>
        </div>
    </div>

</div>

<script src="../js/task4_script.js"></script>
</body>
</html>
<?php $mydb->closeConn($conn); ?>
