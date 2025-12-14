<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Origin, Content-Type, Accept');

require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../models/Sellers.php';

// Create Database & Seller objects
$database = new Database();
$seller   = new Seller($database);

// Allow only POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => 0, 'message' => 'Method Not Allowed']);
    exit;
}

/* -------------------- VALIDATION -------------------- */

// Name
if ($seller->validate_params($_POST['name'] ?? null)) {
    $seller->name = $_POST['name'];
} else {
    echo json_encode(['success' => 0, 'message' => 'Name is required']);
    exit;
}

// Email
if ($seller->validate_params($_POST['email'] ?? null)) {
    $seller->email = $_POST['email'];
} else {
    echo json_encode(['success' => 0, 'message' => 'Email is required']);
    exit;
}

// Password
if ($seller->validate_params($_POST['password'] ?? null)) {
    $seller->password = $_POST['password'];
} else {
    echo json_encode(['success' => 0, 'message' => 'Password is required']);
    exit;
}

// Address
if ($seller->validate_params($_POST['address'] ?? null)) {
    $seller->address = $_POST['address'];
} else {
    echo json_encode(['success' => 0, 'message' => 'Address is required']);
    exit;
}

// Description
if ($seller->validate_params($_POST['description'] ?? null)) {
    $seller->description = $_POST['description'];
} else {
    echo json_encode(['success' => 0, 'message' => 'Description is required']);
    exit;
}

/* -------------------- IMAGE UPLOAD -------------------- */

$seller_images_folder = __DIR__ . '/../../assets/seller_images/';

if (!is_dir($seller_images_folder)) {
    mkdir($seller_images_folder, 0777, true);
}

if (isset($_FILES['image']) && !empty($_FILES['image']['name'])) {

    $file_name = $_FILES['image']['name'];
    $file_tmp  = $_FILES['image']['tmp_name'];
    $extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    // Allow only images
    $allowed = ['jpg', 'jpeg', 'png'];
    if (!in_array($extension, $allowed)) {
        echo json_encode(['success' => 0, 'message' => 'Only JPG, JPEG, PNG images allowed']);
        exit;
    }

    // Safe email for filename
    $safe_email = preg_replace('/[^a-zA-Z0-9]/', '_', $seller->email);

    $new_file_name = time() . "_seller_" . $safe_email . "." . $extension;

    move_uploaded_file($file_tmp, $seller_images_folder . $new_file_name);

    $seller->image = "seller_images/" . $new_file_name;

} else {
    $seller->image = null;
}

/* -------------------- REGISTER SELLER -------------------- */

if ($seller->check_unique_email()) {

    if ($seller->register_seller()) {
        echo json_encode(['success' => 1, 'message' => 'Seller registered successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => 0, 'message' => 'Failed to register seller']);
    }

} else {
    http_response_code(409);
    echo json_encode(['success' => 0, 'message' => 'Email already exists']);
}
