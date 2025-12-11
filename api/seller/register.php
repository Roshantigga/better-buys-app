<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Origin, Content-Type, Accept');

include_once '../../models/Sellers.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Name
    if ($seller->validate_params($_POST['name'] ?? null)) {
        $seller->name = $_POST['name'];
    } else {
        echo json_encode(['success' => 0, 'message' => 'Name is required!']);
        die();
    }

    // Email
    if ($seller->validate_params($_POST['email'] ?? null)) {
        $seller->email = $_POST['email'];
    } else {
        echo json_encode(['success' => 0, 'message' => 'Email is required!']);
        die();
    }

    // Password
    if ($seller->validate_params($_POST['password'] ?? null)) {
        $seller->password = $_POST['password'];
    } else {
        echo json_encode(['success' => 0, 'message' => 'Password is required!']);
        die();
    }

    // Saving picture
    $seller_images_folder = '../../assets/seller_images/';

    if (!is_dir($seller_images_folder)) {
        mkdir($seller_images_folder, 0777, true);
    }

    if (isset($_FILES['image'])) {

        $file_name = $_FILES['image']['name'];
        $file_tmp  = $_FILES['image']['tmp_name'];

        // FIXED extension extraction
        $extension = pathinfo($file_name, PATHINFO_EXTENSION);

        $new_file_name = $seller->email . "_profile." . $extension;

        move_uploaded_file($file_tmp, $seller_images_folder . $new_file_name);

        // Store relative path
        $seller->image = "seller_images/" . $new_file_name;
    }

    // Read JSON input (if any)
$input = json_decode(file_get_contents("php://input"), true);

// Get address from either POST (form-data) or JSON
$address = $_POST['address'] 
           ?? $input['address'] 
           ?? null;

// Validate
if ($seller->validate_params($address)) {
    $seller->address = $address;
} else {
    echo json_encode(['success' => 0, 'message' => 'Address is required!']);
    die();
}


    // Description
    if ($seller->validate_params($_POST['description'] ?? null)) {
        $seller->description = $_POST['description'];
    } else {
        echo json_encode(['success' => 0, 'message' => 'Description is required!']);
        die();
    }

    // Email uniqueness check
    if ($seller->check_unique_email()) {

        if ($seller->register_seller()) {
            echo json_encode(['success' => 1, 'message' => 'Seller registered!']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => 0, 'message' => 'Internal Server Error']);
        }

    } else {
        http_response_code(401);
        echo json_encode(['success' => 0, 'message' => 'Email already exists!']);
    }

} else {
    header('HTTP/1.1 405 Method Not Allowed');
}
