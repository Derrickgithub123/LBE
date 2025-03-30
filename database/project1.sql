-- Create the database
CREATE DATABASE IF NOT EXISTS project;
USE project;

-- Users Table
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(255) NOT NULL,
    last_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('customer', 'admin') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
ALTER TABLE users ADD COLUMN phone_number VARCHAR(20) UNIQUE NULL AFTER email;
ALTER TABLE users MODIFY COLUMN email VARCHAR(255) NULL;
select* from users;
ALTER TABLE users 
ADD COLUMN failed_attempts INT DEFAULT 0,
ADD COLUMN lockout_until DATETIME NULL;
ALTER TABLE users ADD COLUMN remember_token VARCHAR(64) NULL;
ALTER TABLE users 
ADD COLUMN otp_code VARCHAR(6) NULL,
ADD COLUMN otp_expires DATETIME NULL;
SHOW COLUMNS FROM users;
ALTER TABLE users
ADD COLUMN role ENUM('customer', 'admin') DEFAULT 'customer';

-- Social Logins Table
CREATE TABLE social_logins (
    social_login_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    provider ENUM('google', 'facebook') NOT NULL,
    provider_id VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Categories Table
CREATE TABLE categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT
);

-- Products Table
CREATE TABLE products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    stock_quantity INT NOT NULL,
    category_id INT,
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(category_id)
);
ALTER TABLE products
ADD COLUMN image VARCHAR(255) AFTER description;

-- Orders Table
CREATE TABLE orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- Order Items Table
CREATE TABLE order_items (
    order_item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(order_id),
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

-- Payments Table
CREATE TABLE payments (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    payment_method ENUM('mpesa', 'paypal', 'bank_transfer') NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    currency VARCHAR(10) NOT NULL DEFAULT 'KES',
    status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(order_id)
);

-- M-Pesa Payments Table
CREATE TABLE mpesa_payments (
    mpesa_payment_id INT AUTO_INCREMENT PRIMARY KEY,
    payment_id INT NOT NULL,
    mpesa_transaction_id VARCHAR(255) NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    receipt_number VARCHAR(255),
    transaction_date TIMESTAMP,
    FOREIGN KEY (payment_id) REFERENCES payments(payment_id)
);
ALTER TABLE mpesa_payments DROP COLUMN receipt_number;
select* from mpesa_payments;
-- PayPal Payments Table
CREATE TABLE paypal_payments (
    paypal_payment_id INT AUTO_INCREMENT PRIMARY KEY,
    payment_id INT NOT NULL,
    paypal_transaction_id VARCHAR(255) NOT NULL,
    payer_email VARCHAR(255) NOT NULL,
    payer_name VARCHAR(255),
    transaction_date TIMESTAMP,
    FOREIGN KEY (payment_id) REFERENCES payments(payment_id)
);

-- Bank Transfer Payments Table
CREATE TABLE bank_transfer_payments (
    bank_transfer_id INT AUTO_INCREMENT PRIMARY KEY,
    payment_id INT NOT NULL,
    bank_name VARCHAR(255) NOT NULL,
    account_name VARCHAR(255) NOT NULL,
    account_number VARCHAR(255) NOT NULL,
    reference_number VARCHAR(255),
    transaction_date TIMESTAMP,
    proof_of_payment VARCHAR(255),
    FOREIGN KEY (payment_id) REFERENCES payments(payment_id)
);
ALTER TABLE payments MODIFY status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending';
ALTER TABLE payments ADD COLUMN transaction_reference VARCHAR(255) UNIQUE;
CREATE INDEX idx_order_id ON payments(order_id);
CREATE INDEX idx_status ON payments(status);

-- Reviews Table
CREATE TABLE reviews (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_id INT NOT NULL,
    rating INT CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(product_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);
select* from reviews;
-- Legal Documents Table
CREATE TABLE legal_documents (
    document_id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('terms', 'privacy') NOT NULL,
    content TEXT NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
SHOW TABLES;
SELECT * FROM products;
SELECT * FROM categories WHERE category_id = 1;
SELECT * FROM categories;
INSERT INTO categories (category_id, name) VALUES (1, 'Default Category');
INSERT INTO categories (name) VALUES 
('Laptops'), 
('Smartphones'), 
('Large Appliances'),
('Other Appliances'),
('Small Appliances'),
('Cameras'),
('Security Devices'),
('Networking Devices'),
('Computing Devices'),
('Electrical Accessories');
SHOW TABLES;
CREATE TABLE cart (
    cart_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);
select* from users;
DESC users;
SHOW COLUMNS FROM users;
ALTER TABLE users ADD COLUMN phone_number VARCHAR(15) NULL AFTER email;
UPDATE users SET email = NULL WHERE email = '';
UPDATE users SET phone_number = NULL WHERE phone_number = '';
ALTER TABLE users MODIFY email VARCHAR(255) NULL;
DESC users;
select* from products;
SHOW DATABASES;
SELECT * FROM products;
DESCRIBE categories;
DELETE p1 FROM products p1
JOIN products p2 
ON p1.name = p2.name AND p1.category_id = p2.category_id AND p1.product_id > p2.product_id;
DELETE FROM products WHERE name = 'BOSE F1';
DELETE FROM products;
select* from users;
CREATE TABLE admins (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(255) NOT NULL,
    last_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
INSERT INTO admins (first_name, last_name, email, password_hash)
VALUES ('Derrick', 'Kanyoko', 'kanyokoderrick15@gmail.com', '$2y$10$e5PIVzKp9REfiHRPjMv26u0/a5nGvi64xMFSIqGm9vXk.OlZFdXfK');
SELECT * FROM admins;
DELETE FROM admins WHERE email = 'kanyokoderrick15@gmail.com';
CREATE TABLE faqs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question VARCHAR(255) NOT NULL,
    answer TEXT NOT NULL
);
DELETE FROM faqs WHERE question = 'Ask Question';
DELETE FROM faqs WHERE TRIM(LOWER(question)) = 'ask question';
select* from faqs;
SELECT * FROM faqs WHERE LOWER(question) LIKE '%ask question%';
DELETE FROM faqs WHERE question = 'Ask a Question';
CREATE TABLE policies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section VARCHAR(255) NOT NULL,
    category ENUM('terms', 'privacy') NOT NULL,
    content TEXT NOT NULL
);
select* from policies;
CREATE TABLE offers (
    offer_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    discount_price DECIMAL(10,2) NOT NULL,
    start_date DATETIME NOT NULL,
    end_date DATETIME NOT NULL,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE
);
ALTER TABLE offers 
ADD COLUMN offer_name VARCHAR(100) NOT NULL DEFAULT 'Special Offer';

ALTER TABLE offers ADD COLUMN discount_percentage DECIMAL(5,2) DEFAULT 0;

INSERT INTO offers (product_id, discount_price, start_date, end_date) 
VALUES 
(416, 199.99, '2025-04-01', '2025-04-10'),
(417, 299.99, '2025-04-05', '2025-04-15'),
(418, 149.99, '2025-04-02', '2025-04-12');
ALTER TABLE offers ADD COLUMN is_active BOOLEAN DEFAULT FALSE;
ALTER TABLE products ADD COLUMN original_price DECIMAL(10,2) NOT NULL AFTER price;
UPDATE products SET original_price = price; -- Store the original prices for existing products
select* from categories;
ALTER TABLE admins DISCARD TABLESPACE;
CREATE TABLE admins (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    password_hash VARCHAR(255) NOT NULL
) ENGINE=InnoDB;
SELECT TABLE_NAME, ENGINE 
FROM information_schema.tables 
WHERE TABLE_SCHEMA = 'project' AND TABLE_NAME = 'admins';
DROP TABLE admins;
show tables;
select* from admins;
SHOW GRANTS FOR 'your_user'@'localhost';
SHOW GRANTS FOR 'root'@'localhost';
SELECT TABLE_NAME, ENGINE FROM information_schema.tables WHERE TABLE_SCHEMA = 'project';
SET GLOBAL innodb_force_recovery = 1;
SELECT * FROM users LIMIT 5;
SELECT * FROM products LIMIT 5;
SELECT * FROM orders LIMIT 5;
SHOW TABLE STATUS FROM project;
SHOW DATABASES;
USE project;
SHOW TABLES;








