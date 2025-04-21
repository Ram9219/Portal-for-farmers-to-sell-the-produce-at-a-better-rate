<?php
// Include database connection
require_once 'db_connect.php';

// Function to get all products
function getAllProducts($limit = 0, $offset = 0, $category = '', $sort = 'newest') {
    global $conn;
    
    // Base query
    $sql = "SELECT * FROM products WHERE 1=1";
    
    // Add category filter if provided
    if (!empty($category)) {
        $sql .= " AND category = '" . $conn->real_escape_string($category) . "'";
    }
    
    // Add sorting
    switch ($sort) {
        case 'price-low':
            $sql .= " ORDER BY price ASC";
            break;
        case 'price-high':
            $sql .= " ORDER BY price DESC";
            break;
        case 'popularity':
            $sql .= " ORDER BY sales_count DESC";
            break;
        case 'newest':
        default:
            $sql .= " ORDER BY created_at DESC";
            break;
    }
    
    // Add limit and offset if provided
    if ($limit > 0) {
        $sql .= " LIMIT " . (int)$offset . ", " . (int)$limit;
    }
    
    $result = $conn->query($sql);
    $products = [];
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
    }
    
    return $products;
}

// Function to get a single product by ID
function getProductById($id) {
    global $conn;
    
    $sql = "SELECT * FROM products WHERE id = " . (int)$id;
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    return null;
}

// Function to get featured products
function getFeaturedProducts($limit = 4) {
    global $conn;
    
    $sql = "SELECT * FROM products WHERE is_featured = 1 ORDER BY created_at DESC LIMIT " . (int)$limit;
    $result = $conn->query($sql);
    $products = [];
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
    }
    
    return $products;
}

// Function to search products
function searchProducts($keyword) {
    global $conn;
    
    $keyword = $conn->real_escape_string($keyword);
    $sql = "SELECT * FROM products WHERE name LIKE '%$keyword%' OR description LIKE '%$keyword%'";
    $result = $conn->query($sql);
    $products = [];
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
    }
    
    return $products;
}

// Handle API requests
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    header('Content-Type: application/json');
    
    if (isset($_GET['id'])) {
        // Get single product
        $product = getProductById($_GET['id']);
        echo json_encode($product);
    } elseif (isset($_GET['featured'])) {
        // Get featured products
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 4;
        $products = getFeaturedProducts($limit);
        echo json_encode($products);
    } elseif (isset($_GET['search'])) {
        // Search products
        $products = searchProducts($_GET['search']);
        echo json_encode($products);
    } else {
        // Get all products with optional filters
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 0;
        $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
        $category = isset($_GET['category']) ? $_GET['category'] : '';
        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
        
        $products = getAllProducts($limit, $offset, $category, $sort);
        echo json_encode($products);
    }
}
?>