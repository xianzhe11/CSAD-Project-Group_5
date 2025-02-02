-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 02, 2025 at 07:49 AM
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
-- Database: `restaurant_food`
--

-- --------------------------------------------------------

--
-- Table structure for table `menu_categories`
--

CREATE TABLE `menu_categories` (
  `id` int(11) NOT NULL,
  `catName` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_categories`
--

INSERT INTO `menu_categories` (`id`, `catName`) VALUES
(1, 'Appetizers'),
(4, 'Beverages'),
(2, 'Burgers'),
(3, 'Pizza'),
(5, 'Seasonal');

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `id` int(11) NOT NULL,
  `catName` varchar(100) NOT NULL,
  `itemName` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` enum('Available','Unavailable') DEFAULT 'Available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`id`, `catName`, `itemName`, `description`, `price`, `image`, `status`) VALUES
(2, 'Pizza', 'Stuffed Mushrooms', 'Mushrooms filled with cheese and herbs.', 8.00, 'pizza.png', 'Available'),
(13, 'Beverages', 'Tequila Sunrise ', 'Cocktail made of tequila, orange juice, and grenadine syrup.', 6.99, 'tequila_sunrise.jpg', 'Available'),
(14, 'Burgers', 'Double Cheesy Beef', 'Patty of ground beef grilled and placed between two halves of a bun.', 6.99, 'Beef_Cheese_Burger.jpg', 'Available'),
(15, 'Appetizers', 'French Fries', 'Deep-fried potatoes that have been cut into various shapes, especially thin strips.', 6.99, 'french_fries.jpg', 'Available'),
(16, 'Seasonal', 'Italian Pizza', 'Flattened disk of bread dough topped with some combination of olive oil, oregano, tomato, olives & mozzarella', 6.99, 'seasonal_pizza.jpg', 'Available'),
(17, 'Appetizers', 'French Fries', 'Deep-fried potatoes that have been cut into various shapes, especially thin strips.', 6.99, 'french_fries.jpg', 'Available'),
(18, 'Appetizers', 'French Fries', 'Deep-fried potatoes that have been cut into various shapes, especially thin strips.', 6.99, 'french_fries.jpg', 'Available'),
(19, 'Appetizers', 'French Fries', 'Deep-fried potatoes that have been cut into various shapes, especially thin strips.', 6.99, 'french_fries.jpg', 'Available');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `order_id` varchar(25) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `order_type` enum('dine_in','takeaway') NOT NULL,
  `table_number` varchar(10) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `order_status` varchar(50) DEFAULT 'Pending',
  `payment_status` varchar(50) DEFAULT 'Unpaid'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_id`, `user_id`, `payment_method`, `order_type`, `table_number`, `address`, `total_price`, `created_at`, `order_status`, `payment_status`) VALUES
(1, 'ORD1738071', NULL, 'paynow', 'dine_in', '10A', '', 13.98, '2025-01-28 13:45:53', 'Completed', 'Paid'),
(2, 'ORD1738072', NULL, 'paynow', 'dine_in', '12A', NULL, 6.99, '2025-01-28 13:55:47', 'Delivering', 'Unpaid'),
(3, 'ORD1738075', NULL, 'takeaway', '', NULL, 'Dover', 6.99, '2025-01-28 14:53:13', 'Pending', 'Unpaid'),
(5, 'ORD1738076', NULL, 'paypal', 'takeaway', NULL, '0', 6.99, '2025-01-28 14:56:35', 'Pending', 'Unpaid'),
(7, 'ORD17380765974544', NULL, 'paynow', 'takeaway', NULL, 'Clementi', 6.99, '2025-01-28 15:03:17', 'Pending', 'Unpaid'),
(8, 'ORD17383289247601', NULL, 'paynow', 'dine_in', '8A', NULL, 13.98, '2025-01-31 13:08:44', 'Completed', 'Unpaid'),
(9, 'ORD17384757053583', NULL, 'paynow', 'dine_in', '2B', NULL, 6.99, '2025-02-02 05:55:05', 'Pending', 'Unpaid'),
(10, 'ORD17384763153400', NULL, 'paynow', 'dine_in', '16', NULL, 6.99, '2025-02-02 06:05:15', 'Pending', 'Unpaid'),
(11, 'ORD17384766304772', NULL, 'paynow', 'dine_in', '2A', NULL, 6.99, '2025-02-02 06:10:30', 'Pending', 'Unpaid'),
(12, 'ORD17384768162020', NULL, 'paypal', 'dine_in', '2', NULL, 6.99, '2025-02-02 06:13:36', 'Pending', 'Unpaid'),
(13, 'ORD17384771443492', NULL, 'paynow', 'dine_in', '1', NULL, 8.00, '2025-02-02 06:19:04', 'Pending', 'Unpaid'),
(14, 'ORD17384775859560', NULL, 'paynow', 'dine_in', '5', NULL, 8.00, '2025-02-02 06:26:25', 'Pending', 'Unpaid'),
(15, 'ORD17384778934818', 2, 'paynow', 'dine_in', '2', NULL, 6.99, '2025-02-02 06:31:33', 'Delivering', 'Unpaid');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `item_name` varchar(100) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price_each` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `customizations` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `item_name`, `quantity`, `price_each`, `total_price`, `customizations`) VALUES
(1, 1, 'French Fries', 2, 6.99, 13.98, '{\"Extra Dipping Sauce\":\"No\",\"Size\":\"Regular\"}'),
(2, 2, 'French Fries', 1, 6.99, 6.99, '{\"Extra Dipping Sauce\":\"No\",\"Size\":\"Regular\"}'),
(3, 3, 'Tequila Sunrise', 1, 6.99, 6.99, '{\"Size\":\"Medium\",\"No Ice\":\"No\",\"Sugar Level\":\"Normal\"}'),
(5, 5, 'Double Cheesy Beef', 1, 6.99, 6.99, '{\"Extra Cheese\":\"No\",\"No Onion\":\"No\",\"Add Bacon\":\"No\"}'),
(6, 7, 'Italian Pizza', 1, 6.99, 6.99, '{\"Special Ingredient\":\"No\",\"Size\":\"Regular\"}'),
(7, 8, 'Stuffed Mushrooms', 1, 6.99, 6.99, '{\"Crust Type\":\"Regular\",\"Extra Toppings\":\"No\",\"Size\":\"Medium\"}'),
(8, 8, 'Tequila Sunrise', 1, 6.99, 6.99, '{\"Size\":\"Medium\",\"No Ice\":\"No\",\"Sugar Level\":\"Normal\"}'),
(9, 9, 'French Fries', 1, 6.99, 6.99, '{\"Extra Dipping Sauce\":\"No\",\"Size\":\"Regular\"}'),
(10, 10, 'Double Cheesy Beef', 1, 6.99, 6.99, '{\"Extra Cheese\":\"No\",\"No Onion\":\"No\",\"Add Bacon\":\"No\"}'),
(11, 11, 'Italian Pizza', 1, 6.99, 6.99, '{\"Special Ingredient\":\"No\",\"Size\":\"Regular\"}'),
(12, 12, 'Tequila Sunrise', 1, 6.99, 6.99, '{\"Size\":\"Medium\",\"No Ice\":\"No\",\"Sugar Level\":\"Normal\"}'),
(13, 13, 'Stuffed Mushrooms', 1, 8.00, 8.00, '{\"Crust Type\":\"Regular\",\"Extra Toppings\":\"No\",\"Size\":\"Medium\"}'),
(14, 14, 'Stuffed Mushrooms', 1, 8.00, 8.00, '{\"Crust Type\":\"Regular\",\"Extra Toppings\":\"No\",\"Size\":\"Medium\"}'),
(15, 15, 'French Fries', 1, 6.99, 6.99, '{\"Extra Dipping Sauce\":\"No\",\"Size\":\"Regular\"}');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `date_of_visit` date NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `feedback` text NOT NULL,
  `food_quality` tinyint(4) NOT NULL,
  `service` tinyint(4) NOT NULL,
  `value` tinyint(4) NOT NULL,
  `cleanliness` tinyint(4) NOT NULL,
  `speed` tinyint(4) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `name`, `email`, `date_of_visit`, `phone_number`, `feedback`, `food_quality`, `service`, `value`, `cleanliness`, `speed`, `created_at`, `status`) VALUES
(5, 'xz', 'xz@gmail.com', '2000-11-11', '+65 1234 5678', 'Nice!', 5, 5, 5, 5, 5, '2025-01-22 08:33:39', 'approved'),
(6, 'Xian Zhe', 'xianzhe@gmail.com', '2025-01-22', '+65 8888 8888', 'Fantastic Food, Quick Service. Would visit again!', 5, 5, 5, 5, 5, '2025-01-22 09:06:21', 'approved'),
(7, 'Christopher', 'cy@gmail.com', '2025-11-11', '+65 1234 5678', 'Njce', 4, 5, 5, 4, 5, '2025-01-23 00:14:07', 'pending'),
(8, 'Christopher', 'xianzhe@gmail.com', '2025-01-10', '+65 8666 6666', 'too noob', 5, 4, 4, 5, 3, '2025-01-23 03:43:09', 'rejected');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` int(12) DEFAULT NULL,
  `pass` varchar(255) NOT NULL,
  `points` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `profile_picture` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `phone`, `pass`, `points`, `created_at`, `profile_picture`) VALUES
(1, 'john_doe', 'john@example.com', 0, 'hashed_password_here', 0, '2025-01-28 22:40:13', ''),
(2, 'xianzhe', 'xianzhe@gmail.com', 0, '$2y$10$pXv2KanvFpyGV8kAhnx6yOFSeOxubhnb.ujJQIgganQFNPHNV4fPq', 0, '2025-02-02 13:33:25', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `menu_categories`
--
ALTER TABLE `menu_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `catName` (`catName`);

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `catName` (`catName`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_id` (`order_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

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
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `menu_categories`
--
ALTER TABLE `menu_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD CONSTRAINT `menu_items_ibfk_1` FOREIGN KEY (`catName`) REFERENCES `menu_categories` (`catName`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
