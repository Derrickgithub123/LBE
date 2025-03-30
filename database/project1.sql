-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 30, 2025 at 05:34 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `project`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `first_name`, `last_name`, `email`, `password_hash`, `created_at`) VALUES
(1, 'Venom', '19', 'venom@canol.top', '$2y$10$BEAHNQTfPMCi2IgB6oGu1OF2Uo9EHiSJMy5E6jD.hfYNz2xE0lz7m', '2025-03-30 12:28:36');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `name`, `description`) VALUES
(1, 'Default Category', NULL),
(2, 'Laptops', NULL),
(3, 'Smartphones', NULL),
(4, 'Large Appliances', NULL),
(5, 'Other Appliances', NULL),
(6, 'Small Appliances', NULL),
(7, 'Cameras', NULL),
(8, 'Security Devices', NULL),
(9, 'Networking Devices', NULL),
(10, 'Computing Devices', NULL),
(11, 'Electrical Accessories', NULL),
(12, 'Laptops', NULL),
(13, 'Smartphones', NULL),
(14, 'Large Appliances', NULL),
(15, 'Other Appliances', NULL),
(16, 'Small Appliances', NULL),
(17, 'Cameras', NULL),
(18, 'Security Devices', NULL),
(19, 'Networking Devices', NULL),
(20, 'Computing Devices', NULL),
(21, 'Electrical Accessories', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `legal_documents`
--

CREATE TABLE `legal_documents` (
  `document_id` int(11) NOT NULL,
  `type` enum('terms','privacy') NOT NULL,
  `content` text NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mpesa_payments`
--

CREATE TABLE `mpesa_payments` (
  `mpesa_payment_id` int(11) NOT NULL,
  `payment_id` int(11) NOT NULL,
  `mpesa_transaction_id` varchar(255) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `transaction_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `offers`
--

CREATE TABLE `offers` (
  `offer_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `discount_price` decimal(10,2) NOT NULL,
  `offer_name` varchar(100) NOT NULL DEFAULT 'Special Offer',
  `discount_percentage` decimal(5,2) DEFAULT 0.00,
  `is_active` tinyint(1) DEFAULT 0,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `payment_method` enum('mpesa','paypal','bank_transfer') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(10) NOT NULL DEFAULT 'KES',
  `status` enum('pending','completed','failed','refunded') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `transaction_reference` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `original_price` decimal(10,2) DEFAULT NULL,
  `discount` varchar(10) DEFAULT NULL,
  `price_difference` decimal(10,2) DEFAULT NULL,
  `stock_quantity` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `name`, `description`, `image`, `price`, `original_price`, `discount`, `price_difference`, `stock_quantity`, `category_id`, `image_url`, `created_at`) VALUES
(1, 'Samsung Galaxy S23', 'High performance and stunning design.', 'galaxy s23.jpeg', 85500.00, 95000.00, '10% off', 9500.00, 10, 1, 'http://localhost/portifolio/img/galaxy s23.jpeg', '2025-03-30 10:02:36'),
(2, 'Samsung Galaxy A54', 'Affordable with great features.', 'A54.jpeg', 40500.00, 45000.00, '10% off', 4500.00, 10, 1, 'http://localhost/portifolio/img/A54.jpeg', '2025-03-30 10:02:36'),
(3, 'Samsung Galaxy Z Flip', 'Foldable and futuristic.', 'Z flip.jpeg', 117000.00, 130000.00, '10% off', 13000.00, 10, 1, 'http://localhost/portifolio/img/Z flip.jpeg', '2025-03-30 10:02:36'),
(4, 'Samsung Galaxy S22', 'Compact design with great performance.', 'S22.jpeg', 72000.00, 80000.00, '10% off', 8000.00, 10, 1, 'http://localhost/portifolio/img/S22.jpeg', '2025-03-30 10:02:36'),
(5, 'Samsung Galaxy A72', 'Large display with good performance.', 'A72.jpeg', 40500.00, 45000.00, '10% off', 4500.00, 10, 1, 'http://localhost/portifolio/img/A72.jpeg', '2025-03-30 10:02:36'),
(6, 'Samsung Galaxy A51', 'Good camera and performance at a budget price.', 'A51.jpeg', 22500.00, 25000.00, '10% off', 2500.00, 10, 1, 'http://localhost/portifolio/img/A51.jpeg', '2025-03-30 10:02:36'),
(7, 'Samsung Galaxy S20 Ultra', 'Flagship with a powerful camera and screen.', 'S20.jpeg', 121500.00, 135000.00, '10% off', 13500.00, 10, 1, 'http://localhost/portifolio/img/S20.jpeg', '2025-03-30 10:02:36'),
(8, 'Samsung Galaxy M12', 'Big battery with an affordable price.', 'M12.jpeg', 18000.00, 20000.00, '10% off', 2000.00, 10, 1, 'http://localhost/portifolio/img/M12.jpeg', '2025-03-30 10:02:36'),
(9, 'Samsung Galaxy S21 FE', 'Flagship performance at a lower price.', 'S21 FE.jpeg', 81000.00, 90000.00, '10% off', 9000.00, 10, 1, 'http://localhost/portifolio/img/S21 FE.jpeg', '2025-03-30 10:02:36'),
(10, 'Samsung Galaxy M42', 'Mid-range phone with a large battery.', 'M42.jpeg', 25200.00, 28000.00, '10% off', 2800.00, 10, 1, 'http://localhost/portifolio/img/M42.jpeg', '2025-03-30 10:02:36'),
(11, 'Samsung Galaxy Z Flip 5', 'Compact and foldable with innovative design.', 'z flip5.jpeg', 135000.00, 150000.00, '10% off', 15000.00, 10, 1, 'http://localhost/portifolio/img/z flip5.jpeg', '2025-03-30 10:02:36'),
(12, 'Samsung Galaxy A02', 'Affordable with basic features.', 'AO2.jpeg', 13500.00, 15000.00, '10% off', 1500.00, 10, 1, 'http://localhost/portifolio/img/AO2.jpeg', '2025-03-30 10:02:36'),
(13, 'Samsung Galaxy S10e', 'Flagship specs at a smaller price.', 'S10E.jpeg', 45000.00, 50000.00, '10% off', 5000.00, 10, 1, 'http://localhost/portifolio/img/S10E.jpeg', '2025-03-30 10:02:37'),
(14, 'Samsung Galaxy A11', 'Affordable entry-level smartphone.', 'A11.jpeg', 16200.00, 18000.00, '10% off', 1800.00, 10, 1, 'http://localhost/portifolio/img/A11.jpeg', '2025-03-30 10:02:37'),
(15, 'Samsung Galaxy A32', 'Affordable with a large display.', 'A32.jpeg', 19800.00, 22000.00, '10% off', 2200.00, 10, 1, 'http://localhost/portifolio/img/A32.jpeg', '2025-03-30 10:02:37'),
(16, 'Samsung Galaxy Note 20 Ultra', 'Powerful performance with the S Pen.', 'NOTE S20 ULTRA.jpeg', 135000.00, 150000.00, '10% off', 15000.00, 10, 1, 'http://localhost/portifolio/img/NOTE S20 ULTRA.jpeg', '2025-03-30 10:02:37'),
(17, 'Samsung Galaxy A10', 'Budget-friendly with great performance.', 'A10.jpeg', 12600.00, 14000.00, '10% off', 1400.00, 10, 1, 'http://localhost/portifolio/img/A10.jpeg', '2025-03-30 10:02:37'),
(18, 'Samsung Galaxy S9', 'Classic flagship with great features.', 'S9.jpeg', 40500.00, 45000.00, '10% off', 4500.00, 10, 1, 'http://localhost/portifolio/img/S9.jpeg', '2025-03-30 10:02:37'),
(19, 'Samsung Galaxy A53', 'Great value for money with smooth performance.', 'A53.jpeg', 36000.00, 40000.00, '10% off', 4000.00, 10, 1, 'http://localhost/portifolio/img/A53.jpeg', '2025-03-30 10:02:37'),
(20, 'Samsung Galaxy Note 20', 'Powerful device for productivity.', 'NOTE 20.jpeg', 108000.00, 120000.00, '10% off', 12000.00, 10, 1, 'http://localhost/portifolio/img/NOTE 20.jpeg', '2025-03-30 10:02:37'),
(21, 'iPhone 15 Pro Max', 'Top-tier performance with a stunning display.', 'iphone 15 pro max.jpeg', 198000.00, 220000.00, '10% off', 22000.00, 10, 1, 'http://localhost/portifolio/img/iphone 15 pro max.jpeg', '2025-03-30 10:02:38'),
(22, 'iPhone 14 Pro', 'Next-gen performance with a Pro-level camera system.', '14 pro.jpeg', 139500.00, 155000.00, '10% off', 15500.00, 10, 1, 'http://localhost/portifolio/img/14 pro.jpeg', '2025-03-30 10:02:38'),
(23, 'iPhone 14 Plus', 'Big screen, bigger battery, premium performance.', '14 plus.jpeg', 121500.00, 135000.00, '10% off', 13500.00, 10, 1, 'http://localhost/portifolio/img/14 plus.jpeg', '2025-03-30 10:02:38'),
(24, 'iPhone 13 Pro Max', 'Pro camera system and incredible battery life.', 'IPHONE 13 PRO MAX.jpeg', 135000.00, 150000.00, '10% off', 15000.00, 10, 1, 'http://localhost/portifolio/img/IPHONE 13 PRO MAX.jpeg', '2025-03-30 10:02:38'),
(25, 'iPhone 13 Mini', 'Compact design with powerful performance.', '13 mini.jpeg', 85500.00, 95000.00, '10% off', 9500.00, 10, 1, 'http://localhost/portifolio/img/13 mini.jpeg', '2025-03-30 10:02:38'),
(26, 'iPhone 12', '5G-ready with a stunning OLED display.', 'iphone 12.jpeg', 76500.00, 85000.00, '10% off', 8500.00, 10, 1, 'http://localhost/portifolio/img/iphone 12.jpeg', '2025-03-30 10:02:38'),
(27, 'iPhone 12 Pro Max', 'Largest iPhone screen with advanced camera features.', '12 pro max.jpeg', 112500.00, 125000.00, '10% off', 12500.00, 10, 1, 'http://localhost/portifolio/img/12 pro max.jpeg', '2025-03-30 10:02:38'),
(28, 'iPhone SE (2022)', 'Compact, fast, and affordable iPhone.', 'IPHONE SE.jpeg', 49500.00, 55000.00, '10% off', 5500.00, 10, 1, 'http://localhost/portifolio/img/IPHONE SE.jpeg', '2025-03-30 10:02:38'),
(29, 'iPhone 11', 'Great performance with dual-camera system.', 'IPHONE 11.jpeg', 67500.00, 75000.00, '10% off', 7500.00, 10, 1, 'http://localhost/portifolio/img/IPHONE 11.jpeg', '2025-03-30 10:02:38'),
(30, 'iPhone 15 Pro', 'Powerful, sleek, and elegant.', 'iphone 15 pro max.jpeg', 162000.00, 180000.00, '10% off', 18000.00, 10, 1, 'http://localhost/portifolio/img/iphone 15 pro max.jpeg', '2025-03-30 10:02:38'),
(31, 'Xiaomi 13 Pro', 'Premium flagship with Leica camera and Snapdragon 8 Gen 2.', 'XIAOMI 13 PRO.jpeg', 121500.00, 135000.00, '10% off', 13500.00, 10, 1, 'http://localhost/portifolio/img/XIAOMI 13 PRO.jpeg', '2025-03-30 10:02:38'),
(32, 'Xiaomi 13', 'Affordable flagship with Snapdragon 8 Gen 2 and 50 MP camera.', 'XIAOMI 13.jpeg', 108000.00, 120000.00, '10% off', 12000.00, 10, 1, 'http://localhost/portifolio/img/XIAOMI 13.jpeg', '2025-03-30 10:02:38'),
(33, 'Redmi Note 12 Pro', 'Mid-range phone with excellent performance and camera.', 'REDMI NOTE 12 PRO.jpeg', 40500.00, 45000.00, '10% off', 4500.00, 10, 1, 'http://localhost/portifolio/img/REDMI NOTE 12 PRO.jpeg', '2025-03-30 10:02:38'),
(34, 'Redmi Note 12', 'Budget-friendly phone with great performance and features.', 'REDMI NOTE12.jpeg', 27000.00, 30000.00, '10% off', 3000.00, 10, 1, 'http://localhost/portifolio/img/REDMI NOTE12.jpeg', '2025-03-30 10:02:38'),
(35, 'Xiaomi Mi 11', 'Flagship with Snapdragon 888 and 108 MP camera.', 'XIAOMI MI 11.jpeg', 72000.00, 80000.00, '10% off', 8000.00, 10, 1, 'http://localhost/portifolio/img/XIAOMI MI 11.jpeg', '2025-03-30 10:02:38'),
(36, 'Xiaomi 11T Pro', 'Fast charging, great performance, and stunning design.', 'XIAOMI 11T PRO.jpeg', 67500.00, 75000.00, '10% off', 7500.00, 10, 1, 'http://localhost/portifolio/img/XIAOMI 11T PRO.jpeg', '2025-03-30 10:02:38'),
(37, 'Redmi K50 Pro', 'Premium performance with Dimensity 9000 and 108 MP camera.', 'REDMI K50 PRO.jpeg', 54000.00, 60000.00, '10% off', 6000.00, 10, 1, 'http://localhost/portifolio/img/REDMI K50 PRO.jpeg', '2025-03-30 10:02:38'),
(38, 'Xiaomi Poco X5 Pro', 'Powerful performance for gaming and multitasking.', 'XIAOMI POCO X5 PRO.jpeg', 31500.00, 35000.00, '10% off', 3500.00, 10, 1, 'http://localhost/portifolio/img/XIAOMI POCO X5 PRO.jpeg', '2025-03-30 10:02:38'),
(39, 'Xiaomi Mi Mix 4', 'Innovative design with an under-display camera.', 'XIAOMI MI MIX 4.jpeg', 90000.00, 100000.00, '10% off', 10000.00, 10, 1, 'http://localhost/portifolio/img/XIAOMI MI MIX 4.jpeg', '2025-03-30 10:02:39'),
(40, 'Redmi Note 11 Pro+', 'Great value for money with fast charging and 108 MP camera.', 'REDMI 11 PRO+.jpeg', 36000.00, 40000.00, '10% off', 4000.00, 10, 1, 'http://localhost/portifolio/img/REDMI 11 PRO+.jpeg', '2025-03-30 10:02:39'),
(41, 'Nokia 5.4', 'Great camera with cinematic video and performance.', 'NOKIA 5.4.jpeg', 22500.00, 25000.00, '10% off', 2500.00, 10, 1, 'http://localhost/portifolio/img/NOKIA 5.4.jpeg', '2025-03-30 10:02:39'),
(42, 'Nokia 8.3 5G', '5G connectivity and powerful performance.', 'NOKIA 8.3 5G.jpeg', 40500.00, 45000.00, '10% off', 4500.00, 10, 1, 'http://localhost/portifolio/img/NOKIA 8.3 5G.jpeg', '2025-03-30 10:02:39'),
(43, 'Nokia 3.4', 'Affordable phone with solid performance and a big display.', 'NOKIA 3.4.jpeg', 16200.00, 18000.00, '10% off', 1800.00, 10, 1, 'http://localhost/portifolio/img/NOKIA 3.4.jpeg', '2025-03-30 10:02:39'),
(44, 'Nokia 2.4', 'Reliable phone with long-lasting battery life.', 'NOKIA 2.4.jpeg', 13500.00, 15000.00, '10% off', 1500.00, 10, 1, 'http://localhost/portifolio/img/NOKIA 2.4.jpeg', '2025-03-30 10:02:39'),
(45, 'Nokia 7.2', 'Stunning camera with ZEISS optics and solid build quality.', 'NOKIA 7.2.jpeg', 31500.00, 35000.00, '10% off', 3500.00, 10, 1, 'http://localhost/portifolio/img/NOKIA 7.2.jpeg', '2025-03-30 10:02:39'),
(46, 'Nokia 1.4', 'Affordable with a large screen and good performance.', 'NOKIA 1.4.jpeg', 10800.00, 12000.00, '10% off', 1200.00, 10, 1, 'http://localhost/portifolio/img/NOKIA 1.4.jpeg', '2025-03-30 10:02:39'),
(47, 'Nokia 9 PureView', 'Revolutionary five-camera system with incredible photo quality.', 'NOKIA 9PUREVIEW.jpeg', 76500.00, 85000.00, '10% off', 8500.00, 10, 1, 'http://localhost/portifolio/img/NOKIA 9PUREVIEW.jpeg', '2025-03-30 10:02:39'),
(48, 'Nokia XR20', 'Durable and reliable.', 'NOKIA XR20.jpeg', 45000.00, 50000.00, '10% off', 5000.00, 10, 1, 'http://localhost/portifolio/img/NOKIA XR20.jpeg', '2025-03-30 10:02:39'),
(49, 'Nokia G50', 'Feature-packed and affordable.', 'NOKIA G50.jpeg', 31500.00, 35000.00, '10% off', 3500.00, 10, 1, 'http://localhost/portifolio/img/NOKIA G50.jpeg', '2025-03-30 10:02:39'),
(50, 'Nokia 2720 Flip', 'Classic design reinvented.', 'NOKIA 2720 FLIP.jpeg', 18000.00, 20000.00, '10% off', 2000.00, 10, 1, 'http://localhost/portifolio/img/NOKIA 2720 FLIP.jpeg', '2025-03-30 10:02:39'),
(51, 'Klipsch Reference Woofer', 'Powerful bass with premium sound quality.', 'klipsch woofer.jpeg', 3330.00, 3700.00, '10% off', 370.00, 10, 1, 'http://localhost/portifolio/img/klipsch woofer.jpeg', '2025-03-30 10:02:39'),
(52, 'SVS PB-1000', 'Deep, clean bass for your home theater.', 'svs subwoofer.jpeg', 5220.00, 5800.00, '10% off', 580.00, 10, 1, 'http://localhost/portifolio/img/svs subwoofer.jpeg', '2025-03-30 10:02:39'),
(53, 'Yamaha NS-SW300', 'Stylish design with exceptional bass performance.', 'yamaha.jpeg', 2610.00, 2900.00, '10% off', 290.00, 10, 1, 'http://localhost/portifolio/img/yamaha.jpeg', '2025-03-30 10:02:39'),
(54, 'Polk Audio HTS 10', 'Compact and affordable, perfect for small spaces.', 'pol audio subwoofer.jpeg', 4050.00, 4500.00, '10% off', 450.00, 10, 1, 'http://localhost/portifolio/img/pol audio subwoofer.jpeg', '2025-03-30 10:02:39'),
(55, 'Vitron x-bass', 'Vitron V527 2.1CH Multimedia Speaker System', 'vitron.webp', 8100.00, 9000.00, '10% off', 900.00, 10, 1, 'http://localhost/portifolio/img/vitron.webp', '2025-03-30 10:02:39'),
(56, 'JBL BassPro Hub', 'Portable design with premium bass.', 'jbl base pro woofer.jpeg', 2700.00, 3000.00, '10% off', 300.00, 10, 1, 'http://localhost/portifolio/img/jbl base pro woofer.jpeg', '2025-03-30 10:02:39'),
(57, 'AMTEC AM-006', 'Amtec AM-006 15000W X-Bass Subwoofer Multimedia', 'AMTEC SUBWOOFER.jpg', 22500.00, 25000.00, '10% off', 2500.00, 10, 1, 'http://localhost/portifolio/img/AMTEC SUBWOOFER.jpg', '2025-03-30 10:02:39'),
(58, 'AMPEX A-8102', 'Ampex A-8102 Multimedia Speaker System 3.1CH 40000w', 'ampex subwoofer1.jpeg', 4500.00, 5000.00, '10% off', 500.00, 10, 1, 'http://localhost/portifolio/img/ampex subwoofer1.jpeg', '2025-03-30 10:02:39'),
(59, 'ICONIX 2.1CH', 'active subwoofer system', 'iconix subwoofer.webp', 8010.00, 8900.00, '10% off', 890.00, 10, 1, 'http://localhost/portifolio/img/iconix subwoofer.webp', '2025-03-30 10:02:39'),
(60, 'Samsung TV', 'Innovative smart TV with 4K resolution.', 'samsung tv.jpeg', 72000.00, 80000.00, '10% off', 8000.00, 10, 1, 'http://localhost/portifolio/img/samsung tv.jpeg', '2025-03-30 10:02:39'),
(61, 'Skyworth TV', 'Skyworth 55″ 55G3B QLED 4k UHD Google Tv', 'skyworth tv.jpg', 49500.00, 55000.00, '10% off', 5500.00, 10, 1, 'http://localhost/portifolio/img/skyworth tv.jpg', '2025-03-30 10:02:40'),
(62, 'Xiaomi TV', 'Xiaomi Mi TV P1 32 inch HD Smart Android LED TV', 'xiami tv.jpg', 72000.00, 80000.00, '10% off', 8000.00, 10, 1, 'http://localhost/portifolio/img/xiami tv.jpg', '2025-03-30 10:02:40'),
(63, 'LG TV', 'Crystal clear OLED display and smart features.', 'lg.jpeg', 67500.00, 75000.00, '10% off', 7500.00, 10, 1, 'http://localhost/portifolio/img/lg.jpeg', '2025-03-30 10:02:40'),
(64, 'Sony TV', 'Top-tier LED TV with excellent picture quality.', 'sony tv.jpeg', 85500.00, 95000.00, '10% off', 9500.00, 10, 1, 'http://localhost/portifolio/img/sony tv.jpeg', '2025-03-30 10:02:40'),
(65, 'Panasonic TV', 'Advanced features and sleek design.', 'panasonic.jpeg', 63000.00, 70000.00, '10% off', 7000.00, 10, 1, 'http://localhost/portifolio/img/panasonic.jpeg', '2025-03-30 10:02:40'),
(66, 'TCL TV', 'Affordable, high-quality TV with smart features.', 'tsl tv.jpeg', 49500.00, 55000.00, '10% off', 5500.00, 10, 1, 'http://localhost/portifolio/img/tsl tv.jpeg', '2025-03-30 10:02:40'),
(67, 'Sharp TV', 'High definition display with stunning picture quality.', 'sharp tv.jpeg', 54000.00, 60000.00, '10% off', 6000.00, 10, 1, 'http://localhost/portifolio/img/sharp tv.jpeg', '2025-03-30 10:02:40'),
(68, 'Vizio TV', 'Affordable LED TVs with great features.', 'vizio tv.jpeg', 45000.00, 50000.00, '10% off', 5000.00, 10, 1, 'http://localhost/portifolio/img/vizio tv.jpeg', '2025-03-30 10:02:40'),
(69, 'Hisense TV', 'Great value with excellent picture quality.', 'hisense tv.jpeg', 58500.00, 65000.00, '10% off', 6500.00, 10, 1, 'http://localhost/portifolio/img/hisense tv.jpeg', '2025-03-30 10:02:40'),
(70, 'Philips TV', 'Stylish and high-performing smart TV.', 'phillips tv.jpeg', 72000.00, 80000.00, '10% off', 8000.00, 10, 1, 'http://localhost/portifolio/img/phillips tv.jpeg', '2025-03-30 10:02:40'),
(71, 'Sanyo TV', 'Affordable, quality TV with great features.', 'sanyo tv.jpeg', 40500.00, 45000.00, '10% off', 4500.00, 10, 1, 'http://localhost/portifolio/img/sanyo tv.jpeg', '2025-03-30 10:02:40'),
(72, 'QLED TV', 'Quantum dot technology for better brightness and color.', 'QLED TV.jpeg', 162000.00, 180000.00, '10% off', 18000.00, 10, 1, 'http://localhost/portifolio/img/QLED TV.jpeg', '2025-03-30 10:02:40'),
(73, 'LED TV', 'Energy-efficient and reliable performance.', 'LED TV.jpeg', 54000.00, 60000.00, '10% off', 6000.00, 10, 1, 'http://localhost/portifolio/img/LED TV.jpeg', '2025-03-30 10:02:40'),
(74, '8K TV', 'Future-proof your home with 8K resolution.', '8K TV.jpeg', 315000.00, 350000.00, '10% off', 35000.00, 10, 1, 'http://localhost/portifolio/img/8K TV.jpeg', '2025-03-30 10:02:40'),
(75, 'Smart Curved TV', 'Combine smart features with a curved screen for ultimate viewing.', 'SMART CURVED TV.jpeg', 198000.00, 220000.00, '10% off', 22000.00, 10, 1, 'http://localhost/portifolio/img/SMART CURVED TV.jpeg', '2025-03-30 10:02:40'),
(76, 'Smart TV', 'Enjoy streaming with high-definition clarity.', 'smart tv.jpeg', 72000.00, 80000.00, '10% off', 8000.00, 10, 1, 'http://localhost/portifolio/img/smart tv.jpeg', '2025-03-30 10:02:40'),
(77, '4K Ultra HD TV', 'Experience stunning picture quality and clarity.', '4K TV.jpeg', 108000.00, 120000.00, '10% off', 12000.00, 10, 1, 'http://localhost/portifolio/img/4K TV.jpeg', '2025-03-30 10:02:40'),
(78, 'Curved TV', 'Immersive viewing experience with curved screen.', 'CURVED TV.jpeg', 135000.00, 150000.00, '10% off', 15000.00, 10, 1, 'http://localhost/portifolio/img/CURVED TV.jpeg', '2025-03-30 10:02:40'),
(79, 'LED 4K TV', 'High definition with crystal-clear 4K resolution.', 'LED 4K TV.jpeg', 117000.00, 130000.00, '10% off', 13000.00, 10, 1, 'http://localhost/portifolio/img/LED 4K TV.jpeg', '2025-03-30 10:02:40'),
(80, 'Gas Cooker', 'Efficient and durable cooking solution.', 'gas cooker.jpeg', 16200.00, 18000.00, '10% off', 1800.00, 10, 1, 'http://localhost/portifolio/img/gas cooker.jpeg', '2025-03-30 10:02:40'),
(81, 'Electric Oven', 'Bake, roast, and grill your favorite dishes with precision.', 'electric oven.jpeg', 36000.00, 40000.00, '10% off', 4000.00, 10, 1, 'http://localhost/portifolio/img/electric oven.jpeg', '2025-03-30 10:02:40'),
(82, 'Convection Oven', 'Perfect for baking and roasting.', 'convetion oven.jpeg', 27000.00, 30000.00, '10% off', 3000.00, 10, 1, 'http://localhost/portifolio/img/convetion oven.jpeg', '2025-03-30 10:02:40'),
(83, 'Air Fryer Oven', 'Healthy frying and baking in one appliance.', 'air fryer oven.jpeg', 22500.00, 25000.00, '10% off', 2500.00, 10, 1, 'http://localhost/portifolio/img/air fryer oven.jpeg', '2025-03-30 10:02:40'),
(84, 'Pressure Cooker', 'Fast and energy-saving cooking.', 'pressure cooker.jpeg', 9000.00, 10000.00, '10% off', 1000.00, 10, 1, 'http://localhost/portifolio/img/pressure cooker.jpeg', '2025-03-30 10:02:41'),
(85, 'Food Processor', 'Chop, slice, and dice with ease.', 'food processor.jpeg', 16650.00, 18500.00, '10% off', 1850.00, 10, 1, 'http://localhost/portifolio/img/food processor.jpeg', '2025-03-30 10:02:41'),
(86, 'Deep Fryer', 'Fry your favorite foods perfectly.', 'deep fryer.jpeg', 10800.00, 12000.00, '10% off', 1200.00, 10, 1, 'http://localhost/portifolio/img/deep fryer.jpeg', '2025-03-30 10:02:41'),
(87, 'Slow Cooker', 'Cook meals slowly for maximum flavor.', 'slow cooker.jpeg', 13500.00, 15000.00, '10% off', 1500.00, 10, 1, 'http://localhost/portifolio/img/slow cooker.jpeg', '2025-03-30 10:02:41'),
(88, 'Stand Mixer', 'Mix, knead, and whip with ease.', 'stand mixer.jpeg', 22500.00, 25000.00, '10% off', 2500.00, 10, 1, 'http://localhost/portifolio/img/stand mixer.jpeg', '2025-03-30 10:02:41'),
(89, 'Washing Machine', 'Efficient cleaning for your clothes.', 'washing machine.webp', 22500.00, 25000.00, '10% off', 2500.00, 10, 1, 'http://localhost/portifolio/img/washing machine.webp', '2025-03-30 10:02:41'),
(90, 'Refrigerator', 'Keep your food fresh for longer.', 'refrigerator.webp', 45000.00, 50000.00, '10% off', 5000.00, 10, 1, 'http://localhost/portifolio/img/refrigerator.webp', '2025-03-30 10:02:41'),
(91, 'Air Conditioner', 'Stay cool and comfortable throughout the year.', 'air conditioner.jpeg', 40500.00, 45000.00, '10% off', 4500.00, 10, 1, 'http://localhost/portifolio/img/air conditioner.jpeg', '2025-03-30 10:02:41'),
(92, 'Vacuum Cleaner', 'Keep your floors spotless.', 'vacuum cleaner.webp', 16200.00, 18000.00, '10% off', 1800.00, 10, 1, 'http://localhost/portifolio/img/vacuum cleaner.webp', '2025-03-30 10:02:41'),
(93, 'Dishwasher', 'Clean your dishes effortlessly.', 'dishwasher.webp', 36000.00, 40000.00, '10% off', 4000.00, 10, 1, 'http://localhost/portifolio/img/dishwasher.webp', '2025-03-30 10:02:41'),
(94, 'Water Dispenser', 'Cool and hot water at your fingertips.', 'water dispenser.png', 13500.00, 15000.00, '10% off', 1500.00, 10, 1, 'http://localhost/portifolio/img/water dispenser.png', '2025-03-30 10:02:41'),
(95, 'Home Theater System', 'Immerse yourself in cinematic audio.', 'home theatre system.jpeg', 45000.00, 50000.00, '10% off', 5000.00, 10, 1, 'http://localhost/portifolio/img/home theatre system.jpeg', '2025-03-30 10:02:41'),
(96, 'Ice Maker', 'Make ice quickly for your drinks.', 'ice maker.jpeg', 10800.00, 12000.00, '10% off', 1200.00, 10, 1, 'http://localhost/portifolio/img/ice maker.jpeg', '2025-03-30 10:02:41'),
(97, 'Electric Grill', 'Grill your favorite dishes indoors.', 'electric grill.jpeg', 9000.00, 10000.00, '10% off', 1000.00, 10, 1, 'http://localhost/portifolio/img/electric grill.jpeg', '2025-03-30 10:02:41'),
(98, 'Water Heater', 'Instant hot water at your service.', 'water heater.jpeg', 18000.00, 20000.00, '10% off', 2000.00, 10, 1, 'http://localhost/portifolio/img/water heater.jpeg', '2025-03-30 10:02:41'),
(99, 'Blender', 'Perfect for smoothies and more.', 'blender.png', 4050.00, 4500.00, '10% off', 450.00, 10, 1, 'http://localhost/portifolio/img/blender.png', '2025-03-30 10:02:41'),
(100, 'Electric Kettle', 'Fast boiling for your beverages.', 'electric kettle.webp', 2700.00, 3000.00, '10% off', 300.00, 10, 1, 'http://localhost/portifolio/img/electric kettle.webp', '2025-03-30 10:02:41'),
(101, 'Toaster', 'Crispy toast every morning.', 'toaster.jpeg', 2520.00, 2800.00, '10% off', 280.00, 10, 1, 'http://localhost/portifolio/img/toaster.jpeg', '2025-03-30 10:02:41'),
(102, 'Iron', 'Keep your clothes wrinkle-free.', 'iron box.jpeg', 2700.00, 3000.00, '10% off', 300.00, 10, 1, 'http://localhost/portifolio/img/iron box.jpeg', '2025-03-30 10:02:41'),
(103, 'Hair Dryer', 'Style your hair with ease.', 'hair dryer.jpeg', 1620.00, 1800.00, '10% off', 180.00, 10, 1, 'http://localhost/portifolio/img/hair dryer.jpeg', '2025-03-30 10:02:41'),
(104, 'Coffee Maker', 'Brew the perfect cup every time.', 'coffe maker.webp', 5400.00, 6000.00, '10% off', 600.00, 10, 1, 'http://localhost/portifolio/img/coffe maker.webp', '2025-03-30 10:02:41'),
(105, 'Rice Cooker', 'Easy cooking with perfect results.', 'rice cooker.jpeg', 3780.00, 4200.00, '10% off', 420.00, 10, 1, 'http://localhost/portifolio/img/rice cooker.jpeg', '2025-03-30 10:02:41'),
(106, 'Sandwich Maker', 'Quick and easy toasted sandwiches.', 'sandwich maker.jpeg', 2880.00, 3200.00, '10% off', 320.00, 10, 1, 'http://localhost/portifolio/img/sandwich maker.jpeg', '2025-03-30 10:02:42'),
(107, 'Juicer', 'Fresh juice, hassle-free.', 'juicer.jpeg', 6300.00, 7000.00, '10% off', 700.00, 10, 1, 'http://localhost/portifolio/img/juicer.jpeg', '2025-03-30 10:02:42'),
(108, 'Electric Fan', 'Stay cool with powerful airflow.', 'electric fan.jpeg', 3600.00, 4000.00, '10% off', 400.00, 10, 1, 'http://localhost/portifolio/img/electric fan.jpeg', '2025-03-30 10:02:42'),
(109, 'Portable Heater', 'Compact and efficient heating.', 'portable heater.jpeg', 4950.00, 5500.00, '10% off', 550.00, 10, 1, 'http://localhost/portifolio/img/portable heater.jpeg', '2025-03-30 10:02:42'),
(110, 'Electric Griddle', 'Perfect for pancakes and grilling.', 'electric griddle.jpeg', 7200.00, 8000.00, '10% off', 800.00, 10, 1, 'http://localhost/portifolio/img/electric griddle.jpeg', '2025-03-30 10:02:42'),
(111, 'Handheld Vacuum', 'Compact cleaning on the go.', 'handheld vacuum.jpeg', 6120.00, 6800.00, '10% off', 680.00, 10, 1, 'http://localhost/portifolio/img/handheld vacuum.jpeg', '2025-03-30 10:02:42'),
(112, 'Electric Knife', 'Effortless slicing of meats and bread.', 'electric knife.jpeg', 3330.00, 3700.00, '10% off', 370.00, 10, 1, 'http://localhost/portifolio/img/electric knife.jpeg', '2025-03-30 10:02:42'),
(113, 'Popcorn Maker', 'Movie night, made easy.', 'popcorn maker.jpeg', 2880.00, 3200.00, '10% off', 320.00, 10, 1, 'http://localhost/portifolio/img/popcorn maker.jpeg', '2025-03-30 10:02:42'),
(114, 'Yogurt Maker', 'Homemade healthy yogurt.', 'yoghurt maker.jpeg', 4680.00, 5200.00, '10% off', 520.00, 10, 1, 'http://localhost/portifolio/img/yoghurt maker.jpeg', '2025-03-30 10:02:42'),
(115, 'Mini Fridge', 'Compact cooling solution.', 'mini fridge.jpeg', 13500.00, 15000.00, '10% off', 1500.00, 10, 1, 'http://localhost/portifolio/img/mini fridge.jpeg', '2025-03-30 10:02:42'),
(116, 'Hand Mixer', 'Effortless mixing and whisking.', 'handmixer.jpeg', 2250.00, 2500.00, '10% off', 250.00, 10, 1, 'http://localhost/portifolio/img/handmixer.jpeg', '2025-03-30 10:02:42'),
(117, 'HD CCTV Cameras', 'High-definition surveillance for your home or office.', 'hd cctv cameras.jpeg', 13500.00, 15000.00, '10% off', 1500.00, 10, 1, 'http://localhost/portifolio/img/hd cctv cameras.jpeg', '2025-03-30 10:02:42'),
(118, 'Wireless CCTV Cameras', 'Convenient and easy to install wireless CCTV system.', 'wireless cctv cameras.jpeg', 18000.00, 20000.00, '10% off', 2000.00, 10, 1, 'http://localhost/portifolio/img/wireless cctv cameras.jpeg', '2025-03-30 10:02:42'),
(119, 'Outdoor CCTV Cameras', 'Durable and weatherproof for outdoor surveillance.', 'outdoor cctv cameras.jpeg', 16200.00, 18000.00, '10% off', 1800.00, 10, 1, 'http://localhost/portifolio/img/outdoor cctv cameras.jpeg', '2025-03-30 10:02:42'),
(120, 'Pan-Tilt CCTV Cameras', '360-degree rotation for full coverage.', 'pan tilt cctv cameras.jpeg', 22500.00, 25000.00, '10% off', 2500.00, 10, 1, 'http://localhost/portifolio/img/pan tilt cctv cameras.jpeg', '2025-03-30 10:02:42'),
(121, '4K CCTV Cameras', 'Ultra-high-definition camera with 4K resolution.', '4k cctv cameras.jpeg', 36000.00, 40000.00, '10% off', 4000.00, 10, 1, 'http://localhost/portifolio/img/4k cctv cameras.jpeg', '2025-03-30 10:02:42'),
(122, 'Night Vision CCTV Cameras', 'Perfect for low-light environments with night vision capability.', 'night vision cctv cameras.jpeg', 19800.00, 22000.00, '10% off', 2200.00, 10, 1, 'http://localhost/portifolio/img/night vision cctv cameras.jpeg', '2025-03-30 10:02:42'),
(123, 'Dome CCTV Cameras', 'Discreet surveillance with a dome-shaped camera.', 'dome cctv cameras.jpeg', 14400.00, 16000.00, '10% off', 1600.00, 10, 1, 'http://localhost/portifolio/img/dome cctv cameras.jpeg', '2025-03-30 10:02:42'),
(124, 'Bullet CCTV Cameras', 'Durable and visible camera for outdoor surveillance.', 'bullet cctv cameras.jpeg', 16650.00, 18500.00, '10% off', 1850.00, 10, 1, 'http://localhost/portifolio/img/bullet cctv cameras.jpeg', '2025-03-30 10:02:42'),
(125, '360-Degree CCTV Cameras', 'Provides full 360-degree coverage with advanced technology.', '360 cctv cameras.jpeg', 27000.00, 30000.00, '10% off', 3000.00, 10, 1, 'http://localhost/portifolio/img/360 cctv cameras.jpeg', '2025-03-30 10:02:42'),
(126, 'Wireless IP CCTV Cameras', 'Stream footage directly to your mobile device with wireless IP.', 'wireless ip  cctv cameras.jpeg', 20250.00, 22500.00, '10% off', 2250.00, 10, 1, 'http://localhost/portifolio/img/wireless ip  cctv cameras.jpeg', '2025-03-30 10:02:42'),
(127, 'Fingerprint Smart Lock', 'Advanced fingerprint technology for secure entry.', 'fingerprint smartlock.jpeg', 10800.00, 12000.00, '10% off', 1200.00, 10, 1, 'http://localhost/portifolio/img/fingerprint smartlock.jpeg', '2025-03-30 10:02:42'),
(128, 'Bluetooth Smart Lock', 'Unlock with your smartphone using Bluetooth.', 'bluetooth smartlock.jpeg', 13500.00, 15000.00, '10% off', 1500.00, 10, 1, 'http://localhost/portifolio/img/bluetooth smartlock.jpeg', '2025-03-30 10:02:42'),
(129, 'Keypad Smart Lock', 'Easy-to-use keypad lock with customizable codes.', 'keypad smartlock.jpeg', 9000.00, 10000.00, '10% off', 1000.00, 10, 1, 'http://localhost/portifolio/img/keypad smartlock.jpeg', '2025-03-30 10:02:43'),
(130, 'Wi-Fi Smart Lock', 'Control your lock remotely with Wi-Fi connectivity.', 'wifi smartlock.jpeg', 16200.00, 18000.00, '10% off', 1800.00, 10, 1, 'http://localhost/portifolio/img/wifi smartlock.jpeg', '2025-03-30 10:02:43'),
(131, 'Smart Deadbolt Lock', 'Secure your door with a smart deadbolt system.', 'deadbolt smartlock.jpeg', 13050.00, 14500.00, '10% off', 1450.00, 10, 1, 'http://localhost/portifolio/img/deadbolt smartlock.jpeg', '2025-03-30 10:02:43'),
(132, 'Smart Lock with Camera', 'Unlock your door while monitoring visitors with an integrated camera.', 'camera smartlock.jpeg', 22500.00, 25000.00, '10% off', 2500.00, 10, 1, 'http://localhost/portifolio/img/camera smartlock.jpeg', '2025-03-30 10:02:43'),
(133, 'Biometric Smart Lock', 'Unlock using your fingerprint for maximum security.', 'biometric smart lock.jpeg', 18000.00, 20000.00, '10% off', 2000.00, 10, 1, 'http://localhost/portifolio/img/biometric smart lock.jpeg', '2025-03-30 10:02:43'),
(134, 'RFID Smart Lock', 'Access your door with RFID cards or keychains.', 'RFID smart lock.jpeg', 15300.00, 17000.00, '10% off', 1700.00, 10, 1, 'http://localhost/portifolio/img/RFID smart lock.jpeg', '2025-03-30 10:02:44'),
(135, 'Voice Control Smart Lock', 'Unlock your door with voice commands through Alexa or Google Assistant.', 'voice smartlock.jpeg', 19800.00, 22000.00, '10% off', 2200.00, 10, 1, 'http://localhost/portifolio/img/voice smartlock.jpeg', '2025-03-30 10:02:44'),
(136, 'Touchscreen Smart Lock', 'Stylish touchscreen lock with customizable codes.', 'touchscreen smartlock.jpeg', 17100.00, 19000.00, '10% off', 1900.00, 10, 1, 'http://localhost/portifolio/img/touchscreen smartlock.jpeg', '2025-03-30 10:02:44'),
(137, 'Wireless Doorbell Camera', 'Monitor visitors remotely with a wireless doorbell camera.', 'wireless doorbell camera.jpeg', 11250.00, 12500.00, '10% off', 1250.00, 10, 1, 'http://localhost/portifolio/img/wireless doorbell camera.jpeg', '2025-03-30 10:02:44'),
(138, 'Smart Smoke Detector', 'Detect smoke and fire with real-time notifications on your phone.', 'smart smoke detector.jpeg', 7200.00, 8000.00, '10% off', 800.00, 10, 1, 'http://localhost/portifolio/img/smart smoke detector.jpeg', '2025-03-30 10:02:44'),
(139, 'Smart Motion Sensor', 'Detect motion and receive alerts instantly.', 'motion sensor.jpeg', 6750.00, 7500.00, '10% off', 750.00, 10, 1, 'http://localhost/portifolio/img/motion sensor.jpeg', '2025-03-30 10:02:44'),
(140, 'Smart Security Alarm System', 'Comprehensive alarm system with app integration.', 'alarm system.jpeg', 19800.00, 22000.00, '10% off', 2200.00, 10, 1, 'http://localhost/portifolio/img/alarm system.jpeg', '2025-03-30 10:02:44'),
(141, 'Smart Glass Break Sensor', 'Detect glass breakage and get immediate alerts.', 'glass break sensor.jpeg', 4500.00, 5000.00, '10% off', 500.00, 10, 1, 'http://localhost/portifolio/img/glass break sensor.jpeg', '2025-03-30 10:02:44'),
(142, 'Smart Home Control Panel', 'Control all your security devices from a central panel.', 'home cotrol panel.jpeg', 16650.00, 18500.00, '10% off', 1850.00, 10, 1, 'http://localhost/portifolio/img/home cotrol panel.jpeg', '2025-03-30 10:02:44'),
(143, 'Smart Wi-Fi Doorbell', 'Video doorbell with two-way audio and remote unlocking.', 'wifi doorbell.jpeg', 10800.00, 12000.00, '10% off', 1200.00, 10, 1, 'http://localhost/portifolio/img/wifi doorbell.jpeg', '2025-03-30 10:02:44'),
(144, 'Smart Garage Door Opener', 'Control your garage door remotely from your phone.', 'garage door opener.jpeg', 9000.00, 10000.00, '10% off', 1000.00, 10, 1, 'http://localhost/portifolio/img/garage door opener.jpeg', '2025-03-30 10:02:44'),
(145, 'Smart Light Bulbs', 'Control your lights remotely with smart bulbs.', 'smart light bulbs.jpeg', 3150.00, 3500.00, '10% off', 350.00, 10, 1, 'http://localhost/portifolio/img/smart light bulbs.jpeg', '2025-03-30 10:02:44'),
(146, 'Smart Window Alarm', 'Protect your windows with a smart alarm system.', 'window alarm system.jpeg', 5850.00, 6500.00, '10% off', 650.00, 10, 1, 'http://localhost/portifolio/img/window alarm system.jpeg', '2025-03-30 10:02:44'),
(147, 'Smart Doorbell Camera with Video', 'See and speak to visitors with video doorbell functionality.', 'doorbell with camera video.jpeg', 12600.00, 14000.00, '10% off', 1400.00, 10, 1, 'http://localhost/portifolio/img/doorbell with camera video.jpeg', '2025-03-30 10:02:44'),
(148, 'Smart Smoke & CO Detector', 'Detect smoke and carbon monoxide with smart notifications.', 'smoke and co2 detector.jpeg', 9450.00, 10500.00, '10% off', 1050.00, 10, 1, 'http://localhost/portifolio/img/smoke and co2 detector.jpeg', '2025-03-30 10:02:44'),
(149, 'Smart Security Sensor System', 'Comprehensive security sensors for windows, doors, and more.', 'security sensor sysem.jpeg', 20250.00, 22500.00, '10% off', 2250.00, 10, 1, 'http://localhost/portifolio/img/security sensor sysem.jpeg', '2025-03-30 10:02:44'),
(150, 'LED Bulbs', 'Energy-efficient LED bulbs for bright lighting.', 'LED Bulbs.jpeg', 450.00, 500.00, '10% off', 50.00, 10, 1, 'http://localhost/portifolio/img/LED Bulbs.jpeg', '2025-03-30 10:02:44'),
(151, 'Smart Bulbs', 'Wi-Fi-enabled smart bulbs for remote control.', 'Smart Bulbs.jpeg', 1080.00, 1200.00, '10% off', 120.00, 10, 1, 'http://localhost/portifolio/img/Smart Bulbs.jpeg', '2025-03-30 10:02:45'),
(152, 'Energy Saving Bulb', 'Low power consumption, high brightness.', 'Energy Saving Bulb.jpeg', 405.00, 450.00, '10% off', 45.00, 10, 1, 'http://localhost/portifolio/img/Energy Saving Bulb.jpeg', '2025-03-30 10:02:45'),
(153, 'Fluorescent Bulb', 'Long-lasting fluorescent bulb for bright lighting.', 'Fluorescent Bulb.jpeg', 315.00, 350.00, '10% off', 35.00, 10, 1, 'http://localhost/portifolio/img/Fluorescent Bulb.jpeg', '2025-03-30 10:02:45'),
(154, 'Incandescent Bulb', 'Classic incandescent bulb for soft light.', 'Incandescent Bulb.jpeg', 180.00, 200.00, '10% off', 20.00, 10, 1, 'http://localhost/portifolio/img/Incandescent Bulb.jpeg', '2025-03-30 10:02:45'),
(155, 'Extension Cords', 'Flexible extension cords for extra reach.', 'Extension Cords.jpeg', 720.00, 800.00, '10% off', 80.00, 10, 1, 'http://localhost/portifolio/img/Extension Cords.jpeg', '2025-03-30 10:02:45'),
(156, 'Surge Protectors', 'Protect your devices from power surges.', 'Surge Protectors.jpeg', 1620.00, 1800.00, '10% off', 180.00, 10, 1, 'http://localhost/portifolio/img/Surge Protectors.jpeg', '2025-03-30 10:02:45'),
(157, 'Multi-Socket Extension', 'Multi-socket extension cord for multiple devices.', 'Multi-Socket Extension.jpeg', 900.00, 1000.00, '10% off', 100.00, 10, 1, 'http://localhost/portifolio/img/Multi-Socket Extension.jpeg', '2025-03-30 10:02:45'),
(158, 'Heavy Duty Extension', 'Heavy-duty extension for high-power devices.', 'Heavy Duty Extension.jpeg', 1980.00, 2200.00, '10% off', 220.00, 10, 1, 'http://localhost/portifolio/img/Heavy Duty Extension.jpeg', '2025-03-30 10:02:45'),
(159, 'Extension Reel', 'Portable extension reel for outdoor use.', 'Extension Reel.jpeg', 1350.00, 1500.00, '10% off', 150.00, 10, 1, 'http://localhost/portifolio/img/Extension Reel.jpeg', '2025-03-30 10:02:45'),
(160, 'Ceramic Bulb Holders', 'Durable ceramic holders for bulbs.', 'ceramic bulb holder.jpeg', 270.00, 300.00, '10% off', 30.00, 10, 1, 'http://localhost/portifolio/img/ceramic bulb holder.jpeg', '2025-03-30 10:02:45'),
(161, 'Plastic Bulb Holders', 'Lightweight and affordable plastic bulb holders.', 'Plastic Bulb Holders.jpeg', 135.00, 150.00, '10% off', 15.00, 10, 1, 'http://localhost/portifolio/img/Plastic Bulb Holders.jpeg', '2025-03-30 10:02:45'),
(162, 'Screw-in Bulb Holder', 'Easy-to-use screw-in bulb holder.', 'Screw-in Bulb Holde.jpeg', 225.00, 250.00, '10% off', 25.00, 10, 1, 'http://localhost/portifolio/img/Screw-in Bulb Holde.jpeg', '2025-03-30 10:02:45'),
(163, 'Bulb Holder Set', 'Complete set of bulb holders for various bulbs.', 'Bulb Holder Set.jpeg', 540.00, 600.00, '10% off', 60.00, 10, 1, 'http://localhost/portifolio/img/Bulb Holder Set.jpeg', '2025-03-30 10:02:45'),
(164, 'Ceiling Bulb Holder', 'Bulb holder for ceiling lights.', 'Ceiling Bulb Holde.jpeg', 315.00, 350.00, '10% off', 35.00, 10, 1, 'http://localhost/portifolio/img/Ceiling Bulb Holde.jpeg', '2025-03-30 10:02:45'),
(165, 'Power Cables', 'High-quality power cables for electronics.', 'Power Cables.jpeg', 540.00, 600.00, '10% off', 60.00, 10, 1, 'http://localhost/portifolio/img/Power Cables.jpeg', '2025-03-30 10:02:45'),
(166, 'USB Cables', 'Fast-charging USB cables for mobile devices.', 'USB Cables.jpeg', 270.00, 300.00, '10% off', 30.00, 10, 1, 'http://localhost/portifolio/img/USB Cables.jpeg', '2025-03-30 10:02:45'),
(167, 'HDMI Cables', 'High-speed HDMI cables for video and audio.', 'hdmi cables.jpeg', 720.00, 800.00, '10% off', 80.00, 10, 1, 'http://localhost/portifolio/img/hdmi cables.jpeg', '2025-03-30 10:02:45'),
(168, 'Ethernet Cables', 'Fast and reliable Ethernet cables for networking.', 'ethernet cables.jpeg', 450.00, 500.00, '10% off', 50.00, 10, 1, 'http://localhost/portifolio/img/ethernet cables.jpeg', '2025-03-30 10:02:45'),
(169, 'Audio Cables', 'High-quality audio cables for sound systems.', 'Audio Cables.jpeg', 360.00, 400.00, '10% off', 40.00, 10, 1, 'http://localhost/portifolio/img/Audio Cables.jpeg', '2025-03-30 10:02:45'),
(170, 'TP-Link AC1750', 'High-speed dual-band router with improved range.', 'tp link router.jpeg', 7200.00, 8000.00, '10% off', 800.00, 10, 1, 'http://localhost/portifolio/img/tp link router.jpeg', '2025-03-30 10:02:45'),
(171, 'Netgear Nighthawk', 'Fast Wi-Fi router with strong security features.', 'netgear router.jpeg', 10800.00, 12000.00, '10% off', 1200.00, 10, 1, 'http://localhost/portifolio/img/netgear router.jpeg', '2025-03-30 10:02:45'),
(172, 'Asus RT-AC68U', 'Smart router with advanced features for gaming.', 'asus router.jpeg', 9000.00, 10000.00, '10% off', 1000.00, 10, 1, 'http://localhost/portifolio/img/asus router.jpeg', '2025-03-30 10:02:45'),
(173, 'Linksys EA8300', 'Tri-band router with MU-MIMO technology.', 'linksys router.jpeg', 8550.00, 9500.00, '10% off', 950.00, 10, 1, 'http://localhost/portifolio/img/linksys router.jpeg', '2025-03-30 10:02:46'),
(174, 'Google Wi-Fi', 'Mesh system for seamless Wi-Fi coverage across your home.', 'google wifi router.jpeg', 16200.00, 18000.00, '10% off', 1800.00, 10, 1, 'http://localhost/portifolio/img/google wifi router.jpeg', '2025-03-30 10:02:46'),
(175, 'D-Link DGS-108', '8-port gigabit switch for home and office use.', 'D-Link DGS-108.jpeg', 5400.00, 6000.00, '10% off', 600.00, 10, 1, 'http://localhost/portifolio/img/D-Link DGS-108.jpeg', '2025-03-30 10:02:46'),
(176, 'Netgear GS308', '8-port switch with high-speed performance.', 'netgear switch.jpeg', 6750.00, 7500.00, '10% off', 750.00, 10, 1, 'http://localhost/portifolio/img/netgear switch.jpeg', '2025-03-30 10:02:46'),
(177, 'Cisco SG350-10', 'Advanced managed switch with VLAN support.', 'cisco switch.jpeg', 13500.00, 15000.00, '10% off', 1500.00, 10, 1, 'http://localhost/portifolio/img/cisco switch.jpeg', '2025-03-30 10:02:46'),
(178, 'TP-Link TL-SG1016', '16-port gigabit switch with energy-saving features.', 'tp link switch.jpeg', 8100.00, 9000.00, '10% off', 900.00, 10, 1, 'http://localhost/portifolio/img/tp link switch.jpeg', '2025-03-30 10:02:46'),
(179, 'Zyxel GS1900-8', 'Smart managed switch with 8 gigabit ports.', 'Zyxel GS1900-8 switch.jpeg', 9450.00, 10500.00, '10% off', 1050.00, 10, 1, 'http://localhost/portifolio/img/Zyxel GS1900-8 switch.jpeg', '2025-03-30 10:02:46'),
(180, 'Wi-Fi Adapter', 'USB Wi-Fi adapter for laptops and desktops.', 'wifi adapter.jpeg', 1800.00, 2000.00, '10% off', 200.00, 10, 1, 'http://localhost/portifolio/img/wifi adapter.jpeg', '2025-03-30 10:02:46'),
(181, 'Cat 6 Cable', 'Ultra-fast Cat 6 cable for high-bandwidth needs.', 'cat 6 cables.jpeg', 1350.00, 1500.00, '10% off', 150.00, 10, 1, 'http://localhost/portifolio/img/cat 6 cables.jpeg', '2025-03-30 10:02:46'),
(182, 'Flat Ethernet Cable', 'Flexible flat Ethernet cable for easy management.', 'flat ethernet cable.jpeg', 720.00, 800.00, '10% off', 80.00, 10, 1, 'http://localhost/portifolio/img/flat ethernet cable.jpeg', '2025-03-30 10:02:46'),
(183, 'Ethernet Cable Pack', 'Pack of 3 high-quality Ethernet cables.', 'ethernet cable pack.jpeg', 1800.00, 2000.00, '10% off', 200.00, 10, 1, 'http://localhost/portifolio/img/ethernet cable pack.jpeg', '2025-03-30 10:02:46'),
(184, 'USB to Ethernet Adapter', 'Convert USB ports to Ethernet for a stable connection.', 'adapter usb cable.jpeg', 1080.00, 1200.00, '10% off', 120.00, 10, 1, 'http://localhost/portifolio/img/adapter usb cable.jpeg', '2025-03-30 10:02:46'),
(185, 'USB Hub', 'Multi-port USB hub for extending your device connections.', 'usb hub.jpeg', 1350.00, 1500.00, '10% off', 150.00, 10, 1, 'http://localhost/portifolio/img/usb hub.jpeg', '2025-03-30 10:02:46'),
(186, 'Powerline Adapter', 'Extend your network using electrical wiring in your home.', 'power adapter.jpeg', 2700.00, 3000.00, '10% off', 300.00, 10, 1, 'http://localhost/portifolio/img/power adapter.jpeg', '2025-03-30 10:02:46'),
(187, 'Smart Wi-Fi Plug', 'Control your devices remotely with a smart plug.', 'wifi plug.jpeg', 1350.00, 1500.00, '10% off', 150.00, 10, 1, 'http://localhost/portifolio/img/wifi plug.jpeg', '2025-03-30 10:02:46'),
(188, 'HP Core i5', 'High-performance laptop for all your needs.', 'HP Core i5.jpeg', 45000.00, 50000.00, '10% off', 5000.00, 10, 1, 'http://localhost/portifolio/img/HP Core i5.jpeg', '2025-03-30 10:02:46'),
(189, 'Dell Inspiron 15', 'Ultra-thin laptop with excellent battery life.', 'Dell Inspiron 15.jpeg', 49500.00, 55000.00, '10% off', 5500.00, 10, 1, 'http://localhost/portifolio/img/Dell Inspiron 15.jpeg', '2025-03-30 10:02:47'),
(190, 'Lenovo ThinkPad X1', 'Fast and efficient laptop for work and play.', 'Lenovo ThinkPad X1.jpeg', 54000.00, 60000.00, '10% off', 6000.00, 10, 1, 'http://localhost/portifolio/img/Lenovo ThinkPad X1.jpeg', '2025-03-30 10:02:47'),
(191, 'Acer Predator Helios 300', 'Gaming laptop with high-end graphics.', 'Acer Predator Helios 300.jpeg', 72000.00, 80000.00, '10% off', 8000.00, 10, 1, 'http://localhost/portifolio/img/Acer Predator Helios 300.jpeg', '2025-03-30 10:02:47'),
(192, 'HP Pavilion x360', 'Portable laptop with exceptional processing power.', 'HP Pavilion x360.jpeg', 40500.00, 45000.00, '10% off', 4500.00, 10, 1, 'http://localhost/portifolio/img/HP Pavilion x360.jpeg', '2025-03-30 10:02:47'),
(193, 'Asus ZenBook 14', 'Lightweight and fast laptop for professionals.', 'Asus ZenBook 14.jpeg', 46800.00, 52000.00, '10% off', 5200.00, 10, 1, 'http://localhost/portifolio/img/Asus ZenBook 14.jpeg', '2025-03-30 10:02:47'),
(194, 'Microsoft Surface Laptop 4', 'Touchscreen laptop with great performance.', 'Microsoft Surface Laptop 4.jpeg', 52200.00, 58000.00, '10% off', 5800.00, 10, 1, 'http://localhost/portifolio/img/Microsoft Surface Laptop 4.jpeg', '2025-03-30 10:02:47'),
(195, 'Razer Blade Stealth 13', 'Powerful laptop with a high-definition display.', 'Razer Blade Stealth 13.jpeg', 67500.00, 75000.00, '10% off', 7500.00, 10, 1, 'http://localhost/portifolio/img/Razer Blade Stealth 13.jpeg', '2025-03-30 10:02:47'),
(196, 'HP Chromebook 14', 'Affordable laptop with all the basic features.', 'HP Chromebook 14.jpeg', 27000.00, 30000.00, '10% off', 3000.00, 10, 1, 'http://localhost/portifolio/img/HP Chromebook 14.jpeg', '2025-03-30 10:02:47'),
(197, 'Dell XPS 13', 'Perfect for students with fast processing.', 'Dell XPS 13.jpeg', 31500.00, 35000.00, '10% off', 3500.00, 10, 1, 'http://localhost/portifolio/img/Dell XPS 13.jpeg', '2025-03-30 10:02:47'),
(198, 'Alienware m15', 'Super-fast laptop with excellent graphics.', 'Alienware m15.jpeg', 76500.00, 85000.00, '10% off', 8500.00, 10, 1, 'http://localhost/portifolio/img/Alienware m15.jpeg', '2025-03-30 10:02:47'),
(199, 'Lenovo Legion 5', 'Reliable laptop with a high-resolution screen.', 'Lenovo Legion 5.jpeg', 63000.00, 70000.00, '10% off', 7000.00, 10, 1, 'http://localhost/portifolio/img/Lenovo Legion 5.jpeg', '2025-03-30 10:02:47'),
(200, 'HP Envy x360', 'Fast laptop with excellent processing and speed.', 'HP Envy x360.jpeg', 58500.00, 65000.00, '10% off', 6500.00, 10, 1, 'http://localhost/portifolio/img/HP Envy x360.jpeg', '2025-03-30 10:02:47'),
(201, 'MSI GE75 Raider', 'Gaming laptop with top-tier specs.', 'MSI GE75 Raider.jpeg', 85500.00, 95000.00, '10% off', 9500.00, 10, 1, 'http://localhost/portifolio/img/MSI GE75 Raider.jpeg', '2025-03-30 10:02:47'),
(202, 'Lenovo IdeaPad 3', 'Affordable and efficient laptop for everyday tasks.', 'Lenovo IdeaPad 3.jpeg', 22500.00, 25000.00, '10% off', 2500.00, 10, 1, 'http://localhost/portifolio/img/Lenovo IdeaPad 3.jpeg', '2025-03-30 10:02:47'),
(203, 'HP Pavilion Desktop', 'High-performance desktop for gaming and work.', 'HP Pavilion Desktop.jpeg', 54000.00, 60000.00, '10% off', 6000.00, 10, 1, 'http://localhost/portifolio/img/HP Pavilion Desktop.jpeg', '2025-03-30 10:02:47'),
(204, 'Dell Inspiron 3880', 'Mid-range desktop PC for home use.', 'Dell Inspiron 3880 pc desktop.jpeg', 40500.00, 45000.00, '10% off', 4500.00, 10, 1, 'http://localhost/portifolio/img/Dell Inspiron 3880 pc desktop.jpeg', '2025-03-30 10:02:47'),
(205, 'CyberPowerPC Gamer Xtreme', 'Gaming PC with high-end specifications.', 'CyberPowerPC Gamer Xtreme pc.jpeg', 72000.00, 80000.00, '10% off', 8000.00, 10, 1, 'http://localhost/portifolio/img/CyberPowerPC Gamer Xtreme pc.jpeg', '2025-03-30 10:02:47'),
(206, 'Apple Mac Mini', 'Compact and efficient desktop PC.', 'Apple Mac Mini desktop.jpeg', 36000.00, 40000.00, '10% off', 4000.00, 10, 1, 'http://localhost/portifolio/img/Apple Mac Mini desktop.jpeg', '2025-03-30 10:02:47'),
(207, 'Lenovo ThinkCentre M720', 'Affordable desktop PC for basic tasks.', 'Lenovo ThinkCentre M720 Desktop.jpeg', 27000.00, 30000.00, '10% off', 3000.00, 10, 1, 'http://localhost/portifolio/img/Lenovo ThinkCentre M720 Desktop.jpeg', '2025-03-30 10:02:47'),
(208, 'HP EliteDesktop', 'Efficient desktop for work with large storage.', 'HP EliteDesktop.jpeg', 45000.00, 50000.00, '10% off', 5000.00, 10, 1, 'http://localhost/portifolio/img/HP EliteDesktop.jpeg', '2025-03-30 10:02:47'),
(209, 'Acer Aspire TC', 'Affordable PC for everyday tasks.', 'Acer Aspire TC pc.jpeg', 25200.00, 28000.00, '10% off', 2800.00, 10, 1, 'http://localhost/portifolio/img/Acer Aspire TC pc.jpeg', '2025-03-30 10:02:47'),
(210, 'MSI Trident 3', 'Compact gaming PC with top performance.', 'MSI Trident 3 pc.jpeg', 67500.00, 75000.00, '10% off', 7500.00, 10, 1, 'http://localhost/portifolio/img/MSI Trident 3 pc.jpeg', '2025-03-30 10:02:48'),
(211, 'Asus ROG Strix', 'High-performance gaming desktop for enthusiasts.', 'Asus ROG Strix pc.jpeg', 76500.00, 85000.00, '10% off', 8500.00, 10, 1, 'http://localhost/portifolio/img/Asus ROG Strix pc.jpeg', '2025-03-30 10:02:48'),
(212, 'Gateway Desktop PC', 'Budget-friendly desktop for general tasks.', 'gateway pc.jpeg', 22500.00, 25000.00, '10% off', 2500.00, 10, 1, 'http://localhost/portifolio/img/gateway pc.jpeg', '2025-03-30 10:02:48'),
(213, 'Logitech Wireless Mouse', 'Comfortable and responsive wireless mouse.', 'wireless mouse.jpeg', 1080.00, 1200.00, '10% off', 120.00, 10, 1, 'http://localhost/portifolio/img/wireless mouse.jpeg', '2025-03-30 10:02:48'),
(214, 'Mechanical Keyboards', 'Durable and responsive mechanical keyboard.', 'keyboard.jpeg', 3150.00, 3500.00, '10% off', 350.00, 10, 1, 'http://localhost/portifolio/img/keyboard.jpeg', '2025-03-30 10:02:48'),
(215, 'Wireless Earbuds', 'High-quality sound with wireless convenience.', 'wireless earbuds.jpeg', 2250.00, 2500.00, '10% off', 250.00, 10, 1, 'http://localhost/portifolio/img/wireless earbuds.jpeg', '2025-03-30 10:02:48'),
(216, 'External Hard Drive', 'Portable 1TB external hard drive for storage.', 'external hdd.jpeg', 3600.00, 4000.00, '10% off', 400.00, 10, 1, 'http://localhost/portifolio/img/external hdd.jpeg', '2025-03-30 10:02:48'),
(217, 'Gaming Headset', 'High-quality sound with noise cancellation.', 'gaming headset.jpeg', 2700.00, 3000.00, '10% off', 300.00, 10, 1, 'http://localhost/portifolio/img/gaming headset.jpeg', '2025-03-30 10:02:48'),
(218, 'Mouse Pad', 'Non-slip, large mouse pad for gaming.', 'mouse pad.jpeg', 450.00, 500.00, '10% off', 50.00, 10, 1, 'http://localhost/portifolio/img/mouse pad.jpeg', '2025-03-30 10:02:48'),
(219, 'Webcam', 'HD webcam for clear video calls and streaming.', 'webcam.jpeg', 1800.00, 2000.00, '10% off', 200.00, 10, 1, 'http://localhost/portifolio/img/webcam.jpeg', '2025-03-30 10:02:48'),
(220, 'SD Card', '64GB SD card for cameras and storage devices.', 'sd card.jpeg', 720.00, 800.00, '10% off', 80.00, 10, 1, 'http://localhost/portifolio/img/sd card.jpeg', '2025-03-30 10:02:48'),
(221, 'USB Flash Drive', 'Portable 32GB USB drive for quick file transfer.', 'flash drive.jpeg', 540.00, 600.00, '10% off', 60.00, 10, 1, 'http://localhost/portifolio/img/flash drive.jpeg', '2025-03-30 10:02:48'),
(222, 'USB-C Adapter', 'Adapter for USB-C devices and connections.', 'usb c adapter.jpeg', 900.00, 1000.00, '10% off', 100.00, 10, 1, 'http://localhost/portifolio/img/usb c adapter.jpeg', '2025-03-30 10:02:48');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('customer','admin') DEFAULT 'customer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `failed_attempts` int(11) DEFAULT 0,
  `lockout_until` datetime DEFAULT NULL,
  `remember_token` varchar(64) DEFAULT NULL,
  `otp_code` varchar(6) DEFAULT NULL,
  `otp_expires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `email`, `phone_number`, `password_hash`, `role`, `created_at`, `failed_attempts`, `lockout_until`, `remember_token`, `otp_code`, `otp_expires`) VALUES
(1, 'Derrick', 'Kanyoko', 'kanyokoderrick15@gmail.com', NULL, '$argon2id$v=19$m=65536,t=4,p=1$Z2hGblVxR2ZvSkUwR0ZrSA$2FAlf4XvZXfJ+sPqgxg764gcby/kunSBwTfvI12DYKQ', 'customer', '2025-03-30 13:11:01', 0, NULL, NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `legal_documents`
--
ALTER TABLE `legal_documents`
  ADD PRIMARY KEY (`document_id`);

--
-- Indexes for table `mpesa_payments`
--
ALTER TABLE `mpesa_payments`
  ADD PRIMARY KEY (`mpesa_payment_id`),
  ADD KEY `payment_id` (`payment_id`);

--
-- Indexes for table `offers`
--
ALTER TABLE `offers`
  ADD PRIMARY KEY (`offer_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD UNIQUE KEY `transaction_reference` (`transaction_reference`),
  ADD KEY `idx_order_id` (`order_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone_number` (`phone_number`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `legal_documents`
--
ALTER TABLE `legal_documents`
  MODIFY `document_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mpesa_payments`
--
ALTER TABLE `mpesa_payments`
  MODIFY `mpesa_payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `offers`
--
ALTER TABLE `offers`
  MODIFY `offer_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=223;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `mpesa_payments`
--
ALTER TABLE `mpesa_payments`
  ADD CONSTRAINT `mpesa_payments_ibfk_1` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`payment_id`);

--
-- Constraints for table `offers`
--
ALTER TABLE `offers`
  ADD CONSTRAINT `offers_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`);

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
