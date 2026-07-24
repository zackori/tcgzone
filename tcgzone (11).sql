-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 24, 2026 at 01:15 PM
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
-- Table structure for table `cancel_requests`
--

CREATE TABLE `cancel_requests` (
  `cancel_request_id` int NOT NULL,
  `order_id` int NOT NULL,
  `reason` enum('Better Price Elsewhere','Unforeseen Financial Circumstances','Emergency/Unexpected Changes') NOT NULL,
  `status` enum('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending',
  `requested_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int NOT NULL,
  `user_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`cart_id`, `user_id`, `created_at`, `updated_at`) VALUES
(5000, 4, '2026-07-18 09:26:42', '2026-07-18 10:58:24'),
(5001, 1, '2026-07-18 12:49:48', '2026-07-24 10:26:04'),
(5002, 5, '2026-07-19 09:06:10', '2026-07-19 10:14:52');

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `cart_item_id` int NOT NULL,
  `cart_id` int DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `quantity` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `order_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `payment_method` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `order_status` enum('Pending','Processing','In Transit','Delivered','Cancelled') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `total_amount` decimal(15,2) DEFAULT NULL,
  `house_no_street` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `city` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `province` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `zip_code` varchar(20) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `order_date`, `payment_method`, `order_status`, `total_amount`, `house_no_street`, `city`, `province`, `zip_code`) VALUES
(1001, 1, '2026-07-24 07:46:17', 'gcash', 'Delivered', 640.00, 'BLK 12 LOT 17-A Bougainvilla st. Brgy. Lapidario', 'Trece Martires City', 'Cavite', '4109'),
(1002, 1, '2026-07-24 09:10:02', 'cod', 'Delivered', 176.68, 'BLK 12 LOT 17-A Bougainvilla st. Brgy. Lapidario', 'Trece Martires City', 'Cavite', '4109'),
(1003, 1, '2026-07-24 10:26:21', 'gcash', 'Delivered', 82628.37, 'BLK 12 LOT 17-A Bougainvilla st. Brgy. Lapidario', 'Trece Martires City', 'Cavite', '4109');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int NOT NULL,
  `order_id` int DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `quantity` int DEFAULT NULL,
  `unit_price` decimal(15,2) DEFAULT NULL,
  `subtotal` decimal(15,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `product_id`, `quantity`, `unit_price`, `subtotal`) VALUES
(1428, 1001, 48, 1, 490.00, 490.00),
(1429, 1002, 5, 1, 26.68, 26.68),
(1430, 1003, 1, 2, 2203.06, 4406.12),
(1431, 1003, 2, 1, 73600.25, 73600.25),
(1432, 1003, 4, 1, 2299.00, 2299.00),
(1433, 1003, 14, 1, 1015.00, 1015.00),
(1434, 1003, 18, 1, 395.00, 395.00),
(1435, 1003, 24, 1, 3.00, 3.00),
(1436, 1003, 31, 2, 380.00, 760.00);

-- --------------------------------------------------------

--
-- Table structure for table `procurement_orders`
--

CREATE TABLE `procurement_orders` (
  `procurement_order_id` int NOT NULL,
  `supplier_id` int NOT NULL,
  `total_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `order_status` enum('Pending','Processing','In Transit','Delivered','Cancelled') NOT NULL DEFAULT 'Pending',
  `order_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `procurement_orders`
--

INSERT INTO `procurement_orders` (`procurement_order_id`, `supplier_id`, `total_amount`, `order_status`, `order_date`) VALUES
(1, 2, 5322.05, 'Cancelled', '2026-07-24 16:49:45'),
(2, 3, 2128.82, 'Delivered', '2026-07-24 16:54:55'),
(3, 3, 1526.80, 'Delivered', '2026-07-24 17:11:21'),
(4, 1, 5838.28, 'Delivered', '2026-07-24 17:40:34'),
(5, 2, 860.00, 'Delivered', '2026-07-24 17:50:48'),
(6, 3, 450.00, 'Pending', '2026-07-24 18:20:37'),
(7, 5, 216.30, 'Delivered', '2026-07-24 19:09:01');

-- --------------------------------------------------------

--
-- Table structure for table `procurement_order_items`
--

CREATE TABLE `procurement_order_items` (
  `procurement_order_item_id` int NOT NULL,
  `procurement_order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL,
  `unit_cost` decimal(15,2) NOT NULL,
  `subtotal` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `procurement_order_items`
--

INSERT INTO `procurement_order_items` (`procurement_order_item_id`, `procurement_order_id`, `product_id`, `quantity`, `unit_cost`, `subtotal`) VALUES
(1, 1, 14, 5, 1064.41, 5322.05),
(2, 2, 14, 2, 1064.41, 2128.82),
(3, 3, 5, 3, 15.60, 46.80),
(4, 3, 46, 2, 740.00, 1480.00),
(5, 4, 6, 3, 67.98, 203.94),
(6, 4, 9, 2, 850.31, 1700.62),
(7, 4, 10, 3, 246.83, 740.49),
(8, 4, 14, 3, 1064.41, 3193.23),
(9, 5, 18, 2, 430.00, 860.00),
(10, 6, 48, 1, 450.00, 450.00),
(11, 7, 50, 5, 43.26, 216.30);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int NOT NULL,
  `category` enum('Pokémon','Magic: The Gathering','One Piece') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `card_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `product_type` enum('Cards','Sealed','Collections','Character','Leader','Artifact','Legendary Creature','Legendary Artifact','Enchantment','Instant','Creature') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `set_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `rarity` enum('','Common','Uncommon','Rare','Ultra Rare','Secret Rare','C','UC','R','SR','SEC','L','P','SP','AA','TR','MR','M','S','U') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `condition` enum('','Mint','Near Mint','Lightly Played','Damaged') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `selling_price` decimal(15,2) NOT NULL,
  `market_price` decimal(15,2) DEFAULT NULL,
  `product_cost` decimal(15,2) DEFAULT NULL,
  `stock_quantity` int NOT NULL DEFAULT '0',
  `image` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `category`, `card_name`, `product_type`, `set_name`, `rarity`, `condition`, `selling_price`, `market_price`, `product_cost`, `stock_quantity`, `image`, `created_at`) VALUES
(1, 'Pokémon', 'Clefairy — 094/088', 'Cards', 'ME03: Perfect Order\r\n', 'Rare', 'Near Mint', 2203.06, 2050.70, 2060.50, 2, '/tcgzone/assets/images/landing page/image 3.png', '2026-07-17 09:00:52'),
(2, 'Pokémon', 'Rayquaza VMAX (Alternate Art Secret)', 'Cards', 'SWSH07: Evolving Skies', 'Secret Rare', 'Mint', 73600.25, 70167.70, 72700.32, 0, '../../assets/images/landing page/image 4.png', '2026-07-17 09:48:15'),
(3, 'One Piece', 'Monkey.D.Luffy', 'Character', 'Romance Dawn (OP-01)', 'SR', 'Damaged', 179.00, 205.00, 155.65, 6, '/tcgzone/assets/images/landing page/luffy-secret.jpg', '2026-07-18 16:16:04'),
(4, 'Magic: The Gathering', 'Ragavan, Nimble Pilferer', 'Legendary Creature', 'Modern Horizons 2', 'M', 'Mint', 2299.00, 2499.00, 1839.20, 2, '/tcgzone/assets/images/landing page/ragavan.jpg', '2026-07-18 16:30:18'),
(5, 'Pokémon', 'Gengar', 'Cards', 'Fossil', 'Rare', 'Mint', 26.68, 15.60, 24.25, 4, '/tcgzone/assets/images/landing page/gengar-rare.jpg', '2026-07-18 16:30:18'),
(6, 'Pokémon', 'Mega Lopunny & Jigglypuff GX (Secret)', 'Cards', 'Cosmic Eclipse', 'Secret Rare', 'Lightly Played', 79.39, 67.98, 72.17, 4, '/tcgzone/assets/images/landing page/image 10.png', '2026-07-18 16:30:18'),
(7, 'One Piece', 'Trafalgar Law', 'Character', 'Premium Booster PRB 02', 'SR', 'Damaged', 459.00, 499.00, 399.13, 2, '/tcgzone/assets/images/landing page/traf1.jpg', '2026-07-18 16:30:18'),
(8, 'Magic: The Gathering', 'Sol Ring', 'Artifact', 'Commander Masters', 'U', 'Lightly Played', 95.00, 110.00, 86.36, 9, '/tcgzone/assets/images/landing page/solring.jpg', '2026-07-18 16:30:18'),
(9, 'Pokémon', 'Mega Charizard X ex', 'Cards', 'Phantasmal Flames', 'Ultra Rare', 'Mint', 1299.99, 850.31, 1039.99, 4, '/tcgzone/assets/images/landing page/image 6.png', '2026-07-18 16:30:18'),
(10, 'Pokémon', 'Mega Greninja ex', 'Cards', 'Chaos Rising', 'Ultra Rare', 'Lightly Played', 353.76, 246.83, 307.62, 5, '/tcgzone/assets/images/landing page/image 11.png', '2026-07-18 16:30:18'),
(11, 'Magic: The Gathering', 'Birds of Paradise', 'Creature', 'Ravnica Remastered', 'R', 'Damaged', 799.00, 849.00, 665.83, 4, '/tcgzone/assets/images/landing page/birds.jpg', '2026-07-18 16:45:42'),
(12, 'Magic: The Gathering', 'Sheoldred, the Apocalypse', 'Legendary Creature', 'Dominaria United', 'M', 'Damaged', 4599.00, 4899.00, 3679.20, 2, '/tcgzone/assets/images/landing page/sheoldred.jpg', '2026-07-18 16:46:09'),
(13, 'Pokémon', 'Gengar VMAX', 'Cards', 'Fusion Strike', 'Ultra Rare', 'Mint', 63.72, 34.30, 57.93, 13, '/tcgzone/assets/images/landing page/image 9.png', '2026-07-18 16:46:09'),
(14, 'Pokémon', 'Mew ex', 'Cards', 'Paldean Fates', 'Ultra Rare', 'Mint', 1015.00, 1064.41, 812.00, 4, '/tcgzone/assets/images/landing page/image 12.png', '2026-07-18 16:46:09'),
(15, 'Pokémon', 'Mimikyu (Secret)', 'Cards', 'Cosmic Eclipse', 'Secret Rare', 'Damaged', 63.03, 60.73, 57.30, 3, '/tcgzone/assets/images/landing page/mimikyu-secret.jpg', '2026-07-18 16:46:09'),
(16, 'One Piece', 'Otama', 'Character', 'Romance Dawn (OP 01)', 'UC', 'Near Mint', 39.00, 45.00, 35.45, 11, '/tcgzone/assets/images/landing page/otama1.jpg', '2026-07-18 16:46:09'),
(17, 'Pokémon', '151 Booster Pack', 'Sealed', 'Scarlet & Violet 151', '', '', 50.00, 29.34, 45.45, 10, '/tcgzone/assets/images/landing page/151-boost.jpg', '2026-07-18 16:47:53'),
(18, 'One Piece', 'Portgas.D.Ace', 'Character', 'The Time of Battle (OP16)', 'SR', 'Mint', 395.00, 430.00, 343.48, 3, '/tcgzone/assets/images/landing page/ace1.jpg', '2026-07-18 16:48:08'),
(19, 'One Piece', 'Nico Robin', 'Character', 'Romance Dawn (OP 01)', 'C', 'Mint', 35.00, 40.00, 31.82, 15, '/tcgzone/assets/images/landing page/nico1.jpg', '2026-07-18 16:48:08'),
(20, 'Magic: The Gathering', 'Doubling Season', 'Enchantment', 'Wilds of Eldraine: Enchanting Tales', 'M', 'Near Mint', 3699.00, 3899.00, 2959.20, 2, '/tcgzone/assets/images/landing page/doubling.jpg', '2026-07-18 16:48:08'),
(21, 'Pokémon', 'Ditto', 'Cards', 'Skyridge', 'Common', 'Near Mint', 110.00, 78.66, 95.65, 3, '/tcgzone/assets/images/landing page/ditto.jpg', '2026-07-18 16:48:08'),
(22, 'Pokémon', 'White Flare Booster Pack', 'Sealed', 'White Flare', '', '', 17.99, 15.63, 16.35, 13, '/tcgzone/assets/images/landing page/white-boost.jpg', '2026-07-18 16:48:08'),
(23, 'One Piece', 'Sanji', 'Character', 'Romance Dawn (OP 01)', 'UC', 'Mint', 65.00, 79.00, 59.09, 8, '/tcgzone/assets/images/landing page/sanji1.jpg', '2026-07-18 16:48:08'),
(24, 'Pokémon', 'Psyduck', 'Cards', 'Ascended Heroes', 'Common', 'Lightly Played', 3.00, 0.30, 2.73, 19, '/tcgzone/assets/images/landing page/psyduck-com.jpg', '2026-07-18 16:48:08'),
(25, 'Pokémon', 'Snorlax', 'Cards', 'Scarlet & Violet 151', 'Uncommon', 'Damaged', 1.95, 0.25, 1.77, 15, '/tcgzone/assets/images/landing page/snorlax-un.jpg', '2026-07-18 16:48:08'),
(26, 'Magic: The Gathering', 'Black Lotus', 'Artifact', 'Limited Edition Alpha', 'R', 'Near Mint', 2500000.00, 3000000.00, 1785714.29, 1, '/tcgzone/assets/images/landing page/blacklotus.jpg', '2026-07-18 16:48:08'),
(27, 'Magic: The Gathering', 'Serra Angel', 'Creature', 'Foundations', 'U', 'Mint', 59.00, 70.00, 53.64, 8, '/tcgzone/assets/images/landing page/serra.jpg', '2026-07-18 16:48:08'),
(28, 'Pokémon', 'Black Bolt Booster Pack', 'Sealed', 'Black Bolt', '', '', 19.99, 15.58, 18.17, 18, '/tcgzone/assets/images/landing page/black-boost.jpg', '2026-07-18 16:48:08'),
(29, 'Magic: The Gathering', 'Lightning Bolt', 'Instant', 'Magic 2010', 'C', 'Near Mint', 120.00, 140.00, 104.35, 12, '/tcgzone/assets/images/landing page/bolt.jpg', '2026-07-18 16:48:08'),
(30, 'Magic: The Gathering', 'The One Ring', 'Legendary Artifact', 'The Lord of the Rings: Tales of Middle earth', 'M', 'Mint', 4199.00, 4499.00, 3359.20, 2, '/tcgzone/assets/images/landing page/ring.jpg', '2026-07-18 16:48:08'),
(31, 'Pokémon', 'Zekrom ex', 'Cards', 'Black Bolt', 'Ultra Rare', 'Near Mint', 380.00, 543.00, 330.43, 3, '/tcgzone/assets/images/landing page/image 2.png', '2026-07-18 16:48:08'),
(32, 'Pokémon', 'Magikarp', 'Cards', 'Base Set', 'Uncommon', 'Damaged', 49.99, 23.81, 45.45, 5, '/tcgzone/assets/images/landing page/magikarp-un.jpg', '2026-07-18 16:48:08'),
(33, 'Magic: The Gathering', 'Omniscience', 'Enchantment', 'Core Set 2021', 'M', 'Mint', 1699.00, 1799.00, 1359.20, 2, '/tcgzone/assets/images/landing page/omni.jpg', '2026-07-18 16:48:08'),
(34, 'One Piece', 'Monkey.D.Luffy', 'Character', 'Paramount War (OP 02)', 'R', 'Lightly Played', 119.00, 138.00, 103.48, 5, '/tcgzone/assets/images/landing page/luffy2.jpg', '2026-07-18 16:48:08'),
(35, 'Pokémon', 'Umbreon ex', 'Cards', 'Prismatic Evolutions', 'Ultra Rare', 'Near Mint', 1099.01, 1503.91, 879.21, 1, '/tcgzone/assets/images/landing page/image 7.png', '2026-07-18 16:48:08'),
(36, 'Pokémon', 'Kabuto', 'Cards', 'Fossil', 'Common', 'Damaged', 34.32, 22.58, 31.20, 14, '/tcgzone/assets/images/landing page/kabuto-com.jpg', '2026-07-18 16:48:08'),
(37, 'One Piece', 'Nami', 'Character', 'Paramount War (OP 02)', 'SR', 'Near Mint', 289.00, 325.00, 251.30, 3, '/tcgzone/assets/images/landing page/nami2.jpg', '2026-07-18 16:48:08'),
(38, 'One Piece', 'Nami', 'Character', 'Romance Dawn (OP 01)', 'R', 'Mint', 89.00, 99.00, 80.91, 10, '/tcgzone/assets/images/landing page/nami1.jpg', '2026-07-18 16:48:08'),
(39, 'Pokémon', 'Pikachu ex', 'Cards', 'Ascended Heroes', 'Rare', 'Lightly Played', 1599.99, 1231.86, 1279.99, 7, '/tcgzone/assets/images/landing page/image 8.png', '2026-07-18 16:48:08'),
(40, 'One Piece', 'Roronoa Zoro', 'Leader', 'Romance Dawn (OP 01)', 'L', 'Near Mint', 249.00, 279.00, 216.52, 5, '/tcgzone/assets/images/landing page/zoro1.jpg', '2026-07-18 16:50:53'),
(41, 'Pokémon', 'Mega Zygarde ex Premium Collection', 'Collections', '', '', '', 4023.00, 3880.30, 3890.20, 8, '/tcgzone/assets/images/landing page/image 18.png', '2026-07-18 18:22:12'),
(42, 'Pokémon', 'Greninja ex Special Illustration Collection', 'Collections', 'SV: Shrouded Fable', '', '', 15039.15, 11650.00, 12300.60, 3, '/tcgzone/assets/images/landing page/image  19.png', '2026-07-18 18:22:18'),
(43, 'Pokémon', '2 Pack Blister Pack [Team Rocket\'s Articuno, Zapdos & Tyranitar]', 'Collections', '', '', '', 2500.25, 2300.50, 2350.75, 10, '/tcgzone/assets/images/landing page/image 20.jpg', '2026-07-18 18:22:18'),
(44, 'Pokémon', 'Espeon GX Premium Collection', 'Collections', 'SM   Guardians Rising', '', '', 75650.80, 74000.23, 74200.50, 1, '/tcgzone/assets/images/landing page/image 21.png', '2026-07-18 18:22:18'),
(45, 'Pokémon', 'Paldean Fates Great Tusk ex & Iron Treads ex Premium Collection', 'Collections', 'SV: Paldean Fates', '', '', 67850.30, 65000.70, 62000.80, 3, '/tcgzone/assets/images/landing page/image 22.png', '2026-07-18 18:22:18'),
(46, 'One Piece', 'Perona (036) - Royal Blood Release Event Cards (OP10 RE)', 'Character', 'Royal Blood Release Event Cards', 'C', 'Near Mint', 800.00, 740.00, 760.00, 2, '/tcgzone/assets/images/sell/sell_6a620c57052278.16160776_620762_in_1000x1000.jpg', '2026-07-23 12:43:53'),
(47, 'One Piece', 'Boa Hancock (SP) - Extra Booster: Anime 25th Collection (EB-02)', 'Leader', 'Extra Booster: Anime 25th Collection', 'L', 'Mint', 300000.00, 265000.00, 268000.00, 1, '/tcgzone/assets/images/products/card_6a623b32354612.03544659.jpg', '2026-07-23 15:21:50'),
(48, 'One Piece', 'Shirahoshi (OP05-082) (Alternate Art) - Premium Booster -The Best- (PRB-01)', 'Character', 'Premium Booster -The Best-', 'R', 'Mint', 490.00, 450.00, 470.00, 2, '/tcgzone/assets/images/sell/sell_6a623f2261c830.84823451_593472_in_1000x1000.jpg', '2026-07-23 16:20:17'),
(49, 'Pokémon', 'Vaporeon V - SWSH181 - SWSH: Sword & Shield Promo Cards (SWSD)', 'Cards', 'SWSH: Sword & Shield Promo Cards', 'Rare', 'Near Mint', 18050.00, 15000.00, 16050.00, 2, '/tcgzone/assets/images/sell/sell_6a63439a30e772.93754316_253416_in_1000x1000.jpg', '2026-07-24 10:51:40'),
(50, 'Pokémon', 'Espeon V - SWSH07: Evolving Skies (SWSH07)', 'Cards', 'SWSH07: Evolving Skies', 'Ultra Rare', 'Mint', 52.30, 50.00, 43.26, 5, '/tcgzone/assets/images/products/purchased_6a6347cd70ac78.43841882.jpg', '2026-07-24 11:09:01');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT 'Anonymous',
  `rating` tinyint UNSIGNED NOT NULL,
  `review_text` text COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `user_id`, `name`, `rating`, `review_text`, `created_at`) VALUES
(4, 1, 'Slime na cute', 5, 'Absolutely love this marketplace! Found a near-mint Zekrom ex from Black Bolt in under 10 minutes and the price was better than everywhere else. Fast shipping and the card condition was exactly as described. Already bought 3 more cards. Highly recommended for serious collectors!', '2026-07-22 17:50:12'),
(5, 1, 'Ditto', 5, '\"Amazing experience from start to finish! Picked up a pristine Reshiram ex from White Flare without any hassle, and the pricing was surprisingly affordable. The packaging was secure, shipping was quick, and the card looked flawless. Will definitely keep coming back for future releases!\"', '2026-07-22 17:50:48'),
(6, 1, 'Ditto ka na lang', 5, '\"Couldn\'t be happier with my purchase! Finally completed my Scarlet & Violet collection thanks to the wide selection available here. Everything arrived on time, carefully protected, and in excellent condition. Great service, fair prices, and an awesome marketplace for Pokémon TCG fans!\"', '2026-07-22 17:51:09');

-- --------------------------------------------------------

--
-- Table structure for table `sell_requests`
--

CREATE TABLE `sell_requests` (
  `request_id` int NOT NULL,
  `user_id` int NOT NULL,
  `product_id` int DEFAULT NULL,
  `card_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `set_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `category` enum('Pokémon','Magic: The Gathering','One Piece') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `product_type` enum('Cards','Sealed','Collections','Character','Leader','Artifact','Legendary Creature','Legendary Artifact','Enchantment','Instant','Creature') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `rarity` enum('Common','Uncommon','Rare','Ultra Rare','Secret Rare','C','UC','R','SR','SEC','L','P','SP','AA','TR','MR','M','S','U') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `condition` enum('Mint','Near Mint','Lightly Played','Damaged') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `selling_price` decimal(15,2) NOT NULL,
  `stock_quantity` int NOT NULL,
  `image` varchar(512) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_general_ci,
  `status` enum('Pending','Approved','Rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Pending',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sell_requests`
--

INSERT INTO `sell_requests` (`request_id`, `user_id`, `product_id`, `card_name`, `set_name`, `category`, `product_type`, `rarity`, `condition`, `selling_price`, `stock_quantity`, `image`, `notes`, `status`, `created_at`) VALUES
(1, 1, 48, 'Shirahoshi (OP05-082) (Alternate Art) - Premium Booster -The Best- (PRB-01)', 'Premium Booster -The Best-', 'One Piece', 'Character', 'R', 'Near Mint', 470.00, 3, '/tcgzone/assets/images/sell/sell_6a623f2261c830.84823451_593472_in_1000x1000.jpg', '0', 'Approved', '2026-07-24 00:19:46'),
(2, 1, 5, 'Gengar', 'Fossil', 'Pokémon', 'Cards', 'Rare', 'Mint', 20.00, 1, '/tcgzone/assets/images/sell/sell_6a633b6d2deca3.67409564_gengar-rare.jpg', '0', 'Approved', '2026-07-24 18:16:13'),
(3, 1, 49, 'Vaporeon V - SWSH181 - SWSH: Sword & Shield Promo Cards (SWSD)', 'SWSH: Sword & Shield Promo Cards', 'Pokémon', 'Cards', 'Rare', 'Near Mint', 16050.00, 2, '/tcgzone/assets/images/sell/sell_6a63439a30e772.93754316_253416_in_1000x1000.jpg', '0', 'Approved', '2026-07-24 18:51:06');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `supplier_id` int NOT NULL,
  `supplier_name` varchar(120) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`supplier_id`, `supplier_name`) VALUES
(3, 'Eclipse Collectibles Wholesale'),
(2, 'Northstar TCG Distribution'),
(5, 'TCGZONE'),
(1, 'Vault Card Supplies');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `first_name` varchar(50) COLLATE utf8mb4_general_ci DEFAULT '',
  `last_name` varchar(50) COLLATE utf8mb4_general_ci DEFAULT '',
  `email` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT '',
  `dob` date DEFAULT NULL,
  `gender` enum('Male','Female','Other') COLLATE utf8mb4_general_ci DEFAULT 'Male',
  `address_details` varchar(255) COLLATE utf8mb4_general_ci DEFAULT '',
  `address_city` varchar(100) COLLATE utf8mb4_general_ci DEFAULT '',
  `address_province` varchar(100) COLLATE utf8mb4_general_ci DEFAULT '',
  `address_zip` varchar(10) COLLATE utf8mb4_general_ci DEFAULT '',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `first_name`, `last_name`, `email`, `password_hash`, `phone`, `dob`, `gender`, `address_details`, `address_city`, `address_province`, `address_zip`, `created_at`) VALUES
(1, 'minnnjuuu', 'Zack Gregori', 'Ogena', 'mizu11th@gmail.com', '$2y$10$LKeOedvMinjEfBisqIoUGeUGCB/8j5cKNS7XimdzS5yVquyeTB3Ee', '09914846214', '2004-12-23', 'Male', 'BLK 12 LOT 17-A Bougainvilla st. Brgy. Lapidario', 'Trece Martires City', 'Cavite', '4109', '2026-07-10 10:29:14'),
(2, 'lleve', '', '', 'zack3.ogena@gmail.com', '$2y$10$S5.ed8W5xk.PBMVvM7WBsOxo/ufrEOzvVEoROgVF/gOwIU2/D68Xa', '09914846214', NULL, 'Male', '', '', '', '', '2026-07-11 09:48:00'),
(4, 'kjustindt', 'Kurt Justin', 'Dela Torre', 'kurt@gmail.com', '$2y$10$p/URpe7xDXMYcvFrmMB4ienHdzyK/VVltHKA8bt.RS3sM1YaStZDq', '09123456789', '2005-09-17', 'Male', '123 Pick Street', 'Denver', 'Colorado', '80216', '2026-07-17 22:01:15'),
(5, 'fias', 'Gregori', 'Ogena', 'fiasco@gmail.com', '$2y$10$UipzgJYpy9.GgOp1nwyOPelCnlaGqzFv.qHPzFZVeP6FXC22wJ1lG', '09914846214', NULL, 'Male', 'Trece Martires City', 'Cavite', 'Manila', '4109', '2026-07-19 09:05:59');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cancel_requests`
--
ALTER TABLE `cancel_requests`
  ADD PRIMARY KEY (`cancel_request_id`),
  ADD UNIQUE KEY `order_id` (`order_id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `fk_cart_users` (`user_id`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`cart_item_id`),
  ADD UNIQUE KEY `unique_cart_product` (`cart_id`,`product_id`),
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
-- Indexes for table `procurement_orders`
--
ALTER TABLE `procurement_orders`
  ADD PRIMARY KEY (`procurement_order_id`),
  ADD KEY `procurement_orders_supplier_idx` (`supplier_id`);

--
-- Indexes for table `procurement_order_items`
--
ALTER TABLE `procurement_order_items`
  ADD PRIMARY KEY (`procurement_order_item_id`),
  ADD KEY `procurement_items_order_idx` (`procurement_order_id`),
  ADD KEY `procurement_items_product_idx` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reviews_ibfk_1` (`user_id`);

--
-- Indexes for table `sell_requests`
--
ALTER TABLE `sell_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `user_id_idx` (`user_id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`supplier_id`),
  ADD UNIQUE KEY `suppliers_name_unique` (`supplier_name`);

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
-- AUTO_INCREMENT for table `cancel_requests`
--
ALTER TABLE `cancel_requests`
  MODIFY `cancel_request_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5004;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `cart_item_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=544;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1004;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1437;

--
-- AUTO_INCREMENT for table `procurement_orders`
--
ALTER TABLE `procurement_orders`
  MODIFY `procurement_order_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `procurement_order_items`
--
ALTER TABLE `procurement_order_items`
  MODIFY `procurement_order_item_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `sell_requests`
--
ALTER TABLE `sell_requests`
  MODIFY `request_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `supplier_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cancel_requests`
--
ALTER TABLE `cancel_requests`
  ADD CONSTRAINT `fk_cancel_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `fk_cart_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `cart` (`cart_id`),
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `procurement_orders`
--
ALTER TABLE `procurement_orders`
  ADD CONSTRAINT `procurement_orders_supplier_fk` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`supplier_id`);

--
-- Constraints for table `procurement_order_items`
--
ALTER TABLE `procurement_order_items`
  ADD CONSTRAINT `procurement_items_order_fk` FOREIGN KEY (`procurement_order_id`) REFERENCES `procurement_orders` (`procurement_order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `procurement_items_product_fk` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sell_requests`
--
ALTER TABLE `sell_requests`
  ADD CONSTRAINT `sell_requests_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
