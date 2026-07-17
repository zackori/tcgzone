-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 17, 2026 at 02:05 PM
-- Server version: 8.0.43
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tcgzone`
--

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int NOT NULL,
  `customer_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `product_id` int DEFAULT NULL,
  `quantity` int DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `order_date` date DEFAULT NULL,
  `status` enum('Pending','Processing','In Transit','Delivered','Cancelled') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Pending',
  `user_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `customer_name`, `product_id`, `quantity`, `total`, `order_date`, `status`, `user_id`) VALUES
(68, 'Online Customer', 1, 2, 330.00, '2026-07-01', 'Delivered', 1),
(69, 'Online Customer', 3, 1, 155.00, '2026-07-02', 'Delivered', 1),
(70, 'Online Customer', 6, 2, 380.00, '2026-07-03', 'Delivered', 1),
(71, 'Online Customer', 5, 1, 180.00, '2026-07-04', 'Delivered', 1),
(72, 'Online Customer', 8, 2, 170.00, '2026-07-05', 'Delivered', 1);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int NOT NULL,
  `product_name` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `category` varchar(80) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `stock` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `product_name`, `category`, `price`, `stock`) VALUES
(1, 'Pokemon Booster Box', 'Pokemon', 6500.00, 25),
(2, 'One Piece OP-05', 'One Piece', 5500.00, 10),
(3, 'YuGiOh Tin', 'YuGiOh', 2200.00, 0),
(4, 'MTG Commander Deck', 'Magic', 2800.00, 4),
(5, 'Pokemon ETB', 'Pokemon', 3200.00, 0),
(6, 'OP-06 Booster Box', 'One Piece', 6200.00, 20),
(7, 'MTG Booster Pack', 'Magic', 450.00, 100),
(8, 'YuGiOh Structure Deck', 'YuGiOh', 700.00, 35),
(9, 'Scarlet & Violet Booster Box', 'Pokemon', 165.00, 20),
(10, 'Paldea Evolved Booster Box', 'Pokemon', 170.00, 18),
(11, 'Obsidian Flames Booster Box', 'Pokemon', 155.00, 25),
(12, 'Paradox Rift Booster Box', 'Pokemon', 175.00, 20),
(13, 'Temporal Forces Booster Box', 'Pokemon', 180.00, 22),
(14, 'Twilight Masquerade Booster Box', 'Pokemon', 190.00, 18),
(15, 'Surging Sparks Booster Box', 'Pokemon', 200.00, 15),
(16, 'Pokemon 151 Elite Trainer Box', 'Pokemon', 85.00, 40),
(17, 'Crown Zenith Elite Trainer Box', 'Pokemon', 70.00, 35),
(18, 'Shining Fates Elite Trainer Box', 'Pokemon', 65.00, 30),
(19, 'Charizard ex Premium Collection', 'Pokemon', 120.00, 25),
(20, 'Arceus VSTAR Ultra Premium Collection', 'Pokemon', 130.00, 18),
(21, 'Celebrations Ultra Premium Collection', 'Pokemon', 400.00, 12),
(22, 'Pokemon GO Elite Trainer Box', 'Pokemon', 55.00, 25),
(23, 'Brilliant Stars Booster Box', 'Pokemon', 185.00, 20),
(24, 'Fusion Strike Booster Box', 'Pokemon', 220.00, 16),
(25, 'Lost Origin Booster Box', 'Pokemon', 210.00, 15),
(26, 'Silver Tempest Booster Box', 'Pokemon', 175.00, 18),
(27, 'Astral Radiance Booster Box', 'Pokemon', 185.00, 16),
(28, 'Evolving Skies Booster Box', 'Pokemon', 900.00, 10),
(29, 'Hidden Fates Elite Trainer Box', 'Pokemon', 180.00, 15),
(30, 'Champion\'s Path Elite Trainer Box', 'Pokemon', 140.00, 18),
(31, 'Charizard UPC', 'Pokemon', 150.00, 20),
(32, 'Pokemon TCG Classic', 'Pokemon', 400.00, 8),
(33, 'Trainer\'s Toolkit 2024', 'Pokemon', 45.00, 30);

-- --------------------------------------------------------

--
-- Table structure for table `products_test`
--

CREATE TABLE `products_test` (
  `product_id` int NOT NULL,
  `category` enum('Pokémon','Magic','One Piece') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `card_name` varchar(255) NOT NULL,
  `product_type` enum('Cards','Collection') NOT NULL,
  `set_name` varchar(255) NOT NULL,
  `rarity` enum('Common','Uncommon','Rare','Ultra Rare','Secret Rare') NOT NULL,
  `condition` enum('Mint','Near Mint','Lightly Played','Damaged') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `selling_price` decimal(10,2) NOT NULL,
  `market_price` decimal(10,2) DEFAULT NULL,
  `stock_quantity` int NOT NULL DEFAULT '0',
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `products_test`
--

INSERT INTO `products_test` (`product_id`, `category`, `card_name`, `product_type`, `set_name`, `rarity`, `condition`, `selling_price`, `market_price`, `stock_quantity`, `image`, `created_at`) VALUES
(1, 'Pokémon', 'Clefairy — 094/088', 'Cards', 'ME03: Perfect Order\r\n', 'Rare', 'Near Mint', 49.00, 57.00, 1, '../../assets/images/landing page/image 3.png', '2026-07-17 09:00:52'),
(2, 'Pokémon', 'Rayquaza VMAX (Alternate Art Secret)', 'Cards', 'SWSH07: Evolving Skies', 'Secret Rare', 'Mint', 6767.43, 1224.43, 0, '../../assets/images/landing page/image 4.png', '2026-07-17 09:48:15');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `name` varchar(100) DEFAULT 'Anonymous',
  `rating` tinyint UNSIGNED NOT NULL,
  `review_text` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `user_id`, `name`, `rating`, `review_text`, `created_at`) VALUES
(1, 1, 'Zackori', 4, 'test test', '2026-07-12 13:13:58'),
(2, 1, 'Anonymous', 2, 'test test test', '2026-07-12 13:14:38');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `first_name` varchar(50) DEFAULT '',
  `last_name` varchar(50) DEFAULT '',
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT '',
  `dob` date DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT 'Male',
  `address_details` varchar(255) DEFAULT '',
  `address_city` varchar(100) DEFAULT '',
  `address_province` varchar(100) DEFAULT '',
  `address_zip` varchar(10) DEFAULT '',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `first_name`, `last_name`, `email`, `password_hash`, `phone`, `dob`, `gender`, `address_details`, `address_city`, `address_province`, `address_zip`, `created_at`) VALUES
(1, 'minnnjuuu', 'Zack Gregori', 'Ogena', 'mizu11th@gmail.com', '$2y$10$LKeOedvMinjEfBisqIoUGeUGCB/8j5cKNS7XimdzS5yVquyeTB3Ee', '09914846214', '2004-12-23', 'Male', '', 'Trece Martires City', 'Cavite', '4109', '2026-07-10 10:29:14'),
(2, 'lleve', '', '', 'zack3.ogena@gmail.com', '$2y$10$0c0jVFtRZAfbaxtZcCGVq.D/nkKNnFlW91T5HIgcewrjlEZxKxc96', '09914846214', NULL, 'Male', '', '', '', '', '2026-07-11 09:48:00'),
(3, 'clark', 'Clark Christopher', 'Rustique', 'clark@gmail.com', '$2y$10$MnHYcMlwEA0HOuADpHF9RuLmi2SsfH9aoEpsnqaWiwvsS77gxk9De', '09111111111', NULL, 'Male', '', '', '', '', '2026-07-11 10:17:54');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `products_test`
--
ALTER TABLE `products_test`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reviews_ibfk_1` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `products_test`
--
ALTER TABLE `products_test`
  MODIFY `product_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
