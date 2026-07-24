-- Table for customer sell requests
CREATE TABLE `sell_requests` (
  `request_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `card_name` varchar(255) NOT NULL,
  `set_name` varchar(255) NOT NULL,
  `category` enum('Pokémon','Magic: The Gathering','One Piece') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `product_type` enum('Cards','Sealed','Collections','Character','Leader','Artifact','Legendary Creature','Legendary Artifact','Enchantment','Instant','Creature') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `rarity` enum('Common','Uncommon','Rare','Ultra Rare','Secret Rare','C','UC','R','SR','SEC','L','P','SP','AA','TR','MR','M','S','U') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `condition` enum('Mint','Near Mint','Lightly Played','Damaged') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `selling_price` decimal(15,2) NOT NULL,
  `quantity` int NOT NULL,
  `image` varchar(512) DEFAULT NULL,
  `notes` text,
  `status` enum('Pending','Approved','Rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Pending',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`request_id`),
  KEY `user_id_idx` (`user_id`),
  CONSTRAINT `sell_requests_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Optional related tables for bulk buy requests
CREATE TABLE `buy_requests` (
  `request_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `expected_price` decimal(15,2) DEFAULT NULL,
  `admin_offer` decimal(15,2) DEFAULT NULL,
  `status` enum('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`request_id`),
  KEY `user_id_idx` (`user_id`),
  CONSTRAINT `buy_requests_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `buy_request_items` (
  `item_id` int NOT NULL AUTO_INCREMENT,
  `request_id` int NOT NULL,
  `card_name` varchar(255) NOT NULL,
  `game_category` enum('Pokémon','Magic: The Gathering','One Piece') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `set_name` varchar(255) NOT NULL,
  `rarity` enum('Common','Uncommon','Rare','Ultra Rare','Secret Rare','C','UC','R','SR','SEC','L','P','SP','AA','TR','MR','M','S','U') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `condition` enum('Mint','Near Mint','Lightly Played','Damaged') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `quantity` int NOT NULL,
  `front_image` varchar(512) DEFAULT NULL,
  `back_image` varchar(512) DEFAULT NULL,
  `notes` text,
  PRIMARY KEY (`item_id`),
  KEY `request_id_idx` (`request_id`),
  CONSTRAINT `buy_request_items_request_fk` FOREIGN KEY (`request_id`) REFERENCES `buy_requests`(`request_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
