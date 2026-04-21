-- Product Management Schema Update
-- Run this migration to update the products table for fish products

USE sanjanaraj;

-- Drop and recreate products table with new fields
DROP TABLE IF EXISTS products;

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category VARCHAR(100) NOT NULL DEFAULT 'Other',
    name VARCHAR(200) NOT NULL,
    tamil_name VARCHAR(200) DEFAULT NULL,
    description TEXT,
    weight VARCHAR(50) DEFAULT '1 KG',
    pv INT DEFAULT 0,
    price DECIMAL(10, 2) NOT NULL,
    image VARCHAR(255) DEFAULT 'default_product.png',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Seed the 16 fish products from the Product.docx
INSERT INTO products (category, name, tamil_name, description, weight, pv, price, image, is_active) VALUES
('Premium Beach Fish', 'Seer Fish', 'வஞ்சிரம்', 'Premium quality seer fish, fresh from the beach. Known for its rich flavor and firm texture. Ideal for frying, grilling, and making traditional fish curry.', '1 KG', 13, 1200.00, 'fish_1.jpeg', 1),
('Regular Beach Fish', 'Mackerel', 'அயிலை', 'Fresh mackerel with excellent omega-3 content. A staple in South Indian cuisine, perfect for fish fry and kuzhambhu preparations.', '1 KG', 5, 400.00, 'fish_4.jpeg', 1),
('Premium Beach Fish', 'Red Snapper', 'சங்கரா', 'Premium red snapper known for its delicate and mild flavor. Versatile fish suitable for curries, grills, and steamed preparations.', '1 KG', 6, 500.00, 'fish_7.jpeg', 1),
('Premium Beach Fish', 'Grouper', 'கலவா மீன்', 'Fresh grouper with firm, white flesh. A premium catch prized in both Indian and international cuisines.', '1 KG', 5, 650.00, 'fish_10.jpeg', 1),
('Special Beach Fish', 'Stingray', 'திருக்கை மீன்', 'Specialty stingray prepared and cleaned for cooking. Known for its unique texture and rich taste in traditional preparations.', '1 KG', 5, 500.00, 'fish_13.jpeg', 1),
('Regular Beach Fish', 'Sardine', 'மத்தி / சாளை', 'Fresh sardines packed with nutrition and flavor. An affordable superfood rich in protein and omega-3 fatty acids.', '1 KG', 6, 350.00, 'fish_16.jpeg', 1),
('Premium Beach Fish', 'Pomfret', 'வாவல்', 'Silver pomfret with delicate texture and sweet flavor. One of the most sought-after fish varieties for special occasions.', '1 KG', 20, 280.00, 'fish_19.jpeg', 1),
('Special Beach Fish', 'Rock Fish', 'பாறை மீன்', 'Fresh rock fish sourced from coastal reefs. Known for its firm texture and suitability for robust curry preparations.', '1 KG', 10, 600.00, 'fish_22.jpeg', 1),
('Special Beach Fish', 'Leather Jacket', 'ஊதா மீன்', 'Specialty leather jacket fish with tender meat. A delicacy enjoyed in traditional coastal cuisine.', '1 KG', 7, 550.00, 'fish_25.jpeg', 1),
('Special Beach Fish', 'Cornet Fish', 'கொம்பன் மீன்', 'Fresh cornet fish with a unique elongated shape. Known for its mild flavor and versatile cooking applications.', '1 KG', 8, 600.00, 'fish_28.jpeg', 1),
('Shellfish Beach', 'Crab', 'நண்டு', 'Fresh live crabs from the coast. A premium shellfish delicacy, perfect for crab masala and pepper crab preparations.', '1 KG', 10, 650.00, 'fish_31.jpeg', 1),
('Shellfish Beach', 'Prawn', 'இறால்', 'Fresh prawns cleaned and ready to cook. Packed with protein and ideal for curries, biryanis, and stir-fry dishes.', '1 KG', 8, 700.00, 'fish_34.jpeg', 1),
('Freshwater', 'Tilapia', 'திலாபியா', 'Farm-fresh tilapia with mild, sweet flavor. An affordable and nutritious freshwater fish perfect for daily meals.', '1 KG', 3, 180.00, 'fish_37.jpeg', 1),
('Beach Fish', 'Red Mullet', 'நாவரிசை', 'Fresh red mullet known for its distinctive appearance and excellent taste. Suitable for frying and curry preparations.', '1 KG', 6, 500.00, 'fish_40.jpeg', 1),
('Premium Beach Fish', 'Emperor Fish', 'விளமீன்', 'Premium emperor fish with thick, meaty fillets. A high-quality catch prized for its robust flavor.', '1 KG', 6, 550.00, 'fish_43.jpeg', 1),
('Regular Beach Fish', 'Marula', 'கிழவன்மீன்', 'Fresh marula fish with firm flesh and rich taste. A traditional favorite in South Indian coastal cooking.', '1 KG', 7, 650.00, 'fish_46.jpeg', 1);
