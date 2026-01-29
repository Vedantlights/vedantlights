-- Add PDF column to product_details (run on existing database)
USE `vedantlights_db`;

ALTER TABLE `product_details`
  ADD COLUMN `pro_pdf` varchar(255) DEFAULT NULL AFTER `pro_img`;
