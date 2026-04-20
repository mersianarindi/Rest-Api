<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';
include_once '../objects/product.php';

$database = new Database();
$db = $database->getConnection();
$product = new Product($db);

$product->id = isset($_GET['id']) ? $_GET['id'] : die();

if($product->readOne()) {
    $product_arr = array(
        "id" => $product->id,
        "name" => $product->name,
        "description" => $product->description,
        "price" => $product->price,
        "imageUrl" => $product->image_url,
        "calories" => $product->calories,
        "ingredients" => $product->ingredients
    );
    
    http_response_code(200);
    echo json_encode($product_arr);
} else {
    http_response_code(404);
    echo json_encode(array("message" => "Product does not exist."));
}
?>