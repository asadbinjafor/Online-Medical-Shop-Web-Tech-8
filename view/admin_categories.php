<?php
include '../control/admin_categories_process.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category Management - MediShop</title>
    <link rel="stylesheet" type="text/css" href="../css/task2_style.css">
</head>
<body>

<nav>
    <div class="nav-inner">
        <a class="nav-brand" href="admin_dashboard.php">MediShop Admin</a>
        <div class="nav-links">
            <a href="admin_dashboard.php">Dashboard</a>
            <a href="admin_categories.php">Categories</a>
            <a href="admin_medicine.php">Medicines</a>
            <a href="admin_customers.php">Customers</a>
            <a href="admin_orders.php">Orders</a>
            <a href="admin_history.php">History</a>
            <a href="Home.php">Home</a>
            <a href="../control/logout_process.php">Logout</a>
            <span class="nav-user-info">Admin: <?php echo htmlspecialchars($_SESSION["name"]); ?></span>
        </div>
    </div>
</nav>

<div class="page-header">
    <h1>Category Management</h1>
    <p>Add, edit and delete medicine categories</p>
</div>

<div class="main-container">

    <?php if(!empty($errors["delete"])){ ?>
        <div class="msg-error"><?php echo htmlspecialchars($errors["delete"]); ?></div>
    <?php } ?>
    <?php if(!empty($errors["database"])){ ?>
        <div class="msg-error"><?php echo htmlspecialchars($errors["database"]); ?></div>
    <?php } ?>
    <?php if($success != ""){ ?>
        <div class="msg-success"><?php echo htmlspecialchars($success); ?></div>
    <?php } ?>

    <div class="card">
        <h2 class="section-title"><?php echo $editCat ? "Edit Category" : "Add New Category"; ?></h2>

        <form action="" method="post" onsubmit="return validateCategoryForm()">
            <?php if($editCat){ ?>
                <input type="hidden" name="edit_id" value="<?php echo $editCat["id"]; ?>">
            <?php } ?>

            <div class="form-group">
                <label for="cat_name">Category Name</label>
                <input type="text" id="cat_name" name="cat_name"
                       value="<?php echo htmlspecialchars($editCat ? $editCat["name"] : ($_POST["cat_name"] ?? "")); ?>"
                       placeholder="e.g. Aspirin genre">
                <span class="error"><?php echo $errors["cat_name"] ?? ""; ?></span>
            </div>

            <div class="form-group">
                <label for="cat_type">Type</label>
                <select id="cat_type" name="cat_type">
                    <option value="">-- Select Type --</option>
                    <?php
                    $selType = $editCat ? $editCat["category_type"] : ($_POST["cat_type"] ?? "");
                    ?>
                    <option value="liquid" <?php if($selType == "liquid") echo "selected"; ?>>Liquid</option>
                    <option value="solid"  <?php if($selType == "solid")  echo "selected"; ?>>Solid</option>
                </select>
                <span class="error"><?php echo $errors["cat_type"] ?? ""; ?></span>
            </div>

            <div class="form-group" style="display:flex; gap:10px;">
                <?php if($editCat){ ?>
                    <input type="submit" name="edit_category" value="Update Category">
                    <a class="btn-cancel" href="admin_categories.php">Cancel</a>
                <?php } else { ?>
                    <input type="submit" name="add_category" value="Add Category">
                <?php } ?>
            </div>
        </form>
    </div>

    <h2 class="section-title">All Categories</h2>

    <?php if($categories->num_rows == 0){ ?>
        <div class="no-results">No categories found. Add one above.</div>
    <?php } else { ?>
        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; while($cat = $categories->fetch_assoc()){ ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo htmlspecialchars($cat["name"]); ?></td>
                            <td>
                                <span class="badge badge-<?php echo htmlspecialchars($cat["category_type"]); ?>">
                                    <?php echo htmlspecialchars($cat["category_type"]); ?>
                                </span>
                            </td>
                            <td><?php echo date("d M Y", strtotime($cat["created_at"])); ?></td>
                            <td>
                                <a class="btn-action btn-edit-sm"
                                   href="admin_categories.php?edit=<?php echo $cat["id"]; ?>">Edit</a>

                                <form action="" method="post" style="display:inline;"
                                      onsubmit="return confirmDelete('category')">
                                    <input type="hidden" name="cat_id" value="<?php echo $cat["id"]; ?>">
                                    <button type="submit" name="delete_category" class="btn-action btn-delete-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } ?>

</div>

<script src="../js/task2_script.js"></script>
</body>
</html>
<?php $mydb->closeConn($conn); ?>
