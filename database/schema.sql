-- Create database
CREATE DATABASE IF NOT EXISTS farmer_market;
USE farmer_market;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    is_admin TINYINT(1) DEFAULT 0,
    reset_token VARCHAR(255), -- Column to store the reset token
    reset_token_expiry DATETIME, -- Column to store the expiry time of the reset token
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

--contact table
CREATE TABLE contact_responses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- Products table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    category_id INT,
    category VARCHAR(50),
    image VARCHAR(255),
    is_featured TINYINT(1) DEFAULT 0,
    sales_count INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Orders table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    items TEXT NOT NULL, -- JSON string of cart items
    total_amount DECIMAL(10, 2) NOT NULL,
    order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(50) DEFAULT 'Pending',
    shipping_address TEXT NOT NULL, -- Column to store the shipping address
    FOREIGN KEY (user_id) REFERENCES users(id)
);
-- Order items table
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT,
    product_name VARCHAR(100) NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
);

-- Insert sample categories
INSERT INTO categories (name, description, image) VALUES
('Seeds', 'High-quality seeds for various crops and vegetables', 'images/categories/seeds.jpg'),
('Pesticides', 'Effective pest control solutions for your crops', 'images/categories/pesticides.jpg'),
('Fertilizers', 'Nutrient-rich fertilizers for healthy plant growth', 'images/categories/fertilizers.jpg'),
('Tools', 'Durable farming tools and equipment', 'images/categories/tools.jpg');

-- Insert sample products
INSERT INTO products (name, description, price, stock, category_id, category, image, is_featured) VALUES
('Premium Hybrid Tomato Seeds', 'High-yield, disease-resistant tomato variety', 999.00, 100, 1, 'Seeds', 'images/products/tomato-seeds.jpg', 1),
('Organic Neem Oil Pesticide', 'Natural pest control solution, 500ml', 1499.00, 50, 2, 'Pesticides', 'images/products/neem-oil.jpg', 1),
('NPK 20-20-20 Fertilizer', 'Balanced nutrition for all crops, 5kg', 1999.00, 75, 3, 'Fertilizers', 'images/products/npk-fertilizer.jpg', 1),
('Premium Garden Tool Set', '5-piece stainless steel gardening kit', 2999.00, 30, 4, 'Tools', 'images/products/garden-tools.jpg', 1),
('Sweet Corn Seeds', 'High-yielding sweet corn variety, 500g', 799.00, 120, 1, 'Seeds', 'images/products/corn-seeds.jpg', 0),
('Broad Spectrum Insecticide', 'Controls a wide range of insects, 1L', 1799.00, 40, 2, 'Pesticides', 'images/products/insecticide.jpg', 0),
('Organic Compost Fertilizer', '100% organic plant nutrition, 10kg', 1599.00, 60, 3, 'Fertilizers', 'images/products/compost.jpg', 0),
('Professional Pruning Shears', 'Sharp, stainless steel blades for precise cutting', 1299.00, 45, 4, 'Tools', 'images/products/pruning-shears.jpg', 0),
('Cucumber Seeds', 'High-yielding cucumber variety, 250g', 699.00, 90, 1, 'Seeds', 'images/products/cucumber-seeds.jpg', 0);
