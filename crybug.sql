-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 28, 2025 at 08:44 PM
-- Server version: 9.3.0
-- PHP Version: 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `crybug`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_feedback`
--

CREATE TABLE `admin_feedback` (
  `a_id` int NOT NULL,
  `a_cmp_id` varchar(255) DEFAULT NULL,
  `a_message` text NOT NULL,
  `a_issue_type` varchar(255) NOT NULL,
  `a_is_resolved` int DEFAULT '0',
  `a_email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admin_feedback`
--

INSERT INTO `admin_feedback` (`a_id`, `a_cmp_id`, `a_message`, `a_issue_type`, `a_is_resolved`, `a_email`) VALUES
(1, 'CRYCOM57374340', 'no payment', 'Account Issue', 1, '12315479@neocolab.ai'),
(2, 'CRYCOM57374340', 'no payment', 'Account Issue', 0, '12315479@neocolab.ai'),
(3, 'CRYCOM88747905', 'this is report', 'Bug Report', 0, 'test2@gmail.com'),
(4, 'CRYCOM57374340', 'ghfdgg', 'Account Issue', 0, '12315479@neocolab.ai');

-- --------------------------------------------------------

--
-- Table structure for table `bug`
--

CREATE TABLE `bug` (
  `bug_id` varchar(255) NOT NULL,
  `bug_title` varchar(255) DEFAULT NULL,
  `bug_descp` varchar(255) DEFAULT NULL,
  `bug_created_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `bug_resolved_date` timestamp NULL DEFAULT NULL,
  `bug_status` varchar(100) DEFAULT NULL,
  `bug_progress` int NOT NULL,
  `bug_severity` varchar(100) DEFAULT NULL,
  `bug_assigned_to` varchar(255) DEFAULT NULL,
  `bug_reported_by` varchar(255) DEFAULT NULL,
  `bug_alloc_mag` varchar(255) DEFAULT NULL,
  `bug_alloc_cmp` varchar(255) DEFAULT NULL,
  `bug_alloc_emp` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `bug`
--

INSERT INTO `bug` (`bug_id`, `bug_title`, `bug_descp`, `bug_created_date`, `bug_resolved_date`, `bug_status`, `bug_progress`, `bug_severity`, `bug_assigned_to`, `bug_reported_by`, `bug_alloc_mag`, `bug_alloc_cmp`, `bug_alloc_emp`) VALUES
('BUG1745346556371', 'Login issue', 'fsadfsafsfsdf', '2025-04-22 12:59:16', NULL, 'Pending', 0, 'High', 'CRYEMP02387941', 'CRYMGR51725122', 'CRYMGR51725122', 'CRYCOM57374340', 'CRYEMP02387941'),
('BUG1745503326887', 'dgdg', 'dgdfgdfg', '2025-04-24 08:32:06', NULL, 'Pending', 0, 'Medium', 'CRYEMP02313457', 'CRYMGR51725122', 'CRYMGR51725122', 'CRYCOM57374340', 'CRYEMP02313457'),
('CRYBUG001', 'Login Issue', 'Login fails when using special characters in username', '2025-04-10 04:45:00', '2025-04-12 11:00:00', 'Resolved', 100, 'High', 'CRYEMP02067991', 'CRYEMP40516348', 'CRYMGR01741718', 'CRYCOM03173226', 'CRYEMP02067991'),
('CRYBUG002', 'UI Overlap', 'Buttons overlap on small screen devices', '2025-04-09 08:50:00', '2025-04-15 05:30:00', 'Resolved', 100, 'Medium', 'CRYEMP40516348', 'CRYEMP48254159', 'CRYMGR02032276', 'CRYCOM03999873', 'CRYEMP40516348'),
('CRYBUG003', 'Incorrect Budget Calculation', 'Project budget total is showing incorrect value on dashboard', '2025-04-11 02:30:00', NULL, 'Open', 19, 'Critical', 'CRYEMP36424176', 'CRYEMP02067991', 'CRYMGR40361339', 'CRYCOM10524371', 'CRYEMP36424176'),
('CRYBUG005', 'Missing Validation', 'No error shown for invalid email format', '2025-04-14 04:00:00', '2025-04-17 07:40:00', 'Resolved', 100, 'Medium', 'CRYEMP02067991', 'CRYEMP40516348', 'CRYMGR51725122', 'CRYCOM16342198', 'CRYEMP02067991'),
('CRYBUG006', 'Dashboard Crash', 'App crashes when loading heavy analytics', '2025-04-15 04:30:00', NULL, 'Open', 19, 'Critical', 'CRYEMP36424176', 'CRYEMP02067991', 'CRYMGR40361339', 'CRYCOM03173226', 'CRYEMP36424176'),
('CRYBUG007', 'Broken Links', 'Several links in the footer redirect to 404', '2025-04-15 07:00:00', NULL, 'Open', 27, 'Low', 'CRYEMP40516348', 'CRYEMP48254159', 'CRYMGR40361339', 'CRYCOM03173226', 'CRYEMP40516348'),
('CRYBUG008', 'Invalid Date Range', 'Reports accept future dates which break filters', '2025-04-16 03:45:00', '2025-04-18 08:30:00', 'Resolved', 100, 'Medium', 'CRYEMP02067991', 'CRYEMP40516348', 'CRYMGR03894576', 'CRYCOM10524371', 'CRYEMP02067991'),
('CRYBUG009', 'Slow Load Time', 'Project overview page takes 10+ seconds to load', '2025-04-16 05:00:00', NULL, 'In Progress', 95, 'High', 'CRYEMP48254159', 'CRYEMP02067991', 'CRYMGR03894576', 'CRYCOM10524371', 'CRYEMP48254159'),
('CRYBUG010', 'Permission Error', 'Users can access unauthorized sections', '2025-04-17 03:20:00', NULL, 'Open', 23, 'High', 'CRYEMP36424176', 'CRYEMP40516348', 'CRYMGR51725122', 'CRYCOM20822367', 'CRYEMP36424176'),
('CRYBUG011', 'Export Not Working', 'CSV export fails for large reports', '2025-04-17 05:15:00', NULL, 'Open', 76, 'Medium', 'CRYEMP02067991', 'CRYEMP48254159', 'CRYMGR51725122', 'CRYCOM20822367', 'CRYEMP02067991'),
('CRYBUG013', 'Notification Spam', 'System sends duplicate notifications on every action', '2025-04-18 05:15:00', NULL, 'In Progress', 86, 'Low', 'CRYEMP48254159', 'CRYEMP02067991', 'CRYMGR51725122', 'CRYCOM13614964', 'CRYEMP48254159'),
('CRYBUG014', 'Profile Image Upload Fails', 'PNG files cause error 500 on upload', '2025-04-19 06:00:00', NULL, 'Open', 15, 'High', 'CRYEMP36424176', 'CRYEMP40516348', 'CRYMGR02032276', 'CRYCOM05644496', 'CRYEMP36424176'),
('CRYBUG015', 'Pagination Broken', 'Next button does not fetch new rows in table', '2025-04-19 06:45:00', NULL, 'On Hold', 15, 'Medium', 'CRYEMP02067991', 'CRYEMP36424176', 'CRYMGR02032276', 'CRYCOM05644496', 'CRYEMP02067991'),
('CRYBUG016', 'Broken Attachment Links', 'Attachments download leads to broken file', '2025-04-20 02:30:00', NULL, 'Open', 40, 'Medium', 'CRYEMP40516348', 'CRYEMP48254159', 'CRYMGR01741718', 'CRYCOM26182673', 'CRYEMP40516348'),
('CRYBUG018', 'Dark Mode Flicker', 'Dark mode randomly switches to light mode on scroll', '2025-04-17 08:30:00', NULL, 'Open', 12, 'Low', 'CRYEMP40516348', 'CRYEMP02067991', 'CRYMGR50761502', 'CRYCOM24480213', 'CRYEMP40516348'),
('CRYBUG019', 'Data Overwrite Issue', 'Form submission overwrites unrelated database records', '2025-04-18 04:00:00', '2025-04-19 11:40:00', 'Resolved', 100, 'High', 'CRYEMP48254159', 'CRYEMP36424176', 'CRYMGR50761502', 'CRYCOM24480213', 'CRYEMP48254159'),
('CRYBUG020', 'Filter Dropdown Crash', 'Dropdown filters freeze the browser on apply', '2025-04-19 05:15:00', NULL, 'In Progress', 63, 'Medium', 'CRYEMP02067991', 'CRYEMP40516348', 'CRYMGR40361339', 'CRYCOM20822367', 'CRYEMP02067991'),
('CRYBUG021', 'Unresponsive Sidebar', 'Sidebar buttons don’t work on mobile view', '2025-04-20 05:30:00', NULL, 'Open', 39, 'Medium', 'CRYEMP36424176', 'CRYEMP48254159', 'CRYMGR40361339', 'CRYCOM20822367', 'CRYEMP36424176'),
('CRYBUG022', 'Broken Export CSV', 'Exporting CSV adds blank rows and corrupted header', '2025-04-18 03:00:00', NULL, 'Open', 29, 'High', 'CRYEMP40516348', 'CRYEMP36424176', 'CRYMGR51725122', 'CRYCOM13742340', 'CRYEMP40516348'),
('CRYBUG023', 'Auto Logout Failure', 'Users remain logged in even after session timeout', '2025-04-19 07:30:00', NULL, 'Open', 29, 'Critical', 'CRYEMP02067991', 'CRYEMP48254159', 'CRYMGR51725122', 'CRYCOM13742340', 'CRYEMP02067991');

-- --------------------------------------------------------

--
-- Table structure for table `company`
--

CREATE TABLE `company` (
  `cmp_id` varchar(14) NOT NULL,
  `cmp_name` varchar(255) NOT NULL,
  `cmp_descp` varchar(500) NOT NULL,
  `cmp_pincode` varchar(10) NOT NULL,
  `cmp_mail` varchar(255) NOT NULL,
  `cmp_phone` varchar(13) NOT NULL,
  `cmp_password` varchar(255) NOT NULL,
  `cmp_logo` varchar(1000) NOT NULL,
  `cmp_address` varchar(600) NOT NULL,
  `cmp_clients` int DEFAULT NULL,
  `github` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT '#',
  `linkedin` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT '#',
  `x` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT '#'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `company`
--

INSERT INTO `company` (`cmp_id`, `cmp_name`, `cmp_descp`, `cmp_pincode`, `cmp_mail`, `cmp_phone`, `cmp_password`, `cmp_logo`, `cmp_address`, `cmp_clients`, `github`, `linkedin`, `x`) VALUES
('CRYCOM02694494', 'dsad', 'This is a default company description. this is cool and bla bla', '123456', '12315479@neocolab.ai', '1234567890', 'dSAD', '../uploads/fol/profile-circle-svgrepo-com.png', 'fdsdsf', 76, NULL, NULL, NULL),
('CRYCOM03101881', 'fsdf', 'This is a default company description. this is cool and bla bla', '123456', 'hack.3656@gmail.com', '1234567892', '123456', '../uploads/fol/10.jpg', 'dsadsa', 7, NULL, NULL, NULL),
('CRYCOM03173226', 'dsad', 'This is a default company description. this is cool and bla bla', '123456', '12315479@neocolab.ai', '1234567890', '123456', '../uploads/fol/spider-svgrepo-com.png', 'dsaddasd', 5, NULL, NULL, NULL),
('CRYCOM03999873', 'dsa', 'This is a default company description. this is cool and bla bla', '', '12315479@neocolab.ai', '1234567890', 'dsad', '', 'dsadas', 4, NULL, NULL, NULL),
('CRYCOM05644496', 'dsad', 'This is a default company description. this is cool and bla bla', '123456', '12315479@neocolab.ai', '1234567890', 'dSAD', '../uploads/fol/profile-circle-svgrepo-com.png', 'fdsdsf', 4, NULL, NULL, NULL),
('CRYCOM07726462', 'dsada', 'This is a default company description. this is cool and bla bla', '123456', 'hacker.3656@gmail.com', '1234567892', '123456', '../uploads/fol/1350159.jpeg', 'dcsadada', 9, NULL, NULL, NULL),
('CRYCOM08642589', 'fsdf', 'This is a default company description. this is cool and bla bla', '123456', 'hack.3656@gmail.com', '1234567892', '123456', '', 'gxcdsxf', 31, NULL, NULL, NULL),
('CRYCOM10524371', 'Deku', 'This is a default company description. this is cool and bla bla', '123456', 'fsfsd@gmail.com', '9862820693', 'allmight', '../uploads/company_images/kiki.jpg', 'dadadadsdad', 27, NULL, NULL, NULL),
('CRYCOM13586885', 'dsad', 'This is a default company description. this is cool and bla bla', '123456', '12315479@neocolab.ai', '1234567890', '123456', '../uploads/fol/Adobe Express - file (1).png', 'dsaddasd', 44, NULL, NULL, NULL),
('CRYCOM13614964', 'fsdf', 'This is a default company description. this is cool and bla bla', '123456', 'hack.3656@gmail.com', '1234567892', '123456', '../uploads/fol/7.jpg', 'ffds', 37, NULL, NULL, NULL),
('CRYCOM13742340', 'fsdf', 'This is a default company description. this is cool and bla bla', '123456', 'hack.3656@gmail.com', '1234567892', '123456', '', 'dasdadsad', 52, NULL, NULL, NULL),
('CRYCOM16342198', 'fsdf', 'This is a default company description. this is cool and bla bla', '123456', 'hack.3656@gmail.com', '1234567892', '123456', '../uploads/fol/1.jpg', 'dasdadsad', 50, NULL, NULL, NULL),
('CRYCOM20822367', 'fsdf', 'This is a default company description. this is cool and bla bla', '123456', 'hack.3656@gmail.com', '1234567892', '123456', '', 'dsdad', 95, NULL, NULL, NULL),
('CRYCOM21541055', 'fsdf', 'This is a default company description. this is cool and bla bla', '123456', 'hack.3656@gmail.com', '1234567892', '123456', '../uploads/company_images/7.jpg', 'ffds', 22, NULL, NULL, NULL),
('CRYCOM24480213', 'dsa', 'This is a default company description. this is cool and bla bla', '', '12315479@neocolab.ai', '1234567890', 'dsad', '', 'dsadas', 25, NULL, NULL, NULL),
('CRYCOM25553391', 'fsdf', 'This is a default company description. this is cool and bla bla', '123456', 'hack.3656@gmail.com', '1234567892', '123456', '../uploads/fol/7.jpg', 'ffds', 58, NULL, NULL, NULL),
('CRYCOM25752851', 'fsadfa', 'This is a default company description. this is cool and bla bla', '123456', '12315479@neocolab.ai', '1234567890', '123456', '../uploads/fol/L death note.jpg', 'sdad', 17, NULL, NULL, NULL),
('CRYCOM25891592', 'fsdf', 'This is a default company description. this is cool and bla bla', '123456', 'hack.3656@gmail.com', '1234567892', '123456', '../uploads/fol/10.jpg', 'dsadsa', 7, NULL, NULL, NULL),
('CRYCOM26182673', 'fsdf', 'This is a default company description. this is cool and bla bla', '123456', 'hack.3656@gmail.com', '1234567892', '123456', '', 'gxcdsxf', 86, NULL, NULL, NULL),
('CRYCOM26489967', 'tester', 'This is working and ok', '123456', 'test@gmaI.com', '4323432423', '123456', '../uploads/company_images/batman-5-logo-svgrepo-com.png', 'Street:  B10,raja Garden, B10,raja Garden, Model Colony,pune\r\n\r\nCity:   Pune\r\n\r\nState/province/area:    Maharashtra\r\n\r\nPhone number:  25679967\r\n\r\nZip code:  411016\r\n\r\nCountry calling code:  +91\r\n\r\nCountry:  India', 12, '#', '#', '#'),
('CRYCOM26643552', 'fsdf', 'This is a default company description. this is cool and bla bla', '123456', 'hack.3656@gmail.com', '1234567892', '123456', '../uploads/fol/7.jpg', 'ffds', 6, NULL, NULL, NULL),
('CRYCOM29432163', 'dsada', 'This is a default company description. this is cool and bla bla', '123456', 'hacker.3656@gmail.com', '1234567892', '123456', '../uploads/fol/1350159.jpeg', 'dsadads', 72, NULL, NULL, NULL),
('CRYCOM41412570', 'fsdf', 'This is a default company description. this is cool and bla bla', '123456', 'hack.3656@gmail.com', '1234567892', '123456', '', 'dsad', 43, NULL, NULL, NULL),
('CRYCOM41703302', 'fsdf', 'This is a default company description. this is cool and bla bla', '123456', 'hack.3656@gmail.com', '1234567892', '123456', '', 'dasdad', 96, NULL, NULL, NULL),
('CRYCOM46689306', 'dsa', 'This is a default company description. this is cool and bla bla', '123456', '', '1234567890', '123456', '', 'dsa', 52, NULL, NULL, NULL),
('CRYCOM47755100', 'fsdf', 'This is a default company description. this is cool and bla bla', '123456', 'hack.3656@gmail.com', '1234567892', '123456', '../uploads/fol/7.jpg', 'ffds', 70, NULL, NULL, NULL),
('CRYCOM50194405', 'dsad', 'This is a default company description. this is cool and bla bla', '123456', '12315479@neocolab.ai', '1234567890', 'dSAD', '../uploads/fol/profile-circle-svgrepo-com.png', 'fdsdsf', 94, NULL, NULL, NULL),
('CRYCOM51381642', 'fsdf', 'This is a default company description. this is cool and bla bla', '123456', 'hack.3656@gmail.com', '1234567892', '123456', '../uploads/fol/10.jpg', 'dsadsa', 61, NULL, NULL, NULL),
('CRYCOM51932770', 'fsdf', 'This is a default company description. this is cool and bla bla', '123456', 'hack.3656@gmail.com', '1234567892', '123456', '', 'ffds', 22, NULL, NULL, NULL),
('CRYCOM55189281', 'dsa', 'This is a default company description. this is cool and bla bla', '123456', '', '1234567890', '123456', '', 'dsa', 27, NULL, NULL, NULL),
('CRYCOM55395100', 'dsada', 'This is a default company description. this is cool and bla bla', '123456', 'hacker.3656@gmail.com', '1234567892', '123456', '../uploads/fol/10.jpg', 'dadads', 69, NULL, NULL, NULL),
('CRYCOM55612443', 'fsdf', 'This is a default company description. this is cool and bla bla', '123456', 'hack.3656@gmail.com', '1234567892', '123456', '', 'dsad', 62, NULL, NULL, NULL),
('CRYCOM57374340', 'Aniplix', 'This is a default company description. this is cool and bla bla', '123456', '12315479@neocolab.ai', '1234567890', '123123', '../uploads/company_images/slack-1-logo-svgrepo-com.png', 'this is a random address this is working this is also working', 5, 'https://internet.lpu.in/24online/servlet/E24onlineHTTPClient', 'https://internet.lpu.in/24online/servlet/E24onlineHTTPClient', 'https://internet.lpu.in/24online/servlet/E24onlineHTTPClient'),
('CRYCOM63682844', 'fsdf', 'This is a default company description. this is cool and bla bla', '123456', 'hack.3656@gmail.com', '1234567892', '123456', '', 'ffds', 37, NULL, NULL, NULL),
('CRYCOM66734426', 'dsad', 'This is a default company description. this is cool and bla bla', '123456', '12315479@neocolab.ai', '1234567890', 'dSAD', '../uploads/fol/profile-circle-svgrepo-com.png', 'fdsdsf', 69, NULL, NULL, NULL),
('CRYCOM69152269', 'test', 'this is ius dsljasl;djadjlj', '123456', 'hack.3656@gmail.com', '1234567892', '123456', '../uploads/company_images/a-high-quality-transparent-png-illustration-of-a-f.png', 'rhkjhdakjshdkjah dkj kd kajd', 56, NULL, NULL, NULL),
('CRYCOM69245740', 'dsad', 'This is a default company description. this is cool and bla bla', '123456', '12315479@neocolab.ai', '1234567890', '123456', '../uploads/fol/profile-circle-svgrepo-com.png', 'dadsasd', 33, NULL, NULL, NULL),
('CRYCOM69781054', 'fsdf', 'This is a default company description. this is cool and bla bla', '123456', 'hack.3656@gmail.com', '1234567892', '123456', '../uploads/fol/𝘼𝙧𝙞𝙢𝙖 𝙆𝙤𝙪𝙨𝙚𝙞 𝙞𝙘𝙤𝙣.jpg', 'zXCsczdszczcx', 59, NULL, NULL, NULL),
('CRYCOM70564157', 'lic', 'This is a default company description. this is cool and bla bla', '797112', '12315479@neocolab.ai', '1234567890', '147852', '../uploads/company_images/L death note.jpg', 'dimapur', 95, NULL, NULL, NULL),
('CRYCOM70635815', 'dsad', 'This is a default company description. this is cool and bla bla', '123456', '12315479@neocolab.ai', '1234567890', '123456', '../uploads/fol/profile-circle-svgrepo-com.png', 'dadsasd', 97, NULL, NULL, NULL),
('CRYCOM74634288', 'dsad', 'This is a default company description. this is cool and bla bla', '123456', '12315479@neocolab.ai', '1234567890', '123456', '../uploads/fol/profile-circle-svgrepo-com (2).png', 'fdsdsf', 98, NULL, NULL, NULL),
('CRYCOM74644465', 'dsad', 'This is a default company description. this is cool and bla bla', '123456', '12315479@neocolab.ai', '1234567890', 'dSAD', '../uploads/fol/profile-circle-svgrepo-com.png', 'fdsdsf', 1, NULL, NULL, NULL),
('CRYCOM75766041', 'fsdf', 'This is a default company description. this is cool and bla bla', '123456', 'hacker.3656@gmail.com', '1234567892', '147852', '../uploads/fol/hero-image.png', 'dadsa', 11, NULL, NULL, NULL),
('CRYCOM77882170', '', 'This is a default company description. this is cool and bla bla', '', '', '', '', '', '', 50, NULL, NULL, NULL),
('CRYCOM77884419', 'dsad', 'This is a default company description. this is cool and bla bla', '123456', '12315479@neocolab.ai', '1234567890', 'dSAD', '../uploads/fol/profile-circle-svgrepo-com.png', 'fdsdsf', 16, NULL, NULL, NULL),
('CRYCOM79394435', 'Xyz', 'this is a random company description and we are working over it', '123456', 'deku@gmail.ocom', '2342342342', '123456', '../uploads/company_images/logo.jpg', 'any random address', 23, NULL, NULL, NULL),
('CRYCOM79856338', 'fsadfa', 'This is a default company description. this is cool and bla bla', '123456', '12315479@neocolab.ai', '1234567890', '145632', '../uploads/fol/pngwing.com (7).png', 'dsadad', 29, NULL, NULL, NULL),
('CRYCOM84074462', 'dsad', 'This is a default company description. this is cool and bla bla', '123456', '12315479@neocolab.ai', '1234567890', 'dSAD', '../uploads/fol/profile-circle-svgrepo-com.png', 'fdsdsf', 99, NULL, NULL, NULL),
('CRYCOM86054356', 'dsad', 'This is a default company description. this is cool and bla bla', '123456', '12315479@neocolab.ai', '1234567890', 'dSAD', '../uploads/fol/profile-circle-svgrepo-com.png', 'fdsdsf', 7, NULL, NULL, NULL),
('CRYCOM88747905', 'test2', 'this is the description', '123456', 'test2@gmail.com', '7894561531', '123456', '../uploads/company_images/a-detailed-mechanical-spider-representing-a-futuri.png', 'this is the address i want you to see', 89, '#', '#', '#'),
('CRYCOM93115810', 'dsad', 'This is a default company description. this is cool and bla bla', '123456', '12315479@neocolab.ai', '1234567890', '123456', '../uploads/fol/profile-circle-svgrepo-com.png', 'dadsasd', 38, NULL, NULL, NULL),
('CRYCOM95329746', 'dsad', 'This is a default company description. this is cool and bla bla', '123456', '12315479@neocolab.ai', '1234567890', '123456', '../uploads/company_images/pngwing.com (8).png', 'sfsdfsfsdf', 68, NULL, NULL, NULL),
('CRYCOM98304970', 'fsdf', 'This is a default company description. this is cool and bla bla', '123456', 'hack.3656@gmail.com', '1234567892', '123456', '../uploads/fol/7.jpg', 'ffds', 27, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `employee`
--

CREATE TABLE `employee` (
  `emp_id` varchar(255) NOT NULL,
  `emp_name` varchar(255) NOT NULL,
  `emp_mail` varchar(255) NOT NULL,
  `emp_phone` varchar(15) NOT NULL,
  `emp_password` varchar(255) NOT NULL,
  `emp_gender` varchar(255) NOT NULL,
  `emp_profile` varchar(1000) NOT NULL,
  `emp_role` varchar(255) NOT NULL,
  `emp_dept` varchar(366) NOT NULL,
  `emp_exp` varchar(123) NOT NULL,
  `webD` int NOT NULL,
  `auto` int NOT NULL,
  `design` int NOT NULL,
  `verbal` int NOT NULL,
  `mag_id` varchar(255) DEFAULT NULL,
  `github` varchar(255) DEFAULT NULL,
  `linkedin` varchar(255) DEFAULT NULL,
  `x` varchar(255) DEFAULT NULL,
  `onLeave` int NOT NULL DEFAULT '0',
  `emp_join_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `salary` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `employee`
--

INSERT INTO `employee` (`emp_id`, `emp_name`, `emp_mail`, `emp_phone`, `emp_password`, `emp_gender`, `emp_profile`, `emp_role`, `emp_dept`, `emp_exp`, `webD`, `auto`, `design`, `verbal`, `mag_id`, `github`, `linkedin`, `x`, `onLeave`, `emp_join_date`, `salary`) VALUES
('CRYEMP02067991', 'Jiraiya', 'magdxd234@gmail.com', '8956237410', '123456', 'Female', '../uploads/employee_images/CRYEMP02067991_1745403124.jpg', 'Developer', 'Software development', '8', 63, 34, 59, 40, 'CRYMGR51725122', 'https://www.flaticon.com/icon-fonts-most-downloaded', 'https://www.flaticon.com/icon-fonts-most-downloaded', 'https://www.flaticon.com/icon-fonts-most-downloaded', 0, '2024-12-06 00:00:00', 52064),
('CRYEMP02113452', 'Himiko Toga', 'user_CRYEMP02113452_499178@gmail.com', '8923456712', 'pass123', 'Male', '../uploads/employee_images/1.jpg', 'developer', 'engineering', '5', 88, 22, 45, 67, 'CRYMGR51725122', 'https://github.com/aarav-dev', 'https://www.linkedin.com/in/aarav-dev/', 'https://twitter.com/aarav_dev', 0, '2023-01-28 00:00:00', 66751),
('CRYEMP02189478', 'Mei Hatsume', 'user_CRYEMP02189478_878267@gmail.com', '9001234567', 'meera789', 'Female', '../uploads/employee_images/3.jpg', 'designer', 'creative', '6', 54, 44, 90, 50, 'CRYMGR51725122', 'https://github.com/meera-designs', 'https://www.linkedin.com/in/meera-creative/', 'https://twitter.com/meera_uiux', 0, '2021-03-14 00:00:00', 41654),
('CRYEMP02231885', 'Gojo', 'user_CRYEMP02231885_993802@gmail.com', '8976543210', 'jaypass', 'Male', '../uploads/employee_images/4.jpg', 'tester', 'qa', '4', 39, 48, 32, 71, 'CRYMGR51725122', 'https://github.com/jay-tester', 'https://www.linkedin.com/in/jay-qa/', 'https://twitter.com/jay_testing', 0, '2022-04-25 00:00:00', 60670),
('CRYEMP02298213', 'Rika', 'user_CRYEMP02298213_434207@gmail.com', '9123456789', 'neha@321', 'Female', '../uploads/employee_images/5.jpg', 'frontend', 'web', '3', 92, 25, 77, 36, 'CRYMGR51725122', 'https://github.com/neha-ui', 'https://www.linkedin.com/in/neha-ui/', 'https://twitter.com/neha_frontend', 0, '2020-05-18 00:00:00', 47854),
('CRYEMP02313457', 'Eri', 'user_CRYEMP02313457_889632@gmail.com', '8765432190', 'ravi!321', 'Male', '../uploads/employee_images/6.jpg', 'backend', 'server', '7', 68, 41, 34, 58, 'CRYMGR51725122', 'https://github.com/ravi-api', 'https://www.linkedin.com/in/ravi-server/', 'https://twitter.com/ravi_backend', 0, '2023-07-09 00:00:00', 33198),
('CRYEMP02387941', 'Shoto Todoroki', 'user_CRYEMP02387941_345540@gmail.com', '9098765432', 'tanvi#456', 'Female', '../uploads/employee_images/7.jpg', 'project lead', 'management', '9', 77, 33, 45, 84, 'CRYMGR51725122', 'https://github.com/tanvi-lead', 'https://www.linkedin.com/in/tanvi-leader/', 'https://twitter.com/tanvi_mgr', 0, '2020-08-26 00:00:00', 20084),
('CRYEMP02415478', 'Izuku Midoriya', 'user_CRYEMP02415478_758806@gmail.com', '8945612345', 'rohan987', 'Male', '../uploads/employee_images/8.jpg', 'analyst', 'data', '2', 35, 29, 25, 70, 'CRYMGR51725122', 'https://github.com/rohan-analytics', 'https://www.linkedin.com/in/rohan-data/', 'https://twitter.com/rohan_data', 0, '2022-11-21 00:00:00', 52815),
('CRYEMP02464532', 'Kousei Arima', 'user_CRYEMP02464532_857408@gmail.com', '9823456712', 'divya456', 'Female', '../uploads/employee_images/𝘼𝙧𝙞𝙢𝙖 𝙆𝙤𝙪𝙨𝙚𝙞 𝙞𝙘𝙤𝙣.jpg', 'HR', 'people', '6', 48, 31, 50, 88, 'CRYMGR51725122', 'https://github.com/divya-hr', 'https://www.linkedin.com/in/divya-hr/', 'https://twitter.com/divya_people', 0, '2021-03-20 00:00:00', 42734),
('CRYEMP17401869', 'tester', 'testerr@gnail.ocs', '1234567892', '123456', 'Female', '../uploads/employee_images/7jfy.gif', 'testing', 'something', '5', 70, 30, 72, 39, 'CRYMGR40361339', NULL, NULL, NULL, 0, '2025-04-26 18:24:42', 0),
('CRYEMP36424176', 'someone', 'user_CRYEMP36424176_110624@gmail.com', '1234567892', '123123', 'Male', '../uploads/employee_images/4.jpg', 'kuch nahi', 'bca', '1', 63, 64, 76, 82, 'CRYMGR40361339', '', '', '', 0, '2024-04-16 00:00:00', 56617),
('CRYEMP39067287', 'goan', 'user_CRYEMP39067287_580894@gmail.com', '1234567892', '123123', 'Male', '../uploads/employee_images/pngwing.com (4).png', 'developer', 'backedn', '13', 90, 90, 50, 23, NULL, NULL, NULL, NULL, 0, '2022-10-24 00:00:00', 61117),
('CRYEMP40516348', 'fggf', 'user_CRYEMP40516348_672602@gmail.com', '1234567890', '123456', 'Male', '../uploads/employee_images/pngwing.com.png', 'ewqewa', 'dAFQD', '12', 55, 85, 79, 83, 'CRYMGR02032276', '', '', '', 0, '2021-12-22 00:00:00', 21681),
('CRYEMP45969916', 'Minato', 'user_CRYEMP45969916_620327@gmail.com', '123456789', '$2y$10$1il8fvXxrDenPqmt/.Lt2.4caDmnBsRAbQMndzA6nWcAea2wN1JfK', 'Male', '../uploads/employee_images/⚡️𝗠𝗶𝗻𝗮𝘁𝗼 𝗡𝗮𝗺𝗶𝗸𝗮𝘇𝗲⚡️.jpg', 'Backend Dev', 'Testing', '8', 4, 6, 9, 8, 'CRYMGR51725122', NULL, NULL, NULL, 0, '2025-04-24 00:00:00', 123654),
('CRYEMP48254159', 'dfasd', 'user_CRYEMP48254159_983831@gmail.com', '1234567890', '123123', 'Male', '../uploads/employee_images/𝘼𝙧𝙞𝙢𝙖 𝙆𝙤𝙪𝙨𝙚𝙞 𝙞𝙘𝙤𝙣.jpg', 'ddas', 'asasdsad', '13', 89, 83, 76, 91, NULL, '', '', '', 0, '2024-05-18 00:00:00', 31896),
('CRYEMP49812046', 'test', 'test@gmail.com', '1234567892', '123456', 'Female', '../uploads/employee_images/7jfy.gif', 'tester', 'something', '5', 62, 92, 86, 3, NULL, NULL, NULL, NULL, 0, '2025-04-26 18:11:12', 0),
('CRYEMP71564785', 'dasdad', 'dasdadadaddasdsadadada@gmail.com', '3213123123', '147852', 'Female', '../uploads/employee_images/Naruto Uzumaki Insigna.jpg', 'dasdasd', 'something', '5', 50, 50, 50, 50, 'CRYMGR86854406', NULL, NULL, NULL, 0, '2025-04-26 19:05:24', 0),
('CRYEMP77769574', 'Minto ', 'user_CRYEMP77769574_258172@gmail.com', '1234567892', '123456', 'Male', '../uploads/employee_images/Minato.jpg', 'Developer', 'Software development', '12', 82, 85, 78, 7, NULL, NULL, NULL, NULL, 0, '2022-08-28 00:00:00', 30903),
('CRYEMP81566861', 'spider man', 'user_CRYEMP81566861_939367@gmail.com', '1234567890', '123123', 'Male', '../uploads/employee_images/pat2-transparency.png', 'marksman', 'fdsfds', '56', 71, 26, 74, 39, NULL, '', '', '', 0, '2023-03-25 00:00:00', 20103),
('CRYEMP93658860', 'test Final', 'testerrrrr@gmaul.com', '3213213121', '123123', 'Male', '../uploads/employee_images/7jfy.gif', 'tester pro', 'gdgs', '8', 83, 29, 88, 87, 'CRYMGR40361339', NULL, NULL, NULL, 0, '2025-04-26 18:38:49', 0);

--
-- Triggers `employee`
--
DELIMITER $$
CREATE TRIGGER `set_join_date` BEFORE INSERT ON `employee` FOR EACH ROW BEGIN
    IF NEW.emp_join_date IS NULL THEN
        SET NEW.emp_join_date = CURDATE();
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `employee_otp`
--

CREATE TABLE `employee_otp` (
  `emp_mail` varchar(255) NOT NULL,
  `otp` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `employee_otp`
--

INSERT INTO `employee_otp` (`emp_mail`, `otp`) VALUES
('magdxd234@gmail.com', 123456),
('user_CRYEMP02113452_499178@gmail.com', 0),
('user_CRYEMP02189478_878267@gmail.com', 0),
('user_CRYEMP02231885_993802@gmail.com', 0),
('user_CRYEMP02298213_434207@gmail.com', 0),
('user_CRYEMP02313457_889632@gmail.com', 0),
('user_CRYEMP02387941_345540@gmail.com', 0),
('user_CRYEMP02415478_758806@gmail.com', 0),
('user_CRYEMP02464532_857408@gmail.com', 0),
('user_CRYEMP36424176_110624@gmail.com', 0),
('user_CRYEMP39067287_580894@gmail.com', 0),
('user_CRYEMP40516348_672602@gmail.com', 0),
('user_CRYEMP45969916_620327@gmail.com', 0),
('user_CRYEMP48254159_983831@gmail.com', 0),
('user_CRYEMP77769574_258172@gmail.com', 0),
('user_CRYEMP81566861_939367@gmail.com', 0);

-- --------------------------------------------------------

--
-- Table structure for table `emp_feedback`
--

CREATE TABLE `emp_feedback` (
  `EF_id` int NOT NULL,
  `EF_emp_id` varchar(255) DEFAULT NULL,
  `EF_type` varchar(255) DEFAULT NULL,
  `EF_issue` varchar(1000) DEFAULT NULL,
  `EF_priority` varchar(20) DEFAULT NULL,
  `EF_remark` varchar(1000) DEFAULT NULL,
  `EF_resolved` int DEFAULT '0',
  `EF_mag_id` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `emp_feedback`
--

INSERT INTO `emp_feedback` (`EF_id`, `EF_emp_id`, `EF_type`, `EF_issue`, `EF_priority`, `EF_remark`, `EF_resolved`, `EF_mag_id`) VALUES
(1, 'CRYEMP02067991', 'Complaint', 'this is it', 'High', 'this is now done', 1, 'CRYMGR51725122'),
(2, 'CRYEMP02067991', 'Query', 'dasdads', 'Low', 'wqewe', 1, 'CRYMGR51725122'),
(3, 'CRYEMP02067991', 'Appreciation', 'asdsad', 'Critical', NULL, 0, 'CRYMGR51725122'),
(4, 'CRYEMP02067991', 'Suggestion', 'dasdsad', 'High', 'dnone', 1, 'CRYMGR51725122'),
(5, 'CRYEMP02067991', 'Suggestion', 'ddasda', 'Medium', 'this is done buddy', 1, 'CRYMGR51725122'),
(7, 'CRYEMP02067991', 'Query', 't drgdgd', 'Medium', 'now it is done my boy hehe', 1, 'CRYMGR51725122'),
(8, 'CRYEMP02067991', 'Query', 'headache', 'Critical', 'bhar mai ja', 1, 'CRYMGR51725122');

-- --------------------------------------------------------

--
-- Table structure for table `holiday`
--

CREATE TABLE `holiday` (
  `holiday_id` int NOT NULL,
  `holiday_name` varchar(100) NOT NULL,
  `holiday_date` date NOT NULL,
  `holiday_total_days` int DEFAULT '1',
  `holiday_cmp_id` varchar(255) DEFAULT NULL,
  `holiday_day_type` enum('Full','Half') DEFAULT 'Full'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `holiday`
--

INSERT INTO `holiday` (`holiday_id`, `holiday_name`, `holiday_date`, `holiday_total_days`, `holiday_cmp_id`, `holiday_day_type`) VALUES
(1, 'New Year\'s Day', '2025-01-01', 1, NULL, 'Full'),
(2, 'Republic Day', '2025-01-26', 1, NULL, 'Full'),
(3, 'Holi', '2025-03-14', 1, NULL, 'Half'),
(4, 'Good Friday', '2025-04-18', 1, NULL, 'Full'),
(5, 'Independence Day', '2025-08-15', 1, NULL, 'Full'),
(6, 'Diwali', '2025-10-20', 2, NULL, 'Full'),
(7, 'Christmas Eve', '2025-12-24', 1, NULL, 'Half'),
(8, 'Christmas Day', '2025-12-25', 1, NULL, 'Full'),
(9, 'Summer Break', '2025-05-20', 10, 'CRYCOM57374340', 'Full'),
(10, 'Winter Break', '2025-12-26', 7, 'CRYCOM57374340', 'Full'),
(12, 'time pass', '2025-04-27', 1, 'CRYCOM88747905', 'Half'),
(13, 'time pass', '2025-04-28', 1, 'CRYCOM57374340', 'Full'),
(14, 'time pass', '2025-04-30', 1, 'CRYCOM57374340', 'Full');

-- --------------------------------------------------------

--
-- Table structure for table `leaveapp`
--

CREATE TABLE `leaveapp` (
  `leave_id` varchar(255) NOT NULL,
  `leave_type` varchar(255) DEFAULT NULL,
  `leave_duration` varchar(255) DEFAULT NULL,
  `leave_from` date DEFAULT NULL,
  `leave_to` date DEFAULT NULL,
  `leave_reason` varchar(500) DEFAULT NULL,
  `leave_handover_id` varchar(255) DEFAULT NULL,
  `leave_total_leave` int DEFAULT '30',
  `leave_remaining_leave` int DEFAULT '30',
  `leave_used` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `leaveapp`
--

INSERT INTO `leaveapp` (`leave_id`, `leave_type`, `leave_duration`, `leave_from`, `leave_to`, `leave_reason`, `leave_handover_id`, `leave_total_leave`, `leave_remaining_leave`, `leave_used`) VALUES
('CRYEMP02067991', 'casual', 'full', '2025-04-29', '2025-04-30', 'tjiohisoa', 'CRYEMP02113452', 30, 19, 9),
('CRYEMP02113452', NULL, NULL, NULL, NULL, NULL, NULL, 30, 30, 0),
('CRYEMP02189478', NULL, NULL, NULL, NULL, NULL, NULL, 30, 30, 0),
('CRYEMP02231885', NULL, NULL, NULL, NULL, NULL, NULL, 30, 30, 0),
('CRYEMP02298213', NULL, NULL, NULL, NULL, NULL, NULL, 30, 30, 0),
('CRYEMP02313457', NULL, NULL, NULL, NULL, NULL, NULL, 30, 30, 0),
('CRYEMP02387941', NULL, NULL, NULL, NULL, NULL, NULL, 30, 30, 0),
('CRYEMP02415478', NULL, NULL, NULL, NULL, NULL, NULL, 30, 30, 0),
('CRYEMP02464532', NULL, NULL, NULL, NULL, NULL, NULL, 30, 30, 0),
('CRYEMP36424176', NULL, NULL, NULL, NULL, NULL, NULL, 30, 30, 0),
('CRYEMP39067287', NULL, NULL, NULL, NULL, NULL, NULL, 30, 30, 0),
('CRYEMP40516348', NULL, NULL, NULL, NULL, NULL, NULL, 30, 30, 0),
('CRYEMP45969916', NULL, NULL, NULL, NULL, NULL, NULL, 30, 30, 0),
('CRYEMP48254159', NULL, NULL, NULL, NULL, NULL, NULL, 30, 30, 0),
('CRYEMP71564785', NULL, NULL, NULL, NULL, NULL, NULL, 30, 30, 0),
('CRYEMP81566861', NULL, NULL, NULL, NULL, NULL, NULL, 30, 30, 0),
('CRYEMP93658860', 'sick', 'full', '2025-04-27', '2025-04-28', 'sfsfsdffdfd', 'CRYEMP36424176', 30, 26, 2),
('CRYMAG11800034', NULL, NULL, NULL, NULL, NULL, NULL, 30, 30, 0),
('CRYMGR01741718', NULL, NULL, NULL, NULL, NULL, NULL, 30, 30, 0),
('CRYMGR02032276', NULL, NULL, NULL, NULL, NULL, NULL, 30, 30, 0),
('CRYMGR03894576', NULL, NULL, NULL, NULL, NULL, NULL, 30, 30, 0),
('CRYMGR40361339', NULL, NULL, NULL, NULL, NULL, NULL, 30, 30, 0),
('CRYMGR50761502', NULL, NULL, NULL, NULL, NULL, NULL, 30, 30, 0),
('CRYMGR51725122', 'Personal', 'full', '2025-04-29', '2025-04-30', '', 'CRYMGR03894576', 30, 26, 2),
('CRYMGR72551806', NULL, NULL, NULL, NULL, NULL, NULL, 30, 30, 0),
('CRYMGR75861340', 'Sick', 'half', '2025-04-25', '2025-04-25', 'fsfsffsf', '', 30, 27, 3),
('CRYMGR86854406', NULL, NULL, NULL, NULL, NULL, NULL, 30, 30, 0);

-- --------------------------------------------------------

--
-- Table structure for table `manager`
--

CREATE TABLE `manager` (
  `mag_id` varchar(255) NOT NULL,
  `mag_cmp_id` varchar(255) DEFAULT NULL,
  `mag_name` varchar(255) NOT NULL,
  `mag_email` varchar(255) NOT NULL,
  `mag_phone` varchar(15) DEFAULT NULL,
  `mag_password` varchar(255) NOT NULL,
  `mag_role` varchar(100) DEFAULT NULL,
  `mag_profile` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `mag_gender` varchar(255) NOT NULL,
  `github` varchar(1000) DEFAULT NULL,
  `linkedin` varchar(1000) DEFAULT NULL,
  `x` varchar(1000) DEFAULT NULL,
  `onLeave` int NOT NULL DEFAULT '0',
  `mag_salary` int DEFAULT '0',
  `mag_join_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `mag_exp` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `manager`
--

INSERT INTO `manager` (`mag_id`, `mag_cmp_id`, `mag_name`, `mag_email`, `mag_phone`, `mag_password`, `mag_role`, `mag_profile`, `mag_gender`, `github`, `linkedin`, `x`, `onLeave`, `mag_salary`, `mag_join_date`, `mag_exp`) VALUES
('CRYMAG11800034', 'CRYCOM57374340', 'strange', 'marvel@gmail.com', NULL, '123456', 'DevOps Engineer', '../uploads/manager_images/download.jpg', 'Male', NULL, NULL, NULL, 0, 4242332, '2025-04-26 17:45:24', 8),
('CRYMGR01741718', 'CRYCOM57374340', 'magnus', 'magnus@gmail.com', '1234567890', '123456', 'Developer', '../uploads/manager_images/L death note.jpg', 'Female', '', '', '', 0, 73381, '2020-11-22 00:00:00', 6),
('CRYMGR02032276', 'CRYCOM57374340', 'deku', 'deku@gmail.ocom', '1234567890', '123456', 'Admin', '../uploads/manager_images/7d1140ea-a129-401c-8ad8-e8a6f90df8d5.jpg', 'Male', NULL, NULL, NULL, 0, 70865, '2023-02-21 00:00:00', 6),
('CRYMGR03894576', 'CRYCOM57374340', 'buddy', '12315479@neocolab.aitrada', '1234567980', '145632', 'Senior Developer', '../uploads/manager_images/Adobe Express - file (1).png', 'Female', NULL, NULL, NULL, 0, 598, '2023-10-27 00:00:00', 1),
('CRYMGR07511685', 'CRYCOM57374340', 'saf', 'jkasd@gmail.com', '4324531230', '123456', 'Tester', '../uploads/manager_images/cute kurma.jpg', 'Female', NULL, NULL, NULL, 0, 0, '2025-04-23 23:38:05', 6),
('CRYMGR40361339', 'CRYCOM57374340', 'Todorouki', '12315479@neocolab.aidsadadadada', '1234567890', '123456', 'Tester', '../uploads/manager_images/cute wallpapers.jpg', 'Female', NULL, NULL, NULL, 0, 69054, '2022-02-04 00:00:00', 6),
('CRYMGR50761502', 'CRYCOM57374340', 'dd', 'hack.3656@gmail.com', '1234567892', '123456', 'Tester', '../uploads/manager_images/kiki.jpg', 'Female', NULL, NULL, NULL, 0, 73683, '2023-02-14 00:00:00', 3),
('CRYMGR51725122', 'CRYCOM57374340', 'Yo Shindo', 'magdxd234@gmail.com', '2342342342', '123132', 'Senior Developer', '../uploads/manager_images/4.jpg', 'Other', 'https://www.flaticon.com/free-icon-font/home_3917032?related_id=3917032', 'https://www.flaticon.com/free-icon-font/home_3917032?related_id=3917032', '', 0, 58503, '2022-08-20 00:00:00', 4),
('CRYMGR72551806', 'CRYCOM57374340', 'dd', 'dhkjshkjfdsahkj@gmail.com', '1234567892', '123123', 'Manager', '../uploads/manager_images/6.jpg', 'Female', NULL, NULL, NULL, 0, 78410, '2024-02-21 00:00:00', 3),
('CRYMGR75861340', 'CRYCOM57374340', 'Tester ', 'test@gmail.com', '1234567892', '123123', 'Manager', '../uploads/manager_images/pngwing.com (4).png', 'Male', 'https://www.flaticon.com/free-icon-font/home_3917032?related_id=3917032', 'https://www.flaticon.com/free-icon-font/home_3917032?related_id=3917032', 'https://www.flaticon.com/free-icon-font/home_3917032?related_id=3917032', 0, 0, '2025-04-24 16:12:21', 2),
('CRYMGR86854406', 'CRYCOM07726462', 'dd', 'htiosbdi@gmail.com', '1234567892', '123456', 'Admin', '../uploads/manager_images/Adobe Express - file (1).png', 'Male', NULL, NULL, NULL, 0, 43276, '2022-03-09 00:00:00', 9);

-- --------------------------------------------------------

--
-- Table structure for table `manager_feedback`
--

CREATE TABLE `manager_feedback` (
  `MF_id` int NOT NULL,
  `MF_mag_id` varchar(255) DEFAULT NULL,
  `MF_type` varchar(255) DEFAULT NULL,
  `MF_issue` varchar(1000) DEFAULT NULL,
  `MF_priority` varchar(20) DEFAULT NULL,
  `MF_remark` varchar(1000) DEFAULT NULL,
  `MF_is_resolved` int DEFAULT '0',
  `MF_cmp_id` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `manager_feedback`
--

INSERT INTO `manager_feedback` (`MF_id`, `MF_mag_id`, `MF_type`, `MF_issue`, `MF_priority`, `MF_remark`, `MF_is_resolved`, `MF_cmp_id`) VALUES
(1, 'CRYMGR01741718', 'Bug Report', 'this is error', 'Medium', 'this is error and i need to resolve it', 1, 'CRYCOM57374340'),
(3, 'CRYMGR01741718', 'Bug Report', 'this is error', 'Medium', 'how are you', 1, 'CRYCOM57374340'),
(5, 'CRYMGR01741718', 'Complaint', 'staff is rude', 'Critical', 'is it ok now?', 1, 'CRYCOM57374340'),
(6, 'CRYMGR01741718', 'Complaint', 'staff is rude', 'Critical', 'action taken', 1, 'CRYCOM57374340'),
(8, 'CRYMGR01741718', 'Complaint', 'staff is rude', 'Critical', 'done?', 1, 'CRYCOM57374340'),
(9, 'CRYMGR01741718', 'Complaint', 'staff is rude', 'Critical', 'ok fixing it', 1, 'CRYCOM57374340'),
(15, 'CRYMGR01741718', 'Improvement', 'improve the dashboard', 'Medium', 'hjgjhgjhgkyug', 1, 'CRYCOM57374340'),
(16, 'CRYMGR01741718', 'Improvement', 'improve the dashboard', 'Medium', 'htids is wokring', 1, 'CRYCOM57374340'),
(18, 'CRYMGR01741718', 'Improvement', 'da', 'Low', 'xczczc', 1, 'CRYCOM57374340'),
(19, 'CRYMGR51725122', 'Bug', 'somthing', 'Critical', 'done', 1, 'CRYCOM57374340'),
(20, 'CRYMGR51725122', 'Improvement', 'hdgffg', 'High', 'this is wokring', 1, 'CRYCOM57374340'),
(22, 'CRYMGR51725122', 'Improvement', 'this is not jksdj', 'High', 'this is now done ok?', 1, 'CRYCOM57374340');

-- --------------------------------------------------------

--
-- Table structure for table `project`
--

CREATE TABLE `project` (
  `project_id` varchar(255) NOT NULL,
  `project_name` varchar(255) DEFAULT NULL,
  `project_descp` varchar(100) DEFAULT NULL,
  `project_start_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `project_end_date` timestamp NULL DEFAULT NULL,
  `project_budget` varchar(255) DEFAULT NULL,
  `project_profit` varchar(255) DEFAULT NULL,
  `project_progress` int DEFAULT NULL,
  `project_rating` float DEFAULT NULL,
  `project_status` varchar(255) DEFAULT NULL,
  `project_priority` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `project_alloc_mag` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `project_alloc_cmp` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `project_alloc_emp` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `project`
--

INSERT INTO `project` (`project_id`, `project_name`, `project_descp`, `project_start_date`, `project_end_date`, `project_budget`, `project_profit`, `project_progress`, `project_rating`, `project_status`, `project_priority`, `category`, `project_alloc_mag`, `project_alloc_cmp`, `project_alloc_emp`, `created_at`) VALUES
('CRYPRJ001', 'Inventory Management System', 'Manage stock and supply chain.', '2023-03-01 03:30:00', '2023-12-15 12:30:00', '500000', '100000', 100, 4, 'Completed', 'Medium', 'Backend', 'CRYMGR01741718', 'CRYCOM02694494', 'CRYEMP02067991', '2001-10-29 18:30:00'),
('CRYPRJ002', 'Employee Tracker App', 'Tracks employee activities and attendance.', '2023-06-10 05:00:00', '2024-03-20 11:30:00', '300000', '75000', 45, 4.3, 'completed', 'Low', 'Fullstack', 'CRYMGR02032276', 'CRYCOM03101881', 'CRYEMP36424176', '2004-04-06 18:30:00'),
('CRYPRJ003', 'Financial Dashboard', 'Visual representation of financial data.', '2024-01-05 03:15:00', '2024-11-30 10:30:00', '700000', '200000', 70, 4.8, 'on hold', 'Medium', 'Frontend', 'CRYMGR40361339', 'CRYCOM07726462', 'CRYEMP40516348', '2005-11-04 18:30:00'),
('CRYPRJ004', 'Customer Support Portal', 'Helps manage customer queries and feedback.', '2022-09-15 05:30:00', '2023-05-25 10:00:00', '450000', '125000', 60, 4.1, 'active', 'Low', 'Testing', 'CRYMGR50761502', 'CRYCOM13614964', 'CRYEMP48254159', '2006-06-08 18:30:00'),
('CRYPRJ005', 'AI Chatbot Integration', 'Integrate chatbot into customer services.', '2024-02-10 04:00:00', '2024-08-30 12:30:00', '600000', '180000', 55, 4.6, 'In Progress', 'Medium', 'AI/ML', 'CRYMGR03894576', 'CRYCOM08642589', 'CRYEMP02067991', '2004-08-26 18:30:00'),
('CRYPRJ007', 'E-Commerce Backend Revamp', 'Enhance performance and scalability.', '2024-07-01 02:30:00', '2025-02-15 13:00:00', '750000', '220000', 65, 4.2, 'on hold', 'Medium', 'Backend', 'CRYMGR51725122', 'CRYCOM20822367', 'CRYEMP40516348', '2003-12-17 18:30:00'),
('CRYPRJ008', 'Learning Management System', 'Platform to manage training and learning.', '2022-10-05 04:15:00', '2023-08-10 10:45:00', '400000', '95000', 90, 4.7, 'completed', 'Low', 'Fullstack', 'CRYMGR72551806', 'CRYCOM24480213', 'CRYEMP48254159', '2005-11-06 18:30:00'),
('CRYPRJ009', 'Inventory Management System', 'Track and manage warehouse stock.', '2023-01-12 02:45:00', '2023-09-22 11:30:00', '550000', '160000', 39, 4.8, 'On Hold', 'Medium', 'Database', 'CRYMGR40361339', 'CRYCOM05644496', 'CRYEMP02067991', '2007-05-16 18:30:00'),
('CRYPRJ010', 'Cybersecurity Dashboard', 'Real-time threat detection dashboard.', '2024-05-20 04:30:00', '2025-01-25 12:00:00', '850000', '270000', 72, 4.4, 'active', 'High', 'Security', 'CRYMGR51725122', 'CRYCOM16342198', 'CRYEMP36424176', '2009-04-24 18:30:00'),
('CRYPRJ011', 'Healthcare Data Analytics', 'Visualize patient metrics with AI.', '2023-11-03 03:30:00', '2024-08-01 12:30:00', '920000', '330000', 68, 4.5, 'active', 'High', 'Analytics', 'CRYMGR03894576', 'CRYCOM08642589', 'CRYEMP40516348', '2004-06-19 18:30:00'),
('CRYPRJ012', 'Fleet Tracking System', 'Track delivery vehicles in real time.', '2022-06-18 03:15:00', '2023-02-28 11:45:00', '480000', '120000', 100, 4.6, 'active', 'Low', 'IoT', 'CRYMGR02032276', 'CRYCOM03173226', 'CRYEMP48254159', '2004-05-20 18:30:00'),
('CRYPRJ013', 'Social Media Scheduler', 'Automate post scheduling across platforms.', '2023-09-07 04:00:00', '2024-06-14 13:00:00', '610000', '190000', 83, 4.3, 'active', 'Medium', 'Web App', 'CRYMGR40361339', 'CRYCOM13586885', 'CRYEMP02067991', '2008-07-12 18:30:00'),
('CRYPRJ014', 'Employee Portal Redesign', 'UI/UX overhaul for internal HR portal.', '2024-01-10 05:00:00', '2024-12-05 11:15:00', '720000', '250000', 76, 4.7, 'completed', 'Medium', 'Frontend', 'CRYMGR51725122', 'CRYCOM13742340', 'CRYEMP40516348', '2009-07-05 18:30:00'),
('CRYPROJ100', 'Alpha Tracker', 'Employee tracking tool', '2024-01-15 03:30:00', '2024-06-10 12:30:00', '100000', '25000', 80, 4.5, 'completed', 'High', 'HR Tech', 'CRYMGR02032276', 'CRYCOM03173226', 'CRYEMP02067991', '2001-12-21 18:30:00'),
('CRYPROJ101', 'Alpha Tracker - UI', 'Front-end for Alpha Tracker', '2024-01-20 04:30:00', '2024-06-12 11:30:00', '40000', '12000', 75, 4.2, 'in review', 'Medium', 'HR Tech', 'CRYMGR02032276', 'CRYCOM03173226', 'CRYEMP36424176', '2001-04-29 18:30:00'),
('CRYPROJ102', 'Alpha Tracker - Backend', 'Server-side services for Alpha Tracker', '2024-01-22 03:30:00', '2024-06-15 13:00:00', '50000', '13000', 78, 4.4, 'active', 'High', 'HR Tech', 'CRYMGR02032276', 'CRYCOM03173226', 'CRYEMP40516348', '2000-09-18 18:30:00'),
('CRYPROJ103', 'Alpha Tracker - QA', 'Testing and QA automation for Alpha Tracker', '2024-01-25 05:30:00', '2024-06-18 11:00:00', '30000', '8000', 70, 4, 'active', 'Medium', 'HR Tech', 'CRYMGR02032276', 'CRYCOM03173226', 'CRYEMP48254159', '2009-08-05 18:30:00'),
('CRYPROJ104', 'FinCore Analysis', 'Developing core risk engine for fintech product', '2024-02-01 04:00:00', '2024-07-10 11:30:00', '60000', '18000', 80, 4.6, 'in review', 'High', 'FinTech', 'CRYMGR40361339', 'CRYCOM05644496', 'CRYEMP02067991', '2005-11-09 18:30:00'),
('CRYPROJ105', 'ShopSavvy Revamp', 'New features for ShopSavvy online platform', '2024-03-05 04:30:00', '2024-08-15 12:30:00', '45000', '15000', 66, 4.1, 'completed', 'Medium', 'E-commerce', 'CRYMGR01741718', 'CRYCOM08642589', 'CRYEMP40516348', '2000-06-30 18:30:00'),
('CRYPROJ106', 'LearnMax API Upgrade', 'Backend upgrade for LearnMax education platform', '2024-04-01 03:15:00', '2024-09-01 12:00:00', '38000', '12000', 60, 4, 'on hold', 'Medium', 'EdTech', 'CRYMGR51725122', 'CRYCOM20822367', 'CRYEMP36424176', '2004-11-25 18:30:00'),
('CRYPROJ107', 'FleetVision Mobile', 'Logistics tracking app for delivery fleet', '2024-02-18 03:45:00', '2024-07-30 10:30:00', '42000', '10000', 72, 4.3, 'completed', 'High', 'Logistics', 'CRYMGR03894576', 'CRYCOM24480213', 'CRYEMP48254159', '2003-01-13 18:30:00'),
('CRYPROJ108', 'SmartBudget AI', 'AI-powered personal finance and budgeting assistant', '2024-03-05 04:30:00', '2024-08-15 12:30:00', '52000', '17000', 65, 4.5, 'active', 'Medium', 'Finance', 'CRYMGR51725122', 'CRYCOM57374340', 'CRYEMP02067991', '2000-06-21 18:30:00'),
('CRYPROJ109', 'MediConnect Portal', 'Unified patient-doctor communication platform', '2024-01-12 03:00:00', '2024-06-25 11:30:00', '60000', '12000', 80, 4.7, 'in review', 'High', 'Healthcare', 'CRYMGR51725122', 'CRYCOM57374340', 'CRYEMP02067991', '2003-04-01 18:30:00'),
('CRYPROJ112', 'UrbanEco', 'Smart city environmental monitoring platform', '2024-02-10 03:15:00', '2024-08-05 12:00:00', '57000', '14000', 75, 4.6, 'in review', 'High', 'Environment', 'CRYMGR51725122', 'CRYCOM57374340', 'CRYEMP02067991', '2004-10-31 18:30:00'),
('CRYPROJ113', 'RetailSync', 'Omnichannel retail inventory sync system', '2024-03-15 04:30:00', '2024-09-01 12:30:00', '46000', '11000', 62, 4, 'completed', 'High', 'Retail', 'CRYMGR51725122', 'CRYCOM57374340', 'CRYEMP02067991', '2004-06-06 18:30:00'),
('CRYPROJ114', 'SafeZone', 'Campus security and emergency alert app', '2024-01-25 04:00:00', '2024-07-10 11:00:00', '50000', '13000', 77, 4.3, 'active', 'Medium', 'Security', 'CRYMGR51725122', 'CRYCOM57374340', 'CRYEMP02067991', '2007-08-29 18:30:00'),
('CRYPROJ115', 'FitHive', 'Health & wellness community tracking platform', '2024-02-28 03:50:00', '2024-07-20 12:10:00', '41000', '9000', 70, 4.5, 'on hold', 'Low', 'Health', 'CRYMGR51725122', 'CRYCOM57374340', 'CRYEMP02067991', '2005-01-06 18:30:00'),
('CRYPROJ116', 'TravelSphere', 'AI-driven travel recommendation and booking system', '2024-03-05 03:45:00', '2024-09-30 12:30:00', '62000', '16000', 74, 4.7, 'completed', 'High', 'Travel', 'CRYMGR51725122', 'CRYCOM57374340', 'CRYEMP02067991', '2002-02-08 18:30:00'),
('CRYPROJ117', 'TaskForge', 'Collaborative task management for enterprises', '2024-02-15 03:20:00', '2024-08-10 11:30:00', '44000', '9500', 68, 4.2, 'active', 'Medium', 'Productivity', 'CRYMGR51725122', 'CRYCOM57374340', 'CRYEMP02067991', '2005-06-24 18:30:00'),
('CRYPROJ118', 'AquaMeter', 'IoT-based water quality tracking platform', '2024-01-30 03:40:00', '2024-07-05 10:30:00', '53000', '12000', 79, 4.6, 'in review', 'Medium', 'Environment', 'CRYMGR51725122', 'CRYCOM57374340', 'CRYEMP02067991', '2001-02-05 18:30:00'),
('CRYPROJ119', 'QuickQuote', 'Instant insurance quote generator app', '2024-04-01 04:00:00', '2024-10-01 12:00:00', '47000', '10000', 66, 4.1, 'active', 'Low', 'Insurance', 'CRYMGR51725122', 'CRYCOM57374340', 'CRYEMP02067991', '2009-01-12 18:30:00'),
('CRYPROJ120', 'dad', 'dasdad', '2025-04-23 18:30:00', '2025-04-28 18:30:00', '424234234', NULL, 0, NULL, 'Started', 'Medium', 'Development', 'CRYMGR51725122', 'CRYCOM57374340', 'CRYEMP02313457', '2001-11-27 18:30:00'),
('CRYPROJ121', 'dad', 'dasdad', '2025-04-23 18:30:00', '2025-04-28 18:30:00', '424234234', NULL, 0, NULL, 'Started', 'Medium', 'Development', 'CRYMGR51725122', 'CRYCOM57374340', 'CRYEMP02313457', '2002-06-01 18:30:00'),
('CRYPROJ122', 'crybug', 'adsdaadads', '2025-04-29 18:30:00', '2025-04-21 18:30:00', '', NULL, 0, NULL, 'Started', 'High', 'Marketing', 'CRYMGR51725122', 'CRYCOM57374340', 'CRYEMP02189478', '2006-05-15 18:30:00'),
('CRYPROJ126', 'crybug', 'adsdaadads', '2025-04-29 18:30:00', '2025-04-21 18:30:00', '', NULL, 0, NULL, 'Started', 'High', 'Marketing', 'CRYMGR51725122', 'CRYCOM57374340', 'CRYEMP02189478', '2004-08-08 18:30:00'),
('CRYPROJ127', 'test', 'this should be transfered', '2025-04-23 18:30:00', '2025-04-25 18:30:00', '123456', NULL, 0, NULL, 'Started', 'High', 'Design', 'CRYMGR51725122', 'CRYCOM57374340', 'CRYEMP45969916', '2003-12-03 18:30:00'),
('CRYPROJ128', 'something', 'this is street fugeter poriehsa aodsaiud  dhj  ld d', '2025-04-17 18:30:00', '2025-04-18 18:30:00', '1254', NULL, 0, NULL, 'Started', 'High', 'Design', 'CRYMGR51725122', 'CRYCOM57374340', 'CRYEMP02113452', '2005-10-16 18:30:00'),
('CRYPROJ129', 'dadad', 'adasdsads', '2025-04-23 18:30:00', '2025-04-24 18:30:00', '2434', NULL, 0, NULL, 'Started', 'Medium', 'Design', 'CRYMGR51725122', 'CRYCOM57374340', 'CRYEMP02231885', '2007-03-14 18:30:00'),
('CRYPROJ130', 'SAFfsdf', 'fdsfs', '2025-04-25 18:30:00', '2025-04-25 18:30:00', '42342', NULL, 0, NULL, 'Started', 'High', 'Design', 'CRYMGR51725122', 'CRYCOM57374340', 'CRYEMP02313457', '2008-08-19 18:30:00'),
('CRYPROJ131', 'fdsfs', 'dfsrfsd', '2025-04-23 18:30:00', '2025-04-24 18:30:00', '525235', NULL, 0, NULL, 'Started', 'High', 'Design', 'CRYMGR51725122', 'CRYCOM57374340', 'CRYEMP02313457', '2025-04-24 14:00:19'),
('CRYPROJ132', 'testing', 'this is me testing', '2025-04-30 18:30:00', '2025-04-29 18:30:00', '12313', NULL, 46, NULL, 'In Progress', 'Medium', 'Development', 'CRYMGR51725122', 'CRYCOM57374340', 'CRYEMP02067991', '2025-04-27 22:00:55'),
('CRYPROJ133', 'testing', 'this is me testing', '2025-04-30 18:30:00', '2025-04-29 18:30:00', '12313', NULL, 0, NULL, 'Started', 'Medium', 'Development', 'CRYMGR51725122', 'CRYCOM57374340', 'CRYEMP02067991', '2025-04-27 22:02:15'),
('PRJ-07c42323', 'aniplix', 'this is dmeo', '2025-04-20 18:30:00', '2025-04-23 18:30:00', '653256', NULL, 0, NULL, 'Started', 'Low', 'Development', 'CRYMGR51725122', 'CRYCOM57374340', 'CRYEMP02387941', '2001-07-31 18:30:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_feedback`
--
ALTER TABLE `admin_feedback`
  ADD PRIMARY KEY (`a_id`),
  ADD KEY `a_cmp_id` (`a_cmp_id`);

--
-- Indexes for table `bug`
--
ALTER TABLE `bug`
  ADD PRIMARY KEY (`bug_id`),
  ADD KEY `fk_bug_alloc_mag` (`bug_alloc_mag`),
  ADD KEY `fk_bug_alloc_cmp` (`bug_alloc_cmp`),
  ADD KEY `fk_bug_alloc_emp` (`bug_alloc_emp`);

--
-- Indexes for table `company`
--
ALTER TABLE `company`
  ADD PRIMARY KEY (`cmp_id`);

--
-- Indexes for table `employee`
--
ALTER TABLE `employee`
  ADD PRIMARY KEY (`emp_id`),
  ADD UNIQUE KEY `unique_emp_mail` (`emp_mail`),
  ADD KEY `fk_employee_manager` (`mag_id`);

--
-- Indexes for table `employee_otp`
--
ALTER TABLE `employee_otp`
  ADD PRIMARY KEY (`emp_mail`);

--
-- Indexes for table `emp_feedback`
--
ALTER TABLE `emp_feedback`
  ADD PRIMARY KEY (`EF_id`),
  ADD KEY `fk_emp_feedback_employee` (`EF_emp_id`),
  ADD KEY `fk_emp_feedback_manager` (`EF_mag_id`);

--
-- Indexes for table `holiday`
--
ALTER TABLE `holiday`
  ADD PRIMARY KEY (`holiday_id`),
  ADD KEY `holiday_cmp_id` (`holiday_cmp_id`);

--
-- Indexes for table `leaveapp`
--
ALTER TABLE `leaveapp`
  ADD PRIMARY KEY (`leave_id`);

--
-- Indexes for table `manager`
--
ALTER TABLE `manager`
  ADD PRIMARY KEY (`mag_id`),
  ADD UNIQUE KEY `mag_email` (`mag_email`),
  ADD KEY `mag_cmp_id` (`mag_cmp_id`);

--
-- Indexes for table `manager_feedback`
--
ALTER TABLE `manager_feedback`
  ADD PRIMARY KEY (`MF_id`),
  ADD KEY `id` (`MF_mag_id`),
  ADD KEY `fk_manager_feedback_company` (`MF_cmp_id`);

--
-- Indexes for table `project`
--
ALTER TABLE `project`
  ADD PRIMARY KEY (`project_id`),
  ADD KEY `project_alloc_manag` (`project_alloc_mag`),
  ADD KEY `project_alloc_compan` (`project_alloc_cmp`),
  ADD KEY `project_alloc_emp` (`project_alloc_emp`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_feedback`
--
ALTER TABLE `admin_feedback`
  MODIFY `a_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `emp_feedback`
--
ALTER TABLE `emp_feedback`
  MODIFY `EF_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `holiday`
--
ALTER TABLE `holiday`
  MODIFY `holiday_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `manager_feedback`
--
ALTER TABLE `manager_feedback`
  MODIFY `MF_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_feedback`
--
ALTER TABLE `admin_feedback`
  ADD CONSTRAINT `admin_feedback_ibfk_1` FOREIGN KEY (`a_cmp_id`) REFERENCES `company` (`cmp_id`);

--
-- Constraints for table `bug`
--
ALTER TABLE `bug`
  ADD CONSTRAINT `fk_bug_alloc_cmp` FOREIGN KEY (`bug_alloc_cmp`) REFERENCES `company` (`cmp_id`),
  ADD CONSTRAINT `fk_bug_alloc_emp` FOREIGN KEY (`bug_alloc_emp`) REFERENCES `employee` (`emp_id`),
  ADD CONSTRAINT `fk_bug_alloc_mag` FOREIGN KEY (`bug_alloc_mag`) REFERENCES `manager` (`mag_id`);

--
-- Constraints for table `employee`
--
ALTER TABLE `employee`
  ADD CONSTRAINT `fk_employee_manager` FOREIGN KEY (`mag_id`) REFERENCES `manager` (`mag_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `employee_otp`
--
ALTER TABLE `employee_otp`
  ADD CONSTRAINT `employee_otp_ibfk_1` FOREIGN KEY (`emp_mail`) REFERENCES `employee` (`emp_mail`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `emp_feedback`
--
ALTER TABLE `emp_feedback`
  ADD CONSTRAINT `fk_emp_feedback_employee` FOREIGN KEY (`EF_emp_id`) REFERENCES `employee` (`emp_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_emp_feedback_manager` FOREIGN KEY (`EF_mag_id`) REFERENCES `manager` (`mag_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `holiday`
--
ALTER TABLE `holiday`
  ADD CONSTRAINT `holiday_ibfk_1` FOREIGN KEY (`holiday_cmp_id`) REFERENCES `company` (`cmp_id`);

--
-- Constraints for table `manager`
--
ALTER TABLE `manager`
  ADD CONSTRAINT `manager_ibfk_1` FOREIGN KEY (`mag_cmp_id`) REFERENCES `company` (`cmp_id`) ON DELETE CASCADE;

--
-- Constraints for table `manager_feedback`
--
ALTER TABLE `manager_feedback`
  ADD CONSTRAINT `fk_manager_feedback_company` FOREIGN KEY (`MF_cmp_id`) REFERENCES `company` (`cmp_id`),
  ADD CONSTRAINT `manager_feedback_ibfk_1` FOREIGN KEY (`MF_mag_id`) REFERENCES `manager` (`mag_id`);

--
-- Constraints for table `project`
--
ALTER TABLE `project`
  ADD CONSTRAINT `project_ibfk_1` FOREIGN KEY (`project_alloc_mag`) REFERENCES `manager` (`mag_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `project_ibfk_2` FOREIGN KEY (`project_alloc_cmp`) REFERENCES `company` (`cmp_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `project_ibfk_3` FOREIGN KEY (`project_alloc_emp`) REFERENCES `employee` (`emp_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
