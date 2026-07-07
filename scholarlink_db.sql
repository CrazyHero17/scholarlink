-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Jul 07, 2026 at 01:09 PM
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
-- Database: `scholarlink_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `application`
--

CREATE TABLE `application` (
  `ApplicationID` int(11) NOT NULL,
  `ScholarshipID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `DateSubmitted` timestamp NOT NULL DEFAULT current_timestamp(),
  `Status` varchar(50) DEFAULT 'Pending',
  `TotalScore` int(11) DEFAULT 0,
  `GPA` decimal(4,2) DEFAULT NULL,
  `YearLevel` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `application`
--

INSERT INTO `application` (`ApplicationID`, `ScholarshipID`, `UserID`, `DateSubmitted`, `Status`, `TotalScore`, `GPA`, `YearLevel`) VALUES
(3, 9, 4, '2026-03-20 13:29:37', 'Approved', 95, 1.20, NULL),
(4, 10, 4, '2026-03-20 13:42:20', 'Approved', 0, 1.45, NULL),
(5, 9, 6, '2026-03-20 14:26:24', 'Approved', 95, 1.45, NULL),
(6, 11, 6, '2026-03-21 13:46:20', 'Approved', 90, 1.45, NULL),
(7, 10, 6, '2026-03-23 02:20:01', 'Approved', 85, 1.45, NULL),
(11, 12, 6, '2026-04-23 15:58:08', 'Approved', 85, 1.45, NULL),
(14, 9, 9, '2026-05-19 03:41:22', 'Submitted', 0, 0.00, NULL),
(16, 98, 6, '2026-05-19 12:38:26', 'Submitted', 0, 1.50, NULL),
(17, 43, 13, '2026-05-20 07:19:37', 'Submitted', 0, 0.00, NULL),
(18, 203, 6, '2026-06-29 04:07:07', 'Submitted', 0, 1.45, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `application_custom_answers`
--

CREATE TABLE `application_custom_answers` (
  `AnswerID` int(11) NOT NULL,
  `ApplicationID` int(11) NOT NULL,
  `FieldID` int(11) NOT NULL,
  `AnswerText` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `audit_log`
--

CREATE TABLE `audit_log` (
  `AuditID` int(11) NOT NULL,
  `UserID` int(11) DEFAULT NULL,
  `ActionPerformed` varchar(255) DEFAULT NULL,
  `ActionDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `Description` text DEFAULT NULL,
  `IPAddress` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_log`
--

INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES
(1, 3, 'Document Verified', '2026-03-21 14:16:01', 'Evaluator marked Document #4 as Verified', '::1'),
(2, 2, 'Application Approved', '2026-03-22 13:11:39', 'Internal Admin marked Application #6 as Approved', '::1'),
(3, 1, 'Auto Logout', '2026-03-22 15:00:19', 'User session expired due to inactivity.', '::1'),
(4, 3, 'Auto Logout', '2026-03-22 16:10:03', 'User session expired due to inactivity.', '::1'),
(5, 3, 'Auto Logout', '2026-03-22 17:13:30', 'User session expired due to inactivity.', '::1'),
(6, 6, 'Auto Logout', '2026-03-23 02:12:04', 'User session expired due to inactivity.', '::1'),
(7, 3, 'Document Verified', '2026-03-23 02:35:46', 'Evaluator marked Document #5 as Verified', '::1'),
(8, 6, 'Auto Logout', '2026-03-23 05:24:51', 'User session expired due to inactivity.', '::1'),
(9, 6, 'Auto Logout', '2026-03-23 05:35:31', 'User session expired due to inactivity.', '::1'),
(10, 3, 'Document Verified', '2026-03-23 06:07:12', 'Evaluator marked Document #10 as Verified', '::1'),
(11, 3, 'Document Verified', '2026-03-23 06:07:18', 'Evaluator marked Document #11 as Verified', '::1'),
(12, 3, 'Document Verified', '2026-03-23 06:17:29', 'Evaluator marked Document #12 as Verified', '::1'),
(13, 3, 'Document Verified', '2026-03-23 06:17:31', 'Evaluator marked Document #13 as Verified', '::1'),
(14, 3, 'Auto Logout', '2026-03-23 06:40:17', 'User session expired due to inactivity.', '::1'),
(15, 1, 'Security Update', '2026-03-23 17:06:41', 'Super Admin changed Session Timeout to 300 seconds', '::1'),
(16, 1, 'Security Update', '2026-03-23 17:12:58', 'Super Admin changed Strict Password to OFF', '::1'),
(17, 1, 'Security Update', '2026-03-23 17:13:01', 'Super Admin changed Strict Password to ON', '::1'),
(18, 2, 'Auto Logout', '2026-03-23 17:40:52', 'User session expired due to inactivity.', '::1'),
(19, 3, 'Auto Logout', '2026-03-23 17:59:49', 'User session expired due to inactivity.', '::1'),
(20, 6, 'Auto Logout', '2026-04-23 13:50:20', 'User session expired due to inactivity.', '::1'),
(21, 6, 'Auto Logout', '2026-04-23 13:59:11', 'User session expired due to inactivity.', '::1'),
(22, 6, 'Auto Logout', '2026-04-23 14:13:42', 'User session expired due to inactivity.', '::1'),
(23, 3, 'Auto Logout', '2026-04-23 14:31:09', 'User session expired due to inactivity.', '::1'),
(24, 3, 'Auto Logout', '2026-04-23 14:49:29', 'User session expired due to inactivity.', '::1'),
(25, 6, 'Auto Logout', '2026-04-23 15:48:20', 'User session expired due to inactivity.', '::1'),
(26, 2, 'Auto Logout', '2026-04-23 16:49:59', 'User session expired due to inactivity.', '::1'),
(27, 3, 'Auto Logout', '2026-04-23 17:07:01', 'User session expired due to inactivity.', '::1'),
(28, 6, 'Auto Logout', '2026-04-23 17:35:18', 'User session expired due to inactivity.', '::1'),
(29, 6, 'Auto Logout', '2026-04-23 17:56:58', 'User session expired due to inactivity.', '::1'),
(30, 6, 'Auto Logout', '2026-04-23 18:10:38', 'User session expired due to inactivity.', '::1'),
(31, 6, 'Auto Logout', '2026-04-23 18:47:47', 'User session expired due to inactivity.', '::1'),
(32, 6, 'Auto Logout', '2026-04-23 19:08:25', 'User session expired due to inactivity.', '::1'),
(33, 6, 'Auto Logout', '2026-04-23 19:22:10', 'User session expired due to inactivity.', '::1'),
(34, 6, 'Auto Logout', '2026-04-23 19:27:56', 'User session expired due to inactivity.', '::1'),
(35, 6, 'Auto Logout', '2026-04-23 19:46:21', 'User session expired due to inactivity.', '::1'),
(36, 6, 'Auto Logout', '2026-04-23 19:52:31', 'User session expired due to inactivity.', '::1'),
(37, 6, 'Password Reset', '2026-04-23 20:40:07', 'User successfully changed their password via reset link.', '::1'),
(38, 6, 'Password Reset', '2026-04-23 20:43:04', 'User successfully changed their password via reset link.', '::1'),
(39, 3, 'Auto Logout', '2026-04-24 04:25:17', 'User session expired due to inactivity.', '::1'),
(40, 6, 'Auto Logout', '2026-04-24 04:50:02', 'User session expired due to inactivity.', '::1'),
(41, 6, 'Auto Logout', '2026-04-24 07:12:59', 'User session expired due to inactivity.', '::1'),
(42, 6, 'Auto Logout', '2026-04-25 11:49:49', 'User session expired due to inactivity.', '::1'),
(43, 6, 'Auto Logout', '2026-04-25 11:55:06', 'User session expired due to inactivity.', '::1'),
(44, 3, 'Auto Logout', '2026-04-29 13:20:49', 'User session expired due to inactivity.', '::1'),
(45, 3, 'Auto Logout', '2026-04-29 13:33:39', 'User session expired due to inactivity.', '::1'),
(46, 3, 'Auto Logout', '2026-04-29 13:43:52', 'User session expired due to inactivity.', '::1'),
(47, 6, 'Auto Logout', '2026-04-29 14:07:09', 'User session expired due to inactivity.', '::1'),
(48, 6, 'Auto Logout', '2026-05-07 05:13:55', 'User session expired due to inactivity.', '::1'),
(49, 8, 'Account Created', '2026-05-07 05:18:25', 'A new student account was registered via the portal.', '::1'),
(50, 6, 'Auto Logout', '2026-05-15 11:18:01', 'User session expired due to inactivity.', '::1'),
(51, 6, 'Auto Logout', '2026-05-15 11:26:37', 'User session expired due to inactivity.', '::1'),
(52, 6, 'Auto Logout', '2026-05-15 11:53:18', 'User session expired due to inactivity.', '::1'),
(53, 6, 'Auto Logout', '2026-05-15 12:04:16', 'User session expired due to inactivity.', '::1'),
(54, 3, 'Auto Logout', '2026-05-15 15:09:23', 'User session expired due to inactivity.', '::1'),
(55, 1, 'Security Update', '2026-05-15 15:25:46', 'Super Admin changed Strict Password to OFF', '::1'),
(56, 1, 'Security Update', '2026-05-15 15:25:48', 'Super Admin changed Strict Password to ON', '::1'),
(57, 1, 'Security Update', '2026-05-15 15:25:50', 'Super Admin changed Require 2fa to ON', '::1'),
(58, 1, 'Security Update', '2026-05-15 15:25:51', 'Super Admin changed Require 2fa to OFF', '::1'),
(59, 6, 'Auto Logout', '2026-05-15 15:57:27', 'User session expired due to inactivity.', '::1'),
(60, 6, 'Auto Logout', '2026-05-15 16:09:52', 'User session expired due to inactivity.', '::1'),
(61, 6, 'Auto Logout', '2026-05-15 16:20:08', 'User session expired due to inactivity.', '::1'),
(62, 6, 'Auto Logout', '2026-05-15 17:04:56', 'User session expired due to inactivity.', '::1'),
(63, 3, 'Auto Logout', '2026-05-17 14:49:59', 'User session expired due to inactivity.', '::1'),
(64, 6, 'Auto Logout', '2026-05-17 15:30:53', 'User session expired due to inactivity.', '::1'),
(65, 6, 'Auto Logout', '2026-05-17 15:40:50', 'User session expired due to inactivity.', '::1'),
(66, 3, 'Document Verified', '2026-05-17 15:49:19', 'Evaluator marked Document #14 as Verified', '::1'),
(67, 6, 'Auto Logout', '2026-05-17 16:01:33', 'User session expired due to inactivity.', '::1'),
(68, 6, 'Auto Logout', '2026-05-17 16:09:48', 'User session expired due to inactivity.', '::1'),
(69, 6, 'Auto Logout', '2026-05-17 16:32:54', 'User session expired due to inactivity.', '::1'),
(70, 6, 'Auto Logout', '2026-05-18 03:05:55', 'User session expired due to inactivity.', '::1'),
(71, 6, 'Auto Logout', '2026-05-18 14:36:53', 'User session expired due to inactivity.', '::1'),
(72, 6, 'Auto Logout', '2026-05-18 14:42:42', 'User session expired due to inactivity.', '::1'),
(73, 2, 'Auto Logout', '2026-05-18 15:02:03', 'User session expired due to inactivity.', '::1'),
(74, 6, 'Auto Logout', '2026-05-18 15:16:09', 'User session expired due to inactivity.', '::1'),
(75, 2, 'Auto Logout', '2026-05-18 15:17:49', 'User session expired due to inactivity.', '::1'),
(76, 2, 'Auto Logout', '2026-05-18 15:34:12', 'User session expired due to inactivity.', '::1'),
(77, 2, 'Auto Logout', '2026-05-18 15:40:11', 'User session expired due to inactivity.', '::1'),
(78, 6, 'Auto Logout', '2026-05-19 03:18:40', 'User session expired due to inactivity.', '::1'),
(79, 9, 'Account Created', '2026-05-19 03:27:46', 'A new student account was registered via the portal.', '::1'),
(80, 2, 'Auto Logout', '2026-05-19 03:50:20', 'User session expired due to inactivity.', '::1'),
(81, 10, 'Account Created', '2026-05-19 09:40:03', 'A new student account was registered via the portal.', '::1'),
(82, 6, 'Auto Logout', '2026-05-19 12:29:14', 'User session expired due to inactivity.', '::1'),
(83, 6, 'Auto Logout', '2026-05-19 12:48:11', 'User session expired due to inactivity.', '::1'),
(84, 11, 'Account Created', '2026-05-19 12:57:53', 'A new student account was registered via the portal.', '::1'),
(85, 11, 'Auto Logout', '2026-05-19 13:03:41', 'User session expired due to inactivity.', '::1'),
(86, 11, 'Auto Logout', '2026-05-19 13:10:34', 'User session expired due to inactivity.', '::1'),
(87, 12, 'Account Created', '2026-05-19 13:11:49', 'A new student account was registered via the portal.', '::1'),
(88, 2, 'Auto Logout', '2026-05-19 13:40:58', 'User session expired due to inactivity.', '::1'),
(89, 3, 'Auto Logout', '2026-05-19 13:49:16', 'User session expired due to inactivity.', '::1'),
(90, 2, 'Auto Logout', '2026-05-19 13:49:20', 'User session expired due to inactivity.', '::1'),
(91, 3, 'Document Verified', '2026-05-19 13:53:01', 'Evaluator marked Document #15 as Verified', '::1'),
(92, 2, 'Application Approved', '2026-05-19 13:53:55', 'Internal Admin marked Application #11 as Approved', '::1'),
(93, 6, 'Auto Logout', '2026-05-19 14:04:55', 'User session expired due to inactivity.', '::1'),
(94, 6, 'Auto Logout', '2026-05-19 14:28:36', 'User session expired due to inactivity.', '::1'),
(95, 3, 'Auto Logout', '2026-05-19 14:48:07', 'User session expired due to inactivity.', '::1'),
(96, 3, 'Auto Logout', '2026-05-19 15:01:34', 'User session expired due to inactivity.', '::1'),
(97, 3, 'Auto Logout', '2026-05-19 15:21:41', 'User session expired due to inactivity.', '::1'),
(98, 3, 'Auto Logout', '2026-05-19 15:40:07', 'User session expired due to inactivity.', '::1'),
(99, 3, 'Document Verified', '2026-05-20 01:17:50', 'Evaluator marked Document #16 as Verified', '::1'),
(100, 6, 'Auto Logout', '2026-05-20 01:23:31', 'User session expired due to inactivity.', '::1'),
(101, 1, 'Auto Logout', '2026-05-20 01:30:55', 'User session expired due to inactivity.', '::1'),
(102, 13, 'Account Created', '2026-05-20 07:16:31', 'A new student account was registered via the portal.', '::1'),
(103, 13, 'Auto Logout', '2026-05-20 07:29:17', 'User session expired due to inactivity.', '::1'),
(104, 14, 'Account Created', '2026-05-20 07:33:09', 'A new student account was registered via the portal.', '::1'),
(105, 6, 'Password Reset', '2026-05-20 07:38:43', 'User successfully changed their password via reset link.', '::1'),
(106, 6, 'Auto Logout', '2026-05-20 07:50:56', 'User session expired due to inactivity.', '::1'),
(107, 2, 'Auto Logout', '2026-05-20 08:08:01', 'User session expired due to inactivity.', '::1'),
(108, 3, 'Auto Logout', '2026-05-20 08:22:03', 'User session expired due to inactivity.', '::1'),
(109, 3, 'Auto Logout', '2026-05-20 08:27:17', 'User session expired due to inactivity.', '::1'),
(110, 6, 'Password Reset', '2026-06-08 13:27:53', 'User successfully changed their password via reset link.', '::1'),
(111, 6, 'Auto Logout', '2026-06-08 13:44:47', 'User session expired due to inactivity.', '::1'),
(112, 6, 'Auto Logout', '2026-06-08 14:01:27', 'User session expired due to inactivity.', '::1'),
(113, 6, 'Auto Logout', '2026-06-09 16:35:11', 'User session expired due to inactivity.', '::1'),
(114, 2, 'Auto Logout', '2026-06-10 01:50:56', 'User session expired due to inactivity.', '::1'),
(115, 2, 'Auto Logout', '2026-06-10 02:16:59', 'User session expired due to inactivity.', '::1'),
(116, 2, 'Auto Logout', '2026-06-10 02:33:51', 'User session expired due to inactivity.', '::1'),
(117, 2, 'Auto Logout', '2026-06-10 03:17:49', 'User session expired due to inactivity.', '::1'),
(118, 2, 'Auto Logout', '2026-06-10 03:44:34', 'User session expired due to inactivity.', '::1'),
(119, 2, 'Auto Logout', '2026-06-10 05:03:55', 'User session expired due to inactivity.', '::1'),
(120, 3, 'Auto Logout', '2026-06-16 00:51:59', 'User session expired due to inactivity.', '::1'),
(121, 6, 'Auto Logout', '2026-06-16 01:47:23', 'User session expired due to inactivity.', '::1'),
(122, 6, 'Auto Logout', '2026-06-16 01:58:44', 'User session expired due to inactivity.', '::1'),
(123, 6, 'Auto Logout', '2026-06-26 02:00:50', 'User session expired due to inactivity.', '::1'),
(124, 3, 'Auto Logout', '2026-06-26 02:16:29', 'User session expired due to inactivity.', '::1'),
(125, 1, 'System Restore', '2026-06-29 00:16:41', 'Super Admin restored the database from a backup file.', '::1'),
(126, 1, 'System Backup', '2026-06-29 00:16:56', 'Super Admin exported a full database backup.', '::1'),
(127, 1, 'Auto Logout', '2026-06-29 00:25:23', 'User session expired due to inactivity.', '::1'),
(128, 1, 'Auto Logout', '2026-06-29 00:37:28', 'User session expired due to inactivity.', '::1'),
(129, 1, 'Auto Logout', '2026-06-29 00:43:42', 'User session expired due to inactivity.', '::1'),
(130, 1, 'Auto Logout', '2026-06-29 00:53:38', 'User session expired due to inactivity.', '::1'),
(131, 1, 'Auto Logout', '2026-06-29 01:03:21', 'User session expired due to inactivity.', '::1'),
(132, 1, 'Auto Logout', '2026-06-29 01:35:02', 'User session expired due to inactivity.', '::1'),
(133, 3, 'Auto Logout', '2026-06-29 01:49:41', 'User session expired due to inactivity.', '::1'),
(134, 3, 'Auto Logout', '2026-06-29 02:00:03', 'User session expired due to inactivity.', '::1'),
(135, 6, 'Auto Logout', '2026-06-29 02:24:24', 'User session expired due to inactivity.', '::1'),
(136, 6, 'Auto Logout', '2026-06-29 02:30:47', 'User session expired due to inactivity.', '::1'),
(137, 6, 'Auto Logout', '2026-06-29 02:51:27', 'User session expired due to inactivity.', '::1'),
(138, 3, 'Auto Logout', '2026-06-29 03:25:25', 'User session expired due to inactivity.', '::1'),
(139, 3, 'Auto Logout', '2026-06-29 03:43:17', 'User session expired due to inactivity.', '::1'),
(140, 3, 'Auto Logout', '2026-06-29 03:55:41', 'User session expired due to inactivity.', '::1'),
(141, 6, 'Auto Logout', '2026-06-29 04:13:40', 'User session expired due to inactivity.', '::1'),
(142, 3, 'Auto Logout', '2026-06-29 05:44:26', 'User session expired due to inactivity.', '::1'),
(143, 6, 'Auto Logout', '2026-06-29 13:44:25', 'User session expired due to inactivity.', '::1'),
(144, 6, 'Auto Logout', '2026-06-29 14:00:54', 'User session expired due to inactivity.', '::1'),
(145, 2, 'Auto Logout', '2026-06-29 14:28:44', 'User session expired due to inactivity.', '::1'),
(146, 1, 'System Restore', '2026-07-01 00:47:10', 'Super Admin restored the database from a backup file.', '::1');

-- --------------------------------------------------------

--
-- Table structure for table `criteria`
--

CREATE TABLE `criteria` (
  `CriteriaID` int(11) NOT NULL,
  `ScholarshipID` int(11) NOT NULL,
  `CriteriaName` varchar(100) NOT NULL,
  `Weight` decimal(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `document_requirement`
--

CREATE TABLE `document_requirement` (
  `RequirementID` int(11) NOT NULL,
  `ScholarshipID` int(11) NOT NULL,
  `DocumentName` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `document_requirement`
--

INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES
(1, 11, 'Certificate of Registration'),
(4, 10, 'Certificate of Registration'),
(5, 9, 'Certificate of Registration'),
(7, 9, 'Report of Grades'),
(8, 12, 'Report of Grades'),
(9, 12, 'Certificate of Registration'),
(10, 98, 'Report of Grades'),
(11, 98, 'Certificate of Registration'),
(12, 203, 'Certificate of Registration');

-- --------------------------------------------------------

--
-- Table structure for table `landing_content`
--

CREATE TABLE `landing_content` (
  `section_key` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `body` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `landing_content`
--

INSERT INTO `landing_content` (`section_key`, `title`, `body`) VALUES
('grants_header', 'Active Scholarships', 'Currently accepting applications for this semester.'),
('hero', 'Unlock your future with ScholarLink.', 'Discover financial assistance programs, track your applications, and focus on your education. Browse the available TAU grants below to get started.'),
('no_grants', 'No active scholarships', 'There are currently no scholarship programs accepting applications. Please check back later.');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `MessageID` int(11) NOT NULL,
  `SenderID` int(11) NOT NULL,
  `ReceiverID` int(11) NOT NULL,
  `MessageText` text NOT NULL,
  `IsRead` tinyint(1) DEFAULT 0,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`MessageID`, `SenderID`, `ReceiverID`, `MessageText`, `IsRead`, `CreatedAt`) VALUES
(1, 6, 2, 'hello', 1, '2026-05-18 14:56:27'),
(2, 2, 6, 'hi', 1, '2026-05-18 14:56:34'),
(3, 6, 2, 'can i ask a question po?', 1, '2026-05-18 14:59:00'),
(4, 2, 6, 'ano po yon?', 1, '2026-05-18 14:59:09'),
(5, 6, 2, 'about po sana sa scholarship', 1, '2026-05-18 15:09:11'),
(6, 2, 6, 'anong scholarship po/', 1, '2026-05-18 15:23:39'),
(7, 6, 2, 'sa cyber sentinel po', 1, '2026-05-18 15:34:59'),
(8, 6, 2, 'ask ko lang po sana', 1, '2026-05-18 15:35:14'),
(9, 2, 6, 'ano pp yon', 1, '2026-05-18 15:40:07'),
(10, 6, 2, 'ano po requirements', 1, '2026-05-19 03:14:10'),
(11, 2, 6, 'nasa applications na po', 1, '2026-05-19 03:15:52'),
(12, 6, 2, '⚠️ AI Escalate: I couldn\'t get an answer for: \"can I talk to the admin\"', 1, '2026-06-08 13:29:47'),
(13, 6, 2, '⚠️ AI Escalate: I couldn\'t get an answer for: \"1+1\"', 1, '2026-06-08 13:49:27'),
(14, 6, 2, '⚠️ AI Escalate: I couldn\'t get an answer for: \"delete convo\"', 1, '2026-06-16 01:22:11'),
(15, 6, 2, '⚠️ AI Escalate: I couldn\'t get an answer for: \"delete chat\"', 1, '2026-06-16 01:30:16');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `NotificationID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `Title` varchar(255) NOT NULL,
  `Message` text NOT NULL,
  `Type` varchar(50) DEFAULT 'info',
  `IsRead` tinyint(1) DEFAULT 0,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`NotificationID`, `UserID`, `Title`, `Message`, `Type`, `IsRead`, `CreatedAt`) VALUES
(1, 6, 'Application Evaluated! ✍️', 'Your application for the Cyber Sentinel Award has been officially scored and Shortlisted by an evaluator.', 'info', 1, '2026-05-17 15:50:38'),
(2, 6, 'Application Approved! 🏆', 'Congratulations! Your application for the Cyber Sentinel Award has been officially Approved.', 'success', 1, '2026-05-19 13:53:55');

-- --------------------------------------------------------

--
-- Table structure for table `program`
--

CREATE TABLE `program` (
  `ProgramID` int(11) NOT NULL,
  `ProgramName` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `program`
--

INSERT INTO `program` (`ProgramID`, `ProgramName`) VALUES
(1, 'BS Agriculture'),
(2, 'Bachelor of Animal Science (BAS)'),
(3, 'BS Forestry'),
(4, 'BS Food Technology'),
(5, 'AB Economics'),
(6, 'BS Psychology'),
(7, 'BS Development Communication'),
(8, 'BS Business Administration'),
(9, 'BS Entrepreneurship'),
(10, 'BS Agribusiness'),
(11, 'BS Tourism Management'),
(12, 'Bachelor of Elementary Education (BEEd)'),
(13, 'Bachelor of Secondary Education (BSEd)'),
(14, 'Bachelor of Early Childhood Education (BECEd)'),
(15, 'Bachelor of Technology and Livelihood Education (BTLEd)'),
(16, 'BS Agricultural and Biosystems Engineering'),
(17, 'BS Geodetic Engineering'),
(18, 'BS Information Technology (BSIT)'),
(19, 'Doctor of Veterinary Medicine (DVM)');

-- --------------------------------------------------------

--
-- Table structure for table `scholarship`
--

CREATE TABLE `scholarship` (
  `ScholarshipID` int(11) NOT NULL,
  `ProgramID` int(11) NOT NULL,
  `YearLevel` varchar(50) DEFAULT NULL,
  `Name` varchar(150) NOT NULL,
  `Description` text DEFAULT NULL,
  `Deadline` date NOT NULL,
  `AwardAmount` decimal(10,2) DEFAULT NULL,
  `NumberOfSlots` int(11) DEFAULT NULL,
  `Status` varchar(20) DEFAULT 'Active',
  `MinimumGWA` decimal(4,2) DEFAULT 2.00,
  `CreatedBy` int(11) DEFAULT NULL,
  `GenderRequirement` varchar(20) NOT NULL DEFAULT 'Any',
  `ScholarshipType` enum('Private','Government') DEFAULT 'Private',
  `FormConfig` varchar(255) DEFAULT 'Academic,Family,Financial,Essay',
  `ReleaseFrequency` varchar(50) DEFAULT 'Per Semester',
  `AllowsDual` enum('Yes','No') NOT NULL DEFAULT 'No'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `scholarship`
--

INSERT INTO `scholarship` (`ScholarshipID`, `ProgramID`, `YearLevel`, `Name`, `Description`, `Deadline`, `AwardAmount`, `NumberOfSlots`, `Status`, `MinimumGWA`, `CreatedBy`, `GenderRequirement`, `ScholarshipType`, `FormConfig`, `ReleaseFrequency`, `AllowsDual`) VALUES
(1, 2, NULL, 'Livestock Excellence Award', 'Financial assistance for Bachelor of Animal Science students focusing on sustainable farming.', '2026-06-30', 15000.00, NULL, 'Active', 2.00, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(2, 3, NULL, 'Forestry Guardians Grant', 'Support for BS Forestry students dedicated to forest conservation and management.', '2026-07-15', 12000.00, NULL, 'Active', 2.00, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(3, 6, NULL, 'CAS Social Science Grant', 'Awarded to outstanding BS Psychology students demonstrating academic excellence.', '2026-08-01', 10000.00, NULL, 'Active', 2.00, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(4, 8, NULL, 'CBM Business Leadership Scholarship', 'For BS Business Administration students with high leadership potential.', '2026-09-12', 20000.00, NULL, 'Active', 2.00, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(5, 11, NULL, 'Global Tourism Ambassadors Fund', 'Travel and study grant for BS Tourism Management undergraduates.', '2026-10-05', 18000.00, NULL, 'Active', 2.00, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(6, 12, NULL, 'Future Educators Subsidy', 'A monthly allowance program for BEEd and BSEd students in their practice teaching year.', '2026-11-20', 8000.00, NULL, 'Active', 2.00, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(7, 16, NULL, 'Engineering Innovation Fund', 'Research grant for Agricultural and Biosystems Engineering students.', '2026-05-15', 25000.00, NULL, 'Active', 2.00, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(8, 19, NULL, 'Veterinary Medicine Aid', 'Partial tuition coverage for high-performing DVM students.', '2026-04-30', 30000.00, NULL, 'Active', 2.00, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(9, 18, NULL, 'Tech Titans Merit Scholarship', 'Awarded to top-performing BSIT students with a GWA of 1.75 or higher who demonstrate strong technical leadership.', '2026-08-15', 25000.00, 50, 'Active', 2.00, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(10, 18, NULL, 'CodeCrafters Innovation Grant', 'Support for students with exceptional portfolios in web development, mobile apps, or software engineering.', '2026-09-01', 15000.00, NULL, 'Active', 2.00, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(11, 18, NULL, 'Digital Backbone Grant', 'Specifically for IT students pursuing specialized certifications in networking, server management, or cloud infrastructure.', '2026-10-10', 12000.00, 50, 'Active', 2.00, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(12, 18, NULL, 'Cyber Sentinel Award', 'A financial aid program for students focusing on information security and ethical hacking projects.', '2026-07-20', 18000.00, 50, 'Active', 2.00, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(13, 1, NULL, 'Agri-Growth Pioneer Grant', 'Financial assistance for students showcasing innovative organic farming practices.', '2026-08-30', 15000.00, 30, 'Active', 2.00, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(14, 1, NULL, 'Sustainable Farming Initiative', 'Support for research projects aiming to reduce irrigation wastage in local Tarlac farms.', '2026-09-15', 18000.00, 20, 'Active', 1.75, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(15, 1, NULL, 'Crop Science Innovations Subsidy', 'Awarded to high-performing crop production majors focusing on climate-resilient grains.', '2026-10-10', 12000.00, 50, 'Active', 2.25, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(16, 1, NULL, 'Soil Health Research Fellowship', 'Grant for laboratory analysis support and regional field profiling initiatives.', '2026-11-05', 20000.00, 15, 'Active', 1.75, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(17, 1, NULL, 'Tarlac Green Harvest Scholarship', 'Socio-economic grant aiding underprivileged children of agricultural families.', '2026-12-01', 10000.00, 100, 'Active', 2.50, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(18, 2, NULL, 'Livestock Management Merit Grant', 'Aimed at production optimization tracking development across regional livestock farms.', '2026-08-20', 14000.00, 25, 'Active', 2.00, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(19, 2, NULL, 'Poultry Industry Future Leaders Subsidy', 'Industry-backed incentive for poultry genetics and nutrition development research.', '2026-09-25', 16000.00, 30, 'Active', 2.00, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(20, 2, NULL, 'Animal Nutrition Research Fund', 'Covers textbook fees and feed formulation equipment resources for final-year thesis groups.', '2026-10-15', 22000.00, 10, 'Active', 1.50, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(21, 2, NULL, 'Swine Herd Health Excellence Support', 'Focuses on biosecurity enforcement methods and veterinary support systems.', '2026-11-18', 19000.00, 20, 'Active', 1.75, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(22, 2, NULL, 'Pasture & Range Management Assistance', 'Tuition support focused on zero-grazing innovations and forage preservation.', '2026-12-10', 12000.00, 40, 'Active', 2.25, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(23, 3, NULL, 'Forest Ecosystem Preservation Fund', 'Targeting conservation leaders mapping forest reserves and monitoring logging entries.', '2026-08-15', 13000.00, 35, 'Active', 2.00, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(24, 3, NULL, 'Watershed Management Fellowship', 'Specialized stipend for tracking water sources and river basin safety indicators.', '2026-09-30', 17000.00, 15, 'Active', 1.75, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(25, 3, NULL, 'Dendrology Research Support Grant', 'Provides mapping gear kits and allowances for comprehensive high-altitude mountain field works.', '2026-10-22', 21000.00, 12, 'Active', 1.50, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(26, 3, NULL, 'Reforestation Tech Scholarship', 'Aiding system applications leveraging drone monitoring for seed dispersal validation.', '2026-11-12', 15500.00, 25, 'Active', 2.00, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(27, 3, NULL, 'Canopy Conservationist Stipend', 'General retention grant reinforcing academic consistency for forestry practitioners.', '2026-12-15', 11000.00, 50, 'Active', 2.50, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(28, 4, NULL, 'Food Safety & Security Fellowship', 'Focused on tracking compliance rules across local food distribution channels.', '2026-08-25', 16000.00, 20, 'Active', 1.75, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(29, 4, NULL, 'Post-Harvest Processing Innovation Grant', 'Subsidizing technological upgrades reducing raw produce damage rates during delivery.', '2026-09-18', 24000.00, 10, 'Active', 1.50, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(30, 4, NULL, 'Nutrition & Product Development Subsidy', 'Assisting indigenous ingredient exploration for low-cost student cafeteria meals.', '2026-10-30', 13500.00, 40, 'Active', 2.00, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(31, 4, NULL, 'Food Quality Assurance Merit Scholarship', 'Prepares students for international ISO auditing certifications.', '2026-11-15', 17500.00, 30, 'Active', 1.75, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(32, 4, NULL, 'Fermentation Technology Laboratory Support', 'Aids microbiological development and structural bioprocess research.', '2026-12-20', 15000.00, 25, 'Active', 2.25, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(33, 5, NULL, 'Agrarian Economic Policy Fellowship', 'Analyzes tracking parameters governing agricultural pricing structures and supply lines.', '2026-08-11', 15000.00, 25, 'Active', 1.75, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(34, 5, NULL, 'Rural Development Analytics Grant', 'Data mining sponsorship analyzing provincial financial inequality factors.', '2026-09-14', 18500.00, 20, 'Active', 1.75, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(35, 5, NULL, 'Micro-Finance Empowerment Stipend', 'Encourages tracking lending optimizations aiding cooperative farming models.', '2026-10-05', 12500.00, 45, 'Active', 2.00, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(36, 5, NULL, 'Macroeconomic Market Research Scholarship', 'Incentive for mathematical parsing of import-export indicators across Central Luzon.', '2026-11-20', 21000.00, 15, 'Active', 1.50, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(37, 5, NULL, 'Provincial Fiscal Planning Initiative', 'Sponsored by municipal auditing networks for top accounting-capable students.', '2026-12-12', 14000.00, 30, 'Active', 2.25, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(38, 6, NULL, 'Mental Health Advocacy Fellowship', 'Enforces structural deployment of counseling access configurations in rural health units.', '2026-08-28', 13000.00, 40, 'Active', 2.00, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(39, 6, NULL, 'Behavioral Science Research Grant', 'Statistical psychometric validation support targeting student stress vectors.', '2026-09-19', 16500.00, 25, 'Active', 1.75, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(40, 6, NULL, 'Industrial Psychology Leadership Award', 'Aiding workforce assessment tool optimizations for corporate training contexts.', '2026-10-25', 15000.00, 30, 'Active', 2.00, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(41, 6, NULL, 'Community Guidance Counseling Stipend', 'Field work financial support tracking rehabilitation programs for youth centers.', '2026-11-14', 14500.00, 35, 'Active', 2.25, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(42, 6, NULL, 'Cognitive Analytics Development Fund', 'Neurological profiling software subscription access grants for honor students.', '2026-12-18', 22000.00, 10, 'Active', 1.50, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(43, 7, NULL, 'Community Broadcasting Extension Grant', 'Radio and broadcasting production assistance highlighting rural sector breakthroughs.', '2026-08-16', 12000.00, 30, 'Active', 2.25, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(44, 7, NULL, 'Rural Journalism & Advocacy Subsidy', 'Supporting field publication networks recording regional micro-farming testimonies.', '2026-09-22', 14000.00, 40, 'Active', 2.00, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(45, 7, NULL, 'Digital DevCom Media Innovation Scholarship', 'Leveraging graphics, editing engines, and social mechanics for literacy extensions.', '2026-10-12', 16000.00, 25, 'Active', 1.75, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(46, 7, NULL, 'Agricultural Information Dissemination Fund', 'Combats farming misinformation via text-blast translations and infographic models.', '2026-11-30', 15000.00, 35, 'Active', 2.00, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(47, 7, NULL, 'Social Behavior Change Communication Fellowship', 'Focuses on strategic media deployment validating health and wellness habits.', '2026-12-05', 19000.00, 15, 'Active', 1.75, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(48, 8, NULL, 'Corporate Excellence & Ethics Grant', 'Leadership training funding combined with direct commercial banking mentorship loops.', '2026-08-04', 17000.00, 30, 'Active', 1.75, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(49, 8, NULL, 'Agri-Business Enterprise Management Fund', 'Strategic business development aid focused on large-scale crop trading firms.', '2026-09-08', 21000.00, 20, 'Active', 1.50, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(50, 8, NULL, 'Marketing Strategy Leadership Scholarship', 'Focused on expanding e-commerce visibility for cooperative product lines.', '2026-10-18', 13000.00, 50, 'Active', 2.25, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(51, 8, NULL, 'Financial Management Merit Stipend', 'Wipes balance matrices for students exhibiting elite accounting algorithm mastery.', '2026-11-24', 18500.00, 25, 'Active', 1.75, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(52, 8, NULL, 'Young Executives Advancement Support', 'General stipend assisting low-income students maintaining commercial skill focuses.', '2026-12-19', 11000.00, 60, 'Active', 2.50, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(53, 9, NULL, 'Innovation & Startup Incubator Grant', 'Seed funding built directly into structural tuition relief packages.', '2026-08-14', 25000.00, 15, 'Active', 2.00, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(54, 9, NULL, 'Micro-Enterprise Growth Subsidy', 'Assists prototyping operations tracking local raw item processing methods.', '2026-09-29', 15000.00, 35, 'Active', 2.25, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(55, 9, NULL, 'Social Entrepreneurship Venture Fund', 'Encourages eco-friendly manufacturing ventures employing indigenous staff structures.', '2026-10-09', 20000.00, 20, 'Active', 1.75, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(56, 9, NULL, 'E-Commerce Advancement Scholarship', 'Optimizes platform setups tracing trade logistics and alternative checkout loops.', '2026-11-15', 16500.00, 30, 'Active', 2.00, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(57, 9, NULL, 'Rural Youth Enterprise Initiative', 'Incubator subsidy backing high-potential food cart business mechanics.', '2026-12-14', 14000.00, 40, 'Active', 2.50, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(58, 10, NULL, 'Agro-Industrial Supply Chain Grant', 'Mitigates distribution delays by tracking optimized routes for field drop-offs.', '2026-08-19', 18000.00, 25, 'Active', 1.75, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(59, 10, NULL, 'Farm-to-Market Logistics Fellowship', 'System analysis funding studying cold-chain storage implementations.', '2026-09-11', 19500.00, 20, 'Active', 1.75, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(60, 10, NULL, 'Agricultural Commodities Trading Fund', 'Teaches futures modeling and international balance tracking metrics.', '2026-10-21', 22000.00, 15, 'Active', 1.50, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(61, 10, NULL, 'Sustainable Value Chain Innovation Scholarship', 'Aims to eliminate middleman processing fees to directly enrich farm sources.', '2026-11-13', 16000.00, 30, 'Active', 2.00, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(62, 10, NULL, 'Cooperative Management Leadership Support', 'Reinforces accounting transparent data systems for local farmer networks.', '2026-12-08', 13500.00, 45, 'Active', 2.25, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(63, 11, NULL, 'Eco-Tourism Heritage Preservation Grant', 'Sponsors cultural protection mapping operations across central landmarks.', '2026-08-27', 14000.00, 30, 'Active', 2.00, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(64, 11, NULL, 'Hospitality Operations Excellence Subsidy', 'Covers uniform design packages and property management workflow training fees.', '2026-09-15', 15000.00, 40, 'Active', 2.25, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(65, 11, NULL, 'Agritourism Destination Development Fund', 'Promotes farm tour executions leveraging ecological education formats.', '2026-10-14', 17500.00, 25, 'Active', 1.75, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(66, 11, NULL, 'Leisure & Event Management Scholarship', 'Event coordination blueprint structuring awards for project handling groups.', '2026-11-22', 13000.00, 50, 'Active', 2.00, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(67, 11, NULL, 'Cultural Tourism Advocacy Fellowship', 'Sponsors video content production tracking alternative travel destinations.', '2026-12-23', 16000.00, 20, 'Active', 1.75, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(68, 12, NULL, 'Primary Literacy Educators Foundation Grant', 'Provides reading diagnostics modules for community learning hubs.', '2026-08-09', 11500.00, 50, 'Active', 2.00, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(69, 12, NULL, 'Early Childhood Pedagogy Merit Scholarship', 'Instructional design support exploring tactile child development tracking devices.', '2026-09-18', 13500.00, 45, 'Active', 1.75, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(70, 12, NULL, 'Special Needs Education Inclusion Stipend', 'Focuses on structural learning methods for speech-delayed child brackets.', '2026-10-29', 18000.00, 20, 'Active', 1.50, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(71, 12, NULL, 'Rural Primary School Teacher Incentive', 'Aiding transportation fees during comprehensive barangay demonstration layouts.', '2026-11-15', 12000.00, 60, 'Active', 2.25, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(72, 12, NULL, 'Elementary Mathematics Teaching Innovation Fund', 'Gamified learning tracking tools application support for young demographics.', '2026-12-04', 15000.00, 30, 'Active', 2.00, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(73, 13, NULL, '制造 STEM Secondary Teacher Advancement Grant', 'Science and Math major optimization fund boosting diagnostic laboratory performance.', '2026-08-23', 14500.00, 40, 'Active', 1.75, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(74, 13, NULL, 'Humanities & Social Sciences Educators Fund', 'History curriculum planning support tracing multi-cultural baseline changes.', '2026-09-04', 12500.00, 50, 'Active', 2.00, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(75, 13, NULL, 'Language & Literacy Instruction Fellowship', 'English-Filipino bilingual tracking matrix research subsidy.', '2026-10-19', 13000.00, 45, 'Active', 2.25, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(76, 13, NULL, 'Educational Technology Integration Scholarship', 'Prepares student mentors inside Google/Microsoft learning suite architectures.', '2026-11-11', 16000.00, 35, 'Active', 1.75, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(77, 13, NULL, 'Secondary Level Guidance Mentorship Award', 'Assists psychological evaluation framework mapping for junior-high demographics.', '2026-12-14', 15500.00, 30, 'Active', 2.00, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(78, 14, NULL, 'Early Years Cognitive Development Grant', 'Flashcard validation and behavior logging engine design support.', '2026-08-31', 12000.00, 35, 'Active', 2.25, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(79, 14, NULL, 'Play-Based Learning Pedagogy Scholarship', 'Backing sensory integration tool procurement for nursery testing rooms.', '2026-09-27', 14000.00, 30, 'Active', 2.00, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(80, 14, NULL, 'Child Psychology & Growth Fellowship', 'Tracks developmental abnormalities tracking criteria flags in early learners.', '2026-10-06', 17000.00, 20, 'Active', 1.75, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(81, 14, NULL, 'Kindergarten Instruction Excellence Fund', 'Visual aid construction allowance supporting public center deployments.', '2026-11-20', 11000.00, 55, 'Active', 2.50, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(82, 14, NULL, 'Foundational Literacy & Numeracy Stipend', 'Combats processing difficulties through phonics mapping structures.', '2026-12-10', 13500.00, 40, 'Active', 2.00, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(83, 15, NULL, 'Home Economics Vocational Training Grant', 'Culinary inventory tooling allowance mixed with nutrition scaling software tracking.', '2026-08-12', 13000.00, 40, 'Active', 2.25, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(84, 15, NULL, 'Industrial Arts Technical Instructional Fund', 'Drafting toolsets and safety hardware configuration subsidies for shop rooms.', '2026-09-26', 15500.00, 30, 'Active', 2.00, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(85, 15, NULL, 'Agri-Fishery Arts Education Scholarship', 'Aquaponics setup installation training packages linked to educational modules.', '2026-10-15', 16500.00, 25, 'Active', 1.75, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(86, 15, NULL, 'Livelihood Skills Empowerment Subsidy', 'Sponsors micro-skill tracking structures inside community dressmaking classes.', '2026-11-29', 12000.00, 50, 'Active', 2.50, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(87, 15, NULL, 'Entrepreneurial Tech Education Fellowship', 'Prepares vocational leaders to instruct basic business bookkeeping systems.', '2026-12-05', 14500.00, 35, 'Active', 2.00, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(88, 16, NULL, 'Farm Mechanization Design Fellowship', 'CAD software mapping licenses focused on custom sorting harvester upgrades.', '2026-08-08', 22000.00, 15, 'Active', 1.75, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(89, 16, NULL, 'Irrigation & Water Systems Engineering Grant', 'Math processing algorithms tracing pipeline friction losses in rural lines.', '2026-09-19', 24000.00, 12, 'Active', 1.50, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(90, 16, NULL, 'Renewable Energy Bio-Systems Research Fund', 'Biomass conversion optimization engineering framework research.', '2026-10-31', 26000.00, 10, 'Active', 1.50, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(91, 16, NULL, 'Precision Agriculture Machinery Scholarship', 'Sensor integration parameters tracing automated pesticide deployment lines.', '2026-11-14', 20000.00, 20, 'Active', 1.75, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(92, 16, NULL, 'Post-Harvest Infrastructure Engineering Stipend', 'Storage structural calculation logs checking aeration system efficiency rules.', '2026-12-11', 18000.00, 25, 'Active', 2.00, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(93, 17, NULL, 'Land Surveying & Geomatics Excellence Grant', 'Total station equipment calibration tracking allowances and processing kits.', '2026-08-21', 19000.00, 20, 'Active', 1.75, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(94, 17, NULL, 'GIS & Spatial Mapping Research Fellowship', 'ArcGIS layer processing configurations tracing provincial zone modifications.', '2026-09-05', 23000.00, 15, 'Active', 1.50, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(95, 17, NULL, 'Remote Sensing Technological Innovation Fund', 'Satellite data parsing logic verifying structural mapping change arrays.', '2026-10-24', 25000.00, 10, 'Active', 1.50, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(96, 17, NULL, 'Cadastral Surveying Professional Support', 'Boundary dispute evaluation logs logic checking legal real estate criteria.', '2026-11-18', 17000.00, 25, 'Active', 2.00, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(97, 17, NULL, 'Hydrographic Mapping Academic Scholarship', 'Underwater contour tracking data configurations optimizing river management lines.', '2026-12-15', 21500.00, 18, 'Active', 1.75, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(98, 18, NULL, 'Cloud Architecture & DevOps Excellence Fund', 'Subsidizes container deployment metrics modeling, secure network pooling and pfSense rules.', '2026-08-12', 20000.00, 40, 'Active', 1.75, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(99, 18, NULL, 'Full-Stack Systems Automation Scholarship', 'For students developing Ionic, Angular, and PHP applications optimizing institutional work.', '2026-09-28', 22000.00, 30, 'Active', 1.50, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(100, 18, NULL, 'AI & Data Analytics Research Fellowship', 'Python matrix parsing validation algorithms handling extensive agricultural data charts.', '2026-10-15', 25000.00, 15, 'Active', 1.50, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(101, 18, NULL, 'UI/UX Design & Human-Computer Synergy Grant', 'Dashboard interface testing optimization frameworks checking responsive front-ends.', '2026-11-19', 15000.00, 50, 'Active', 2.00, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(102, 18, NULL, 'Mobile Application Development Innovation Stipend', 'Hardware support allowance boosting API loading optimizations inside regional structures.', '2026-12-22', 18000.00, 35, 'Active', 1.75, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(103, 19, NULL, 'Large Animal Clinical Medicine Fellowship', 'Equine and bovine pathology treatment field tracking allowances.', '2026-08-07', 28000.00, 10, 'Active', 1.50, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(104, 19, NULL, 'Zoonotic Disease Epidemiology Research Grant', 'Tracking logic checking virus spreading parameters across wildlife interfaces.', '2026-09-14', 32000.00, 8, 'Active', 1.25, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(105, 19, NULL, 'Small Animal Surgery Excellence Scholarship', 'Surgical laboratory material support and anesthesia equipment handling checks.', '2026-10-26', 24000.00, 15, 'Active', 1.75, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(106, 19, NULL, 'Veterinary Public Health Advocacy Fund', 'Meat safety inspection compliance tracking across municipal drop-off decks.', '2026-11-30', 21000.00, 20, 'Active', 2.00, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(107, 19, NULL, 'Wildlife & Exotic Fauna Conservation Stipend', 'Sponsors wildlife center training rotations preserving protective environments.', '2026-12-12', 26000.00, 12, 'Active', 1.50, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(108, 1, NULL, 'Agri-Tech Innovation Grant', 'Support for organic farming technology.', '2026-12-31', 15000.00, 10, 'Active', 2.00, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(109, 1, NULL, 'Sustainable Yield Subsidy', 'Assistance for sustainable yield projects.', '2026-12-31', 12000.00, 10, 'Active', 2.25, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(110, 1, NULL, 'Future Agronomist Stipend', 'Grant for promising agriculture students.', '2026-12-31', 10000.00, 15, 'Active', 2.50, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(111, 1, NULL, 'Soil Science Research Fund', 'Funding for soil health research.', '2026-12-31', 20000.00, 5, 'Active', 1.75, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(112, 1, NULL, 'Local Farm Cooperative Grant', 'Grant for students engaging with local coops.', '2026-12-31', 8000.00, 20, 'Active', 2.50, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(113, 2, NULL, 'Livestock Management Grant', 'Support for livestock industry focus.', '2026-12-31', 15000.00, 10, 'Active', 2.00, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(114, 2, NULL, 'Veterinary Tech Assistance', 'Support for vet science research.', '2026-12-31', 12000.00, 10, 'Active', 2.25, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(115, 2, NULL, 'Animal Nutrition Award', 'Incentive for top nutrition students.', '2026-12-31', 10000.00, 15, 'Active', 2.50, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(116, 2, NULL, 'Sustainable Poultry Initiative', 'Grant for poultry tech innovation.', '2026-12-31', 20000.00, 5, 'Active', 1.75, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(117, 2, NULL, 'Rural Animal Welfare Fund', 'Support for animal protection projects.', '2026-12-31', 8000.00, 20, 'Active', 2.50, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(118, 3, NULL, 'Forest Ecosystem Grant', 'Support for forest conservation projects.', '2026-12-31', 15000.00, 10, 'Active', 2.00, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(119, 3, NULL, 'Watershed Management Aid', 'Funding for water resource management.', '2026-12-31', 12000.00, 10, 'Active', 2.25, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(120, 3, NULL, 'Forestry Leadership Award', 'Award for top forestry students.', '2026-12-31', 10000.00, 15, 'Active', 2.50, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(121, 3, NULL, 'Tree Nursery Tech Grant', 'Grant for nursery innovation.', '2026-12-31', 20000.00, 5, 'Active', 1.75, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(122, 3, NULL, 'Community Forestry Stipend', 'Grant for community outreach.', '2026-12-31', 8000.00, 20, 'Active', 2.50, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(123, 4, NULL, 'Food Safety Excellence Grant', 'Award for food safety research.', '2026-12-31', 15000.00, 10, 'Active', 2.00, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(124, 4, NULL, 'Product Innovation Subsidy', 'Grant for food product development.', '2026-12-31', 12000.00, 10, 'Active', 2.25, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(125, 4, NULL, 'Nutrition Science Award', 'Support for nutrition tech students.', '2026-12-31', 10000.00, 15, 'Active', 2.50, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(126, 4, NULL, 'Quality Assurance Fellowship', 'Incentive for QA compliance.', '2026-12-31', 20000.00, 5, 'Active', 1.75, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(127, 4, NULL, 'Laboratory Operations Grant', 'Funding for food lab work.', '2026-12-31', 8000.00, 20, 'Active', 2.50, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(128, 5, NULL, 'Market Analysis Grant', 'Grant for economic policy studies.', '2026-12-31', 15000.00, 10, 'Active', 2.00, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(129, 5, NULL, 'Rural Development Scholarship', 'Support for rural econ focus.', '2026-12-31', 12000.00, 10, 'Active', 2.25, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(130, 5, NULL, 'Finance Empowerment Fund', 'Grant for micro-finance studies.', '2026-12-31', 10000.00, 15, 'Active', 2.50, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(131, 5, NULL, 'Macroeconomic Research Aid', 'Funding for macro econ research.', '2026-12-31', 20000.00, 5, 'Active', 1.75, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(132, 5, NULL, 'Policy Advocacy Grant', 'Incentive for policy debate students.', '2026-12-31', 8000.00, 20, 'Active', 2.50, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(133, 6, NULL, 'Psychological Advocacy Grant', 'Support for mental health awareness.', '2026-12-31', 15000.00, 10, 'Active', 2.00, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(134, 6, NULL, 'Research Excellence Subsidy', 'Grant for behavioral research.', '2026-12-31', 12000.00, 10, 'Active', 2.25, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(135, 6, NULL, 'Counseling Leadership Award', 'Award for counseling excellence.', '2026-12-31', 10000.00, 15, 'Active', 2.50, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(136, 6, NULL, 'Social Science Fellowship', 'Fellowship for psychology majors.', '2026-12-31', 20000.00, 5, 'Active', 1.75, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(137, 6, NULL, 'Human Behavior Tech Grant', 'Grant for tech-based behavioral studies.', '2026-12-31', 8000.00, 20, 'Active', 2.50, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(138, 7, NULL, 'DevCom Media Grant', 'Support for development journalism.', '2026-12-31', 15000.00, 10, 'Active', 2.00, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(139, 7, NULL, 'Broadcasting Excellence Subsidy', 'Grant for broadcast media students.', '2026-12-31', 12000.00, 10, 'Active', 2.25, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(140, 7, NULL, 'Digital Advocacy Award', 'Award for social media campaigns.', '2026-12-31', 10000.00, 15, 'Active', 2.50, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(141, 7, NULL, 'Communication Fellowship', 'Support for advocacy projects.', '2026-12-31', 20000.00, 5, 'Active', 1.75, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(142, 7, NULL, 'Rural Media Outreach Fund', 'Grant for rural media outreach.', '2026-12-31', 8000.00, 20, 'Active', 2.50, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(143, 8, NULL, 'Corporate Leadership Grant', 'Grant for future business leaders.', '2026-12-31', 15000.00, 10, 'Active', 2.00, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(144, 8, NULL, 'Marketing Excellence Subsidy', 'Support for top marketing students.', '2026-12-31', 12000.00, 10, 'Active', 2.25, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(145, 8, NULL, 'Accounting Merit Award', 'Award for financial accounting.', '2026-12-31', 10000.00, 15, 'Active', 2.50, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(146, 8, NULL, 'Business Ethics Fellowship', 'Fellowship for business integrity.', '2026-12-31', 20000.00, 5, 'Active', 1.75, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(147, 8, NULL, 'Entrepreneurial Finance Fund', 'Support for business start-ups.', '2026-12-31', 8000.00, 20, 'Active', 2.50, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(148, 9, NULL, 'Startup Incubator Grant', 'Funding for new business ventures.', '2026-12-31', 15000.00, 10, 'Active', 2.00, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(149, 9, NULL, 'Micro-Enterprise Subsidy', 'Grant for small business owners.', '2026-12-31', 12000.00, 10, 'Active', 2.25, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(150, 9, NULL, 'Social Venture Award', 'Award for social enterprise.', '2026-12-31', 10000.00, 15, 'Active', 2.50, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(151, 9, NULL, 'Business Strategy Fellowship', 'Grant for strategy planning.', '2026-12-31', 20000.00, 5, 'Active', 1.75, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(152, 9, NULL, 'Future Entrep Stipend', 'General support for entrep students.', '2026-12-31', 8000.00, 20, 'Active', 2.50, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(153, 10, NULL, 'Agri-Supply Chain Grant', 'Grant for supply chain efficiency.', '2026-12-31', 15000.00, 10, 'Active', 2.00, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(154, 10, NULL, 'Market Logistics Subsidy', 'Support for logistics management.', '2026-12-31', 12000.00, 10, 'Active', 2.25, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(155, 10, NULL, 'Commodity Trading Award', 'Award for trading simulation.', '2026-12-31', 10000.00, 15, 'Active', 2.50, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(156, 10, NULL, 'Agri-Business Fellowship', 'Fellowship for agribusiness tech.', '2026-12-31', 20000.00, 5, 'Active', 1.75, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(157, 10, NULL, 'Value Chain Innovation Fund', 'Grant for value chain innovation.', '2026-12-31', 8000.00, 20, 'Active', 2.50, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(158, 11, NULL, 'Tourism Heritage Grant', 'Grant for cultural preservation.', '2026-12-31', 15000.00, 10, 'Active', 2.00, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(159, 11, NULL, 'Hospitality Service Subsidy', 'Support for hospitality students.', '2026-12-31', 12000.00, 10, 'Active', 2.25, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(160, 11, NULL, 'Destination Dev Award', 'Award for tourism development.', '2026-12-31', 10000.00, 15, 'Active', 2.50, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(161, 11, NULL, 'Event Management Fellowship', 'Fellowship for event planning.', '2026-12-31', 20000.00, 5, 'Active', 1.75, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(162, 11, NULL, 'Travel Tech Stipend', 'Grant for travel technology.', '2026-12-31', 8000.00, 20, 'Active', 2.50, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(163, 12, NULL, 'Primary Literacy Grant', 'Grant for literacy teachers.', '2026-12-31', 15000.00, 10, 'Active', 2.00, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(164, 12, NULL, 'Early Ed Pedagogic Subsidy', 'Support for early childhood education.', '2026-12-31', 12000.00, 10, 'Active', 2.25, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(165, 12, NULL, 'Inclusive Education Award', 'Award for special needs inclusion.', '2026-12-31', 10000.00, 15, 'Active', 2.50, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(166, 12, NULL, 'Primary Teaching Fellowship', 'Fellowship for elementary training.', '2026-12-31', 20000.00, 5, 'Active', 1.75, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(167, 12, NULL, 'Math Literacy Stipend', 'Grant for numeracy instruction.', '2026-12-31', 8000.00, 20, 'Active', 2.50, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(168, 13, NULL, 'STEM Teacher Advancement Grant', 'Grant for STEM educators.', '2026-12-31', 15000.00, 10, 'Active', 2.00, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(169, 13, NULL, 'Humanities Social Science Fund', 'Support for social science teaching.', '2026-12-31', 12000.00, 10, 'Active', 2.25, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(170, 13, NULL, 'Bilingual Instruction Award', 'Award for literacy training.', '2026-12-31', 10000.00, 15, 'Active', 2.50, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(171, 13, NULL, 'EdTech Integration Fellowship', 'Fellowship for educational technology.', '2026-12-31', 20000.00, 5, 'Active', 1.75, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(172, 13, NULL, 'Guidance Mentorship Stipend', 'Support for school counseling.', '2026-12-31', 8000.00, 20, 'Active', 2.50, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(173, 14, NULL, 'Early Cognitive Grant', 'Grant for cognitive science.', '2026-12-31', 15000.00, 10, 'Active', 2.00, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(174, 14, NULL, 'Play-Based Subsidy', 'Support for play-based curricula.', '2026-12-31', 12000.00, 10, 'Active', 2.25, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(175, 14, NULL, 'Child Psychology Award', 'Award for child psych majors.', '2026-12-31', 10000.00, 15, 'Active', 2.50, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(176, 14, NULL, 'Foundational Literacy Fund', 'Funding for foundational reading.', '2026-12-31', 20000.00, 5, 'Active', 1.75, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(177, 14, NULL, 'Early Ed Excellence Stipend', 'Support for early childhood teaching.', '2026-12-31', 8000.00, 20, 'Active', 2.50, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(178, 15, NULL, 'Vocational Home Econ Grant', 'Grant for home ec vocational training.', '2026-12-31', 15000.00, 10, 'Active', 2.00, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(179, 15, NULL, 'Industrial Tech Subsidy', 'Support for industrial arts tech.', '2026-12-31', 12000.00, 10, 'Active', 2.25, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(180, 15, NULL, 'Agri-Fishery Arts Award', 'Award for fishery/agri techs.', '2026-12-31', 10000.00, 15, 'Active', 2.50, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(181, 15, NULL, 'Livelihood Skills Fellowship', 'Fellowship for livelihood skills.', '2026-12-31', 20000.00, 5, 'Active', 1.75, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(182, 15, NULL, 'Entrep Tech Education Stipend', 'Grant for entrepreneurial teaching.', '2026-12-31', 8000.00, 20, 'Active', 2.50, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(183, 16, NULL, 'Farm Mechanization Grant', 'Support for farm mechanization tech.', '2026-12-31', 15000.00, 10, 'Active', 2.00, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(184, 16, NULL, 'Irrigation Systems Subsidy', 'Grant for irrigation engineering.', '2026-12-31', 12000.00, 10, 'Active', 2.25, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(185, 16, NULL, 'Renewable Energy Award', 'Award for bio-energy engineering.', '2026-12-31', 10000.00, 15, 'Active', 2.50, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(186, 16, NULL, 'Precision Agri Fellowship', 'Fellowship for precision agriculture.', '2026-12-31', 20000.00, 5, 'Active', 1.75, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(187, 16, NULL, 'Post-Harvest Eng Stipend', 'Funding for storage tech engineering.', '2026-12-31', 8000.00, 20, 'Active', 2.50, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(188, 17, NULL, 'Land Surveying Grant', 'Support for geodetic surveying.', '2026-12-31', 15000.00, 10, 'Active', 2.00, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(189, 17, NULL, 'GIS Research Subsidy', 'Grant for GIS and spatial mapping.', '2026-12-31', 12000.00, 10, 'Active', 2.25, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(190, 17, NULL, 'Remote Sensing Award', 'Award for remote sensing tech.', '2026-12-31', 10000.00, 15, 'Active', 2.50, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(191, 17, NULL, 'Cadastral Survey Fellowship', 'Fellowship for cadastral mapping.', '2026-12-31', 20000.00, 5, 'Active', 1.75, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(192, 17, NULL, 'Hydrographic Mapping Stipend', 'Grant for aquatic survey.', '2026-12-31', 8000.00, 20, 'Active', 2.50, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(193, 18, NULL, 'IT Systems Automation Grant', 'Support for IT system automation.', '2026-12-31', 15000.00, 10, 'Active', 2.00, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(194, 18, NULL, 'Full-Stack Development Subsidy', 'Grant for web development excellence.', '2026-12-31', 12000.00, 10, 'Active', 2.25, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(195, 18, NULL, 'AI Research Fellowship', 'Fellowship for AI and Data Science.', '2026-12-31', 10000.00, 15, 'Active', 2.50, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(196, 18, NULL, 'UI/UX Excellence Award', 'Award for design excellence.', '2026-12-31', 20000.00, 5, 'Active', 1.75, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(197, 18, NULL, 'Mobile Development Grant', 'Support for mobile app dev.', '2026-12-31', 8000.00, 20, 'Active', 2.50, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(198, 19, NULL, 'Large Animal Medicine Grant', 'Grant for veterinary clinical med.', '2026-12-31', 15000.00, 10, 'Active', 2.00, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(199, 19, NULL, 'Zoonotic Research Subsidy', 'Support for zoonotic disease study.', '2026-12-31', 12000.00, 10, 'Active', 2.25, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(200, 19, NULL, 'Small Animal Surgery Award', 'Award for vet surgery majors.', '2026-12-31', 10000.00, 15, 'Active', 2.50, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(201, 19, NULL, 'Veterinary Public Health Fund', 'Funding for vet public health projects.', '2026-12-31', 20000.00, 5, 'Active', 1.75, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(202, 19, NULL, 'Wildlife Conservation Stipend', 'Grant for wildlife vet medicine.', '2026-12-31', 8000.00, 20, 'Active', 2.50, 3, 'Any', 'Government', 'Academic,Family,Financial,Essay', 'Per Semester', 'No'),
(203, 18, NULL, 'asfasfas', 'asfadsfasfas', '2026-07-30', 10000.00, 20, 'Active', 1.50, 3, 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Year', 'No');

-- --------------------------------------------------------

--
-- Table structure for table `scholarship_criteria`
--

CREATE TABLE `scholarship_criteria` (
  `CriteriaID` int(11) NOT NULL,
  `ScholarshipID` int(11) NOT NULL,
  `CriteriaName` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `scholarship_criteria`
--

INSERT INTO `scholarship_criteria` (`CriteriaID`, `ScholarshipID`, `CriteriaName`) VALUES
(1, 11, 'Essay'),
(2, 12, 'Essay'),
(3, 98, 'Essay');

-- --------------------------------------------------------

--
-- Table structure for table `scholarship_custom_fields`
--

CREATE TABLE `scholarship_custom_fields` (
  `FieldID` int(11) NOT NULL,
  `ScholarshipID` int(11) NOT NULL,
  `FieldName` varchar(255) NOT NULL,
  `FieldType` enum('Text','Textarea','Number','Date') DEFAULT 'Text'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `score`
--

CREATE TABLE `score` (
  `ScoreID` int(11) NOT NULL,
  `ApplicationID` int(11) NOT NULL,
  `CriteriaID` int(11) NOT NULL,
  `EvaluatorID` int(11) DEFAULT NULL,
  `ScoreValue` decimal(5,2) NOT NULL,
  `Comments` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `submitted_document`
--

CREATE TABLE `submitted_document` (
  `SubmittedDocID` int(11) NOT NULL,
  `ApplicationID` int(11) NOT NULL,
  `RequirementID` int(11) NOT NULL,
  `FilePath` varchar(255) NOT NULL,
  `UploadDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `VerificationStatus` varchar(50) DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `submitted_document`
--

INSERT INTO `submitted_document` (`SubmittedDocID`, `ApplicationID`, `RequirementID`, `FilePath`, `UploadDate`, `VerificationStatus`) VALUES
(4, 6, 1, '../uploads/documents/APP6_REQ1_1774102516.png', '2026-03-21 14:15:16', 'Verified'),
(5, 7, 4, '../uploads/documents/STU-6-APP-7-REQ-4-VAULT-69c0a47204486.png', '2026-03-23 02:24:50', 'Verified'),
(10, 5, 7, 'uploads/documents/STU-6-APP-5-REQ-7-69c0ca4c6b76e.png', '2026-03-23 05:06:20', 'Verified'),
(11, 5, 5, 'uploads/documents/STU-6-APP-5-REQ-5-69c0cdac3ea71.png', '2026-03-23 05:20:44', 'Verified'),
(12, 3, 5, 'uploads/documents/STU-4-APP-3-REQ-5-69c0dacf8e768.png', '2026-03-23 06:16:47', 'Verified'),
(13, 3, 7, 'uploads/documents/STU-4-APP-3-REQ-7-69c0dad557300.png', '2026-03-23 06:16:53', 'Verified'),
(14, 11, 9, 'uploads/vault/6/1774231846_Screenshot 2026-02-20 174228.png', '2026-05-15 11:29:21', 'Verified'),
(15, 11, 8, 'uploads/documents/STU-6-APP-11-REQ-8-6a0c6af542c40.pdf', '2026-05-19 13:51:49', 'Verified'),
(16, 16, 10, 'uploads/documents/STU-6-APP-16-REQ-10-6a0c7d35cf168.pdf', '2026-05-19 15:09:41', 'Verified'),
(17, 16, 11, 'uploads/vault/6/1774231846_Screenshot 2026-02-20 174228.png', '2026-05-19 15:09:53', 'Pending'),
(18, 18, 12, '../uploads/documents/STU-6-APP-18-REQ-12-6a41fcca1d4d0.pdf', '2026-06-29 05:04:10', 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `system_notifications`
--

CREATE TABLE `system_notifications` (
  `NotifID` int(11) NOT NULL,
  `RecipientID` int(11) NOT NULL,
  `Title` varchar(255) NOT NULL,
  `Message` text NOT NULL,
  `IsRead` tinyint(1) DEFAULT 0,
  `DateCreated` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_notifications`
--

INSERT INTO `system_notifications` (`NotifID`, `RecipientID`, `Title`, `Message`, `IsRead`, `DateCreated`) VALUES
(1, 3, 'MOA Deactivation Notice', 'Super Admin Notice: Your scholarship program \'asfasfas\' is flagged for deactivation based on the Memorandum of Agreement (MOA). Please review your terms or contact the Super Admin.', 1, '2026-06-29 22:01:19'),
(2, 3, 'MOA Deactivation Notice', 'Super Admin Notice: Your scholarship program \'asfasfas\' is flagged for deactivation based on the Memorandum of Agreement (MOA). Please review your terms or contact the Super Admin.', 1, '2026-06-29 22:05:16'),
(3, 3, 'MOA Deactivation Notice', 'System Notice: Your scholarship program \'asfasfas\' is flagged for deactivation based on the Memorandum of Agreement (MOA). Please review your terms or contact the University Admin.', 1, '2026-06-29 22:29:03'),
(4, 2, 'MOA Deactivation Notice', 'The scholarship program \'asfasfas\' was flagged for MOA Deactivation. The External Provider has been officially notified.', 1, '2026-06-29 22:29:03'),
(5, 3, 'MOA Deactivation Notice', 'System Notice: Your scholarship program \'Wildlife Conservation Stipend\' is flagged for deactivation based on the Memorandum of Agreement (MOA). Please review your terms or contact the University Admin.', 0, '2026-07-01 09:24:02'),
(6, 2, 'MOA Deactivation Notice', 'The scholarship program \'Wildlife Conservation Stipend\' was flagged for MOA Deactivation. The External Provider has been officially notified.', 0, '2026-07-01 09:24:02');

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `setting_key` varchar(50) NOT NULL,
  `setting_value` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`setting_key`, `setting_value`) VALUES
('require_2fa', '0'),
('session_timeout', '300'),
('strict_password', '1');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `UserID` int(11) NOT NULL,
  `Username` varchar(50) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `PasswordHash` varchar(255) NOT NULL,
  `FirstName` varchar(100) NOT NULL,
  `LastName` varchar(100) NOT NULL,
  `Role` enum('Student','Internal_Admin','External_Admin','Super_Admin') DEFAULT 'Student',
  `AccountStatus` varchar(20) DEFAULT 'Active',
  `StudentID_Num` varchar(50) DEFAULT NULL,
  `YearLevel` varchar(50) DEFAULT NULL,
  `GPA` decimal(4,2) DEFAULT NULL,
  `Major` varchar(100) DEFAULT NULL,
  `ProgramID` int(11) DEFAULT NULL,
  `Department` varchar(100) DEFAULT NULL,
  `Organization` varchar(100) DEFAULT NULL,
  `ContactNumber` varchar(20) DEFAULT NULL,
  `DateOfBirth` date DEFAULT NULL,
  `ProfilePicture` varchar(255) DEFAULT NULL,
  `ResetToken` varchar(255) DEFAULT NULL,
  `ResetTokenExpire` datetime DEFAULT NULL,
  `Gender` varchar(20) NOT NULL DEFAULT 'Not Specified'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`UserID`, `Username`, `Email`, `PasswordHash`, `FirstName`, `LastName`, `Role`, `AccountStatus`, `StudentID_Num`, `YearLevel`, `GPA`, `Major`, `ProgramID`, `Department`, `Organization`, `ContactNumber`, `DateOfBirth`, `ProfilePicture`, `ResetToken`, `ResetTokenExpire`, `Gender`) VALUES
(1, 'superadmin', 'admin@tau.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System', 'Admin', 'Super_Admin', 'Active', NULL, NULL, NULL, NULL, NULL, 'IT Department', NULL, NULL, NULL, NULL, NULL, NULL, 'Not Specified'),
(2, 'internal01', 'scholarships@tau.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Office of', 'Student Affairs', 'Internal_Admin', 'Active', NULL, NULL, NULL, NULL, NULL, 'OSA', NULL, NULL, NULL, NULL, NULL, NULL, 'Not Specified'),
(3, 'ched_eval', 'region3@ched.gov.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Regional', 'Evaluator', 'External_Admin', 'Active', NULL, NULL, NULL, NULL, NULL, NULL, 'CHED Region III', NULL, NULL, NULL, NULL, NULL, 'Not Specified'),
(4, '2023-0001', 'juan@tau.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Juan', 'Dela cruz', 'Student', 'Active', '2023-0001', NULL, 1.45, 'BS Information Technology (BSIT)', 18, NULL, NULL, '09201952345', '2004-02-20', '../uploads/profiles/USER_4_PROFILE_1774007130.png', NULL, NULL, 'Not Specified'),
(5, '2023-0002', 'maria@tau.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Maria', 'Clara', 'Student', 'Active', '2023-0002', NULL, 1.20, 'Agriculture', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Not Specified'),
(6, '2023100194', 'cjcanaria63@gmail.com', '$2y$10$wLXMYYkp.yKwA3vKg6QNqeR0..8wfdMlyq9ZraYGKFbF2ebuHLmxW', 'Cj', 'Canaria', 'Student', 'Active', '2023100194', '3rd Year', 1.45, 'BS Information Technology (BSIT)', 18, NULL, NULL, '09369522832', '2001-06-12', '../uploads/profiles/USER_6_PROFILE_1777004051.jpg', NULL, NULL, 'Male'),
(7, '2023100067', 'bbleb21@gmail.com', '$2y$10$E.Y5ibEodV06RUkZPeaj2e8j0ThJkyK..47RjvG0uso0mqxyUNhXm', 'Juan', 'Canaria', 'Student', 'Active', '2023100067', '2nd Year', 0.00, 'BS Tourism Management', 11, NULL, NULL, NULL, NULL, NULL, '9792e5c7e0a4c8b1fc15ef797aa34636b81cd221e3033ac39b22f9956940945f', '2026-05-19 21:04:47', 'Not Specified'),
(9, '2023100797', 'chescamaetablarin@gmail.com', '$2y$10$F8IYhfJPlxbYOwavnTRDa.fPfR5TDyc30R6rgHvpw57rYnuU3OWd6', 'Chesca Mae', 'Tablarin', 'Student', 'Active', '2023100797', '3rd Year', 0.00, 'BS Information Technology (BSIT)', 18, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Female'),
(11, '2023100068', 'jkcanaria123@gmail.com', '$2y$10$pGKywrMVkks058ChcQff/Or65qoV6de1j3yVwLrL7a443L791H8UW', 'John', 'Baloco', 'Student', 'Active', '2023100068', '2nd Year', 0.00, 'BS Geodetic Engineering', 17, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Male'),
(12, '2023100184', 'jluna@gmail.com', '$2y$10$TZZShYuhtR1TqRcXouk37umalrBC5oVZ45/O2/dC3y8cTjS6YStua', 'Juan', 'De Luna', 'Student', 'Active', '2023100184', '2nd Year', 0.00, 'AB Economics', 5, NULL, NULL, '09216081819', '2008-12-18', NULL, NULL, NULL, 'Male'),
(13, '2023100767', 'chescatablarinmangino@gmail.com', '$2y$10$pKnRPsVTD1BSVi06k.fbQufhNXxOHdLS6//f22Dla5lajv7ji91Xi', 'Chesca Mae', 'Tablarin', 'Student', 'Active', '2023100767', '1st Year', 0.00, 'BS Development Communication', 7, NULL, NULL, '09876754667', '2000-10-06', NULL, NULL, NULL, 'Female'),
(14, '2023100269', 'robinzalzos@gmail.com', '$2y$10$uC3HuBstewUEZcHLA5WPB.Qu3EY4w8cduffPCGquIBGU/YjAvEibS', 'CJ', 'Dela Cruz', 'Student', 'Active', '2023100269', '2nd Year', 0.00, 'BS Psychology', 6, NULL, NULL, '09152347886', '1999-09-05', NULL, NULL, NULL, 'Male'),
(15, '', 'chrisjunebagayansanidad@gmail.com', '$2y$10$UPaP0uVGqJgGChia74TeeuYjBvTiK3l35kJHpVkGIiIqQBtQCclPK', 'John', 'James', 'Student', 'Active', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Not Specified');

-- --------------------------------------------------------

--
-- Table structure for table `user_vault`
--

CREATE TABLE `user_vault` (
  `VaultID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `DocumentType` varchar(100) NOT NULL,
  `FilePath` varchar(255) NOT NULL,
  `UploadDate` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_vault`
--

INSERT INTO `user_vault` (`VaultID`, `UserID`, `DocumentType`, `FilePath`, `UploadDate`) VALUES
(2, 6, 'Certificate of Registration (COR)', '../uploads/vault/6/1774231846_Screenshot 2026-02-20 174228.png', '2026-03-23 10:10:46'),
(3, 6, 'Certificate of Indigency', '../uploads/vault/6/1779206783_downloaded_file.pdf', '2026-05-20 00:06:23');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `application`
--
ALTER TABLE `application`
  ADD PRIMARY KEY (`ApplicationID`),
  ADD KEY `ScholarshipID` (`ScholarshipID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `application_custom_answers`
--
ALTER TABLE `application_custom_answers`
  ADD PRIMARY KEY (`AnswerID`),
  ADD KEY `ApplicationID` (`ApplicationID`),
  ADD KEY `FieldID` (`FieldID`);

--
-- Indexes for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`AuditID`);

--
-- Indexes for table `criteria`
--
ALTER TABLE `criteria`
  ADD PRIMARY KEY (`CriteriaID`),
  ADD KEY `ScholarshipID` (`ScholarshipID`);

--
-- Indexes for table `document_requirement`
--
ALTER TABLE `document_requirement`
  ADD PRIMARY KEY (`RequirementID`),
  ADD KEY `ScholarshipID` (`ScholarshipID`);

--
-- Indexes for table `landing_content`
--
ALTER TABLE `landing_content`
  ADD PRIMARY KEY (`section_key`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`MessageID`),
  ADD KEY `SenderID` (`SenderID`),
  ADD KEY `ReceiverID` (`ReceiverID`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`NotificationID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `program`
--
ALTER TABLE `program`
  ADD PRIMARY KEY (`ProgramID`);

--
-- Indexes for table `scholarship`
--
ALTER TABLE `scholarship`
  ADD PRIMARY KEY (`ScholarshipID`),
  ADD KEY `ProgramID` (`ProgramID`),
  ADD KEY `fk_scholarship_creator` (`CreatedBy`);

--
-- Indexes for table `scholarship_criteria`
--
ALTER TABLE `scholarship_criteria`
  ADD PRIMARY KEY (`CriteriaID`),
  ADD KEY `ScholarshipID` (`ScholarshipID`);

--
-- Indexes for table `scholarship_custom_fields`
--
ALTER TABLE `scholarship_custom_fields`
  ADD PRIMARY KEY (`FieldID`),
  ADD KEY `ScholarshipID` (`ScholarshipID`);

--
-- Indexes for table `score`
--
ALTER TABLE `score`
  ADD PRIMARY KEY (`ScoreID`),
  ADD KEY `ApplicationID` (`ApplicationID`),
  ADD KEY `CriteriaID` (`CriteriaID`),
  ADD KEY `EvaluatorID` (`EvaluatorID`);

--
-- Indexes for table `submitted_document`
--
ALTER TABLE `submitted_document`
  ADD PRIMARY KEY (`SubmittedDocID`),
  ADD KEY `ApplicationID` (`ApplicationID`),
  ADD KEY `RequirementID` (`RequirementID`);

--
-- Indexes for table `system_notifications`
--
ALTER TABLE `system_notifications`
  ADD PRIMARY KEY (`NotifID`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`setting_key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `Username` (`Username`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Indexes for table `user_vault`
--
ALTER TABLE `user_vault`
  ADD PRIMARY KEY (`VaultID`),
  ADD KEY `UserID` (`UserID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `application`
--
ALTER TABLE `application`
  MODIFY `ApplicationID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `application_custom_answers`
--
ALTER TABLE `application_custom_answers`
  MODIFY `AnswerID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `AuditID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=147;

--
-- AUTO_INCREMENT for table `criteria`
--
ALTER TABLE `criteria`
  MODIFY `CriteriaID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `document_requirement`
--
ALTER TABLE `document_requirement`
  MODIFY `RequirementID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `MessageID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `NotificationID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `program`
--
ALTER TABLE `program`
  MODIFY `ProgramID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `scholarship`
--
ALTER TABLE `scholarship`
  MODIFY `ScholarshipID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=204;

--
-- AUTO_INCREMENT for table `scholarship_criteria`
--
ALTER TABLE `scholarship_criteria`
  MODIFY `CriteriaID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `scholarship_custom_fields`
--
ALTER TABLE `scholarship_custom_fields`
  MODIFY `FieldID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `score`
--
ALTER TABLE `score`
  MODIFY `ScoreID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `submitted_document`
--
ALTER TABLE `submitted_document`
  MODIFY `SubmittedDocID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `system_notifications`
--
ALTER TABLE `system_notifications`
  MODIFY `NotifID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `user_vault`
--
ALTER TABLE `user_vault`
  MODIFY `VaultID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `application`
--
ALTER TABLE `application`
  ADD CONSTRAINT `application_ibfk_1` FOREIGN KEY (`ScholarshipID`) REFERENCES `scholarship` (`ScholarshipID`) ON DELETE CASCADE,
  ADD CONSTRAINT `application_ibfk_2` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE;

--
-- Constraints for table `application_custom_answers`
--
ALTER TABLE `application_custom_answers`
  ADD CONSTRAINT `fk_aca_application` FOREIGN KEY (`ApplicationID`) REFERENCES `application` (`ApplicationID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_aca_field` FOREIGN KEY (`FieldID`) REFERENCES `scholarship_custom_fields` (`FieldID`) ON DELETE CASCADE;

--
-- Constraints for table `criteria`
--
ALTER TABLE `criteria`
  ADD CONSTRAINT `criteria_ibfk_1` FOREIGN KEY (`ScholarshipID`) REFERENCES `scholarship` (`ScholarshipID`) ON DELETE CASCADE;

--
-- Constraints for table `document_requirement`
--
ALTER TABLE `document_requirement`
  ADD CONSTRAINT `document_requirement_ibfk_1` FOREIGN KEY (`ScholarshipID`) REFERENCES `scholarship` (`ScholarshipID`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`SenderID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`ReceiverID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE;

--
-- Constraints for table `scholarship`
--
ALTER TABLE `scholarship`
  ADD CONSTRAINT `fk_scholarship_creator` FOREIGN KEY (`CreatedBy`) REFERENCES `users` (`UserID`) ON DELETE SET NULL,
  ADD CONSTRAINT `scholarship_ibfk_1` FOREIGN KEY (`ProgramID`) REFERENCES `program` (`ProgramID`) ON DELETE CASCADE;

--
-- Constraints for table `scholarship_criteria`
--
ALTER TABLE `scholarship_criteria`
  ADD CONSTRAINT `scholarship_criteria_ibfk_1` FOREIGN KEY (`ScholarshipID`) REFERENCES `scholarship` (`ScholarshipID`) ON DELETE CASCADE;

--
-- Constraints for table `scholarship_custom_fields`
--
ALTER TABLE `scholarship_custom_fields`
  ADD CONSTRAINT `fk_scf_scholarship` FOREIGN KEY (`ScholarshipID`) REFERENCES `scholarship` (`ScholarshipID`) ON DELETE CASCADE;

--
-- Constraints for table `score`
--
ALTER TABLE `score`
  ADD CONSTRAINT `score_ibfk_1` FOREIGN KEY (`ApplicationID`) REFERENCES `application` (`ApplicationID`) ON DELETE CASCADE,
  ADD CONSTRAINT `score_ibfk_2` FOREIGN KEY (`CriteriaID`) REFERENCES `criteria` (`CriteriaID`) ON DELETE CASCADE,
  ADD CONSTRAINT `score_ibfk_3` FOREIGN KEY (`EvaluatorID`) REFERENCES `users` (`UserID`) ON DELETE SET NULL;

--
-- Constraints for table `submitted_document`
--
ALTER TABLE `submitted_document`
  ADD CONSTRAINT `submitted_document_ibfk_1` FOREIGN KEY (`ApplicationID`) REFERENCES `application` (`ApplicationID`) ON DELETE CASCADE,
  ADD CONSTRAINT `submitted_document_ibfk_2` FOREIGN KEY (`RequirementID`) REFERENCES `document_requirement` (`RequirementID`) ON DELETE CASCADE;

--
-- Constraints for table `user_vault`
--
ALTER TABLE `user_vault`
  ADD CONSTRAINT `user_vault_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
