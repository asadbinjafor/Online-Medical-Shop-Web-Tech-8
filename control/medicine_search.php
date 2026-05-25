<?php
include_once '../model/mydb.php';
header("Content-Type: application/json");

$q      = trim($_GET["q"] ?? "");
$vendor = trim($_GET["vendor"] ?? "");
$genre  = trim($_GET["genre"] ?? "");
$type   = trim($_GET["type"] ?? "");

if($type != "" && $type != "liquid" && $type != "solid"){
    http_response_code(400);
    echo json_encode(array("success"=>false, "message"=>"Invalid category type"));
    exit();
}

$mydb = new MyDB();
$conn = $mydb->createConn();

if($conn->connect_error){
    http_response_code(500);
    echo json_encode(array("success"=>false, "message"=>"Database connection failed"));
    exit();
}

$result   = $mydb->searchMedicines($q, $vendor, $genre, $type, $conn);
$medicines = array();

while($row = $result->fetch_assoc()){
    $medicines[] = array(
        "id"            => $row["id"],
        "name"          => htmlspecialchars($row["name"]),
        "vendor_name"   => htmlspecialchars($row["vendor_name"]),
        "price"         => $row["price"],
        "availability"  => $row["availability"],
        "description"   => htmlspecialchars($row["description"] ?? ""),
        "image_path"    => htmlspecialchars($row["image_path"] ?? ""),
        "category_name" => htmlspecialchars($row["category_name"] ?? ""),
        "category_type" => htmlspecialchars($row["category_type"] ?? "")
    );
}

echo json_encode(array("success"=>true, "medicines"=>$medicines));
$mydb->closeConn($conn);
?>
