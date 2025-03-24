-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 22, 2025 at 12:40 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dbquanao`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `colors`
--

CREATE TABLE `colors` (
  `color_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `hex_code` varchar(7) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `colors`
--

INSERT INTO `colors` (`color_id`, `name`, `hex_code`) VALUES
(1, 'Đen', '#000000'),
(2, 'Trắng', '#FFFFFF'),
(3, 'Xám', '#808080'),
(4, 'Xám nhạt', '#D3D3D3'),
(5, 'Xanh navy', '#001F3F'),
(6, 'Xanh dương', '#0000FF'),
(7, 'Xanh dương nhạt', '#ADD8E6'),
(8, 'Xanh lá', '#008000'),
(9, 'Xanh rêu', '#556B2F'),
(10, 'Be', '#F5F5DC'),
(11, 'Nâu', '#8B4513'),
(12, 'Nâu đất', '#A0522D'),
(13, 'Đỏ', '#FF0000'),
(14, 'Đỏ đô', '#800000'),
(15, 'Cam', '#FFA500'),
(16, 'Vàng', '#FFFF00'),
(17, 'Vàng nhạt', '#FFFACD'),
(18, 'Hồng', '#FFC0CB'),
(19, 'Hồng pastel', '#FFD1DC'),
(20, 'Tím', '#800080'),
(21, 'Tím pastel', '#E6E6FA');

-- --------------------------------------------------------

--
-- Table structure for table `importreceipt`
--

CREATE TABLE `importreceipt` (
  `ImportReceipt_id` int(11) NOT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `importreceipt_details`
--

CREATE TABLE `importreceipt_details` (
  `ImportReceipt_details_id` int(11) NOT NULL,
  `ImportReceipt_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `rate_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `shipping_address` varchar(255) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `payment_method_id` int(11) DEFAULT NULL,
  `staff_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `order_detail_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `variant_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_method`
--

CREATE TABLE `payment_method` (
  `payment_method_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payment_method`
--

INSERT INTO `payment_method` (`payment_method_id`, `name`) VALUES
(1, 'Thanh toán khi nhận hàng (COD)'),
(2, 'Chuyển khoản ngân hàng'),
(3, 'Thanh toán qua ví Momo');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `permission_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`permission_id`, `name`) VALUES
(1, 'Quản lý sản phẩm'),
(2, 'Quản lý đơn hàng'),
(3, 'Quản lý người dùng'),
(4, 'Quản lý đơn nhập'),
(5, 'Xem báo cáo'),
(6, 'Quản lý quyền');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `rating_avg` float DEFAULT 0,
  `rating_count` int(11) DEFAULT 0,
  `sold_count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_variants`
--

CREATE TABLE `product_variants` (
  `variant_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `size` varchar(50) DEFAULT NULL,
  `stock` int(11) DEFAULT 0,
  `color_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rates`
--

CREATE TABLE `rates` (
  `rate_id` int(11) NOT NULL,
  `price_min` decimal(10,2) DEFAULT NULL,
  `price_max` decimal(10,2) DEFAULT NULL,
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
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `name`) VALUES
(1, 'user'),
(2, 'admin'),
(3, 'manager'),
(4, 'staff');

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
(1, 2, 1, 'read'),
(2, 2, 1, 'write'),
(3, 2, 1, 'delete'),
(4, 2, 2, 'read'),
(5, 2, 2, 'write'),
(6, 2, 2, 'delete'),
(7, 2, 3, 'read'),
(8, 2, 3, 'write'),
(9, 2, 3, 'delete'),
(10, 2, 4, 'read'),
(11, 2, 4, 'write'),
(12, 2, 4, 'delete'),
(13, 2, 5, 'read'),
(14, 2, 5, 'write'),
(15, 2, 5, 'delete'),
(16, 2, 6, 'read'),
(17, 2, 6, 'write'),
(18, 2, 6, 'delete'),
(19, 3, 1, 'read'),
(20, 3, 1, 'write'),
(21, 3, 2, 'read'),
(22, 3, 2, 'write'),
(23, 3, 3, 'read'),
(24, 3, 3, 'write'),
(25, 3, 4, 'read'),
(26, 3, 4, 'write'),
(27, 3, 5, 'read'),
(28, 4, 1, 'read'),
(29, 4, 2, 'read'),
(30, 4, 2, 'write'),
(31, 4, 4, 'read');

-- --------------------------------------------------------

--
-- Table structure for table `supplier`
--

CREATE TABLE `supplier` (
  `supplier_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `supplier`
--

INSERT INTO `supplier` (`supplier_id`, `name`, `email`, `address`) VALUES
(1, 'Công ty TNHH Minh Tâm', 'minhtam@supplier.vn', '123 Nguyễn Trãi, Q5, TP.HCM'),
(2, 'Công ty CP StyleMax', 'stylemax@supplier.vn', '45 Lê Lai, Q1, TP.HCM'),
(3, 'Công ty TNHH Gia Khang', 'giakhang@supplier.vn', '78 Cộng Hòa, Q.Tân Bình, TP.HCM'),
(4, 'Công ty TNHH Song Hành', 'songhanh@supplier.vn', '56 Hai Bà Trưng, Q1, TP.HCM'),
(5, 'Công ty TNHH UrbanLook', 'urbanlook@supplier.vn', '88 Trần Hưng Đạo, Q5, TP.HCM'),
(6, 'Công ty TNHH Nhật Quang', 'nhatquang@supplier.vn', '101 Trường Chinh, Q.Tân Phú, TP.HCM'),
(7, 'Công ty TNHH ElegantCo', 'elegantco@supplier.vn', '12 Phạm Văn Đồng, Q.Thủ Đức, TP.HCM'),
(8, 'Công ty CP Hoàng Gia', 'hoanggia@supplier.vn', '99 Nguyễn Thái Học, Q1, TP.HCM'),
(9, 'Công ty TNHH Tâm Đức', 'tamduc@supplier.vn', '67 Phan Văn Trị, Q.Bình Thạnh, TP.HCM'),
(10, 'Công ty TNHH SmartWear', 'smartwear@supplier.vn', '88 Nguyễn Văn Linh, Q.7, TP.HCM'),
(11, 'Công ty TNHH Nam Phong', 'namphong@supplier.vn', '145 Trần Quang Khải, Q1, TP.HCM'),
(12, 'Công ty TNHH Fashina', 'fashina@supplier.vn', '22 Nguyễn Văn Cừ, Q10, TP.HCM'),
(13, 'Công ty CP Đại Hưng', 'daihung@supplier.vn', '200 Điện Biên Phủ, Q.Bình Thạnh, TP.HCM'),
(14, 'Công ty TNHH GoldStyle', 'goldstyle@supplier.vn', '33 Cách Mạng Tháng 8, Q3, TP.HCM'),
(15, 'Công ty TNHH Tín Nghĩa', 'tinnghia@supplier.vn', '77 Nguyễn Văn Đậu, Q.Bình Thạnh, TP.HCM'),
(16, 'Công ty TNHH EverVibe', 'evervibe@supplier.vn', '144 Lý Chính Thắng, Q3, TP.HCM'),
(17, 'Công ty TNHH Bình Minh', 'binhminh@supplier.vn', '10 Nguyễn Kiệm, Q.Phú Nhuận, TP.HCM'),
(18, 'Công ty TNHH M&T Distributors', 'mt@supplier.vn', '55 Võ Thị Sáu, Q1, TP.HCM'),
(19, 'Công ty TNHH Alpha Zone', 'alphazone@supplier.vn', '17 Nguyễn Hữu Cảnh, Q.Bình Thạnh, TP.HCM'),
(20, 'Công ty CP Phúc Hưng', 'phuchung@supplier.vn', '29 Phạm Ngũ Lão, Q1, TP.HCM');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL,
  `status` tinyint(4) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `email`, `phone`, `address`, `role_id`, `status`) VALUES
(1, 'admin', '$2b$12$ThfEhiblJgeQd614ORjnd.GAot9.o7oCywkvfS/mbwn3JXvYIRO0u', 'admin@shop.vn', '0909000000', 'Trụ sở chính', 2, 1),
(2, 'khach1', '$2b$12$f3kbUTglXNsNesC0.l45zuNIQo/4a4mjAtDk71VjF7K3PvSz4FnwG', 'khach1@gmail.com', '0942621045', '2 Nguyễn Trãi, Q5, TP.HCM', 1, 1),
(3, 'khach2', '$2b$12$rbrlA84KFqPqQX1TQ/J7YOoPGcy002KhMgjedMYTg28P.gU0nCyGm', 'khach2@gmail.com', '0944623751', '3 Lý Thường Kiệt, Q10, TP.HCM', 1, 1),
(4, 'khach3', '$2b$12$IDjtDeTwzCx6U1bSXjA/TunHKOfllgggntL0CtKM8vI8B2J3kB476', 'khach3@gmail.com', '0982312485', '2 Nguyễn Trãi, Q5, TP.HCM', 1, 1),
(5, 'khach4', '$2b$12$Vw0CgkpT6FCIvTA74bdEJumJUuvxPqfZZt0fGEhyE07GX0CYJhUP6', 'khach4@gmail.com', '0917953107', '4 Trần Hưng Đạo, Q5, TP.HCM', 1, 1),
(6, 'khach5', '$2b$12$QW8lXltoejdT/Aa3sPZMvO7y5I6KssyIfU/2tGVSZy4Hp8wI6IWJ.', 'khach5@gmail.com', '0976872107', '2 Nguyễn Trãi, Q5, TP.HCM', 1, 1),
(7, 'khach6', '$2b$12$7glrzLPggQWEjVm0mEvkUufQPqYm10oWQc.OUL.pfNFo12PfX4twG', 'khach6@gmail.com', '0917709849', '3 Lý Thường Kiệt, Q10, TP.HCM', 1, 1),
(8, 'khach7', '$2b$12$i80EkuB7cek/IluladRzaebCXtQCzNDLyTBScirT0q7zA/FzxqBry', 'khach7@gmail.com', '0953694093', '4 Trần Hưng Đạo, Q5, TP.HCM', 1, 1),
(9, 'khach8', '$2b$12$jDvLNnDgxofA3CGjq0lZa.FaGyMDc0MthmRJL0kndqaR3ABziMoTq', 'khach8@gmail.com', '0942430465', '6 Nguyễn Thị Minh Khai, Q1, TP.HCM', 1, 1),
(10, 'khach9', '$2b$12$oX2A.kXkEMqWska5Q.AkR.YnuBDq7VB3h7Pb3HV0gcZq4ENFeQb1a', 'khach9@gmail.com', '0934115454', '4 Trần Hưng Đạo, Q5, TP.HCM', 1, 1),
(11, 'khach10', '$2b$12$KhoWUNYUN94wKlraDyXeSOVh/Glw01.Oo84l/cRZO0ffnv1I7KwiC', 'khach10@gmail.com', '0966748532', '3 Lý Thường Kiệt, Q10, TP.HCM', 1, 1),
(12, 'khach11', '$2b$12$LpTsnAOQMWpZd4QPZJraRuGjW8hJfgjifK5pMsvYdzUJIjAyeCQVu', 'khach11@gmail.com', '0961827225', '3 Lý Thường Kiệt, Q10, TP.HCM', 1, 1),
(13, 'khach12', '$2b$12$6k3FABZfuaDaGrl975tmQ.VE0VQh4sYp5LeT8AGkS9XJ3oP4w3q0q', 'khach12@gmail.com', '0976580428', '3 Lý Thường Kiệt, Q10, TP.HCM', 1, 1),
(14, 'khach13', '$2b$12$VBkvM1izwKPWzK/Y6B7AOuQ3NWdoP/epOA9DdXQv2mQATAgWAwpkq', 'khach13@gmail.com', '0932543130', '2 Nguyễn Trãi, Q5, TP.HCM', 1, 1),
(15, 'khach14', '$2b$12$ictOn6Jpyh.mksQHjTkQAuMQ/yTycxC9y.NxowojlzrsP11FqJ.4C', 'khach14@gmail.com', '0950046185', '3 Lý Thường Kiệt, Q10, TP.HCM', 1, 1),
(16, 'khach15', '$2b$12$Ar09ZtGSymrwQjNGQNuDF./YQ88IDTD5uRJRdgkPkU7RRrhZu74ri', 'khach15@gmail.com', '0973301187', '1 Lê Lợi, Q1, TP.HCM', 1, 1),
(17, 'khach16', '$2b$12$twN7W09qMUmyYYmQ/aT6I.Pt/oi.5EQrGh6fJeDII4FN/LWjr5.sq', 'khach16@gmail.com', '0989737539', '4 Trần Hưng Đạo, Q5, TP.HCM', 1, 1),
(18, 'khach17', '$2b$12$9b4qANFQCk0hujCXKfWb0.85ZMQc5x5taSSjtxKmWqdHRYfWjWwzG', 'khach17@gmail.com', '0917618081', '6 Nguyễn Thị Minh Khai, Q1, TP.HCM', 1, 1),
(19, 'khach18', '$2b$12$jgkupVTGukFnDm9fhOy4Pevxfl4IDz7GV2OFUX.AujgjPkuxap8NO', 'khach18@gmail.com', '0992498011', '3 Lý Thường Kiệt, Q10, TP.HCM', 1, 1),
(20, 'khach19', '$2b$12$380MZEHcnfBuSZTsutTrh.4qDTuN4rcKVe90hynbflWgrFWfsDP1q', 'khach19@gmail.com', '0945060040', '6 Nguyễn Thị Minh Khai, Q1, TP.HCM', 1, 1),
(21, 'khach20', '$2b$12$i7VcwcG/zzAR1.xRZSrbXuB5cAH7SlkwZyqNr3Mnt8x8CWHHuJhAq', 'khach20@gmail.com', '0979292556', '6 Nguyễn Thị Minh Khai, Q1, TP.HCM', 1, 1),
(22, 'khach21', '$2b$12$NWpNt5Ye3Xb1.8V6kH9wxOOVtVyCwIWg5p4/dspHru8nCFL6fQXvW', 'khach21@gmail.com', '0979223791', '4 Trần Hưng Đạo, Q5, TP.HCM', 1, 1),
(23, 'khach22', '$2b$12$4/6g66hUvJP7EUr6CRWxBOnAGIbZcLTGkiQ3VNpmMEwJT3cUnsO4G', 'khach22@gmail.com', '0913708252', '4 Trần Hưng Đạo, Q5, TP.HCM', 1, 1),
(24, 'khach23', '$2b$12$A0FkDc1t3TvU6R93sBVmF.kTkh0sy1J/cpbW60rH26rTrs2OhHc2m', 'khach23@gmail.com', '0952048540', '5 Điện Biên Phủ, Q3, TP.HCM', 1, 1),
(25, 'khach24', '$2b$12$NwW.3cGTiNEAM3NmIyeXeevhq6S0/b7UfxImP47WG9I46CEfQloKO', 'khach24@gmail.com', '0960574477', '1 Lê Lợi, Q1, TP.HCM', 1, 1),
(26, 'khach25', '$2b$12$vRGJcUQ4puGamXwRzv81MuYKoGUqh/TrTuPwyVGHgsbX2RIq961e.', 'khach25@gmail.com', '0992689911', '4 Trần Hưng Đạo, Q5, TP.HCM', 1, 1),
(27, 'khach26', '$2b$12$P3GRREYdE3UX2dOKGzryeO1fxOcm7FZ/0E8CCFQPd1KI481u1QJty', 'khach26@gmail.com', '0990398010', '2 Nguyễn Trãi, Q5, TP.HCM', 1, 1),
(28, 'khach27', '$2b$12$pokbRgAjjc/5FvST8IApsOoAi1JYbyLG6Z6mb3Awcmi4vCO2xm2Mm', 'khach27@gmail.com', '0933960752', '1 Lê Lợi, Q1, TP.HCM', 1, 1),
(29, 'khach28', '$2b$12$r7eeiWkC6m83So7xvNAdr.MLFELSAfTQF.tICLhnWoxCcKOW724N2', 'khach28@gmail.com', '0997361400', '2 Nguyễn Trãi, Q5, TP.HCM', 1, 1),
(30, 'khach29', '$2b$12$sJ8FL0fbdL0hP7mJlvF1seb5gPythodPKFIh18uPPI8vjsO3HoMNq', 'khach29@gmail.com', '0921586114', '4 Trần Hưng Đạo, Q5, TP.HCM', 1, 1),
(31, 'khach30', '$2b$12$WVnq5DUBeEHhgJMZohMRO.zZddnt.eJXBvo/9MVzZZ/lSjpo8iI0e', 'khach30@gmail.com', '0959712888', '2 Nguyễn Trãi, Q5, TP.HCM', 1, 1),
(32, 'khach31', '$2b$12$i2mqmd1cgPPNyJnc1uzmOeKeicEB72iYbzVbN8b.ePdfrpukJynNa', 'khach31@gmail.com', '0960364148', '2 Nguyễn Trãi, Q5, TP.HCM', 1, 1),
(33, 'khach32', '$2b$12$xZz/.FIPOBZ6vDT.RatG2OQzyDxifTMO90R9tV0xdofXGj00O10ky', 'khach32@gmail.com', '0924796831', '6 Nguyễn Thị Minh Khai, Q1, TP.HCM', 1, 1),
(34, 'khach33', '$2b$12$zzaecOscqOOUh8/GUs6/7.lg8YYyehZA7c2zyzTZaAl8ENINDxUDC', 'khach33@gmail.com', '0934911077', '1 Lê Lợi, Q1, TP.HCM', 1, 1),
(35, 'khach34', '$2b$12$3/gjbkKKD5A3VtKU104CU.xUX.ZTxrSx2aaGxBLSNmyR51v/dLHSy', 'khach34@gmail.com', '0936579122', '6 Nguyễn Thị Minh Khai, Q1, TP.HCM', 1, 1),
(36, 'khach35', '$2b$12$gsvbX54P4ZtxRsnXnNJ6nOQbVkn/TKORtYwUWk0ah487O2205IdlG', 'khach35@gmail.com', '0963498627', '6 Nguyễn Thị Minh Khai, Q1, TP.HCM', 1, 1),
(37, 'khach36', '$2b$12$zllMu62P/ZdF1.rw2BJCiOXmcI8zFwXyGP7seQS2c54FaI0xwqLK.', 'khach36@gmail.com', '0981498476', '2 Nguyễn Trãi, Q5, TP.HCM', 1, 1),
(38, 'khach37', '$2b$12$A68NgUz3601RoYR9VsOY/OcTmC67lzEorwbnWj1/H6X6qiT6hR6cW', 'khach37@gmail.com', '0982795079', '5 Điện Biên Phủ, Q3, TP.HCM', 1, 1),
(39, 'khach38', '$2b$12$PVLkeYZuq7AEVsLSWGrRnuPQe0Le1CXxu6oMutk23nDPufy9EcUSS', 'khach38@gmail.com', '0927204630', '4 Trần Hưng Đạo, Q5, TP.HCM', 1, 1),
(40, 'khach39', '$2b$12$erXEkFcgCI7dJxA/1zxTCOTQ3LLUiT9nZnybCAVp86wRH5r7CmUxy', 'khach39@gmail.com', '0943205787', '5 Điện Biên Phủ, Q3, TP.HCM', 1, 1),
(41, 'khach40', '$2b$12$6x4ogHdcRukhbCrbC7sa6.T.CfOmvQipTBA6aYMZ/jrVtSzTN8G1u', 'khach40@gmail.com', '0976935807', '2 Nguyễn Trãi, Q5, TP.HCM', 1, 1),
(42, 'manager1', '$2b$12$oMymjZwdbmIfpqFWIcdZ/e9OmwMOXq5k./CQeEtVRYCZS4ZtkqLTa', 'manager1@shop.vn', '0917456942', '10 Nguyễn Huệ, Q1, TP.HCM', 3, 1),
(43, 'manager2', '$2b$12$kLokKohl.dKJJYOmR9yWgO03FX87teNhq1XATiohDQDzm1axNymUK', 'manager2@shop.vn', '0913642183', '10 Nguyễn Huệ, Q1, TP.HCM', 3, 1),
(44, 'manager3', '$2b$12$GnH1Kmsa/eLFpBFiP3a57.arpaFVcABAOlAudat8lO.KAQGCrYMZm', 'manager3@shop.vn', '0919575310', '10 Nguyễn Huệ, Q1, TP.HCM', 3, 1),
(45, 'manager4', '$2b$12$e8mN2eFVb5gUvi8cGQWMc.aMuA7C5KhPj7Y4.cH6smk7s.APoTHxu', 'manager4@shop.vn', '0914907092', '10 Nguyễn Huệ, Q1, TP.HCM', 3, 1),
(46, 'manager5', '$2b$12$JQcg4MM6B7YdrkJfGZC22uOiV3scxaBvERY0PNJ/ovGG06mjL50ge', 'manager5@shop.vn', '0914411559', '20 Lý Thường Kiệt, Q10, TP.HCM', 3, 1),
(47, 'staff1', '$2b$12$8Ng/4YfhLo3.CgdH692tKuzVBzRb5gel4xsdx8P8jyS/z2cBd/7H2', 'staff1@shop.vn', '0924828108', '10 Nguyễn Huệ, Q1, TP.HCM', 4, 1),
(48, 'staff2', '$2b$12$8eYguzNcVc6xl48zRvOIe.zkwqjhkjP7DpG4x/nUTThZpTPni8ZTW', 'staff2@shop.vn', '0929139872', '20 Lý Thường Kiệt, Q10, TP.HCM', 4, 1),
(49, 'staff3', '$2b$12$Hj/6Ou/DCM9qBLI/9RJ9luRDPzt8cRn2vNxfTcAFYyI4wyW9qr/CG', 'staff3@shop.vn', '0921094254', '20 Lý Thường Kiệt, Q10, TP.HCM', 4, 1),
(50, 'staff4', '$2b$12$YiiNnyHdxcy7w5wOjCZ59.uKLzQAfeH1phCieEie4AoNB8ZR2AUtS', 'staff4@shop.vn', '0926653504', '30 Cách Mạng Tháng 8, Q3, TP.HCM', 4, 1),
(51, 'staff5', '$2b$12$9M8YAEx9vFP7acIuNG3.pu3VUX5NtFHUrDDA7Ifx5t88Tx3GCE22.', 'staff5@shop.vn', '0924230965', '30 Cách Mạng Tháng 8, Q3, TP.HCM', 4, 1),
(52, 'staff6', '$2b$12$RnQo2P7bzVoXqEvBf4VPCuJBcPpfE7KxEPH533jSFRcyTL9qvATG2', 'staff6@shop.vn', '0923421304', '20 Lý Thường Kiệt, Q10, TP.HCM', 4, 1),
(53, 'staff7', '$2b$12$pqXcwmcYbasDRBqVCwJaOu9CuG2JBAj7wESLZgGe8sXbQ/0xpk0rq', 'staff7@shop.vn', '0926357369', '30 Cách Mạng Tháng 8, Q3, TP.HCM', 4, 1),
(54, 'staff8', '$2b$12$x838rX7BRRE8FGNy6Wp1PeVpR.cDovdXXZQjh4hERmydRx9h7b1nC', 'staff8@shop.vn', '0928194675', '30 Cách Mạng Tháng 8, Q3, TP.HCM', 4, 1),
(55, 'staff9', '$2b$12$9fCHo7Curau5sTVM1pLOSemKXvCOYh60mbEW3jOnH9LnYvbC1LnuS', 'staff9@shop.vn', '0929323035', '20 Lý Thường Kiệt, Q10, TP.HCM', 4, 1),
(56, 'staff10', '$2b$12$.H/rpfT5ZRB.L2IIvFnog.9qnxAqp9kZ8tjg.VtX4IqYOQ4eJMDhK', 'staff10@shop.vn', '0929275807', '30 Cách Mạng Tháng 8, Q3, TP.HCM', 4, 1),
(57, 'staff11', '$2b$12$Grr4KriTbddIvoRCkO/vzOsCIFCIaYh1g86vQai1PxxohhUszRbpK', 'staff11@shop.vn', '0926475084', '10 Nguyễn Huệ, Q1, TP.HCM', 4, 1),
(58, 'staff12', '$2b$12$R6msl5/lMnQu4TLaEwRMhOFJEpdH/NT.hW2j16sxZgQPPXZbW9ASi', 'staff12@shop.vn', '0926871569', '20 Lý Thường Kiệt, Q10, TP.HCM', 4, 1);

--
-- Indexes for dumped tables
--

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
  ADD PRIMARY KEY (`ImportReceipt_details_id`),
  ADD KEY `ImportReceipt_id` (`ImportReceipt_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `rate_id` (`rate_id`);

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
  ADD KEY `color_id` (`color_id`);

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
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cart_details`
--
ALTER TABLE `cart_details`
  MODIFY `cart_detail_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `colors`
--
ALTER TABLE `colors`
  MODIFY `color_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `importreceipt`
--
ALTER TABLE `importreceipt`
  MODIFY `ImportReceipt_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `importreceipt_details`
--
ALTER TABLE `importreceipt_details`
  MODIFY `ImportReceipt_details_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `order_detail_id` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_variants`
--
ALTER TABLE `product_variants`
  MODIFY `variant_id` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `role_permission_detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `supplier`
--
ALTER TABLE `supplier`
  MODIFY `supplier_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

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
  ADD CONSTRAINT `importreceipt_details_ibfk_1` FOREIGN KEY (`ImportReceipt_id`) REFERENCES `importreceipt` (`ImportReceipt_id`),
  ADD CONSTRAINT `importreceipt_details_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`),
  ADD CONSTRAINT `importreceipt_details_ibfk_3` FOREIGN KEY (`rate_id`) REFERENCES `rates` (`rate_id`);

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
