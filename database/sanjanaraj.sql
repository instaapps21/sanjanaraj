-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 15, 2026 at 07:56 PM
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
-- Database: `sanjanaraj`
--

-- --------------------------------------------------------

--
-- Table structure for table `cms_pages`
--

CREATE TABLE `cms_pages` (
  `id` int(11) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `title` varchar(150) NOT NULL,
  `content` longtext DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cms_pages`
--

INSERT INTO `cms_pages` (`id`, `slug`, `title`, `content`, `updated_at`) VALUES
(1, 'terms', 'Terms & Conditions', '<h2>Terms and Conditions</h2><p>Welcome to Future India Network.</p>', '2026-04-05 04:49:23'),
(2, 'privacy', 'Privacy Policy', '<h2>Privacy Policy</h2><p>Your privacy is important to us.</p>', '2026-04-05 04:49:23');

-- --------------------------------------------------------

--
-- Table structure for table `complaints`
--

CREATE TABLE `complaints` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `admin_reply` text DEFAULT NULL,
  `status` enum('open','resolved') DEFAULT 'open',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kyc_details`
--

CREATE TABLE `kyc_details` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `document_type` varchar(50) NOT NULL,
  `document_number` varchar(100) NOT NULL,
  `document_path` varchar(255) NOT NULL,
  `bank_name` varchar(100) DEFAULT NULL,
  `branch_name` varchar(100) DEFAULT NULL,
  `account_holder_name` varchar(100) DEFAULT NULL,
  `account_number` varchar(100) DEFAULT NULL,
  `ifsc_code` varchar(50) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `rejection_reason` text DEFAULT NULL,
  `submitted_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `news_events`
--

CREATE TABLE `news_events` (
  `id` int(11) NOT NULL,
  `type` enum('news','announcement','event') DEFAULT 'news',
  `title` varchar(200) NOT NULL,
  `content` text DEFAULT NULL,
  `event_date` date DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `packages`
--

CREATE TABLE `packages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `direct_income` decimal(10,2) NOT NULL,
  `matching_bonus` decimal(10,2) NOT NULL,
  `level_income` decimal(10,2) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `packages`
--

INSERT INTO `packages` (`id`, `name`, `price`, `direct_income`, `matching_bonus`, `level_income`, `is_active`) VALUES
(1, 'Starter Package', 1000.00, 100.00, 150.00, 50.00, 1),
(2, 'Pro Package', 5000.00, 500.00, 750.00, 250.00, 1);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category` varchar(100) NOT NULL DEFAULT 'Other',
  `name` varchar(200) NOT NULL,
  `tamil_name` varchar(200) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `weight` varchar(50) DEFAULT '1 KG',
  `pv` int(11) DEFAULT 0,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT 'default_product.png',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category`, `name`, `tamil_name`, `description`, `weight`, `pv`, `price`, `image`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Premium Beach Fish', 'Seer Fish', 'வஞ்சரம் மீன்', 'Premium quality seer fish, fresh from the beach. Known for its rich flavor and firm texture. Ideal for frying, grilling, and making traditional fish curry. கடற்கரையிலிருந்து புதிதாகப் பிடிக்கப்பட்ட, உயர்தரமான சீர் மீன். அதன் செறிவான சுவை மற்றும் உறுதியான தன்மைக்காகப் பெயர் பெற்றது. பொரிப்பதற்கும், சுடுவதற்கும், மற்றும் பாரம்பரிய மீன் குழம்பு செய்வதற்கும் மிகவும் ஏற்றது.', '1 KG', 13, 1200.00, 'fish_1.jpeg', 1, '2026-04-13 13:52:05', '2026-04-13 14:17:31'),
(2, 'Regular Beach Fish', 'Mackerel', 'அயிலா மீன்', 'Fresh mackerel with excellent omega-3 content. A staple in South Indian cuisine, perfect for fish fry and kuzhambhu preparations. சிறந்த ஒமேகா-3 சத்து நிறைந்த புத்தம் புதிய கானாங்கெளுத்தி மீன். தென்னிந்திய சமையலில் ஒரு முக்கிய அங்கமான இது, மீன் வறுவல் மற்றும் குழம்பு தயாரிப்புகளுக்கு மிகவும் ஏற்றது.', '1 KG', 5, 400.00, 'fish_4.jpeg', 1, '2026-04-13 13:52:05', '2026-04-13 14:16:53'),
(3, 'Premium Beach Fish', 'Red Snapper', 'சங்கரா மீன்', 'Premium red snapper known for its delicate and mild flavor. Versatile fish suitable for curries, grills, and steamed preparations. அதன் மென்மையான மற்றும் லேசான சுவைக்காக அறியப்படும் உயர்தர செம்பறை மீன். குழம்புகள், கிரில் மற்றும் ஆவியில் வேகவைக்கப்பட்ட உணவுகளுக்கு ஏற்ற, பலவிதமாகப் பயன்படுத்தக்கூடிய மீன்.', '1 KG', 6, 500.00, 'fish_7.jpeg', 1, '2026-04-13 13:52:05', '2026-04-13 14:16:10'),
(4, 'Premium Beach Fish', 'Grouper', 'கலவை மீன்', 'Fresh grouper with firm, white flesh. A premium catch prized in both Indian and international cuisines. உறுதியான, வெண்மையான சதையைக் கொண்ட புத்தம் புதிய குரூப்பர் மீன். இந்திய மற்றும் சர்வதேச சமையல் வகைகளில் பெரிதும் போற்றப்படும் ஒரு உயர்தர மீன்.', '1 KG', 5, 650.00, 'fish_10.jpeg', 1, '2026-04-13 13:52:05', '2026-04-13 14:15:16'),
(5, 'Special Beach Fish', 'Stingray', 'திருக்கை மீன் மீன்', 'Specialty stingray prepared and cleaned for cooking. Known for its unique texture and rich taste in traditional preparations. (சமைப்பதற்காகத் தயாரிக்கப்பட்டு சுத்தம் செய்யப்பட்ட சிறப்புத் திருக்கை மீன். பாரம்பரிய சமையல் முறைகளில் அதன் தனித்துவமான அமைப்பு மற்றும் செறிவான சுவைக்காகப் பெயர் பெற்றது).', '1 KG', 5, 500.00, 'fish_13.jpeg', 1, '2026-04-13 13:52:05', '2026-04-13 14:14:40'),
(6, 'Regular Beach Fish', 'Sardine', 'சார்டின் / சாளை மீன்', 'Fresh sardines packed with nutrition and flavor. An affordable superfood rich in protein and omega-3 fatty acids. (ஊட்டச்சத்தும் சுவையும் நிறைந்த புத்தம் புதிய மத்தி மீன்கள். புரதம் மற்றும் ஒமேகா-3 கொழுப்பு அமிலங்கள் செறிந்த, மலிவு விலையிலான ஒரு சூப்பர்ஃபுட்)', '1 KG', 6, 350.00, 'fish_16.jpeg', 1, '2026-04-13 13:52:05', '2026-04-13 14:13:30'),
(7, 'Premium Beach Fish', 'Pomfret', 'வாவால் மீன்', 'Silver pomfret with delicate texture and sweet flavor. One of the most sought-after fish varieties for special occasions. (மென்மையான அமைப்பையும் இனிமையான சுவையையும் கொண்ட வெள்ளிப் பாம்ஃப்ரெட். விசேஷ நிகழ்வுகளுக்காக மிகவும் விரும்பப்படும் மீன் வகைகளில் இதுவும் ஒன்று)', '1 KG', 20, 280.00, 'fish_19.jpeg', 1, '2026-04-13 13:52:05', '2026-04-13 14:12:35'),
(8, 'Special Beach Fish', 'Rock Fish', 'பாறை மீன்', 'Fresh rock fish sourced from coastal reefs. Known for its firm texture and suitability for robust curry preparations. (கடலோரப் பவளப்பாறைகளிலிருந்து பெறப்படும் புத்தம் புதிய பாறை மீன். அதன் உறுதியான தன்மைக்காகவும், சுவையான கறி வகைகளுக்கு ஏற்றதாகவும் அறியப்படுகிறது).', '1 KG', 10, 600.00, 'fish_22.jpeg', 1, '2026-04-13 13:52:05', '2026-04-13 14:11:54'),
(9, 'Special Beach Fish', 'Leather Jacket', 'தோல் மீன்', 'Specialty leather jacket fish with tender meat. A delicacy enjoyed in traditional coastal cuisine. (மென்மையான சதை கொண்ட சிறப்பு வகை லெதர் ஜாக்கெட் மீன். பாரம்பரிய கடலோர சமையலில் விரும்பி உண்ணப்படும் ஒரு சுவைமிகு உணவு).', '1 KG', 7, 550.00, 'fish_25.jpeg', 1, '2026-04-13 13:52:05', '2026-04-13 14:11:16'),
(10, 'Special Beach Fish', 'Cornet Fish', 'கொம்பு மீன்', 'Fresh cornet fish with a unique elongated shape. Known for its mild flavor and versatile cooking applications. (தனித்துவமான நீள்வட்ட வடிவம் கொண்ட புத்தம் புதிய கார்னெட் மீன். அதன் மென்மையான சுவை மற்றும் பல்துறை சமையல் பயன்பாடுகளுக்காக அறியப்படுகிறது)', '1 KG', 8, 600.00, 'fish_28.jpeg', 1, '2026-04-13 13:52:05', '2026-04-13 14:09:10'),
(11, 'Shellfish Beach', 'Crab', 'நண்டு', 'Fresh live crabs from the coast. A premium shellfish delicacy, perfect for crab masala and pepper crab preparations. (கடற்கரையிலிருந்து பிடிக்கப்பட்ட புத்தம் புதிய உயிருள்ள நண்டுகள். நண்டு மசாலா மற்றும் மிளகு நண்டு தயாரிப்புகளுக்கு மிகவும் பொருத்தமான, ஒரு உயர்தரமான கடல் உணவு வகை).', '1 KG', 10, 650.00, 'fish_31.jpeg', 1, '2026-04-13 13:52:05', '2026-04-13 14:07:59'),
(12, 'Shellfish Beach', 'Prawn', 'இறால்', 'Fresh prawns cleaned and ready to cook. Packed with protein and ideal for curries, biryanis, and stir-fry dishes. (புத்தம் புதிய இறால்கள் சுத்தம் செய்யப்பட்டு, சமைக்கத் தயாராக உள்ளன. புரதச்சத்து நிறைந்த இவை, குழம்புகள், பிரியாணிகள் மற்றும் வறுவல் உணவுகளுக்கு மிகவும் ஏற்றவை)', '1 KG', 8, 700.00, 'fish_34.jpeg', 1, '2026-04-13 13:52:05', '2026-04-13 14:06:17'),
(13, 'Freshwater', 'Tilapia', 'திலாபியா / ஜிலேபி மீன்', 'Farm-fresh tilapia with mild, sweet flavor. An affordable and nutritious freshwater fish perfect for daily meals (பண்ணையில் இருந்து புதிதாகப் பிடிக்கப்பட்ட, மென்மையான இனிப்புச் சுவையுடைய திலாப்பியா. அன்றாட உணவுகளுக்கு ஏற்ற, மலிவான மற்றும் சத்தான நன்னீர் மீன்).', '1 KG', 3, 180.00, 'fish_37.jpeg', 1, '2026-04-13 13:52:05', '2026-04-13 14:04:47'),
(14, 'Beach Fish', 'Red Mullet', 'செம்மீன்', 'Fresh red mullet known for its distinctive appearance and excellent taste. Suitable for frying and curry preparations (அதன் தனித்துவமான தோற்றம் மற்றும் சிறந்த சுவைக்காக அறியப்படும் புத்தம் புதிய சிவப்பு முல்லட் மீன். பொரிப்பதற்கும் கறி வகைகளுக்கும் ஏற்றது).', '1 KG', 6, 500.00, 'fish_40.jpeg', 1, '2026-04-13 13:52:05', '2026-04-13 14:03:10'),
(15, 'Premium Beach Fish', 'Emperor Fish', 'எம்பரர் / வண்ஜி மீன்', 'Premium emperor fish with thick, meaty fillets. A high-quality catch prized for its robust flavor. (தடித்த, சதைப்பற்றுள்ள ஃபில்லெட்டுகளைக் கொண்ட உயர்தர எம்பரர் மீன். அதன் செறிவான சுவைக்காகப் பெரிதும் மதிக்கப்படும் ஒரு உயர்தர மீன்).', '1 KG', 6, 550.00, 'fish_43.jpeg', 1, '2026-04-13 13:52:05', '2026-04-13 14:02:20'),
(16, 'Regular Beach Fish', 'Marula', 'கானாங்கான்', 'Fresh marula fish with firm flesh and rich taste. A traditional favorite in South Indian coastal cooking (கெட்டியான சதையையும் நிறைவான சுவையையும் கொண்ட புத்தம் புதிய மருலா மீன். தென்னிந்தியக் கடலோரச் சமையலில் பாரம்பரியமாக விரும்பப்படும் ஒரு உணவு).', '1 KG', 7, 650.00, 'fish_46.jpeg', 1, '2026-04-13 13:52:05', '2026-04-13 14:03:41');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `type` enum('credit','debit') NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `sponsor_id` varchar(50) DEFAULT NULL,
  `referral_id` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `splname` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `aadhaar_no` varchar(20) DEFAULT NULL,
  `pan_no` varchar(20) DEFAULT NULL,
  `utrn_no` varchar(50) DEFAULT NULL,
  `package_id` int(11) DEFAULT 0,
  `wallet_balance` decimal(10,2) DEFAULT 0.00,
  `total_earned` decimal(10,2) DEFAULT 0.00,
  `binary_position` enum('left','right') DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `role` enum('user','admin','customer') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `pv` int(11) DEFAULT 0,
  `bv` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `sponsor_id`, `referral_id`, `name`, `email`, `phone`, `splname`, `password_hash`, `aadhaar_no`, `pan_no`, `utrn_no`, `package_id`, `wallet_balance`, `total_earned`, `binary_position`, `parent_id`, `role`, `created_at`, `updated_at`, `pv`, `bv`) VALUES
(1, NULL, 'FIN_ADMIN', 'Admin User', 'admin@sanjanaraj.in', '0000000000', '', '$2y$10$cQ2NXrYI.M9YmaZ2J1C1Fu9H.k58sUNmP1bmMQ3i9ORYcfMKMCpN2', NULL, NULL, NULL, 0, 0.00, 0.00, NULL, NULL, 'admin', '2026-04-05 04:18:58', '2026-04-05 04:39:29', 0, 0),
(2, 'FIN_ADMIN', 'FIN7314EC', 'Test User', 'testuser@example.com', '9988776655', '', '$2y$10$JZnaCXk6a/mw1nGPe82XIumq/eoyanpT810JDQvttI9uHP4BdvBBm', NULL, NULL, NULL, 0, 0.00, 0.00, NULL, 1, 'user', '2026-04-05 04:32:03', '2026-04-05 04:32:03', 0, 0),
(4, 'FIN_ADMIN', 'FIN09F929', 'Petchiappan Kaliappan', 'kpetchiappancse@gmail.com', '9942796726', '', '$2y$10$N8wCuqOo7Npw8WIHBY1lse2i0yndhgRQaLnnYeJk2YBcY5x/vizqe', NULL, NULL, NULL, 0, 0.00, 0.00, NULL, 1, 'user', '2026-04-07 12:41:03', '2026-04-15 17:14:01', 0, 0),
(5, 'FIN09F929', 'FINC0F064', 'KESHAVA ERANAN P', 'kpncse@smcet.edu.in', '9876543210', '', '$2y$10$BhgusEdolC1jjv1ImlfbIOyHgRpqrrdSAL8Su/xrxFu2zJ1lGKEPy', '123456782233', 'AOJPP9882L', 'MS123456789001', 0, 0.00, 0.00, 'left', 4, 'user', '2026-04-13 14:37:24', '2026-04-13 14:45:25', 0, 0),
(6, 'FIN09F929', 'FIN20FB81', 'Koushick P', 'hod_cse@smcet.edu.in', '1234567890', '', '$2y$10$zscUZUT0mtzeQqyAKEY6IuYcjyUPdoJm.2D3jcXKwL4MQ4lRcR29e', '565678784545', 'AOJPP9883K', 'MS123456789012', 0, 0.00, 0.00, 'right', 4, 'user', '2026-04-13 14:47:09', '2026-04-13 14:48:10', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `withdrawals`
--

CREATE TABLE `withdrawals` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `request_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `process_date` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cms_pages`
--
ALTER TABLE `cms_pages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `complaints`
--
ALTER TABLE `complaints`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `kyc_details`
--
ALTER TABLE `kyc_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `news_events`
--
ALTER TABLE `news_events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `packages`
--
ALTER TABLE `packages`
  ADD PRIMARY KEY (`id`);

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
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `referral_id` (`referral_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone` (`phone`);

--
-- Indexes for table `withdrawals`
--
ALTER TABLE `withdrawals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cms_pages`
--
ALTER TABLE `cms_pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `complaints`
--
ALTER TABLE `complaints`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kyc_details`
--
ALTER TABLE `kyc_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `news_events`
--
ALTER TABLE `news_events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `packages`
--
ALTER TABLE `packages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `withdrawals`
--
ALTER TABLE `withdrawals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `complaints`
--
ALTER TABLE `complaints`
  ADD CONSTRAINT `complaints_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `kyc_details`
--
ALTER TABLE `kyc_details`
  ADD CONSTRAINT `kyc_details_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `withdrawals`
--
ALTER TABLE `withdrawals`
  ADD CONSTRAINT `withdrawals_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
