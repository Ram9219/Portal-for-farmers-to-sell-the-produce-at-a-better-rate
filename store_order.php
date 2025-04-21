<?php
// Enable error reporting for debugging (disable in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

session_start();
require_once 'backend/db_connect.php';

// Log received data for debugging
file_put_contents('debug.log', "\n\n" . date('Y-m-d H:i:s') . " - New Request\n", FILE_APPEND);
file_put_contents('debug.log', "POST Data: " . print_r($_POST, true) . "\n", FILE_APPEND);
file_put_contents('debug.log', "SESSION Data: " . print_r($_SESSION, true) . "\n", FILE_APPEND);

// Get user ID from session
$userId = $_SESSION['user_id'] ?? null;
$shippingAddress = $_POST['shipping_address'] ?? null;
$total = $_POST['total'] ?? 0;
$itemsJson = $_POST['items'] ?? null;

// Validate input
if (!$userId) {
    file_put_contents('debug.log', "Error: User not authenticated\n", FILE_APPEND);
    echo json_encode(['success' => false, 'message' => 'User not authenticated. Please log in.']);
    exit();
}

if (!$shippingAddress || strlen(trim($shippingAddress)) < 10) {
    file_put_contents('debug.log', "Error: Invalid shipping address\n", FILE_APPEND);
    echo json_encode(['success' => false, 'message' => 'Please enter a valid shipping address (at least 10 characters).']);
    exit();
}

if (!is_numeric($total) || $total <= 0) {
    file_put_contents('debug.log', "Error: Invalid total amount: $total\n", FILE_APPEND);
    echo json_encode(['success' => false, 'message' => 'Invalid order total.']);
    exit();
}

if (!$itemsJson) {
    file_put_contents('debug.log', "Error: No items received\n", FILE_APPEND);
    echo json_encode(['success' => false, 'message' => 'Your cart is empty.']);
    exit();
}

// Decode cart items
$items = json_decode($itemsJson, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    file_put_contents('debug.log', "Error: JSON decode failed - " . json_last_error_msg() . "\n", FILE_APPEND);
    echo json_encode(['success' => false, 'message' => 'Invalid cart data format.']);
    exit();
}

if (empty($items)) {
    file_put_contents('debug.log', "Error: Empty items array\n", FILE_APPEND);
    echo json_encode(['success' => false, 'message' => 'Your cart is empty.']);
    exit();
}

// Sanitize and validate items
foreach ($items as $item) {
    if (!isset($item['id'], $item['name'], $item['price'], $item['quantity']) || 
        !is_numeric($item['price']) || !is_numeric($item['quantity']) || $item['quantity'] <= 0) {
        file_put_contents('debug.log', "Error: Invalid item structure\n", FILE_APPEND);
        echo json_encode(['success' => false, 'message' => 'Invalid item in cart.']);
        exit();
    }
}

// Insert the order into the database
try {
    $conn->begin_transaction();
    
    $stmt = $conn->prepare("INSERT INTO orders (user_id, shipping_address, total_amount, items, status) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $status = 'Pending';
    $itemsJson = json_encode($items, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE);
    $stmt->bind_param("issss", $userId, $shippingAddress, $total, $itemsJson, $status);
    
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $orderId = $stmt->insert_id;
    $stmt->close();
    
    $conn->commit();
    
    file_put_contents('debug.log', "Success: Order created - ID: $orderId\n", FILE_APPEND);
    echo json_encode([
        'success' => true, 
        'message' => 'Order stored successfully.', 
        'order_id' => $orderId
    ]);
    
} catch (Exception $e) {
    $conn->rollback();
    file_put_contents('debug.log', "Database Error: " . $e->getMessage() . "\n", FILE_APPEND);
    echo json_encode([
        'success' => false, 
        'message' => 'Failed to store order.',
        'error_details' => $e->getMessage()
    ]);
}
?>