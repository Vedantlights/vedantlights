-- ============================================
-- Migration: Add product_pdf_details table
-- Allows multiple PDFs per product (like brochure items)
-- Each PDF has: name (display name) + file (filename)
-- Run on existing database
-- ============================================

CREATE TABLE IF NOT EXISTS `product_pdf_details` (
  `pdf_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pro_id` int(11) unsigned NOT NULL,
  `pdf_name` varchar(255) NOT NULL COMMENT 'Display name like Datasheet, Manual, etc.',
  `pdf_file` varchar(255) NOT NULL COMMENT 'Filename stored in uploads/Product/',
  PRIMARY KEY (`pdf_id`),
  KEY `pro_id` (`pro_id`),
  CONSTRAINT `product_pdf_pro_id_fk` FOREIGN KEY (`pro_id`) REFERENCES `product_details` (`pro_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Example: Insert a PDF for product ID 1
-- INSERT INTO product_pdf_details (pro_id, pdf_name, pdf_file) VALUES (1, 'Datasheet', 'product1_datasheet.pdf');
-- INSERT INTO product_pdf_details (pro_id, pdf_name, pdf_file) VALUES (1, 'Installation Guide', 'product1_guide.pdf');
