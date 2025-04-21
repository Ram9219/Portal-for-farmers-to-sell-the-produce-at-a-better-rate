<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
$userEmail = $isLoggedIn ? $_SESSION['user_email'] : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmer's Market - Agricultural Supplies</title>
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
                    },
                    animation: {
                        'fade-in': 'fadeIn 1s ease-in-out',
                        'slide-up': 'slideUp 0.5s ease-out',
                        'bounce-slow': 'bounce 3s infinite',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        slideUp: {
                            '0%': { transform: 'translateY(20px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' },
                        }
                    }
                }
            }
        }
    </script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        .staggered-animation > * {
            opacity: 0;
            transform: translateY(20px);
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            transition: all 0.3s ease;
        }
        
        .hero-pattern {
            background-color: #f0fdf4;
            background-image: url("https://images.unsplash.com/flagged/photo-1576106035594-69a5d049173f?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTh8fGluZGlhbiUyMGFncmljdWx0dXJlfGVufDB8fDB8fHww");
        }
    </style>
</head>
<body class="bg-gray-50 font-sans">
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
                        <button class="text-gray-700 hover:text-primary-600 focus:outline-none">
                            <i class="fas fa-user text-xl"></i>
                        </button>
                        <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 hidden group-hover:block">
                            <?php if ($isLoggedIn): ?>
                                <!-- Logged-in Menu -->
                                <div id="logged-in-menu">
                                    <a href="dashboard.html" class="block px-4 py-2 text-sm text-gray-700 hover:bg-primary-50">Dashboard</a>
                                    <a href="orders.html" class="block px-4 py-2 text-sm text-gray-700 hover:bg-primary-50">My Orders</a>
                                    <a href="logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-primary-50">Logout</a>
                                </div>
                            <?php else: ?>
                                <!-- Logged-out Menu -->
                                <div id="logged-out-menu">
                                    <a href="login.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-primary-50">Login</a>
                                    <a href="register.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-primary-50">Register</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Mobile Menu Button -->
                    <button id="mobile-menu-button" class="md:hidden text-gray-700 hover:text-primary-600 focus:outline-none">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
            
            <!-- Mobile Navigation -->
            <div id="mobile-menu" class="md:hidden hidden pb-4">
                <div class="flex flex-col space-y-3">
                    <a href="index.php" class="text-gray-700 hover:text-primary-600 font-medium">Home</a>
                    <a href="products.html" class="text-gray-700 hover:text-primary-600 font-medium">Products</a>
                    <a href="about.html" class="text-gray-700 hover:text-primary-600 font-medium">About Us</a>
                    <a href="contact.html" class="text-gray-700 hover:text-primary-600 font-medium">Contact</a>
                    <div class="relative mt-2">
                        <input type="text" placeholder="Search products..." class="w-full pl-8 pr-4 py-1 rounded-full border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <i class="fas fa-search absolute left-3 top-2 text-gray-400"></i>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Rest of the HTML remains the same -->
    <!-- ... -->
     <!-- Hero Section with Animation -->
    <section class="hero-pattern py-16 md:py-24">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row items-center">
                <div class="md:w-1/2 mb-10 md:mb-0 animate-fade-in">
                    <h1 class="text-4xl md:text-5xl font-bold text-primary-800 mb-4 shadow-lg">Quality Agricultural Supplies for Your Farm</h1>
                    <p class="text-lg text-gray-900 mb-8 font-bold shadow-lg">Get premium seeds, pesticides, fertilizers, and farming tools delivered to your doorstep.</p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="products.html" class="bg-primary-600 hover:bg-primary-700 text-white font-bold py-3 px-6 rounded-lg transition duration-300 text-center">
                            Shop Now
                        </a>
                        <a href="about.html" class="border border-primary-600 text-primary-600 hover:bg-primary-50 font-bold py-3 px-6 rounded-lg transition duration-300 text-center">
                            Learn More
                        </a>
                    </div>
                </div>
                <div class="md:w-1/2 relative animate-bounce-slow">
                    <img src="https://images.pexels.com/photos/31551947/pexels-photo-31551947/free-photo-of-indian-farmer-spraying-pesticides-in-rice-field.jpeg?auto=compress&cs=tinysrgb&w=600" alt="Farming Supplies"   class="w-[600px] h-[400px] rounded-lg shadow-xl mx-auto">
                    <!-- <img src="https://placehold.co/600x400/f0fdf4/166534?text=Farming+Supplies" alt="Farming Supplies" class="rounded-lg shadow-xl mx-auto"> -->
                    <div class="absolute -bottom-5 -left-5 bg-primary-500 text-white py-2 px-4 rounded-lg shadow-lg animate-pulse">
                        <p class="font-bold">Special Offer</p>
                        <p>20% OFF on Seeds</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center text-gray-800 mb-12">Our Product Categories</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 staggered-animation">
                <div class="bg-primary-50 rounded-lg p-6 text-center shadow-md">
                    <div class="bg-primary-100 w-16 h-16 mx-auto rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-seedling text-primary-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Seeds</h3>
                    <p class="text-gray-600 mb-4">High-quality seeds for various crops and vegetables</p>
                    <a href="seeds.html" class="text-primary-600 hover:text-primary-700 font-medium">View Products <i class="fas fa-arrow-right ml-1"></i></a>
                </div>
                <div class="bg-primary-50 rounded-lg p-6 text-center shadow-md">
                    <div class="bg-primary-100 w-16 h-16 mx-auto rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-spray-can text-primary-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Pesticides</h3>
                    <p class="text-gray-600 mb-4">Effective pest control solutions for your crops</p>
                    <a href="pesticides.html" class="text-primary-600 hover:text-primary-700 font-medium">View Products <i class="fas fa-arrow-right ml-1"></i></a>
                </div>
                <div class="bg-primary-50 rounded-lg p-6 text-center shadow-md">
                    <div class="bg-primary-100 w-16 h-16 mx-auto rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-fill-drip text-primary-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Fertilizers</h3>
                    <p class="text-gray-600 mb-4">Nutrient-rich fertilizers for healthy plant growth</p>
                    <a href="fertilizers.html" class="text-primary-600 hover:text-primary-700 font-medium">View Products <i class="fas fa-arrow-right ml-1"></i></a>
                </div>
                <div class="bg-primary-50 rounded-lg p-6 text-center shadow-md">
                    <div class="bg-primary-100 w-16 h-16 mx-auto rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-tools text-primary-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Tools & Equipment</h3>
                    <p class="text-gray-600 mb-4">Durable farming tools and equipment</p>
                    <a href="equipment.html" class="text-primary-600 hover:text-primary-700 font-medium">View Products <i class="fas fa-arrow-right ml-1"></i></a>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center text-gray-800 mb-4">Featured Products</h2>
            <p class="text-center text-gray-600 mb-12 max-w-2xl mx-auto">Discover our best-selling agricultural supplies that farmers trust for quality and results.</p>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Product 1 -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden product-card">
                    <!-- <img src="https://placehold.co/300x200/f0fdf4/166534?text=Hybrid+Seeds" alt="Hybrid Seeds" class="w-full h-48 object-cover"> -->
                    <img src="assets\tamato-hybrid.jpg" alt="Hybrid Seeds" class="w-full h-48 object-cover">
                    <div class="p-4">
                        <span class="text-xs font-semibold text-primary-600 bg-primary-50 px-2 py-1 rounded-full">Seeds</span>
                        <h3 class="text-lg font-semibold mt-2">Premium Hybrid Tomato Seeds</h3>
                        <p class="text-gray-600 text-sm mt-1">High-yield, disease-resistant tomato variety</p>
                        <div class="flex justify-between items-center mt-4">
                            <span class="text-lg font-bold text-primary-700">Rs.999.00</span>
                            <button class="add-to-cart bg-primary-600 hover:bg-primary-700 text-white px-3 py-1 rounded-lg text-sm" data-id="1" data-name="Premium Hybrid Tomato Seeds" data-price="12.99">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Product 2 -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden product-card">
                    <img src="assets\neem.jpg" alt="Organic Pesticide" class="w-full h-48 object-cover">
                    <div class="p-4">
                        <span class="text-xs font-semibold text-primary-600 bg-primary-50 px-2 py-1 rounded-full">Pesticides</span>
                        <h3 class="text-lg font-semibold mt-2">Organic Neem Oil Pesticide</h3>
                        <p class="text-gray-600 text-sm mt-1">Natural pest control solution, 500ml</p>
                        <div class="flex justify-between items-center mt-4">
                            <span class="text-lg font-bold text-primary-700">Rs.1499.00</span>
                            <button class="add-to-cart bg-primary-600 hover:bg-primary-700 text-white px-3 py-1 rounded-lg text-sm" data-id="2" data-name="Organic Neem Oil Pesticide" data-price="18.50">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Product 3 -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden product-card">
                    <img src="assets\npk.jpg" alt="NPK Fertilizer" class="w-full h-48 object-cover">
                    <div class="p-4">
                        <span class="text-xs font-semibold text-primary-600 bg-primary-50 px-2 py-1 rounded-full">Fertilizers</span>
                        <h3 class="text-lg font-semibold mt-2">NPK 20-20-20 Fertilizer</h3>
                        <p class="text-gray-600 text-sm mt-1">Balanced nutrition for all crops, 5kg</p>
                        <div class="flex justify-between items-center mt-4">
                            <span class="text-lg font-bold text-primary-700">Rs.1999.00</span>
                            <button class="add-to-cart bg-primary-600 hover:bg-primary-700 text-white px-3 py-1 rounded-lg text-sm" data-id="3" data-name="NPK 20-20-20 Fertilizer" data-price="24.99">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Product 4 -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden product-card">
                    <img src="assets\garden-tool.jpg" alt="Garden Tools" class="w-full h-48 object-cover">
                    <div class="p-4">
                        <span class="text-xs font-semibold text-primary-600 bg-primary-50 px-2 py-1 rounded-full">Tools</span>
                        <h3 class="text-lg font-semibold mt-2">Premium Garden Tool Set</h3>
                        <p class="text-gray-600 text-sm mt-1">5-piece stainless steel gardening kit</p>
                        <div class="flex justify-between items-center mt-4">
                            <span class="text-lg font-bold text-primary-700">Rs.2999.00</span>
                            <button class="add-to-cart bg-primary-600 hover:bg-primary-700 text-white px-3 py-1 rounded-lg text-sm" data-id="4" data-name="Premium Garden Tool Set" data-price="35.99">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-10">
                <a href="products.html" class="inline-block bg-white border border-primary-600 text-primary-600 hover:bg-primary-50 font-bold py-3 px-6 rounded-lg transition duration-300">
                    View All Products
                </a>
            </div>
        </div>
    </section>

    <!-- Why Choose Us -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center text-gray-800 mb-12">Why Choose Us</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="flex flex-col items-center text-center">
                    <div class="bg-primary-100 w-20 h-20 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-award text-primary-600 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Premium Quality</h3>
                    <p class="text-gray-600">We source only the highest quality agricultural supplies from trusted manufacturers.</p>
                </div>
                <div class="flex flex-col items-center text-center">
                    <div class="bg-primary-100 w-20 h-20 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-truck text-primary-600 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Fast Delivery</h3>
                    <p class="text-gray-600">Quick and reliable delivery to ensure you get your supplies when you need them.</p>
                </div>
                <div class="flex flex-col items-center text-center">
                    <div class="bg-primary-100 w-20 h-20 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-headset text-primary-600 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Expert Support</h3>
                    <p class="text-gray-600">Our team of agricultural experts is always available to provide guidance and support.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center text-gray-800 mb-12">What Our Customers Say</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex items-center mb-4">
                        <div class="text-yellow-400 flex">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <span class="ml-2 text-gray-600">5.0</span>
                    </div>
                    <p class="text-gray-700 mb-4">"The hybrid seeds I purchased have given me the best yield in years. Highly recommend their products for serious farmers."</p>
                    <div class="flex items-center">
                        <img src="https://placehold.co/100/166534/ffffff?text=JD" alt="John Doe" class="w-10 h-10 rounded-full mr-3">
                        <div>
                            <h4 class="font-semibold">Lakshman Singh</h4>
                            <p class="text-sm text-gray-600">Vegetable Farmer</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex items-center mb-4">
                        <div class="text-yellow-400 flex">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                        <span class="ml-2 text-gray-600">4.5</span>
                    </div>
                    <p class="text-gray-700 mb-4">"Fast delivery and excellent customer service. The organic pesticides are effective and safe for my crops. Will order again."</p>
                    <div class="flex items-center">
                        <img src="https://placehold.co/100/166534/ffffff?text=MS" alt="Mary Smith" class="w-10 h-10 rounded-full mr-3">
                        <div>
                            <h4 class="font-semibold">Avadhesh Tripathi</h4>
                            <p class="text-sm text-gray-600">Organic Farmer</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <div class="flex items-center mb-4">
                        <div class="text-yellow-400 flex">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <span class="ml-2 text-gray-600">5.0</span>
                    </div>
                    <p class="text-gray-700 mb-4">"The garden tools are of exceptional quality. Durable and comfortable to use. Their fertilizers have also improved my soil quality significantly."</p>
                    <div class="flex items-center">
                        <img src="https://placehold.co/100/166534/ffffff?text=RJ" alt="Robert Johnson" class="w-10 h-10 rounded-full mr-3">
                        <div>
                            <h4 class="font-semibold">Jayant Shukla</h4>
                            <p class="text-sm text-gray-600">Hobby Gardener</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Newsletter -->
    <section class="py-16 bg-primary-700">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl mx-auto text-center">
                <h2 class="text-3xl font-bold text-white mb-4">Stay Updated</h2>
                <p class="text-primary-100 mb-8">Subscribe to our newsletter for the latest products, farming tips, and exclusive offers.</p>
                <form class="flex flex-col sm:flex-row gap-2 max-w-lg mx-auto">
                    <input type="email" placeholder="Your email address" class="flex-grow px-4 py-3 rounded-lg focus:outline-none">
                    <button type="submit" class="bg-white text-primary-700 hover:bg-primary-50 font-bold py-3 px-6 rounded-lg transition duration-300">
                        Subscribe
                    </button>
                </form>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white pt-12 pb-6">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center mb-4">
                        <i class="fas fa-seedling text-primary-400 text-2xl mr-2"></i>
                        <span class="text-xl font-bold">FarmerSupply</span>
                    </div>
                    <p class="text-gray-400 mb-4">Your trusted partner for quality agricultural supplies.</p>
                    <div class="flex space-x-4">
                        <!-- facebook -->
                        <a href="https://www.facebook.com/share/1642VYYPg2/" class="text-gray-400 hover:text-primary-400"><i class="fab fa-facebook-f"></i></a>
                        <!-- twitter -->
                        <a href="https://x.com/Ramkuma62342260" class="text-gray-400 hover:text-primary-400"><i class="fab fa-twitter"></i></a>
                        <!-- instagram -->
                        <a href="#" class="text-gray-400 hover:text-primary-400"><i class="fab fa-instagram"></i></a>
                        <!-- youtube -->
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
                        <li><a href="seeds.html" class="text-gray-400 hover:text-primary-400">Seeds</a></li>
                        <li><a href="pesticides.html" class="text-gray-400 hover:text-primary-400">Pesticides</a></li>
                        <li><a href="fertilizers.html" class="text-gray-400 hover:text-primary-400">Fertilizers</a></li>
                        <li><a href="equipments.html" class="text-gray-400 hover:text-primary-400">Tools & Equipment</a></li>
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


    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Custom JS -->
    <script>
    $(document).ready(function () {
        // Mobile menu toggle
        $('#mobile-menu-button').click(function () {
            $('#mobile-menu').slideToggle();
        });

        // Animation for staggered elements
        $('.staggered-animation > *').each(function (index) {
            $(this).delay(index * 100).animate({
                opacity: 1
            }, 500);
        });

        // Shopping cart functionality
        let cart = [];
        try {
            cart = JSON.parse(localStorage.getItem('cart')) || [];
        } catch (error) {
            console.error('Error accessing localStorage:', error);
        }
        updateCartCount();

        $('.add-to-cart').click(function () {
            const id = $(this).data('id');
            const name = $(this).data('name');
            const price = $(this).data('price');

            if (!id || !name || !price) {
                console.error('Missing product data attributes.');
                return;
            }

            // Check if product is already in cart
            const existingItem = cart.find(item => item.id === id);

            if (existingItem) {
                existingItem.quantity += 1;
            } else {
                cart.push({
                    id: id,
                    name: name,
                    price: price,
                    quantity: 1
                });
            }

            // Save to localStorage
            localStorage.setItem('cart', JSON.stringify(cart));

            // Update cart count
            updateCartCount();

            // Show notification
            showNotification(`${name} added to cart!`);
        });

        // Helper functions
        function updateCartCount() {
            const totalItems = Array.isArray(cart) ? cart.reduce((total, item) => total + item.quantity, 0) : 0;
            $('#cart-count').text(totalItems);
        }

        function showNotification(message) {
            // Create notification element if it doesn't exist
            if ($('#notification').length === 0) {
                $('body').append('<div id="notification" class="fixed top-20 right-4 bg-primary-600 text-white py-2 px-4 rounded-lg shadow-lg z-50 transform translate-x-full transition-transform duration-300"></div>');
            }

            // Show notification with message
            $('#notification').text(message).removeClass('translate-x-full');

            // Hide after 3 seconds
            setTimeout(function () {
                $('#notification').addClass('translate-x-full');
            }, 3000);
        }
    });
    </script>
</body>
</html>