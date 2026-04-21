-- SANJANARAJ - Database Schema (MySQL)

CREATE DATABASE IF NOT EXISTS sanjanaraj;
USE sanjanaraj;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sponsor_id VARCHAR(50) DEFAULT NULL,
    referral_id VARCHAR(50) UNIQUE NOT NULL, -- User's own referral code
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    package_id INT DEFAULT 0, -- 0 means inactive/free
    wallet_balance DECIMAL(10,2) DEFAULT 0.00,
    total_earned DECIMAL(10,2) DEFAULT 0.00,
    binary_position ENUM('left', 'right') DEFAULT NULL,
    parent_id INT DEFAULT NULL, -- Upline parent ID in the binary tree
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Packages table
CREATE TABLE IF NOT EXISTS packages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    direct_income DECIMAL(10, 2) NOT NULL,
    matching_bonus DECIMAL(10, 2) NOT NULL,
    level_income DECIMAL(10, 2) NOT NULL,
    is_active TINYINT(1) DEFAULT 1
);

-- Wallet Transactions table
CREATE TABLE IF NOT EXISTS transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    type ENUM('credit', 'debit') NOT NULL,
    description VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Withdrawals table
CREATE TABLE IF NOT EXISTS withdrawals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    request_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    process_date TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Products table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category ENUM('Health & Wellness', 'Personal Care', 'Home Care', 'Other') DEFAULT 'Other',
    name VARCHAR(200) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image VARCHAR(255) DEFAULT 'default_product.png',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Default Admin & Packages (for seeding)
INSERT IGNORE INTO packages (name, price, direct_income, matching_bonus, level_income) VALUES
('Starter Package', 1000.00, 100.00, 150.00, 50.00),
('Pro Package', 5000.00, 500.00, 750.00, 250.00);

-- Insert admin with hash of 'admin123'
INSERT IGNORE INTO users (name, phone, email, password_hash, referral_id, role) 
VALUES ('Admin User', '0000000000', 'admin@sanjanaraj.in', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'FIN_ADMIN', 'admin');
