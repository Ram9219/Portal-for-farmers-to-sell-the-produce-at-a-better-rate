<?php
// Start the session and include the database connection
session_start();
require_once 'backend/db_connect.php'; // Include the database connection file

// Redirect to index.php if already logged in
if (isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

// Handle registration
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['Signup'])) {
    $fname = trim($_POST['fname']);
    $lname = trim($_POST['lname']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Validate required fields
    if (empty($fname) || empty($lname) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Check if email already exists
        $checkQuery = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $checkQuery->bind_param("s", $email);
        $checkQuery->execute();
        $checkQuery->store_result();
        if ($checkQuery->num_rows > 0) {
            $error = "Email already registered.";
        } else {
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user
            $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            if ($stmt) {
                $name = $fname . " " . $lname;
                $stmt->bind_param("sss", $name, $email, $hashedPassword);
                if ($stmt->execute()) {
                    // Automatically log in the user after successful registration
                    $_SESSION['user_id'] = $stmt->insert_id; // Store the user ID in the session
                    $_SESSION['user_name'] = $name; // Store the user's full name in the session
                    $_SESSION['user_email'] = $email; // Store the user's email in the session

                    // Redirect to index.php
                    header("Location: index.php");
                    exit();
                } else {
                    $error = "Registration failed. Please try again.";
                }
            } else {
                $error = "Database error: Unable to prepare statement.";
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
    <title>Register - Farmer's Market</title>
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
    <!-- Header/Navigation -->
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
            </div>
        </div>
    </header>

    <!-- Registration Form -->
    <div class="flex justify-center items-center min-h-screen bg-gray-100">
        <div class="bg-white shadow-md rounded-lg p-8 w-full max-w-md">
            <h1 class="text-2xl font-bold text-center text-primary-700 mb-6">Register</h1>
            <?php if (isset($error)): ?>
                <p class="text-red-500 text-center mb-4"><?php echo $error; ?></p>
            <?php endif; ?>
            <form action="" method="post" class="space-y-4">
                <div>
                    <label for="fname" class="block text-gray-700 font-medium">First Name:</label>
                    <input type="text" id="fname" name="fname" class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500" required>
                </div>
                <div>
                    <label for="lname" class="block text-gray-700 font-medium">Last Name:</label>
                    <input type="text" id="lname" name="lname" class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500" required>
                </div>
                <div>
                    <label for="email" class="block text-gray-700 font-medium">Email:</label>
                    <input type="email" id="email" name="email" class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500" required>
                </div>
                <div>
                    <label for="password" class="block text-gray-700 font-medium">Password:</label>
                    <input type="password" id="password" name="password" class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500" required>
                </div>
                <div>
                    <label for="confirm_password" class="block text-gray-700 font-medium">Confirm Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500" required>
                </div>
                <button type="submit" name="Signup" class="w-full bg-primary-600 text-white py-2 rounded-md hover:bg-primary-700">Register</button>
            </form>
            <p class="text-center text-gray-600 mt-4">Already have an account? <a href="login.php" class="text-primary-600 hover:underline">Login</a></p>
        </div>
    </div>

    <!-- Footer -->
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
                    <ul class="text-gray-400 flex space-x-4">
                        <li><a href="index.php" class="hover:text-primary-400">Home</a></li>
                        <li><a href="products.html" class="hover:text-primary-400">Products</a></li>
                        <li><a href="about.html" class="hover:text-primary-400">About Us</a></li>
                        <li><a href="contact.html" class="hover:text-primary-400">Contact</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>