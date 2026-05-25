<?php
include '../control/home_process.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - MediShop</title>
    <link rel="stylesheet" type="text/css" href="../css/task1_style.css">
    <?php if(isset($_SESSION["user_id"]) && $_SESSION["role"] === "customer"){ ?>
    <link rel="stylesheet" type="text/css" href="../css/task3_style.css">
    <link rel="stylesheet" type="text/css" href="../css/task4_style.css">
    <?php } ?>
</head>
<body>

<nav>
    <div class="nav-inner">
        <a class="nav-brand" href="Home.php">MediShop</a>
        <div class="nav-links">
            <a href="Home.php">Home</a>
            <?php if(isset($_SESSION["user_id"])){ ?>
                <?php if($_SESSION["role"] === "customer"){ ?>
                    <a href="cart.php">Cart (<span id="navCartCount"><?php echo $mydb->getCartCount($_SESSION["user_id"], $conn); ?></span>)</a>
                    <a href="customer_orders.php">My Orders</a>
                <?php } ?>
                <a href="profile.php">Profile</a>
                <?php if($_SESSION["role"] === "admin"){ ?>
                    <a href="admin_dashboard.php">Admin Dashboard</a>
                <?php } ?>
                <a href="../control/logout_process.php">Logout</a>
                <span class="nav-user-info">Hi, <?php echo htmlspecialchars($_SESSION["name"]); ?></span>
            <?php } else { ?>
                <a href="login.php">Login</a>
                <a href="Registration.php">Register</a>
            <?php } ?>
        </div>
    </div>
</nav>

<div class="page-header">
    <h1>Online Medicine Shop</h1>
    <p>Browse medicines by category, vendor and name</p>
</div>

<div class="main-container">

    <div class="filters-bar">
        <div class="filter-item">
            <label for="searchText">Search Medicine</label>
            <input type="text" id="searchText" placeholder="Type medicine name..." onkeyup="searchMedicines()">
        </div>
        <div class="filter-item">
            <label for="vendorFilter">Vendor</label>
            <select id="vendorFilter" onchange="searchMedicines()">
                <option value="">All Vendors</option>
                <?php while($vendor = $vendors->fetch_assoc()){ ?>
                    <option value="<?php echo htmlspecialchars($vendor["vendor_name"]); ?>">
                        <?php echo htmlspecialchars($vendor["vendor_name"]); ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div class="filter-item">
            <label for="genreFilter">Genre</label>
            <select id="genreFilter" onchange="searchMedicines()">
                <option value="">All Genres</option>
                <?php
                $categoriesForFilter = $mydb->getCategories($conn);
                while($cat = $categoriesForFilter->fetch_assoc()){
                ?>
                    <option value="<?php echo htmlspecialchars($cat["name"]); ?>">
                        <?php echo htmlspecialchars($cat["name"]); ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div class="filter-item">
            <label for="typeFilter">Type</label>
            <select id="typeFilter" onchange="searchMedicines()">
                <option value="">All Types</option>
                <option value="liquid">Liquid</option>
                <option value="solid">Solid</option>
            </select>
        </div>
    </div>

    <h2 class="section-title">Browse by Category</h2>
    <div class="category-list">
        <a class="category-link <?php if(!isset($_GET["category_id"]) && !isset($_GET["type"])) echo "active"; ?>" href="Home.php">All</a>
        <a class="category-link <?php if(isset($_GET["type"]) && $_GET["type"]=="liquid") echo "active"; ?>" href="Home.php?type=liquid">Liquid</a>
        <a class="category-link <?php if(isset($_GET["type"]) && $_GET["type"]=="solid") echo "active"; ?>" href="Home.php?type=solid">Solid</a>
        <?php while($category = $categories->fetch_assoc()){ ?>
            <a class="category-link <?php if(isset($_GET["category_id"]) && $_GET["category_id"]==$category["id"]) echo "active"; ?>"
               href="Home.php?category_id=<?php echo $category["id"]; ?>">
                <?php echo htmlspecialchars($category["name"]); ?>
                (<?php echo htmlspecialchars($category["category_type"]); ?>)
            </a>
        <?php } ?>
    </div>

    <h2 class="section-title">Medicines</h2>
    <div id="medicineList" class="medicine-grid">
        <?php if($medicines->num_rows == 0){ ?>
            <div class="no-results">
                <div>No medicines found.</div>
            </div>
        <?php } ?>

        <?php while($medicine = $medicines->fetch_assoc()){ ?>
            <div class="medicine-card" data-medicine-id="<?php echo $medicine["id"]; ?>">
                <h3><a href="medicine_detail.php?id=<?php echo $medicine["id"]; ?>"><?php echo htmlspecialchars($medicine["name"]); ?></a></h3>
                <span class="badge badge-<?php echo htmlspecialchars($medicine["category_type"] ?? "solid"); ?>">
                    <?php echo htmlspecialchars($medicine["category_type"] ?? ""); ?>
                </span>
                <p>Genre: <?php echo htmlspecialchars($medicine["category_name"] ?? ""); ?></p>
                <p>Vendor: <?php echo htmlspecialchars($medicine["vendor_name"]); ?></p>
                <p>Availability: <?php echo htmlspecialchars($medicine["availability"]); ?> units</p>
                <p class="medicine-price">BDT <?php echo htmlspecialchars($medicine["price"]); ?></p>
                <a class="btn-link" href="medicine_detail.php?id=<?php echo $medicine["id"]; ?>">View Details</a>
                <?php if(isset($_SESSION["user_id"]) && $_SESSION["role"] === "customer" && $medicine["availability"] > 0){ ?>
                    <div class="add-cart-form">
                        <input type="number" class="add-cart-qty" id="qty-input-<?php echo $medicine["id"]; ?>"
                               value="1" min="1" max="<?php echo $medicine["availability"]; ?>"
                               data-stock="<?php echo $medicine["availability"]; ?>">
                        <button class="btn-add-cart" onclick="addToCart(<?php echo $medicine["id"]; ?>)">Add to Cart</button>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    </div>

</div>

<script src="../js/task1_script.js"></script>
<?php if(isset($_SESSION["user_id"]) && $_SESSION["role"] === "customer"){ ?>
<script src="../js/task3_script.js"></script>
<script src="../js/task4_script.js"></script>
<?php } ?>
</body>
</html>
<?php $mydb->closeConn($conn); ?>
