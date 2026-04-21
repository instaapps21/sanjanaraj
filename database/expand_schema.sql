-- SANJANARAJ - Expansion Schema

USE sanjanaraj;

-- 1. CMS Pages Table
CREATE TABLE IF NOT EXISTS cms_pages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(50) UNIQUE NOT NULL,
    title VARCHAR(150) NOT NULL,
    content LONGTEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 2. News & Events Table
CREATE TABLE IF NOT EXISTS news_events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('news', 'announcement', 'event') DEFAULT 'news',
    title VARCHAR(200) NOT NULL,
    content TEXT,
    event_date DATE NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Complaints Table
CREATE TABLE IF NOT EXISTS complaints (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    admin_reply TEXT NULL,
    status ENUM('open', 'resolved') DEFAULT 'open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 4. Update Users Role Enum to allow 'customer' (retail buyers)
-- Note: MySQL ALTER TABLE for ENUM requires replacing the entire ENUM list.
ALTER TABLE users MODIFY COLUMN role ENUM('user', 'admin', 'customer') DEFAULT 'user';

-- Insert default CMS pages
INSERT IGNORE INTO cms_pages (slug, title, content) VALUES
('terms', 'Terms & Conditions', '<h2>Terms and Conditions</h2><p>Welcome to SANJANARAJ.</p>'),
('privacy', 'Privacy Policy', '<h2>Privacy Policy</h2><p>Your privacy is important to us.</p>');
