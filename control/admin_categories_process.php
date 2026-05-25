<?php
include_once '../model/mydb.php';
include_once 'auth.php';
include_once 'admin_gate.php';

if(session_status() == PHP_SESSION_NONE){ session_start(); }
requireAdmin();

$mydb    = new MyDB();
$conn    = $mydb->createConn();
$errors  = array();
$success = "";
$editCat = null;


if(isset($_GET["edit"])){
    $editId  = (int)$_GET["edit"];
    $result  = $mydb->getCategoryById($editId, $conn);
    if($result->num_rows > 0){
        $editCat = $result->fetch_assoc();
    }
}


if(isset($_POST["add_category"])){
    $name = trim($_POST["cat_name"] ?? "");
    $type = trim($_POST["cat_type"] ?? "");

    if($name == ""){
        $errors["cat_name"] = "Category name is required";
    }
    if(!in_array($type, array("liquid", "solid"))){
        $errors["cat_type"] = "Please select liquid or solid";
    }
    if($name != "" && empty($errors)){
        $exists = $mydb->categoryNameExists($name, $conn);
        if($exists->num_rows > 0){
            $errors["cat_name"] = "A category with this name already exists";
        }
    }
    if(empty($errors)){
        if($mydb->createCategory($name, $type, $conn)){
            $success = "Category added successfully";
        } else {
            $errors["database"] = "Failed to add category. Please try again.";
        }
    }
}


if(isset($_POST["edit_category"])){
    $editId = (int)($_POST["edit_id"] ?? 0);
    $name   = trim($_POST["cat_name"] ?? "");
    $type   = trim($_POST["cat_type"] ?? "");

    if($name == ""){
        $errors["cat_name"] = "Category name is required";
    }
    if(!in_array($type, array("liquid", "solid"))){
        $errors["cat_type"] = "Please select liquid or solid";
    }
    if($name != "" && empty($errors)){
        $exists = $mydb->categoryNameExistsForOther($name, $editId, $conn);
        if($exists->num_rows > 0){
            $errors["cat_name"] = "A category with this name already exists";
        }
    }
    if(empty($errors)){
        if($mydb->updateCategory($editId, $name, $type, $conn)){
            $success = "Category updated successfully";
            $editCat = null;
        } else {
            $errors["database"] = "Failed to update category. Please try again.";
        }
    } else {
        $result  = $mydb->getCategoryById($editId, $conn);
        $editCat = $result->num_rows > 0 ? $result->fetch_assoc() : null;
    }
}


if(isset($_POST["delete_category"])){
    $delId = (int)($_POST["cat_id"] ?? 0);
    if($mydb->medicinesExistInCategory($delId, $conn)){
        $errors["delete"] = "Cannot delete: medicines exist under this category";
    } else {
        if($mydb->deleteCategory($delId, $conn)){
            $success = "Category deleted successfully";
        } else {
            $errors["delete"] = "Failed to delete category. Please try again.";
        }
    }
}

$categories = $mydb->getCategories($conn);
?>
