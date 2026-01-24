-- ============================================
-- DUMMY DATA FOR VEDANT LIGHTS DATABASE
-- Database: vedantlights_db
-- ============================================

-- Clear existing data (optional - uncomment if you want to reset)
-- SET FOREIGN_KEY_CHECKS = 0;
-- TRUNCATE TABLE product_details;
-- TRUNCATE TABLE category_details;
-- TRUNCATE TABLE brand_details;
-- TRUNCATE TABLE user_details;
-- SET FOREIGN_KEY_CHECKS = 1;

-- ============================================
-- 1. BRAND_DETAILS TABLE
-- ============================================
INSERT INTO `brand_details` (`brand_name`, `is_delete`) VALUES
('Crompton', 0),
('Philips', 0),
('Osram', 0),
('Havells', 0),
('Syska', 0),
('Wipro', 0),
('Bajaj', 0),
('Eveready', 0);

-- ============================================
-- 2. CATEGORY_DETAILS TABLE
-- Note: brandId references brand_details.brand_id
-- Note: Column name is 'caterogyName' (typo in original schema)
-- Using subqueries to get brand_id dynamically
-- ============================================
-- Categories for Crompton
INSERT INTO `category_details` (`brandId`, `caterogyName`) VALUES
((SELECT brand_id FROM brand_details WHERE brand_name = 'Crompton' LIMIT 1), 'LED Bulbs'),
((SELECT brand_id FROM brand_details WHERE brand_name = 'Crompton' LIMIT 1), 'LED Tubes'),
((SELECT brand_id FROM brand_details WHERE brand_name = 'Crompton' LIMIT 1), 'LED Panels'),
((SELECT brand_id FROM brand_details WHERE brand_name = 'Crompton' LIMIT 1), 'Street Lights'),
((SELECT brand_id FROM brand_details WHERE brand_name = 'Crompton' LIMIT 1), 'Flood Lights');

-- Categories for Philips
INSERT INTO `category_details` (`brandId`, `caterogyName`) VALUES
((SELECT brand_id FROM brand_details WHERE brand_name = 'Philips' LIMIT 1), 'LED Bulbs'),
((SELECT brand_id FROM brand_details WHERE brand_name = 'Philips' LIMIT 1), 'LED Downlights'),
((SELECT brand_id FROM brand_details WHERE brand_name = 'Philips' LIMIT 1), 'LED Strips'),
((SELECT brand_id FROM brand_details WHERE brand_name = 'Philips' LIMIT 1), 'Smart Lights'),
((SELECT brand_id FROM brand_details WHERE brand_name = 'Philips' LIMIT 1), 'LED Panels');

-- Categories for Osram
INSERT INTO `category_details` (`brandId`, `caterogyName`) VALUES
((SELECT brand_id FROM brand_details WHERE brand_name = 'Osram' LIMIT 1), 'LED Bulbs'),
((SELECT brand_id FROM brand_details WHERE brand_name = 'Osram' LIMIT 1), 'LED Tubes'),
((SELECT brand_id FROM brand_details WHERE brand_name = 'Osram' LIMIT 1), 'Industrial Lights'),
((SELECT brand_id FROM brand_details WHERE brand_name = 'Osram' LIMIT 1), 'Garden Lights');

-- Categories for Havells
INSERT INTO `category_details` (`brandId`, `caterogyName`) VALUES
((SELECT brand_id FROM brand_details WHERE brand_name = 'Havells' LIMIT 1), 'LED Bulbs'),
((SELECT brand_id FROM brand_details WHERE brand_name = 'Havells' LIMIT 1), 'LED Panels'),
((SELECT brand_id FROM brand_details WHERE brand_name = 'Havells' LIMIT 1), 'LED Downlights'),
((SELECT brand_id FROM brand_details WHERE brand_name = 'Havells' LIMIT 1), 'Emergency Lights');

-- Categories for Syska
INSERT INTO `category_details` (`brandId`, `caterogyName`) VALUES
((SELECT brand_id FROM brand_details WHERE brand_name = 'Syska' LIMIT 1), 'LED Bulbs'),
((SELECT brand_id FROM brand_details WHERE brand_name = 'Syska' LIMIT 1), 'LED Strips'),
((SELECT brand_id FROM brand_details WHERE brand_name = 'Syska' LIMIT 1), 'LED Panels');

-- Categories for Wipro
INSERT INTO `category_details` (`brandId`, `caterogyName`) VALUES
((SELECT brand_id FROM brand_details WHERE brand_name = 'Wipro' LIMIT 1), 'LED Bulbs'),
((SELECT brand_id FROM brand_details WHERE brand_name = 'Wipro' LIMIT 1), 'LED Tubes'),
((SELECT brand_id FROM brand_details WHERE brand_name = 'Wipro' LIMIT 1), 'LED Panels');

-- ============================================
-- 3. PRODUCT_DETAILS TABLE
-- Note: Using subqueries to get brand_id and catId dynamically
-- This ensures correct foreign key relationships regardless of auto-increment values
-- ============================================
-- Products for Crompton LED Bulbs
INSERT INTO `product_details` (`brand_id`, `catId`, `pro_name`, `pro_desc`, `pro_tech`, `pro_img`) VALUES
((SELECT brand_id FROM brand_details WHERE brand_name = 'Crompton' LIMIT 1), 
 (SELECT cat_id FROM category_details WHERE brandId = (SELECT brand_id FROM brand_details WHERE brand_name = 'Crompton' LIMIT 1) AND caterogyName = 'LED Bulbs' LIMIT 1), 
 'Crompton 9W LED Bulb', 
 'Energy efficient LED bulb with long lifespan and bright white light. Perfect for home and office use.', 
 'Wattage: 9W, Luminous Flux: 900lm, Color Temperature: 6500K, Base: B22, Life: 25000 hours', 
 'crompton_9w_bulb.png'),
((SELECT brand_id FROM brand_details WHERE brand_name = 'Crompton' LIMIT 1), 
 (SELECT cat_id FROM category_details WHERE brandId = (SELECT brand_id FROM brand_details WHERE brand_name = 'Crompton' LIMIT 1) AND caterogyName = 'LED Bulbs' LIMIT 1), 
 'Crompton 12W LED Bulb', 
 'High brightness LED bulb suitable for large rooms and commercial spaces.', 
 'Wattage: 12W, Luminous Flux: 1200lm, Color Temperature: 6500K, Base: B22, Life: 25000 hours', 
 'crompton_12w_bulb.png'),
((SELECT brand_id FROM brand_details WHERE brand_name = 'Crompton' LIMIT 1), 
 (SELECT cat_id FROM category_details WHERE brandId = (SELECT brand_id FROM brand_details WHERE brand_name = 'Crompton' LIMIT 1) AND caterogyName = 'LED Bulbs' LIMIT 1), 
 'Crompton 15W LED Bulb', 
 'Ultra-bright LED bulb for maximum illumination in spacious areas.', 
 'Wattage: 15W, Luminous Flux: 1500lm, Color Temperature: 6500K, Base: B22, Life: 25000 hours', 
 'crompton_15w_bulb.png');

-- Products for Crompton LED Tubes
INSERT INTO `product_details` (`brand_id`, `catId`, `pro_name`, `pro_desc`, `pro_tech`, `pro_img`) VALUES
((SELECT brand_id FROM brand_details WHERE brand_name = 'Crompton' LIMIT 1), 
 (SELECT cat_id FROM category_details WHERE brandId = (SELECT brand_id FROM brand_details WHERE brand_name = 'Crompton' LIMIT 1) AND caterogyName = 'LED Tubes' LIMIT 1), 
 'Crompton 20W LED Tube Light', 
 'Energy saving LED tube light with uniform light distribution. Ideal for offices and commercial spaces.', 
 'Wattage: 20W, Length: 2 feet, Luminous Flux: 2000lm, Color Temperature: 6500K, Life: 50000 hours', 
 'crompton_20w_tube.png'),
((SELECT brand_id FROM brand_details WHERE brand_name = 'Crompton' LIMIT 1), 
 (SELECT cat_id FROM category_details WHERE brandId = (SELECT brand_id FROM brand_details WHERE brand_name = 'Crompton' LIMIT 1) AND caterogyName = 'LED Tubes' LIMIT 1), 
 'Crompton 36W LED Tube Light', 
 'High efficiency LED tube light for large commercial and industrial applications.', 
 'Wattage: 36W, Length: 4 feet, Luminous Flux: 3600lm, Color Temperature: 6500K, Life: 50000 hours', 
 'crompton_36w_tube.png');

-- Products for Philips LED Bulbs
INSERT INTO `product_details` (`brand_id`, `catId`, `pro_name`, `pro_desc`, `pro_tech`, `pro_img`) VALUES
((SELECT brand_id FROM brand_details WHERE brand_name = 'Philips' LIMIT 1), 
 (SELECT cat_id FROM category_details WHERE brandId = (SELECT brand_id FROM brand_details WHERE brand_name = 'Philips' LIMIT 1) AND caterogyName = 'LED Bulbs' LIMIT 1), 
 'Philips 9W LED Bulb', 
 'Premium quality LED bulb with excellent color rendering and energy efficiency.', 
 'Wattage: 9W, Luminous Flux: 900lm, Color Temperature: 6500K, Base: B22, Life: 25000 hours, CRI: 80', 
 'philips_9w_bulb.png'),
((SELECT brand_id FROM brand_details WHERE brand_name = 'Philips' LIMIT 1), 
 (SELECT cat_id FROM category_details WHERE brandId = (SELECT brand_id FROM brand_details WHERE brand_name = 'Philips' LIMIT 1) AND caterogyName = 'LED Bulbs' LIMIT 1), 
 'Philips 12W LED Bulb', 
 'Bright LED bulb with warm white light perfect for living spaces.', 
 'Wattage: 12W, Luminous Flux: 1200lm, Color Temperature: 3000K, Base: B22, Life: 25000 hours, CRI: 80', 
 'philips_12w_bulb.png');

-- Products for Philips Smart Lights
INSERT INTO `product_details` (`brand_id`, `catId`, `pro_name`, `pro_desc`, `pro_tech`, `pro_img`) VALUES
((SELECT brand_id FROM brand_details WHERE brand_name = 'Philips' LIMIT 1), 
 (SELECT cat_id FROM category_details WHERE brandId = (SELECT brand_id FROM brand_details WHERE brand_name = 'Philips' LIMIT 1) AND caterogyName = 'Smart Lights' LIMIT 1), 
 'Philips Hue Smart Bulb', 
 'WiFi enabled smart LED bulb with app control and voice assistant compatibility.', 
 'Wattage: 9W, Luminous Flux: 800lm, Color: RGB, Connectivity: WiFi, App Control: Yes, Voice Control: Alexa/Google', 
 'philips_hue_smart.png'),
((SELECT brand_id FROM brand_details WHERE brand_name = 'Philips' LIMIT 1), 
 (SELECT cat_id FROM category_details WHERE brandId = (SELECT brand_id FROM brand_details WHERE brand_name = 'Philips' LIMIT 1) AND caterogyName = 'Smart Lights' LIMIT 1), 
 'Philips Smart LED Strip', 
 'RGB color changing LED strip with remote and app control for ambient lighting.', 
 'Wattage: 12W/m, Length: 5m, Colors: RGB, Remote Control: Yes, App Control: Yes', 
 'philips_smart_strip.png');

-- Products for Osram LED Bulbs
INSERT INTO `product_details` (`brand_id`, `catId`, `pro_name`, `pro_desc`, `pro_tech`, `pro_img`) VALUES
((SELECT brand_id FROM brand_details WHERE brand_name = 'Osram' LIMIT 1), 
 (SELECT cat_id FROM category_details WHERE brandId = (SELECT brand_id FROM brand_details WHERE brand_name = 'Osram' LIMIT 1) AND caterogyName = 'LED Bulbs' LIMIT 1), 
 'Osram 9W LED Bulb', 
 'German engineered LED bulb with superior quality and performance.', 
 'Wattage: 9W, Luminous Flux: 900lm, Color Temperature: 6500K, Base: B22, Life: 30000 hours', 
 'osram_9w_bulb.png'),
((SELECT brand_id FROM brand_details WHERE brand_name = 'Osram' LIMIT 1), 
 (SELECT cat_id FROM category_details WHERE brandId = (SELECT brand_id FROM brand_details WHERE brand_name = 'Osram' LIMIT 1) AND caterogyName = 'LED Bulbs' LIMIT 1), 
 'Osram 15W LED Bulb', 
 'High performance LED bulb for industrial and commercial use.', 
 'Wattage: 15W, Luminous Flux: 1500lm, Color Temperature: 6500K, Base: B22, Life: 30000 hours', 
 'osram_15w_bulb.png');

-- Products for Osram Industrial Lights
INSERT INTO `product_details` (`brand_id`, `catId`, `pro_name`, `pro_desc`, `pro_tech`, `pro_img`) VALUES
((SELECT brand_id FROM brand_details WHERE brand_name = 'Osram' LIMIT 1), 
 (SELECT cat_id FROM category_details WHERE brandId = (SELECT brand_id FROM brand_details WHERE brand_name = 'Osram' LIMIT 1) AND caterogyName = 'Industrial Lights' LIMIT 1), 
 'Osram 50W Industrial LED Light', 
 'Heavy duty industrial LED light with high IP rating for harsh environments.', 
 'Wattage: 50W, Luminous Flux: 5000lm, IP Rating: IP65, Operating Temperature: -20°C to 50°C, Life: 50000 hours', 
 'osram_50w_industrial.png'),
((SELECT brand_id FROM brand_details WHERE brand_name = 'Osram' LIMIT 1), 
 (SELECT cat_id FROM category_details WHERE brandId = (SELECT brand_id FROM brand_details WHERE brand_name = 'Osram' LIMIT 1) AND caterogyName = 'Industrial Lights' LIMIT 1), 
 'Osram 100W Industrial LED Light', 
 'Ultra-bright industrial LED light for warehouses and large industrial spaces.', 
 'Wattage: 100W, Luminous Flux: 10000lm, IP Rating: IP65, Operating Temperature: -20°C to 50°C, Life: 50000 hours', 
 'osram_100w_industrial.png');

-- Products for Havells LED Bulbs
INSERT INTO `product_details` (`brand_id`, `catId`, `pro_name`, `pro_desc`, `pro_tech`, `pro_img`) VALUES
((SELECT brand_id FROM brand_details WHERE brand_name = 'Havells' LIMIT 1), 
 (SELECT cat_id FROM category_details WHERE brandId = (SELECT brand_id FROM brand_details WHERE brand_name = 'Havells' LIMIT 1) AND caterogyName = 'LED Bulbs' LIMIT 1), 
 'Havells 9W LED Bulb', 
 'Premium LED bulb with excellent build quality and energy savings.', 
 'Wattage: 9W, Luminous Flux: 900lm, Color Temperature: 6500K, Base: B22, Life: 25000 hours', 
 'havells_9w_bulb.png'),
((SELECT brand_id FROM brand_details WHERE brand_name = 'Havells' LIMIT 1), 
 (SELECT cat_id FROM category_details WHERE brandId = (SELECT brand_id FROM brand_details WHERE brand_name = 'Havells' LIMIT 1) AND caterogyName = 'LED Bulbs' LIMIT 1), 
 'Havells 12W LED Bulb', 
 'Bright LED bulb with instant start and flicker-free operation.', 
 'Wattage: 12W, Luminous Flux: 1200lm, Color Temperature: 6500K, Base: B22, Life: 25000 hours', 
 'havells_12w_bulb.png');

-- Products for Havells LED Panels
INSERT INTO `product_details` (`brand_id`, `catId`, `pro_name`, `pro_desc`, `pro_tech`, `pro_img`) VALUES
((SELECT brand_id FROM brand_details WHERE brand_name = 'Havells' LIMIT 1), 
 (SELECT cat_id FROM category_details WHERE brandId = (SELECT brand_id FROM brand_details WHERE brand_name = 'Havells' LIMIT 1) AND caterogyName = 'LED Panels' LIMIT 1), 
 'Havells 18W LED Panel', 
 'Sleek LED panel light for modern office spaces with uniform illumination.', 
 'Wattage: 18W, Size: 2x2 feet, Luminous Flux: 1800lm, Color Temperature: 6500K, Life: 50000 hours', 
 'havells_18w_panel.png'),
((SELECT brand_id FROM brand_details WHERE brand_name = 'Havells' LIMIT 1), 
 (SELECT cat_id FROM category_details WHERE brandId = (SELECT brand_id FROM brand_details WHERE brand_name = 'Havells' LIMIT 1) AND caterogyName = 'LED Panels' LIMIT 1), 
 'Havells 36W LED Panel', 
 'Large LED panel light for conference rooms and commercial spaces.', 
 'Wattage: 36W, Size: 2x4 feet, Luminous Flux: 3600lm, Color Temperature: 6500K, Life: 50000 hours', 
 'havells_36w_panel.png');

-- Products for Syska LED Bulbs
INSERT INTO `product_details` (`brand_id`, `catId`, `pro_name`, `pro_desc`, `pro_tech`, `pro_img`) VALUES
((SELECT brand_id FROM brand_details WHERE brand_name = 'Syska' LIMIT 1), 
 (SELECT cat_id FROM category_details WHERE brandId = (SELECT brand_id FROM brand_details WHERE brand_name = 'Syska' LIMIT 1) AND caterogyName = 'LED Bulbs' LIMIT 1), 
 'Syska 9W LED Bulb', 
 'Affordable LED bulb with good quality and energy efficiency.', 
 'Wattage: 9W, Luminous Flux: 900lm, Color Temperature: 6500K, Base: B22, Life: 20000 hours', 
 'syska_9w_bulb.png'),
((SELECT brand_id FROM brand_details WHERE brand_name = 'Syska' LIMIT 1), 
 (SELECT cat_id FROM category_details WHERE brandId = (SELECT brand_id FROM brand_details WHERE brand_name = 'Syska' LIMIT 1) AND caterogyName = 'LED Bulbs' LIMIT 1), 
 'Syska 12W LED Bulb', 
 'Value for money LED bulb with bright white light.', 
 'Wattage: 12W, Luminous Flux: 1200lm, Color Temperature: 6500K, Base: B22, Life: 20000 hours', 
 'syska_12w_bulb.png');

-- Products for Syska LED Strips
INSERT INTO `product_details` (`brand_id`, `catId`, `pro_name`, `pro_desc`, `pro_tech`, `pro_img`) VALUES
((SELECT brand_id FROM brand_details WHERE brand_name = 'Syska' LIMIT 1), 
 (SELECT cat_id FROM category_details WHERE brandId = (SELECT brand_id FROM brand_details WHERE brand_name = 'Syska' LIMIT 1) AND caterogyName = 'LED Strips' LIMIT 1), 
 'Syska 5m RGB LED Strip', 
 'Colorful RGB LED strip with remote control for decorative lighting.', 
 'Wattage: 12W/m, Length: 5m, Colors: RGB, Remote Control: Yes, Waterproof: IP65', 
 'syska_5m_rgb_strip.png'),
((SELECT brand_id FROM brand_details WHERE brand_name = 'Syska' LIMIT 1), 
 (SELECT cat_id FROM category_details WHERE brandId = (SELECT brand_id FROM brand_details WHERE brand_name = 'Syska' LIMIT 1) AND caterogyName = 'LED Strips' LIMIT 1), 
 'Syska 10m RGB LED Strip', 
 'Long RGB LED strip perfect for large decorative installations.', 
 'Wattage: 12W/m, Length: 10m, Colors: RGB, Remote Control: Yes, Waterproof: IP65', 
 'syska_10m_rgb_strip.png');

-- Products for Wipro LED Bulbs
INSERT INTO `product_details` (`brand_id`, `catId`, `pro_name`, `pro_desc`, `pro_tech`, `pro_img`) VALUES
((SELECT brand_id FROM brand_details WHERE brand_name = 'Wipro' LIMIT 1), 
 (SELECT cat_id FROM category_details WHERE brandId = (SELECT brand_id FROM brand_details WHERE brand_name = 'Wipro' LIMIT 1) AND caterogyName = 'LED Bulbs' LIMIT 1), 
 'Wipro 9W LED Bulb', 
 'Reliable LED bulb with good performance and competitive pricing.', 
 'Wattage: 9W, Luminous Flux: 900lm, Color Temperature: 6500K, Base: B22, Life: 20000 hours', 
 'wipro_9w_bulb.png'),
((SELECT brand_id FROM brand_details WHERE brand_name = 'Wipro' LIMIT 1), 
 (SELECT cat_id FROM category_details WHERE brandId = (SELECT brand_id FROM brand_details WHERE brand_name = 'Wipro' LIMIT 1) AND caterogyName = 'LED Bulbs' LIMIT 1), 
 'Wipro 15W LED Bulb', 
 'High brightness LED bulb for well-lit spaces.', 
 'Wattage: 15W, Luminous Flux: 1500lm, Color Temperature: 6500K, Base: B22, Life: 20000 hours', 
 'wipro_15w_bulb.png');

-- Products for Wipro LED Tubes
INSERT INTO `product_details` (`brand_id`, `catId`, `pro_name`, `pro_desc`, `pro_tech`, `pro_img`) VALUES
((SELECT brand_id FROM brand_details WHERE brand_name = 'Wipro' LIMIT 1), 
 (SELECT cat_id FROM category_details WHERE brandId = (SELECT brand_id FROM brand_details WHERE brand_name = 'Wipro' LIMIT 1) AND caterogyName = 'LED Tubes' LIMIT 1), 
 'Wipro 20W LED Tube', 
 'Energy efficient LED tube light for office and home use.', 
 'Wattage: 20W, Length: 2 feet, Luminous Flux: 2000lm, Color Temperature: 6500K, Life: 40000 hours', 
 'wipro_20w_tube.png'),
((SELECT brand_id FROM brand_details WHERE brand_name = 'Wipro' LIMIT 1), 
 (SELECT cat_id FROM category_details WHERE brandId = (SELECT brand_id FROM brand_details WHERE brand_name = 'Wipro' LIMIT 1) AND caterogyName = 'LED Tubes' LIMIT 1), 
 'Wipro 36W LED Tube', 
 'Bright LED tube light for commercial applications.', 
 'Wattage: 36W, Length: 4 feet, Luminous Flux: 3600lm, Color Temperature: 6500K, Life: 40000 hours', 
 'wipro_36w_tube.png');

-- ============================================
-- 4. USER_DETAILS TABLE (Admin Users)
-- ============================================
-- Note: If user_id is AUTO_INCREMENT, remove the user_id column from INSERT
-- If user_id is NOT auto-increment, keep the explicit values below

-- OPTION 1: Clear existing users first (uncomment if you want to reset all users)
-- DELETE FROM `user_details`;

-- OPTION 2: Insert with IGNORE (skips duplicates - recommended if data may exist)
-- Version A: If user_id is NOT auto-increment (explicit IDs)
INSERT IGNORE INTO `user_details` (`user_id`, `username`, `password`) VALUES
(1, 'admin', 'admin'),
(2, 'vedant', 'vedant123'),
(3, 'manager', 'manager123'),
(4, 'superadmin', 'superadmin123'),
(5, 'testuser', 'test123');

-- Version B: If user_id IS auto-increment, use this instead (comment out Version A):
-- INSERT IGNORE INTO `user_details` (`username`, `password`) VALUES
-- ('admin', 'admin'),
-- ('vedant', 'vedant123'),
-- ('manager', 'manager123'),
-- ('superadmin', 'superadmin123'),
-- ('testuser', 'test123');

-- OPTION 3: Replace existing users (overwrites if user_id exists)
-- REPLACE INTO `user_details` (`user_id`, `username`, `password`) VALUES
-- (1, 'admin', 'admin'),
-- (2, 'vedant', 'vedant123'),
-- (3, 'manager', 'manager123'),
-- (4, 'superadmin', 'superadmin123'),
-- (5, 'testuser', 'test123');

-- OPTION 4: Insert with ON DUPLICATE KEY UPDATE (updates if exists, inserts if not)
-- INSERT INTO `user_details` (`user_id`, `username`, `password`) VALUES
-- (1, 'admin', 'admin'),
-- (2, 'vedant', 'vedant123'),
-- (3, 'manager', 'manager123'),
-- (4, 'superadmin', 'superadmin123'),
-- (5, 'testuser', 'test123')
-- ON DUPLICATE KEY UPDATE 
--     username = VALUES(username),
--     password = VALUES(password);

-- ============================================
-- NOTES:
-- ============================================
-- 1. The category_details table uses 'caterogyName' (with typo) as per original schema
-- 2. Foreign key relationships:
--    - category_details.brandId -> brand_details.brand_id
--    - product_details.brand_id -> brand_details.brand_id
--    - product_details.catId -> category_details.cat_id
-- 3. Product images (pro_img) are stored as filenames only
--    Actual image files should be placed in: uploads/Product/
-- 4. User passwords are stored in plain text (as per current implementation)
--    Consider hashing passwords in production
-- 5. This script uses subqueries to dynamically resolve foreign key relationships
--    based on brand names and category names, so it works regardless of auto-increment values
-- 6. If you need to verify the data, run these queries:
--    SELECT brand_id, brand_name FROM brand_details;
--    SELECT cat_id, brandId, caterogyName FROM category_details;
--    SELECT pro_id, brand_id, catId, pro_name FROM product_details;
