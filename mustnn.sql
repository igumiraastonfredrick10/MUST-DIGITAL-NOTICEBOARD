-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 04, 2025 at 12:41 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mustnn`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `entity_type` varchar(50) DEFAULT NULL,
  `entity_id` int(11) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `entity_type`, `entity_id`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, 'User logged in', 'auth', NULL, NULL, NULL, '2025-11-03 11:58:19'),
(2, 1, 'Created post: ttttt', 'notice', 1, NULL, NULL, '2025-11-03 12:01:46'),
(3, 1, 'User logged in', 'auth', NULL, NULL, NULL, '2025-11-03 12:49:44'),
(4, 2, 'User logged in', 'auth', NULL, NULL, NULL, '2025-11-03 13:03:04'),
(5, 2, 'User logged in', 'auth', NULL, NULL, NULL, '2025-11-03 13:05:14'),
(6, 2, 'Created post: where is micheal', 'notice', 2, NULL, NULL, '2025-11-03 13:07:05'),
(7, 2, 'Created post: hope', 'notice', 3, NULL, NULL, '2025-11-03 13:08:18'),
(8, 2, 'User logged in', 'auth', NULL, NULL, NULL, '2025-11-03 13:10:08'),
(9, 2, 'User logged in', 'auth', NULL, NULL, NULL, '2025-11-03 13:18:21'),
(10, 2, 'User logged in', 'auth', NULL, NULL, NULL, '2025-11-03 13:43:09'),
(11, 2, 'Updated post: hope', 'notice', 3, NULL, NULL, '2025-11-03 13:44:30'),
(12, 2, 'User logged in', 'auth', NULL, NULL, NULL, '2025-11-03 13:50:24'),
(13, 1, 'User logged in', 'auth', NULL, NULL, NULL, '2025-11-03 13:52:56'),
(14, 2, 'User logged in', 'auth', NULL, NULL, NULL, '2025-11-03 14:01:51'),
(15, 2, 'User logged in', 'auth', NULL, NULL, NULL, '2025-11-03 14:09:18'),
(16, 2, 'User logged in', 'auth', NULL, NULL, NULL, '2025-11-03 17:51:18'),
(17, 6, 'User logged in', 'auth', NULL, NULL, NULL, '2025-11-04 11:36:23'),
(18, 6, 'Created post: Haza imwe', 'notice', 4, NULL, NULL, '2025-11-04 11:40:05');

-- --------------------------------------------------------

--
-- Table structure for table `audience_types`
--

CREATE TABLE `audience_types` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audience_types`
--

INSERT INTO `audience_types` (`id`, `name`, `slug`, `description`, `is_active`, `created_at`) VALUES
(1, 'All Users', 'all', 'Visible to all registered users', 1, '2025-11-03 11:55:49'),
(2, 'Students Only', 'students', 'Visible only to students', 1, '2025-11-03 11:55:49'),
(3, 'Lecturers Only', 'lecturers', 'Visible only to lecturers', 1, '2025-11-03 11:55:49'),
(4, 'Specific Faculty', 'faculty', 'Visible to specific faculty, departments, programs, or years', 1, '2025-11-03 11:55:49'),
(5, 'Specific Program', 'program', 'Visible to specific academic programs', 1, '2025-11-03 11:55:49'),
(6, 'Specific Year', 'year', 'Visible to specific year levels', 1, '2025-11-03 11:55:49'),
(7, 'Specific Department', 'department', 'Visible to specific departments', 1, '2025-11-03 11:55:49');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `icon` varchar(50) DEFAULT 'bullhorn',
  `color` varchar(7) DEFAULT '#007bff',
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `icon`, `color`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Announcements', 'bullhorn', '#dc3545', 'General announcements and notices', 1, '2025-11-03 11:55:49', '2025-11-03 11:55:49'),
(2, 'Academic Notices', 'graduation-cap', '#007bff', 'Academic-related information', 1, '2025-11-03 11:55:49', '2025-11-03 11:55:49'),
(3, 'Events', 'calendar-day', '#28a745', 'Upcoming events and activities', 1, '2025-11-03 11:55:49', '2025-11-03 11:55:49'),
(4, 'Library Updates', 'book-open', '#6f42c1', 'Library news and updates', 1, '2025-11-03 11:55:49', '2025-11-03 11:55:49'),
(5, 'Campus News', 'newspaper', '#fd7e14', 'Campus news and information', 1, '2025-11-03 11:55:49', '2025-11-03 11:55:49'),
(6, 'Examinations', 'clipboard-check', '#e83e8c', 'Examination schedules and results', 1, '2025-11-03 11:55:49', '2025-11-03 11:55:49'),
(7, 'Admissions', 'user-plus', '#20c997', 'Admission-related notices', 1, '2025-11-03 11:55:49', '2025-11-03 11:55:49'),
(8, 'Fee Payments', 'money-bill-wave', '#ffc107', 'Fee payment information', 1, '2025-11-03 11:55:49', '2025-11-03 11:55:49'),
(9, 'Job Opportunities', 'briefcase', '#6c757d', 'Career and job opportunities', 1, '2025-11-03 11:55:49', '2025-11-03 11:55:49'),
(10, 'Research', 'microscope', '#17a2b8', 'Research opportunities and updates', 1, '2025-11-03 11:55:49', '2025-11-03 11:55:49');

-- --------------------------------------------------------

--
-- Table structure for table `chat_likes`
--

CREATE TABLE `chat_likes` (
  `id` int(11) NOT NULL,
  `message_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chat_messages`
--

INSERT INTO `chat_messages` (`id`, `user_id`, `username`, `message`, `created_at`) VALUES
(1, 1, 'Unknown User', 'hi', '2025-11-03 12:40:03'),
(2, 1, 'Unknown User', 'Test message from debug button', '2025-11-03 12:42:02'),
(3, 1, 'Unknown User', 'Test message from debug button', '2025-11-03 12:42:07'),
(4, 1, 'Unknown User', 'hi', '2025-11-03 12:42:19'),
(5, 1, 'System Administrator', 'hi', '2025-11-03 12:46:05'),
(6, 1, 'System Administrator', 'hey', '2025-11-03 13:01:00'),
(7, 2, 'John Doe', 'hi', '2025-11-03 13:03:17'),
(8, 2, 'Catherine Kyarikunda', 'huuu', '2025-11-03 13:05:34'),
(9, 2, 'Catherine Kyarikunda', 'hi', '2025-11-03 13:29:08'),
(10, 2, 'Catherine Kyarikunda', 'hi', '2025-11-03 13:29:24'),
(11, 2, 'Catherine Kyarikunda', 'hi', '2025-11-03 13:32:43'),
(12, 1, 'System Administrator', 'hi', '2025-11-03 13:53:23'),
(13, 6, 'Igumira Aston', 'hi', '2025-11-04 11:36:44');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(10) NOT NULL,
  `faculty_id` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`, `code`, `faculty_id`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Computer Science', 'CS', 1, 'Computer Science Department', 1, '2025-11-03 11:55:49', '2025-11-03 11:55:49'),
(2, 'Information Technology', 'IT', 1, 'Information Technology Department', 1, '2025-11-03 11:55:49', '2025-11-03 11:55:49'),
(3, 'Software Engineering', 'SE', 1, 'Software Engineering Department', 1, '2025-11-03 11:55:49', '2025-11-03 11:55:49'),
(4, 'Civil Engineering', 'IS', 6, 'Civil Engineering Department', 1, '2025-11-03 11:55:49', '2025-11-03 19:49:35'),
(5, 'Mechanical Engineering', 'MEE', 6, 'MEE departments', 1, '2025-11-03 19:51:01', '2025-11-03 19:51:01'),
(6, 'Electrical Engineering', 'EEE', 6, 'EEE', 1, '2025-11-03 19:51:01', '2025-11-03 19:51:01'),
(7, 'Chemistry', '', 4, 'chemistry department', 1, '2025-11-04 09:13:55', '2025-11-04 09:13:55'),
(8, 'Biology', '', 4, 'biology department', 1, '2025-11-04 09:15:36', '2025-11-04 09:15:36'),
(9, 'Mathemartics', '', 4, 'Biology department', 1, '2025-11-04 09:17:08', '2025-11-04 09:17:08'),
(10, 'Physics', '', 4, 'Physics department', 1, '2025-11-04 09:17:08', '2025-11-04 09:17:08'),
(11, 'Educations foundations and psychology', 'EFP', 4, 'Educations foundations and psychology department', 1, '2025-11-04 09:21:12', '2025-11-04 09:21:12'),
(12, 'Laboratory Technology', '', 4, 'Diploma in Science Laboratory Technology', 1, '2025-11-04 09:21:12', '2025-11-04 09:21:12'),
(13, 'Higher Education Access Certificate', 'HEAC', 4, 'Higher Education Access Certificate', 1, '2025-11-04 09:22:37', '2025-11-04 09:22:37'),
(14, 'Accounting and Finance', '', 3, 'Department of Accounting and Finance', 1, '2025-11-04 09:28:09', '2025-11-04 09:28:09'),
(15, 'Economics and Entrepreneurship', '', 3, 'Department of Economics and Entrepreneurship', 1, '2025-11-04 09:28:09', '2025-11-04 09:28:09'),
(16, 'Human resource management', '', 3, 'Department of Human resource management', 1, '2025-11-04 09:31:13', '2025-11-04 09:31:13'),
(17, 'Procurement and Marketing', '', 3, 'Department of Procurement and Marketing', 1, '2025-11-04 09:31:13', '2025-11-04 09:31:13'),
(18, 'Planning and governance', '', 7, 'Department of Planning and governance', 1, '2025-11-04 09:35:43', '2025-11-04 09:35:43'),
(19, 'Human Development and relational sciences', '', 7, 'Department of Human Development and relational sciences', 1, '2025-11-04 09:35:43', '2025-11-04 09:35:43'),
(20, 'Environment and livelihood support system', '', 7, 'Department of Environment and livelihood support system', 1, '2025-11-04 09:38:48', '2025-11-04 09:38:48'),
(21, 'Community engagement and Service learning', '', 7, 'Department of Community engagement and Service learning', 1, '2025-11-04 09:38:48', '2025-11-04 09:38:48'),
(22, 'Anesthesia', '', 2, 'Department of Anesthesia', 1, '2025-11-04 09:44:14', '2025-11-04 09:44:14'),
(23, 'Biochemistry', '', 2, 'Department of Biochemistry', 1, '2025-11-04 09:44:14', '2025-11-04 09:44:14'),
(24, 'Community Surgery', '', 2, 'Department of Community Surgery', 1, '2025-11-04 09:45:04', '2025-11-04 09:45:04'),
(25, 'Dental Surgery', '', 2, 'Department of Dental Surgery', 1, '2025-11-04 09:47:07', '2025-11-04 09:47:07'),
(26, 'Dermatology', '', 2, 'Department of Dermatology', 1, '2025-11-04 09:47:07', '2025-11-04 09:47:07'),
(29, 'Ears, Nose and Throat', '', 2, 'Department of Ears, Nose and Throat', 1, '2025-11-04 09:52:04', '2025-11-04 09:52:04'),
(30, 'Emergency medicine', '', 2, 'Department of Emergency medicine', 1, '2025-11-04 09:52:04', '2025-11-04 09:52:04'),
(31, 'Family Medicine', '', 2, 'Department of Family Medicine', 1, '2025-11-04 09:53:32', '2025-11-04 09:53:32'),
(32, 'Internal medicine', '', 2, 'Internal medicine', 1, '2025-11-04 09:53:32', '2025-11-04 09:53:32'),
(33, 'Medical Laboratory Science', '', 2, 'Department of Medical Laboratory Science', 1, '2025-11-04 09:55:32', '2025-11-04 09:55:32'),
(34, 'Nursing', '', 2, 'Department of Nursing', 1, '2025-11-04 09:55:32', '2025-11-04 09:55:32'),
(35, 'Obstetrics and Gynecology', '', 2, 'Department of Obstetrics and Gynecology', 1, '2025-11-04 09:58:44', '2025-11-04 09:58:44'),
(36, 'Ophthalmology', '', 2, 'Department of Ophthalmology', 1, '2025-11-04 09:58:44', '2025-11-04 09:58:44'),
(37, 'Pediatrics and child Health', '', 2, 'Department of Pediatrics and child Health', 1, '2025-11-04 10:02:18', '2025-11-04 10:02:18'),
(38, 'Pharmaceutical Sciences', '', 2, 'Department of Pharmaceutical Sciences', 1, '2025-11-04 10:02:18', '2025-11-04 10:02:18'),
(39, 'Pharmacy', '', 2, 'Department of Pharmacy', 1, '2025-11-04 10:04:37', '2025-11-04 10:04:37'),
(40, 'Physiology', '', 2, 'Department of Physiology', 1, '2025-11-04 10:04:37', '2025-11-04 10:04:37');

-- --------------------------------------------------------

--
-- Table structure for table `faculties`
--

CREATE TABLE `faculties` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(10) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `faculties`
--

INSERT INTO `faculties` (`id`, `name`, `code`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Faculty of Computing and Informatics', 'FCI', 'Computer Science, Information Technology, and related programs', 1, '2025-11-03 11:55:49', '2025-11-03 11:55:49'),
(2, 'Faculty of Medicine', 'FOM', 'Medical and health sciences programs', 1, '2025-11-03 11:55:49', '2025-11-03 11:55:49'),
(3, 'Faculty of Business and Management Sciences', 'FBMS', 'Business, management, and commerce programs', 1, '2025-11-03 11:55:49', '2025-11-03 11:55:49'),
(4, 'Faculty of Science', 'FOS', 'Natural sciences and mathematics programs', 1, '2025-11-03 11:55:49', '2025-11-03 11:55:49'),
(5, 'Faculty of Education', 'FOE', 'Education and teaching programs', 1, '2025-11-03 11:55:49', '2025-11-03 11:55:49'),
(6, 'Faculty of Applied Sciences And Technology', 'FAST', 'Applied sciences and technology programs', 1, '2025-11-03 11:55:49', '2025-11-03 19:45:16'),
(7, 'Faculty of Interdisciplinary studies', 'FIS', 'FIS', 1, '2025-11-03 19:43:20', '2025-11-03 19:44:39');

-- --------------------------------------------------------

--
-- Table structure for table `notices`
--

CREATE TABLE `notices` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `category_id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `priority` enum('low','normal','high','urgent') DEFAULT 'normal',
  `status` enum('draft','published','archived') DEFAULT 'draft',
  `slug` varchar(255) NOT NULL,
  `publish_date` timestamp NULL DEFAULT NULL,
  `expiry_date` timestamp NULL DEFAULT NULL,
  `is_pinned` tinyint(1) DEFAULT 0,
  `view_count` int(11) DEFAULT 0,
  `allow_comments` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notices`
--

INSERT INTO `notices` (`id`, `title`, `content`, `category_id`, `author_id`, `priority`, `status`, `slug`, `publish_date`, `expiry_date`, `is_pinned`, `view_count`, `allow_comments`, `created_at`, `updated_at`) VALUES
(1, 'ttttt', 'ffff', 4, 1, 'normal', 'published', 'ttttt-1762171306', '2025-11-03 12:01:46', NULL, 0, 0, 1, '2025-11-03 12:01:46', '2025-11-03 12:01:46'),
(2, 'where is micheal', 'tttt', 1, 2, 'normal', 'published', 'where-is-micheal-1762175225', '2025-11-03 13:07:05', NULL, 0, 0, 1, '2025-11-03 13:07:05', '2025-11-03 13:07:05'),
(3, 'hope', 'see this post', 5, 2, 'normal', 'published', 'hope-1762175298', '2025-11-03 13:08:18', NULL, 0, 0, 1, '2025-11-03 13:08:18', '2025-11-03 13:44:30'),
(4, 'Haza imwe', 'Muhuname', 5, 6, 'urgent', 'published', 'haza-imwe-1762256405', '2025-11-04 11:40:05', NULL, 0, 0, 1, '2025-11-04 11:40:05', '2025-11-04 11:40:05');

-- --------------------------------------------------------

--
-- Table structure for table `notice_attachments`
--

CREATE TABLE `notice_attachments` (
  `id` int(11) NOT NULL,
  `notice_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_type` varchar(100) NOT NULL,
  `file_size` int(11) NOT NULL,
  `display_order` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notice_attachments`
--

INSERT INTO `notice_attachments` (`id`, `notice_id`, `file_name`, `file_path`, `file_type`, `file_size`, `display_order`, `created_at`) VALUES
(1, 1, 'Screenshot 2024-05-14 215047.png', 'uploads/690899aa84171_1762171306.png', '0', 117496, 1, '2025-11-03 12:01:46'),
(2, 4, 'Lecture One - Copy.pdf', 'uploads/6909e61572460_1762256405.pdf', '0', 202694, 1, '2025-11-04 11:40:05');

-- --------------------------------------------------------

--
-- Table structure for table `notice_audiences`
--

CREATE TABLE `notice_audiences` (
  `id` int(11) NOT NULL,
  `notice_id` int(11) NOT NULL,
  `audience_type_id` int(11) NOT NULL,
  `faculty_id` int(11) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `program_id` int(11) DEFAULT NULL,
  `year_level` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notice_audiences`
--

INSERT INTO `notice_audiences` (`id`, `notice_id`, `audience_type_id`, `faculty_id`, `department_id`, `program_id`, `year_level`, `created_at`) VALUES
(1, 1, 4, NULL, NULL, 1, 2, '2025-11-03 12:01:46'),
(2, 2, 2, NULL, NULL, NULL, NULL, '2025-11-03 13:07:05'),
(3, 3, 4, NULL, NULL, 1, 3, '2025-11-03 13:08:18'),
(4, 4, 2, NULL, NULL, NULL, NULL, '2025-11-04 11:40:05');

-- --------------------------------------------------------

--
-- Table structure for table `notice_comments`
--

CREATE TABLE `notice_comments` (
  `id` int(11) NOT NULL,
  `notice_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `is_approved` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notice_comments`
--

INSERT INTO `notice_comments` (`id`, `notice_id`, `user_id`, `content`, `is_approved`, `created_at`, `updated_at`) VALUES
(1, 3, 2, 'tttt', 1, '2025-11-03 13:10:52', '2025-11-03 13:10:52'),
(2, 2, 2, 'hey', 1, '2025-11-03 17:51:34', '2025-11-03 17:51:34');

-- --------------------------------------------------------

--
-- Stand-in structure for view `notice_details`
-- (See below for the actual view)
--
CREATE TABLE `notice_details` (
`id` int(11)
,`title` varchar(255)
,`content` text
,`priority` enum('low','normal','high','urgent')
,`status` enum('draft','published','archived')
,`publish_date` timestamp
,`expiry_date` timestamp
,`is_pinned` tinyint(1)
,`view_count` int(11)
,`allow_comments` tinyint(1)
,`created_at` timestamp
,`updated_at` timestamp
,`category_name` varchar(50)
,`category_icon` varchar(50)
,`category_color` varchar(7)
,`author_name` varchar(101)
,`author_username` varchar(50)
,`audience_type_name` varchar(50)
,`audience_type_slug` varchar(50)
,`target_faculty` varchar(100)
,`target_department` varchar(100)
,`target_program` varchar(100)
,`target_year` int(11)
);

-- --------------------------------------------------------

--
-- Table structure for table `notice_likes`
--

CREATE TABLE `notice_likes` (
  `id` int(11) NOT NULL,
  `notice_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notice_likes`
--

INSERT INTO `notice_likes` (`id`, `notice_id`, `user_id`, `created_at`) VALUES
(1, 1, 1, '2025-11-03 12:09:53'),
(2, 3, 2, '2025-11-03 13:10:39');

-- --------------------------------------------------------

--
-- Table structure for table `online_users`
--

CREATE TABLE `online_users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `last_seen` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `online_users`
--

INSERT INTO `online_users` (`user_id`, `username`, `last_seen`) VALUES
(6, 'Igumira Aston', '2025-11-04 11:39:55');

-- --------------------------------------------------------

--
-- Table structure for table `post_read_status`
--

CREATE TABLE `post_read_status` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `notice_id` int(11) NOT NULL,
  `read_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `post_read_summary`
-- (See below for the actual view)
--
CREATE TABLE `post_read_summary` (
`notice_id` int(11)
,`title` varchar(255)
,`post_created` timestamp
,`total_reads` bigint(21)
,`unique_readers` bigint(21)
);

-- --------------------------------------------------------

--
-- Table structure for table `programs`
--

CREATE TABLE `programs` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(20) NOT NULL,
  `department_id` int(11) NOT NULL,
  `faculty_id` int(11) NOT NULL,
  `duration_years` int(11) NOT NULL DEFAULT 3,
  `degree_type` enum('certificate','diploma','bachelor','master','phd') NOT NULL DEFAULT 'bachelor',
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `programs`
--

INSERT INTO `programs` (`id`, `name`, `code`, `department_id`, `faculty_id`, `duration_years`, `degree_type`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Bachelor of Computer Science', 'BCS', 1, 1, 3, 'bachelor', 'Undergraduate program in Computer Science', 1, '2025-11-03 11:55:49', '2025-11-03 11:55:49'),
(2, 'Bachelor of Information Technology', 'BIT', 2, 1, 3, 'bachelor', 'Undergraduate program in Information Technology', 1, '2025-11-03 11:55:49', '2025-11-03 11:55:49'),
(3, 'Bachelor of Software Engineering', 'BSE', 3, 1, 4, 'bachelor', 'Undergraduate program in Software Engineering', 1, '2025-11-03 11:55:49', '2025-11-03 11:55:49'),
(4, 'Bachelor of Information Systems', 'BIS', 4, 1, 3, 'bachelor', 'Undergraduate program in Information Systems', 1, '2025-11-03 11:55:49', '2025-11-03 11:55:49'),
(5, 'Master of Computer Science', 'MCS', 1, 1, 2, 'master', 'Graduate program in Computer Science', 1, '2025-11-03 11:55:49', '2025-11-03 11:55:49'),
(6, 'Master of Information Technology', 'MIT', 2, 1, 2, 'master', 'Graduate program in Information Technology', 1, '2025-11-03 11:55:49', '2025-11-03 11:55:49'),
(7, 'Bachelor of Mechanical Engineering', 'BME', 5, 6, 4, 'bachelor', 'BME', 1, '2025-11-03 19:54:02', '2025-11-03 19:54:02'),
(8, ' Masters in Mechanical Engineering', 'MME', 5, 6, 3, 'master', 'MME', 1, '2025-11-03 19:54:02', '2025-11-03 19:59:24'),
(9, 'Bachelors in Civil Engineering', 'BCE', 4, 6, 4, 'bachelor', 'CVE', 1, '2025-11-03 19:56:00', '2025-11-03 19:56:00'),
(10, 'Masters in Civil Engineering', 'MCE', 4, 6, 3, 'master', 'MCE', 1, '2025-11-03 19:56:00', '2025-11-03 19:58:58'),
(11, 'Bachelors in Electrical Engineering', 'BEE', 6, 6, 4, 'bachelor', 'BEE', 1, '2025-11-03 19:58:16', '2025-11-03 19:58:16'),
(12, 'Masters in Electrical Engineering', 'MEE', 6, 6, 3, 'master', 'MEE', 1, '2025-11-03 19:58:16', '2025-11-03 19:58:16'),
(13, 'Bachelors of Science in chemistry', 'BSC', 7, 4, 3, 'bachelor', 'Undergraduate Bachelors of Science in chemistry', 1, '2025-11-04 10:08:51', '2025-11-04 11:19:09'),
(16, 'bachelors in civil engineering', 'BCE', 4, 6, 3, 'bachelor', 'undergraduate bachelors in civil engineering', 1, '2025-11-04 10:18:31', '2025-11-04 11:19:39'),
(17, 'bachelors of community engagement and service learning ', 'BCESL', 21, 7, 3, 'bachelor', 'undergraduate bachelors of community engagement and service learning ', 1, '2025-11-04 10:21:11', '2025-11-04 11:20:02'),
(18, 'bachelors of community surgery', 'BCS', 24, 4, 3, 'bachelor', 'undergraduate bachelors of community surgery', 1, '2025-11-04 10:23:11', '2025-11-04 11:20:19'),
(19, 'bachelors of dental surgery', 'BDS', 25, 2, 3, 'bachelor', 'undergraduate bachelors of dental surgery', 1, '2025-11-04 10:24:46', '2025-11-04 11:20:33'),
(20, 'bachelors of dermatology', 'BOD', 26, 2, 3, 'bachelor', 'undergraduate bachelors of dermatology', 1, '2025-11-04 10:25:48', '2025-11-04 11:20:56'),
(21, 'bachelors of ear,nose and throat', 'BENT', 29, 2, 3, 'bachelor', 'undergraduate bachelors of ear,nose and throat', 1, '2025-11-04 10:27:13', '2025-11-04 11:21:30'),
(22, 'bachelors of economics and entrepreneurship', 'BEE', 15, 3, 3, 'bachelor', 'undergraduate bachelors of economics and entrepreneurship', 1, '2025-11-04 10:28:33', '2025-11-04 11:21:51'),
(23, 'bachelors of educations foundations and psychology', 'BEFP', 11, 5, 3, 'bachelor', 'undergraduate bachelors of educations foundations and psychology ', 1, '2025-11-04 10:30:22', '2025-11-04 11:22:21'),
(24, 'bachelors of emergency medicine', 'BEM', 30, 2, 3, 'bachelor', 'undergraduate bachelors of emergency medicine', 1, '2025-11-04 10:34:23', '2025-11-04 11:18:53'),
(25, 'bachelors of environment and livelihood support system ', 'BELSS', 20, 4, 3, 'bachelor', 'undergraduate bachelors of environment and livelihood support system ', 1, '2025-11-04 10:38:53', '2025-11-04 11:22:43'),
(26, 'bachelors of laboratory technology', 'BLT', 12, 4, 3, 'bachelor', 'undergraduate bachelors of laboratory technology', 1, '2025-11-04 10:41:33', '2025-11-04 11:23:04'),
(27, 'bachelors of mathematics', 'BOM', 9, 5, 3, 'bachelor', 'undergraduate bachelors of mathematics', 1, '2025-11-04 10:42:49', '2025-11-04 11:23:27'),
(28, 'bachelors of mechanical engineering', 'BME', 5, 6, 3, 'bachelor', 'undergraduate bachelors of mechanical engineering', 1, '2025-11-04 10:44:55', '2025-11-04 11:23:44'),
(29, 'bachelors of medical laboratory', 'BML', 33, 4, 3, 'bachelor', 'undergraduate bachelors of medical laboratory', 1, '2025-11-04 10:48:50', '2025-11-04 11:24:04'),
(30, 'bachelors of pharmaceutical sciences', 'BPS', 38, 2, 3, 'bachelor', 'undergraduate bachelors of pharmaceutical sciences', 1, '2025-11-04 10:51:19', '2025-11-04 11:17:09'),
(31, 'bachelor of  Pharmacy ', 'BSP', 39, 2, 5, 'bachelor', 'undergraduate bachelor of  Pharmacy ', 1, '2025-11-04 10:54:45', '2025-11-04 11:17:55'),
(32, 'bachelor of procurement and marketing', 'BPM', 17, 3, 3, 'bachelor', 'undergraduate bachelor of procurement and marketing', 1, '2025-11-04 10:58:40', '2025-11-04 10:58:40');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `role` enum('admin','lecturer','student') NOT NULL DEFAULT 'student',
  `faculty_id` int(11) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `program_id` int(11) DEFAULT NULL,
  `year_of_study` int(11) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `first_name`, `last_name`, `role`, `faculty_id`, `department_id`, `program_id`, `year_of_study`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@must.ac.ug', 'admin123', 'System', 'Administrator', 'admin', NULL, NULL, NULL, NULL, 1, '2025-11-03 11:55:49', '2025-11-03 11:55:49'),
(2, 'Cathy', 'student1@must.ac.ug', 'cathy123', 'Catherine', 'Kyarikunda', 'student', 1, 1, 1, 3, 1, '2025-11-03 11:55:49', '2025-11-03 13:09:29'),
(3, 'beacky', 'lecturer1@must.ac.ug', 'beacky123', 'Kyomugisha', 'Beatrice', 'lecturer', 1, 1, 1, 3, 1, '2025-11-03 11:55:49', '2025-11-04 08:23:30'),
(4, 'Amara', 'claireamara@must.ac.ug', 'claire123', 'Namara ', 'Claire', 'student', 6, 4, 9, 4, 1, '2025-11-04 08:21:10', '2025-11-04 08:21:10'),
(6, 'aston', 'aston@must.ac.ug', 'aston', 'Igumira', 'Aston', 'student', 4, 9, 27, 3, 1, '2025-11-04 11:01:21', '2025-11-04 11:03:07'),
(7, 'victor', 'victor@gmail.ac.ug', 'victor', 'kirabo', 'victor', 'student', 7, 20, 25, 2, 1, '2025-11-04 11:04:56', '2025-11-04 11:04:56'),
(8, 'anitah', 'anitah@gmail.ac.ug', 'anitah', 'nakirunga', 'anitah', 'student', 3, 17, 32, 1, 1, '2025-11-04 11:06:30', '2025-11-04 11:06:30'),
(9, 'frank', 'frank@gmail.ac.ug', 'frank', 'nomwesiga', 'frank', 'student', 2, 12, 29, 4, 1, '2025-11-04 11:09:22', '2025-11-04 11:09:22'),
(10, 'innocent', 'innocent@gmail.ac.ug', 'innocent', 'Akankwatagye ', 'Innocent', 'student', 1, 3, 3, 2, 1, '2025-11-04 11:12:37', '2025-11-04 11:12:37'),
(11, 'joshua', 'joshua@gmail.ac.ug', 'joshua', 'akandwanaho', 'joshua', 'student', 2, 26, 20, 1, 1, '2025-11-04 11:16:25', '2025-11-04 11:16:25'),
(12, 'Keith', 'ndyamuhakyikeith@must.ac.ug', 'keith123', 'Ndyamuhakyi', 'Keith', 'student', 7, 21, 17, 3, 1, '2025-11-04 11:34:56', '2025-11-04 11:34:56'),
(13, 'Fortunate', 'twesiimefortunate@must.ac.ug', 'fortunate123', 'Twesiime', 'Fortunate', 'student', 4, 7, 13, 3, 1, '2025-11-04 11:34:56', '2025-11-04 11:34:56');

-- --------------------------------------------------------

--
-- Stand-in structure for view `user_details`
-- (See below for the actual view)
--
CREATE TABLE `user_details` (
`id` int(11)
,`username` varchar(50)
,`email` varchar(100)
,`first_name` varchar(50)
,`last_name` varchar(50)
,`role` enum('admin','lecturer','student')
,`year_of_study` int(11)
,`is_active` tinyint(1)
,`created_at` timestamp
,`faculty_name` varchar(100)
,`faculty_code` varchar(10)
,`department_name` varchar(100)
,`department_code` varchar(10)
,`program_name` varchar(100)
,`program_code` varchar(20)
,`duration_years` int(11)
);

-- --------------------------------------------------------

--
-- Structure for view `notice_details`
--
DROP TABLE IF EXISTS `notice_details`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `notice_details`  AS SELECT `n`.`id` AS `id`, `n`.`title` AS `title`, `n`.`content` AS `content`, `n`.`priority` AS `priority`, `n`.`status` AS `status`, `n`.`publish_date` AS `publish_date`, `n`.`expiry_date` AS `expiry_date`, `n`.`is_pinned` AS `is_pinned`, `n`.`view_count` AS `view_count`, `n`.`allow_comments` AS `allow_comments`, `n`.`created_at` AS `created_at`, `n`.`updated_at` AS `updated_at`, `c`.`name` AS `category_name`, `c`.`icon` AS `category_icon`, `c`.`color` AS `category_color`, concat(`u`.`first_name`,' ',`u`.`last_name`) AS `author_name`, `u`.`username` AS `author_username`, `at`.`name` AS `audience_type_name`, `at`.`slug` AS `audience_type_slug`, `f`.`name` AS `target_faculty`, `d`.`name` AS `target_department`, `p`.`name` AS `target_program`, `na`.`year_level` AS `target_year` FROM (((((((`notices` `n` left join `categories` `c` on(`n`.`category_id` = `c`.`id`)) left join `users` `u` on(`n`.`author_id` = `u`.`id`)) left join `notice_audiences` `na` on(`n`.`id` = `na`.`notice_id`)) left join `audience_types` `at` on(`na`.`audience_type_id` = `at`.`id`)) left join `faculties` `f` on(`na`.`faculty_id` = `f`.`id`)) left join `departments` `d` on(`na`.`department_id` = `d`.`id`)) left join `programs` `p` on(`na`.`program_id` = `p`.`id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `post_read_summary`
--
DROP TABLE IF EXISTS `post_read_summary`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `post_read_summary`  AS SELECT `n`.`id` AS `notice_id`, `n`.`title` AS `title`, `n`.`created_at` AS `post_created`, count(`prs`.`id`) AS `total_reads`, count(distinct `prs`.`user_id`) AS `unique_readers` FROM (`notices` `n` left join `post_read_status` `prs` on(`n`.`id` = `prs`.`notice_id`)) WHERE `n`.`status` = 'published' GROUP BY `n`.`id`, `n`.`title`, `n`.`created_at` ORDER BY `n`.`created_at` DESC ;

-- --------------------------------------------------------

--
-- Structure for view `user_details`
--
DROP TABLE IF EXISTS `user_details`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `user_details`  AS SELECT `u`.`id` AS `id`, `u`.`username` AS `username`, `u`.`email` AS `email`, `u`.`first_name` AS `first_name`, `u`.`last_name` AS `last_name`, `u`.`role` AS `role`, `u`.`year_of_study` AS `year_of_study`, `u`.`is_active` AS `is_active`, `u`.`created_at` AS `created_at`, `f`.`name` AS `faculty_name`, `f`.`code` AS `faculty_code`, `d`.`name` AS `department_name`, `d`.`code` AS `department_code`, `p`.`name` AS `program_name`, `p`.`code` AS `program_code`, `p`.`duration_years` AS `duration_years` FROM (((`users` `u` left join `faculties` `f` on(`u`.`faculty_id` = `f`.`id`)) left join `departments` `d` on(`u`.`department_id` = `d`.`id`)) left join `programs` `p` on(`u`.`program_id` = `p`.`id`)) ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `audience_types`
--
ALTER TABLE `audience_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_slug` (`slug`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_name` (`name`);

--
-- Indexes for table `chat_likes`
--
ALTER TABLE `chat_likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_like` (`message_id`,`user_id`),
  ADD KEY `idx_message_id` (`message_id`);

--
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_name` (`name`),
  ADD KEY `idx_faculty` (`faculty_id`);

--
-- Indexes for table `faculties`
--
ALTER TABLE `faculties`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `idx_name` (`name`),
  ADD KEY `idx_code` (`code`);

--
-- Indexes for table `notices`
--
ALTER TABLE `notices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_publish_date` (`publish_date`),
  ADD KEY `idx_category` (`category_id`),
  ADD KEY `idx_author` (`author_id`),
  ADD KEY `idx_priority` (`priority`),
  ADD KEY `idx_notices_published` (`status`,`publish_date`,`is_pinned`);
ALTER TABLE `notices` ADD FULLTEXT KEY `idx_search` (`title`,`content`);

--
-- Indexes for table `notice_attachments`
--
ALTER TABLE `notice_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_notice` (`notice_id`);

--
-- Indexes for table `notice_audiences`
--
ALTER TABLE `notice_audiences`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_notice` (`notice_id`),
  ADD KEY `idx_audience_type` (`audience_type_id`),
  ADD KEY `idx_faculty` (`faculty_id`),
  ADD KEY `idx_department` (`department_id`),
  ADD KEY `idx_program` (`program_id`),
  ADD KEY `idx_year` (`year_level`),
  ADD KEY `idx_notice_audiences_composite` (`notice_id`,`audience_type_id`,`faculty_id`,`program_id`,`year_level`);

--
-- Indexes for table `notice_comments`
--
ALTER TABLE `notice_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_notice` (`notice_id`),
  ADD KEY `idx_user` (`user_id`);

--
-- Indexes for table `notice_likes`
--
ALTER TABLE `notice_likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_like` (`notice_id`,`user_id`),
  ADD KEY `idx_notice` (`notice_id`),
  ADD KEY `idx_user` (`user_id`);

--
-- Indexes for table `online_users`
--
ALTER TABLE `online_users`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `post_read_status`
--
ALTER TABLE `post_read_status`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_post` (`user_id`,`notice_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_notice` (`notice_id`),
  ADD KEY `idx_read_at` (`read_at`);

--
-- Indexes for table `programs`
--
ALTER TABLE `programs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_name` (`name`),
  ADD KEY `idx_department` (`department_id`),
  ADD KEY `idx_faculty` (`faculty_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_role` (`role`),
  ADD KEY `fk_users_faculty` (`faculty_id`),
  ADD KEY `fk_users_department` (`department_id`),
  ADD KEY `fk_users_program` (`program_id`),
  ADD KEY `idx_users_composite` (`role`,`faculty_id`,`program_id`,`year_of_study`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `audience_types`
--
ALTER TABLE `audience_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `chat_likes`
--
ALTER TABLE `chat_likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `faculties`
--
ALTER TABLE `faculties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `notices`
--
ALTER TABLE `notices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `notice_attachments`
--
ALTER TABLE `notice_attachments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `notice_audiences`
--
ALTER TABLE `notice_audiences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `notice_comments`
--
ALTER TABLE `notice_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `notice_likes`
--
ALTER TABLE `notice_likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `post_read_status`
--
ALTER TABLE `post_read_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `programs`
--
ALTER TABLE `programs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `departments`
--
ALTER TABLE `departments`
  ADD CONSTRAINT `departments_ibfk_1` FOREIGN KEY (`faculty_id`) REFERENCES `faculties` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notices`
--
ALTER TABLE `notices`
  ADD CONSTRAINT `notices_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notices_ibfk_2` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notice_attachments`
--
ALTER TABLE `notice_attachments`
  ADD CONSTRAINT `notice_attachments_ibfk_1` FOREIGN KEY (`notice_id`) REFERENCES `notices` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notice_audiences`
--
ALTER TABLE `notice_audiences`
  ADD CONSTRAINT `notice_audiences_ibfk_1` FOREIGN KEY (`notice_id`) REFERENCES `notices` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notice_audiences_ibfk_2` FOREIGN KEY (`audience_type_id`) REFERENCES `audience_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notice_audiences_ibfk_3` FOREIGN KEY (`faculty_id`) REFERENCES `faculties` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notice_audiences_ibfk_4` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notice_audiences_ibfk_5` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notice_comments`
--
ALTER TABLE `notice_comments`
  ADD CONSTRAINT `notice_comments_ibfk_1` FOREIGN KEY (`notice_id`) REFERENCES `notices` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notice_comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notice_likes`
--
ALTER TABLE `notice_likes`
  ADD CONSTRAINT `notice_likes_ibfk_1` FOREIGN KEY (`notice_id`) REFERENCES `notices` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notice_likes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `post_read_status`
--
ALTER TABLE `post_read_status`
  ADD CONSTRAINT `post_read_status_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `post_read_status_ibfk_2` FOREIGN KEY (`notice_id`) REFERENCES `notices` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `programs`
--
ALTER TABLE `programs`
  ADD CONSTRAINT `programs_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `programs_ibfk_2` FOREIGN KEY (`faculty_id`) REFERENCES `faculties` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_users_faculty` FOREIGN KEY (`faculty_id`) REFERENCES `faculties` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_users_program` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
