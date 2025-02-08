-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 08, 2025 at 05:30 PM
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
(13, 'Beverages', 'Tequila Sunrise ', 'Cocktail made of tequila, orange juice, and grenadine syrup.', 6.00, '../food_images/67a76f56287f5.png', 'Available'),
(14, 'Burgers', 'Double Cheesy Beef', 'Patty of ground beef grilled and placed between two halves of a bun.', 6.00, '../food_images/67a76f620dd53.png', 'Available'),
(16, 'Seasonal', 'Italian Pizza', 'Flattened disk of bread dough topped with some combination of olive oil, oregano, tomato, olives & mozzarella', 6.00, '../food_images/67a77216c8d81.png', 'Available'),
(19, 'Appetizers', 'French Fries', 'Deep-fried potatoes that have been cut into various shapes, especially thin strips.', 6.00, '../food_images/67a76f4bd11cf.png', 'Available'),
(22, 'Seasonal', 'Call Service', 'Request Waiter to table for assistance', 0.00, '../food_images/67a773494dda9.png', 'Available'),
(23, 'Appetizers', 'Call Service', 'Request Waiter to table for assistance', 0.00, '../food_images/67a7736d5b991.png', 'Available'),
(24, 'Beverages', 'Call Service', 'Request Waiter to table for assistance', 0.00, '../food_images/67a77389a1a49.png', 'Available'),
(25, 'Burgers', 'Call Service', 'Request Waiter to table for assistance', 0.00, '../food_images/67a7739ee64d6.png', 'Available'),
(26, 'Pizza', 'Call Service', 'Request Waiter to table for assistance', 0.00, '../food_images/67a773bedc42a.png', 'Available'),
(27, 'Appetizers', 'Crispy Calamari', 'Golden-fried calamari served with a tangy lemon garlic aioli and a sprinkle of fresh parsley', 10.00, '../food_images/67a7757d6b497.png', 'Available'),
(28, 'Appetizers', 'Bruschetta', 'Toasted baguette slices topped with a fresh mixture of diced tomatoes, basil, garlic, and extra-virgin olive oil', 7.00, '../food_images/67a77593152a1.png', 'Available'),
(29, 'Pizza', 'Margherita Pizza', 'A classic thin-crust pizza layered with zesty tomato sauce, fresh mozzarella, and basil leaves', 12.00, '../food_images/67a775ae09e19.png', 'Available'),
(30, 'Pizza', 'Pepperoni Pizza', 'A hearty pizza loaded with spicy pepperoni, melted mozzarella, and a robust tomato sauce on a crispy crust.', 14.00, '../food_images/67a775cda8eff.png', 'Available'),
(31, 'Burgers', 'Classic Cheeseburger', 'A juicy beef patty with melted cheddar cheese, crisp lettuce, tomato, and pickles, all served on a toasted brioche bun.', 11.00, '../food_images/67a775ecbaaac.png', 'Available'),
(32, 'Burgers', 'Mushroom Swiss Burger', 'A tender grilled beef burger topped with sautéed mushrooms, Swiss cheese, and caramelized onions for a rich, savory flavor.', 13.00, '../food_images/67a775ffe80aa.png', 'Available'),
(33, 'Beverages', 'Fresh Lemonade', 'Homemade lemonade made with freshly squeezed lemons and a hint of mint for extra refreshment\r\n', 3.00, '../food_images/67a776188c0bf.png', 'Available'),
(34, 'Beverages', 'Iced Tea', 'Chilled, brewed iced tea served with a twist of lemon—a perfect thirst-quencher for any time of day', 2.00, '../food_images/67a7762d63b22.png', 'Available'),
(35, 'Seasonal', 'Pumpkin Spice Flatbread (Fall)', 'A seasonal flatbread featuring a creamy pumpkin sauce topped with roasted butternut squash and a hint of sage.', 12.00, '../food_images/67a7764f8ef11.png', 'Available'),
(36, 'Seasonal', 'Winter Citrus Salad (Winter)', 'A vibrant salad of mixed greens with seasonal citrus segments, pomegranate seeds, and a light, zesty dressing', 9.00, '../food_images/67a776bed312b.png', 'Available');

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
(15, 'ORD17384778934818', 2, 'paynow', 'dine_in', '2', NULL, 6.99, '2025-02-02 06:31:33', 'Delivering', 'Unpaid'),
(16, 'ORD17387324371594', 2, 'paynow', 'dine_in', '2', NULL, 13.98, '2025-02-05 05:13:57', 'Pending', 'Unpaid'),
(17, 'ORD17387369572875', 2, 'paynow', 'dine_in', '2', NULL, 13.98, '2025-02-05 06:29:17', 'Pending', 'Unpaid'),
(18, 'ORD17387372005637', 2, 'paynow', 'dine_in', '4', NULL, 13.98, '2025-02-05 06:33:20', 'Pending', 'Unpaid'),
(19, 'ORD17389819321676', NULL, 'paynow', 'dine_in', '5', NULL, 6.99, '2025-02-08 02:32:12', 'Pending', 'Unpaid'),
(20, 'ORD17389829276020', 2, 'paynow', 'dine_in', '5', NULL, 6.99, '2025-02-08 02:48:47', 'Pending', 'Unpaid'),
(21, 'ORD17389933971541', 2, 'paynow', 'dine_in', '5', NULL, 8.00, '2025-02-08 05:43:17', 'Pending', 'Unpaid'),
(24, 'ORD17390299932080', NULL, 'paynow', 'dine_in', '2', NULL, 10.00, '2025-02-08 15:53:13', 'Pending', 'Unpaid');

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
(15, 15, 'French Fries', 1, 6.99, 6.99, '{\"Extra Dipping Sauce\":\"No\",\"Size\":\"Regular\"}'),
(16, 16, 'French Fries', 1, 6.99, 6.99, '{\"Extra Dipping Sauce\":\"No\",\"Size\":\"Regular\"}'),
(17, 16, 'French Fries', 1, 6.99, 6.99, '{\"Extra Dipping Sauce\":\"No\",\"Size\":\"Regular\"}'),
(18, 17, 'French Fries', 1, 6.99, 6.99, '{\"Extra Dipping Sauce\":\"No\",\"Size\":\"Regular\"}'),
(19, 17, 'French Fries', 1, 6.99, 6.99, '{\"Extra Dipping Sauce\":\"No\",\"Size\":\"Regular\"}'),
(20, 18, 'French Fries', 1, 6.99, 6.99, '{\"Extra Dipping Sauce\":\"No\",\"Size\":\"Regular\"}'),
(21, 18, 'Tequila Sunrise', 1, 6.99, 6.99, '{\"Size\":\"Medium\",\"No Ice\":\"No\",\"Sugar Level\":\"Normal\"}'),
(22, 19, 'French Fries', 1, 6.99, 6.99, '{\"Extra Dipping Sauce\":\"No\",\"Size\":\"Regular\"}'),
(23, 20, 'French Fries', 1, 6.99, 6.99, '{\"Extra Dipping Sauce\":\"No\",\"Size\":\"Regular\"}'),
(24, 21, 'Stuffed Mushrooms', 1, 8.00, 8.00, '{\"Crust Type\":\"Regular\",\"Extra Toppings\":\"No\",\"Size\":\"Medium\"}'),
(27, 24, 'Crispy Calamari', 1, 10.00, 10.00, '{\"Extra Dipping Sauce\":\"No\",\"Size\":\"Regular\"}');

-- --------------------------------------------------------

--
-- Table structure for table `queuedetails`
--

CREATE TABLE `queuedetails` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `contact_number` varchar(20) NOT NULL,
  `number_of_persons` int(11) NOT NULL,
  `groups` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `queuedetails`
--

INSERT INTO `queuedetails` (`id`, `name`, `contact_number`, `number_of_persons`, `groups`) VALUES
(42, 'test3', '213', 4, 4),
(43, 'wadwda', '213', 1, 5),
(44, 'tat', '23', 1, 6);

-- --------------------------------------------------------

--
-- Table structure for table `reservation`
--

CREATE TABLE `reservation` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `contact` varchar(15) NOT NULL,
  `date_rsv` date NOT NULL,
  `time_rsv` time NOT NULL,
  `guests` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservation`
--

INSERT INTO `reservation` (`id`, `name`, `email`, `contact`, `date_rsv`, `time_rsv`, `guests`) VALUES
(23, 'christopher low', 'christopherlow06@gmail.com', '85228909', '2025-02-04', '16:16:00', 5),
(24, 'xz', 'skibidi@gmail.com', '1231', '2025-02-03', '16:19:00', 5);

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
(2, 'xianzhe', 'xianzhe@gmail.com', 0, '$2y$10$pXv2KanvFpyGV8kAhnx6yOFSeOxubhnb.ujJQIgganQFNPHNV4fPq', 430, '2025-02-02 13:33:25', '');

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
-- Indexes for table `queuedetails`
--
ALTER TABLE `queuedetails`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reservation`
--
ALTER TABLE `reservation`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `queuedetails`
--
ALTER TABLE `queuedetails`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `reservation`
--
ALTER TABLE `reservation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

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
