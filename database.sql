-- =====================================================
-- ShopEase Online Shopping System
-- Database: online_shopping
-- Import this file via phpMyAdmin (WAMP)
-- =====================================================

CREATE DATABASE IF NOT EXISTS online_shopping
  CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

USE online_shopping;

-- -----------------------------------------------------
-- Table: customers
-- -----------------------------------------------------
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS cart;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS customers;

CREATE TABLE customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(120) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    address VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Table: products
-- -----------------------------------------------------
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    category VARCHAR(80) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    discount_price DECIMAL(10,2) DEFAULT NULL,
    description TEXT,
    image VARCHAR(255) NOT NULL,
    rating DECIMAL(2,1) DEFAULT 4.5,
    stock INT DEFAULT 50,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Table: cart  (works for guests via session_id and for
-- logged-in customers via customer_id)
-- -----------------------------------------------------
CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(100) NOT NULL,
    customer_id INT DEFAULT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Table: orders
-- -----------------------------------------------------
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status VARCHAR(30) DEFAULT 'Pending',
    shipping_address VARCHAR(255) DEFAULT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Table: order_items
-- -----------------------------------------------------
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Sample customer (password = "password123")
-- Hash below = password_hash('password123', PASSWORD_DEFAULT)
-- -----------------------------------------------------
INSERT INTO customers (full_name, email, password, phone, address) VALUES
('Test User', 'test@shopease.com', '$2b$10$MMcdGO.zRZzCxRNSzAmEYuBu.SPB1VrFWxemv5ZAHryr8ii2Agc1O', '9876543210', '123 Main Street, Chennai');

-- -----------------------------------------------------
-- Sample products (12+)
-- -----------------------------------------------------
INSERT INTO products (name, category, price, discount_price, description, image, rating, stock) VALUES
('Wireless Bluetooth Headphones', 'Electronics', 3999.00, 2799.00, 'Over-ear wireless headphones with active noise cancellation, 30-hour battery life and deep bass.', 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=500', 4.5, 60),
('Smart Fitness Watch', 'Electronics', 5999.00, 4499.00, 'Track your heart rate, sleep and workouts with this sleek AMOLED smart watch.', 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=500', 4.3, 45),
('4K Ultra HD Action Camera', 'Electronics', 8999.00, 7499.00, 'Waterproof action camera with 4K video, image stabilization and Wi-Fi transfer.', 'https://images.unsplash.com/photo-1526170375885-4d8ecf77b99f?w=500', 4.6, 30),
('Men''s Classic Leather Jacket', 'Fashion', 4499.00, 3299.00, 'Genuine leather jacket with a timeless classic fit, perfect for winter.', 'https://images.unsplash.com/photo-1551028719-00167b16eac5?w=500', 4.4, 25),
('Women''s Running Shoes', 'Fashion', 2999.00, 1999.00, 'Lightweight breathable running shoes with cushioned sole for all-day comfort.', 'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?w=500', 4.7, 80),
('Designer Sunglasses', 'Fashion', 1499.00, 999.00, 'UV protected polarized designer sunglasses with a durable metal frame.', 'https://images.unsplash.com/photo-1572635196237-14b3f281503f?w=500', 4.2, 50),
('Non-Stick Cookware Set (5 pcs)', 'Home & Kitchen', 3499.00, 2599.00, 'Durable non-stick cookware set including pans and pots for everyday cooking.', 'https://images.unsplash.com/photo-1584990347449-a5d9f800a783?w=500', 4.5, 40),
('Automatic Espresso Coffee Maker', 'Home & Kitchen', 7999.00, 6499.00, 'Brew barista-style espresso at home with this fully automatic coffee maker.', 'https://images.unsplash.com/photo-1517668808822-9ebb02f2a0e6?w=500', 4.6, 20),
('Memory Foam Pillow Set (2 pcs)', 'Home & Kitchen', 1299.00, 899.00, 'Orthopedic memory foam pillows for better neck and spine support.', 'https://images.unsplash.com/photo-1522771930-78848d9293e8?w=500', 4.3, 70),
('Best-Seller Novel Collection (3 Books)', 'Books', 999.00, 699.00, 'A bundle of three best-selling fiction novels loved by readers worldwide.', 'https://images.unsplash.com/photo-1512820790803-83ca734da794?w=500', 4.8, 100),
('Kids Building Blocks Set (120 pcs)', 'Toys', 1799.00, 1299.00, 'Educational building block set that boosts creativity and motor skills.', 'https://images.unsplash.com/photo-1587654780291-39c9404d746b?w=500', 4.7, 55),
('Yoga Mat with Carry Strap', 'Sports', 1199.00, 799.00, 'Extra-thick non-slip yoga mat ideal for yoga, pilates and floor exercises.', 'https://images.unsplash.com/photo-1601925260368-ae2f83cf8b7f?w=500', 4.4, 90),
('Portable Bluetooth Speaker', 'Electronics', 2499.00, 1799.00, 'Compact portable speaker with rich bass and 12-hour battery backup.', 'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=500', 4.5, 65),
('Stainless Steel Water Bottle 1L', 'Sports', 799.00, 499.00, 'Double-wall insulated bottle that keeps drinks cold for 24 hours.', 'https://images.unsplash.com/photo-1602143407151-7111542de6e8?w=500', 4.6, 120);
