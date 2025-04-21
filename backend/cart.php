<?php
// Include database connection
require_once 'db_connect.php';

// Start session
session_start();

// Initialize cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Function to add item to cart
function addToCart($product_id, $quantity = 1) {
    global $conn;
    
    // Get product details
    $sql = "SELECT * FROM products WHERE id = " . (int)$product_id;
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $product = $result->fetch_assoc();
        
        // Check if product is already in cart
        $found = false;
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['id'] == $product_id) {
                $item['quantity'] += $quantity;
                $found = true;
                break;
            }
        }
        
        // If product is not in cart, add it
        if (!$found) {
            $_SESSION['cart'][] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'price' => $product['price'],
                'quantity' => $quantity,
                'image' => $product['image']
            ];
        }
        
        return true;
    }
    
    return false;
}

// Function to update cart item quantity
function updateCartItem($product_id, $quantity) {
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] == $product_id) {
            $item['quantity'] = $quantity;
            break;
        }
    }
}

// Function to remove item from cart
function removeFromCart($product_id) {
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['id'] == $product_id) {
            unset($_SESSION['cart'][$key]);
            $_SESSION['cart'] = array_values($_SESSION['cart']); // Re-index array
            break;
        }
    }
}

// Function to get cart contents
function getCart() {
    return $_SESSION['cart'];
}

// Function to clear cart
function clearCart() {
    $_SESSION['cart'] = [];
}

// Function to calculate cart total
function getCartTotal() {
    $total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    return $total;
}

// Handle API requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    // Get request data
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['action'])) {
        switch ($data['action']) {
            case 'add':
                $product_id = isset($data['product_id']) ? (int)$data['product_id'] : 0;
                $quantity = isset($data['quantity']) ? (int)$data['quantity'] : 1;
                
                $result = addToCart($product_id, $quantity);
                echo json_encode(['success' => $result]);
                break;
                
            case 'update':
                $product_id = isset($data['product_id']) ? (int)$data['product_id'] : 0;
                $quantity = isset($data['quantity']) ? (int)$data['quantity'] : 1;
                
                updateCartItem($product_id, $quantity);
                echo json_encode(['success' => true]);
                break;
                
            case 'remove':
                $product_id = isset($data['product_id']) ? (int)$data['product_id'] : 0;
                
                removeFromCart($product_id);
                echo json_encode(['success' => true]);
                break;
                
            case 'clear':
                clearCart();
                echo json_encode(['success' => true]);
                break;
                
            default:
                echo json_encode(['success' => false, 'message' => 'Invalid action']);
                break;
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'No action specified']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    header('Content-Type: application/json');
    
    if (isset($_GET['action'])  === 'GET') {
    header('Content-Type: application/json');
    
    if (isset($_GET['action'])) {
        switch ($_GET['action']) {
            case 'get':
                $cart = getCart();
                $total = getCartTotal();
                echo json_encode(['success' => true, 'cart' => $cart, 'total' => $total]);
                break;
                
            default:
                echo json_encode(['success' => false, 'message' => 'Invalid action']);
                break;
        }
    } 
}else {
        // Default to returning cart contents
        $cart = getCart();
        $total = getCartTotal();
        echo json_encode(['success' => true, 'cart' => $cart, 'total' => $total]);
    }
}
?>