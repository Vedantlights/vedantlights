-- ============================================
-- VEDANT LIGHTS - DATABASE SCHEMA
-- Run this first, then dummy_data.sql
-- ============================================

CREATE DATABASE IF NOT EXISTS `vedantlights_db`
  DEFAULT CHARACTER SET utf8
  DEFAULT COLLATE utf8_general_ci;

USE `vedantlights_db`;

-- ============================================
-- 1. BRAND_DETAILS
-- ============================================
CREATE TABLE IF NOT EXISTS `brand_details` (
  `brand_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `brand_name` varchar(255) NOT NULL,
  `is_delete` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`brand_id`),
  UNIQUE KEY `brand_name` (`brand_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- ============================================
-- 2. CATEGORY_DETAILS
-- Note: caterogyName has typo (per original schema)
-- ============================================
CREATE TABLE IF NOT EXISTS `category_details` (
  `cat_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `brandId` int(11) unsigned NOT NULL,
  `caterogyName` varchar(255) NOT NULL,
  PRIMARY KEY (`cat_id`),
  KEY `brandId` (`brandId`),
  CONSTRAINT `category_details_brandId_fk` FOREIGN KEY (`brandId`) REFERENCES `brand_details` (`brand_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- ============================================
-- 3. PRODUCT_DETAILS
-- ============================================
CREATE TABLE IF NOT EXISTS `product_details` (
  `pro_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `brand_id` int(11) unsigned NOT NULL,
  `catId` int(11) unsigned NOT NULL,
  `pro_name` varchar(255) NOT NULL,
  `pro_desc` text,
  `pro_tech` text,
  `pro_img` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`pro_id`),
  KEY `brand_id` (`brand_id`),
  KEY `catId` (`catId`),
  CONSTRAINT `product_details_brand_id_fk` FOREIGN KEY (`brand_id`) REFERENCES `brand_details` (`brand_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `product_details_catId_fk` FOREIGN KEY (`catId`) REFERENCES `category_details` (`cat_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- ============================================
-- 4. USER_DETAILS (admin login)
-- ============================================
CREATE TABLE IF NOT EXISTS `user_details` (
  `user_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
