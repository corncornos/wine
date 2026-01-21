-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 20, 2025 at 06:33 PM
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
-- Database: `accountsdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `image` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `description`, `created_at`, `image`, `quantity`) VALUES
(1, 'Jose Cuervo Gold', 1240.00, 'Makes you Crazy', '2025-05-20 08:18:41', 'uploads/Jose_Cuervo_Gold_1L_3200x.jpg', 6),
(2, 'Olmeca Tequila', 1499.00, 'Devils Inside', '2025-05-20 08:20:13', 'uploads/Olmeca Tequila.jpg', 5),
(3, 'Reposado Tequila', 890.00, 'Pour a Shot', '2025-05-20 08:22:10', 'uploads/1800_Reposado_3200x.jpg', 8),
(4, 'Jim Bean', 899.00, 'Sip Sip Hooray', '2025-05-20 08:23:01', 'uploads/JimBeam.jpg', 10),
(5, 'Spy Valley Satellite', 1299.00, 'Unleash your taste', '2025-05-20 08:23:54', 'uploads/SpyValleySatelliteSauvBlanc_1680x.jpg', 10),
(6, 'Paulaner Drunkel', 459.00, 'Taste the delight', '2025-05-20 08:25:08', 'uploads/PaulanerDunkel.jpg', 10),
(7, 'Corona Extra', 714.00, 'Beyond Ordinary', '2025-05-20 08:27:31', 'uploads/CoronaExtra330mlBottleCaseof24_38c1fe38-18d3-454d-b4b9-59563871e3f3_large.jpg', 10),
(8, 'Red Horse Can', 759.00, 'Indulgence Redefined', '2025-05-20 08:28:32', 'uploads/RedHorseBeer330mLCanBundleof24_2_3891dbdc-5381-47ef-acd2-31078a2488f5_large.jpg', 10),
(9, 'San Miguel Super', 359.00, 'A sip of ecstasy', '2025-05-20 08:29:22', 'uploads/SanMiguelSuperDryBeer330mLCanBundleof6_a3df3f02-6e6a-4e38-a74c-a6ab3d8f1500_large.jpg', 9),
(10, 'Sigma Glass', 89.00, 'One sip at the time', '2025-05-20 08:32:01', 'uploads/glass2.jpg', 10),
(11, 'Coupe Glass', 89.00, 'Smooth Sail', '2025-05-20 08:32:44', 'uploads/glass3.jpg', 10),
(12, 'Luxury Glass', 89.00, 'One sip at the time', '2025-05-20 08:33:25', 'uploads/glass4.jpg', 10),
(13, 'Jack Daniels Large', 1699.00, 'Sip the Unseen', '2025-05-20 08:36:44', 'uploads/Jd700ml_large.png', 10),
(14, 'Jack Daniels Old', 1249.00, 'Crafted with passion', '2025-05-20 08:37:29', 'uploads/Untitleddesign-2021-05-18T191320.806_2048x2048_39a34cb9-339e-4553-bf76-188538335fd6_large.png', 10),
(15, 'Benjies Devil', 5000.00, 'Devils Corner', '2025-05-20 08:38:14', 'uploads/JDApple_large.png', 10);

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `transaction_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_method` varchar(50) NOT NULL DEFAULT 'COD'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `username`, `product_id`, `quantity`, `total_price`, `transaction_date`, `payment_method`) VALUES
(37, 'Guest', 3, 1, 890.00, '2025-05-20 08:42:50', 'COD'),
(38, 'Guest', 1, 2, 2480.00, '2025-05-20 12:12:34', 'COD'),
(39, 'hatdog', 1, 2, 2480.00, '2025-05-20 13:34:27', 'GCash'),
(40, 'yeah', 2, 1, 1499.00, '2025-05-20 16:30:18', 'COD'),
(41, 'yeah', 3, 1, 890.00, '2025-05-20 16:30:18', 'COD'),
(42, 'yeah', 2, 4, 5996.00, '2025-05-20 16:32:14', 'COD'),
(43, 'yeah', 9, 1, 359.00, '2025-05-20 16:33:29', 'GCash');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `role` varchar(20) NOT NULL,
  `username` varchar(500) NOT NULL,
  `password` varchar(255) NOT NULL,
  `date` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `role`, `username`, `password`, `date`) VALUES
(1, 'admin', 'admin', 'admin', '2025-05-18'),
(14, 'user', 'cocon', '123', '2025-05-21'),
(15, 'user', 'hatdog', '123', '2025-05-21'),
(16, 'user', 'yeah', 'e', '2025-05-21');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
