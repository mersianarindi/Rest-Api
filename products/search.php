<?php
include_once '../config/cors.php';
include_once '../config/database.php';
include_once '../objects/product.php';

$database = new Database();
$db = $database->getConnection();

$product = new Product($db);

// ambil keyword (pakai q biar standar API)
$keywords = $_GET['q'] ?? "";

$stmt = $product->search($keywords);

$data = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

    $data[] = [
        "id" => $row['id'],
        "name" => $row['name'],
        "description" => html_entity_decode($row['description']),
        "price" => $row['price'],
        "image_url" => $row['image_url'],
        "calories" => $row['calories']
    ];
}

if (count($data) > 0) {

    http_response_code(200);

    echo json_encode([
        "status" => "success",
        "data" => $data
    ]);

} else {

    http_response_code(404);

    echo json_encode([
        "status" => "error",
        "message" => "data_not_found"
    ]);
}
?>