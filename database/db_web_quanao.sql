-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 17, 2025 at 03:24 AM
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
-- Database: `db_web_quanao`
--

-- --------------------------------------------------------

--
-- Table structure for table `bank_account`
--

CREATE TABLE `bank_account` (
  `account_id` int(11) NOT NULL,
  `bank_code` varchar(20) NOT NULL,
  `account_number` varchar(50) NOT NULL,
  `account_name` varchar(100) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_default` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bank_account`
--

INSERT INTO `bank_account` (`account_id`, `bank_code`, `account_number`, `account_name`, `is_active`, `is_default`) VALUES
(1, 'MBBank', '0968937705', 'SAGKUTO', 1, 1),
(2, 'Vietcombank', '1025574990', 'SAGKUTO', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`cart_id`, `user_id`, `created_at`) VALUES
(2, 2, '2025-05-14 00:53:33');

-- --------------------------------------------------------

--
-- Table structure for table `cart_details`
--

CREATE TABLE `cart_details` (
  `cart_detail_id` int(11) NOT NULL,
  `cart_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `variant_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cart_details`
--

INSERT INTO `cart_details` (`cart_detail_id`, `cart_id`, `product_id`, `variant_id`, `quantity`) VALUES
(33, 2, 2, 6, 13),
(34, 2, 2, 7, 4);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `name`, `is_deleted`) VALUES
(1, 'Áo thun', 0),
(2, 'Quần short', 0),
(3, 'Áo Sơ Mi', 0),
(4, 'Áo Polo', 0),
(5, 'Áo Khoác', 0),
(6, 'Quần lót', 0),
(7, 'Phụ kiện', 0);

-- --------------------------------------------------------

--
-- Table structure for table `colors`
--

CREATE TABLE `colors` (
  `color_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `hex_code` varchar(7) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `colors`
--

INSERT INTO `colors` (`color_id`, `name`, `hex_code`, `is_deleted`) VALUES
(1, 'Đen', '#282A2B', 0),
(2, 'Be', '#DBD1BC', 0),
(3, 'Nâu', '#90713B', 0),
(4, 'Xám nhạt', '#9FA9A9', 0),
(5, 'Hồng nhạt', '#D07771', 0),
(6, 'Xanh rêu', '#95987B', 0),
(7, 'Xanh biển đậm', '#4F5C7C', 0),
(8, 'Trắng', '#F5F1E6', 0),
(9, 'Đỏ', '#A5051D', 0),
(10, 'Olive', '#59564F', 0),
(11, 'Xanh biển nhạt', '#387EA0', 0),
(12, 'Navy', '#3C4252', 0),
(13, 'Rượu vang', '#391D2B', 0),
(14, 'Be đậm', '#B58F6C', 0);

-- --------------------------------------------------------

--
-- Table structure for table `importreceipt`
--

CREATE TABLE `importreceipt` (
  `ImportReceipt_id` int(11) NOT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total_price` decimal(12,2) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `status` tinyint(4) DEFAULT 1,
  `is_deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `importreceipt`
--

INSERT INTO `importreceipt` (`ImportReceipt_id`, `supplier_id`, `user_id`, `total_price`, `created_at`, `status`, `is_deleted`) VALUES
(1, 20, 3, 823000000.00, '2025-04-12 18:57:21', 0, 0),
(2, 12, 3, 615650000.00, '2025-04-13 19:01:31', 0, 0),
(3, 10, 3, 549150000.00, '2025-04-17 10:02:26', 0, 0),
(4, 2, 3, 322500000.00, '2025-04-18 12:34:56', 0, 0),
(5, 19, 3, 271200000.00, '2025-04-19 19:03:03', 0, 0),
(6, 8, 3, 689250000.00, '2025-04-20 21:17:15', 0, 0),
(7, 1, 1, 24000000.00, '2025-05-16 19:27:47', 0, 0),
(8, 1, 1, 8000000.00, '2025-05-16 19:37:27', 0, 0),
(9, 8, 1, 14400000.00, '2025-05-16 19:48:56', 0, 0),
(10, 9, 1, 39350000.00, '2025-05-16 20:01:39', 0, 0),
(11, 11, 1, 40950000.00, '2025-05-16 20:11:05', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `importreceipt_details`
--

CREATE TABLE `importreceipt_details` (
  `importreceipt_details_id` int(11) NOT NULL,
  `importreceipt_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `variant_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `status` tinyint(1) DEFAULT 0,
  `unit_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_price` decimal(14,2) NOT NULL DEFAULT 0.00,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Dumping data for table `importreceipt_details`
--

INSERT INTO `importreceipt_details` (`importreceipt_details_id`, `importreceipt_id`, `product_id`, `variant_id`, `quantity`, `created_at`, `status`, `unit_price`, `total_price`, `is_deleted`) VALUES
(1, 1, 1, 1, 50, '2025-04-16 19:28:40', 0, 540000.00, 27000000.00, 0),
(2, 1, 1, 2, 50, '2025-04-16 19:36:10', 0, 540000.00, 27000000.00, 0),
(3, 1, 1, 3, 50, '2025-04-16 19:37:03', 0, 540000.00, 27000000.00, 0),
(4, 1, 1, 4, 50, '2025-04-16 19:37:42', 0, 540000.00, 27000000.00, 0),
(5, 1, 1, 5, 50, '2025-04-16 19:37:42', 0, 540000.00, 27000000.00, 0),
(6, 1, 2, 6, 50, '2025-04-16 19:38:49', 0, 490000.00, 24500000.00, 0),
(7, 1, 2, 7, 50, '2025-04-16 19:39:45', 0, 490000.00, 24500000.00, 0),
(8, 1, 2, 8, 50, '2025-04-16 19:39:45', 0, 490000.00, 24500000.00, 0),
(9, 1, 3, 9, 50, '2025-04-16 19:40:37', 0, 540000.00, 27000000.00, 0),
(10, 1, 3, 10, 50, '2025-04-16 19:41:13', 0, 540000.00, 27000000.00, 0),
(11, 1, 3, 11, 50, '2025-04-16 19:41:13', 0, 540000.00, 27000000.00, 0),
(12, 1, 3, 12, 50, '2025-04-16 19:41:13', 0, 540000.00, 27000000.00, 0),
(13, 1, 4, 13, 50, '2025-04-16 19:42:44', 0, 785000.00, 39250000.00, 0),
(14, 1, 4, 14, 50, '2025-04-16 19:43:20', 0, 785000.00, 39250000.00, 0),
(15, 1, 4, 15, 50, '2025-04-16 19:43:20', 0, 785000.00, 39250000.00, 0),
(16, 1, 4, 16, 50, '2025-04-16 19:43:20', 0, 785000.00, 39250000.00, 0),
(17, 1, 4, 17, 50, '2025-04-16 19:43:20', 0, 785000.00, 39250000.00, 0),
(18, 1, 5, 18, 50, '2025-04-16 19:44:28', 0, 420000.00, 21000000.00, 0),
(19, 1, 5, 19, 50, '2025-04-16 19:45:46', 0, 420000.00, 21000000.00, 0),
(20, 1, 5, 20, 50, '2025-04-16 19:45:46', 0, 420000.00, 21000000.00, 0),
(21, 1, 5, 21, 50, '2025-04-16 19:45:46', 0, 420000.00, 21000000.00, 0),
(22, 1, 5, 22, 50, '2025-04-16 19:45:46', 0, 420000.00, 21000000.00, 0),
(23, 1, 5, 23, 50, '2025-04-16 19:45:46', 0, 420000.00, 21000000.00, 0),
(24, 1, 5, 24, 50, '2025-04-16 19:45:46', 0, 420000.00, 21000000.00, 0),
(25, 1, 5, 25, 50, '2025-04-16 19:45:46', 0, 420000.00, 21000000.00, 0),
(26, 1, 6, 26, 50, '2025-04-17 14:15:15', 0, 569000.00, 28450000.00, 0),
(27, 1, 6, 27, 50, '2025-04-17 14:16:12', 0, 569000.00, 28450000.00, 0),
(28, 1, 6, 28, 50, '2025-04-17 14:16:12', 0, 569000.00, 28450000.00, 0),
(29, 1, 6, 29, 50, '2025-04-17 14:16:12', 0, 569000.00, 28450000.00, 0),
(30, 1, 6, 30, 50, '2025-04-17 14:16:12', 0, 569000.00, 28450000.00, 0),
(31, 5, 7, 31, 50, '2025-04-17 14:19:28', 0, 499000.00, 24950000.00, 0),
(32, 5, 7, 32, 50, '2025-04-17 14:20:05', 0, 499000.00, 24950000.00, 0),
(33, 5, 7, 33, 50, '2025-04-17 14:20:05', 0, 499000.00, 24950000.00, 0),
(34, 5, 7, 34, 50, '2025-04-17 14:20:05', 0, 499000.00, 24950000.00, 0),
(35, 5, 7, 35, 50, '2025-04-17 14:20:05', 0, 499000.00, 24950000.00, 0),
(36, 5, 7, 36, 50, '2025-04-17 14:20:05', 0, 499000.00, 24950000.00, 0),
(37, 2, 8, 37, 50, '2025-04-17 14:22:16', 0, 471000.00, 23550000.00, 0),
(38, 2, 8, 38, 50, '2025-04-17 14:22:51', 0, 471000.00, 23550000.00, 0),
(39, 2, 8, 39, 50, '2025-04-17 14:22:51', 0, 471000.00, 23550000.00, 0),
(40, 2, 8, 40, 50, '2025-04-17 14:22:51', 0, 471000.00, 23550000.00, 0),
(41, 2, 8, 41, 50, '2025-04-17 14:22:51', 0, 471000.00, 23550000.00, 0),
(42, 3, 9, 42, 50, '2025-04-17 14:25:37', 0, 441000.00, 22050000.00, 0),
(43, 3, 9, 42, 50, '2025-04-17 14:25:37', 0, 441000.00, 22050000.00, 0),
(44, 4, 10, 44, 50, '2025-04-17 14:30:05', 0, 441000.00, 22050000.00, 0),
(45, 4, 10, 45, 50, '2025-04-17 14:30:39', 0, 441000.00, 22050000.00, 0),
(46, 4, 10, 46, 50, '2025-04-17 14:30:39', 0, 441000.00, 22050000.00, 0),
(47, 4, 10, 47, 50, '2025-04-17 14:30:39', 0, 441000.00, 22050000.00, 0),
(48, 4, 10, 48, 50, '2025-04-17 14:30:39', 0, 441000.00, 22050000.00, 0),
(49, 4, 10, 49, 50, '2025-04-17 14:30:39', 0, 441000.00, 22050000.00, 0),
(50, 4, 11, 50, 50, '2025-04-17 14:30:39', 0, 399000.00, 19950000.00, 0),
(51, 4, 11, 51, 50, '2025-04-20 20:03:38', 0, 399000.00, 19950000.00, 0),
(52, 4, 11, 52, 50, '2025-04-20 20:03:57', 0, 399000.00, 19950000.00, 0),
(53, 4, 11, 53, 50, '2025-04-20 20:03:57', 0, 399000.00, 19950000.00, 0),
(54, 3, 12, 54, 50, '2025-04-20 20:07:30', 0, 499000.00, 24950000.00, 0),
(55, 3, 12, 55, 50, '2025-04-20 20:07:43', 0, 499000.00, 24950000.00, 0),
(56, 3, 12, 56, 50, '2025-04-20 20:07:43', 0, 499000.00, 24950000.00, 0),
(57, 3, 12, 57, 50, '2025-04-20 20:07:43', 0, 499000.00, 24950000.00, 0),
(58, 3, 12, 58, 50, '2025-04-20 20:07:43', 0, 499000.00, 24950000.00, 0),
(59, 3, 12, 59, 50, '2025-04-20 20:07:43', 0, 499000.00, 24950000.00, 0),
(60, 2, 13, 60, 50, '2025-04-20 20:11:51', 0, 981000.00, 49050000.00, 0),
(61, 2, 13, 61, 50, '2025-04-20 20:12:19', 0, 981000.00, 49050000.00, 0),
(62, 2, 13, 62, 50, '2025-04-20 20:12:19', 0, 981000.00, 49050000.00, 0),
(63, 2, 13, 63, 50, '2025-04-20 20:12:19', 0, 981000.00, 49050000.00, 0),
(64, 2, 13, 64, 50, '2025-04-20 20:12:19', 0, 981000.00, 49050000.00, 0),
(65, 2, 13, 65, 50, '2025-04-20 20:12:19', 0, 981000.00, 49050000.00, 0),
(66, 2, 14, 66, 50, '2025-04-20 20:14:44', 0, 540000.00, 27000000.00, 0),
(67, 2, 14, 67, 50, '2025-04-20 20:18:13', 0, 540000.00, 27000000.00, 0),
(68, 2, 14, 68, 50, '2025-04-20 20:18:41', 0, 540000.00, 27000000.00, 0),
(69, 2, 14, 69, 50, '2025-04-20 20:18:41', 0, 540000.00, 27000000.00, 0),
(70, 2, 14, 70, 50, '2025-04-20 20:18:41', 0, 540000.00, 27000000.00, 0),
(71, 2, 15, 71, 50, '2025-04-20 20:20:20', 0, 343000.00, 17150000.00, 0),
(72, 2, 15, 72, 50, '2025-04-20 20:20:36', 0, 343000.00, 17150000.00, 0),
(73, 2, 15, 73, 50, '2025-04-20 20:20:36', 0, 343000.00, 17150000.00, 0),
(74, 2, 15, 74, 50, '2025-04-20 20:20:36', 0, 343000.00, 17150000.00, 0),
(78, 3, 16, 78, 50, '2025-04-20 20:22:10', 0, 471000.00, 23550000.00, 0),
(79, 3, 16, 79, 50, '2025-04-20 20:23:15', 0, 471000.00, 23550000.00, 0),
(80, 3, 16, 80, 50, '2025-04-20 20:23:27', 0, 471000.00, 23550000.00, 0),
(81, 3, 17, 81, 50, '2025-04-20 20:25:12', 0, 589000.00, 29450000.00, 0),
(82, 3, 17, 82, 50, '2025-04-20 20:25:36', 0, 589000.00, 29450000.00, 0),
(83, 3, 17, 83, 50, '2025-04-20 20:25:36', 0, 589000.00, 29450000.00, 0),
(84, 3, 17, 84, 50, '2025-04-20 20:25:36', 0, 589000.00, 29450000.00, 0),
(85, 3, 17, 85, 50, '2025-04-20 20:25:36', 0, 589000.00, 29450000.00, 0),
(86, 3, 17, 86, 50, '2025-04-20 20:25:36', 0, 589000.00, 29450000.00, 0),
(87, 3, 18, 87, 50, '2025-04-20 20:27:15', 0, 540000.00, 27000000.00, 0),
(88, 3, 18, 88, 50, '2025-04-20 20:27:31', 0, 540000.00, 27000000.00, 0),
(89, 3, 18, 89, 50, '2025-04-20 20:27:31', 0, 540000.00, 27000000.00, 0),
(90, 3, 18, 90, 50, '2025-04-20 20:27:31', 0, 540000.00, 27000000.00, 0),
(91, 4, 19, 91, 50, '2025-04-20 20:28:52', 0, 736000.00, 36800000.00, 0),
(92, 4, 19, 92, 50, '2025-04-20 20:29:06', 0, 736000.00, 36800000.00, 0),
(93, 4, 19, 93, 50, '2025-04-20 20:29:06', 0, 736000.00, 36800000.00, 0),
(94, 5, 20, 94, 50, '2025-04-20 20:30:19', 0, 117000.00, 5850000.00, 0),
(95, 5, 20, 95, 50, '2025-04-20 20:30:42', 0, 117000.00, 5850000.00, 0),
(96, 5, 20, 96, 50, '2025-04-20 20:30:42', 0, 117000.00, 5850000.00, 0),
(97, 5, 20, 97, 50, '2025-04-20 20:30:42', 0, 117000.00, 5850000.00, 0),
(98, 5, 20, 98, 50, '2025-04-20 20:30:42', 0, 117000.00, 5850000.00, 0),
(99, 5, 20, 99, 50, '2025-04-20 20:30:42', 0, 117000.00, 5850000.00, 0),
(100, 5, 21, 100, 50, '2025-04-20 20:32:09', 0, 98000.00, 4900000.00, 0),
(101, 5, 21, 101, 50, '2025-04-20 20:32:43', 0, 98000.00, 4900000.00, 0),
(102, 5, 21, 102, 50, '2025-04-20 20:32:43', 0, 98000.00, 4900000.00, 0),
(103, 5, 21, 103, 50, '2025-04-20 20:32:43', 0, 98000.00, 4900000.00, 0),
(104, 5, 21, 104, 50, '2025-04-20 20:32:43', 0, 98000.00, 4900000.00, 0),
(105, 5, 21, 105, 50, '2025-04-20 20:32:43', 0, 98000.00, 4900000.00, 0),
(106, 5, 21, 106, 50, '2025-04-20 20:32:43', 0, 98000.00, 4900000.00, 0),
(107, 5, 21, 107, 50, '2025-04-20 20:32:43', 0, 98000.00, 4900000.00, 0),
(108, 5, 21, 108, 50, '2025-04-20 20:32:43', 0, 98000.00, 4900000.00, 0),
(109, 5, 22, 109, 50, '2025-04-20 20:33:57', 0, 94000.00, 4700000.00, 0),
(110, 5, 22, 110, 50, '2025-04-20 20:34:32', 0, 94000.00, 4700000.00, 0),
(111, 5, 22, 111, 50, '2025-04-20 20:34:32', 0, 94000.00, 4700000.00, 0),
(112, 5, 22, 112, 50, '2025-04-20 20:34:32', 0, 94000.00, 4700000.00, 0),
(113, 5, 22, 113, 50, '2025-04-20 20:34:32', 0, 94000.00, 4700000.00, 0),
(114, 5, 22, 114, 50, '2025-04-20 20:34:32', 0, 94000.00, 4700000.00, 0),
(115, 5, 22, 115, 50, '2025-04-20 20:34:32', 0, 94000.00, 4700000.00, 0),
(116, 5, 22, 116, 50, '2025-04-20 20:34:32', 0, 94000.00, 4700000.00, 0),
(117, 5, 22, 117, 50, '2025-04-20 20:34:32', 0, 94000.00, 4700000.00, 0),
(118, 6, 23, 118, 50, '2025-04-20 21:17:15', 0, 450000.00, 22500000.00, 0),
(119, 6, 23, 119, 50, '2025-04-20 21:17:15', 0, 450000.00, 22500000.00, 0),
(120, 6, 23, 120, 50, '2025-04-20 21:17:15', 0, 450000.00, 22500000.00, 0),
(121, 6, 23, 121, 50, '2025-04-20 21:17:15', 0, 450000.00, 22500000.00, 0),
(122, 6, 23, 122, 50, '2025-04-20 21:17:15', 0, 450000.00, 22500000.00, 0),
(123, 6, 23, 123, 50, '2025-04-20 21:17:15', 0, 450000.00, 22500000.00, 0),
(124, 6, 23, 124, 55, '2025-04-20 21:17:15', 0, 450000.00, 24750000.00, 0),
(125, 6, 23, 125, 50, '2025-04-20 21:17:15', 0, 450000.00, 22500000.00, 0),
(126, 6, 23, 126, 50, '2025-04-20 21:17:15', 0, 450000.00, 22500000.00, 0),
(127, 6, 23, 127, 50, '2025-04-20 21:17:15', 0, 450000.00, 22500000.00, 0),
(128, 6, 23, 128, 50, '2025-04-20 21:17:15', 0, 450000.00, 22500000.00, 0),
(129, 6, 23, 129, 50, '2025-04-20 21:17:15', 0, 450000.00, 22500000.00, 0),
(130, 6, 23, 130, 50, '2025-04-20 21:17:15', 0, 450000.00, 22500000.00, 0),
(131, 6, 23, 131, 50, '2025-04-20 21:17:15', 0, 450000.00, 22500000.00, 0),
(132, 6, 23, 132, 50, '2025-04-20 21:17:15', 0, 450000.00, 22500000.00, 0),
(133, 6, 23, 133, 50, '2025-04-20 21:17:15', 0, 450000.00, 22500000.00, 0),
(134, 6, 23, 134, 50, '2025-04-20 21:17:15', 0, 450000.00, 22500000.00, 0),
(135, 6, 23, 135, 50, '2025-04-20 21:17:15', 0, 450000.00, 22500000.00, 0),
(136, 6, 24, 136, 50, '2025-04-20 21:17:15', 0, 470000.00, 23500000.00, 0),
(137, 6, 24, 137, 50, '2025-04-20 21:17:15', 0, 470000.00, 23500000.00, 0),
(138, 6, 24, 138, 50, '2025-04-20 21:17:15', 0, 470000.00, 23500000.00, 0),
(139, 6, 24, 139, 50, '2025-04-20 21:17:15', 0, 470000.00, 23500000.00, 0),
(140, 6, 24, 140, 50, '2025-04-20 21:17:15', 0, 470000.00, 23500000.00, 0),
(141, 6, 24, 141, 50, '2025-04-20 21:17:15', 0, 470000.00, 23500000.00, 0),
(142, 6, 24, 142, 50, '2025-04-20 21:17:15', 0, 470000.00, 23500000.00, 0),
(143, 6, 24, 143, 50, '2025-04-20 21:17:15', 0, 470000.00, 23500000.00, 0),
(144, 6, 24, 144, 50, '2025-04-20 21:17:15', 0, 470000.00, 23500000.00, 0),
(145, 6, 24, 145, 50, '2025-04-20 21:17:15', 0, 470000.00, 23500000.00, 0),
(146, 6, 24, 146, 50, '2025-04-20 21:17:15', 0, 470000.00, 23500000.00, 0),
(147, 6, 24, 147, 50, '2025-04-20 21:17:15', 0, 470000.00, 23500000.00, 0),
(148, 7, 25, 148, 50, '2025-05-16 19:27:47', 0, 80000.00, 4000000.00, 0),
(149, 7, 25, 149, 50, '2025-05-16 19:27:47', 0, 80000.00, 4000000.00, 0),
(150, 7, 25, 150, 50, '2025-05-16 19:27:47', 0, 80000.00, 4000000.00, 0),
(151, 7, 25, 151, 50, '2025-05-16 19:27:47', 0, 80000.00, 4000000.00, 0),
(152, 7, 25, 152, 50, '2025-05-16 19:27:47', 0, 80000.00, 4000000.00, 0),
(153, 7, 25, 153, 50, '2025-05-16 19:27:47', 0, 80000.00, 4000000.00, 0),
(154, 8, 26, 154, 50, '2025-05-16 19:37:27', 0, 80000.00, 4000000.00, 0),
(155, 8, 26, 155, 50, '2025-05-16 19:37:27', 0, 80000.00, 4000000.00, 0),
(156, 9, 27, 156, 50, '2025-05-16 19:48:56', 0, 20000.00, 1000000.00, 0),
(157, 9, 27, 157, 50, '2025-05-16 19:48:56', 0, 20000.00, 1000000.00, 0),
(158, 9, 27, 158, 50, '2025-05-16 19:48:56', 0, 20000.00, 1000000.00, 0),
(159, 9, 28, 160, 50, '2025-05-16 19:48:56', 0, 22000.00, 1100000.00, 0),
(160, 9, 27, 158, 50, '2025-05-16 19:48:56', 0, 20000.00, 1000000.00, 0),
(161, 9, 27, 159, 50, '2025-05-16 19:48:56', 0, 20000.00, 1000000.00, 0),
(162, 9, 28, 161, 50, '2025-05-16 19:48:56', 0, 22000.00, 1100000.00, 0),
(163, 9, 28, 162, 50, '2025-05-16 19:48:56', 0, 22000.00, 1100000.00, 0),
(164, 9, 28, 163, 50, '2025-05-16 19:48:56', 0, 22000.00, 1100000.00, 0),
(165, 9, 29, 164, 50, '2025-05-16 19:48:56', 0, 25000.00, 1250000.00, 0),
(166, 9, 29, 165, 50, '2025-05-16 19:48:56', 0, 25000.00, 1250000.00, 0),
(167, 9, 29, 166, 50, '2025-05-16 19:48:56', 0, 25000.00, 1250000.00, 0),
(168, 9, 29, 167, 50, '2025-05-16 19:48:56', 0, 25000.00, 1250000.00, 0),
(169, 10, 30, 168, 50, '2025-05-16 20:01:39', 0, 55000.00, 2750000.00, 0),
(170, 10, 30, 169, 50, '2025-05-16 20:01:39', 0, 55000.00, 2750000.00, 0),
(171, 10, 30, 170, 30, '2025-05-16 20:01:39', 0, 55000.00, 1650000.00, 0),
(172, 10, 30, 170, 20, '2025-05-16 20:01:39', 0, 55000.00, 1100000.00, 0),
(173, 10, 30, 171, 30, '2025-05-16 20:01:39', 0, 55000.00, 1650000.00, 0),
(174, 10, 30, 171, 20, '2025-05-16 20:01:39', 0, 55000.00, 1100000.00, 0),
(175, 10, 30, 172, 30, '2025-05-16 20:01:39', 0, 55000.00, 1650000.00, 0),
(176, 10, 30, 172, 20, '2025-05-16 20:01:39', 0, 55000.00, 1100000.00, 0),
(177, 10, 30, 172, 20, '2025-05-16 20:01:39', 0, 55000.00, 1100000.00, 0),
(178, 10, 30, 173, 10, '2025-05-16 20:01:39', 0, 55000.00, 550000.00, 0),
(179, 10, 30, 173, 20, '2025-05-16 20:01:39', 0, 55000.00, 1100000.00, 0),
(180, 10, 30, 173, 20, '2025-05-16 20:01:39', 0, 55000.00, 1100000.00, 0),
(181, 10, 31, 174, 50, '2025-05-16 20:01:39', 0, 56000.00, 2800000.00, 0),
(182, 10, 31, 175, 50, '2025-05-16 20:01:39', 0, 56000.00, 2800000.00, 0),
(183, 10, 31, 176, 50, '2025-05-16 20:01:39', 0, 56000.00, 2800000.00, 0),
(184, 10, 31, 177, 50, '2025-05-16 20:01:39', 0, 56000.00, 2800000.00, 0),
(185, 10, 31, 178, 50, '2025-05-16 20:01:39', 0, 56000.00, 2800000.00, 0),
(186, 10, 31, 179, 50, '2025-05-16 20:01:39', 0, 56000.00, 2800000.00, 0),
(187, 10, 32, 180, 10, '2025-05-16 20:01:39', 0, 45000.00, 450000.00, 0),
(188, 10, 32, 180, 20, '2025-05-16 20:01:39', 0, 45000.00, 900000.00, 0),
(189, 10, 32, 180, 30, '2025-05-16 20:01:39', 0, 45000.00, 1350000.00, 0),
(190, 10, 32, 181, 50, '2025-05-16 20:01:39', 0, 45000.00, 2250000.00, 0),
(191, 11, 33, 182, 70, '2025-05-16 20:11:05', 0, 22000.00, 1540000.00, 0),
(192, 11, 33, 183, 70, '2025-05-16 20:11:05', 0, 22000.00, 1540000.00, 0),
(193, 11, 33, 184, 70, '2025-05-16 20:11:05', 0, 22000.00, 1540000.00, 0),
(194, 11, 33, 185, 70, '2025-05-16 20:11:05', 0, 22000.00, 1540000.00, 0),
(195, 11, 34, 186, 70, '2025-05-16 20:11:05', 0, 40000.00, 2800000.00, 0),
(196, 11, 34, 187, 70, '2025-05-16 20:11:05', 0, 40000.00, 2800000.00, 0),
(197, 11, 35, 188, 70, '2025-05-16 20:11:05', 0, 42000.00, 2940000.00, 0),
(198, 11, 35, 189, 70, '2025-05-16 20:11:05', 0, 42000.00, 2940000.00, 0),
(199, 11, 35, 190, 70, '2025-05-16 20:11:05', 0, 42000.00, 2940000.00, 0),
(200, 11, 35, 191, 70, '2025-05-16 20:11:05', 0, 42000.00, 2940000.00, 0),
(201, 11, 35, 192, 70, '2025-05-16 20:11:05', 0, 42000.00, 2940000.00, 0),
(202, 11, 35, 193, 70, '2025-05-16 20:11:05', 0, 42000.00, 2940000.00, 0),
(203, 11, 36, 194, 70, '2025-05-16 20:11:05', 0, 55000.00, 3850000.00, 0),
(204, 11, 36, 196, 70, '2025-05-16 20:11:05', 0, 55000.00, 3850000.00, 0),
(205, 11, 36, 195, 70, '2025-05-16 20:11:05', 0, 55000.00, 3850000.00, 0);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `status` enum('Chờ xác nhận','Đã thanh toán, chờ giao hàng','Đang giao hàng','Giao thành công','Đã huỷ') DEFAULT 'Chờ xác nhận',
  `total_price` decimal(12,2) DEFAULT NULL,
  `shipping_address` varchar(255) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `payment_method_id` int(11) DEFAULT NULL,
  `staff_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `status`, `total_price`, `shipping_address`, `note`, `created_at`, `payment_method_id`, `staff_id`) VALUES
(41, 5, 'Đang giao hàng', 3887800.00, 'Địa chỉ giao hàng mẫu 26', 'Ghi chú đơn hàng mẫu', '2025-04-05 00:00:00', 2, NULL),
(42, 5, 'Chờ xác nhận', 5057800.00, 'Địa chỉ giao hàng mẫu 56', 'Ghi chú đơn hàng mẫu', '2025-05-12 00:00:00', 1, NULL),
(43, 5, 'Đã thanh toán, chờ giao hàng', 5057800.00, 'Địa chỉ giao hàng mẫu 28', 'Ghi chú đơn hàng mẫu', '2025-05-08 00:00:00', 2, NULL),
(44, 2, 'Đã huỷ', 5057800.00, 'Địa chỉ giao hàng mẫu 3', 'Ghi chú đơn hàng mẫu', '2025-05-14 00:00:00', 2, NULL),
(45, 6, 'Giao thành công', 5057800.00, 'Địa chỉ giao hàng mẫu 76', 'Ghi chú đơn hàng mẫu', '2025-03-07 00:00:00', 2, NULL),
(46, 3, 'Giao thành công', 5057800.00, 'Địa chỉ giao hàng mẫu 38', 'Ghi chú đơn hàng mẫu', '2025-02-13 00:00:00', 1, NULL),
(47, 2, 'Giao thành công', 5057800.00, 'Địa chỉ giao hàng mẫu 15', 'Ghi chú đơn hàng mẫu', '2025-02-23 00:00:00', 1, NULL),
(48, 3, 'Đang giao hàng', 5057800.00, 'Địa chỉ giao hàng mẫu 71', 'Ghi chú đơn hàng mẫu', '2025-04-04 00:00:00', 1, NULL),
(49, 6, 'Giao thành công', 5057800.00, 'Địa chỉ giao hàng mẫu 47', 'Ghi chú đơn hàng mẫu', '2025-01-13 00:00:00', 2, NULL),
(50, 4, 'Giao thành công', 5057800.00, 'Địa chỉ giao hàng mẫu 95', 'Ghi chú đơn hàng mẫu', '2025-03-18 00:00:00', 2, NULL),
(51, 4, 'Giao thành công', 5057800.00, 'Địa chỉ giao hàng mẫu 52', 'Ghi chú đơn hàng mẫu', '2025-03-14 00:00:00', 2, NULL),
(52, 7, 'Giao thành công', 5057800.00, 'Địa chỉ giao hàng mẫu 22', 'Ghi chú đơn hàng mẫu', '2025-02-02 00:00:00', 1, NULL),
(53, 5, 'Đang giao hàng', 5057800.00, 'Địa chỉ giao hàng mẫu 26', 'Ghi chú đơn hàng mẫu', '2025-03-20 00:00:00', 2, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `order_detail_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(12,2) DEFAULT NULL,
  `total_price` decimal(12,2) DEFAULT NULL,
  `variant_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_details`
--

INSERT INTO `order_details` (`order_detail_id`, `order_id`, `product_id`, `quantity`, `price`, `total_price`, `variant_id`) VALUES
(41, 49, 7, 1, 648700.00, 648700.00, 34),
(42, 48, 7, 1, 648700.00, 648700.00, 34),
(43, 47, 7, 1, 648700.00, 648700.00, 34),
(44, 46, 7, 1, 648700.00, 648700.00, 34),
(45, 45, 7, 1, 648700.00, 648700.00, 34),
(46, 44, 7, 1, 648700.00, 648700.00, 34),
(47, 43, 7, 1, 648700.00, 648700.00, 34),
(48, 42, 7, 1, 648700.00, 648700.00, 34),
(49, 41, 7, 1, 648700.00, 648700.00, 34),
(50, 53, 25, 2, 104000.00, 208000.00, 152),
(51, 52, 25, 2, 104000.00, 208000.00, 152),
(52, 51, 25, 2, 104000.00, 208000.00, 152),
(53, 50, 25, 2, 104000.00, 208000.00, 152),
(54, 49, 25, 2, 104000.00, 208000.00, 152),
(55, 48, 25, 2, 104000.00, 208000.00, 152),
(56, 47, 25, 2, 104000.00, 208000.00, 152),
(57, 46, 25, 2, 104000.00, 208000.00, 152),
(58, 45, 25, 2, 104000.00, 208000.00, 152),
(59, 44, 25, 2, 104000.00, 208000.00, 152),
(60, 43, 25, 2, 104000.00, 208000.00, 152),
(61, 42, 25, 2, 104000.00, 208000.00, 152),
(62, 41, 25, 2, 104000.00, 208000.00, 152),
(63, 53, 21, 2, 127400.00, 254800.00, 106),
(64, 52, 21, 2, 127400.00, 254800.00, 106),
(65, 51, 21, 2, 127400.00, 254800.00, 106),
(66, 50, 21, 2, 127400.00, 254800.00, 106),
(67, 49, 21, 2, 127400.00, 254800.00, 106),
(68, 48, 21, 2, 127400.00, 254800.00, 106),
(69, 47, 21, 2, 127400.00, 254800.00, 106),
(70, 46, 21, 2, 127400.00, 254800.00, 106),
(71, 45, 21, 2, 127400.00, 254800.00, 106),
(72, 44, 21, 2, 127400.00, 254800.00, 106),
(73, 43, 21, 2, 127400.00, 254800.00, 106),
(74, 42, 21, 2, 127400.00, 254800.00, 106),
(75, 41, 21, 2, 127400.00, 254800.00, 106),
(76, 53, 14, 3, 702000.00, 2106000.00, 70),
(77, 52, 14, 3, 702000.00, 2106000.00, 70),
(78, 51, 14, 3, 702000.00, 2106000.00, 70),
(79, 50, 14, 3, 702000.00, 2106000.00, 70),
(80, 49, 14, 3, 702000.00, 2106000.00, 70),
(81, 48, 14, 3, 702000.00, 2106000.00, 70),
(82, 47, 14, 3, 702000.00, 2106000.00, 70),
(83, 46, 14, 3, 702000.00, 2106000.00, 70),
(84, 45, 14, 3, 702000.00, 2106000.00, 70),
(85, 44, 14, 3, 702000.00, 2106000.00, 70),
(86, 43, 14, 3, 702000.00, 2106000.00, 70),
(87, 42, 14, 3, 702000.00, 2106000.00, 70),
(88, 41, 14, 3, 702000.00, 2106000.00, 70),
(89, 53, 8, 1, 612300.00, 612300.00, 37),
(90, 52, 8, 1, 612300.00, 612300.00, 37),
(91, 51, 8, 1, 612300.00, 612300.00, 37),
(92, 50, 8, 1, 612300.00, 612300.00, 37),
(93, 49, 8, 1, 612300.00, 612300.00, 37),
(94, 48, 8, 1, 612300.00, 612300.00, 37),
(95, 47, 8, 1, 612300.00, 612300.00, 37),
(96, 46, 8, 1, 612300.00, 612300.00, 37),
(97, 45, 8, 1, 612300.00, 612300.00, 37),
(98, 44, 8, 1, 612300.00, 612300.00, 37),
(99, 43, 8, 1, 612300.00, 612300.00, 37),
(100, 42, 8, 1, 612300.00, 612300.00, 37),
(101, 41, 8, 1, 612300.00, 612300.00, 37),
(102, 53, 23, 2, 585000.00, 1170000.00, 121),
(103, 52, 23, 2, 585000.00, 1170000.00, 121),
(104, 51, 23, 2, 585000.00, 1170000.00, 121),
(105, 50, 23, 2, 585000.00, 1170000.00, 121),
(106, 49, 23, 2, 585000.00, 1170000.00, 121),
(107, 48, 23, 2, 585000.00, 1170000.00, 121),
(108, 47, 23, 2, 585000.00, 1170000.00, 121),
(109, 46, 23, 2, 585000.00, 1170000.00, 121),
(110, 45, 23, 2, 585000.00, 1170000.00, 121),
(111, 44, 23, 2, 585000.00, 1170000.00, 121),
(112, 43, 23, 2, 585000.00, 1170000.00, 121),
(113, 42, 23, 2, 585000.00, 1170000.00, 121);

-- --------------------------------------------------------

--
-- Table structure for table `payment_method`
--

CREATE TABLE `payment_method` (
  `payment_method_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payment_method`
--

INSERT INTO `payment_method` (`payment_method_id`, `name`, `is_deleted`) VALUES
(1, 'Thanh toán khi nhận hàng (COD)', 0),
(2, 'Chuyển khoản ngân hàng', 0);

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `permission_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `is_deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`permission_id`, `name`, `is_deleted`) VALUES
(1, 'Quản lý sản phẩm', 0),
(2, 'Quản lý đơn hàng', 0),
(3, 'Quản lý khách hàng', 0),
(4, 'Quản lý đơn nhập', 0),
(5, 'Xem báo cáo', 0),
(6, 'Quản lý quyền', 0),
(7, 'Quản lý nhà cung cấp', 0),
(8, 'Quản lý thuộc tính', 0),
(9, 'Quản lý nhân viên', 0),
(10, 'Quản lý tài khoản ngân hàng', 0);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `price` decimal(12,2) DEFAULT NULL,
  `rating_avg` float DEFAULT 0,
  `rating_count` int(11) DEFAULT 0,
  `sold_count` int(11) DEFAULT 0,
  `price_sale` decimal(12,2) DEFAULT NULL,
  `pttg` decimal(5,2) DEFAULT 0.00,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `name`, `description`, `category_id`, `price`, `rating_avg`, `rating_count`, `sold_count`, `price_sale`, `pttg`, `is_deleted`) VALUES
(1, 'Áo polo nam dệt kim tay ngắn form regular', 'Khám phá phong cách lịch lãm và thoải mái tuyệt đối với chiếc áo polo nam dệt kim tay ngắn form regular – lựa chọn hoàn hảo cho mọi chàng trai hiện đại. Được làm từ chất liệu cotton cao cấp pha sợi spandex, áo mang đến cảm giác mềm mại, thoáng khí nhưng vẫn giữ được form dáng chuẩn sau nhiều lần giặt. Thiết kế tinh tế với cổ áo bo gân, tay áo ôm nhẹ tạo điểm nhấn năng động, dễ dàng phối cùng quần jean, kaki hoặc short cho nhiều phong cách từ công sở đến dạo phố.\r\n\r\n✔️ Chất vải dày dặn – không bai xù\r\n✔️ Đường may chắc chắn – tỉ mỉ đến từng chi tiết\r\n✔️ Dễ phối đồ – phù hợp mọi hoàn cảnh\r\n✔️ Size đa dạng – phù hợp mọi vóc dáng\r\n\r\nSở hữu ngay để nâng tầm phong cách cùng sự thoải mái suốt cả ngày!', 4, 540000.00, 0, 0, 0, 702000.00, 30.00, 0),
(2, 'Áo polo nam phối màu tay và cổ form fitted', 'Tạo điểm nhấn cá tính trong tủ đồ của bạn với áo polo nam phối màu tay và cổ form fitted – thiết kế vừa vặn tôn dáng, chuẩn xu hướng thời trang hiện đại. Phần cổ áo và tay áo phối màu tinh tế mang lại cảm giác trẻ trung, năng động nhưng vẫn giữ được vẻ lịch lãm đặc trưng của dòng áo polo cổ điển.\r\n\r\nÁo được may từ chất liệu cotton co giãn 4 chiều, cho cảm giác mặc thoải mái, thấm hút mồ hôi tốt – lý tưởng cho các hoạt động thường ngày hay khi di chuyển ngoài trời. Đường may ôm nhẹ phần vai và thân, tôn lên vóc dáng săn chắc, phù hợp với các chàng trai yêu thích sự gọn gàng và phong cách.\r\n\r\n✔️ Form fitted – ôm nhẹ body, tôn dáng\r\n✔️ Cổ & tay áo phối màu nổi bật – tạo điểm nhấn phong cách\r\n✔️ Vải mềm, thoáng khí – mặc thoải mái cả ngày\r\n✔️ Phối đồ dễ dàng – phù hợp từ công sở đến cafe cuối tuần\r\n\r\nMột chiếc polo không chỉ đơn thuần là áo – mà là tuyên ngôn thời trang của bạn!', 4, 490000.00, 0, 0, 0, 637000.00, 30.00, 0),
(3, 'Áo sơ mi nam tay dài vải Nano Non Iron fitted', 'Đẳng cấp đến từ sự đơn giản – áo sơ mi nam tay dài vải Nano Non Iron form fitted là lựa chọn lý tưởng cho những quý ông hiện đại, bận rộn nhưng vẫn yêu cầu cao về phong cách. Với chất liệu vải Nano công nghệ cao, áo sở hữu khả năng chống nhăn vượt trội, giữ form chuẩn suốt cả ngày mà không cần ủi – giúp bạn tiết kiệm thời gian mà vẫn luôn chỉn chu.\r\n\r\nThiết kế form fitted ôm gọn vừa vặn, tôn dáng và mang lại sự thoải mái tối đa nhờ độ co giãn nhẹ. Cổ áo cứng cáp, tay áo sắc sảo, đường may tinh tế – chiếc sơ mi này phù hợp từ văn phòng công sở đến những buổi họp quan trọng hay sự kiện cần sự chuyên nghiệp.\r\n\r\n✔️ Chống nhăn – không cần ủi, mặc là phẳng\r\n✔️ Form fitted – ôm dáng vừa vặn, hiện đại\r\n✔️ Chất vải Nano mềm mại, thoáng khí, không gây bí\r\n✔️ Phối đồ linh hoạt – dễ dàng kết hợp với quần tây, jean hoặc kaki\r\n\r\nSở hữu ngay để nâng tầm phong thái chuyên nghiệp và tinh tế mỗi ngày!', 3, 540000.00, 0, 0, 0, 702000.00, 30.00, 0),
(4, 'Áo khoác gile nam chần bông có túi form regular', 'Giữ ấm đầy phong cách với áo khoác gile nam chần bông có túi form regular – thiết kế không tay hiện đại, thích hợp cho những ngày se lạnh nhưng vẫn cần sự linh hoạt trong di chuyển. Áo được chần bông nhẹ, êm ái, giúp giữ nhiệt tốt mà không gây nặng nề hay cồng kềnh.\r\n\r\nChất liệu vải dù chống nước nhẹ, dễ vệ sinh, kết hợp với lớp lót mềm mại bên trong mang đến cảm giác ấm áp và thoải mái khi mặc. Form regular vừa vặn với hầu hết vóc dáng, không kén người mặc. Hai túi tiện lợi hai bên, có thể giữ tay ấm hoặc đựng các vật dụng cá nhân như điện thoại, ví, tai nghe,...\r\n\r\n✔️ Chần bông giữ ấm – nhẹ, không dày cộm\r\n✔️ Kiểu dáng gọn gàng – dễ phối áo thun, sơ mi hoặc hoodie bên trong\r\n✔️ Túi tiện lợi – vừa thời trang vừa thực dụng\r\n✔️ Form regular – phù hợp với nhiều vóc dáng và phong cách\r\n\r\nChiếc gile lý tưởng để bạn kết hợp linh hoạt từ đi làm, đi chơi đến dạo phố mỗi ngày!', 5, 785000.00, 0, 0, 0, 1020500.00, 30.00, 0),
(5, 'Quần short nỉ nam cotton có hình thêu relax', 'Thoải mái, trẻ trung và đậm chất riêng – quần short nỉ nam cotton có hình thêu relax là lựa chọn hoàn hảo cho những ngày cuối tuần thảnh thơi hay các buổi tập thể thao nhẹ nhàng. Được làm từ chất liệu cotton nỉ mềm mại, sản phẩm mang đến cảm giác mặc cực kỳ dễ chịu, thoáng khí và thân thiện với làn da.\r\n\r\nThiết kế form suông vừa vặn, phối cùng phần hình thêu tinh tế ở ống quần tạo điểm nhấn cá tính mà không làm mất đi sự đơn giản, dễ phối đồ. Lưng quần có dây rút co giãn, giúp bạn điều chỉnh linh hoạt theo vóc dáng. Dù là đi chơi, ở nhà hay đi dạo phố – chiếc quần short này luôn mang lại vẻ năng động và tự tin cho bạn.\r\n\r\n✔️ Chất nỉ cotton dày dặn – thoáng, mềm, thấm hút mồ hôi\r\n✔️ Hình thêu relax – tinh tế, nổi bật nhưng không lòe loẹt\r\n✔️ Lưng thun co giãn – thoải mái mọi chuyển động\r\n✔️ Phối đồ cực dễ – kết hợp áo thun, tanktop hay hoodie đều hợp\r\n\r\nLà chiếc quần short “must-have” trong tủ đồ nam mỗi khi bạn muốn vừa thoải mái vừa trông thật cool!', 2, 420000.00, 0, 0, 0, 546000.00, 30.00, 0),
(6, 'Áo thun tay ngắn nam S.café in.Boxy', 'Nâng tầm trải nghiệm mặc hằng ngày với áo thun tay ngắn nam S.Café in.Boxy – thiết kế hiện đại kết hợp cùng chất liệu đột phá từ công nghệ S.Café. Với thành phần cafe tái chế tích hợp trong sợi vải, áo sở hữu khả năng khử mùi, thoáng khí và khô nhanh vượt trội, mang lại cảm giác thoải mái suốt cả ngày dài.\r\n\r\nPhom dáng boxy hiện đại, rộng rãi và cực kỳ trẻ trung – phù hợp cho những ai yêu thích sự năng động, phá cách nhưng vẫn tinh tế. Thiết kế in tối giản trước ngực tạo điểm nhấn nhẹ nhàng, dễ phối với nhiều phong cách từ basic đến streetwear.\r\n\r\n✔️ Vải S.Café – công nghệ xanh, thân thiện môi trường\r\n✔️ Khử mùi – thoáng khí – thấm hút mồ hôi tốt\r\n✔️ Form boxy – phóng khoáng, che dáng tốt\r\n✔️ Dễ phối đồ – phù hợp đi học, đi chơi, đi làm\r\n\r\nLựa chọn lý tưởng cho chàng trai hiện đại – sống xanh, mặc chất!', 1, 569000.00, 0, 0, 0, 739700.00, 30.00, 0),
(7, 'Áo thun tay ngắn nam S.Café gắn icon.Loose', 'Thoáng mát – bền bỉ – đậm cá tính: Áo thun tay ngắn nam S.Café gắn icon.Loose là lựa chọn hoàn hảo cho những ngày năng động và cần sự tự do trong từng chuyển động. Ứng dụng công nghệ vải S.Café – làm từ bã cà phê tái chế – áo sở hữu khả năng khử mùi, khô nhanh và chống tia UV, giúp bạn luôn tự tin và dễ chịu dù hoạt động cả ngày dài.\r\n\r\nThiết kế form loose rộng rãi, trẻ trung, mang hơi hướng streetwear thoải mái, dễ phối đồ. Điểm nhấn là chi tiết icon gắn trước ngực hoặc tay áo, tạo điểm nhấn tinh tế mà không rườm rà. Dù đi học, đi chơi hay đơn giản là dạo phố, chiếc áo này luôn đồng hành cùng phong cách tự do và năng động của bạn.\r\n\r\n✔️ Vải công nghệ S.Café – thân thiện môi trường, hiệu suất cao\r\n✔️ Khử mùi – chống UV – thoáng mát cả ngày\r\n✔️ Phom loose rộng rãi – dễ chịu, che dáng tốt\r\n✔️ Chi tiết icon gắn tinh tế – nhấn nhẹ cá tính riêng\r\n\r\nMột chiếc áo basic nhưng không nhàm chán – mặc nhẹ, sống chất!', 1, 499000.00, 0, 0, 0, 648700.00, 30.00, 0),
(8, 'Áo sơ mi oxford nam tay dài fitted - Smartshirt', 'Tinh tế trong từng đường nét – áo sơ mi Oxford nam tay dài form fitted Smartshirt là biểu tượng của sự chỉn chu và hiện đại dành cho nam giới. Được may từ vải Oxford dày dặn nhưng vẫn thoáng mát, áo mang đến cảm giác mặc dễ chịu cả ngày, giữ form chuẩn và ít nhăn – lý tưởng cho môi trường công sở hay các dịp cần lịch sự.\r\n\r\nThiết kế form fitted ôm nhẹ cơ thể, tôn dáng mà vẫn đảm bảo sự thoải mái khi vận động. Đặc biệt, chi tiết Smartshirt mang hàm ý thiết kế thông minh – từ chất liệu, đường may cho đến cử động đều được tối ưu, giúp bạn tự tin và linh hoạt trong mọi hoàn cảnh.\r\n\r\n✔️ Vải Oxford cao cấp – bền màu, đứng dáng, thoáng khí\r\n✔️ Thiết kế Smartshirt – hiện đại, tối ưu cử động\r\n✔️ Form fitted – gọn gàng, tôn dáng lịch thiệp\r\n✔️ Phù hợp đi làm, gặp đối tác, dạo phố hay hẹn hò\r\n\r\nMột chiếc sơ mi thông minh – dành cho người đàn ông tinh tế và biết chọn đúng phong cách.', 3, 471000.00, 0, 0, 0, 612300.00, 30.00, 0),
(9, 'Quần short denim nam form straight', 'Năng động, khỏe khoắn và không bao giờ lỗi mốt – quần short denim nam form straight là item không thể thiếu trong tủ đồ của mọi chàng trai hiện đại. Thiết kế ống suông vừa vặn, giúp tạo cảm giác cân đối và thoải mái khi mặc, phù hợp với nhiều vóc dáng.\r\n\r\nĐược làm từ chất liệu denim cao cấp, bề mặt mềm, độ bền cao và dễ giặt sạch. Phong cách tối giản nhưng vẫn nam tính, quần có thể kết hợp linh hoạt với áo thun, polo hay sơ mi – từ phong cách đời thường đến streetwear trẻ trung.\r\n\r\n✔️ Form straight – ống suông gọn gàng, dễ mặc\r\n✔️ Denim dày dặn – bền màu, không bai nhão\r\n✔️ Túi tiện lợi – tăng tính ứng dụng\r\n✔️ Phối đồ linh hoạt – phù hợp nhiều phong cách\r\n\r\nChiếc quần lý tưởng để bạn tự tin xuống phố – thoải mái nhưng vẫn cực “chất”!', 2, 441000.00, 0, 0, 0, 573300.00, 30.00, 0),
(10, 'Áo polo nam tay ngắn miếng patch ngực', 'Đơn giản nhưng nổi bật – áo polo nam tay ngắn có miếng patch ngực mang đến sự kết hợp hoàn hảo giữa phong cách cổ điển và điểm nhấn hiện đại. Thiết kế cổ bẻ truyền thống, tay bo gọn gàng kết hợp cùng miếng patch thêu/logo ngực cá tính, giúp chiếc áo trở nên khác biệt mà vẫn giữ được vẻ lịch sự vốn có của dòng polo.\r\n\r\nChất liệu cotton co giãn nhẹ, thấm hút mồ hôi tốt, đảm bảo sự thoải mái khi mặc suốt cả ngày. Form áo vừa vặn (regular/slim) tôn dáng, dễ phối đồ với mọi loại quần – từ jeans, kaki đến short.\r\n\r\n✔️ Thiết kế polo tay ngắn – trẻ trung, năng động\r\n✔️ Miếng patch ngực – tạo điểm nhấn nổi bật\r\n✔️ Chất vải cotton mềm mại, thoáng khí\r\n✔️ Dễ mặc – dễ phối – phù hợp nhiều hoàn cảnh\r\n\r\nChiếc áo polo cho chàng trai hiện đại – tinh tế trong từng chi tiết nhỏ!', 4, 441000.00, 0, 0, 0, 573300.00, 30.00, 0),
(11, 'Áo sơ mi nam tay ngắn kẻ sọc cotton fitted', 'Lịch lãm, gọn gàng và không bao giờ lỗi thời – áo sơ mi nam tay ngắn kẻ sọc cotton form fitted là lựa chọn hoàn hảo cho những chàng trai yêu thích phong cách nam tính nhưng vẫn thoải mái trong từng chuyển động. Thiết kế kẻ sọc tinh tế giúp tạo hiệu ứng thon gọn, tôn dáng tối đa.\r\n\r\nChất liệu cotton mềm mại, thoáng khí và thấm hút mồ hôi tốt – lý tưởng cho những ngày hè oi bức hay các hoạt động ngoài trời. Dáng fitted ôm nhẹ, ôn hoà giữa form slim và regular, giúp giữ sự chỉn chu mà không bị gò bó. Dễ phối cùng quần tây, jeans hoặc short cho nhiều phong cách từ đi làm đến đi chơi.\r\n\r\n✔️ Vải cotton thoáng mát – dễ chịu cả ngày dài\r\n✔️ Họa tiết kẻ sọc trẻ trung – tôn dáng, thanh lịch\r\n✔️ Form fitted – vừa vặn, không quá ôm\r\n✔️ Phối đồ linh hoạt – công sở, dạo phố đều hợp\r\n\r\nChiếc sơ mi không thể thiếu cho mùa hè – mặc lên là sáng cả outfit!', 3, 399000.00, 0, 0, 0, 518700.00, 30.00, 0),
(12, 'Áo sơ mi nam dài tay cotton cổ trụ form fitted', 'Tinh tế, tối giản và khác biệt – áo sơ mi nam dài tay cotton cổ trụ form fitted mang đến làn gió mới cho phong cách công sở và thời trang thường nhật. Thiết kế cổ trụ hiện đại, thay thế cổ áo truyền thống bằng đường bo gọn gàng, tạo nên vẻ ngoài trẻ trung, năng động mà vẫn giữ sự lịch thiệp cần có.\r\n\r\nÁo sử dụng chất liệu cotton cao cấp, mềm mại, thấm hút mồ hôi tốt, cho cảm giác mặc dễ chịu cả ngày. Form fitted ôm nhẹ cơ thể, tôn dáng mà vẫn thoải mái khi vận động. Phù hợp để phối cùng quần jeans, kaki hoặc quần tây – linh hoạt từ đi làm đến đi chơi.\r\n\r\n✔️ Cổ trụ hiện đại – phá cách nhẹ, tinh tế\r\n✔️ Vải cotton mềm mịn – thoáng mát, co giãn nhẹ\r\n✔️ Form fitted – gọn gàng, tôn dáng nam tính\r\n✔️ Dễ phối đồ – phù hợp cả phong cách casual lẫn smart\r\n\r\nChiếc sơ mi dành cho những người đàn ông yêu sự khác biệt nhưng vẫn luôn chỉn chu.', 3, 499000.00, 0, 0, 0, 648700.00, 30.00, 0),
(13, 'Áo khoác dày tay chần bông có túi form loose', 'Đối đầu thời tiết lạnh với phong cách thật chất – áo khoác dày tay chần bông có túi form loose là lựa chọn hoàn hảo cho những ngày đông lạnh nhưng vẫn muốn giữ được vẻ ngoài năng động, cá tính. Thiết kế tay áo chần bông giúp giữ ấm tối ưu, đồng thời tạo hiệu ứng layer nổi bật, mang lại vẻ ngoài khỏe khoắn và thời thượng.\r\n\r\nVới form loose rộng rãi, áo mang đến cảm giác mặc thoải mái, dễ layering cùng hoodie, sweater hay áo len bên trong. Hai túi lớn phía trước tiện lợi cho việc giữ ấm tay hoặc đựng các vật dụng cá nhân. Chất liệu vải dày dặn, giữ nhiệt tốt, thích hợp cho cả đi học, đi làm, hoặc du lịch mùa lạnh.\r\n\r\n✔️ Chần bông tay – giữ ấm tốt, tạo điểm nhấn cá tính\r\n✔️ Chất liệu dày – chắn gió, giữ nhiệt hiệu quả\r\n✔️ Form loose – phóng khoáng, thoải mái vận động\r\n✔️ Có túi tiện lợi – thực dụng và thời trang\r\n\r\nChiếc áo khoác lý tưởng cho mùa lạnh – ấm áp, năng động và cực dễ phối đồ.', 5, 981000.00, 0, 0, 0, 1275300.00, 30.00, 0),
(14, 'Áo sweater nam cổ tròn point label', 'Đơn giản mà khác biệt – áo sweater nam cổ tròn point label là lựa chọn lý tưởng cho những ngày se lạnh cần sự ấm áp nhẹ nhàng mà vẫn thời trang. Thiết kế cổ tròn cổ điển, tay dài bo gấu kết hợp với chi tiết point label trước ngực hoặc thân áo, tạo điểm nhấn tinh tế, tăng chiều sâu cho outfit mà không cần quá phô trương.\r\n\r\nÁo được làm từ chất vải nỉ bông mềm mại, dày vừa phải, giữ ấm tốt nhưng không gây bí bách. Phom dáng regular vừa vặn, dễ phối với quần jeans, jogger hoặc short – từ phong cách năng động đến tối giản đều', 5, 540000.00, 0, 0, 0, 702000.00, 30.00, 0),
(15, 'Áo thun nam cotton tay ngắn trơn regular', 'Tối giản nhưng không đơn điệu – áo thun nam cotton tay ngắn trơn form regular là item cơ bản mà bất kỳ tủ đồ nam nào cũng nên có. Với thiết kế trơn màu dễ phối, cổ tròn cổ điển và phom regular thoải mái, chiếc áo mang lại sự linh hoạt tuyệt đối trong mọi hoàn cảnh – từ mặc ở nhà, đi chơi đến phối layer cùng áo khoác.\r\n\r\nChất liệu 100% cotton cao cấp, mềm mại, thấm hút mồ hôi tốt, không gây bí da kể cả khi hoạt động ngoài trời hay trong thời tiết nóng. Đường may chắc chắn, bền dáng sau nhiều lần giặt, giữ cho áo luôn mới và form đẹp.\r\n\r\n✔️ Cotton 100% – mềm, mát, dễ chịu suốt cả ngày\r\n✔️ Form regular – thoải mái, phù hợp mọi vóc dáng\r\n✔️ Thiết kế trơn – dễ phối với bất kỳ outfit nào\r\n✔️ Cổ tròn cơ bản – trẻ trung, không lỗi mốt\r\n\r\nChiếc áo “basic” mà bạn sẽ muốn mặc đi mặc lại – càng đơn giản, càng dễ nổi bật.', 1, 343000.00, 0, 0, 0, 445900.00, 30.00, 0),
(16, 'Áo thun tay ngắn nam S.Café gắn icon.Fitted', 'Mặc đẹp mỗi ngày và thân thiện với môi trường – áo thun tay ngắn nam S.Café gắn icon.Fitted là sự kết hợp hoàn hảo giữa thời trang tối giản và công nghệ vải xanh bền vững. Chất liệu vải ứng dụng công nghệ S.Café từ bã cà phê tái chế, giúp áo sở hữu khả năng khử mùi, khô nhanh và chống tia UV, cực kỳ lý tưởng cho các chàng trai năng động.\r\n\r\nThiết kế form fitted ôm nhẹ, tôn dáng gọn gàng mà vẫn đảm bảo sự thoải mái. Điểm nhấn là chi tiết icon nhỏ gọn được gắn khéo léo ở ngực hoặc tay áo, giúp chiếc áo trở nên cá tính và khác biệt trong sự tối giản.\r\n\r\n✔️ Vải S.Café thân thiện môi trường – thoáng khí, khử mùi hiệu quả\r\n✔️ Phom dáng fitted – tôn body, hiện đại\r\n✔️ Thiết kế gắn icon – nhấn nhẹ cá tính, không cần cầu kỳ\r\n✔️ Dễ phối đồ – phù hợp cả mặc thường ngày lẫn thể thao nhẹ\r\n\r\nSự lựa chọn của chàng trai hiện đại: mặc đẹp, sống xanh, cảm thấy thoải mái cả ngày dài.', 1, 471000.00, 0, 0, 0, 612300.00, 30.00, 0),
(17, 'Áo polo nam premium 100% cotton phối sọc form fitt', 'Đẳng cấp đến từ sự tinh tế – áo polo nam premium 100% cotton phối sọc form fitted là lựa chọn lý tưởng cho những quý ông hiện đại yêu thích phong cách gọn gàng, lịch thiệp nhưng vẫn thoải mái. Sản phẩm được may từ 100% cotton cao cấp, mềm mịn, thoáng khí, thân thiện với làn da và giữ form cực tốt.\r\n\r\nThiết kế phối sọc tinh tế chạy dọc hoặc ngang tùy phiên bản, giúp tạo điểm nhấn bắt mắt mà không làm mất đi sự tối giản sang trọng. Phom fitted ôm nhẹ, tôn dáng và giúp tổng thể trở nên gọn gàng, hiện đại hơn. Phù hợp cho cả đi làm, gặp gỡ đối tác, hoặc những buổi dạo phố, cafe cuối tuần.\r\n\r\n✔️ 100% cotton cao cấp – mềm mát, thấm hút vượt trội\r\n✔️ Form fitted – ôm nhẹ, tôn dáng sang trọng\r\n✔️ Thiết kế phối sọc thanh lịch – không phô trương nhưng nổi bật\r\n✔️ Cổ polo chuẩn – giữ form, không bị bai nhão\r\n\r\nChiếc polo cao cấp giúp bạn nâng tầm phong cách – tinh gọn, lịch lãm, đầy khí chất.', 4, 589000.00, 0, 0, 0, 765700.00, 30.00, 0),
(18, 'Áo polo nam tay ngắn cotton sọc ngang form loose', 'Tự do thể hiện phong cách cá nhân với áo polo nam tay ngắn cotton sọc ngang form loose – lựa chọn lý tưởng cho những ai yêu thích sự thoải mái, trẻ trung mà vẫn giữ nét chỉn chu của chiếc áo cổ bẻ truyền thống. Thiết kế sọc ngang nổi bật, mang lại cảm giác năng động, khỏe khoắn và phù hợp với nhiều phong cách từ basic đến streetwear.\r\n\r\nChất liệu cotton mềm mại, thấm hút tốt, tạo cảm giác dễ chịu cả ngày dài. Phom áo loose rộng rãi, phù hợp với nhiều vóc dáng, dễ mix cùng jeans, quần short hay kaki để đi chơi, dạo phố hoặc hoạt động ngoài trời.\r\n\r\n✔️ Vải cotton cao cấp – thoáng khí, thấm hút mồ hôi\r\n✔️ Họa tiết sọc ngang – trẻ trung, cá tính\r\n✔️ Dáng loose – rộng rãi, thoải mái mọi chuyển động\r\n✔️ Cổ polo lịch sự – dễ mặc, dễ phối\r\n\r\nChiếc áo hoàn hảo cho những ngày muốn', 4, 540000.00, 0, 0, 0, 702000.00, 30.00, 0),
(19, 'Áo khoác denim nam form regular', 'Mạnh mẽ – cá tính – không bao giờ lỗi mốt. Áo khoác denim nam form regular là item kinh điển trong tủ đồ phái mạnh, giúp bạn dễ dàng tạo dấu ấn riêng với phong cách bụi bặm và nam tính. Thiết kế form regular vừa vặn, không quá ôm cũng không quá rộng, phù hợp với nhiều vóc dáng và dễ phối đồ trong mọi hoàn cảnh.\r\n\r\nSản phẩm sử dụng chất liệu denim dày dặn, bền màu, giúp giữ form tốt và mang lại cảm giác chắc chắn khi mặc. Đường may tỉ mỉ, chi tiết túi trước ngực và khuy kim loại tạo nên sự cân đối giữa cổ điển và hiện đại. Dễ dàng kết hợp với áo thun, hoodie hoặc sơ mi bên trong – từ dạo phố, đi làm đến đi chơi đều phù hợp.\r\n\r\n✔️ Denim cao cấp – đứng dáng, chắc tay, lâu phai màu\r\n✔️ Form regular – dễ mặc, dễ phối\r\n✔️ Thiết kế cổ điển – cá tính, nam tính, thời trang\r\n✔️ Tính ứng dụng cao – mặc quanh năm, phối được nhiều phong cách\r\n\r\nChiếc áo khoác denim', 5, 736000.00, 0, 0, 0, 956800.00, 30.00, 0),
(20, 'Quần lót nam organic cotton .Boxer', 'Thoải mái từ bên trong – quần lót nam organic cotton .Boxer mang đến cảm giác dễ chịu tối đa cho những chuyển động cả ngày dài. Được làm từ cotton hữu cơ (organic cotton) thân thiện với làn da và môi trường, chất vải mềm mịn, co giãn vừa phải, giúp ôm nhẹ nhưng không gây bó sát hay khó chịu.\r\n\r\nThiết kế dạng boxer suông nhẹ, thoáng khí, phù hợp cho những ai yêu thích sự thoải mái và tự do trong từng bước di chuyển. Cạp thun mềm, đàn hồi tốt, không để lại vết hằn trên da. Màu sắc tối giản, dễ sử dụng hàng ngày.\r\n\r\n✔️ Organic cotton – an toàn cho da, thân thiện môi trường\r\n✔️ Dáng boxer – thoáng mát, không bó sát\r\n✔️ Cạp thun mềm – co giãn tốt, giữ form ổn định\r\n✔️ Thiết kế đơn giản – tiện dụng, tinh tế\r\n\r\nSự lựa chọn hoàn hảo cho những quý ông đề cao cảm giác thoải mái và chất lượng bền vững.', 6, 117000.00, 0, 0, 0, 80000.00, 30.00, 0),
(21, 'Quần lót nam organic cotton phối lưng.Brief', 'Gọn gàng – thoải mái – chuẩn tinh tế. Quần lót nam organic cotton phối lưng.Brief được thiết kế dành riêng cho những quý ông ưu tiên cảm giác dễ chịu mà vẫn chú trọng đến phom dáng. Với chất liệu organic cotton thân thiện với làn da, sản phẩm mang lại độ mềm mại, thoáng khí và thấm hút mồ hôi hiệu quả – lý tưởng để mặc hằng ngày.\r\n\r\nKiểu dáng brief ôm gọn, hỗ trợ tốt mà không gây cấn hoặc khó chịu. Phần lưng thun phối màu hoặc họa tiết, đàn hồi tốt, tạo điểm nhấn cá tính nhưng không kém phần sang trọng. Sự kết hợp giữa tính năng và thiết kế giúp chiếc brief này vừa tiện dụng, vừa thời trang.\r\n\r\n✔️ Vải cotton hữu cơ – mềm mịn, lành tính, bảo vệ môi trường\r\n✔️ Dáng brief – ôm gọn, hỗ trợ tối đa\r\n✔️ Lưng phối nổi bật – cạp thun chắc chắn, không hằn da\r\n✔️ Phù hợp mặc hằng ngày – từ đi làm, thể thao đến nghỉ ngơi\r\n\r\nMột chiếc quần lót không chỉ để mặc – mà để bạn cảm thấy tự tin & thoải mái suốt cả ngày.', 6, 98000.00, 0, 0, 0, 127400.00, 30.00, 0),
(22, 'Quần Lót Nam Organic Cotton Trơn phối lưng. Boxer', 'Tối giản nhưng chuẩn mực – quần lót nam Organic Cotton trơn phối lưng .Boxer là lựa chọn hoàn hảo cho những chàng trai yêu thích cảm giác thoải mái, nhẹ nhàng và an toàn cho làn da. Sử dụng chất liệu organic cotton cao cấp, quần mang đến sự mềm mại, thoáng khí và thấm hút mồ hôi tốt – thích hợp để mặc suốt cả ngày dài, kể cả khi vận động nhiều.\r\n\r\nKiểu dáng boxer suông nhẹ, giúp ôm vừa vặn cơ thể mà không gây bí bách hay hằn da. Phần lưng thun bản to phối màu/hoạ tiết, tạo điểm nhấn tinh tế và giúp quần giữ form tốt, không bị cuộn hay xô lệch khi mặc.\r\n\r\n✔️ 100% organic cotton – an toàn, không gây kích ứng\r\n✔️ Dáng boxer – thoáng mát, không bó sát\r\n✔️ Thiết kế trơn – đơn giản, tinh tế\r\n✔️ Cạp thun phối – chắc chắn, tôn vẻ hiện đại\r\n\r\nMặc đơn giản nhưng cảm nhận sự chỉn chu từ bên trong – lựa chọn xứng tầm cho phái mạnh đề cao chất lượng và sự thoải mái.', 6, 94000.00, 0, 0, 0, 122200.00, 30.00, 0),
(23, 'Quần short nam cotton poly form straight', 'Quần short nam chất liệu cotton pha poly mềm mại, thoáng khí nhưng vẫn giữ form tốt\r\nThiết kế form straight đứng dáng, phù hợp nhiều vóc dáng và dễ phối đồ\r\nPhong cách tối giản, dễ ứng dụng trong cả sinh hoạt hàng ngày lẫn khi đi chơi\r\n\r\n✔️ Chất liệu: Cotton + Polyester bền nhẹ, co giãn nhẹ\r\n✔️ Kiểu dáng: Form straight – đứng dáng gọn gàng\r\n✔️ Lưng quần: Chun co giãn hoặc cài nút tuỳ mẫu\r\n✔️ Túi: Có túi hai bên tiện dụng\r\n✔️ Thích hợp: Đi học, đi làm, đi chơi', 2, 450000.00, 0, 0, 0, 585000.00, 30.00, 0),
(24, 'Quần short nam vải texture lưng chun loose', 'Quần short nam chất liệu vải texture độc đáo, tạo hiệu ứng bề mặt nhẹ nhàng và phong cách\r\nThiết kế lưng chun co giãn, thoải mái cho mọi dáng người, dễ dàng điều chỉnh\r\nForm dáng loose rộng rãi, mang lại sự thoải mái khi vận động hoặc mặc hàng ngày', 2, 470000.00, 0, 0, 0, 600000.00, 30.00, 0),
(25, 'Nón lưỡi trai hình thêu. Freesize', 'Khám phá sự kết hợp hoàn hảo giữa phong cách năng động và tinh thần trẻ trung với chiếc nón lưỡi trai hình thêu freesize – phụ kiện không thể thiếu cho mọi tín đồ thời trang. Được thiết kế với phom dáng ôm vừa vặn, nón mang đến cảm giác đội nhẹ nhàng, thoáng mát suốt cả ngày. Họa tiết thêu nổi bật giúp tạo điểm nhấn cá tính, dễ dàng mix cùng áo thun, sơ mi hay hoodie cho nhiều phong cách từ dạo phố đến du lịch.\r\n\r\n✔️ Chất liệu bền đẹp – giữ form tốt qua thời gian\r\n✔️ Họa tiết thêu sắc nét – nổi bật cá tính riêng\r\n✔️ Dây điều chỉnh linh hoạt – phù hợp mọi kích cỡ đầu\r\n✔️ Freesize – dễ dàng sử dụng cho cả nam và nữ\r\n\r\nSở hữu ngay chiếc nón \"must-have\" này để hoàn thiện outfit mỗi ngày thêm ấn tượng!', 7, NULL, 0, 0, 0, 104000.00, 30.00, 0),
(26, 'Nón bucket denim.Freesize', 'Khám phá nét phá cách đầy cá tính với chiếc nón bucket denim freesize – điểm nhấn hoàn hảo cho phong cách đường phố hiện đại. Thiết kế dáng tròn cổ điển kết hợp cùng chất liệu denim bền bỉ mang đến vẻ ngoài năng động, trẻ trung và không kém phần thời thượng. Phù hợp cho cả nam và nữ, nón dễ dàng phối cùng áo thun, sơ mi oversized hay set đồ streetwear để tạo nên một diện mạo đậm chất riêng.\r\n\r\n✔️ Chất liệu denim cao cấp – bền màu, giữ form tốt\r\n✔️ Thiết kế freesize – phù hợp nhiều kích cỡ đầu\r\n✔️ Phom nón ôm nhẹ – thoải mái khi đội lâu\r\n✔️ Dễ phối đồ – nâng tầm mọi outfit thường ngày\r\n\r\nThêm ngay vào tủ đồ để sẵn sàng “bung chất” cá tính ở bất cứ đâu!', 7, NULL, 0, 0, 0, 104000.00, 30.00, 0),
(27, 'Vớ hài dệt logo Routine', 'Hoàn thiện phong cách năng động và tinh tế với Pack 3 đôi vớ hài dệt logo Routine – phụ kiện nhỏ nhưng đóng vai trò lớn trong tủ đồ của bạn. Thiết kế cổ thấp trẻ trung, ôm chân vừa vặn, giúp bạn tự tin trong từng bước đi. Chất vải cotton pha sợi co giãn tạo cảm giác thoáng mát, mềm mại, thấm hút mồ hôi hiệu quả – lý tưởng cho cả ngày dài hoạt động.\r\n\r\nLogo Routine được dệt nổi bật, tinh tế ở phần mu bàn chân, tạo điểm nhấn cá tính khi kết hợp cùng sneaker hoặc giày thể thao.\r\n\r\n✔️ Thiết kế cổ thấp – giấu vớ khéo léo trong giày\r\n✔️ Co giãn linh hoạt – mang êm chân, không bị tuột\r\n✔️ Chất liệu thoáng khí – hạn chế mùi, khô thoáng suốt ngày dài\r\n✔️ Mỗi pack gồm 3 đôi – tiện lợi, dễ phối màu\r\n\r\nChọn vớ Routine – chọn sự chỉn chu đến từng chi tiết!', 7, 20000.00, 0, 0, 0, 29000.00, 30.00, 0),
(28, 'Vớ dài dệt icon. Freesize', 'Thể hiện phong cách cá nhân một cách tinh tế với vớ dài dệt icon – freesize, phụ kiện thời trang không thể thiếu trong tủ đồ của các tín đồ streetwear hiện đại. Thiết kế cổ cao cùng họa tiết icon dệt sắc nét giúp đôi vớ trở thành điểm nhấn đầy cá tính cho mỗi outfit, dù là đi học, đi làm hay dạo phố cuối tuần.\r\n\r\nSử dụng chất liệu cotton pha spandex mềm mại, co giãn tốt và thấm hút hiệu quả, vớ mang đến cảm giác thoải mái suốt ngày dài – ngay cả trong điều kiện vận động nhiều.\r\n\r\n✔️ Dệt icon tinh xảo – nổi bật cá tính riêng\r\n✔️ Chất vải dày dặn – hạn chế xù lông, giữ dáng lâu dài\r\n✔️ Co giãn 4 chiều – ôm vừa vặn, không bó chân\r\n✔️ Phù hợp mọi size – thiết kế freesize linh hoạt\r\n\r\nLựa chọn hoàn hảo để hoàn thiện set đồ năng động, chất riêng không trộn lẫn!', 7, 22000.00, 0, 0, 0, 29000.00, 30.00, 0),
(29, 'Vớ ngắn sọc ngang. Freesize', 'Thể hiện cá tính và gu thẩm mỹ độc đáo với vớ dài dệt icon – Freesize, phụ kiện không thể thiếu cho những ai yêu thích phong cách streetwear hiện đại. Với thiết kế cổ cao kết hợp cùng icon dệt sắc nét, đôi vớ giúp outfit thêm phần nổi bật dù chỉ là chi tiết nhỏ.\r\n\r\nChất vải cotton pha co giãn cao cấp mang đến cảm giác êm ái, thấm hút mồ hôi tốt và hạn chế mùi hiệu quả – lý tưởng để mang cả ngày dài mà vẫn khô thoáng, thoải mái.\r\n\r\n✔️ Thiết kế unisex – dễ phối với sneaker, boot, giày thể thao\r\n✔️ Dệt icon sắc sảo – nổi bật cá tính riêng\r\n✔️ Chất vải dày dặn – không xù, không chảy nhão\r\n✔️ Freesize – phù hợp với hầu hết kích thước chân\r\n\r\nTạo điểm nhấn khác biệt cho phong cách thường ngày của bạn chỉ với một đôi vớ!', 7, 25000.00, 0, 0, 0, 39000.00, 30.00, 0),
(30, 'Nón lưỡi trai phối màu. Freesize', 'Tạo điểm nhấn cá tính và năng động cho outfit mỗi ngày với nón lưỡi trai phối màu – freesize, phụ kiện thời trang không thể thiếu cho mọi phong cách đường phố. Thiết kế phối màu hài hòa cùng form nón cứng cáp giúp tổng thể trở nên thu hút hơn mà vẫn đảm bảo sự thoải mái khi đội.\r\n\r\nSản phẩm được may từ chất liệu vải dày dặn, thấm hút tốt, mang lại cảm giác thoáng mát trong cả ngày dài. Phần dây khóa phía sau dễ dàng điều chỉnh phù hợp với nhiều kích cỡ đầu, giúp ôm gọn và giữ form nón tốt trong thời gian dài sử dụng.\r\n\r\n✔️ Thiết kế phối màu hiện đại – nổi bật cá tính\r\n✔️ Form nón cứng cáp – không bị móp hay xệ\r\n✔️ Freesize – dễ dàng điều chỉnh theo vòng đầu\r\n✔️ Phù hợp nhiều phong cách – từ sporty đến casual\r\n\r\nMột item đơn giản nhưng đủ sức nâng tầm outfit hằng ngày của bạn!', 7, 55000.00, 0, 0, 0, 72000.00, 30.00, 0),
(31, 'Nón lưỡi trai hình thêu .Freesize', 'Hoàn thiện outfit với điểm nhấn ấn tượng từ nón lưỡi trai hình thêu – freesize, món phụ kiện tối giản nhưng đầy tinh tế cho những ai yêu thích sự năng động và cá tính. Phần hình thêu sắc nét ở mặt trước mang đến dấu ấn riêng biệt, dễ dàng kết hợp cùng nhiều phong cách từ đường phố đến thể thao.\r\n\r\nSản phẩm được làm từ chất liệu vải bền màu, thoáng khí, form nón chắc chắn cùng khóa điều chỉnh phía sau linh hoạt phù hợp với mọi kích cỡ đầu. Dù là đi học, đi chơi hay dạo phố, chiếc nón này đều là lựa chọn lý tưởng để hoàn thiện phong cách của bạn.\r\n\r\n✔️ Hình thêu nổi bật – tạo điểm nhấn tinh tế\r\n✔️ Vải dày thoáng – không xù lông, không bí da đầu\r\n✔️ Freesize – dễ dàng điều chỉnh kích cỡ\r\n✔️ Phối đồ linh hoạt – từ basic đến streetwear\r\n\r\nSở hữu ngay chiếc nón cực “chất” này để thể hiện phong cách riêng của bạn!', 7, NULL, 0, 0, 0, 72800.00, 30.00, 0),
(32, 'Nón lưỡi trai denim. Freesize', 'Tạo điểm nhấn cá tính với nón lưỡi trai denim – freesize, item không thể thiếu cho những tín đồ thời trang yêu thích chất liệu denim bụi bặm và phong cách đường phố. Với gam màu trung tính dễ phối, chiếc nón này giúp bạn “lên đồ” nhanh gọn mà vẫn nổi bật.\r\n\r\nThiết kế bo tròn ôm đầu, chất liệu denim mềm mại nhưng vẫn giữ form tốt, cùng phần khóa gài linh hoạt phía sau giúp điều chỉnh vừa vặn với mọi kích cỡ đầu. Dù là outfit đơn giản hay mix và match phá cách, chiếc nón này đều mang đến điểm nhấn trẻ trung, năng động.\r\n\r\n✔️ Chất liệu denim cao cấp – bền màu, lên form đẹp\r\n✔️ Form chuẩn – ôm đầu, thoải mái suốt ngày dài\r\n✔️ Freesize – dễ điều chỉnh, phù hợp mọi dáng đầu\r\n✔️ Dễ phối – hợp với jeans, áo thun, sơ mi, hoodie,...\r\n\r\nMua ngay để hoàn thiện phong cách denim bụi bặm và đầy cá tính!', 7, NULL, 0, 0, 0, 63000.00, 40.00, 0),
(33, 'Vớ dài .Freesize', 'Đôi vớ dài với dòng chữ Vớ Va Vớ Vẩn độc đáo không chỉ là một món phụ kiện giữ ấm mà còn thể hiện cá tính hài hước, chất chơi và khác biệt của bạn. Thiết kế unisex phù hợp cả nam và nữ, kiểu dáng cổ điển dễ phối cùng sneaker, giày thể thao hay thậm chí giày tây cho outfit thêm phần nổi bật.\r\n\r\n✔️ Chất liệu cotton co giãn – thoáng khí, thấm hút tốt\r\n✔️ In dệt chữ sắc nét – không bong tróc, bền màu\r\n✔️ Freesize – ôm chân thoải mái, không bị tuột\r\n✔️ Phối được nhiều phong cách – từ basic đến phá cách', 7, 22000.00, 0, 0, 0, 33000.00, 50.00, 0),
(34, 'Túi Tote hình in .Freesize', 'Túi Tote hình in – Đơn giản mà nổi bật!\r\nMang đậm tinh thần tối giản nhưng không kém phần cá tính, chiếc túi tote với họa tiết hình in độc đáo là điểm nhấn hoàn hảo cho mọi outfit hàng ngày. Thiết kế freesize rộng rãi, phù hợp để mang theo sách vở, laptop, hay vật dụng cá nhân khi đi học, đi làm hay dạo phố.\r\n\r\n✔️ Vải canvas dày dặn – bền bỉ, không xù lông\r\n✔️ Hình in sắc nét – trẻ trung, khó phai màu\r\n✔️ Dây đeo chắc chắn – thoải mái mang cả ngày dài\r\n✔️ Phối được nhiều phong cách – từ streetwear đến casual\r\n\r\nLựa chọn lý tưởng cho những ai yêu thích sự tiện lợi pha chút nghệ thuật!', 7, NULL, 0, 0, 0, 60000.00, 50.00, 0),
(35, 'Túi tote jean recycled. Freesize', 'Túi Tote Jean Recycled – Thời trang bền vững, phong cách khác biệt\r\nMang hơi thở của xu hướng sống xanh, túi tote jean recycled không chỉ là phụ kiện tiện dụng mà còn thể hiện cá tính và trách nhiệm với môi trường. Được làm từ chất liệu denim tái chế, túi giữ được độ bền, dày dặn và form đứng tự nhiên – phù hợp cho cả đi học, đi làm hay dạo phố.\r\n\r\n✔️ Chất liệu jean tái chế thân thiện môi trường – bền đẹp theo thời gian\r\n✔️ Thiết kế tối giản – dễ phối với mọi outfit từ năng động đến vintage\r\n✔️ Dây đeo chắc chắn – chịu lực tốt, tiện mang theo nhiều vật dụng\r\n✔️ Freesize – đựng thoải mái sách, vở, laptop hoặc đồ cá nhân\r\n\r\nLựa chọn hoàn hảo cho người trẻ yêu thời trang và quan tâm đến môi trường!', 7, NULL, 0, 0, 0, 63000.00, 50.00, 0),
(36, 'Túi Tote Bag', 'Túi Tote Đựng Cả Vũ Trụ – Cứu cánh cho mọi ngày bận rộn\r\nKhông chỉ là chiếc túi, đây là một tuyên ngôn cá tính. Với thiết kế slogan nổi bật \"Đựng Cả Vũ Trụ\", chiếc tote này mang đến sự kết hợp hoàn hảo giữa thời trang và tính ứng dụng. Chất vải canvas dày dặn, freesize thoải mái chứa cả laptop, tài liệu, đồ dùng cá nhân – sẵn sàng đồng hành từ sáng đến tối.\r\n\r\n✔️ In chữ nổi bật, chất vải canvas cứng cáp\r\n✔️ Kích thước lớn – đựng \"cả vũ trụ\" thật sự\r\n✔️ Quai đeo chắc chắn, may viền tỉ mỉ\r\n✔️ Phù hợp mọi phong cách – đi học, đi làm hay dạo phố\r\n\r\nTúi tiện dụng – thần thái cực chất, dành riêng cho những tâm hồn rộng lớn!', 7, 55000.00, 0, 0, 0, 82500.00, 50.00, 0);

-- --------------------------------------------------------

--
-- Table structure for table `product_variants`
--

CREATE TABLE `product_variants` (
  `variant_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `stock` int(11) DEFAULT 0,
  `color_id` int(11) DEFAULT NULL,
  `size_id` int(11) DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_variants`
--

INSERT INTO `product_variants` (`variant_id`, `product_id`, `image`, `stock`, `color_id`, `size_id`, `is_deleted`) VALUES
(1, 1, '10f24kni004-ivory-ao-len-tay-ngan-nam-1-jpg-uz8v.jpg', 50, 8, 4, 0),
(2, 1, '10f24kni004-ivory-ao-len-tay-ngan-nam-1-jpg-uz8v.jpg', 50, 8, 3, 0),
(3, 1, '10f24kni004-ivory-ao-len-tay-ngan-nam-1-jpg-uz8v.jpg', 50, 8, 5, 0),
(4, 1, '10f24kni004-navy-ao-len-tay-ngan-nam-1-jpg-kah0.jpg', 50, 12, 5, 0),
(5, 1, '10f24kni004-navy-ao-len-tay-ngan-nam-1-jpg-kah0.jpg', 47, 12, 4, 0),
(6, 2, '10f24pol023-d-sapphire-rainy-day-ao-polo-nam-1-jpg-4u8s.jpg', 36, 7, 2, 0),
(7, 2, '10f24pol023-rainy-day-covert-green-ao-polo-nam-1-jpg-qcn0.jpg', 46, 2, 3, 0),
(8, 2, '10f24pol023-d-sapphire-rainy-day-ao-polo-nam-1-jpg-4u8s.jpg', 50, 7, 3, 0),
(9, 3, '10s25shl015-black-ao-so-mi-nam-1-jpg-o1t0.jpg', 44, 1, 4, 0),
(10, 3, '10s25shl015-white-ao-so-mi-nam-1-jpg-4e7k.jpg', 50, 8, 4, 0),
(11, 3, '10s25shl015-black-ao-so-mi-nam-1-jpg-o1t0.jpg', 50, 1, 5, 0),
(12, 3, '10s25shl015-white-ao-so-mi-nam-1-jpg-4e7k.jpg', 50, 8, 5, 0),
(13, 4, '10f24jac005-dried-sage-ao-khoac-nam-1-jpg-7q20.jpg', 48, 6, 5, 0),
(14, 4, '10f24jac005-silver-birch-ao-khoac-nam-1-jpg-3nvj.jpg', 50, 8, 4, 0),
(15, 4, '10f24jac005-black-ao-khoac-nam-1-jpg-x1gs.jpg', 50, 1, 4, 0),
(16, 4, '10f24jac005-silver-birch-ao-khoac-nam-1-jpg-3nvj.jpg', 46, 8, 3, 0),
(17, 4, '10f24jac005-black-ao-khoac-nam-1-jpg-x1gs.jpg', 50, 1, 5, 0),
(18, 5, '10F24PKS001_BITTER-CHOCO_1_quan-short-nam-1-ubui.jpg', 50, 13, 2, 0),
(19, 5, '10F24PKS001_BITTER-CHOCO_1_quan-short-nam-1-ubui.jpg', 48, 13, 1, 0),
(20, 5, '10F24PKS001_BLACK_1_quan-short-nam-1-ocls.jpg', 50, 1, 3, 0),
(21, 5, '10F24PKS001_BLACK_1_quan-short-nam-1-ocls.jpg', 50, 1, 1, 0),
(22, 5, '10F24PKS001_COVERT-GREEN_1_quan-short-nam-1-rgiw.jpg', 50, 2, 2, 0),
(23, 5, '10F24PKS001_COVERT-GREEN_1_quan-short-nam-1-rgiw.jpg', 50, 2, 3, 0),
(24, 5, '10F24PKS001_PUMICE-STONE_quan-short-nam-1-jkaw.jpg', 50, 8, 1, 0),
(25, 5, '10F24PKS001_PUMICE-STONE_quan-short-nam-1-jkaw.jpg', 50, 8, 2, 0),
(26, 6, '10s25tss048-matcha-latte-1-jpg-6tuh.jpg', 50, 6, 3, 0),
(27, 6, '10s25tss048-matcha-latte-1-jpg-6tuh.jpg', 50, 6, 4, 0),
(28, 6, '10s25tss048-strawberry-1-jpg-ayc7.jpg', 50, 9, 3, 0),
(29, 6, '10s25tss048-strawberry-1-jpg-ayc7.jpg', 50, 9, 4, 0),
(30, 6, '10s25tss048-strawberry-1-jpg-ayc7.jpg', 50, 9, 5, 0),
(31, 7, '10s25tss008-cool-brew-1-jpg-xdx8.jpg', 48, 3, 2, 0),
(32, 7, '10s25tss008-cool-brew-1-jpg-xdx8.jpg', 50, 3, 3, 0),
(33, 7, '10s25tss008-cool-brew-1-jpg-xdx8.jpg', 50, 3, 4, 0),
(34, 7, '10s25tss008-brown-rice-1-jpg-vnjs.jpg', 50, 2, 3, 0),
(35, 7, '10s25tss008-brown-rice-1-jpg-vnjs.jpg', 50, 2, 4, 0),
(36, 7, '10s25tss008-brown-rice-1-jpg-vnjs.jpg', 50, 2, 5, 0),
(37, 8, '10S24SHL002C_WHITE_ao-so-mi-nam-dai-tay-1-szlg.jpg', 50, 8, 6, 0),
(38, 8, '10S24SHL002C_WHITE_ao-so-mi-nam-dai-tay-1-szlg.jpg', 50, 8, 5, 0),
(39, 8, '10S24SHL002C_WHITE_ao-so-mi-nam-dai-tay-1-szlg.jpg', 50, 8, 2, 0),
(40, 8, '10s24shl002c-blue-ao-so-mi-nam-1-jpg-6bdu.jpg', 50, 11, 5, 0),
(41, 8, '10s24shl002c-blue-ao-so-mi-nam-1-jpg-6bdu.jpg', 50, 7, 3, 0),
(42, 9, '10f24dps002-l-indigo-quan-short-nam-1-jpg-88kg.jpg', 50, 7, 3, 0),
(43, 9, '10f24dps002-l-indigo-quan-short-nam-1-jpg-88kg.jpg', 50, 7, 2, 0),
(44, 10, '10s25pol015-black-1-jpg-soav.jpg', 50, 1, 1, 0),
(45, 10, '10s25pol015-black-1-jpg-soav.jpg', 50, 1, 2, 0),
(46, 10, '10s25pol015-black-1-jpg-soav.jpg', 50, 1, 3, 0),
(47, 10, '10s25pol015-white-alyssum-1-jpg-3yjd.jpg', 50, 8, 3, 0),
(48, 10, '10s25pol015-white-alyssum-1-jpg-3yjd.jpg', 50, 8, 4, 0),
(49, 10, '10s25pol015-white-alyssum-1-jpg-3yjd.jpg', 50, 8, 6, 0),
(50, 11, '10s25shs016-l-blue-ao-so-mi-nam-1-jpg-9zxg.jpg', 50, 11, 5, 0),
(51, 11, '10s25shs016-m-blue-ao-so-mi-nam-1-jpg-8og0.jpg', 50, 7, 6, 0),
(52, 11, '10s25shs016-l-blue-ao-so-mi-nam-1-jpg-9zxg.jpg', 50, 11, 1, 0),
(53, 11, '10s25shs016-m-blue-ao-so-mi-nam-1-jpg-8og0.jpg', 50, 7, 1, 0),
(54, 12, '10s24shl006c-covert-green-1-ao-so-mi-nam-1-jpg.jpg', 50, 10, 2, 0),
(55, 12, '10s24shl006c-covert-green-1-ao-so-mi-nam-1-jpg.jpg', 50, 10, 3, 0),
(56, 12, '10s24shl006c-covert-green-1-ao-so-mi-nam-1-jpg.jpg', 50, 10, 4, 0),
(57, 12, '10s24shl006-blue-1-jpg-3n6n.jpg', 50, 11, 5, 0),
(58, 12, '10s24shl006-blue-1-jpg-3n6n.jpg', 50, 11, 6, 0),
(59, 12, '10s24shl006-blue-1-jpg-3n6n.jpg', 50, 11, 3, 0),
(60, 13, '10f24jac004-black-ao-khoac-nam-5-jpg-g0vc.jpg', 50, 1, 3, 0),
(61, 13, '10f24jac004-black-ao-khoac-nam-5-jpg-g0vc.jpg', 50, 1, 4, 0),
(62, 13, '10f24jac004-black-ao-khoac-nam-5-jpg-g0vc.jpg', 50, 1, 5, 0),
(63, 13, '10f24jac004-silver-birch-ao-khoac-nam-5-jpg-3uy2.jpg', 50, 8, 4, 0),
(64, 13, '10f24jac004-silver-birch-ao-khoac-nam-5-jpg-3uy2.jpg', 50, 8, 5, 0),
(65, 13, '10f24jac004-silver-birch-ao-khoac-nam-5-jpg-3uy2.jpg', 50, 8, 6, 0),
(66, 14, '10F23SWE001_BLUE-QUARTZ_ao-sweater-nam-3-hfyp.jpg', 50, 7, 2, 0),
(67, 14, '10F23SWE001_BLUE-QUARTZ_ao-sweater-nam-3-hfyp.jpg', 50, 7, 3, 0),
(68, 14, '10F23SWE001_BLUE-QUARTZ_ao-sweater-nam-3-hfyp.jpg', 50, 7, 4, 0),
(69, 14, '10F23SWE001_BLACK_ao-sweater-nam-3-nmkb.jpg', 50, 1, 1, 0),
(70, 14, '10F23SWE001_BLACK_ao-sweater-nam-3-nmkb.jpg', 50, 1, 6, 0),
(71, 15, '10s25tss003-bark-1-jpg-kg4r.jpg', 50, 5, 5, 0),
(72, 15, '10s25tss003-bark-1-jpg-kg4r.jpg', 50, 5, 6, 0),
(73, 15, '10s25tss003-white-1-jpg-hp1y.jpg', 50, 8, 2, 0),
(74, 15, '10s25tss003-white-1-jpg-hp1y.jpg', 50, 8, 3, 0),
(78, 16, '10s25tss006-cool-brew-1-jpg-7zno.jpg', 50, 3, 4, 0),
(79, 16, '10s25tss006-cool-brew-1-jpg-7zno.jpg', 50, 3, 5, 0),
(80, 16, '10s25tss006-cool-brew-1-jpg-7zno.jpg', 50, 3, 6, 0),
(81, 17, '10s24pol004p-bright-white-ao-polo-nam-6-jpg-2233.jpg', 50, 8, 1, 0),
(82, 17, '10s24pol004p-bright-white-ao-polo-nam-6-jpg-2233.jpg', 50, 8, 2, 0),
(83, 17, '10s24pol004p-bright-white-ao-polo-nam-6-jpg-2233.jpg', 50, 8, 3, 0),
(84, 17, '10s24pol004p-dark-sapphire-ao-polo-nam-6-jpg-y684.jpg', 50, 1, 3, 0),
(85, 17, '10s24pol004p-dark-sapphire-ao-polo-nam-6-jpg-y684.jpg', 50, 1, 4, 0),
(86, 17, '10s24pol004p-dark-sapphire-ao-polo-nam-6-jpg-y684.jpg', 50, 1, 5, 0),
(87, 18, '10f24pol012-navy-blazer-ao-polo-nam-1-jpg-o7bd.jpg', 50, 7, 3, 0),
(88, 18, '10f24pol012-navy-blazer-ao-polo-nam-1-jpg-o7bd.jpg', 50, 7, 4, 0),
(89, 18, '10f24pol012-white-ao-polo-nam-1-jpg-7idj.jpg', 50, 8, 4, 0),
(90, 18, '10f24pol012-white-ao-polo-nam-1-jpg-7idj.jpg', 50, 8, 5, 0),
(91, 19, '10f24dja002-d-grey-ao-khoac-jean-nam-5-jpg-kyn2.jpg', 50, 1, 4, 0),
(92, 19, '10f24dja002-d-grey-ao-khoac-jean-nam-5-jpg-kyn2.jpg', 50, 1, 5, 0),
(93, 19, '10f24dja002-d-grey-ao-khoac-jean-nam-5-jpg-kyn2.jpg', 50, 1, 6, 0),
(94, 20, '10s25und001-white-jpg-um7a.jpg', 50, 8, 1, 0),
(95, 20, '10s25und001-white-jpg-um7a.jpg', 50, 8, 2, 0),
(96, 20, '10s25und001-white-jpg-um7a.jpg', 50, 8, 4, 0),
(97, 20, '10s25und001-navy-jpg-jf6p.jpg', 50, 12, 3, 0),
(98, 20, '10s25und001-navy-jpg-jf6p.jpg', 50, 12, 4, 0),
(99, 20, '10s25und001-navy-jpg-jf6p.jpg', 50, 12, 5, 0),
(100, 21, '10s25und002-grey-jpg-imo1.jpg', 50, 4, 1, 0),
(101, 21, '10s25und002-grey-jpg-imo1.jpg', 50, 4, 2, 0),
(102, 21, '10s25und002-grey-jpg-imo1.jpg', 50, 4, 3, 0),
(103, 21, '10s25und002-navy-jpg-8v44.jpg', 50, 12, 2, 0),
(104, 21, '10s25und002-navy-jpg-8v44.jpg', 50, 12, 3, 0),
(105, 21, '10s25und002-navy-jpg-8v44.jpg', 50, 12, 4, 0),
(106, 21, '10s25und002-black-jpg-tlol.jpg', 50, 1, 1, 0),
(107, 21, '10s25und002-black-jpg-tlol.jpg', 50, 1, 2, 0),
(108, 21, '10s25und002-black-jpg-tlol.jpg', 50, 1, 3, 0),
(109, 22, '10f24und005-grey-boxer-nam-1-jpg-9h5j.jpg', 50, 4, 2, 0),
(110, 22, '10f24und005-grey-boxer-nam-1-jpg-9h5j.jpg', 50, 4, 3, 0),
(111, 22, '10f24und005-grey-boxer-nam-1-jpg-9h5j.jpg', 50, 4, 4, 0),
(112, 22, '10f24und005-navy-boxer-nam-1-jpg-7eq3.jpg', 50, 12, 2, 0),
(113, 22, '10f24und005-navy-boxer-nam-1-jpg-7eq3.jpg', 50, 12, 3, 0),
(114, 22, '10f24und005-navy-boxer-nam-1-jpg-7eq3.jpg', 50, 12, 4, 0),
(115, 22, '10f24und005-black-boxer-nam-1-jpg-2ibv.jpg', 50, 1, 2, 0),
(116, 22, '10f24und005-black-boxer-nam-1-jpg-2ibv.jpg', 50, 1, 3, 0),
(117, 22, '10f24und005-black-boxer-nam-1-jpg-2ibv.jpg', 50, 1, 4, 0),
(118, 23, '10s24psh003-black-quan-short-nam-1-jpg-67om.jpg', 50, 1, 1, 0),
(119, 23, '10s24psh003-black-quan-short-nam-1-jpg-67om.jpg', 50, 1, 2, 0),
(120, 23, '10s24psh003-black-quan-short-nam-1-jpg-67om.jpg', 50, 1, 3, 0),
(121, 23, '10s24psh003-black-quan-short-nam-1-jpg-67om.jpg', 50, 1, 4, 0),
(122, 23, '10s24psh003-black-quan-short-nam-1-jpg-67om.jpg', 50, 1, 5, 0),
(123, 23, '10s24psh003-black-quan-short-nam-1-jpg-67om.jpg', 50, 1, 6, 0),
(124, 23, '10s24psh003r1-safari-1-jpg-1ry0.jpg', 55, 2, 1, 0),
(125, 23, '10s24psh003r1-safari-1-jpg-1ry0.jpg', 50, 2, 2, 0),
(126, 23, '10s24psh003r1-safari-1-jpg-1ry0.jpg', 50, 2, 3, 0),
(127, 23, '10s24psh003r1-safari-1-jpg-1ry0.jpg', 50, 2, 4, 0),
(128, 23, '10s24psh003r1-safari-1-jpg-1ry0.jpg', 50, 2, 5, 0),
(129, 23, '10s24psh003r1-safari-1-jpg-1ry0.jpg', 50, 2, 6, 0),
(130, 23, '10s24psh003-tofu-quan-short-nam-1-jpg-tcwv.jpg', 50, 8, 1, 0),
(131, 23, '10s24psh003-tofu-quan-short-nam-1-jpg-tcwv.jpg', 50, 8, 2, 0),
(132, 23, '10s24psh003-tofu-quan-short-nam-1-jpg-tcwv.jpg', 50, 8, 3, 0),
(133, 23, '10s24psh003-tofu-quan-short-nam-1-jpg-tcwv.jpg', 50, 8, 4, 0),
(134, 23, '10s24psh003-tofu-quan-short-nam-1-jpg-tcwv.jpg', 50, 8, 5, 0),
(135, 23, '10s24psh003-tofu-quan-short-nam-1-jpg-tcwv.jpg', 50, 8, 6, 0),
(136, 24, '10s25psh051-peyote-quan-short-nam-1-jpg-h3dj.jpg', 50, 2, 1, 0),
(137, 24, '10s25psh051-peyote-quan-short-nam-1-jpg-h3dj.jpg', 50, 2, 2, 0),
(138, 24, '10s25psh051-peyote-quan-short-nam-1-jpg-h3dj.jpg', 50, 2, 3, 0),
(139, 24, '10s25psh051-peyote-quan-short-nam-1-jpg-h3dj.jpg', 50, 2, 4, 0),
(140, 24, '10s25psh051-peyote-quan-short-nam-1-jpg-h3dj.jpg', 50, 2, 5, 0),
(141, 24, '10s25psh051-peyote-quan-short-nam-1-jpg-h3dj.jpg', 50, 2, 6, 0),
(142, 24, '10s25psh051-tea-quan-short-nam-1-jpg-4ix2.jpg', 50, 6, 1, 0),
(143, 24, '10s25psh051-tea-quan-short-nam-1-jpg-4ix2.jpg', 50, 6, 2, 0),
(144, 24, '10s25psh051-tea-quan-short-nam-1-jpg-4ix2.jpg', 50, 6, 3, 0),
(145, 24, '10s25psh051-tea-quan-short-nam-1-jpg-4ix2.jpg', 50, 6, 4, 0),
(146, 24, '10s25psh051-tea-quan-short-nam-1-jpg-4ix2.jpg', 50, 6, 5, 0),
(147, 24, '10s25psh051-tea-quan-short-nam-1-jpg-4ix2.jpg', 50, 6, 6, 0),
(148, 25, '10s25cap011-black-1-jpg-7snu.jpg', 50, 1, 1, 0),
(149, 25, '10s25cap011-black-1-jpg-7snu.jpg', 50, 1, 2, 0),
(150, 25, '10s25cap011-red-1-jpg-wqkn.jpg', 50, 9, 1, 0),
(151, 25, '10s25cap011-red-1-jpg-wqkn.jpg', 50, 9, 2, 0),
(152, 25, 'flatlay21-34958-jpg-89xt.jpg', 50, 12, 1, 0),
(153, 25, 'flatlay21-34958-jpg-89xt.jpg', 50, 12, 2, 0),
(154, 26, '10s25hat001-blue-2-jpg-pfmm.jpg', 50, 1, 1, 0),
(155, 26, '10s25hat001-blue-2-jpg-pfmm.jpg', 50, 1, 2, 0),
(156, 27, '10s25soc022-2-jpg-qe2d.jpg', 50, 8, 1, 0),
(157, 27, '10s25soc022-2-jpg-qe2d.jpg', 50, 8, 2, 0),
(158, 27, '10s25soc022-4-jpg-gthi.jpg', 100, 1, 1, 0),
(159, 27, '10s25soc022-4-jpg-gthi.jpg', 50, 1, 2, 0),
(160, 28, '10s25soc006-black-1-jpg-oa1z.jpg', 50, 1, 1, 0),
(161, 28, '10s25soc006-black-1-jpg-oa1z.jpg', 50, 1, 2, 0),
(162, 28, '10s25soc006-white-1-jpg-beqq.jpg', 50, 8, 1, 0),
(163, 28, '10s25soc006-white-1-jpg-beqq.jpg', 50, 8, 2, 0),
(164, 29, '10s25soc009-white-1-jpg-z7bk.jpg', 50, 8, 1, 0),
(165, 29, '10s25soc009-white-1-jpg-z7bk.jpg', 50, 8, 2, 0),
(166, 29, '10s25soc009-black-1-jpg-qhim.jpg', 50, 1, 1, 0),
(167, 29, '10s25soc009-black-1-jpg-qhim.jpg', 50, 1, 2, 0),
(168, 30, '10s25cap012-beige-1-jpg-w4ht.jpg', 50, 8, 1, 0),
(169, 30, '10s25cap012-beige-1-jpg-w4ht.jpg', 50, 8, 2, 0),
(170, 30, '10s25cap012-green-1-jpg-vbj3.jpg', 50, 6, 1, 0),
(171, 30, '10s25cap012-green-1-jpg-vbj3.jpg', 50, 6, 2, 0),
(172, 30, '10s25cap012-navy-1-jpg-kdoz.jpg', 70, 12, 1, 0),
(173, 30, '10s25cap012-navy-1-jpg-kdoz.jpg', 50, 12, 2, 0),
(174, 31, '10s25cap003-oatmilk-1-jpg-qwv8.jpg', 50, 8, 1, 0),
(175, 31, '10s25cap003-oatmilk-1-jpg-qwv8.jpg', 50, 8, 2, 0),
(176, 31, '10s25cap003-ca-phe-sua-da-1-jpg-1sa5.jpg', 50, 2, 1, 0),
(177, 31, '10s25cap003-ca-phe-sua-da-1-jpg-1sa5.jpg', 50, 2, 2, 0),
(178, 31, '10s25cap003-americano-1-jpg-nrfv.jpg', 50, 1, 1, 0),
(179, 31, '10s25cap003-americano-1-jpg-nrfv.jpg', 50, 1, 2, 0),
(180, 32, '10s25cap001-non-luoi-trai-1-jpg-4zjw.jpg', 60, 1, 1, 0),
(181, 32, '10s25cap001-non-luoi-trai-1-jpg-4zjw.jpg', 50, 1, 2, 0),
(182, 33, '10f24soc028-beige-vo-dai-1-jpg-9mbo.jpg', 70, 2, 1, 0),
(183, 33, '10f24soc028-beige-vo-dai-1-jpg-9mbo.jpg', 70, 2, 2, 0),
(184, 33, '10f24soc028-green-vo-dai-1-jpg-dm24.jpg', 70, 6, 1, 0),
(185, 33, '10f24soc028-green-vo-dai-1-jpg-dm24.jpg', 70, 6, 2, 0),
(186, 34, '10s25bagu002-blue-1-jpg-ax9o.jpg', 70, 7, 2, 0),
(187, 34, '10s25bagu002-blue-1-jpg-ax9o.jpg', 70, 7, 3, 0),
(188, 35, '10s25bagu010-black-1-jpg-moot.jpg', 70, 1, 2, 0),
(189, 35, '10s25bagu010-black-1-jpg-moot.jpg', 70, 1, 3, 0),
(190, 35, '10s25bagu010-black-1-jpg-moot.jpg', 70, 1, 1, 0),
(191, 35, '10s25bagu010-blue-1-jpg-yx14.jpg', 70, 7, 1, 0),
(192, 35, '10s25bagu010-blue-1-jpg-yx14.jpg', 70, 7, 2, 0),
(193, 35, '10s25bagu010-blue-1-jpg-yx14.jpg', 70, 7, 3, 0),
(194, 36, '10f24bagu006-green-tui-tote-1-jpg.jpg', 70, 6, 1, 0),
(195, 36, '10f24bagu006-green-tui-tote-1-jpg.jpg', 70, 6, 2, 0),
(196, 36, '10f24bagu006-green-tui-tote-1-jpg.jpg', 70, 6, 3, 0);

-- --------------------------------------------------------

--
-- Table structure for table `rates`
--

CREATE TABLE `rates` (
  `rate_id` int(11) NOT NULL,
  `price_min` decimal(12,2) DEFAULT NULL,
  `price_max` decimal(12,2) DEFAULT NULL,
  `rate` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `is_deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `name`, `is_deleted`) VALUES
(1, 'Khách hàng', 0),
(2, 'Admin', 0),
(3, 'Quản lý', 0),
(4, 'Nhân viên', 0);

-- --------------------------------------------------------

--
-- Table structure for table `role_permission_details`
--

CREATE TABLE `role_permission_details` (
  `role_permission_detail_id` int(11) NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `permission_id` int(11) DEFAULT NULL,
  `action` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_permission_details`
--

INSERT INTO `role_permission_details` (`role_permission_detail_id`, `role_id`, `permission_id`, `action`) VALUES
(32, 2, 1, 'read'),
(33, 2, 1, 'write'),
(34, 2, 1, 'delete'),
(35, 2, 2, 'read'),
(36, 2, 2, 'write'),
(37, 2, 2, 'delete'),
(38, 2, 3, 'read'),
(39, 2, 3, 'write'),
(40, 2, 3, 'delete'),
(41, 2, 4, 'read'),
(42, 2, 4, 'write'),
(43, 2, 4, 'delete'),
(44, 2, 5, 'read'),
(45, 2, 5, 'write'),
(46, 2, 5, 'delete'),
(47, 2, 6, 'read'),
(48, 2, 6, 'write'),
(49, 2, 6, 'delete'),
(50, 2, 7, 'read'),
(51, 2, 7, 'write'),
(52, 2, 7, 'delete'),
(53, 2, 8, 'read'),
(54, 2, 8, 'write'),
(55, 2, 8, 'delete'),
(56, 2, 9, 'read'),
(57, 2, 9, 'write'),
(58, 2, 9, 'delete'),
(59, 2, 10, 'read'),
(60, 2, 10, 'write'),
(61, 2, 10, 'delete'),
(62, 3, 1, 'read'),
(63, 3, 1, 'write'),
(64, 3, 2, 'read'),
(65, 3, 2, 'write'),
(66, 3, 3, 'read'),
(67, 3, 3, 'write'),
(68, 3, 4, 'read'),
(69, 3, 4, 'write'),
(70, 3, 5, 'read'),
(71, 3, 7, 'read'),
(72, 3, 8, 'read'),
(73, 3, 8, 'write'),
(74, 3, 9, 'read'),
(75, 4, 1, 'read'),
(76, 4, 2, 'read'),
(77, 4, 2, 'write'),
(78, 4, 4, 'read'),
(79, 4, 7, 'read'),
(80, 4, 8, 'read');

-- --------------------------------------------------------

--
-- Table structure for table `sizes`
--

CREATE TABLE `sizes` (
  `size_id` int(11) NOT NULL,
  `name` varchar(10) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

--
-- Dumping data for table `sizes`
--

INSERT INTO `sizes` (`size_id`, `name`, `is_deleted`) VALUES
(1, 'S', 0),
(2, 'M', 0),
(3, 'L', 0),
(4, 'X', 0),
(5, 'XL', 0),
(6, '2XL', 0);

-- --------------------------------------------------------

--
-- Table structure for table `supplier`
--

CREATE TABLE `supplier` (
  `supplier_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `supplier`
--

INSERT INTO `supplier` (`supplier_id`, `name`, `email`, `address`, `is_deleted`) VALUES
(1, 'Công ty TNHH Minh Tâm', 'minhtam@supplier.vn', '123 Nguyễn Trãi, Q5, TP.HCM', 0),
(2, 'Công ty CP StyleMax', 'stylemax@supplier.vn', '45 Lê Lai, Q1, TP.HCM', 0),
(3, 'Công ty TNHH Gia Khang', 'giakhang@supplier.vn', '78 Cộng Hòa, Q.Tân Bình, TP.HCM', 0),
(4, 'Công ty TNHH Song Hành', 'songhanh@supplier.vn', '56 Hai Bà Trưng, Q1, TP.HCM', 0),
(5, 'Công ty TNHH UrbanLook', 'urbanlook@supplier.vn', '88 Trần Hưng Đạo, Q5, TP.HCM', 0),
(6, 'Công ty TNHH Nhật Quang', 'nhatquang@supplier.vn', '101 Trường Chinh, Q.Tân Phú, TP.HCM', 0),
(7, 'Công ty TNHH ElegantCo', 'elegantco@supplier.vn', '12 Phạm Văn Đồng, Q.Thủ Đức, TP.HCM', 0),
(8, 'Công ty CP Hoàng Gia', 'hoanggia@supplier.vn', '99 Nguyễn Thái Học, Q1, TP.HCM', 0),
(9, 'Công ty TNHH Tâm Đức', 'tamduc@supplier.vn', '67 Phan Văn Trị, Q.Bình Thạnh, TP.HCM', 0),
(10, 'Công ty TNHH SmartWear', 'smartwear@supplier.vn', '88 Nguyễn Văn Linh, Q.7, TP.HCM', 0),
(11, 'Công ty TNHH Nam Phong', 'namphong@supplier.vn', '145 Trần Quang Khải, Q1, TP.HCM', 0),
(12, 'Công ty TNHH Fashina', 'fashina@supplier.vn', '22 Nguyễn Văn Cừ, Q10, TP.HCM', 0),
(13, 'Công ty CP Đại Hưng', 'daihung@supplier.vn', '200 Điện Biên Phủ, Q.Bình Thạnh, TP.HCM', 0),
(14, 'Công ty TNHH GoldStyle', 'goldstyle@supplier.vn', '33 Cách Mạng Tháng 8, Q3, TP.HCM', 0),
(15, 'Công ty TNHH Tín Nghĩa', 'tinnghia@supplier.vn', '77 Nguyễn Văn Đậu, Q.Bình Thạnh, TP.HCM', 0),
(16, 'Công ty TNHH EverVibe', 'evervibe@supplier.vn', '144 Lý Chính Thắng, Q3, TP.HCM', 0),
(17, 'Công ty TNHH Bình Minh', 'binhminh@supplier.vn', '10 Nguyễn Kiệm, Q.Phú Nhuận, TP.HCM', 0),
(18, 'Công ty TNHH M&T Distributors', 'mt@supplier.vn', '55 Võ Thị Sáu, Q1, TP.HCM', 0),
(19, 'Công ty TNHH Alpha Zone', 'alphazone@supplier.vn', '17 Nguyễn Hữu Cảnh, Q.Bình Thạnh, TP.HCM', 0),
(20, 'Công ty CP Phúc Hưng', 'phuchung@supplier.vn', '29 Phạm Ngũ Lão, Q1, TP.HCM', 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL,
  `status` tinyint(4) DEFAULT 1,
  `otp` varchar(6) DEFAULT NULL,
  `otp_expired_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `password`, `email`, `phone`, `role_id`, `status`, `otp`, `otp_expired_at`) VALUES
(1, 'admin', '$2y$10$2GqI1uPXboKEgUDKeBR4Ded1JwDddOKgTHJQ.Ra9Cl6c8/mn4gFES', 'admin@example.com', '0900000000', 2, 1, NULL, NULL),
(2, 'khach1', '$2y$10$wJJ77ST9bygbyzEpo8vHj.srKA9qvrAnVLm7dhBlrm8jpzJ4JBfS6', 'khach1@example.com', '0900000001', 1, 1, NULL, NULL),
(3, 'Nguyễn Văn Sếp', '$2y$10$ESlOb1eUU2LtktlBpfQLqOPveoNbeS7OvHJSusZf1olspj328eKsS', 'staff1@example.com', '0900000002', 4, 1, NULL, NULL),
(4, 'Nguyễn Văn A', '$2y$10$MXMCxEikkuouwd4XgZiYmehyMlIzDGfBPomf7gx2atQ6e.cfdv/Aa', 'nguyenvana1@gmail.com', '0354956477', 1, 1, NULL, NULL),
(5, 'Nguyễn Văn B', '$2y$10$vTZl/mByC2AV1G9h3uPVUuh2nm6/Q0LzDpIcvG7vL6vcIsieqPxR2', 'nguyenvanb1@gmail.com', '0354956475', 1, 1, NULL, NULL),
(6, 'Nguyễn Văn C', '$2y$10$SXfl3sHD1VW/s2df4XVE7.TfFpxzKqb41boDcXhjf6kDlVEO2VNBW', 'nguyenvanc1@gmail.com', '0354956474', 1, 1, NULL, NULL),
(7, 'Nguyễn Văn D', '$2y$10$/cuND6Vf8VLDdxovkwgHxeXebcgtqhiPDbyDzNAX2l8wN0FNwhrMm', 'nguyenvand1@gmail.com', '0357879984', 1, 1, NULL, NULL),
(8, 'Nguyễn Văn E', '$2y$10$Nr4SGX5XoxMgLj5E1Z4GU.YyMAwCi4jrpqbCh3lrRtYX87KBXeV9u', 'nguyenvane1@gmail.com', '0357879986', 1, 1, NULL, NULL),
(9, 'Nguyễn Văn F', '$2y$10$boaQGtOnxhpmH1JZCmzUEenvP3dWldBdpbyLkPTVF2sPfK1VzsbFS', 'nguyenvanf1@gmail.com', '0354956479', 1, 1, NULL, NULL),
(10, 'Đỗ Nhân Viên', '$2y$10$yZaNk8h3m8qX15uPJ7D2EuWyIbXDVe0K82MwIoWOYjpdBXxiyCM/W', 'staff2@example.com', '0357674485', 4, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_addresses`
--

CREATE TABLE `user_addresses` (
  `address_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `address_detail` varchar(255) NOT NULL COMMENT 'Số nhà, tên đường…',
  `ward` varchar(100) NOT NULL COMMENT 'Phường / Xã',
  `district` varchar(100) NOT NULL COMMENT 'Quận / Huyện',
  `province` varchar(100) NOT NULL COMMENT 'Tỉnh / Thành phố',
  `is_default` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1 = mặc định',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_addresses`
--

INSERT INTO `user_addresses` (`address_id`, `user_id`, `address_detail`, `ward`, `district`, `province`, `is_default`, `created_at`, `updated_at`) VALUES
(1, 3, '273 An Dương Vương, Phường 2, Quận 5, Hồ Chí Minh', '27307', '774', '79', 1, '2025-05-17 08:22:00', '2025-05-17 08:22:00'),
(2, 10, '273 An Dương Vương, Phường 2, Quận 5, Hồ Chí Minh', '27307', '774', '79', 1, '2025-05-17 08:24:07', '2025-05-17 08:24:07');

-- --------------------------------------------------------

--
-- Table structure for table `vouchers`
--

CREATE TABLE `vouchers` (
  `voucher_id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `discount` decimal(10,2) NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vouchers`
--

INSERT INTO `vouchers` (`voucher_id`, `code`, `discount`, `start_date`, `end_date`, `status`) VALUES
(1, 'sagkuto', 4.00, '2025-05-01', '2025-05-22', 'active'),
(2, 'skt', 8.00, '2025-05-01', '2025-05-07', 'inactive'),
(5, 'vietnam', 10.00, '2025-05-01', '2025-05-30', 'active'),
(12, 'sgu', 5.00, '2025-05-01', '2025-05-23', 'active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bank_account`
--
ALTER TABLE `bank_account`
  ADD PRIMARY KEY (`account_id`),
  ADD UNIQUE KEY `ux_bank_account` (`bank_code`,`account_number`),
  ADD KEY `idx_bank_code` (`bank_code`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `cart_details`
--
ALTER TABLE `cart_details`
  ADD PRIMARY KEY (`cart_detail_id`),
  ADD KEY `cart_id` (`cart_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `variant_id` (`variant_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `colors`
--
ALTER TABLE `colors`
  ADD PRIMARY KEY (`color_id`);

--
-- Indexes for table `importreceipt`
--
ALTER TABLE `importreceipt`
  ADD PRIMARY KEY (`ImportReceipt_id`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `importreceipt_details`
--
ALTER TABLE `importreceipt_details`
  ADD PRIMARY KEY (`importreceipt_details_id`),
  ADD KEY `importreceipt_id` (`importreceipt_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `variant_id` (`variant_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `payment_method_id` (`payment_method_id`),
  ADD KEY `staff_id` (`staff_id`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`order_detail_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `variant_id` (`variant_id`);

--
-- Indexes for table `payment_method`
--
ALTER TABLE `payment_method`
  ADD PRIMARY KEY (`payment_method_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`permission_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD PRIMARY KEY (`variant_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `color_id` (`color_id`),
  ADD KEY `fk_size_id` (`size_id`);

--
-- Indexes for table `rates`
--
ALTER TABLE `rates`
  ADD PRIMARY KEY (`rate_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `role_permission_details`
--
ALTER TABLE `role_permission_details`
  ADD PRIMARY KEY (`role_permission_detail_id`),
  ADD KEY `role_id` (`role_id`),
  ADD KEY `permission_id` (`permission_id`);

--
-- Indexes for table `sizes`
--
ALTER TABLE `sizes`
  ADD PRIMARY KEY (`size_id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `supplier`
--
ALTER TABLE `supplier`
  ADD PRIMARY KEY (`supplier_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `role_id` (`role_id`);

--
-- Indexes for table `user_addresses`
--
ALTER TABLE `user_addresses`
  ADD PRIMARY KEY (`address_id`),
  ADD KEY `idx_user` (`user_id`);

--
-- Indexes for table `vouchers`
--
ALTER TABLE `vouchers`
  ADD PRIMARY KEY (`voucher_id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bank_account`
--
ALTER TABLE `bank_account`
  MODIFY `account_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `cart_details`
--
ALTER TABLE `cart_details`
  MODIFY `cart_detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `colors`
--
ALTER TABLE `colors`
  MODIFY `color_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `importreceipt`
--
ALTER TABLE `importreceipt`
  MODIFY `ImportReceipt_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `importreceipt_details`
--
ALTER TABLE `importreceipt_details`
  MODIFY `importreceipt_details_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=206;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `order_detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=151;

--
-- AUTO_INCREMENT for table `payment_method`
--
ALTER TABLE `payment_method`
  MODIFY `payment_method_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `permission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `product_variants`
--
ALTER TABLE `product_variants`
  MODIFY `variant_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=197;

--
-- AUTO_INCREMENT for table `rates`
--
ALTER TABLE `rates`
  MODIFY `rate_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `role_permission_details`
--
ALTER TABLE `role_permission_details`
  MODIFY `role_permission_detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT for table `sizes`
--
ALTER TABLE `sizes`
  MODIFY `size_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `supplier`
--
ALTER TABLE `supplier`
  MODIFY `supplier_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `user_addresses`
--
ALTER TABLE `user_addresses`
  MODIFY `address_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `vouchers`
--
ALTER TABLE `vouchers`
  MODIFY `voucher_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `cart_details`
--
ALTER TABLE `cart_details`
  ADD CONSTRAINT `cart_details_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `cart` (`cart_id`),
  ADD CONSTRAINT `cart_details_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`),
  ADD CONSTRAINT `cart_details_ibfk_3` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`variant_id`);

--
-- Constraints for table `importreceipt`
--
ALTER TABLE `importreceipt`
  ADD CONSTRAINT `importreceipt_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `supplier` (`supplier_id`),
  ADD CONSTRAINT `importreceipt_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `importreceipt_details`
--
ALTER TABLE `importreceipt_details`
  ADD CONSTRAINT `importreceipt_details_ibfk_1` FOREIGN KEY (`importreceipt_id`) REFERENCES `importreceipt` (`ImportReceipt_id`),
  ADD CONSTRAINT `importreceipt_details_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`),
  ADD CONSTRAINT `importreceipt_details_ibfk_3` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`variant_id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`payment_method_id`) REFERENCES `payment_method` (`payment_method_id`),
  ADD CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`staff_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `order_details_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`),
  ADD CONSTRAINT `order_details_ibfk_3` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`variant_id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`);

--
-- Constraints for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD CONSTRAINT `fk_size_id` FOREIGN KEY (`size_id`) REFERENCES `sizes` (`size_id`),
  ADD CONSTRAINT `product_variants_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`),
  ADD CONSTRAINT `product_variants_ibfk_2` FOREIGN KEY (`color_id`) REFERENCES `colors` (`color_id`);

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `role_permission_details`
--
ALTER TABLE `role_permission_details`
  ADD CONSTRAINT `role_permission_details_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`),
  ADD CONSTRAINT `role_permission_details_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`permission_id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`);

--
-- Constraints for table `user_addresses`
--
ALTER TABLE `user_addresses`
  ADD CONSTRAINT `fk_useraddr_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
