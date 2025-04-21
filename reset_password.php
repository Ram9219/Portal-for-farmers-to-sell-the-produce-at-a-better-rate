<?php
require_once 'backend/db_connect.php'; // Include the database connection file
session_start();

// Check if the token is provided in the URL
if (isset($_GET['token'])) {
    $token = htmlspecialchars($_GET['token']); // Sanitize the token

    // Validate the token
    $sql = "SELECT id, reset_token_expiry FROM users WHERE reset_token = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Check if the token has expired
        $expiry = new DateTime($user['reset_token_expiry']);
        $now = new DateTime();

        if ($expiry > $now) {
            // Token is valid, handle password reset
            if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['password'], $_POST['confirm_password'])) {
                if ($_POST['password'] !== $_POST['confirm_password']) {
                    $error = "Passwords do not match.";
                } else {
                    $newPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);

                    // Update the password in the database
                    $updateSql = "UPDATE users SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE id = ?";
                    $updateStmt = $conn->prepare($updateSql);
                    $updateStmt->bind_param("si", $newPassword, $user['id']);
                    $updateStmt->execute();

                    // Set success message
                    $success = "Your password has been reset successfully.";
                }
            }
        } else {
            $error = "The reset token has expired. Please request a new password reset.";
        }
    } else {
        $error = "Invalid reset token.";
    }
} else {
    $error = "No reset token provided.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<header class="sticky top-0 z-50 w-full bg-white shadow-md">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <!-- Logo -->
                <a href="index.php" class="flex items-center">
                    <i class="fas fa-seedling text-primary-600 text-2xl mr-2"></i>
                    <span class="text-xl font-bold text-primary-700">FarmerSupply</span>
                </a>
                
                <!-- Desktop Navigation -->
                <nav class="hidden md:flex space-x-8">
                    <a href="index.php" class="text-gray-700 hover:text-primary-600 font-medium">Home</a>
                    <a href="products.html" class="text-gray-700 hover:text-primary-600 font-medium">Products</a>
                    <a href="about.html" class="text-gray-700 hover:text-primary-600 font-medium">About Us</a>
                    <a href="contact.html" class="text-gray-700 hover:text-primary-600 font-medium">Contact</a>
                </nav>
                
                <!-- User Actions -->
                <div class="flex items-center space-x-4">
                    <!-- Search -->
                    <div class="hidden md:block relative">
                        <input type="text" placeholder="Search products..." class="pl-8 pr-4 py-1 rounded-full border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <i class="fas fa-search absolute left-3 top-2 text-gray-400"></i>
                    </div>
                    
                    <!-- Cart -->
                    <div class="relative">
                        <a href="cart.html" class="text-gray-700 hover:text-primary-600">
                            <i class="fas fa-shopping-cart text-xl"></i>
                            <span id="cart-count" class="absolute -top-2 -right-2 bg-primary-600 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">0</span>
                        </a>
                    </div>
                    
                    <!-- User Account -->
                    <div class="relative group">
                        <button class="text-primary-600 focus:outline-none">
                            <i class="fas fa-user text-xl"></i>
                        </button>
                        <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 hidden group-hover:block">
                            <div id="logged-out-menu">
                                <a href="login.php" class="block px-4 py-2 text-sm text-primary-600 bg-primary-50" onclick="toggleForm('login')">Login</a>
                                <a href="login.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-primary-50" onclick="toggleForm('register')">Register</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <div class="flex justify-center items-center min-h-screen bg-gray-100">
        <div class="bg-white shadow-md rounded-lg p-8 w-full max-w-md">
            <h1 class="text-2xl font-bold text-center text-primary-700 mb-6">Reset Password</h1>
            <?php if (isset($error)): ?>
                <p class="text-red-500 text-center mb-4"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <?php if (isset($success)): ?>
                <p class="text-green-500 text-center mb-4"><?php echo htmlspecialchars($success); ?></p>
                <div class="text-center">
                    <a href="login.php" class="bg-primary-600 text-white py-2 px-4 rounded-md hover:bg-primary-700">Go to Login</a>
                </div>
            <?php else: ?>
                <form action="" method="post" class="space-y-4">
                    <div>
                        <label for="password" class="block text-gray-700 font-medium">New Password:</label>
                        <input type="password" id="password" name="password" class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500" required>
                    </div>
                    <div>
                        <label for="confirm_password" class="block text-gray-700 font-medium">Confirm Password:</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500" required>
                    </div>
                    <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-primary-700">Reset Password</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
    <footer class="bg-gray-800 text-white pt-12 pb-6 mt-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center mb-4">
                        <i class="fas fa-seedling text-primary-400 text-2xl mr-2"></i>
                        <span class="text-xl font-bold">FarmerSupply</span>
                    </div>
                    <p class="text-gray-400 mb-4">Your trusted partner for quality agricultural supplies.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-primary-400"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-gray-400 hover:text-primary-400"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-gray-400 hover:text-primary-400"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-gray-400 hover:text-primary-400"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="index.php" class="text-gray-400 hover:text-primary-400">Home</a></li>
                        <li><a href="products.html" class="text-gray-400 hover:text-primary-400">Products</a></li>
                        <li><a href="about.html" class="text-gray-400 hover:text-primary-400">About Us</a></li>
                        <li><a href="contact.html" class="text-gray-400 hover:text-primary-400">Contact</a></li>
                        <li><a href="blog.html" class="text-gray-400 hover:text-primary-400">Blog</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Product Categories</h3>
                    <ul class="space-y-2">
                        <li><a href="products.html?category=seeds" class="text-gray-400 hover:text-primary-400">Seeds</a></li>
                        <li><a href="products.html?category=pesticides" class="text-gray-400 hover:text-primary-400">Pesticides</a></li>
                        <li><a href="products.html?category=fertilizers" class="text-gray-400 hover:text-primary-400">Fertilizers</a></li>
                        <li><a href="products.html?category=tools" class="text-gray-400 hover:text-primary-400">Tools & Equipment</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Contact Us</h3>
                    <ul class="space-y-2">
                    <li class="flex items-start">
                            <i class="fas fa-map-marker-alt text-primary-400 mt-1 mr-2"></i>
                            <span class="text-gray-400">Lovely Professional University . Jalandhar Punjab .</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-phone text-primary-400 mt-1 mr-2"></i>
                            <span class="text-gray-400">+91 9219447763</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-envelope text-primary-400 mt-1 mr-2"></i>
                            <span class="text-gray-400">ramkumar977@gmail.com</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-envelope text-primary-400 mt-1 mr-2"></i>
                            <span class="text-gray-400">tanyayadav1234@gmail.com</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-envelope text-primary-400 mt-1 mr-2"></i>
                            <span class="text-gray-400">sahilpathan4351@gmail.com</span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-6 text-center">
            <p class="text-gray-400">Created by &copy; RamKumar  ||  Tanya Yadav   ||   Sahil Pathan</p>
            </div>
        </div>
    </footer>
</body>
</html>