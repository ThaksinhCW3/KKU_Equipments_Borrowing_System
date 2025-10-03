-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: mysql:3306
-- Generation Time: Oct 03, 2025 at 08:03 AM
-- Server version: 8.0.43
-- PHP Version: 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `borrow_mysqldb`
--

-- --------------------------------------------------------

--
-- Table structure for table `borrow_requests`
--

CREATE TABLE `borrow_requests` (
  `id` bigint UNSIGNED NOT NULL,
  `req_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `users_id` bigint UNSIGNED NOT NULL,
  `equipments_id` bigint UNSIGNED NOT NULL,
  `start_at` datetime DEFAULT NULL,
  `end_at` datetime DEFAULT NULL,
  `status` enum('pending','approved','rejected','check_out','returned','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `request_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `request_reason_detail` text COLLATE utf8mb4_unicode_ci,
  `reject_reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cancel_reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `borrow_requests`
--

INSERT INTO `borrow_requests` (`id`, `req_id`, `users_id`, `equipments_id`, `start_at`, `end_at`, `status`, `request_reason`, `request_reason_detail`, `reject_reason`, `cancel_reason`, `created_at`, `updated_at`) VALUES
(61, 'REQKAEJJHNDWK', 6, 3, '2025-10-03 00:00:00', '2025-10-08 00:00:00', 'pending', 'assignment', 'เฮ็ดเชี่ยหยังจักอย่างนิละ', NULL, NULL, '2025-10-03 07:52:30', '2025-10-03 07:52:30'),
(62, 'REQKPEX7HOIC0', 6, 1, '2025-10-03 00:00:00', '2025-10-09 00:00:00', 'pending', 'assignment', 'เทสๆนะนองงงง', NULL, NULL, '2025-10-03 08:02:19', '2025-10-03 08:02:19');

-- --------------------------------------------------------

--
-- Table structure for table `borrow_transactions`
--

CREATE TABLE `borrow_transactions` (
  `id` bigint UNSIGNED NOT NULL,
  `borrow_requests_id` bigint UNSIGNED NOT NULL,
  `checked_out_at` datetime DEFAULT NULL,
  `checked_in_at` datetime DEFAULT NULL,
  `penalty_amount` int NOT NULL DEFAULT '0',
  `penalty_check` enum('paid','unpaid') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unpaid' COMMENT 'to check penalty payment',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `borrow_transactions`
--

INSERT INTO `borrow_transactions` (`id`, `borrow_requests_id`, `checked_out_at`, `checked_in_at`, `penalty_amount`, `penalty_check`, `notes`, `created_at`, `updated_at`) VALUES
(14, 61, NULL, NULL, 0, 'unpaid', NULL, '2025-10-03 07:52:30', '2025-10-03 07:52:30'),
(15, 62, NULL, NULL, 0, 'unpaid', NULL, '2025-10-03 08:02:19', '2025-10-03 08:02:19');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`, `created_at`) VALUES
('app_cacheadmin_dashboard_2025_', 'a:9:{s:12:\"borrowStatus\";a:5:{s:13:\"TotalRequests\";i:1;s:10:\"checkinReq\";i:0;s:8:\"Approved\";i:0;s:8:\"Rejected\";i:0;s:7:\"Pending\";i:1;}s:9:\"chartData\";a:5:{s:6:\"labels\";a:12:{i:0;s:3:\"Jan\";i:1;s:3:\"Feb\";i:2;s:3:\"Mar\";i:3;s:3:\"Apr\";i:4;s:3:\"May\";i:5;s:3:\"Jun\";i:6;s:3:\"Jul\";i:7;s:3:\"Aug\";i:8;s:3:\"Sep\";i:9;s:3:\"Oct\";i:10;s:3:\"Nov\";i:11;s:3:\"Dec\";}s:13:\"TotalRequests\";a:12:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:1;i:10;i:0;i:11;i:0;}s:10:\"checkinReq\";a:12:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;s:1:\"0\";i:10;i:0;i:11;i:0;}s:7:\"Pending\";a:12:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;s:1:\"1\";i:10;i:0;i:11;i:0;}s:8:\"Rejected\";a:12:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;s:1:\"0\";i:10;i:0;i:11;i:0;}}s:14:\"availableYears\";O:29:\"Illuminate\\Support\\Collection\":2:{s:8:\"\0*\0items\";a:1:{i:0;i:2025;}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}s:12:\"selectedYear\";i:2025;s:13:\"selectedMonth\";N;s:14:\"recentRequests\";O:29:\"Illuminate\\Support\\Collection\":2:{s:8:\"\0*\0items\";a:1:{i:0;a:6:{s:2:\"id\";s:13:\"REQKAEJJHNDWK\";s:9:\"user_name\";s:19:\"unikzer00@gmail.com\";s:14:\"equipment_name\";s:4:\"sony\";s:5:\"start\";s:10:\"2025-10-03\";s:3:\"end\";s:10:\"2025-10-08\";s:6:\"status\";s:7:\"Pending\";}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}s:14:\"categoryCounts\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:1:{i:0;O:19:\"App\\Models\\Category\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:10:\"categories\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:6:{s:2:\"id\";i:1;s:7:\"cate_id\";s:7:\"SFWQ133\";s:4:\"name\";s:3:\"cam\";s:10:\"created_at\";N;s:10:\"updated_at\";N;s:16:\"equipments_count\";i:2;}s:11:\"\0*\0original\";a:6:{s:2:\"id\";i:1;s:7:\"cate_id\";s:7:\"SFWQ133\";s:4:\"name\";s:3:\"cam\";s:10:\"created_at\";N;s:10:\"updated_at\";N;s:16:\"equipments_count\";i:2;}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:3:{i:0;s:2:\"id\";i:1;s:7:\"cate_id\";i:2;s:4:\"name\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}s:16:\"popularEquipment\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}s:15:\"borrowingTrends\";a:3:{s:6:\"labels\";a:12:{i:0;s:3:\"Jan\";i:1;s:3:\"Feb\";i:2;s:3:\"Mar\";i:3;s:3:\"Apr\";i:4;s:3:\"May\";i:5;s:3:\"Jun\";i:6;s:3:\"Jul\";i:7;s:3:\"Aug\";i:8;s:3:\"Sep\";i:9;s:3:\"Oct\";i:10;s:3:\"Nov\";i:11;s:3:\"Dec\";}s:4:\"data\";a:12:{i:0;i:0;i:1;i:0;i:2;i:0;i:3;i:0;i:4;i:0;i:5;i:0;i:6;i:0;i:7;i:0;i:8;i:0;i:9;i:0;i:10;i:0;i:11;i:0;}s:5:\"title\";s:49:\"ยอดยืมรายเดือน - 2025\";}}', 1759478600, '2025-10-03 07:58:20'),
('app_cacheall_categories', 'O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:1:{i:0;O:19:\"App\\Models\\Category\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:10:\"categories\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:5:{s:2:\"id\";i:1;s:7:\"cate_id\";s:7:\"SFWQ133\";s:4:\"name\";s:3:\"cam\";s:10:\"created_at\";N;s:10:\"updated_at\";N;}s:11:\"\0*\0original\";a:5:{s:2:\"id\";i:1;s:7:\"cate_id\";s:7:\"SFWQ133\";s:4:\"name\";s:3:\"cam\";s:10:\"created_at\";N;s:10:\"updated_at\";N;}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:3:{i:0;s:2:\"id\";i:1;s:7:\"cate_id\";i:2;s:4:\"name\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}', 1759478905, '2025-10-03 07:58:25'),
('app_cachecategories:all', 'O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:1:{i:0;O:19:\"App\\Models\\Category\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:10:\"categories\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:5:{s:2:\"id\";i:1;s:7:\"cate_id\";s:7:\"SFWQ133\";s:4:\"name\";s:3:\"cam\";s:10:\"created_at\";N;s:10:\"updated_at\";N;}s:11:\"\0*\0original\";a:5:{s:2:\"id\";i:1;s:7:\"cate_id\";s:7:\"SFWQ133\";s:4:\"name\";s:3:\"cam\";s:10:\"created_at\";N;s:10:\"updated_at\";N;}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:3:{i:0;s:2:\"id\";i:1;s:7:\"cate_id\";i:2;s:4:\"name\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}', 1759478655, '2025-10-03 07:54:15'),
('app_cacheequipment:CAM0002', 'O:20:\"App\\Models\\Equipment\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:10:\"equipments\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:10:{s:2:\"id\";i:2;s:4:\"code\";s:7:\"CAM0002\";s:4:\"name\";s:5:\"canon\";s:11:\"description\";s:4:\"gges\";s:11:\"accessories\";s:8:\"[{\'ff\'}]\";s:13:\"categories_id\";i:1;s:6:\"status\";s:9:\"available\";s:10:\"photo_path\";s:2:\"[]\";s:10:\"created_at\";N;s:10:\"updated_at\";N;}s:11:\"\0*\0original\";a:10:{s:2:\"id\";i:2;s:4:\"code\";s:7:\"CAM0002\";s:4:\"name\";s:5:\"canon\";s:11:\"description\";s:4:\"gges\";s:11:\"accessories\";s:8:\"[{\'ff\'}]\";s:13:\"categories_id\";i:1;s:6:\"status\";s:9:\"available\";s:10:\"photo_path\";s:2:\"[]\";s:10:\"created_at\";N;s:10:\"updated_at\";N;}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:1:{s:11:\"accessories\";s:5:\"array\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:7:{i:0;s:4:\"code\";i:1;s:4:\"name\";i:2;s:11:\"description\";i:3;s:13:\"categories_id\";i:4;s:6:\"status\";i:5;s:10:\"photo_path\";i:6;s:11:\"accessories\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}', 1759479152, '2025-10-03 08:02:32'),
('app_cacheequipment:CAME111', 'O:20:\"App\\Models\\Equipment\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:10:\"equipments\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:10:{s:2:\"id\";i:1;s:4:\"code\";s:7:\"CAME111\";s:4:\"name\";s:5:\"canon\";s:11:\"description\";s:18:\"fesesesesesesesuck\";s:11:\"accessories\";s:8:\"unik ggg\";s:13:\"categories_id\";i:1;s:6:\"status\";s:9:\"available\";s:10:\"photo_path\";s:2:\"[]\";s:10:\"created_at\";N;s:10:\"updated_at\";N;}s:11:\"\0*\0original\";a:10:{s:2:\"id\";i:1;s:4:\"code\";s:7:\"CAME111\";s:4:\"name\";s:5:\"canon\";s:11:\"description\";s:18:\"fesesesesesesesuck\";s:11:\"accessories\";s:8:\"unik ggg\";s:13:\"categories_id\";i:1;s:6:\"status\";s:9:\"available\";s:10:\"photo_path\";s:2:\"[]\";s:10:\"created_at\";N;s:10:\"updated_at\";N;}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:1:{s:11:\"accessories\";s:5:\"array\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:7:{i:0;s:4:\"code\";i:1;s:4:\"name\";i:2;s:11:\"description\";i:3;s:13:\"categories_id\";i:4;s:6:\"status\";i:5;s:10:\"photo_path\";i:6;s:11:\"accessories\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}', 1759478819, '2025-10-03 07:56:59'),
('app_cacheequipment:SN_001', 'O:20:\"App\\Models\\Equipment\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:10:\"equipments\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:10:{s:2:\"id\";i:3;s:4:\"code\";s:6:\"SN_001\";s:4:\"name\";s:4:\"sony\";s:11:\"description\";s:6:\"ggsony\";s:11:\"accessories\";s:6:\"ggsony\";s:13:\"categories_id\";i:1;s:6:\"status\";s:11:\"unavailable\";s:10:\"photo_path\";s:2:\"[]\";s:10:\"created_at\";N;s:10:\"updated_at\";s:19:\"2025-10-03 07:52:30\";}s:11:\"\0*\0original\";a:10:{s:2:\"id\";i:3;s:4:\"code\";s:6:\"SN_001\";s:4:\"name\";s:4:\"sony\";s:11:\"description\";s:6:\"ggsony\";s:11:\"accessories\";s:6:\"ggsony\";s:13:\"categories_id\";i:1;s:6:\"status\";s:11:\"unavailable\";s:10:\"photo_path\";s:2:\"[]\";s:10:\"created_at\";N;s:10:\"updated_at\";s:19:\"2025-10-03 07:52:30\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:1:{s:11:\"accessories\";s:5:\"array\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:7:{i:0;s:4:\"code\";i:1;s:4:\"name\";i:2;s:11:\"description\";i:3;s:13:\"categories_id\";i:4;s:6:\"status\";i:5;s:10:\"photo_path\";i:6;s:11:\"accessories\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}', 1759478657, '2025-10-03 07:54:17'),
('app_cacheequipments_with_category', 'O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:3:{i:0;O:20:\"App\\Models\\Equipment\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:10:\"equipments\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:10:{s:2:\"id\";i:1;s:4:\"code\";s:7:\"CAME111\";s:4:\"name\";s:5:\"canon\";s:11:\"description\";s:18:\"fesesesesesesesuck\";s:11:\"accessories\";s:8:\"unik ggg\";s:13:\"categories_id\";i:1;s:6:\"status\";s:9:\"available\";s:10:\"photo_path\";s:2:\"[]\";s:10:\"created_at\";N;s:10:\"updated_at\";N;}s:11:\"\0*\0original\";a:10:{s:2:\"id\";i:1;s:4:\"code\";s:7:\"CAME111\";s:4:\"name\";s:5:\"canon\";s:11:\"description\";s:18:\"fesesesesesesesuck\";s:11:\"accessories\";s:8:\"unik ggg\";s:13:\"categories_id\";i:1;s:6:\"status\";s:9:\"available\";s:10:\"photo_path\";s:2:\"[]\";s:10:\"created_at\";N;s:10:\"updated_at\";N;}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:1:{s:11:\"accessories\";s:5:\"array\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:1:{s:8:\"category\";O:19:\"App\\Models\\Category\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:10:\"categories\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:5:{s:2:\"id\";i:1;s:7:\"cate_id\";s:7:\"SFWQ133\";s:4:\"name\";s:3:\"cam\";s:10:\"created_at\";N;s:10:\"updated_at\";N;}s:11:\"\0*\0original\";a:5:{s:2:\"id\";i:1;s:7:\"cate_id\";s:7:\"SFWQ133\";s:4:\"name\";s:3:\"cam\";s:10:\"created_at\";N;s:10:\"updated_at\";N;}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:3:{i:0;s:2:\"id\";i:1;s:7:\"cate_id\";i:2;s:4:\"name\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:7:{i:0;s:4:\"code\";i:1;s:4:\"name\";i:2;s:11:\"description\";i:3;s:13:\"categories_id\";i:4;s:6:\"status\";i:5;s:10:\"photo_path\";i:6;s:11:\"accessories\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:1;O:20:\"App\\Models\\Equipment\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:10:\"equipments\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:10:{s:2:\"id\";i:2;s:4:\"code\";s:7:\"CAM0002\";s:4:\"name\";s:5:\"canon\";s:11:\"description\";s:4:\"gges\";s:11:\"accessories\";s:8:\"[{\'ff\'}]\";s:13:\"categories_id\";i:1;s:6:\"status\";s:9:\"available\";s:10:\"photo_path\";s:2:\"[]\";s:10:\"created_at\";N;s:10:\"updated_at\";N;}s:11:\"\0*\0original\";a:10:{s:2:\"id\";i:2;s:4:\"code\";s:7:\"CAM0002\";s:4:\"name\";s:5:\"canon\";s:11:\"description\";s:4:\"gges\";s:11:\"accessories\";s:8:\"[{\'ff\'}]\";s:13:\"categories_id\";i:1;s:6:\"status\";s:9:\"available\";s:10:\"photo_path\";s:2:\"[]\";s:10:\"created_at\";N;s:10:\"updated_at\";N;}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:1:{s:11:\"accessories\";s:5:\"array\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:1:{s:8:\"category\";r:49;}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:7:{i:0;s:4:\"code\";i:1;s:4:\"name\";i:2;s:11:\"description\";i:3;s:13:\"categories_id\";i:4;s:6:\"status\";i:5;s:10:\"photo_path\";i:6;s:11:\"accessories\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:2;O:20:\"App\\Models\\Equipment\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:10:\"equipments\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:10:{s:2:\"id\";i:3;s:4:\"code\";s:6:\"SN_001\";s:4:\"name\";s:4:\"sony\";s:11:\"description\";s:6:\"ggsony\";s:11:\"accessories\";s:6:\"ggsony\";s:13:\"categories_id\";i:1;s:6:\"status\";s:11:\"unavailable\";s:10:\"photo_path\";s:2:\"[]\";s:10:\"created_at\";N;s:10:\"updated_at\";s:19:\"2025-10-03 07:52:30\";}s:11:\"\0*\0original\";a:10:{s:2:\"id\";i:3;s:4:\"code\";s:6:\"SN_001\";s:4:\"name\";s:4:\"sony\";s:11:\"description\";s:6:\"ggsony\";s:11:\"accessories\";s:6:\"ggsony\";s:13:\"categories_id\";i:1;s:6:\"status\";s:11:\"unavailable\";s:10:\"photo_path\";s:2:\"[]\";s:10:\"created_at\";N;s:10:\"updated_at\";s:19:\"2025-10-03 07:52:30\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:1:{s:11:\"accessories\";s:5:\"array\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:1:{s:8:\"category\";r:49;}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:7:{i:0;s:4:\"code\";i:1;s:4:\"name\";i:2;s:11:\"description\";i:3;s:13:\"categories_id\";i:4;s:6:\"status\";i:5;s:10:\"photo_path\";i:6;s:11:\"accessories\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}', 1759478904, '2025-10-03 07:58:25'),
('app_cacheequipments:page:1:', 'O:42:\"Illuminate\\Pagination\\LengthAwarePaginator\":12:{s:8:\"\0*\0items\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:2:{i:0;O:20:\"App\\Models\\Equipment\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:10:\"equipments\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:10:{s:2:\"id\";i:2;s:4:\"code\";s:7:\"CAM0002\";s:4:\"name\";s:5:\"canon\";s:11:\"description\";s:4:\"gges\";s:11:\"accessories\";s:8:\"[{\'ff\'}]\";s:13:\"categories_id\";i:1;s:6:\"status\";s:9:\"available\";s:10:\"photo_path\";s:2:\"[]\";s:10:\"created_at\";N;s:10:\"updated_at\";N;}s:11:\"\0*\0original\";a:10:{s:2:\"id\";i:2;s:4:\"code\";s:7:\"CAM0002\";s:4:\"name\";s:5:\"canon\";s:11:\"description\";s:4:\"gges\";s:11:\"accessories\";s:8:\"[{\'ff\'}]\";s:13:\"categories_id\";i:1;s:6:\"status\";s:9:\"available\";s:10:\"photo_path\";s:2:\"[]\";s:10:\"created_at\";N;s:10:\"updated_at\";N;}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:1:{s:11:\"accessories\";s:5:\"array\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:7:{i:0;s:4:\"code\";i:1;s:4:\"name\";i:2;s:11:\"description\";i:3;s:13:\"categories_id\";i:4;s:6:\"status\";i:5;s:10:\"photo_path\";i:6;s:11:\"accessories\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:1;O:20:\"App\\Models\\Equipment\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:10:\"equipments\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:10:{s:2:\"id\";i:3;s:4:\"code\";s:6:\"SN_001\";s:4:\"name\";s:4:\"sony\";s:11:\"description\";s:6:\"ggsony\";s:11:\"accessories\";s:6:\"ggsony\";s:13:\"categories_id\";i:1;s:6:\"status\";s:11:\"unavailable\";s:10:\"photo_path\";s:2:\"[]\";s:10:\"created_at\";N;s:10:\"updated_at\";s:19:\"2025-10-03 07:52:30\";}s:11:\"\0*\0original\";a:10:{s:2:\"id\";i:3;s:4:\"code\";s:6:\"SN_001\";s:4:\"name\";s:4:\"sony\";s:11:\"description\";s:6:\"ggsony\";s:11:\"accessories\";s:6:\"ggsony\";s:13:\"categories_id\";i:1;s:6:\"status\";s:11:\"unavailable\";s:10:\"photo_path\";s:2:\"[]\";s:10:\"created_at\";N;s:10:\"updated_at\";s:19:\"2025-10-03 07:52:30\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:1:{s:11:\"accessories\";s:5:\"array\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:7:{i:0;s:4:\"code\";i:1;s:4:\"name\";i:2;s:11:\"description\";i:3;s:13:\"categories_id\";i:4;s:6:\"status\";i:5;s:10:\"photo_path\";i:6;s:11:\"accessories\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}s:10:\"\0*\0perPage\";i:15;s:14:\"\0*\0currentPage\";i:1;s:7:\"\0*\0path\";s:21:\"http://localhost:8000\";s:8:\"\0*\0query\";a:0:{}s:11:\"\0*\0fragment\";N;s:11:\"\0*\0pageName\";s:4:\"page\";s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:10:\"onEachSide\";i:3;s:10:\"\0*\0options\";a:2:{s:4:\"path\";s:21:\"http://localhost:8000\";s:8:\"pageName\";s:4:\"page\";}s:8:\"\0*\0total\";i:2;s:11:\"\0*\0lastPage\";i:1;}', 1759478849, '2025-10-03 08:02:29'),
('app_cachereqdetail:[]', 'O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}', 1759479140, '2025-10-03 08:02:20'),
('app_cachereqdetail:REQKPEX7HOIC0', 'O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:1:{i:0;O:24:\"App\\Models\\BorrowRequest\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:15:\"borrow_requests\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:13:{s:2:\"id\";i:62;s:6:\"req_id\";s:13:\"REQKPEX7HOIC0\";s:8:\"users_id\";i:6;s:13:\"equipments_id\";i:1;s:8:\"start_at\";s:19:\"2025-10-03 00:00:00\";s:6:\"end_at\";s:19:\"2025-10-09 00:00:00\";s:6:\"status\";s:7:\"pending\";s:14:\"request_reason\";s:10:\"assignment\";s:21:\"request_reason_detail\";s:36:\"เทสๆนะนองงงง\";s:13:\"reject_reason\";N;s:13:\"cancel_reason\";N;s:10:\"created_at\";s:19:\"2025-10-03 08:02:19\";s:10:\"updated_at\";s:19:\"2025-10-03 08:02:19\";}s:11:\"\0*\0original\";a:13:{s:2:\"id\";i:62;s:6:\"req_id\";s:13:\"REQKPEX7HOIC0\";s:8:\"users_id\";i:6;s:13:\"equipments_id\";i:1;s:8:\"start_at\";s:19:\"2025-10-03 00:00:00\";s:6:\"end_at\";s:19:\"2025-10-09 00:00:00\";s:6:\"status\";s:7:\"pending\";s:14:\"request_reason\";s:10:\"assignment\";s:21:\"request_reason_detail\";s:36:\"เทสๆนะนองงงง\";s:13:\"reject_reason\";N;s:13:\"cancel_reason\";N;s:10:\"created_at\";s:19:\"2025-10-03 08:02:19\";s:10:\"updated_at\";s:19:\"2025-10-03 08:02:19\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:8:\"start_at\";s:8:\"datetime\";s:6:\"end_at\";s:8:\"datetime\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:9:\"equipment\";O:20:\"App\\Models\\Equipment\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:10:\"equipments\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:6:{s:2:\"id\";i:1;s:4:\"code\";s:7:\"CAME111\";s:4:\"name\";s:5:\"canon\";s:11:\"description\";s:18:\"fesesesesesesesuck\";s:13:\"categories_id\";i:1;s:10:\"photo_path\";s:2:\"[]\";}s:11:\"\0*\0original\";a:6:{s:2:\"id\";i:1;s:4:\"code\";s:7:\"CAME111\";s:4:\"name\";s:5:\"canon\";s:11:\"description\";s:18:\"fesesesesesesesuck\";s:13:\"categories_id\";i:1;s:10:\"photo_path\";s:2:\"[]\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:1:{s:11:\"accessories\";s:5:\"array\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:1:{s:8:\"category\";O:19:\"App\\Models\\Category\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:10:\"categories\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:2:{s:2:\"id\";i:1;s:4:\"name\";s:3:\"cam\";}s:11:\"\0*\0original\";a:2:{s:2:\"id\";i:1;s:4:\"name\";s:3:\"cam\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:3:{i:0;s:2:\"id\";i:1;s:7:\"cate_id\";i:2;s:4:\"name\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:7:{i:0;s:4:\"code\";i:1;s:4:\"name\";i:2;s:11:\"description\";i:3;s:13:\"categories_id\";i:4;s:6:\"status\";i:5;s:10:\"photo_path\";i:6;s:11:\"accessories\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}s:4:\"user\";O:15:\"App\\Models\\User\":35:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:5:\"users\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:5:{s:2:\"id\";i:6;s:3:\"uid\";s:13:\"uidCFIYLAOSAA\";s:4:\"name\";s:19:\"unikzer00@gmail.com\";s:5:\"email\";s:19:\"unikzer00@gmail.com\";s:11:\"phonenumber\";s:10:\"2323232323\";}s:11:\"\0*\0original\";a:5:{s:2:\"id\";i:6;s:3:\"uid\";s:13:\"uidCFIYLAOSAA\";s:4:\"name\";s:19:\"unikzer00@gmail.com\";s:5:\"email\";s:19:\"unikzer00@gmail.com\";s:11:\"phonenumber\";s:10:\"2323232323\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:17:\"email_verified_at\";s:8:\"datetime\";s:8:\"password\";s:6:\"hashed\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:2:{i:0;s:8:\"password\";i:1;s:14:\"remember_token\";}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:6:{i:0;s:3:\"uid\";i:1;s:4:\"name\";i:2;s:5:\"email\";i:3;s:11:\"phonenumber\";i:4;s:8:\"password\";i:5;s:4:\"role\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}s:19:\"\0*\0authPasswordName\";s:8:\"password\";s:20:\"\0*\0rememberTokenName\";s:14:\"remember_token\";}}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:18:{i:0;s:8:\"users_id\";i:1;s:6:\"req_id\";i:2;s:13:\"equipments_id\";i:3;s:8:\"start_at\";i:4;s:6:\"end_at\";i:5;s:6:\"status\";i:6;s:14:\"request_reason\";i:7;s:21:\"request_reason_detail\";i:8;s:13:\"reject_reason\";i:9;s:13:\"cancel_reason\";i:10;s:3:\"uid\";i:11;s:10:\"photo_path\";i:12;s:5:\"email\";i:13;s:11:\"phonenumber\";i:14;s:4:\"code\";i:15;s:4:\"name\";i:16;s:11:\"description\";i:17;s:13:\"categories_id\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}', 1759479140, '2025-10-03 08:02:20');

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint UNSIGNED NOT NULL,
  `cate_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `cate_id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'SFWQ133', 'cam', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `equipments`
--

CREATE TABLE `equipments` (
  `id` bigint UNSIGNED NOT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `accessories` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `categories_id` bigint UNSIGNED NOT NULL,
  `status` enum('available','unavailable','maintenance','retired') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'available',
  `photo_path` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `equipments`
--

INSERT INTO `equipments` (`id`, `code`, `name`, `description`, `accessories`, `categories_id`, `status`, `photo_path`, `created_at`, `updated_at`) VALUES
(1, 'CAME111', 'canon', 'fesesesesesesesuck', 'unik ggg', 1, 'unavailable', '[]', NULL, '2025-10-03 08:02:19'),
(2, 'CAM0002', 'canon', 'gges', '[{\'ff\'}]', 1, 'available', '[]', NULL, NULL),
(3, 'SN_001', 'sony', 'ggsony', 'ggsony', 1, 'unavailable', '[]', NULL, '2025-10-03 07:52:30');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `id` bigint UNSIGNED NOT NULL,
  `admin_id` bigint UNSIGNED NOT NULL,
  `action` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_id` bigint UNSIGNED NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `logs`
--

INSERT INTO `logs` (`id`, `admin_id`, `action`, `target_type`, `target_id`, `description`, `created_at`, `updated_at`) VALUES
(1, 2, 'delete', 'equipment', 1, 'Deleted equipment: กล้อง (ID efsefwe)', '2025-09-11 20:03:15', '2025-09-11 20:03:15'),
(2, 2, 'delete', 'equipment', 18, 'Deleted equipment: Managera (ID NIIJW34ZAD)', '2025-09-11 20:03:58', '2025-09-11 20:03:58'),
(3, 2, 'create', 'equipment', 28, 'Created equipment: bruh (ID 28)', '2025-09-11 20:10:48', '2025-09-11 20:10:48'),
(4, 2, 'update', 'equipment', 2, 'Updated equipment: unika → unikaa (ID 2)', '2025-09-11 20:13:58', '2025-09-11 20:13:58'),
(5, 7, 'delete', 'equipment', 28, 'Deleted equipment: bruh (ID XQEOGFJM58)', '2025-09-12 04:40:44', '2025-09-12 04:40:44'),
(6, 7, 'create', 'equipment', 29, 'Created equipment: 123 (ID 29)', '2025-09-12 05:23:47', '2025-09-12 05:23:47'),
(7, 7, 'update', 'equipment', 2, 'Updated equipment: unikaa → unikaa (ID 2)', '2025-09-12 05:39:09', '2025-09-12 05:39:09');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(59, '0001_01_01_000001_create_cache_table', 1),
(60, '2025_08_19_025801_create_users', 1),
(61, '2025_08_19_025947_create_categories_table', 1),
(62, '2025_08_19_025955_create_equipments_table', 1),
(63, '2025_08_19_025956_create_notification_table', 1),
(64, '2025_08_19_025999_create_borrow_requests_table', 1),
(65, '2025_08_19_026000_create_borrow_transaction_table', 1),
(66, '2025_08_19_075628_add_role_to_users_table', 1),
(67, '2025_08_19_080839_add_two_factor_columns_to_users_table', 1),
(68, '2025_08_20_071746_create_personal_access_tokens_table', 1),
(69, '2025_08_20_134532_add_user_id_to_borrow_requests_table', 2),
(70, '2025_09_01_075311_create_failed_jobs_table', 3),
(71, '2025_09_01_075339_create_jobs_table', 4),
(72, '2025_09_12_023508_create_logs_table', 5),
(73, '2025_10_03_045327_add_missing_columns_to_borrow_requests_table', 6),
(74, '2025_10_03_045538_remove_extra_columns_from_borrow_requests_table', 7),
(75, '2025_10_03_050755_update_request_reason_column_to_string', 8),
(76, '2025_10_03_055408_add_student_id_image_to_verification_requests_table', 9),
(77, '2025_10_03_080000_add_request_reason_detail_to_borrow_requests_table', 10);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint UNSIGNED NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES
('156c1604-c8b6-49e2-a812-a754c9de4386', 'App\\Notifications\\BorrowRequestCreated', 'App\\Models\\User', 2, '{\"request_id\":50,\"equipment\":\"Managera\",\"user\":\"Golf\",\"status\":\"created\",\"message\":\"\\u0e21\\u0e35\\u0e04\\u0e33\\u0e02\\u0e2d\\u0e22\\u0e37\\u0e21\\u0e2d\\u0e38\\u0e1b\\u0e01\\u0e23\\u0e13\\u0e4c\\u0e43\\u0e2b\\u0e21\\u0e48\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/requests\"}', NULL, '2025-09-05 06:34:47', '2025-09-05 06:34:47'),
('1b20931e-4510-4939-946a-b3b2b63cd38f', 'App\\Notifications\\BorrowRequestCreated', 'App\\Models\\User', 7, '{\"request_id\":54,\"equipment\":\"unik ch\",\"user\":\"Golf\",\"status\":\"created\",\"message\":\"\\u0e21\\u0e35\\u0e04\\u0e33\\u0e02\\u0e2d\\u0e22\\u0e37\\u0e21\\u0e2d\\u0e38\\u0e1b\\u0e01\\u0e23\\u0e13\\u0e4c\\u0e43\\u0e2b\\u0e21\\u0e48\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/requests\"}', NULL, '2025-09-09 07:00:26', '2025-09-09 07:00:26'),
('1b9fd544-d385-4690-a9d1-c59ca7e8dfef', 'App\\Notifications\\BorrowRequestApproved', 'App\\Models\\User', 7, '{\"request_id\":52,\"equipment\":\"123\",\"message\":\"\\u0e04\\u0e33\\u0e02\\u0e2d\\u0e22\\u0e37\\u0e21\\u0e02\\u0e2d\\u0e07\\u0e04\\u0e38\\u0e13\\u0e44\\u0e14\\u0e49\\u0e23\\u0e31\\u0e1a\\u0e01\\u0e32\\u0e23\\u0e2d\\u0e19\\u0e38\\u0e21\\u0e31\\u0e15\\u0e34\\u0e41\\u0e25\\u0e49\\u0e27\",\"status\":\"approved\",\"type\":\"borrow_request_approved\",\"created_at\":\"2025-09-07 06:53:23\"}', NULL, '2025-09-07 06:53:23', '2025-09-07 06:53:23'),
('2b082d19-c007-4aac-b016-de1875354346', 'App\\Notifications\\BorrowRequestCreated', 'App\\Models\\User', 2, '{\"request_id\":53,\"equipment\":\"camel\",\"user\":\"Golf\",\"status\":\"created\",\"message\":\"\\u0e21\\u0e35\\u0e04\\u0e33\\u0e02\\u0e2d\\u0e22\\u0e37\\u0e21\\u0e2d\\u0e38\\u0e1b\\u0e01\\u0e23\\u0e13\\u0e4c\\u0e43\\u0e2b\\u0e21\\u0e48\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/requests\"}', NULL, '2025-09-07 06:54:49', '2025-09-07 06:54:49'),
('2be259bf-8e56-48a0-88e1-f54785b3007c', 'App\\Notifications\\BorrowRequestCreated', 'App\\Models\\User', 7, '{\"request_id\":53,\"equipment\":\"camel\",\"user\":\"Golf\",\"status\":\"created\",\"message\":\"\\u0e21\\u0e35\\u0e04\\u0e33\\u0e02\\u0e2d\\u0e22\\u0e37\\u0e21\\u0e2d\\u0e38\\u0e1b\\u0e01\\u0e23\\u0e13\\u0e4c\\u0e43\\u0e2b\\u0e21\\u0e48\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/requests\"}', NULL, '2025-09-07 06:54:49', '2025-09-07 06:54:49'),
('595e22ea-a723-4049-9a56-c1bcc6404d37', 'App\\Notifications\\BorrowRequestApproved', 'App\\Models\\User', 3, '{\"request_id\":42,\"equipment\":\"\\u0e01\\u0e25\\u0e49\\u0e2d\\u0e07\",\"message\":\"\\u0e04\\u0e33\\u0e02\\u0e2d\\u0e22\\u0e37\\u0e21\\u0e02\\u0e2d\\u0e07\\u0e04\\u0e38\\u0e13\\u0e44\\u0e14\\u0e49\\u0e23\\u0e31\\u0e1a\\u0e01\\u0e32\\u0e23\\u0e2d\\u0e19\\u0e38\\u0e21\\u0e31\\u0e15\\u0e34\\u0e41\\u0e25\\u0e49\\u0e27\",\"type\":\"borrow_request_approved\",\"created_at\":\"2025-09-01 08:10:00\"}', NULL, '2025-09-01 08:10:00', '2025-09-01 08:10:00'),
('5996e3f5-91b5-4399-84ac-c737c3838997', 'App\\Notifications\\BorrowRequestCreated', 'App\\Models\\User', 2, '{\"request_id\":54,\"equipment\":\"unik ch\",\"user\":\"Golf\",\"status\":\"created\",\"message\":\"\\u0e21\\u0e35\\u0e04\\u0e33\\u0e02\\u0e2d\\u0e22\\u0e37\\u0e21\\u0e2d\\u0e38\\u0e1b\\u0e01\\u0e23\\u0e13\\u0e4c\\u0e43\\u0e2b\\u0e21\\u0e48\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/requests\"}', NULL, '2025-09-09 07:00:26', '2025-09-09 07:00:26'),
('6bd85e58-09d0-4c5e-8459-ceffde5a34ea', 'App\\Notifications\\BorrowRequestCreated', 'App\\Models\\User', 7, '{\"request_id\":52,\"equipment\":\"123\",\"user\":\"Golf\",\"status\":\"created\",\"message\":\"\\u0e21\\u0e35\\u0e04\\u0e33\\u0e02\\u0e2d\\u0e22\\u0e37\\u0e21\\u0e2d\\u0e38\\u0e1b\\u0e01\\u0e23\\u0e13\\u0e4c\\u0e43\\u0e2b\\u0e21\\u0e48\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/requests\"}', NULL, '2025-09-07 06:51:30', '2025-09-07 06:51:30'),
('72eec621-70f4-4cb6-8cd0-e803c3467f15', 'App\\Notifications\\BorrowRequestCreated', 'App\\Models\\User', 7, '{\"request_id\":55,\"equipment\":\"Managera\",\"user\":\"Golf\",\"status\":\"created\",\"message\":\"\\u0e21\\u0e35\\u0e04\\u0e33\\u0e02\\u0e2d\\u0e22\\u0e37\\u0e21\\u0e2d\\u0e38\\u0e1b\\u0e01\\u0e23\\u0e13\\u0e4c\\u0e43\\u0e2b\\u0e21\\u0e48\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/requests\"}', NULL, '2025-09-11 07:31:04', '2025-09-11 07:31:04'),
('754413f5-a9b8-4010-9ed6-7f74850ff64f', 'App\\Notifications\\BorrowRequestRejected', 'App\\Models\\User', 7, '{\"request_id\":45,\"equipment\":\"\\u0e01\\u0e25\\u0e49\\u0e2d\\u0e07\",\"status\":\"rejected\",\"message\":\"\\u0e04\\u0e33\\u0e02\\u0e2d\\u0e22\\u0e37\\u0e21\\u0e02\\u0e2d\\u0e07\\u0e04\\u0e38\\u0e13\\u0e16\\u0e39\\u0e01\\u0e1b\\u0e0f\\u0e34\\u0e40\\u0e2a\\u0e18\",\"reason\":\"idk\",\"type\":\"borrow_request_rejected\",\"created_at\":\"2025-09-04 04:09:12\"}', '2025-09-05 06:37:46', '2025-09-04 04:09:12', '2025-09-05 06:37:46'),
('7b415c5a-a76d-4c4f-8b78-d2753e0da403', 'App\\Notifications\\BorrowRequestCreated', 'App\\Models\\User', 2, '{\"request_id\":45,\"equipment\":\"\\u0e01\\u0e25\\u0e49\\u0e2d\\u0e07\",\"user\":\"Golf\",\"status\":\"created\",\"message\":\"\\u0e21\\u0e35\\u0e04\\u0e33\\u0e02\\u0e2d\\u0e22\\u0e37\\u0e21\\u0e2d\\u0e38\\u0e1b\\u0e01\\u0e23\\u0e13\\u0e4c\\u0e43\\u0e2b\\u0e21\\u0e48\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/requests\"}', NULL, '2025-09-03 09:15:32', '2025-09-03 09:15:32'),
('81667619-dd20-42e9-8560-88e82ba4ec7e', 'App\\Notifications\\BorrowRequestApproved', 'App\\Models\\User', 7, '{\"request_id\":50,\"equipment\":\"Managera\",\"message\":\"\\u0e04\\u0e33\\u0e02\\u0e2d\\u0e22\\u0e37\\u0e21\\u0e02\\u0e2d\\u0e07\\u0e04\\u0e38\\u0e13\\u0e44\\u0e14\\u0e49\\u0e23\\u0e31\\u0e1a\\u0e01\\u0e32\\u0e23\\u0e2d\\u0e19\\u0e38\\u0e21\\u0e31\\u0e15\\u0e34\\u0e41\\u0e25\\u0e49\\u0e27\",\"status\":\"approved\",\"type\":\"borrow_request_approved\",\"created_at\":\"2025-09-05 06:36:06\"}', '2025-09-05 06:37:46', '2025-09-05 06:36:06', '2025-09-05 06:37:46'),
('8bfcf80f-1875-4f27-8d12-522d2d23d1b7', 'App\\Notifications\\BorrowRequestCreated', 'App\\Models\\User', 7, '{\"request_id\":45,\"equipment\":\"\\u0e01\\u0e25\\u0e49\\u0e2d\\u0e07\",\"user\":\"Golf\",\"status\":\"created\",\"message\":\"\\u0e21\\u0e35\\u0e04\\u0e33\\u0e02\\u0e2d\\u0e22\\u0e37\\u0e21\\u0e2d\\u0e38\\u0e1b\\u0e01\\u0e23\\u0e13\\u0e4c\\u0e43\\u0e2b\\u0e21\\u0e48\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/requests\"}', '2025-09-05 06:37:47', '2025-09-03 09:15:32', '2025-09-05 06:37:47'),
('8f325831-8d6b-4dbd-bf6f-c81a5b60a072', 'App\\Notifications\\BorrowRequestCreated', 'App\\Models\\User', 7, '{\"request_id\":51,\"equipment\":\"Managera\",\"user\":\"Golf\",\"status\":\"created\",\"message\":\"\\u0e21\\u0e35\\u0e04\\u0e33\\u0e02\\u0e2d\\u0e22\\u0e37\\u0e21\\u0e2d\\u0e38\\u0e1b\\u0e01\\u0e23\\u0e13\\u0e4c\\u0e43\\u0e2b\\u0e21\\u0e48\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/requests\"}', NULL, '2025-09-05 06:38:09', '2025-09-05 06:38:09'),
('9ed34e81-038a-40e8-9c78-7a4be06fe127', 'App\\Notifications\\BorrowRequestCreated', 'App\\Models\\User', 7, '{\"request_id\":50,\"equipment\":\"Managera\",\"user\":\"Golf\",\"status\":\"created\",\"message\":\"\\u0e21\\u0e35\\u0e04\\u0e33\\u0e02\\u0e2d\\u0e22\\u0e37\\u0e21\\u0e2d\\u0e38\\u0e1b\\u0e01\\u0e23\\u0e13\\u0e4c\\u0e43\\u0e2b\\u0e21\\u0e48\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/requests\"}', '2025-09-05 06:37:46', '2025-09-05 06:34:47', '2025-09-05 06:37:46'),
('9f5b2d04-c37f-48ac-85f5-d39781e3ca68', 'App\\Notifications\\BorrowRequestCreated', 'App\\Models\\User', 2, '{\"request_id\":44,\"equipment\":\"\\u0e01\\u0e25\\u0e49\\u0e2d\\u0e07\",\"user\":\"Golf\",\"message\":\"\\u0e21\\u0e35\\u0e04\\u0e33\\u0e02\\u0e2d\\u0e22\\u0e37\\u0e21\\u0e2d\\u0e38\\u0e1b\\u0e01\\u0e23\\u0e13\\u0e4c\\u0e43\\u0e2b\\u0e21\\u0e48\"}', NULL, '2025-09-01 09:04:11', '2025-09-01 09:04:11'),
('a433e1b8-1945-40fd-b4d4-a7136fe76a4d', 'App\\Notifications\\BorrowRequestRejected', 'App\\Models\\User', 7, '{\"request_id\":44,\"equipment\":\"\\u0e01\\u0e25\\u0e49\\u0e2d\\u0e07\",\"status\":\"rejected\",\"message\":\"\\u0e04\\u0e33\\u0e02\\u0e2d\\u0e22\\u0e37\\u0e21\\u0e02\\u0e2d\\u0e07\\u0e04\\u0e38\\u0e13\\u0e16\\u0e39\\u0e01\\u0e1b\\u0e0f\\u0e34\\u0e40\\u0e2a\\u0e18\",\"reason\":\"\\u0e42\\u0e04\\u0e15\\u0e23\\u0e01\\u0e32\\u0e01\\u0e21\\u0e36\\u0e07\\u0e2d\\u0e30\",\"type\":\"borrow_request_rejected\",\"created_at\":\"2025-09-03 08:46:08\"}', '2025-09-03 08:47:01', '2025-09-03 08:46:08', '2025-09-03 08:47:01'),
('a8091518-61ad-4e68-a09d-cfadb3cbc610', 'App\\Notifications\\BorrowRequestCreated', 'App\\Models\\User', 2, '{\"request_id\":42,\"equipment\":\"\\u0e01\\u0e25\\u0e49\\u0e2d\\u0e07\",\"user\":\"unik ch\",\"message\":\"\\u0e21\\u0e35\\u0e04\\u0e33\\u0e02\\u0e2d\\u0e22\\u0e37\\u0e21\\u0e2d\\u0e38\\u0e1b\\u0e01\\u0e23\\u0e13\\u0e4c\\u0e43\\u0e2b\\u0e21\\u0e48\"}', NULL, '2025-09-01 08:08:38', '2025-09-01 08:08:38'),
('b6dcd8ba-f573-45f8-837c-037fa68aaf8a', 'App\\Notifications\\BorrowRequestCreated', 'App\\Models\\User', 2, '{\"request_id\":51,\"equipment\":\"Managera\",\"user\":\"Golf\",\"status\":\"created\",\"message\":\"\\u0e21\\u0e35\\u0e04\\u0e33\\u0e02\\u0e2d\\u0e22\\u0e37\\u0e21\\u0e2d\\u0e38\\u0e1b\\u0e01\\u0e23\\u0e13\\u0e4c\\u0e43\\u0e2b\\u0e21\\u0e48\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/requests\"}', NULL, '2025-09-05 06:38:09', '2025-09-05 06:38:09'),
('c98875fc-0852-4593-b0b3-e2a3d046d3d2', 'App\\Notifications\\BorrowRequestCreated', 'App\\Models\\User', 2, '{\"request_id\":52,\"equipment\":\"123\",\"user\":\"Golf\",\"status\":\"created\",\"message\":\"\\u0e21\\u0e35\\u0e04\\u0e33\\u0e02\\u0e2d\\u0e22\\u0e37\\u0e21\\u0e2d\\u0e38\\u0e1b\\u0e01\\u0e23\\u0e13\\u0e4c\\u0e43\\u0e2b\\u0e21\\u0e48\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/requests\"}', NULL, '2025-09-07 06:51:30', '2025-09-07 06:51:30'),
('ca56825e-dd6a-4dd6-9501-4a99c6c0f7d4', 'App\\Notifications\\BorrowRequestCreated', 'App\\Models\\User', 7, '{\"request_id\":44,\"equipment\":\"\\u0e01\\u0e25\\u0e49\\u0e2d\\u0e07\",\"user\":\"Golf\",\"message\":\"\\u0e21\\u0e35\\u0e04\\u0e33\\u0e02\\u0e2d\\u0e22\\u0e37\\u0e21\\u0e2d\\u0e38\\u0e1b\\u0e01\\u0e23\\u0e13\\u0e4c\\u0e43\\u0e2b\\u0e21\\u0e48\"}', '2025-09-03 08:47:03', '2025-09-01 09:04:11', '2025-09-03 08:47:03'),
('cfbabdd7-ea07-4cca-9a13-0f22ee8931be', 'App\\Notifications\\BorrowRequestCreated', 'App\\Models\\User', 2, '{\"request_id\":55,\"equipment\":\"Managera\",\"user\":\"Golf\",\"status\":\"created\",\"message\":\"\\u0e21\\u0e35\\u0e04\\u0e33\\u0e02\\u0e2d\\u0e22\\u0e37\\u0e21\\u0e2d\\u0e38\\u0e1b\\u0e01\\u0e23\\u0e13\\u0e4c\\u0e43\\u0e2b\\u0e21\\u0e48\",\"url\":\"http:\\/\\/127.0.0.1:8000\\/admin\\/requests\"}', NULL, '2025-09-11 07:31:04', '2025-09-11 07:31:04'),
('d0dd31db-0598-4170-bf6d-988a764e89d6', 'App\\Notifications\\BorrowRequestApproved', 'App\\Models\\User', 7, '{\"request_id\":46,\"equipment\":\"123\",\"message\":\"\\u0e04\\u0e33\\u0e02\\u0e2d\\u0e22\\u0e37\\u0e21\\u0e02\\u0e2d\\u0e07\\u0e04\\u0e38\\u0e13\\u0e44\\u0e14\\u0e49\\u0e23\\u0e31\\u0e1a\\u0e01\\u0e32\\u0e23\\u0e2d\\u0e19\\u0e38\\u0e21\\u0e31\\u0e15\\u0e34\\u0e41\\u0e25\\u0e49\\u0e27\",\"status\":\"approved\",\"type\":\"borrow_request_approved\",\"created_at\":\"2025-09-05 05:53:17\"}', '2025-09-05 06:37:46', '2025-09-05 05:53:17', '2025-09-05 06:37:46'),
('eb53c41b-15ba-440d-9eb3-24e71fc1b123', 'App\\Notifications\\BorrowRequestApproved', 'App\\Models\\User', 7, '{\"request_id\":51,\"equipment\":\"Managera\",\"message\":\"\\u0e04\\u0e33\\u0e02\\u0e2d\\u0e22\\u0e37\\u0e21\\u0e02\\u0e2d\\u0e07\\u0e04\\u0e38\\u0e13\\u0e44\\u0e14\\u0e49\\u0e23\\u0e31\\u0e1a\\u0e01\\u0e32\\u0e23\\u0e2d\\u0e19\\u0e38\\u0e21\\u0e31\\u0e15\\u0e34\\u0e41\\u0e25\\u0e49\\u0e27\",\"status\":\"approved\",\"type\":\"borrow_request_approved\",\"created_at\":\"2025-09-05 06:38:32\"}', NULL, '2025-09-05 06:38:32', '2025-09-05 06:38:32');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('9V2IdT5lvciSZCOCPyt1jdonAtE6zGLhybnXr6av', 7, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoicGRrZ3Rza2xoUmNnRkxrVWlPYjNlWnhGVk1sbU5OOEh0RDVpZHRkRyI7czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6NztzOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czozOToiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2FkbWluL3RyYW5zYWN0aW9uIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1757835538),
('GPQFZvWmI4Nh3yGe8P4wI7it132V7zUgtAQypQkj', 7, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiY3Z5eHBxUDI1ZU96bWNWSVBsSTA4aHFTVnpEVkFxdmhiQjc0bHRpVyI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjM3OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYWRtaW4vZXF1aXBtZW50Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Nzt9', 1757655013),
('RU5PURMNkkHJdNyYKhM4PEI8J2epNWMKPSDVoI6d', 7, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiUmtzdElTN1NRUTlzbXRQUmkzVHhxV3YzdUM4bWxqUHJydXc0dVR4USI7czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6NztzOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czozOToiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2FkbWluL3RyYW5zYWN0aW9uIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1757835168),
('vywaiI88HpY24E96Iq3pCjpbVTXVmSQ7GqV6b56f', 8, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoia2JvRWtRelYzM2txNmE4djNvcEdvU2hQVnBHblZBR0VhZVdENWVTQSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjg7fQ==', 1757656112),
('yw8dLjOWPHTsubDD2JpIKeFbzhRMa3GSfAvfjnFk', 7, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiNUVqczIwR3BORFk5YVZPYUZlNHFkNmNRUWVFdnRET0NlZ2RJN0hwayI7czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6NztzOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czozNzoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2FkbWluL2VxdWlwbWVudCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1757652471);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `uid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `phonenumber` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `two_factor_secret` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `two_factor_recovery_codes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `two_factor_confirmed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `role` enum('admin','staff','borrower') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'borrower',
  `ip_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `uid`, `name`, `email`, `email_verified_at`, `phonenumber`, `password`, `remember_token`, `two_factor_secret`, `two_factor_recovery_codes`, `two_factor_confirmed_at`, `created_at`, `updated_at`, `role`, `ip_address`) VALUES
(2, '01K37WKMG3Y7D0HPJF16A6K06B', 'admin', 'admin@user.com', NULL, '0000000001', '$2y$12$6tpXz97yKRXMLa3A9./FguQY1rSKGInzjieXEWSTaed/lBzXvEn5G', NULL, NULL, NULL, NULL, '2025-08-22 03:19:26', '2025-08-22 03:19:26', 'admin', ''),
(3, 'uidKYYP2M6EPH', 'unik ch', 'borrower@user.com', NULL, '000000987', '$2y$12$Mofypp1Fcaiuq5ryGLulc.WLXQmlgfCk2309gLESRnLPqvb9cWeU.', NULL, NULL, NULL, NULL, '2025-08-22 04:34:23', '2025-08-22 04:34:23', 'borrower', '127.0.0.1'),
(5, 'uid33JM6BJA9W', 'user', 'user@user.com', NULL, '2345677772', '$2y$12$55S/JPMOsYIxIgOARNF4HOarF3vymgtIBCjy/omRuM7G397Dqw0sS', NULL, NULL, NULL, NULL, '2025-08-25 08:57:26', '2025-08-25 08:57:26', 'borrower', '127.0.0.1'),
(6, 'uidCFIYLAOSAA', 'unikzer00@gmail.com', 'unikzer00@gmail.com', NULL, '2323232323', '$2y$12$HnCHDkte9F4qrX5c7gQJ8uDuNElzv0rrxgdAGnnlnL1OHBOrymtJS', NULL, NULL, NULL, NULL, '2025-08-26 08:26:54', '2025-08-26 08:26:54', 'borrower', '127.0.0.1'),
(7, 'uid9GKSYASDIT', 'Golf', 'thaksin.mnv785@gmail.com', NULL, '0816278304', '$2y$12$xLnrWMk0yX95yjUGRbm8HOabce3XG6Jr4gwbcQnHXEXjg4wjuzECS', 'BSlVZSvGtQwif1dH8s1pKSqdJstC8MWZ8lU7UmzQKiqUp60PVWSyzWa4Qiey', NULL, NULL, NULL, '2025-09-01 08:31:21', '2025-09-01 08:31:21', 'admin', '127.0.0.1'),
(8, 'TH123', 'Thaksinh', 'kobl44246@gmail.com', NULL, '1234567800', '$2y$12$YQK/ZoKF3IuSjYZGCiLsaedBqWGT04AxCBhtdzmlE8k0/kR4m1zwS', NULL, NULL, NULL, NULL, '2025-09-12 05:48:31', '2025-09-12 05:48:31', 'borrower', '127.0.0.1'),
(9, 'unik09', 'unik', 'unik09john@gmail.com', NULL, '0201234567', '$2y$12$t4fnnocSiGFKvfP9vtJpSOocdLSyQ3.m0HUel1EC2ChESzsBvpDqO', NULL, NULL, NULL, NULL, '2025-10-03 04:02:40', '2025-10-03 04:02:40', 'admin', '127.0.0.1');

-- --------------------------------------------------------

--
-- Table structure for table `verification_requests`
--

CREATE TABLE `verification_requests` (
  `id` int UNSIGNED NOT NULL,
  `users_id` bigint UNSIGNED NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `student_id_image_path` text,
  `reject_note` text,
  `processed_by` bigint DEFAULT NULL,
  `process_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `verification_requests`
--

INSERT INTO `verification_requests` (`id`, `users_id`, `status`, `student_id_image_path`, `reject_note`, `processed_by`, `process_at`, `created_at`, `updated_at`) VALUES
(5, 6, 'approved', '/storage/verification/1759472801_unik3.jfif', NULL, NULL, '2025-10-03 06:26:41', '2025-10-03 06:26:41', '2025-10-03 06:26:41'),
(6, 9, 'approved', '/storage/verification/1759474140_thorfinn.jfif', NULL, NULL, '2025-10-03 06:49:00', '2025-10-03 06:49:00', '2025-10-03 06:49:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `borrow_requests`
--
ALTER TABLE `borrow_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `borrow_requests_users_id_foreign` (`users_id`),
  ADD KEY `borrow_requests_equipments_id_foreign` (`equipments_id`);

--
-- Indexes for table `borrow_transactions`
--
ALTER TABLE `borrow_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `borrow_transaction_borrow_requests_id_foreign` (`borrow_requests_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `equipments`
--
ALTER TABLE `equipments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code_unique` (`code`),
  ADD KEY `equipments_categories_id_fk` (`categories_id`) USING BTREE;

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_uid_unique` (`uid`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_phonenumber_unique` (`phonenumber`);

--
-- Indexes for table `verification_requests`
--
ALTER TABLE `verification_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_verification_requests_users_id` (`users_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `borrow_requests`
--
ALTER TABLE `borrow_requests`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `borrow_transactions`
--
ALTER TABLE `borrow_transactions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `equipments`
--
ALTER TABLE `equipments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `verification_requests`
--
ALTER TABLE `verification_requests`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `borrow_requests`
--
ALTER TABLE `borrow_requests`
  ADD CONSTRAINT `borrow_requests_equipments_id_foreign` FOREIGN KEY (`equipments_id`) REFERENCES `equipments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `borrow_requests_users_id_foreign` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `borrow_transactions`
--
ALTER TABLE `borrow_transactions`
  ADD CONSTRAINT `borrow_transaction_borrow_requests_id_foreign` FOREIGN KEY (`borrow_requests_id`) REFERENCES `borrow_requests` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `equipments`
--
ALTER TABLE `equipments`
  ADD CONSTRAINT `equipments_categories_id_foreign` FOREIGN KEY (`categories_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `verification_requests`
--
ALTER TABLE `verification_requests`
  ADD CONSTRAINT `fk_verification_requests_users_id` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
