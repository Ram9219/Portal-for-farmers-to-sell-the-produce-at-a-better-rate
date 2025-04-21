<?php
session_start();
require_once 'backend/db_connect.php'; // Include the database connection file

// Redirect to index.php if already logged in
if (isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['Signin'])) {
        // Handle login
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);

        // Validate required fields
        if (empty($email) || empty($password)) {
            $error = "Email and password are required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Invalid email format.";
        } else {
            // Check if the user exists in the database
            $sql = "SELECT id, name, email, password FROM users WHERE email = ?";
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                $error = "Database error: " . $conn->error;
            } else {
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $user = $result->fetch_assoc();

                    // Verify the password
                    if (password_verify($password, $user['password'])) {
                        // Start a session for the logged-in user
                        session_regenerate_id(true); // Regenerate session ID for security
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['user_name'] = $user['name'];
                        $_SESSION['user_email'] = $user['email'];

                        // Redirect to index.php
                        header("Location: index.php");
                        exit();
                    } else {
                        $error = "Invalid password.";
                    }
                } else {
                    $error = "No account found with this email.";
                }
            }
        }
    } elseif (isset($_POST['ResetPassword'])) {
        // Handle password reset
        $email = trim($_POST['email']);

        if (empty($email)) {
            $error = "Email is required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Invalid email format.";
        } else {
            // Check if the email exists
            $sql = "SELECT id FROM users WHERE email = ?";
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                $error = "Database error: " . $conn->error;
            } else {
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows > 0) {
                    // Generate a reset token
                    $resetToken = bin2hex(random_bytes(16));
                    $resetTokenExpiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

                    // Store the reset token in the database
                    $updateSql = "UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE email = ?";
                    $updateStmt = $conn->prepare($updateSql);
                    if (!$updateStmt) {
                        $error = "Database error: " . $conn->error;
                    } else {
                        $updateStmt->bind_param("sss", $resetToken, $resetTokenExpiry, $email);
                        $updateStmt->execute();

                        // Send reset email
                        $resetLink = "http://localhost/int219/reset_password.php?token=$resetToken";
                        $subject = "Password Reset Request";
                        $message = "Click the link below to reset your password:\n\n$resetLink";
                        $headers = "From: ramkumar92192005@gmail.com";

                        if (mail($email, $subject, $message, $headers)) {
                            $success = "A password reset link has been sent to your email.";
                        } else {
                            $error = "Failed to send the reset email. Please try again later.";
                        }
                    }
                } else {
                    $error = "No account found with this email.";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Farmer's Market</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            200: '#bbf7d0',
                            300: '#86efac',
                            400: '#4ade80',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            800: '#166534',
                            900: '#14532d',
                        }
                    }
                }
            }
        }
    </script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
    <!-- Login Form -->
    <div class="flex justify-center items-center min-h-screen bg-gray-100">
        <div class="bg-white shadow-md rounded-lg p-8 w-full max-w-md">
            <h1 class="text-2xl font-bold text-center text-primary-700 mb-6">Login</h1>
            <?php if (isset($error)): ?>
                <p class="text-red-500 text-center mb-4"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <?php if (isset($success)): ?>
                <p class="text-green-500 text-center mb-4"><?php echo htmlspecialchars($success); ?></p>
            <?php endif; ?>
            <form action="" method="post" class="space-y-4">
                <div>
                    <label for="email" class="block text-gray-700 font-medium">Email:</label>
                    <input type="email" id="email" name="email" class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500" required>
                </div>
                <div>
                    <label for="password" class="block text-gray-700 font-medium">Password:</label>
                    <input type="password" id="password" name="password" class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500" required>
                </div>
                <button type="submit" name="Signin" class="w-full bg-primary-600 text-white py-2 rounded-md hover:bg-primary-700">Login</button>
            </form>
            <p class="text-center text-gray-600 mt-4">
                <a href="#" onclick="toggleResetForm()" class="text-primary-600 hover:underline">Forgot Password?</a>
            </p>
            <form id="reset-form" action="" method="post" class="space-y-4 mt-4" style="display: none;">
                <div>
                    <label for="reset-email" class="block text-gray-700 font-medium">Enter your email to reset password:</label>
                    <input type="email" id="reset-email" name="email" class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500" required>
                </div>
                <button type="submit" name="ResetPassword" class="w-full bg-primary-600 text-white py-2 rounded-md hover:bg-primary-700">Reset Password</button>
            </form>
            <p class="text-center text-gray-600 mt-4">Don't have an account? <a href="register.php" class="text-primary-600 hover:underline">Register</a></p>
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

    <script>
        function toggleResetForm() {
            const loginForm = document.querySelector('form[action=""]'); // Select the login form
            const resetForm = document.getElementById('reset-form'); // Select the reset form

            // Toggle visibility
            if (resetForm.style.display === 'none' || resetForm.style.display === '') {
                loginForm.style.display = 'none'; // Hide the login form
                resetForm.style.display = 'block'; // Show the reset form
            } else {
                loginForm.style.display = 'block'; // Show the login form
                resetForm.style.display = 'none'; // Hide the reset form
            }
        }
    </script>
</body>
</html>