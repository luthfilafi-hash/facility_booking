-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 09, 2026 at 04:46 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `facility_booking`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `action` varchar(255) NOT NULL,
  `details` text NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `details`, `ip_address`, `created`) VALUES
(1, 5, 'Update Booking', 'Changed facility booking #14 status to rejected.', '127.0.0.1', '2026-07-06 11:38:40'),
(2, 5, 'Update Booking', 'Changed facility booking #14 status to approved.', '127.0.0.1', '2026-07-06 11:44:11'),
(3, 5, 'Update Booking', 'Changed facility booking #15 status to rejected.', '127.0.0.1', '2026-07-06 11:44:26'),
(4, 5, 'Update Maintenance', 'Changed maintenance #2 status to completed.', '127.0.0.1', '2026-07-09 09:57:48'),
(5, 5, 'Add Maintenance', 'Scheduled maintenance #3 for facility #4.', '127.0.0.1', '2026-07-09 10:00:14'),
(6, 5, 'Update Booking', 'Changed facility booking #12 status to approved.', '127.0.0.1', '2026-07-09 10:34:58');

-- --------------------------------------------------------

--
-- Table structure for table `availability`
--

CREATE TABLE `availability` (
  `id` int NOT NULL,
  `facility_id` int DEFAULT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `day_of_week` varchar(20) DEFAULT NULL,
  `status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `availability`
--

INSERT INTO `availability` (`id`, `facility_id`, `start_time`, `end_time`, `day_of_week`, `status`) VALUES
(1, 1, '08:00:00', '10:00:00', 'Monday', 'available'),
(2, 1, '10:00:00', '12:00:00', 'Monday', 'available'),
(3, 1, '12:00:00', '14:00:00', 'Monday', 'available'),
(4, 1, '14:00:00', '16:00:00', 'Monday', 'available'),
(5, 1, '16:00:00', '18:00:00', 'Monday', 'available'),
(6, 1, '08:00:00', '10:00:00', 'Tuesday', 'available'),
(7, 1, '10:00:00', '12:00:00', 'Tuesday', 'available'),
(8, 1, '12:00:00', '14:00:00', 'Tuesday', 'available'),
(9, 1, '14:00:00', '16:00:00', 'Tuesday', 'available'),
(10, 1, '16:00:00', '18:00:00', 'Tuesday', 'available'),
(11, 1, '08:00:00', '10:00:00', 'Wednesday', 'available'),
(12, 1, '10:00:00', '12:00:00', 'Wednesday', 'available'),
(13, 1, '12:00:00', '14:00:00', 'Wednesday', 'available'),
(14, 1, '14:00:00', '16:00:00', 'Wednesday', 'available'),
(15, 1, '16:00:00', '18:00:00', 'Wednesday', 'available'),
(16, 1, '08:00:00', '10:00:00', 'Thursday', 'available'),
(17, 1, '10:00:00', '12:00:00', 'Thursday', 'available'),
(18, 1, '12:00:00', '14:00:00', 'Thursday', 'available'),
(19, 1, '14:00:00', '16:00:00', 'Thursday', 'available'),
(20, 1, '16:00:00', '18:00:00', 'Thursday', 'available'),
(21, 1, '08:00:00', '10:00:00', 'Friday', 'available'),
(22, 1, '10:00:00', '12:00:00', 'Friday', 'available'),
(23, 1, '12:00:00', '14:00:00', 'Friday', 'available'),
(24, 1, '14:00:00', '16:00:00', 'Friday', 'available'),
(25, 1, '16:00:00', '18:00:00', 'Friday', 'available'),
(26, 2, '08:00:00', '10:00:00', 'Monday', 'available'),
(27, 2, '10:00:00', '12:00:00', 'Monday', 'available'),
(28, 2, '12:00:00', '14:00:00', 'Monday', 'available'),
(29, 2, '14:00:00', '16:00:00', 'Monday', 'available'),
(30, 2, '16:00:00', '18:00:00', 'Monday', 'available'),
(31, 2, '08:00:00', '10:00:00', 'Tuesday', 'available'),
(32, 2, '10:00:00', '12:00:00', 'Tuesday', 'available'),
(33, 2, '12:00:00', '14:00:00', 'Tuesday', 'available'),
(34, 2, '14:00:00', '16:00:00', 'Tuesday', 'available'),
(35, 2, '16:00:00', '18:00:00', 'Tuesday', 'available'),
(36, 2, '08:00:00', '10:00:00', 'Wednesday', 'available'),
(37, 2, '10:00:00', '12:00:00', 'Wednesday', 'available'),
(38, 2, '12:00:00', '14:00:00', 'Wednesday', 'available'),
(39, 2, '14:00:00', '16:00:00', 'Wednesday', 'available'),
(40, 2, '16:00:00', '18:00:00', 'Wednesday', 'available'),
(41, 2, '08:00:00', '10:00:00', 'Thursday', 'available'),
(42, 2, '10:00:00', '12:00:00', 'Thursday', 'available'),
(43, 2, '12:00:00', '14:00:00', 'Thursday', 'available'),
(44, 2, '14:00:00', '16:00:00', 'Thursday', 'available'),
(45, 2, '16:00:00', '18:00:00', 'Thursday', 'available'),
(46, 2, '08:00:00', '10:00:00', 'Friday', 'available'),
(47, 2, '10:00:00', '12:00:00', 'Friday', 'available'),
(48, 2, '12:00:00', '14:00:00', 'Friday', 'available'),
(49, 2, '14:00:00', '16:00:00', 'Friday', 'available'),
(50, 2, '16:00:00', '18:00:00', 'Friday', 'available'),
(51, 3, '08:00:00', '10:00:00', 'Monday', 'available'),
(52, 3, '10:00:00', '12:00:00', 'Monday', 'available'),
(53, 3, '12:00:00', '14:00:00', 'Monday', 'available'),
(54, 3, '14:00:00', '16:00:00', 'Monday', 'available'),
(55, 3, '16:00:00', '18:00:00', 'Monday', 'available'),
(56, 3, '08:00:00', '10:00:00', 'Tuesday', 'available'),
(57, 3, '10:00:00', '12:00:00', 'Tuesday', 'available'),
(58, 3, '12:00:00', '14:00:00', 'Tuesday', 'available'),
(59, 3, '14:00:00', '16:00:00', 'Tuesday', 'available'),
(60, 3, '16:00:00', '18:00:00', 'Tuesday', 'available'),
(61, 3, '08:00:00', '10:00:00', 'Wednesday', 'available'),
(62, 3, '10:00:00', '12:00:00', 'Wednesday', 'available'),
(63, 3, '12:00:00', '14:00:00', 'Wednesday', 'available'),
(64, 3, '14:00:00', '16:00:00', 'Wednesday', 'available'),
(65, 3, '16:00:00', '18:00:00', 'Wednesday', 'available'),
(66, 3, '08:00:00', '10:00:00', 'Thursday', 'available'),
(67, 3, '10:00:00', '12:00:00', 'Thursday', 'available'),
(68, 3, '12:00:00', '14:00:00', 'Thursday', 'available'),
(69, 3, '14:00:00', '16:00:00', 'Thursday', 'available'),
(70, 3, '16:00:00', '18:00:00', 'Thursday', 'available'),
(71, 3, '08:00:00', '10:00:00', 'Friday', 'available'),
(72, 3, '10:00:00', '12:00:00', 'Friday', 'available'),
(73, 3, '12:00:00', '14:00:00', 'Friday', 'available'),
(74, 3, '14:00:00', '16:00:00', 'Friday', 'available'),
(75, 3, '16:00:00', '18:00:00', 'Friday', 'available'),
(76, 4, '08:00:00', '10:00:00', 'Monday', 'available'),
(77, 4, '10:00:00', '12:00:00', 'Monday', 'available'),
(78, 4, '12:00:00', '14:00:00', 'Monday', 'available'),
(79, 4, '14:00:00', '16:00:00', 'Monday', 'available'),
(80, 4, '16:00:00', '18:00:00', 'Monday', 'available'),
(81, 4, '08:00:00', '10:00:00', 'Tuesday', 'available'),
(82, 4, '10:00:00', '12:00:00', 'Tuesday', 'available'),
(83, 4, '12:00:00', '14:00:00', 'Tuesday', 'available'),
(84, 4, '14:00:00', '16:00:00', 'Tuesday', 'available'),
(85, 4, '16:00:00', '18:00:00', 'Tuesday', 'available'),
(86, 4, '08:00:00', '10:00:00', 'Wednesday', 'available'),
(87, 4, '10:00:00', '12:00:00', 'Wednesday', 'available'),
(88, 4, '12:00:00', '14:00:00', 'Wednesday', 'available'),
(89, 4, '14:00:00', '16:00:00', 'Wednesday', 'available'),
(90, 4, '16:00:00', '18:00:00', 'Wednesday', 'available'),
(91, 4, '08:00:00', '10:00:00', 'Thursday', 'available'),
(92, 4, '10:00:00', '12:00:00', 'Thursday', 'available'),
(93, 4, '12:00:00', '14:00:00', 'Thursday', 'available'),
(94, 4, '14:00:00', '16:00:00', 'Thursday', 'available'),
(95, 4, '16:00:00', '18:00:00', 'Thursday', 'available'),
(96, 4, '08:00:00', '10:00:00', 'Friday', 'available'),
(97, 4, '10:00:00', '12:00:00', 'Friday', 'available'),
(98, 4, '12:00:00', '14:00:00', 'Friday', 'available'),
(99, 4, '14:00:00', '16:00:00', 'Friday', 'available'),
(100, 4, '16:00:00', '18:00:00', 'Friday', 'available'),
(101, 5, '08:00:00', '10:00:00', 'Monday', 'available'),
(102, 5, '10:00:00', '12:00:00', 'Monday', 'available'),
(103, 5, '12:00:00', '14:00:00', 'Monday', 'available'),
(104, 5, '14:00:00', '16:00:00', 'Monday', 'available'),
(105, 5, '16:00:00', '18:00:00', 'Monday', 'available'),
(106, 5, '08:00:00', '10:00:00', 'Tuesday', 'available'),
(107, 5, '10:00:00', '12:00:00', 'Tuesday', 'available'),
(108, 5, '12:00:00', '14:00:00', 'Tuesday', 'available'),
(109, 5, '14:00:00', '16:00:00', 'Tuesday', 'available'),
(110, 5, '16:00:00', '18:00:00', 'Tuesday', 'available'),
(111, 5, '08:00:00', '10:00:00', 'Wednesday', 'available'),
(112, 5, '10:00:00', '12:00:00', 'Wednesday', 'available'),
(113, 5, '12:00:00', '14:00:00', 'Wednesday', 'available'),
(114, 5, '14:00:00', '16:00:00', 'Wednesday', 'available'),
(115, 5, '16:00:00', '18:00:00', 'Wednesday', 'available'),
(116, 5, '08:00:00', '10:00:00', 'Thursday', 'available'),
(117, 5, '10:00:00', '12:00:00', 'Thursday', 'available'),
(118, 5, '12:00:00', '14:00:00', 'Thursday', 'available'),
(119, 5, '14:00:00', '16:00:00', 'Thursday', 'available'),
(120, 5, '16:00:00', '18:00:00', 'Thursday', 'available'),
(121, 5, '08:00:00', '10:00:00', 'Friday', 'available'),
(122, 5, '10:00:00', '12:00:00', 'Friday', 'available'),
(123, 5, '12:00:00', '14:00:00', 'Friday', 'available'),
(124, 5, '14:00:00', '16:00:00', 'Friday', 'available'),
(125, 5, '16:00:00', '18:00:00', 'Friday', 'available'),
(126, 6, '08:00:00', '10:00:00', 'Monday', 'available'),
(127, 6, '10:00:00', '12:00:00', 'Monday', 'available'),
(128, 6, '12:00:00', '14:00:00', 'Monday', 'available'),
(129, 6, '14:00:00', '16:00:00', 'Monday', 'available'),
(130, 6, '16:00:00', '18:00:00', 'Monday', 'available'),
(131, 6, '08:00:00', '10:00:00', 'Tuesday', 'available'),
(132, 6, '10:00:00', '12:00:00', 'Tuesday', 'available'),
(133, 6, '12:00:00', '14:00:00', 'Tuesday', 'available'),
(134, 6, '14:00:00', '16:00:00', 'Tuesday', 'available'),
(135, 6, '16:00:00', '18:00:00', 'Tuesday', 'available'),
(136, 6, '08:00:00', '10:00:00', 'Wednesday', 'available'),
(137, 6, '10:00:00', '12:00:00', 'Wednesday', 'available'),
(138, 6, '12:00:00', '14:00:00', 'Wednesday', 'available'),
(139, 6, '14:00:00', '16:00:00', 'Wednesday', 'available'),
(140, 6, '16:00:00', '18:00:00', 'Wednesday', 'available'),
(141, 6, '08:00:00', '10:00:00', 'Thursday', 'available'),
(142, 6, '10:00:00', '12:00:00', 'Thursday', 'available'),
(143, 6, '12:00:00', '14:00:00', 'Thursday', 'available'),
(144, 6, '14:00:00', '16:00:00', 'Thursday', 'available'),
(145, 6, '16:00:00', '18:00:00', 'Thursday', 'available'),
(146, 6, '08:00:00', '10:00:00', 'Friday', 'available'),
(147, 6, '10:00:00', '12:00:00', 'Friday', 'available'),
(148, 6, '12:00:00', '14:00:00', 'Friday', 'available'),
(149, 6, '14:00:00', '16:00:00', 'Friday', 'available'),
(150, 6, '16:00:00', '18:00:00', 'Friday', 'available'),
(151, 7, '08:00:00', '10:00:00', 'Monday', 'available'),
(152, 7, '10:00:00', '12:00:00', 'Monday', 'available'),
(153, 7, '12:00:00', '14:00:00', 'Monday', 'available'),
(154, 7, '14:00:00', '16:00:00', 'Monday', 'available'),
(155, 7, '16:00:00', '18:00:00', 'Monday', 'available'),
(156, 7, '08:00:00', '10:00:00', 'Tuesday', 'available'),
(157, 7, '10:00:00', '12:00:00', 'Tuesday', 'available'),
(158, 7, '12:00:00', '14:00:00', 'Tuesday', 'available'),
(159, 7, '14:00:00', '16:00:00', 'Tuesday', 'available'),
(160, 7, '16:00:00', '18:00:00', 'Tuesday', 'available'),
(161, 7, '08:00:00', '10:00:00', 'Wednesday', 'available'),
(162, 7, '10:00:00', '12:00:00', 'Wednesday', 'available'),
(163, 7, '12:00:00', '14:00:00', 'Wednesday', 'available'),
(164, 7, '14:00:00', '16:00:00', 'Wednesday', 'available'),
(165, 7, '16:00:00', '18:00:00', 'Wednesday', 'available'),
(166, 7, '08:00:00', '10:00:00', 'Thursday', 'available'),
(167, 7, '10:00:00', '12:00:00', 'Thursday', 'available'),
(168, 7, '12:00:00', '14:00:00', 'Thursday', 'available'),
(169, 7, '14:00:00', '16:00:00', 'Thursday', 'available'),
(170, 7, '16:00:00', '18:00:00', 'Thursday', 'available'),
(171, 7, '08:00:00', '10:00:00', 'Friday', 'available'),
(172, 7, '10:00:00', '12:00:00', 'Friday', 'available'),
(173, 7, '12:00:00', '14:00:00', 'Friday', 'available'),
(174, 7, '14:00:00', '16:00:00', 'Friday', 'available'),
(175, 7, '16:00:00', '18:00:00', 'Friday', 'available'),
(176, 8, '08:00:00', '10:00:00', 'Monday', 'available'),
(177, 8, '10:00:00', '12:00:00', 'Monday', 'available'),
(178, 8, '12:00:00', '14:00:00', 'Monday', 'available'),
(179, 8, '14:00:00', '16:00:00', 'Monday', 'available'),
(180, 8, '16:00:00', '18:00:00', 'Monday', 'available'),
(181, 8, '08:00:00', '10:00:00', 'Tuesday', 'available'),
(182, 8, '10:00:00', '12:00:00', 'Tuesday', 'available'),
(183, 8, '12:00:00', '14:00:00', 'Tuesday', 'available'),
(184, 8, '14:00:00', '16:00:00', 'Tuesday', 'available'),
(185, 8, '16:00:00', '18:00:00', 'Tuesday', 'available'),
(186, 8, '08:00:00', '10:00:00', 'Wednesday', 'available'),
(187, 8, '10:00:00', '12:00:00', 'Wednesday', 'available'),
(188, 8, '12:00:00', '14:00:00', 'Wednesday', 'available'),
(189, 8, '14:00:00', '16:00:00', 'Wednesday', 'available'),
(190, 8, '16:00:00', '18:00:00', 'Wednesday', 'available'),
(191, 8, '08:00:00', '10:00:00', 'Thursday', 'available'),
(192, 8, '10:00:00', '12:00:00', 'Thursday', 'available'),
(193, 8, '12:00:00', '14:00:00', 'Thursday', 'available'),
(194, 8, '14:00:00', '16:00:00', 'Thursday', 'available'),
(195, 8, '16:00:00', '18:00:00', 'Thursday', 'available'),
(196, 8, '08:00:00', '10:00:00', 'Friday', 'available'),
(197, 8, '10:00:00', '12:00:00', 'Friday', 'available'),
(198, 8, '12:00:00', '14:00:00', 'Friday', 'available'),
(199, 8, '14:00:00', '16:00:00', 'Friday', 'available'),
(200, 8, '16:00:00', '18:00:00', 'Friday', 'available'),
(201, 9, '08:00:00', '10:00:00', 'Monday', 'available'),
(202, 9, '10:00:00', '12:00:00', 'Monday', 'available'),
(203, 9, '12:00:00', '14:00:00', 'Monday', 'available'),
(204, 9, '14:00:00', '16:00:00', 'Monday', 'available'),
(205, 9, '16:00:00', '18:00:00', 'Monday', 'available'),
(206, 9, '08:00:00', '10:00:00', 'Tuesday', 'available'),
(207, 9, '10:00:00', '12:00:00', 'Tuesday', 'available'),
(208, 9, '12:00:00', '14:00:00', 'Tuesday', 'available'),
(209, 9, '14:00:00', '16:00:00', 'Tuesday', 'available'),
(210, 9, '16:00:00', '18:00:00', 'Tuesday', 'available'),
(211, 9, '08:00:00', '10:00:00', 'Wednesday', 'available'),
(212, 9, '10:00:00', '12:00:00', 'Wednesday', 'available'),
(213, 9, '12:00:00', '14:00:00', 'Wednesday', 'available'),
(214, 9, '14:00:00', '16:00:00', 'Wednesday', 'available'),
(215, 9, '16:00:00', '18:00:00', 'Wednesday', 'available'),
(216, 9, '08:00:00', '10:00:00', 'Thursday', 'available'),
(217, 9, '10:00:00', '12:00:00', 'Thursday', 'available'),
(218, 9, '12:00:00', '14:00:00', 'Thursday', 'available'),
(219, 9, '14:00:00', '16:00:00', 'Thursday', 'available'),
(220, 9, '16:00:00', '18:00:00', 'Thursday', 'available'),
(221, 9, '08:00:00', '10:00:00', 'Friday', 'available'),
(222, 9, '10:00:00', '12:00:00', 'Friday', 'available'),
(223, 9, '12:00:00', '14:00:00', 'Friday', 'available'),
(224, 9, '14:00:00', '16:00:00', 'Friday', 'available'),
(225, 9, '16:00:00', '18:00:00', 'Friday', 'available'),
(226, 10, '08:00:00', '10:00:00', 'Monday', 'available'),
(227, 10, '10:00:00', '12:00:00', 'Monday', 'available'),
(228, 10, '12:00:00', '14:00:00', 'Monday', 'available'),
(229, 10, '14:00:00', '16:00:00', 'Monday', 'available'),
(230, 10, '16:00:00', '18:00:00', 'Monday', 'available'),
(231, 10, '08:00:00', '10:00:00', 'Tuesday', 'available'),
(232, 10, '10:00:00', '12:00:00', 'Tuesday', 'available'),
(233, 10, '12:00:00', '14:00:00', 'Tuesday', 'available'),
(234, 10, '14:00:00', '16:00:00', 'Tuesday', 'available'),
(235, 10, '16:00:00', '18:00:00', 'Tuesday', 'available'),
(236, 10, '08:00:00', '10:00:00', 'Wednesday', 'available'),
(237, 10, '10:00:00', '12:00:00', 'Wednesday', 'available'),
(238, 10, '12:00:00', '14:00:00', 'Wednesday', 'available'),
(239, 10, '14:00:00', '16:00:00', 'Wednesday', 'available'),
(240, 10, '16:00:00', '18:00:00', 'Wednesday', 'available'),
(241, 10, '08:00:00', '10:00:00', 'Thursday', 'available'),
(242, 10, '10:00:00', '12:00:00', 'Thursday', 'available'),
(243, 10, '12:00:00', '14:00:00', 'Thursday', 'available'),
(244, 10, '14:00:00', '16:00:00', 'Thursday', 'available'),
(245, 10, '16:00:00', '18:00:00', 'Thursday', 'available'),
(246, 10, '08:00:00', '10:00:00', 'Friday', 'available'),
(247, 10, '10:00:00', '12:00:00', 'Friday', 'available'),
(248, 10, '12:00:00', '14:00:00', 'Friday', 'available'),
(249, 10, '14:00:00', '16:00:00', 'Friday', 'available'),
(250, 10, '16:00:00', '18:00:00', 'Friday', 'available'),
(251, 11, '08:00:00', '10:00:00', 'Monday', 'available'),
(252, 11, '10:00:00', '12:00:00', 'Monday', 'available'),
(253, 11, '12:00:00', '14:00:00', 'Monday', 'available'),
(254, 11, '14:00:00', '16:00:00', 'Monday', 'available'),
(255, 11, '16:00:00', '18:00:00', 'Monday', 'available'),
(256, 11, '08:00:00', '10:00:00', 'Tuesday', 'available'),
(257, 11, '10:00:00', '12:00:00', 'Tuesday', 'available'),
(258, 11, '12:00:00', '14:00:00', 'Tuesday', 'available'),
(259, 11, '14:00:00', '16:00:00', 'Tuesday', 'available'),
(260, 11, '16:00:00', '18:00:00', 'Tuesday', 'available'),
(261, 11, '08:00:00', '10:00:00', 'Wednesday', 'available'),
(262, 11, '10:00:00', '12:00:00', 'Wednesday', 'available'),
(263, 11, '12:00:00', '14:00:00', 'Wednesday', 'available'),
(264, 11, '14:00:00', '16:00:00', 'Wednesday', 'available'),
(265, 11, '16:00:00', '18:00:00', 'Wednesday', 'available'),
(266, 11, '08:00:00', '10:00:00', 'Thursday', 'available'),
(267, 11, '10:00:00', '12:00:00', 'Thursday', 'available'),
(268, 11, '12:00:00', '14:00:00', 'Thursday', 'available'),
(269, 11, '14:00:00', '16:00:00', 'Thursday', 'available'),
(270, 11, '16:00:00', '18:00:00', 'Thursday', 'available'),
(271, 11, '08:00:00', '10:00:00', 'Friday', 'available'),
(272, 11, '10:00:00', '12:00:00', 'Friday', 'available'),
(273, 11, '12:00:00', '14:00:00', 'Friday', 'available'),
(274, 11, '14:00:00', '16:00:00', 'Friday', 'available'),
(275, 11, '16:00:00', '18:00:00', 'Friday', 'available'),
(276, 12, '08:00:00', '10:00:00', 'Monday', 'available'),
(277, 12, '10:00:00', '12:00:00', 'Monday', 'available'),
(278, 12, '12:00:00', '14:00:00', 'Monday', 'available'),
(279, 12, '14:00:00', '16:00:00', 'Monday', 'available'),
(280, 12, '16:00:00', '18:00:00', 'Monday', 'available'),
(281, 12, '08:00:00', '10:00:00', 'Tuesday', 'available'),
(282, 12, '10:00:00', '12:00:00', 'Tuesday', 'available'),
(283, 12, '12:00:00', '14:00:00', 'Tuesday', 'available'),
(284, 12, '14:00:00', '16:00:00', 'Tuesday', 'available'),
(285, 12, '16:00:00', '18:00:00', 'Tuesday', 'available'),
(286, 12, '08:00:00', '10:00:00', 'Wednesday', 'available'),
(287, 12, '10:00:00', '12:00:00', 'Wednesday', 'available'),
(288, 12, '12:00:00', '14:00:00', 'Wednesday', 'available'),
(289, 12, '14:00:00', '16:00:00', 'Wednesday', 'available'),
(290, 12, '16:00:00', '18:00:00', 'Wednesday', 'available'),
(291, 12, '08:00:00', '10:00:00', 'Thursday', 'available'),
(292, 12, '10:00:00', '12:00:00', 'Thursday', 'available'),
(293, 12, '12:00:00', '14:00:00', 'Thursday', 'available'),
(294, 12, '14:00:00', '16:00:00', 'Thursday', 'available'),
(295, 12, '16:00:00', '18:00:00', 'Thursday', 'available'),
(296, 12, '08:00:00', '10:00:00', 'Friday', 'available'),
(297, 12, '10:00:00', '12:00:00', 'Friday', 'available'),
(298, 12, '12:00:00', '14:00:00', 'Friday', 'available'),
(299, 12, '14:00:00', '16:00:00', 'Friday', 'available'),
(300, 12, '16:00:00', '18:00:00', 'Friday', 'available'),
(301, 1, '08:00:00', '10:00:00', 'Saturday', 'available'),
(302, 1, '10:00:00', '12:00:00', 'Saturday', 'available'),
(303, 1, '12:00:00', '14:00:00', 'Saturday', 'available'),
(304, 1, '14:00:00', '16:00:00', 'Saturday', 'available'),
(305, 1, '16:00:00', '18:00:00', 'Saturday', 'available'),
(306, 1, '08:00:00', '10:00:00', 'Sunday', 'available'),
(307, 1, '10:00:00', '12:00:00', 'Sunday', 'available'),
(308, 1, '12:00:00', '14:00:00', 'Sunday', 'available'),
(309, 1, '14:00:00', '16:00:00', 'Sunday', 'available'),
(310, 1, '16:00:00', '18:00:00', 'Sunday', 'available'),
(311, 2, '08:00:00', '10:00:00', 'Saturday', 'available'),
(312, 2, '10:00:00', '12:00:00', 'Saturday', 'available'),
(313, 2, '12:00:00', '14:00:00', 'Saturday', 'available'),
(314, 2, '14:00:00', '16:00:00', 'Saturday', 'available'),
(315, 2, '16:00:00', '18:00:00', 'Saturday', 'available'),
(316, 2, '08:00:00', '10:00:00', 'Sunday', 'available'),
(317, 2, '10:00:00', '12:00:00', 'Sunday', 'available'),
(318, 2, '12:00:00', '14:00:00', 'Sunday', 'available'),
(319, 2, '14:00:00', '16:00:00', 'Sunday', 'available'),
(320, 2, '16:00:00', '18:00:00', 'Sunday', 'available'),
(321, 3, '08:00:00', '10:00:00', 'Saturday', 'available'),
(322, 3, '10:00:00', '12:00:00', 'Saturday', 'available'),
(323, 3, '12:00:00', '14:00:00', 'Saturday', 'available'),
(324, 3, '14:00:00', '16:00:00', 'Saturday', 'available'),
(325, 3, '16:00:00', '18:00:00', 'Saturday', 'available'),
(326, 3, '08:00:00', '10:00:00', 'Sunday', 'available'),
(327, 3, '10:00:00', '12:00:00', 'Sunday', 'available'),
(328, 3, '12:00:00', '14:00:00', 'Sunday', 'available'),
(329, 3, '14:00:00', '16:00:00', 'Sunday', 'available'),
(330, 3, '16:00:00', '18:00:00', 'Sunday', 'available'),
(331, 4, '08:00:00', '10:00:00', 'Saturday', 'available'),
(332, 4, '10:00:00', '12:00:00', 'Saturday', 'available'),
(333, 4, '12:00:00', '14:00:00', 'Saturday', 'available'),
(334, 4, '14:00:00', '16:00:00', 'Saturday', 'available'),
(335, 4, '16:00:00', '18:00:00', 'Saturday', 'available'),
(336, 4, '08:00:00', '10:00:00', 'Sunday', 'available'),
(337, 4, '10:00:00', '12:00:00', 'Sunday', 'available'),
(338, 4, '12:00:00', '14:00:00', 'Sunday', 'available'),
(339, 4, '14:00:00', '16:00:00', 'Sunday', 'available'),
(340, 4, '16:00:00', '18:00:00', 'Sunday', 'available'),
(341, 5, '08:00:00', '10:00:00', 'Saturday', 'available'),
(342, 5, '10:00:00', '12:00:00', 'Saturday', 'available'),
(343, 5, '12:00:00', '14:00:00', 'Saturday', 'available'),
(344, 5, '14:00:00', '16:00:00', 'Saturday', 'available'),
(345, 5, '16:00:00', '18:00:00', 'Saturday', 'available'),
(346, 5, '08:00:00', '10:00:00', 'Sunday', 'available'),
(347, 5, '10:00:00', '12:00:00', 'Sunday', 'available'),
(348, 5, '12:00:00', '14:00:00', 'Sunday', 'available'),
(349, 5, '14:00:00', '16:00:00', 'Sunday', 'available'),
(350, 5, '16:00:00', '18:00:00', 'Sunday', 'available'),
(351, 6, '08:00:00', '10:00:00', 'Saturday', 'available'),
(352, 6, '10:00:00', '12:00:00', 'Saturday', 'available'),
(353, 6, '12:00:00', '14:00:00', 'Saturday', 'available'),
(354, 6, '14:00:00', '16:00:00', 'Saturday', 'available'),
(355, 6, '16:00:00', '18:00:00', 'Saturday', 'available'),
(356, 6, '08:00:00', '10:00:00', 'Sunday', 'available'),
(357, 6, '10:00:00', '12:00:00', 'Sunday', 'available'),
(358, 6, '12:00:00', '14:00:00', 'Sunday', 'available'),
(359, 6, '14:00:00', '16:00:00', 'Sunday', 'available'),
(360, 6, '16:00:00', '18:00:00', 'Sunday', 'available'),
(361, 7, '08:00:00', '10:00:00', 'Saturday', 'available'),
(362, 7, '10:00:00', '12:00:00', 'Saturday', 'available'),
(363, 7, '12:00:00', '14:00:00', 'Saturday', 'available'),
(364, 7, '14:00:00', '16:00:00', 'Saturday', 'available'),
(365, 7, '16:00:00', '18:00:00', 'Saturday', 'available'),
(366, 7, '08:00:00', '10:00:00', 'Sunday', 'available'),
(367, 7, '10:00:00', '12:00:00', 'Sunday', 'available'),
(368, 7, '12:00:00', '14:00:00', 'Sunday', 'available'),
(369, 7, '14:00:00', '16:00:00', 'Sunday', 'available'),
(370, 7, '16:00:00', '18:00:00', 'Sunday', 'available'),
(371, 8, '08:00:00', '10:00:00', 'Saturday', 'available'),
(372, 8, '10:00:00', '12:00:00', 'Saturday', 'available'),
(373, 8, '12:00:00', '14:00:00', 'Saturday', 'available'),
(374, 8, '14:00:00', '16:00:00', 'Saturday', 'available'),
(375, 8, '16:00:00', '18:00:00', 'Saturday', 'available'),
(376, 8, '08:00:00', '10:00:00', 'Sunday', 'available'),
(377, 8, '10:00:00', '12:00:00', 'Sunday', 'available'),
(378, 8, '12:00:00', '14:00:00', 'Sunday', 'available'),
(379, 8, '14:00:00', '16:00:00', 'Sunday', 'available'),
(380, 8, '16:00:00', '18:00:00', 'Sunday', 'available'),
(381, 9, '08:00:00', '10:00:00', 'Saturday', 'available'),
(382, 9, '10:00:00', '12:00:00', 'Saturday', 'available'),
(383, 9, '12:00:00', '14:00:00', 'Saturday', 'available'),
(384, 9, '14:00:00', '16:00:00', 'Saturday', 'available'),
(385, 9, '16:00:00', '18:00:00', 'Saturday', 'available'),
(386, 9, '08:00:00', '10:00:00', 'Sunday', 'available'),
(387, 9, '10:00:00', '12:00:00', 'Sunday', 'available'),
(388, 9, '12:00:00', '14:00:00', 'Sunday', 'available'),
(389, 9, '14:00:00', '16:00:00', 'Sunday', 'available'),
(390, 9, '16:00:00', '18:00:00', 'Sunday', 'available'),
(391, 10, '08:00:00', '10:00:00', 'Saturday', 'available'),
(392, 10, '10:00:00', '12:00:00', 'Saturday', 'available'),
(393, 10, '12:00:00', '14:00:00', 'Saturday', 'available'),
(394, 10, '14:00:00', '16:00:00', 'Saturday', 'available'),
(395, 10, '16:00:00', '18:00:00', 'Saturday', 'available'),
(396, 10, '08:00:00', '10:00:00', 'Sunday', 'available'),
(397, 10, '10:00:00', '12:00:00', 'Sunday', 'available'),
(398, 10, '12:00:00', '14:00:00', 'Sunday', 'available'),
(399, 10, '14:00:00', '16:00:00', 'Sunday', 'available'),
(400, 10, '16:00:00', '18:00:00', 'Sunday', 'available'),
(401, 11, '08:00:00', '10:00:00', 'Saturday', 'available'),
(402, 11, '10:00:00', '12:00:00', 'Saturday', 'available'),
(403, 11, '12:00:00', '14:00:00', 'Saturday', 'available'),
(404, 11, '14:00:00', '16:00:00', 'Saturday', 'available'),
(405, 11, '16:00:00', '18:00:00', 'Saturday', 'available'),
(406, 11, '08:00:00', '10:00:00', 'Sunday', 'available'),
(407, 11, '10:00:00', '12:00:00', 'Sunday', 'available'),
(408, 11, '12:00:00', '14:00:00', 'Sunday', 'available'),
(409, 11, '14:00:00', '16:00:00', 'Sunday', 'available'),
(410, 11, '16:00:00', '18:00:00', 'Sunday', 'available'),
(411, 12, '08:00:00', '10:00:00', 'Saturday', 'available'),
(412, 12, '10:00:00', '12:00:00', 'Saturday', 'available'),
(413, 12, '12:00:00', '14:00:00', 'Saturday', 'available'),
(414, 12, '14:00:00', '16:00:00', 'Saturday', 'available'),
(415, 12, '16:00:00', '18:00:00', 'Saturday', 'available'),
(416, 12, '08:00:00', '10:00:00', 'Sunday', 'available'),
(417, 12, '10:00:00', '12:00:00', 'Sunday', 'available'),
(418, 12, '12:00:00', '14:00:00', 'Sunday', 'available'),
(419, 12, '14:00:00', '16:00:00', 'Sunday', 'available'),
(420, 12, '16:00:00', '18:00:00', 'Sunday', 'available');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `facility_id` int NOT NULL,
  `timeslot_id` int NOT NULL,
  `booking_date` date NOT NULL,
  `status` varchar(255) NOT NULL,
  `notes` text,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `facility_id`, `timeslot_id`, `booking_date`, `status`, `notes`, `created`, `modified`) VALUES
(1, 6, 1, 21, '2026-06-26', 'approved', '', '2026-06-26 01:32:02', '2026-06-26 03:12:40'),
(2, 6, 1, 7, '2026-06-30', 'pending', NULL, '2026-06-26 01:43:36', '2026-06-26 01:43:36'),
(3, 6, 4, 96, '2026-07-03', 'approved', '', '2026-06-26 02:53:07', '2026-06-28 18:34:30'),
(5, 9, 11, 272, '2026-06-26', 'pending', NULL, '2026-06-28 18:55:43', '2026-06-28 18:55:43'),
(11, 7, 5, 101, '2026-07-06', 'approved', '', '2026-07-05 09:45:52', '2026-07-05 09:46:16'),
(12, 7, 5, 350, '2026-07-05', 'approved', '', '2026-07-05 11:03:53', '2026-07-09 10:34:58'),
(14, 8, 3, 53, '2026-07-06', 'approved', 'tak bayar kolej', '2026-07-06 11:24:42', '2026-07-09 10:34:22'),
(15, 6, 3, 53, '2026-07-06', 'rejected', '', '2026-07-06 11:43:07', '2026-07-06 11:44:26'),
(18, 7, 12, 296, '2026-07-10', 'approved', 'jangan lambat', '2026-07-09 19:57:43', '2026-07-09 20:00:10');

-- --------------------------------------------------------

--
-- Table structure for table `equipments`
--

CREATE TABLE `equipments` (
  `id` int NOT NULL,
  `facility_id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_quantity` int NOT NULL,
  `available_quantity` int NOT NULL,
  `image_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `equipments`
--

INSERT INTO `equipments` (`id`, `facility_id`, `name`, `total_quantity`, `available_quantity`, `image_path`) VALUES
(1, 3, 'Badminton Racket', 10, 10, 'uploads/badminton_racket_landscape.png'),
(2, 5, 'Professional Football', 5, 5, 'uploads/football_eq.png'),
(3, 1, 'Professional Basketball', 8, 8, 'uploads/basketball_eq.png'),
(4, 2, 'Tennis Racket', 12, 12, 'uploads/tennis_racket_1783226734396.png'),
(5, 4, 'Track Spikes (Size 7-12)', 20, 20, 'uploads/track_spikes_1783226753055.png'),
(6, 6, 'Indoor Volleyball', 6, 6, 'uploads/volleyball_eq.png'),
(7, 7, 'Swimming Goggles', 15, 15, 'uploads/swimming_goggles_1783226770846.png'),
(8, 8, 'Hex Dumbbell Set (10-50lbs)', 10, 10, 'uploads/dumbbell_set_1783226789054.png'),
(9, 9, 'Squash Racket', 8, 8, 'uploads/squash_racket_landscape.png'),
(10, 10, 'Wooden Baseball Bat', 5, 5, 'uploads/pollination_baseball_bat.jpg'),
(11, 11, 'Premium Golf Driver', 4, 4, 'uploads/golf_driver_1783226833964.png'),
(12, 12, 'Table Tennis Paddle', 10, 6, 'uploads/pingpong_eq.png'),
(13, 3, 'Shuttlecock', 10, 10, 'uploads/shuttlecock_1783226705237.png'),
(14, 2, 'Tennis Balls', 12, 12, 'uploads/tennis_balls_1783226744066.png'),
(15, 7, 'Swimming Cap', 15, 15, 'uploads/swimming_cap_1783226780248.png'),
(16, 9, 'Double Dot Squash Ball', 8, 8, 'uploads/squash_ball_1783226805895.png'),
(17, 10, 'Baseball', 5, 5, 'uploads/baseball_only_fancy.png'),
(18, 11, 'Golf Balls', 4, 4, 'uploads/golf_balls_1783226846197.png'),
(19, 12, 'Table Tennis Balls', 10, 7, 'uploads/pingpong_balls_fancy.png'),
(20, 1, 'Premium Yoga Mat', 10, 10, 'uploads/pollination_yoga_mat.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `equipment_bookings`
--

CREATE TABLE `equipment_bookings` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `booking_id` int DEFAULT NULL,
  `equipment_id` int NOT NULL,
  `quantity` int NOT NULL,
  `booking_date` date NOT NULL,
  `status` varchar(255) DEFAULT 'pending',
  `notes` text,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `equipment_bookings`
--

INSERT INTO `equipment_bookings` (`id`, `user_id`, `booking_id`, `equipment_id`, `quantity`, `booking_date`, `status`, `notes`, `created`, `modified`) VALUES
(6, 9, 5, 11, 2, '2026-06-26', 'pending', NULL, '2026-06-28 18:56:10', '2026-06-28 18:56:10'),
(11, 7, 11, 2, 1, '2026-07-06', 'pending', NULL, '2026-07-05 09:45:52', '2026-07-05 09:45:52'),
(13, 8, 14, 1, 1, '2026-07-06', 'completed', NULL, '2026-07-06 11:24:42', '2026-07-08 18:34:54'),
(14, 8, 14, 13, 1, '2026-07-06', 'completed', NULL, '2026-07-06 11:24:42', '2026-07-08 18:34:54'),
(15, 6, 15, 1, 1, '2026-07-06', 'rejected', NULL, '2026-07-06 11:43:07', '2026-07-06 11:44:26'),
(16, 6, 15, 13, 1, '2026-07-06', 'rejected', NULL, '2026-07-06 11:43:07', '2026-07-06 11:44:26'),
(21, 7, 18, 12, 4, '2026-07-10', 'pending', NULL, '2026-07-09 19:57:43', '2026-07-09 19:57:43'),
(22, 7, 18, 19, 3, '2026-07-10', 'pending', NULL, '2026-07-09 19:57:43', '2026-07-09 19:57:43');

-- --------------------------------------------------------

--
-- Table structure for table `facilities`
--

CREATE TABLE `facilities` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `location` varchar(255) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `capacity` int DEFAULT NULL,
  `maintenance_time` datetime DEFAULT NULL,
  `maintenance_end_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `facilities`
--

INSERT INTO `facilities` (`id`, `name`, `description`, `location`, `image_path`, `status`, `created`, `modified`, `capacity`, `maintenance_time`, `maintenance_end_time`) VALUES
(1, 'Main Basketball Court', 'Professional full-size indoor basketball court with glossy wood flooring.', 'Sports Complex', 'images/basketball.png', 'available', '2026-06-24 13:13:21', '2026-06-24 13:13:21', 20, NULL, NULL),
(2, 'Tennis Court', 'Modern outdoor hard surface tennis court.', 'Outdoor Arena', 'images/tennis.png', 'available', '2026-06-24 13:13:21', '2026-06-24 13:13:21', 4, NULL, NULL),
(3, 'Badminton Court', 'Standard indoor badminton court with professional nets.', 'Sports Hall', 'images/badminton.png', 'available', '2026-06-24 13:13:21', '2026-07-05 12:33:40', 4, NULL, NULL),
(4, 'Running Track', 'Outdoor professional running track with stadium seating.', 'Outdoor Arena', 'images/track.png', 'available', '2026-06-24 13:13:21', '2026-06-24 13:13:21', 100, NULL, NULL),
(5, 'Football Field', 'Outdoor grass football field with bright floodlights.', 'Outdoor Arena', 'images/football.png', 'available', '2026-06-24 13:13:21', '2026-06-24 13:13:21', 22, NULL, NULL),
(6, 'Volleyball Court', 'Modern indoor volleyball court with high ceilings.', 'Sports Hall', 'images/volleyball.png', 'available', '2026-06-24 13:13:21', '2026-06-24 13:13:21', 12, NULL, NULL),
(7, 'Swimming Pool', 'Olympic size indoor swimming pool with clear racing lanes.', 'Aquatics Center', 'images/swimming.png', 'available', '2026-06-24 13:13:21', '2026-06-24 13:13:21', 50, NULL, NULL),
(8, 'Gymnasium', 'Modern indoor fitness center with premium workout equipment.', 'Sports Complex', 'images/gym.png', 'available', '2026-06-24 13:13:21', '2026-06-24 13:13:21', 40, NULL, NULL),
(9, 'Squash Court', 'Indoor squash court with bright white walls and red boundary lines.', 'Sports Hall', 'images/squash.png', 'available', '2026-06-24 13:13:21', '2026-06-24 13:13:21', 2, NULL, NULL),
(10, 'Baseball Field', 'Professional outdoor baseball field with manicured grass.', 'Outdoor Arena', 'images/baseball.png', 'available', '2026-06-24 13:47:12', '2026-07-05 11:35:43', 36, NULL, NULL),
(11, 'Indoor Golf Simulator', 'State-of-the-art indoor golf simulator with virtual courses.', 'Recreation Hub', 'images/golf.png', 'available', '2026-06-25 20:18:55', '2026-06-25 20:18:55', 4, NULL, NULL),
(12, 'Table Tennis Room', 'Dedicated room with professional ping pong tables and paddles.', 'Recreation Hub', 'images/table_tennis.png', 'available', '2026-06-25 20:18:55', '2026-06-25 20:18:55', 8, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `maintenance`
--

CREATE TABLE `maintenance` (
  `id` int NOT NULL,
  `facility_id` int NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `description` text,
  `status` varchar(255) NOT NULL DEFAULT 'scheduled',
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `maintenance`
--

INSERT INTO `maintenance` (`id`, `facility_id`, `start_date`, `end_date`, `description`, `status`, `created`, `modified`) VALUES
(2, 1, '2026-07-06', '2026-07-07', 'tok man jatuh longkang', 'completed', '2026-07-06 12:00:01', '2026-07-09 09:57:48'),
(3, 4, '2026-07-09', '2026-07-11', '', 'scheduled', '2026-07-09 10:00:14', '2026-07-09 10:00:14');

-- --------------------------------------------------------

--
-- Table structure for table `maintenance_reports`
--

CREATE TABLE `maintenance_reports` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `facility_id` int DEFAULT NULL,
  `description` text,
  `image_path` varchar(255) DEFAULT NULL,
  `admin_reply` text,
  `status` varchar(50) DEFAULT 'pending',
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT '0',
  `created` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `title`, `message`, `is_read`, `created`) VALUES
(1, 6, 'Booking Status Updated', 'Your Facility booking (#3) status has been changed to Approved.', 0, '2026-06-28 18:34:30'),
(2, 7, 'Booking Status Updated', 'Your Facility booking (#11) status has been changed to Approved.', 1, '2026-07-05 09:46:16'),
(4, 6, 'Facility Available!', 'Maintenance for Baseball Field is complete. It is now open for bookings.', 0, '2026-07-05 11:40:03'),
(5, 7, 'Facility Available!', 'Maintenance for Baseball Field is complete. It is now open for bookings.', 1, '2026-07-05 11:40:03'),
(6, 8, 'Facility Available!', 'Maintenance for Baseball Field is complete. It is now open for bookings.', 1, '2026-07-05 11:40:03'),
(7, 9, 'Facility Available!', 'Maintenance for Baseball Field is complete. It is now open for bookings.', 0, '2026-07-05 11:40:03'),
(8, 10, 'Facility Available!', 'Maintenance for Baseball Field is complete. It is now open for bookings.', 0, '2026-07-05 11:40:03'),
(9, 11, 'Facility Available!', 'Maintenance for Baseball Field is complete. It is now open for bookings.', 1, '2026-07-05 11:40:03'),
(10, 8, 'Equipment Automatically Returned', 'Your booking time has ended. The 1x Badminton Racket you borrowed has been automatically marked as returned.', 1, '2026-07-08 18:34:54'),
(11, 8, 'Equipment Automatically Returned', 'Your booking time has ended. The 1x Shuttlecock you borrowed has been automatically marked as returned.', 1, '2026-07-08 18:34:54'),
(12, 7, 'Booking Status Updated', 'Your Facility booking (#18) status has been changed to Approved.', 1, '2026-07-09 20:00:10'),
(13, 4, 'New Maintenance Report', 'A student has reported a maintenance issue regarding: Badminton Court', 1, '2026-07-09 23:26:17'),
(14, 5, 'New Maintenance Report', 'A student has reported a maintenance issue regarding: Badminton Court', 0, '2026-07-09 23:26:17'),
(15, 7, 'Report Updated', 'Your report for Badminton Court was updated. Status: In progress. Staff Reply: \"tetek jugak\"', 1, '2026-07-09 23:31:52'),
(16, 4, 'New Maintenance Report', 'A student has reported a maintenance issue regarding: Badminton Court', 1, '2026-07-09 23:33:25'),
(17, 5, 'New Maintenance Report', 'A student has reported a maintenance issue regarding: Badminton Court', 0, '2026-07-09 23:33:25'),
(18, 7, 'Report Updated', 'Your report for Badminton Court was updated. Status: In progress. Staff Reply: \"saya akan cuba halau\"', 1, '2026-07-09 23:33:59'),
(19, 7, 'Report Updated', 'Your report for Badminton Court was updated. Status: Resolved.', 1, '2026-07-09 23:34:04'),
(20, 4, 'New Maintenance Report', 'A student has reported a maintenance issue regarding: Badminton Court', 1, '2026-07-09 23:37:40'),
(21, 5, 'New Maintenance Report', 'A student has reported a maintenance issue regarding: Badminton Court', 0, '2026-07-09 23:37:40'),
(22, 7, 'Report Updated', 'Your report for Badminton Court was updated. Status: In progress.', 1, '2026-07-09 23:37:56'),
(23, 7, 'Report Updated', 'Your report for Badminton Court was updated. Status: Rejected.', 0, '2026-07-09 23:42:29');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `student_id` varchar(50) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `bio` text,
  `avatar` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `student_id`, `password`, `role`, `created`, `modified`, `phone`, `bio`, `avatar`) VALUES
(4, 'Super BigBoss Admin :p', 'admin@gmail.com', '', '$2y$10$HR.typEXUQDUkt.eiWaiQ.wc5Eh12FDNYPth6cySVw30VwcZI8C4u', 'admin', '2026-06-24 21:32:10', '2026-07-05 21:53:05', '60 19-244 0309', '', 'uploads/avatars/avatar_4_1783507665.png'),
(5, 'Baqir hadi', 'staff@gmail.com', NULL, '$2y$10$qlRSR80k35gRkPergoPMpeuA.mUIScNF/brPQvkG8epIEpUboBE2a', 'staff', '2026-06-24 21:32:10', '2026-06-24 21:32:10', '08004546780', '', 'uploads/avatars/avatar_5_1783562153.png'),
(6, 'Manap Shauqi ', 'student@gmail.com', NULL, '$2y$10$lLLa.jnfVFz9HRcc1C6VNeKjUWFGnb7XBvWKOo/Kzn8FouEHnFq6S', 'student', '2026-06-24 21:32:10', '2026-06-24 21:32:10', NULL, NULL, NULL),
(7, 'MUHAMMAD LUTHFIL AFI BIN ROSHAFIAN', '2025231816@student.uitm.edu.my', '2025231816', '$2y$10$mKtOH3ufx7nHizdKUIOwNeTyN/gvxBr4o050PznwfIqMoAyIxa1BS', 'student', '2026-06-27 20:22:38', '2026-06-27 20:22:38', '0122054201', 'handsome L\r\n', NULL),
(8, 'FARAH NISRINA BINTI SAIFUL BAHREIN', '2025231606@student.uitm.edu.my', '2025231606', '$2y$10$mKtOH3ufx7nHizdKUIOwNeTyN/gvxBr4o050PznwfIqMoAyIxa1BS', 'student', '2026-06-27 20:22:38', '2026-06-27 21:14:47', NULL, NULL, NULL),
(9, 'FATIN SUHAILA BINTI AMIZAN', '2025483472@student.uitm.edu.my', '2025483472', '$2y$10$mKtOH3ufx7nHizdKUIOwNeTyN/gvxBr4o050PznwfIqMoAyIxa1BS', 'student', '2026-06-27 20:22:38', '2026-06-27 20:22:38', NULL, NULL, NULL),
(10, 'MUHAMMAD NURHAFIZAL BIN NADZRI', '2025236992@student.uitm.edu.my', '2025236992', '$2y$10$mKtOH3ufx7nHizdKUIOwNeTyN/gvxBr4o050PznwfIqMoAyIxa1BS', 'student', '2026-06-27 20:22:38', '2026-06-27 20:22:38', NULL, NULL, NULL),
(11, 'ABDUL KHALIQ BIN ABDUL RAHMAN', '2025239528@student.uitm.edu.my', '2025239528', '$2y$10$mKtOH3ufx7nHizdKUIOwNeTyN/gvxBr4o050PznwfIqMoAyIxa1BS', 'student', '2026-06-27 20:22:38', '2026-06-27 20:22:38', NULL, NULL, NULL),
(12, 'MUHAMMAD IQBAL MUEEZ BIN MUSA', '2025420666@student.uitm.edu.my', '019-2637496', '$2y$10$JOClSnVXgaW3nqdmK0nyPOiXQcEWxUBdyY0AmszHqaDEdY4Krs/J2', 'student', '2026-07-09 02:10:50', '2026-07-09 02:10:50', '019-2637496', '', 'uploads/avatars/avatar_12_1783534800.jpeg'),
(13, 'izz danish', 'izzdanish0345@gmail.com', '2025424676', '$2y$10$sFXppLxY.iJfsszU4flNzu.zU7nIICFr6v8nAhh2VGQ96rEQWb68O', 'student', '2026-07-09 20:01:25', '2026-07-09 20:01:25', NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_audit_user` (`user_id`);

--
-- Indexes for table `availability`
--
ALTER TABLE `availability`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_avail_facility` (`facility_id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_bookings_user` (`user_id`),
  ADD KEY `fk_bookings_facility` (`facility_id`),
  ADD KEY `fk_bookings_timeslot` (`timeslot_id`);

--
-- Indexes for table `equipments`
--
ALTER TABLE `equipments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_eq_facility` (`facility_id`);

--
-- Indexes for table `equipment_bookings`
--
ALTER TABLE `equipment_bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_eqbk_user` (`user_id`),
  ADD KEY `fk_eqbk_eq` (`equipment_id`),
  ADD KEY `fk_eqbk_booking` (`booking_id`);

--
-- Indexes for table `facilities`
--
ALTER TABLE `facilities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `maintenance`
--
ALTER TABLE `maintenance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_maint_facility` (`facility_id`);

--
-- Indexes for table `maintenance_reports`
--
ALTER TABLE `maintenance_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `facility_id` (`facility_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_notif_user` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `availability`
--
ALTER TABLE `availability`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=446;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `equipments`
--
ALTER TABLE `equipments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `equipment_bookings`
--
ALTER TABLE `equipment_bookings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `facilities`
--
ALTER TABLE `facilities`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `maintenance`
--
ALTER TABLE `maintenance`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `maintenance_reports`
--
ALTER TABLE `maintenance_reports`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `fk_audit_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `availability`
--
ALTER TABLE `availability`
  ADD CONSTRAINT `fk_avail_facility` FOREIGN KEY (`facility_id`) REFERENCES `facilities` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `fk_bookings_facility` FOREIGN KEY (`facility_id`) REFERENCES `facilities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_bookings_timeslot` FOREIGN KEY (`timeslot_id`) REFERENCES `availability` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_bookings_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `equipments`
--
ALTER TABLE `equipments`
  ADD CONSTRAINT `fk_eq_facility` FOREIGN KEY (`facility_id`) REFERENCES `facilities` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `equipment_bookings`
--
ALTER TABLE `equipment_bookings`
  ADD CONSTRAINT `fk_eqbk_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_eqbk_eq` FOREIGN KEY (`equipment_id`) REFERENCES `equipments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_eqbk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `maintenance`
--
ALTER TABLE `maintenance`
  ADD CONSTRAINT `fk_maint_facility` FOREIGN KEY (`facility_id`) REFERENCES `facilities` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `maintenance_reports`
--
ALTER TABLE `maintenance_reports`
  ADD CONSTRAINT `maintenance_reports_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `maintenance_reports_ibfk_2` FOREIGN KEY (`facility_id`) REFERENCES `facilities` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_notif_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
