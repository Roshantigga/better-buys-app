<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Origin, Content-Type, Accept');

require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../models/Product.php';

// Create Database & Product objects
$database = new Database();
$product  = new Product($database);

// Allow only POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => 0,
        'message' => 'Method Not Allowed'
    ]);
    exit;
}

/* -------------------- VALIDATION -------------------- */

// Seller ID
if ($product->validate_params($_POST['seller_id'] ?? null)) {
    $product->seller_id = $_POST['seller_id'];
} else {
    echo json_encode(['success' => 0, 'message' => 'Seller ID is required']);
    exit;
}

// Product name
if ($product->validate_params($_POST['name'] ?? null)) {
    $product->name = $_POST['name'];
} else {
    echo json_encode(['success' => 0, 'message' => 'Product name is required']);
    exit;
}

// Price per kg
if ($product->validate_params($_POST['price_per_kg'] ?? null)) {
    $product->price_per_kg = $_POST['price_per_kg'];
} else {
    echo json_encode(['success' => 0, 'message' => 'Price per kg is required']);
    exit;
}

// Description
if ($product->validate_params($_POST['description'] ?? null)) {
    $product->description = $_POST['description'];
} else {
    echo json_encode(['success' => 0, 'message' => 'Description is required']);
    exit;
}

/* -------------------- IMAGE UPLOAD -------------------- */

$product_images_folder = __DIR__ . '/../../assets/product_images/';

if (!is_dir($product_images_folder)) {
    mkdir($product_images_folder, 0777, true);
}

if (isset($_FILES['image']) && !empty($_FILES['image']['name'])) {

    $file_name = $_FILES['image']['name'];
    $file_tmp  = $_FILES['image']['tmp_name'];
    $extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    // Allow only images
    $allowed_extensions = ['jpg', 'jpeg', 'png'];
    if (!in_array($extension, $allowed_extensions)) {
        echo json_encode(['success' => 0, 'message' => 'Only JPG, JPEG, PNG images allowed']);
        exit;
    }

    // Safe product name
    $safe_name = preg_replace('/[^a-zA-Z0-9]/', '_', $product->name);

    // Unique file name
    $new_file_name = time() . "_product_" . $safe_name . "." . $extension;

    move_uploaded_file($file_tmp, $product_images_folder . $new_file_name);

    // Save relative path
    $product->image = "product_images/" . $new_file_name;

} else {
    echo json_encode(['success' => 0, 'message' => 'Product image is required']);
    exit;
}

/* -------------------- INSERT PRODUCT -------------------- */

if ($product->add_product()) {
    echo json_encode([
        'success' => 1,
        'message' => 'Product added successfully'
    ]);
} else {
    echo json_encode([
        'success' => 0,
        'message' => 'Failed to add product'
    ]);
}
