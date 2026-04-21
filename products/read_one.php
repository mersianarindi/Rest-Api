<?php
include_once '../config/cors.php';
include_once '../config/database.php';
include_once '../objects/product.php';

$database = new Database();
$db = $database->getConnection();

$product = new Product($db);

// ambil id dari URL
$product->id = $_GET['id'] ?? 0;

// cek data
if ($product->read_one()) {

    http_response_code(200);

    echo json_encode([
        "status" => "success",
        "data" => [
            "id" => $product->id,
            "name" => $product->name,
            "description" => $product->description,
            "price" => $product->price,
            "image_url" => $product->image_url,
            "calories" => $product->calories,
            "ingredients" => $product->ingredients
        ]
    ]);

} else {

    http_response_code(404);

    echo json_encode([
        "status" => "error",
        "message" => "data_not_found"
    ]);
}
?>