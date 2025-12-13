<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Origin, Content-Type, Accept');

require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../models/Sellers.php';

// Initialize DB & Model
$database = new Database();
$seller   = new Seller($database);

// Allow only POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => 0,
        'message' => 'Method Not Allowed'
    ]);
    exit;
}

// Validate Email
if (!isset($_POST['email']) || trim($_POST['email']) === '') {
    http_response_code(400);
    echo json_encode([
        'success' => 0,
        'message' => 'Email is required'
    ]);
    exit;
}
$seller->email = $_POST['email'];

// Validate Password
if (!isset($_POST['password']) || trim($_POST['password']) === '') {
    http_response_code(400);
    echo json_encode([
        'success' => 0,
        'message' => 'Password is required'
    ]);
    exit;
}
$seller->password = $_POST['password'];

// Attempt login
$loginResult = $seller->login();

// Success
if (is_array($loginResult)) {
    http_response_code(200);
    echo json_encode([
        'success' => 1,
        'message' => 'Login successful',
        'data' => $loginResult
    ]);
    exit;
}

// Failure
http_response_code(401);
echo json_encode([
    'success' => 0,
    'message' => $loginResult
]);
