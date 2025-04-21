<?php
session_start();
require_once 'backend/db_connect.php'; // Include your database connection file

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'User not logged in.'
    ]);
    exit();
}

// Get cart data from the request
$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['cart']) || !isset($data['total']) || !is_array($data['cart']) || !is_numeric($data['total'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request data.'
    ]);
    exit();
}

$userId = $_SESSION['user_id'];
$cart = json_encode($data['cart']); // Convert cart array to JSON
$totalAmount = $data['total'];
$orderDate = date('Y-m-d H:i:s'); // Current date and time
$orderStatus = 'Pending'; // Default order status

// Insert order into the database
$sql = "INSERT INTO orders (user_id, items, total_amount, order_date, status) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $conn->error
    ]);
    exit();
}
$stmt->bind_param("isdss", $userId, $cart, $totalAmount, $orderDate, $orderStatus);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Order placed successfully.'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to place order: ' . $stmt->error
    ]);
}
?>