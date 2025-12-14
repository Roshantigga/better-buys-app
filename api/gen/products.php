<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Origin, Content-Type, Accept');

require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../models/Product.php';

// Create Database & Product objects
$database = new Database();
$product  = new Product($database);

// Allow only GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode([
        'success' => 0,
        'message' => 'Method Not Allowed'
    ]);
    exit;
}

// Validate seller_id
if (!$product->validate_params($_GET['seller_id'] ?? null)) {
    echo json_encode([
        'success' => 0,
        'message' => 'Seller ID is required'
    ]);
    exit;
}

$seller_id = $_GET['seller_id'];

// Fetch products
$data = $product->get_products_by_seller($seller_id);

echo json_encode([
    'success'  => 1,
    'products' => $data
]);
