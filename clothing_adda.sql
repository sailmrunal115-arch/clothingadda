-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 24, 2026 at 08:25 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `clothing_adda`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `reset_token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `reset_token`) VALUES
(1, 'admin', '$2y$10$bHRh2B/wHNMYVIoWqGtE3uY7pDdaVdrtMmDSfIDcz0stguDNFO8X2', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `quantity`, `created_at`) VALUES
(31, 3, 15, 1, '2026-02-20 10:39:46');

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

CREATE TABLE `coupons` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `discount_type` enum('percent','fixed') NOT NULL,
  `discount_value` decimal(10,2) NOT NULL,
  `expiry_date` date DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `user_name` varchar(100) NOT NULL,
  `user_email` varchar(100) NOT NULL,
  `products` text NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','shipped','delivered','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_method` varchar(20) NOT NULL DEFAULT 'COD',
  `payment_status` varchar(20) DEFAULT 'pending',
  `coupon_code` varchar(50) DEFAULT NULL,
  `discount_amount` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `product_id`, `quantity`, `user_name`, `user_email`, `products`, `total_price`, `status`, `created_at`, `payment_method`, `payment_status`, `coupon_code`, `discount_amount`) VALUES
(1, 4, 0, 1, 'mrunal', 'lofivbs@gmail.com', 'body con (Qty: 1), ', 2599.00, 'cancelled', '2026-02-23 10:29:57', 'COD', 'pending', NULL, 0.00),
(2, 4, 0, 1, 'mrunal', 'lofivbs@gmail.com', 'KURTA (Qty: 1), ', 349.00, 'cancelled', '2026-02-23 10:33:21', 'COD', 'pending', NULL, 0.00),
(3, 4, 0, 1, 'mrunal', 'lofivbs@gmail.com', 'KURTA (Qty: 1), ', 349.00, 'pending', '2026-02-23 10:48:13', 'COD', 'pending', NULL, 0.00),
(4, 2, 0, 1, 'mrunal', 'sailmrunal115@gmail.com', 'DRESS (Qty: 5), KURTA (Qty: 2), ', 8193.00, '', '2026-02-26 08:49:36', 'COD', 'pending', NULL, 0.00),
(6, 2, 0, 1, 'mrunal', 'sailmrunal115@gmail.com', 'DRESS (Qty: 1), ', 1499.00, 'pending', '2026-02-26 12:09:14', 'COD', 'pending', NULL, 0.00),
(7, 2, 0, 1, 'mrunal', 'sailmrunal115@gmail.com', 'KURTA (Qty: 1), ', 349.00, 'pending', '2026-02-26 12:09:29', 'COD', 'pending', NULL, 0.00),
(8, 2, 0, 1, 'mrunal', 'sailmrunal115@gmail.com', 'KURTA (Qty: 1), ', 349.00, 'pending', '2026-02-26 12:09:57', 'COD', 'pending', NULL, 0.00),
(9, 2, 0, 1, 'mrunal', 'sailmrunal115@gmail.com', 'DRESS (Qty: 2), ', 2998.00, 'pending', '2026-02-26 12:22:34', 'COD', 'pending', NULL, 0.00),
(10, 2, 0, 1, 'mrunal', 'sailmrunal115@gmail.com', 'COAT (Qty: 1), ', 769.00, 'pending', '2026-02-26 12:37:44', 'COD', 'pending', NULL, 0.00),
(11, 2, 0, 1, 'mrunal', 'sailmrunal115@gmail.com', 'COAT (Qty: 1), ', 769.00, 'pending', '2026-02-26 12:37:55', 'COD', 'pending', NULL, 0.00),
(12, 2, 0, 1, 'mrunal', 'sailmrunal115@gmail.com', 'DRESS (Qty: 1), ', 1499.00, 'pending', '2026-02-26 12:46:04', 'COD', 'pending', NULL, 0.00),
(13, 2, 0, 1, 'mrunal', 'sailmrunal115@gmail.com', 'KURTA (Qty: 1), ', 349.00, 'pending', '2026-02-26 12:47:03', 'COD', 'pending', NULL, 0.00),
(14, 2, 0, 1, 'mrunal', 'sailmrunal115@gmail.com', 'DRESS (Qty: 1), ', 1499.00, 'pending', '2026-02-26 12:50:58', 'Online', 'pending', NULL, 0.00),
(15, 2, 0, 1, 'mrunal', 'sailmrunal115@gmail.com', 'DRESS (Qty: 1), ', 1499.00, 'pending', '2026-02-26 12:54:27', 'COD', 'pending', NULL, 0.00),
(16, 2, 0, 1, 'mrunal', 'sailmrunal115@gmail.com', 'DRESS (Qty: 1), ', 1499.00, 'pending', '2026-02-26 12:55:27', 'COD', 'pending', NULL, 0.00),
(17, 5, 0, 1, 'mrunal', 'mrunal115@gmail.com', 'DRESS (Qty: 1), ', 1499.00, 'cancelled', '2026-03-11 10:19:30', 'COD', 'pending', NULL, 0.00),
(18, 5, 0, 1, 'mrunal', 'mrunal115@gmail.com', 'KURTA (Qty: 1), ', 349.00, 'cancelled', '2026-03-11 10:22:17', 'COD', 'pending', NULL, 0.00),
(19, 5, 0, 1, 'mrunal', 'mrunal115@gmail.com', 'DRESS (Qty: 1), ', 1499.00, 'pending', '2026-03-11 10:26:45', 'COD', 'pending', NULL, 0.00),
(20, 5, 0, 1, 'mrunal', 'mrunal115@gmail.com', 'COAT (Qty: 1), ', 769.00, 'pending', '2026-03-11 10:29:02', 'Online', 'pending', NULL, 0.00),
(21, 5, 0, 1, 'mrunal', 'mrunal115@gmail.com', 'DRESS (Qty: 1), ', 1499.00, 'pending', '2026-03-11 10:32:18', 'Online', 'pending', NULL, 0.00),
(22, 5, 0, 1, 'mrunal', 'mrunal115@gmail.com', 'DRESS (Qty: 1), ', 1499.00, 'pending', '2026-03-11 10:41:18', 'COD', 'pending', NULL, 0.00),
(23, 5, 0, 1, 'mrunal', 'mrunal115@gmail.com', 'DRESS (Qty: 1), ', 1499.00, 'pending', '2026-03-11 10:43:34', 'COD', 'pending', NULL, 0.00),
(24, 5, 0, 1, 'mrunal', 'mrunal115@gmail.com', 'KURTA (Qty: 1), ', 349.00, 'pending', '2026-03-11 10:46:22', 'COD', 'pending', NULL, 0.00),
(25, 5, 0, 1, 'mrunal', 'mrunal115@gmail.com', 'KURTA (Qty: 1), ', 349.00, 'pending', '2026-03-11 10:48:01', 'COD', 'pending', NULL, 0.00),
(26, 5, 0, 1, 'mrunal', 'mrunal115@gmail.com', 'KURTA (Qty: 1), ', 349.00, 'pending', '2026-03-11 10:50:54', 'COD', 'pending', NULL, 0.00),
(27, 5, 0, 1, 'mrunal', 'mrunal115@gmail.com', 'DRESS (Qty: 1), ', 1499.00, 'pending', '2026-03-11 10:53:35', 'COD', 'paid', NULL, 0.00),
(28, 5, 0, 1, 'mrunal', 'mrunal115@gmail.com', 'KURTA (Qty: 1), ', 349.00, 'pending', '2026-03-11 11:02:51', 'COD', 'paid', NULL, 0.00),
(29, 5, 0, 1, 'mrunal', 'mrunal115@gmail.com', 'DRESS (Qty: 1), ', 1499.00, 'cancelled', '2026-03-11 11:09:10', 'COD', 'paid', NULL, 0.00),
(30, 5, 0, 1, 'mrunal', 'mrunal115@gmail.com', 'KURTA (Qty: 1), ', 349.00, 'cancelled', '2026-03-11 11:14:46', 'COD', 'paid', NULL, 0.00),
(31, 5, 0, 1, 'mrunal', 'mrunal115@gmail.com', 'KURTA (Qty: 1), ', 349.00, 'cancelled', '2026-03-11 11:15:45', 'Online', 'paid', NULL, 0.00),
(32, 5, 0, 1, 'mrunal', 'mrunal115@gmail.com', 'KURTA (Qty: 1), ', 349.00, 'cancelled', '2026-03-11 11:16:42', 'Online', 'paid', NULL, 0.00),
(33, 5, 0, 1, 'mrunal', 'mrunal115@gmail.com', 'KURTA (Qty: 1), ', 349.00, 'cancelled', '2026-03-11 11:17:10', 'Online', 'paid', NULL, 0.00),
(34, 5, 0, 1, 'mrunal', 'mrunal115@gmail.com', 'KURTA (Qty: 1), ', 349.00, 'cancelled', '2026-03-11 11:17:51', 'Online', 'paid', NULL, 0.00),
(35, 5, 0, 1, 'mrunal', 'mrunal115@gmail.com', 'KURTA (Qty: 1), ', 349.00, 'delivered', '2026-03-11 14:38:04', 'Online', 'paid', NULL, 0.00),
(36, 5, 0, 1, 'mrunal', 'mrunal115@gmail.com', 'KURTA (Qty: 2), ', 698.00, 'cancelled', '2026-03-13 09:02:33', 'Online', 'paid', NULL, 0.00),
(37, 5, 0, 1, 'mrunal', 'mrunal115@gmail.com', 'body con (Qty: 1), ', 2599.00, 'cancelled', '2026-03-14 10:01:49', 'COD', 'paid', NULL, 0.00),
(38, 5, 0, 1, 'mrunal', 'mrunal115@gmail.com', 'KURTA (Qty: 1), ', 349.00, 'cancelled', '2026-03-14 10:01:58', 'Online', 'paid', NULL, 0.00),
(39, 5, 0, 1, 'mrunal', 'mrunal115@gmail.com', 'KURTA (Qty: 1), DRESS (Qty: 1), ', 1848.00, 'delivered', '2026-03-15 13:39:03', 'Online', 'paid', NULL, 0.00),
(40, 5, 0, 1, 'mrunal', 'mrunal115@gmail.com', 'body con (Qty: 1), ', 2599.00, 'delivered', '2026-03-15 17:01:02', 'COD', 'paid', NULL, 0.00),
(41, 5, 0, 1, 'mrunal', 'mrunal115@gmail.com', 'BLOUSE (Qty: 1), ', 599.00, 'delivered', '2026-03-15 20:19:44', 'COD', 'paid', NULL, 0.00),
(42, 5, 0, 1, 'mrunal', 'mrunal115@gmail.com', 'TROUSER (Qty: 1), ', 799.00, 'delivered', '2026-03-16 09:48:34', 'COD', 'paid', NULL, 0.00),
(43, 5, 0, 1, 'mrunal', 'mrunal115@gmail.com', 'BLOUSE (Qty: 1), ', 599.00, 'pending', '2026-03-16 10:36:26', 'Online', 'pending', NULL, 0.00),
(44, 5, 0, 1, 'mrunal', 'mrunal115@gmail.com', 'TROUSER (Qty: 1), ', 799.00, 'cancelled', '2026-03-16 10:47:37', 'Online', 'pending', '', 0.00),
(45, 5, 0, 1, 'mrunal', 'mrunal115@gmail.com', 'TROUSER (Qty: 1), ', 799.00, 'delivered', '2026-03-16 10:48:06', 'COD', 'paid', '', 0.00),
(46, 5, 0, 1, 'mrunal', 'mrunal115@gmail.com', 'FORMAL SHIRT (Qty: 1), ', 699.00, 'delivered', '2026-03-16 10:54:22', 'COD', 'paid', NULL, 0.00),
(47, 5, 0, 1, 'mrunal', 'mrunal115@gmail.com', 'TROUSER (Qty: 1), ', 799.00, 'delivered', '2026-03-21 06:52:13', 'COD', 'paid', NULL, 0.00),
(48, 5, 0, 1, 'mrunal', 'mrunal115@gmail.com', 'BLOUSE (Qty: 1), ', 599.00, 'delivered', '2026-03-21 06:52:26', 'Online', 'paid', NULL, 0.00),
(49, 5, 0, 1, 'mrunal', 'mrunal115@gmail.com', 'TROUSER (Qty: 1), ', 799.00, 'delivered', '2026-03-21 06:55:33', 'Online', 'paid', NULL, 0.00),
(51, 5, 0, 1, 'mrunal', 'mrunal115@gmail.com', 'FORMAL SHIRT (Qty: 1), ', 699.00, 'delivered', '2026-03-21 15:19:00', 'COD', 'paid', NULL, 0.00),
(52, 5, 0, 1, 'mrunal', 'mrunal115@gmail.com', 'BLOUSE (Qty: 1), ', 599.00, 'cancelled', '2026-03-21 18:12:07', 'Online', 'pending', NULL, 0.00),
(53, 5, 0, 1, 'mrunal', 'mrunal115@gmail.com', 'TROUSER (Qty: 1), ', 799.00, 'cancelled', '2026-03-21 18:21:13', 'Online', 'pending', NULL, 0.00),
(54, 5, 0, 1, 'mrunal', 'mrunal115@gmail.com', 'BLOUSE (Qty: 1), ', 599.00, 'cancelled', '2026-03-21 18:24:13', 'Online', 'pending', NULL, 0.00),
(55, 5, 0, 1, 'mrunal', 'mrunal115@gmail.com', 'BLOUSE (Qty: 1), ', 599.00, 'cancelled', '2026-03-21 18:27:05', 'Online', 'paid', NULL, 0.00),
(56, 5, 0, 1, 'mrunal', 'mrunal115@gmail.com', 'FORMAL SHIRT (Qty: 1), ', 699.00, 'delivered', '2026-03-21 18:32:26', 'Online', 'pending', NULL, 0.00),
(57, 5, 0, 1, 'mrunal', 'mrunal115@gmail.com', 'TANK TOP (Qty: 1), ', 499.00, 'delivered', '2026-03-21 18:38:11', 'COD', 'paid', NULL, 0.00),
(58, 5, 0, 1, 'mrunal', 'mrunal115@gmail.com', 'FORMAL SHIRT (Qty: 1), ', 699.00, 'delivered', '2026-03-21 18:38:17', 'Online', 'paid', NULL, 0.00),
(59, 5, 0, 1, 'mrunal', 'mrunal115@gmail.com', 'TROUSER (Qty: 1), ', 799.00, 'cancelled', '2026-03-21 18:39:08', 'Online', 'paid', NULL, 0.00),
(60, 5, 0, 1, 'mrunal', 'mrunal115@gmail.com', 'COAT (Qty: 1), ', 769.00, 'delivered', '2026-03-21 18:48:23', 'Online', 'paid', NULL, 0.00),
(61, 5, 0, 1, 'mrunal', 'mrunal115@gmail.com', 'FORMAL SUIT (Qty: 1), ', 1299.00, 'delivered', '2026-03-21 18:48:42', 'Online', 'pending', NULL, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `image`, `category`, `created_at`) VALUES
(5, 'WINTER JACKETS', 2499, 'images/Contrast Zip Up Hooded Puffer Coat.jpg', 'women', '2026-01-15 14:36:58'),
(7, 'Print Round Neck ', 1299, 'images/Men Summer Letter Print Round Neck Short Sleeve Casual T-Shirt.jpg', 'men', '2026-02-11 12:00:22'),
(8, 'FULL Sleeve T-Shirt', 369, 'images/Men Letter Graphic Contrast Trim Tee Without Necklace.jpg', 'men', '2026-02-11 12:31:08'),
(10, 'SHORT KURTA', 450, 'images/Men\'s Top.jpg', 'men', '2026-02-11 12:40:34'),
(12, 'TRACK PANTS', 599, 'images/Dawn Track Pants.jpg', 'men', '2026-02-11 12:56:46'),
(13, 'JOGGERS', 899, 'images/Manfinity EMRG Men\'s Loose Fit Jeans With Flap Pockets On The Side Baggy Long Washed Cargo Jean Plain Black Urban Street Wear Friends.jpg', 'men', '2026-02-11 13:01:25'),
(14, 'TRACK JACKET', 499, 'images/Nera Track Jacket.jpg', 'men', '2026-02-11 13:03:51'),
(15, 'SHORTS', 399, 'images/download.jpg', 'men', '2026-02-11 13:07:00'),
(16, 'vest', 299, 'images/download (1).jpg', 'men', '2026-02-11 13:12:27'),
(17, 'BAGGY JEANS', 899, 'images/Our editors scoured New York Fashion Week for the___.jpg', 'men', '2026-02-11 13:13:36'),
(18, 'SAREE', 749, 'images/best image of lahanga.jpg', 'women', '2026-02-11 13:28:28'),
(19, 'GOWN', 1800, 'images/download (2).jpg', 'women', '2026-02-11 13:31:39'),
(20, 'DROP SHOULDER', 449, 'images/Drop Shoulder Waffle Knit Oversized Crop Tee _ SHEIN USA.jpg', 'women', '2026-02-11 13:33:08'),
(21, 'HOODIES', 899, 'images/hoodies.jpg', 'women', '2026-02-11 13:34:05'),
(22, 'body con', 2599, 'images/HOUSE OF CB Milena Jersey Corset Maxi Dress in Dark Cherry at Nordstrom, Size X-Large.jpg', 'women', '2026-02-11 13:35:03'),
(23, 'KURTA', 349, 'images/kurta.jpg', 'women', '2026-02-11 13:35:57'),
(24, 'DRESS', 1499, 'images/Sólido Tubo Vestido Cami.jpg', 'women', '2026-02-11 13:36:49'),
(25, 'COAT', 769, 'images/Gabardina cropped.jpg', 'women', '2026-02-11 13:38:01'),
(26, 'LEATHER JACKET', 899, '1773603933_LEATHER JACKET.jpg', 'men', '2026-03-15 19:45:33'),
(27, 'FORMAL SUIT', 1299, '1773604317_FORMAL SUIT.jpg', 'men', '2026-03-15 19:51:57'),
(28, 'FORMAL SHIRT', 699, '1773604394_FORMAL SHIRT.jpg', 'men', '2026-03-15 19:53:14'),
(29, 'TROUSER', 799, '1773604682_TROUSER.jpg', 'women', '2026-03-15 19:58:02'),
(30, 'BLOUSE', 599, '1773604699_BLOUSE.jpg', 'women', '2026-03-15 19:58:19'),
(31, 'TANK TOP', 499, '1773604724_TANK TOP.jpg', 'women', '2026-03-15 19:58:44');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `user_name` varchar(100) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL,
  `review` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `product_id`, `user_id`, `user_name`, `rating`, `review`, `created_at`) VALUES
(1, 28, 5, 'mrunal', 5, 'GOOD FITS AND COLOUR TEXTURE', '2026-03-21 07:54:22'),
(2, 28, 5, 'mrunal', 4, 'I LIKE THE PINK SHIRT ESPECIALLY', '2026-03-21 07:55:05'),
(3, 31, 5, 'mrunal', 4, 'GOOD FIT', '2026-03-21 07:56:24'),
(4, 31, 5, 'mrunal', 5, 'VERY GOOD as shown in images and fabric also good ', '2026-03-21 08:30:58');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `phone` varchar(15) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `pincode` varchar(10) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `created_at`, `phone`, `address`, `pincode`, `gender`, `reset_token`) VALUES
(2, 'mrunal', 'sailmrunal115@gmail.com', '$2y$10$iRQDPq3.0qwY2g.7UcwEz.7VlKz3h/mXhZ/ObwcPUXWGjcUHDbw5W', '2026-02-11 09:51:57', '9370612557', 'Mahalaxmi nagar tekdi near Durga mata mandir ambernath East', '421501', 'Male', NULL),
(3, 'imposter', 'imposterrr@gmail.com', '$2y$10$zQdUtk51u6m13o.HIHI84uF93tvqdPBtaDAHk1MUWNYLQrS7B/9Om', '2026-02-17 12:09:50', NULL, NULL, NULL, NULL, NULL),
(4, 'mrunal', 'lofivbs@gmail.com', '$2y$10$Mpunp5l7CxFaJC5Scm2Q0uKh7Om2iiLJt1jQR4Ax42JyhalXmG8ey', '2026-02-19 09:17:31', NULL, NULL, NULL, NULL, NULL),
(5, 'mrunal', 'mrunal115@gmail.com', '$2y$10$8/5aqM/jJxnmWZhOFW3/Hu3D00KA.uO0VoKO/ucTIc7bSVPVowXY6', '2026-03-11 10:18:16', '9370612557', 'Mahalaxmi nagar tekdi near Durga mata mandir ambernath East', '421501', 'Male', '820e046f16dc63ab25544624694eb541');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=109;

--
-- AUTO_INCREMENT for table `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
