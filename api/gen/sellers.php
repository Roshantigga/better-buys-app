<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Origin, Content-Type, Accept');

require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../models/Sellers.php';

// Create Database & Seller objects
$database = new Database();
$seller   = new Seller($database);

// Allow only GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode([
        'success' => 0,
        'message' => 'Method Not Allowed'
    ]);
    exit;
}

// Fetch sellers
$data = $seller->all_sellers();

echo json_encode([
    'success' => 1,
    'sellers' => $data
]);
