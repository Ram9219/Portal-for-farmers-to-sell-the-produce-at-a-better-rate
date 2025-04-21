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

// Fetch user details from the database
$userId = $_SESSION['user_id'];
$sql = "SELECT id, name, email, phone FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    // Fetch order history
    $orderSql = "SELECT id, items, total_amount, order_date, status FROM orders WHERE user_id = ? ORDER BY order_date DESC";
    $orderStmt = $conn->prepare($orderSql);
    $orderStmt->bind_param("i", $userId);
    $orderStmt->execute();
    $orderResult = $orderStmt->get_result();

    $orders = [];
    while ($order = $orderResult->fetch_assoc()) {
        $order['items'] = json_decode($order['items'], true); // Decode JSON items
        $orders[] = $order;
    }

    echo json_encode([
        'success' => true,
        'user' => $user,
        'orders' => $orders
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'User not found.'
    ]);
}
?>