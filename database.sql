CREATE DATABASE IF NOT EXISTS shopkart CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE shopkart;

CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Product categories: each category gets its own section in ShopKart.
CREATE TABLE categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE products (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id INT UNSIGNED NOT NULL,
    name VARCHAR(150) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image_url VARCHAR(500),
    stock INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_products_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT
);

CREATE TABLE addresses (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address_line VARCHAR(255) NOT NULL,
    city VARCHAR(100) NOT NULL,
    state VARCHAR(100) NOT NULL,
    postal_code VARCHAR(20) NOT NULL,
    country VARCHAR(100) NOT NULL DEFAULT 'India',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE orders (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    address_id INT UNSIGNED NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(30) NOT NULL DEFAULT 'razorpay',
    payment_status VARCHAR(30) NOT NULL DEFAULT 'pending',
    razorpay_order_id VARCHAR(100),
    razorpay_payment_id VARCHAR(100),
    status VARCHAR(30) NOT NULL DEFAULT 'placed',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (address_id) REFERENCES addresses(id) ON DELETE RESTRICT
);

CREATE TABLE order_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT UNSIGNED NOT NULL,
    product_id INT UNSIGNED NOT NULL,
    product_name VARCHAR(150) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
);

-- Main ShopKart categories.
INSERT INTO categories (name, slug, description) VALUES
('Shoes', 'shoes', 'Running, casual and everyday footwear.'),
('Bags', 'bags', 'Backpacks, laptop bags and everyday bags.'),
('Mobile Phones', 'mobile-phones', 'Smartphones for everyday use.'),
('Laptops', 'laptops', 'Laptops and computers for work, study and gaming.'),
('AirPods', 'airpods', 'Wireless earbuds and AirPods-style audio products.');

-- 10 entries in each category (50 products total).
INSERT INTO products (category_id, name, description, price, image_url, stock) VALUES
-- Shoes: 10
((SELECT id FROM categories WHERE slug='shoes'), 'Running Shoes Pro', 'Lightweight running shoes with cushioned sole.', 1899.00, 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=800&q=80', 30),
((SELECT id FROM categories WHERE slug='shoes'), 'Casual Sneakers', 'Comfortable sneakers for daily wear.', 2199.00, 'https://images.unsplash.com/photo-1549298916-b41d501d3772?auto=format&fit=crop&w=800&q=80', 25),
((SELECT id FROM categories WHERE slug='shoes'), 'Sports Shoes', 'Breathable sports shoes for training and walking.', 2499.00, 'https://images.unsplash.com/photo-1608231387042-66d1773070a5?auto=format&fit=crop&w=800&q=80', 20),
((SELECT id FROM categories WHERE slug='shoes'), 'White Everyday Shoes', 'Clean white sneakers for everyday outfits.', 1999.00, 'https://images.unsplash.com/photo-1552346154-21d32810aba3?auto=format&fit=crop&w=800&q=80', 18),
((SELECT id FROM categories WHERE slug='shoes'), 'Walking Shoes', 'Soft and supportive shoes for long walks.', 1699.00, 'https://images.unsplash.com/photo-1460353581641-37baddab0fa2?auto=format&fit=crop&w=800&q=80', 35),
((SELECT id FROM categories WHERE slug='shoes'), 'Training Shoes', 'Stable training shoes for gym workouts.', 2799.00, 'https://images.unsplash.com/photo-1539185441755-769473a23570?auto=format&fit=crop&w=800&q=80', 14),
((SELECT id FROM categories WHERE slug='shoes'), 'High Top Sneakers', 'Classic high-top sneakers with padded collar.', 2399.00, 'https://images.unsplash.com/photo-1525966222134-fcfa99b8ae77?auto=format&fit=crop&w=800&q=80', 16),
((SELECT id FROM categories WHERE slug='shoes'), 'Men Casual Shoes', 'Smart casual shoes for office and outings.', 2299.00, 'https://images.unsplash.com/photo-1560769629-975ec94e6a86?auto=format&fit=crop&w=800&q=80', 21),
((SELECT id FROM categories WHERE slug='shoes'), 'Street Sneakers', 'Modern street-style sneakers with durable outsole.', 2599.00, 'https://images.unsplash.com/photo-1495555961986-6d4c1ecb7be3?auto=format&fit=crop&w=800&q=80', 19),
((SELECT id FROM categories WHERE slug='shoes'), 'Lightweight Sports Shoes', 'Flexible lightweight shoes for active days.', 2099.00, 'https://images.unsplash.com/photo-1554130848-1c7e5a8e0e9b?auto=format&fit=crop&w=800&q=80', 24),

-- Bags / backpacks: 10
((SELECT id FROM categories WHERE slug='bags'), 'Laptop Backpack', 'Water-resistant backpack with laptop compartment.', 999.00, 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?auto=format&fit=crop&w=800&q=80', 40),
((SELECT id FROM categories WHERE slug='bags'), 'Travel Backpack', 'Spacious backpack for travel and everyday use.', 1499.00, 'https://images.unsplash.com/photo-1622560480605-d83c853bc5c3?auto=format&fit=crop&w=800&q=80', 28),
((SELECT id FROM categories WHERE slug='bags'), 'College Backpack', 'Durable backpack with multiple student compartments.', 899.00, 'https://images.unsplash.com/photo-1581605405669-fcdf81165afa?auto=format&fit=crop&w=800&q=80', 32),
((SELECT id FROM categories WHERE slug='bags'), 'Office Laptop Bag', 'Professional laptop bag for office and business travel.', 1299.00, 'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?auto=format&fit=crop&w=800&q=80', 20),
((SELECT id FROM categories WHERE slug='bags'), 'Messenger Bag', 'Compact messenger bag for documents and gadgets.', 799.00, 'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?auto=format&fit=crop&w=800&q=80', 26),
((SELECT id FROM categories WHERE slug='bags'), 'Casual Daypack', 'Small everyday backpack with comfortable straps.', 1099.00, 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?auto=format&fit=crop&w=800&q=80', 22),
((SELECT id FROM categories WHERE slug='bags'), 'Gym Duffel Bag', 'Roomy duffel bag for gym and short trips.', 1199.00, 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?auto=format&fit=crop&w=800&q=80', 17),
((SELECT id FROM categories WHERE slug='bags'), 'Travel Sling Bag', 'Lightweight sling bag for essentials and travel.', 699.00, 'https://images.unsplash.com/photo-1590874103328-eac38a683ce7?auto=format&fit=crop&w=800&q=80', 29),
((SELECT id FROM categories WHERE slug='bags'), 'Waterproof Backpack', 'Weather-resistant backpack for outdoor use.', 1599.00, 'https://images.unsplash.com/photo-1622260614153-03223fb72052?auto=format&fit=crop&w=800&q=80', 13),
((SELECT id FROM categories WHERE slug='bags'), 'Classic School Bag', 'Comfortable school bag with organized storage.', 849.00, 'https://images.unsplash.com/photo-1588072432836-e10032774350?auto=format&fit=crop&w=800&q=80', 31),

-- Mobile phones: 10
((SELECT id FROM categories WHERE slug='mobile-phones'), 'Android Smartphone A1', 'Affordable Android smartphone with bright display.', 12999.00, 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?auto=format&fit=crop&w=800&q=80', 15),
((SELECT id FROM categories WHERE slug='mobile-phones'), '5G Smartphone Pro', 'Fast 5G smartphone for everyday use.', 17999.00, 'https://images.unsplash.com/photo-1598327105666-5b89351aff97?auto=format&fit=crop&w=800&q=80', 12),
((SELECT id FROM categories WHERE slug='mobile-phones'), 'Budget Smartphone', 'Value-focused smartphone for calls, apps and media.', 8999.00, 'https://images.unsplash.com/photo-1510557880182-3d4d3cba35a5?auto=format&fit=crop&w=800&q=80', 24),
((SELECT id FROM categories WHERE slug='mobile-phones'), 'Camera Phone X', 'Smartphone with a high-resolution camera system.', 21999.00, 'https://images.unsplash.com/photo-1556656793-08538906a9f8?auto=format&fit=crop&w=800&q=80', 10),
((SELECT id FROM categories WHERE slug='mobile-phones'), 'Gaming Phone G5', 'Performance smartphone designed for mobile gaming.', 26999.00, 'https://images.unsplash.com/photo-1592286927505-2fd4f0b4a7d1?auto=format&fit=crop&w=800&q=80', 8),
((SELECT id FROM categories WHERE slug='mobile-phones'), 'Business Smartphone', 'Reliable phone with large display and long battery life.', 15999.00, 'https://images.unsplash.com/photo-1533228100845-08145b01de14?auto=format&fit=crop&w=800&q=80', 14),
((SELECT id FROM categories WHERE slug='mobile-phones'), 'Compact Smartphone', 'Compact phone for simple everyday use.', 11999.00, 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=800&q=80', 18),
((SELECT id FROM categories WHERE slug='mobile-phones'), 'Ultra Display Phone', 'Large display smartphone for video and entertainment.', 23999.00, 'https://images.unsplash.com/photo-1592899677977-9c10ca588bbd?auto=format&fit=crop&w=800&q=80', 9),
((SELECT id FROM categories WHERE slug='mobile-phones'), '5G Camera Phone', '5G smartphone with versatile cameras.', 19999.00, 'https://images.unsplash.com/photo-1567581935884-3349723552ca?auto=format&fit=crop&w=800&q=80', 11),
((SELECT id FROM categories WHERE slug='mobile-phones'), 'Everyday Smartphone', 'Balanced smartphone for work, study and entertainment.', 13999.00, 'https://images.unsplash.com/photo-1598327105666-5b89351aff97?auto=format&fit=crop&w=800&q=80', 16),

-- Laptops: 10
((SELECT id FROM categories WHERE slug='laptops'), 'Student Laptop', 'Reliable laptop for study, coding and office work.', 45999.00, 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?auto=format&fit=crop&w=800&q=80', 10),
((SELECT id FROM categories WHERE slug='laptops'), 'Gaming Laptop', 'High-performance laptop for gaming and development.', 74999.00, 'https://images.unsplash.com/photo-1603302576837-37561b2e2302?auto=format&fit=crop&w=800&q=80', 7),
((SELECT id FROM categories WHERE slug='laptops'), 'Business Laptop', 'Slim business laptop for office and productivity.', 58999.00, 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?auto=format&fit=crop&w=800&q=80', 9),
((SELECT id FROM categories WHERE slug='laptops'), 'Coding Laptop', 'Developer-friendly laptop for programming and projects.', 52999.00, 'https://images.unsplash.com/photo-1531297484001-80022131f5a1?auto=format&fit=crop&w=800&q=80', 12),
((SELECT id FROM categories WHERE slug='laptops'), 'Thin and Light Laptop', 'Portable laptop with a slim design for students.', 49999.00, 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=800&q=80', 8),
((SELECT id FROM categories WHERE slug='laptops'), 'Creator Laptop', 'Powerful laptop for editing, design and content creation.', 82999.00, 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?auto=format&fit=crop&w=800&q=80', 5),
((SELECT id FROM categories WHERE slug='laptops'), 'Office Laptop', 'Everyday laptop for documents, meetings and browsing.', 41999.00, 'https://images.unsplash.com/photo-1484788984921-03950022c9ef?auto=format&fit=crop&w=800&q=80', 13),
((SELECT id FROM categories WHERE slug='laptops'), 'Performance Laptop', 'Fast laptop for multitasking and development.', 67999.00, 'https://images.unsplash.com/photo-1602080858428-57174f9431cf?auto=format&fit=crop&w=800&q=80', 6),
((SELECT id FROM categories WHERE slug='laptops'), 'Student Pro Laptop', 'Balanced laptop for classes, coding and entertainment.', 47999.00, 'https://images.unsplash.com/photo-1525547719571-a2d4ac8945e2?auto=format&fit=crop&w=800&q=80', 11),
((SELECT id FROM categories WHERE slug='laptops'), 'USB-C Hub Laptop Kit', 'Laptop bundle with a multi-port USB-C hub.', 48999.00, 'https://images.unsplash.com/photo-1625842268584-8f3296236761?auto=format&fit=crop&w=800&q=80', 10),

-- AirPods / wireless earbuds: 10
((SELECT id FROM categories WHERE slug='airpods'), 'AirPods Pro 2', 'Premium wireless earbuds with active noise cancellation.', 24999.00, 'https://images.unsplash.com/photo-1600294037681-c80b4cb5b434?auto=format&fit=crop&w=800&q=80', 12),
((SELECT id FROM categories WHERE slug='airpods'), 'AirPods 3rd Gen', 'Wireless earbuds with spatial audio support.', 18999.00, 'https://images.unsplash.com/photo-1606220945770-b5b6c2c55bf1?auto=format&fit=crop&w=800&q=80', 15),
((SELECT id FROM categories WHERE slug='airpods'), 'AirPods Lite', 'Compact wireless earbuds for everyday listening.', 4999.00, 'https://images.unsplash.com/photo-1588423771073-b8903fbb85b5?auto=format&fit=crop&w=800&q=80', 25),
((SELECT id FROM categories WHERE slug='airpods'), 'AirPods Max Style', 'Over-ear wireless headphones with premium sound.', 12999.00, 'https://images.unsplash.com/photo-1546435770-a3e426bf472b?auto=format&fit=crop&w=800&q=80', 9),
((SELECT id FROM categories WHERE slug='airpods'), 'Wireless Buds Pro', 'Noise-reducing wireless earbuds with charging case.', 5999.00, 'https://images.unsplash.com/photo-1590658268037-6bf12165a8df?auto=format&fit=crop&w=800&q=80', 18),
((SELECT id FROM categories WHERE slug='airpods'), 'Wireless Buds X', 'Lightweight earbuds with touch controls.', 3999.00, 'https://images.unsplash.com/photo-1606220945770-b5b6c2c55bf1?auto=format&fit=crop&w=800&q=80', 21),
((SELECT id FROM categories WHERE slug='airpods'), 'ANC Earbuds', 'Wireless earbuds with active noise reduction.', 6999.00, 'https://images.unsplash.com/photo-1600294037681-c80b4cb5b434?auto=format&fit=crop&w=800&q=80', 14),
((SELECT id FROM categories WHERE slug='airpods'), 'Sport Earbuds', 'Secure-fit wireless earbuds for workouts.', 4499.00, 'https://images.unsplash.com/photo-1598331668826-20cecc596b86?auto=format&fit=crop&w=800&q=80', 17),
((SELECT id FROM categories WHERE slug='airpods'), 'Daily Wireless Earbuds', 'Simple wireless earbuds for music and calls.', 2999.00, 'https://images.unsplash.com/photo-1590658268037-6bf12165a8df?auto=format&fit=crop&w=800&q=80', 30),
((SELECT id FROM categories WHERE slug='airpods'), 'Premium Earbuds', 'Premium wireless earbuds with clear audio.', 8999.00, 'https://images.unsplash.com/photo-1588423771073-b8903fbb85b5?auto=format&fit=crop&w=800&q=80', 10);
