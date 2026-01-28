-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 28, 2026 at 09:30 AM
-- Server version: 10.1.37-MariaDB
-- PHP Version: 7.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sch_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `email_api_settings`
--

CREATE TABLE `email_api_settings` (
  `id` int(11) NOT NULL,
  `host` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `port` int(6) DEFAULT '465',
  `encryption` varchar(10) DEFAULT 'ssl',
  `from_email` varchar(255) DEFAULT NULL,
  `from_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `email_api_settings`
--

INSERT INTO `email_api_settings` (`id`, `host`, `username`, `password`, `port`, `encryption`, `from_email`, `from_name`) VALUES
(1, 'smtp.gmail.com', 'testemail@gmail.com', 'testpassword123', 587, 'tls', 'testemail@gmail.com', 'Climax Academy'),
(2, 'smtp.mailtrap.io', 'mailtrap_user', 'mailtrap_pass', 2525, 'tls', 'noreply@mgtechs.com.ng', 'MGTECHS SMART INNOVATIONS');

-- --------------------------------------------------------

--
-- Table structure for table `enquiries`
--

CREATE TABLE `enquiries` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `sender_name` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `message` text,
  `file` varchar(255) DEFAULT NULL,
  `alt_contact` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Pending',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sms_api_settings`
--

CREATE TABLE `sms_api_settings` (
  `id` int(11) NOT NULL,
  `api_name` varchar(100) DEFAULT 'Infobip',
  `base_url` varchar(255) NOT NULL,
  `api_key` varchar(255) NOT NULL,
  `sender_id` varchar(50) DEFAULT NULL,
  `environment` enum('test','live') DEFAULT 'test',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `sms_api_settings`
--

INSERT INTO `sms_api_settings` (`id`, `api_name`, `base_url`, `api_key`, `sender_id`, `environment`, `created_at`) VALUES
(1, 'Termii SMS API', 'https://api.ng.termii.com/api/sms/send', 'TEST_API_KEY_12345', 'MGTECHS', '', '2025-11-05 18:12:57'),
(2, 'Twilio SMS API', 'https://api.twilio.com/2025-11-05/sms/send', 'TWILIO_TEST_KEY_98765', 'CLIMAX', '', '2025-11-05 18:12:57');

-- --------------------------------------------------------

--
-- Table structure for table `staff_role`
--

CREATE TABLE `staff_role` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `staff_role`
--

INSERT INTO `staff_role` (`id`, `name`, `description`) VALUES
(1, 'Teacher', 'Responsible for classroom instruction'),
(2, 'Accountant', 'Handles financial records and salary processing'),
(3, 'Clerk', 'Maintains administrative records'),
(4, 'Principal', 'Oversees academic and administrative operations');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_academic_calendar`
--

CREATE TABLE `tbl_academic_calendar` (
  `id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `activity` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `num_days` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_academic_calendar`
--

INSERT INTO `tbl_academic_calendar` (`id`, `session_id`, `activity`, `start_date`, `end_date`, `num_days`, `created_at`, `updated_at`) VALUES
(1, 1, 'test', '2025-11-11', '2025-11-29', 19, '2025-10-21 00:21:46', '2025-10-21 00:21:46'),
(2, 1, 'Exams', '2025-12-11', '2025-01-29', -315, '2025-10-21 00:25:29', '2025-10-21 00:25:29'),
(3, 1, 'orientation', '2025-12-11', '2025-12-29', 19, '2025-10-21 00:27:30', '2025-10-21 00:27:30');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_announcements`
--

CREATE TABLE `tbl_announcements` (
  `id` int(11) NOT NULL,
  `title` varchar(90) NOT NULL,
  `announcement` longtext NOT NULL,
  `create_date` datetime NOT NULL,
  `level` int(11) NOT NULL COMMENT '0 = Teachers, 1 = Student, 2 = Both'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_announcements`
--

INSERT INTO `tbl_announcements` (`id`, `title`, `announcement`, `create_date`, `level`) VALUES
(1, 'MGTECHS TEST ANNOUCEMENT ', '<p>MGTECHS TEST ANNOUCEMENT&nbsp;&nbsp;MGTECHS TEST ANNOUCEMENT&nbsp;&nbsp;MGTECHS TEST ANNOUCEMENT&nbsp;&nbsp;MGTECHS TEST ANNOUCEMENT&nbsp;&nbsp;MGTECHS TEST ANNOUCEMENT&nbsp;</p><p>MGTECHS TEST ANNOUCEMENT&nbsp;&nbsp;MGTECHS TEST ANNOUCEMENT&nbsp;</p>', '2025-10-18 01:54:30', 2);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_applications`
--

CREATE TABLE `tbl_applications` (
  `id` int(11) NOT NULL,
  `passport` varchar(255) NOT NULL,
  `session_id` int(11) NOT NULL,
  `term_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `number` varchar(50) NOT NULL,
  `address` text NOT NULL,
  `gender` varchar(10) NOT NULL,
  `dob` date NOT NULL,
  `pob` varchar(150) NOT NULL,
  `lga` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `nationality` varchar(100) NOT NULL,
  `previous_school` varchar(150) DEFAULT NULL,
  `previous_class` varchar(100) DEFAULT NULL,
  `report_card` varchar(255) DEFAULT NULL,
  `class_applying_id` int(11) DEFAULT NULL,
  `category` enum('DAY','BOARDING') DEFAULT 'DAY',
  `parent_name` varchar(150) DEFAULT NULL,
  `parent_email` varchar(150) DEFAULT NULL,
  `parent_number` varchar(50) DEFAULT NULL,
  `parent_address` text,
  `status` enum('Not Admitted','Admitted') DEFAULT 'Not Admitted',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_application_settings`
--

CREATE TABLE `tbl_application_settings` (
  `id` int(11) NOT NULL,
  `type` enum('student','staff') NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `application_fee` decimal(10,2) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'inactive',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_category`
--

CREATE TABLE `tbl_category` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_classes`
--

CREATE TABLE `tbl_classes` (
  `id` int(11) NOT NULL,
  `name` varchar(90) NOT NULL,
  `registration_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_classes`
--

INSERT INTO `tbl_classes` (`id`, `name`, `registration_date`) VALUES
(3, 'JSS ONE', '2024-03-18 12:41:05'),
(4, 'JSS TWO', '2024-03-18 12:41:20'),
(5, 'JSS THREE', '2024-03-18 12:42:31'),
(6, 'SSS ONE', '2024-03-18 12:42:41'),
(7, 'SSS TWO', '2024-03-18 12:42:53'),
(8, 'SSS THREE', '2024-03-18 12:43:09'),
(9, 'PRIMARY ONE', '2025-10-19 01:36:48'),
(10, 'PRIMARY TWO', '2025-10-20 20:05:06'),
(11, 'PRIMARY THREE', '2025-10-20 20:05:22'),
(12, 'PRIMARY FOUR', '2025-10-20 20:05:39'),
(13, 'PRIMARY FIVE', '2025-10-20 20:05:53'),
(14, 'PRIMARY SIX', '2025-10-20 20:06:06'),
(15, 'NURSARY ONE', '2025-10-20 20:06:20'),
(16, 'NURSARY TWO', '2025-10-20 20:06:35'),
(17, 'NURSARY THREE', '2025-10-20 20:06:55'),
(18, 'PLAY CLASS', '2025-10-20 20:07:32'),
(19, 'ALUMINAI', '2025-10-20 20:07:50');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_class_promotion`
--

CREATE TABLE `tbl_class_promotion` (
  `id` int(10) UNSIGNED NOT NULL,
  `from_class_id` int(11) NOT NULL,
  `to_class_id` int(11) NOT NULL,
  `order` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_class_promotion`
--

INSERT INTO `tbl_class_promotion` (`id`, `from_class_id`, `to_class_id`, `order`) VALUES
(1, 8, 19, 1),
(2, 3, 4, 0),
(3, 4, 5, 0),
(4, 5, 6, 0),
(5, 6, 7, 0),
(6, 7, 8, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_division_system`
--

CREATE TABLE `tbl_division_system` (
  `division` varchar(50) NOT NULL,
  `min` int(11) NOT NULL,
  `max` int(11) NOT NULL,
  `min_point` int(11) NOT NULL,
  `max_point` int(11) NOT NULL,
  `points` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_division_system`
--

INSERT INTO `tbl_division_system` (`division`, `min`, `max`, `min_point`, `max_point`, `points`) VALUES
('0', 0, 29, 34, 35, 5),
('1', 75, 100, 7, 17, 1),
('2', 65, 74, 18, 21, 2),
('3', 45, 64, 22, 25, 3),
('4', 30, 44, 26, 33, 4);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_exams_questions_settings`
--

CREATE TABLE `tbl_exams_questions_settings` (
  `id` int(11) NOT NULL,
  `allow_submission` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_expenses`
--

CREATE TABLE `tbl_expenses` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `session_id` varchar(50) DEFAULT NULL,
  `term_id` varchar(50) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `amount` decimal(15,2) DEFAULT NULL,
  `balance` decimal(15,2) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_fee_mapping`
--

CREATE TABLE `tbl_fee_mapping` (
  `id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `term_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_fee_mapping`
--

INSERT INTO `tbl_fee_mapping` (`id`, `session_id`, `term_id`, `class_id`, `amount`, `created_at`) VALUES
(1, 1, 1, 5, '15000.00', '2025-11-05 14:50:59');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_grade_system`
--

CREATE TABLE `tbl_grade_system` (
  `id` int(11) NOT NULL,
  `name` varchar(20) NOT NULL,
  `min` double NOT NULL,
  `max` double NOT NULL,
  `remark` varchar(90) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_grade_system`
--

INSERT INTO `tbl_grade_system` (`id`, `name`, `min`, `max`, `remark`) VALUES
(1, 'A', 75, 100, 'Excellent'),
(2, 'B', 65, 74, 'Very Good'),
(3, 'C', 45, 64, 'Good'),
(4, 'D', 30, 44, 'Satisfactory'),
(5, 'F', 0, 29, 'Fail');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_leave_applications`
--

CREATE TABLE `tbl_leave_applications` (
  `id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `purpose` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `returning_date` date NOT NULL,
  `number_of_days` int(11) NOT NULL,
  `status` tinyint(1) DEFAULT '0',
  `approved_message` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_leave_applications`
--

INSERT INTO `tbl_leave_applications` (`id`, `staff_id`, `user_name`, `purpose`, `start_date`, `returning_date`, `number_of_days`, `status`, `approved_message`, `created_at`) VALUES
(1, 24, 'Maxwell Halilu', 'Health Issue', '2025-11-17', '2025-11-28', 12, 0, NULL, '2025-11-16 16:05:39');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_loan_applications`
--

CREATE TABLE `tbl_loan_applications` (
  `id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `loan_amount` decimal(12,2) NOT NULL,
  `repayment_date` date NOT NULL,
  `approved_amount` decimal(12,2) DEFAULT NULL,
  `status` enum('Pending','Approved') DEFAULT 'Pending',
  `repayment_status` enum('In Progress','Paid') DEFAULT 'In Progress',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_loan_applications`
--

INSERT INTO `tbl_loan_applications` (`id`, `staff_id`, `loan_amount`, `repayment_date`, `approved_amount`, `status`, `repayment_status`, `created_at`) VALUES
(1, 1, '5000.00', '2025-12-05', '3000.00', 'Approved', 'In Progress', '2025-11-05 15:24:23'),
(2, 24, '50000.00', '2025-11-10', NULL, '', '', '2025-11-08 17:19:25');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_login_sessions`
--

CREATE TABLE `tbl_login_sessions` (
  `session_key` varchar(90) NOT NULL,
  `staff` int(11) DEFAULT NULL,
  `student` varchar(20) DEFAULT NULL,
  `ip_address` varchar(90) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_login_sessions`
--

INSERT INTO `tbl_login_sessions` (`session_key`, `staff`, `student`, `ip_address`) VALUES
('7S5P497USTTDDT7ESYL4', 23, NULL, '::1');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_paystack_api_settings`
--

CREATE TABLE `tbl_paystack_api_settings` (
  `id` int(11) NOT NULL,
  `api_name` varchar(100) NOT NULL,
  `api_secret_key` text NOT NULL,
  `api_public_key` text NOT NULL,
  `environment` enum('live','test') DEFAULT 'test',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_paystack_api_settings`
--

INSERT INTO `tbl_paystack_api_settings` (`id`, `api_name`, `api_secret_key`, `api_public_key`, `environment`, `created_at`) VALUES
(1, 'Paystack', 'sk_test_0425ea87a642296b67e02d948ce995f8c5cd21eb', 'pk_test_58c55b020966a8a5a1393f60b0ad5b22378eebdc', 'test', '2025-10-21 00:45:36');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_promotions`
--

CREATE TABLE `tbl_promotions` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `student_name` varchar(255) NOT NULL,
  `from_class_id` int(11) NOT NULL,
  `to_class_id` int(11) NOT NULL,
  `from_session_id` int(11) NOT NULL,
  `to_session_id` int(11) NOT NULL,
  `promoted_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_promotions`
--

INSERT INTO `tbl_promotions` (`id`, `student_id`, `student_name`, `from_class_id`, `to_class_id`, `from_session_id`, `to_session_id`, `promoted_at`) VALUES
(1, 0, 'OSWARD JORAM SEBATWALE', 4, 5, 1, 4, '2025-10-20 04:38:38'),
(2, 0, 'PAULO W MOSHI', 4, 5, 1, 4, '2025-10-20 04:38:38'),
(3, 0, 'REHEMA JAMES MUSSA', 4, 5, 1, 4, '2025-10-20 04:38:38'),
(4, 0, 'TUMSIFU ALFRED KAMALA', 4, 5, 1, 4, '2025-10-20 04:38:39'),
(5, 0, 'YUSTINO EZRAEL MBIGO', 4, 5, 1, 4, '2025-10-20 04:38:39'),
(6, 0, 'ALICE M MUGISHA', 4, 5, 1, 4, '2025-10-20 04:38:39'),
(7, 0, 'ALLY ZUBERI ALLY', 4, 5, 1, 4, '2025-10-20 04:38:39'),
(8, 0, 'ASHRAF NASSOR SAID', 4, 5, 1, 4, '2025-10-20 04:38:40'),
(9, 0, 'BONIFACE PONTIAN MUTEGEYA', 4, 5, 1, 4, '2025-10-20 04:38:40'),
(10, 0, 'BRIAN ELIWAHA TOMITE', 4, 5, 1, 4, '2025-10-20 04:38:40'),
(11, 0, 'CHARLES THADEY NDUVA', 4, 5, 1, 4, '2025-10-20 04:38:40'),
(12, 0, 'QUEEN  JULIUS BENJAMIN', 5, 6, 1, 4, '2025-10-20 04:46:07'),
(13, 0, 'RAJABU M MILANZI', 5, 6, 1, 4, '2025-10-20 04:46:07'),
(14, 0, 'REHEMA SILIVESTER LEMABI', 5, 6, 1, 4, '2025-10-20 04:46:08'),
(15, 0, 'SHAIBU RASHIDI MPONDA', 5, 6, 1, 4, '2025-10-20 04:46:08'),
(16, 0, 'UMMUKULTHUM  BAKARI PANGO', 5, 6, 1, 4, '2025-10-20 04:46:08'),
(17, 11233, 'Maxwell Ephraim Halilu', 6, 7, 1, 4, '2025-10-20 04:49:35'),
(18, 0, 'ANDREW ISAAC MABIKI', 6, 7, 1, 4, '2025-10-20 04:49:35'),
(19, 0, 'BRYSON KHAMIS MKHANY', 6, 7, 1, 4, '2025-10-20 04:49:35'),
(20, 0, 'EMMNAUEL EMMNAUEL JOSEPH', 6, 7, 1, 4, '2025-10-20 04:49:35'),
(21, 0, 'FRANCO FRANCO MLAWA', 6, 7, 1, 4, '2025-10-20 04:49:36'),
(22, 0, 'MICHAEL GABRIEL NDEKWA', 6, 7, 1, 4, '2025-10-20 04:49:36'),
(23, 0, 'NYEMO WILFRED SENHYINA', 6, 7, 1, 4, '2025-10-20 04:49:36'),
(24, 0, 'RAMADHANI JUMA KIFUNTA', 6, 7, 1, 4, '2025-10-20 04:49:36'),
(25, 0, 'SAIDA ABDULQADIR MOHAMED', 6, 7, 1, 4, '2025-10-20 04:49:36'),
(26, 0, 'SALMA FADHIL MGANGA', 6, 7, 1, 4, '2025-10-20 04:49:36');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_results`
--

CREATE TABLE `tbl_results` (
  `id` int(11) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `class_id` int(11) NOT NULL COMMENT 'FK to tbl_classes.id',
  `subject_id` int(11) NOT NULL COMMENT 'FK to tbl_subjects.id',
  `session_id` int(11) NOT NULL COMMENT 'FK to tbl_sessions.id',
  `term_id` int(11) NOT NULL COMMENT 'FK to tbl_terms.id',
  `first_test` decimal(5,2) DEFAULT '0.00',
  `second_test` decimal(5,2) DEFAULT '0.00',
  `third_test` decimal(5,2) DEFAULT '0.00',
  `exam_score` decimal(5,2) DEFAULT '0.00' COMMENT 'Examination Score',
  `total_score` decimal(5,2) DEFAULT '0.00' COMMENT 'Total = CA + Exam',
  `grade` varchar(5) DEFAULT NULL,
  `remark` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_results`
--

INSERT INTO `tbl_results` (`id`, `student_id`, `class_id`, `subject_id`, `session_id`, `term_id`, `first_test`, `second_test`, `third_test`, `exam_score`, `total_score`, `grade`, `remark`, `created_at`, `updated_at`) VALUES
(45, 'RG007', 5, 8, 1, 1, '6.00', '9.00', '9.00', '30.00', '54.00', 'C', 'Good', '2025-11-04 13:22:37', '2025-11-15 08:07:48'),
(46, 'RG004', 5, 8, 1, 1, '9.00', '9.00', '9.00', '9.00', '36.00', 'D', 'Satisfactory', '2025-11-04 13:22:37', '2025-11-15 08:07:48'),
(47, 'RG005', 5, 8, 1, 1, '9.00', '9.00', '9.00', '9.00', '36.00', 'D', 'Satisfactory', '2025-11-04 13:22:37', '2025-11-15 08:07:48'),
(48, 'RG002', 5, 8, 1, 1, '9.00', '9.00', '9.00', '9.00', '36.00', 'D', 'Satisfactory', '2025-11-04 13:22:38', '2025-11-15 08:07:48'),
(49, 'RG006', 5, 8, 1, 1, '9.00', '9.00', '9.00', '9.00', '36.00', 'D', 'Satisfactory', '2025-11-04 13:22:38', '2025-11-15 08:07:48'),
(50, 'RG003', 5, 8, 1, 1, '9.00', '9.00', '9.00', '9.00', '36.00', 'D', 'Satisfactory', '2025-11-04 13:22:38', '2025-11-15 08:07:48'),
(51, 'RG009', 5, 8, 1, 1, '9.00', '9.00', '9.00', '9.00', '36.00', 'D', 'Satisfactory', '2025-11-04 13:22:38', '2025-11-15 08:07:49'),
(52, 'RG011', 5, 8, 1, 1, '9.00', '9.00', '9.00', '9.00', '36.00', 'D', 'Satisfactory', '2025-11-04 13:22:38', '2025-11-15 08:07:49'),
(53, 'RG008', 5, 8, 1, 1, '9.00', '9.00', '9.00', '9.00', '36.00', 'D', 'Satisfactory', '2025-11-04 13:22:38', '2025-11-15 08:07:49'),
(54, 'RG001', 5, 8, 1, 1, '9.00', '9.00', '9.00', '60.00', '87.00', 'A', 'Excellent', '2025-11-04 13:22:39', '2025-11-08 16:49:22'),
(55, 'RG010', 5, 8, 1, 1, '9.00', '9.00', '9.00', '9.00', '36.00', 'D', 'Satisfactory', '2025-11-04 13:22:39', '2025-11-04 13:22:39'),
(56, 'RG007', 5, 11, 1, 1, '8.00', '8.00', '8.00', '8.00', '32.00', 'D', 'Satisfactory', '2025-11-04 13:24:29', '2025-11-04 13:24:29'),
(57, 'RG004', 5, 11, 1, 1, '8.00', '8.00', '8.00', '8.00', '32.00', 'D', 'Satisfactory', '2025-11-04 13:24:29', '2025-11-05 12:19:54'),
(58, 'RG005', 5, 11, 1, 1, '8.00', '8.00', '8.00', '8.00', '32.00', 'D', 'Satisfactory', '2025-11-04 13:24:29', '2025-11-04 13:24:29'),
(59, 'RG002', 5, 11, 1, 1, '8.00', '8.00', '8.00', '8.00', '32.00', 'D', 'Satisfactory', '2025-11-04 13:24:29', '2025-11-04 13:24:29'),
(60, 'RG006', 5, 11, 1, 1, '8.00', '8.00', '8.00', '8.00', '32.00', 'D', 'Satisfactory', '2025-11-04 13:24:29', '2025-11-04 13:24:29'),
(61, 'RG003', 5, 11, 1, 1, '8.00', '8.00', '8.00', '8.00', '32.00', 'D', 'Satisfactory', '2025-11-04 13:24:29', '2025-11-04 13:24:29'),
(62, 'RG009', 5, 11, 1, 1, '8.00', '8.00', '8.00', '8.00', '32.00', 'D', 'Satisfactory', '2025-11-04 13:24:29', '2025-11-04 13:24:29'),
(63, 'RG011', 5, 11, 1, 1, '8.00', '8.00', '8.00', '8.00', '32.00', 'D', 'Satisfactory', '2025-11-04 13:24:30', '2025-11-04 13:24:30'),
(64, 'RG008', 5, 11, 1, 1, '8.00', '8.00', '8.00', '8.00', '32.00', 'D', 'Satisfactory', '2025-11-04 13:24:30', '2025-11-04 13:24:30'),
(65, 'RG001', 5, 11, 1, 1, '8.00', '8.00', '8.00', '56.00', '80.00', 'D', 'Satisfactory', '2025-11-04 13:24:30', '2025-11-05 12:13:34'),
(66, 'RG010', 5, 11, 1, 1, '8.00', '8.00', '8.00', '8.00', '32.00', 'D', 'Satisfactory', '2025-11-04 13:24:30', '2025-11-04 13:24:30'),
(67, 'RG001', 5, 7, 1, 1, '10.00', '7.00', '9.00', '68.00', '94.00', NULL, NULL, '2025-11-05 12:13:33', '2025-11-05 12:13:33'),
(68, 'RG004', 5, 7, 1, 1, '6.00', '4.00', '8.00', '30.00', '48.00', NULL, NULL, '2025-11-05 12:19:54', '2025-11-05 12:19:54'),
(69, '11233', 5, 8, 1, 1, '10.00', '10.00', '9.00', '68.00', '97.00', 'A', 'Excellent', '2025-11-12 15:12:28', '2025-12-22 22:05:35'),
(70, '11233', 5, 7, 1, 1, '7.00', '8.00', '4.00', '60.00', '79.00', NULL, NULL, '2025-12-10 13:37:04', '2025-12-22 22:05:35'),
(71, '11233', 5, 11, 1, 1, '5.00', '8.00', '6.00', '67.00', '86.00', NULL, NULL, '2025-12-10 13:37:05', '2025-12-22 22:05:35'),
(72, '11233', 5, 3, 1, 1, '4.00', '0.00', '0.00', '0.00', '4.00', NULL, NULL, '2025-12-22 22:04:53', '2025-12-22 22:05:34'),
(73, '11233', 5, 4, 1, 1, '0.00', '0.00', '6.00', '0.00', '6.00', NULL, NULL, '2025-12-22 22:04:53', '2025-12-22 22:05:35'),
(74, '11233', 5, 5, 1, 1, '0.00', '5.00', '0.00', '0.00', '5.00', NULL, NULL, '2025-12-22 22:04:53', '2025-12-22 22:05:35'),
(75, '11233', 5, 6, 1, 1, '0.00', '0.00', '6.00', '0.00', '6.00', NULL, NULL, '2025-12-22 22:04:53', '2025-12-22 22:05:35'),
(76, '11233', 5, 9, 1, 1, '0.00', '4.00', '0.00', '0.00', '4.00', NULL, NULL, '2025-12-22 22:04:54', '2025-12-22 22:05:35'),
(77, '11233', 5, 10, 1, 1, '0.00', '5.00', '0.00', '0.00', '5.00', NULL, NULL, '2025-12-22 22:04:54', '2025-12-22 22:05:35'),
(78, '11233', 5, 12, 1, 1, '0.00', '5.00', '0.00', '0.00', '5.00', NULL, NULL, '2025-12-22 22:04:54', '2025-12-22 22:05:36');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_salary_mapping`
--

CREATE TABLE `tbl_salary_mapping` (
  `id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `qualification` varchar(100) NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `employment_type` enum('Full Time','Part Time') NOT NULL,
  `account_number` varchar(50) NOT NULL,
  `bank_name` varchar(100) NOT NULL,
  `bank_code` varchar(20) NOT NULL,
  `salary_amount` decimal(12,2) NOT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_salary_mapping`
--

INSERT INTO `tbl_salary_mapping` (`id`, `staff_id`, `qualification`, `role_id`, `employment_type`, `account_number`, `bank_name`, `bank_code`, `salary_amount`, `status`, `created_at`) VALUES
(1, 24, 'HND', 2, 'Full Time', '7088916857', 'Opay MFB', '383646', '30520.00', 'Active', '2025-11-05 14:49:13'),
(2, 24, 'BSc', 3, 'Full Time', '1234567890', 'Opay MFB', '383646', '7777.00', 'Inactive', '2025-11-12 20:03:49');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_school`
--

CREATE TABLE `tbl_school` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `logo` varchar(50) NOT NULL,
  `result_system` int(11) NOT NULL COMMENT '0 = Average, 1 = Division',
  `allow_results` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_school`
--

INSERT INTO `tbl_school` (`id`, `name`, `logo`, `result_system`, `allow_results`) VALUES
(1, 'MGTECHS ACADEMY', 'school_logo1760870308.png', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_score_entry_settings`
--

CREATE TABLE `tbl_score_entry_settings` (
  `id` int(11) UNSIGNED NOT NULL,
  `session_id` int(11) NOT NULL,
  `term_id` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 = Disabled, 1 = Enabled',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_score_entry_settings`
--

INSERT INTO `tbl_score_entry_settings` (`id`, `session_id`, `term_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, '2025-11-12 16:11:28', '2025-11-15 09:06:20');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_sessions`
--

CREATE TABLE `tbl_sessions` (
  `id` int(11) NOT NULL,
  `session_name` varchar(50) NOT NULL,
  `is_active` tinyint(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_sessions`
--

INSERT INTO `tbl_sessions` (`id`, `session_name`, `is_active`, `created_at`) VALUES
(1, '2025/2026', 1, '2025-10-18 23:39:19'),
(2, '2024/2025', 0, '2025-10-18 23:39:19'),
(3, '2023/2024', 0, '2025-10-18 23:39:19'),
(4, '2026/2027', 0, '2025-10-20 01:09:05');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_site_settings`
--

CREATE TABLE `tbl_site_settings` (
  `id` int(11) NOT NULL,
  `site_name` varchar(200) NOT NULL,
  `site_code` varchar(50) NOT NULL,
  `website` varchar(200) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `facebook_url` varchar(255) DEFAULT NULL,
  `x_url` varchar(255) DEFAULT NULL,
  `whatsapp_url` varchar(255) DEFAULT NULL,
  `linkedin_url` varchar(255) DEFAULT NULL,
  `instagram_url` varchar(255) DEFAULT NULL,
  `account_name` varchar(200) DEFAULT NULL,
  `bank_name` varchar(200) DEFAULT NULL,
  `account_number` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_site_settings`
--

INSERT INTO `tbl_site_settings` (`id`, `site_name`, `site_code`, `website`, `address`, `email`, `phone`, `facebook_url`, `x_url`, `whatsapp_url`, `linkedin_url`, `instagram_url`, `account_name`, `bank_name`, `account_number`, `created_at`, `updated_at`) VALUES
(1, 'MGTechs international Academy', 'MGT', 'https://mgtechs.com.ng', 'Song, Yola Nigeria', 'mgtechs09@gmail.com', '07088916857', 'https://facebook.com/yourpage', 'https://x.com', 'https://flinkdin.com', 'https://linkedin.com/company/yourpage', 'https://instagram.com/yourpage', 'MGTechs  Intanational Academy', 'FirstBank', '7088916857', '2025-11-14 13:02:05', '2025-11-14 13:02:05');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_smtp`
--

CREATE TABLE `tbl_smtp` (
  `id` int(11) NOT NULL,
  `server` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `port` varchar(255) NOT NULL,
  `encryption` varchar(255) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_smtp`
--

INSERT INTO `tbl_smtp` (`id`, `server`, `username`, `password`, `port`, `encryption`, `status`) VALUES
(1, 'smtp server here', 'smtp username here', 'smtp password here', '587', 'tls', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_staff`
--

CREATE TABLE `tbl_staff` (
  `id` int(11) NOT NULL,
  `fname` varchar(20) NOT NULL,
  `lname` varchar(20) NOT NULL,
  `gender` varchar(6) NOT NULL,
  `email` varchar(90) NOT NULL,
  `password` varchar(90) NOT NULL,
  `level` int(11) NOT NULL DEFAULT '0' COMMENT '0 = Admin, 1 = Academic, 2 = Teacher, 4 = Accountant, 5 = Front Desk, 6 = Library',
  `status` int(11) NOT NULL DEFAULT '1' COMMENT '0 = Blocked, 1 = Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_staff`
--

INSERT INTO `tbl_staff` (`id`, `fname`, `lname`, `gender`, `email`, `password`, `level`, `status`) VALUES
(1, 'MG', 'Techs', 'Male', 'bmashauri704@gmail.com', '$2y$10$l8XYJDrBHTyeZkpupiRhwey6jJihzku0bYXiVtBM5kDRz3sZvSpgC', 0, 1),
(3, 'ABDUL', 'SHABAN', 'Male', 'abdul@srms.test', '$2y$10$l8XYJDrBHTyeZkpupiRhwey6jJihzku0bYXiVtBM5kDRz3sZvSpgC', 2, 1),
(4, 'COLLINS', 'MPAGAMA', 'Male', 'collins@srms.test', '$2y$10$l8XYJDrBHTyeZkpupiRhwey6jJihzku0bYXiVtBM5kDRz3sZvSpgC', 2, 1),
(5, 'DAVID', 'OMAO', 'Male', 'david@srms.test', '$2y$10$l8XYJDrBHTyeZkpupiRhwey6jJihzku0bYXiVtBM5kDRz3sZvSpgC', 2, 1),
(6, 'DENIS', 'MWAMBUNGU', 'Male', 'denis@srms.test', '$2y$10$l8XYJDrBHTyeZkpupiRhwey6jJihzku0bYXiVtBM5kDRz3sZvSpgC', 2, 1),
(7, 'ERICK', 'LUOGA', 'Male', 'erick@srms.test', '$2y$10$l8XYJDrBHTyeZkpupiRhwey6jJihzku0bYXiVtBM5kDRz3sZvSpgC', 2, 1),
(8, 'FARAJI', 'FARAJI', 'Male', 'faraji@srms.test', '$2y$10$l8XYJDrBHTyeZkpupiRhwey6jJihzku0bYXiVtBM5kDRz3sZvSpgC', 2, 1),
(9, 'FATMA', 'BAHADAD', 'Female', 'fatma@srms.test', '$2y$10$l8XYJDrBHTyeZkpupiRhwey6jJihzku0bYXiVtBM5kDRz3sZvSpgC', 2, 1),
(10, 'FRANCIS', 'MASANJA', 'Male', 'francis@srms.test', '$2y$10$l8XYJDrBHTyeZkpupiRhwey6jJihzku0bYXiVtBM5kDRz3sZvSpgC', 2, 1),
(11, 'GLADNESS ', 'PHILIPO', 'Female', 'gladness@srms.test', '$2y$10$l8XYJDrBHTyeZkpupiRhwey6jJihzku0bYXiVtBM5kDRz3sZvSpgC', 2, 1),
(12, 'GRATION', 'GRATION', 'Male', 'gration@srms.test', '$2y$10$l8XYJDrBHTyeZkpupiRhwey6jJihzku0bYXiVtBM5kDRz3sZvSpgC', 2, 1),
(13, 'HANS', 'UISSO', 'Male', 'hans@srms.test', '$2y$10$l8XYJDrBHTyeZkpupiRhwey6jJihzku0bYXiVtBM5kDRz3sZvSpgC', 2, 1),
(14, 'HANSON', 'MAITA', 'Male', 'hanson@srms.test', '$2y$10$l8XYJDrBHTyeZkpupiRhwey6jJihzku0bYXiVtBM5kDRz3sZvSpgC', 2, 1),
(15, 'HENRY', 'GOWELLE', 'Male', 'henry@srms.test', '$2y$10$l8XYJDrBHTyeZkpupiRhwey6jJihzku0bYXiVtBM5kDRz3sZvSpgC', 2, 1),
(16, 'HILDA', 'KANDAUMA', 'Female', 'hilda@srms.test', '$2y$10$l8XYJDrBHTyeZkpupiRhwey6jJihzku0bYXiVtBM5kDRz3sZvSpgC', 2, 1),
(17, 'INNOCENT', 'MBAWALA', 'Male', 'innocent@srms.test', '$2y$10$l8XYJDrBHTyeZkpupiRhwey6jJihzku0bYXiVtBM5kDRz3sZvSpgC', 2, 1),
(18, 'JAMALI', 'NZOTA', 'Male', 'jamali@srms.test', '$2y$10$l8XYJDrBHTyeZkpupiRhwey6jJihzku0bYXiVtBM5kDRz3sZvSpgC', 2, 1),
(19, 'JAMIL', 'ABDALLAH', 'Male', 'jamil@srms.test', '$2y$10$l8XYJDrBHTyeZkpupiRhwey6jJihzku0bYXiVtBM5kDRz3sZvSpgC', 2, 1),
(20, 'JOAN', 'NKYA', 'Female', 'joan@srms.test', '$2y$10$l8XYJDrBHTyeZkpupiRhwey6jJihzku0bYXiVtBM5kDRz3sZvSpgC', 2, 1),
(21, 'JOSEPH', 'HAMISI', 'Male', 'joseph@srms.test', '$2y$10$l8XYJDrBHTyeZkpupiRhwey6jJihzku0bYXiVtBM5kDRz3sZvSpgC', 2, 1),
(23, 'MG', 'Tech', 'Male', 'bwiremunyweki@gmail.com', '$2y$10$l8XYJDrBHTyeZkpupiRhwey6jJihzku0bYXiVtBM5kDRz3sZvSpgC', 1, 1),
(24, 'Maxwell', 'Halilu', 'Male', 'mgtechs09@gmail.com', '$2y$10$d7Np736DqXHyXdU1M3KT1ukk5w/kSulBWJohmUYe2mYgoxbNvYeMG', 2, 1),
(25, 'Alice', 'Okoro', 'Female', 'alice.accountant@example.com', '482c811da5d5b4bc6d497ffa98491e38', 6, 1),
(26, 'Ben', 'Umar', 'Male', 'frontdesk@example.com', '$2y$10$l8XYJDrBHTyeZkpupiRhwey6jJihzku0bYXiVtBM5kDRz3sZvSpgC', 4, 1),
(27, 'Clara', 'Eze', 'Female', 'clara.library@example.com', '482c811da5d5b4bc6d497ffa98491e38', 5, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_staff_applications`
--

CREATE TABLE `tbl_staff_applications` (
  `id` int(11) NOT NULL,
  `passport` varchar(255) DEFAULT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `number` varchar(50) NOT NULL,
  `address` text,
  `gender` varchar(20) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `place_of_birth` varchar(150) DEFAULT NULL,
  `marital_status` varchar(50) DEFAULT NULL,
  `qualification` varchar(50) DEFAULT NULL,
  `certificate` varchar(255) DEFAULT NULL,
  `subjects` text,
  `category` enum('PART TIME','FULL TIME') DEFAULT 'PART TIME',
  `status` enum('Pending','Approved') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_students`
--

CREATE TABLE `tbl_students` (
  `id` varchar(20) NOT NULL,
  `fname` varchar(70) NOT NULL,
  `mname` varchar(70) NOT NULL,
  `lname` varchar(70) NOT NULL,
  `gender` varchar(7) NOT NULL,
  `email` varchar(90) NOT NULL,
  `class` int(11) NOT NULL,
  `password` varchar(90) NOT NULL,
  `level` int(11) NOT NULL DEFAULT '3' COMMENT '3 = student',
  `display_image` varchar(50) NOT NULL DEFAULT 'Blank',
  `status` int(11) NOT NULL DEFAULT '1' COMMENT '0 = Disabled, 1 = Enabled',
  `session_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_students`
--

INSERT INTO `tbl_students` (`id`, `fname`, `mname`, `lname`, `gender`, `email`, `class`, `password`, `level`, `display_image`, `status`, `session_id`, `created_at`) VALUES
('111', 'JOHNSEN', 'J', 'JOHNSEN', 'Male', 'J@J.COM', 9, '$2y$10$4CAvkJOjJfQUJvm81Df3yOGYdo2SQLtf9ZG5gWNBgwYPkcvGbYaUO', 3, 'DEFAULT', 1, 1, '2025-11-24 01:53:41'),
('11233', 'Maxwell', 'Ephraim', 'Halilu', 'Male', 'mgtechs091@gmail.com', 5, '$2y$10$.Q80zLNdra0n.ajzqHLYZ.V.MXpbw4VwH2ovffwuczFiB4VcWlVlO', 3, 'DEFAULT', 1, 4, '2025-11-24 01:53:41'),
('44T', 'RRRRRR', 'RRRRR', 'RRRRRRR', 'Male', 'R@R.COM', 5, '$2y$10$bqSrqRqUetDglp.buTmA3.NLwGPgFKLBDDl2x45nwkcv0TpM5EjqG', 3, 'DEFAULT', 1, NULL, '2025-11-24 01:53:41'),
('RG001', 'OSWARD', 'JORAM', 'SEBATWALE', 'Male', 'oswardj@srms.test', 5, '$2y$10$XIE1yTXiRYWKrW7.e.OjGeYy.B9guq/sLh9rqu47YO1/QR1ZX93VW', 3, 'avator_1710936891.jpg', 1, 4, '2025-11-24 01:53:41'),
('RG002', 'PAULO', 'W', 'MOSHI', 'Male', 'paulow@srms.test', 5, '$2y$10$RN3P.rFnGYY2eZaLR6wOge8yJByl1Fjm3TbzCU0S/ZVBconH0fNo.', 3, 'avator_1710936905.jpg', 1, 4, '2025-11-24 01:53:41'),
('RG003', 'REHEMA', 'JAMES', 'MUSSA', 'Female', 'rehemaj@srms.test', 5, '$2y$10$sVdbGNtV2rUa6JvQE6xCOOtvXTxboEYRu7DZ4p/Iw7n3AZiJS/3Ly', 3, 'avator_1710936914.jpg', 1, 4, '2025-11-24 01:53:41'),
('RG004', 'TUMSIFU', 'ALFRED', 'KAMALA', 'Female', 'tumsifua@srms.test', 5, '$2y$10$fyfsYVo8oNrziA9QN9iMHuc5A8M8IGe6o/LSmmUmbBeNnhxNVIRl2', 3, 'avator_1710936926.jpg', 1, 4, '2025-11-24 01:53:41'),
('RG005', 'YUSTINO', 'EZRAEL', 'MBIGO', 'Male', 'yustinoe@srms.test', 5, '$2y$10$PtjCClDQa/ZbyB3fVeVUee2Z7PjEuTt8.haBL0um8qbuqHzwml0MS', 3, 'avator_1710936946.jpg', 1, 4, '2025-11-24 01:53:41'),
('RG006', 'ALICE', 'M', 'MUGISHA', 'Female', 'alice@srms.test', 5, '$2y$10$B1TJ31juU36kicOs2ULaw.tTTWmtcqhgJ2orYxHmjvM7pSfCWJr5C', 3, 'avator_1710936962.jpg', 1, 4, '2025-11-24 01:53:41'),
('RG007', 'ALLY', 'ZUBERI', 'ALLY', 'Male', 'ally@srzuberis.test', 5, '$2y$10$w/iWaMfiDJFOCcpeO0Ffu../ig07nvyFE8PgZl2GJ9.zslriWCyR2', 3, 'DEFAULT', 1, 4, '2025-11-24 01:53:41'),
('RG008', 'ASHRAF', 'NASSOR', 'SAID', 'Male', 'ashraf@srnassors.test', 5, '$2y$10$Rzeb6teMSYV5qEGl5eq9MOvUFrkZ.ZIbi6f1ciG2vCrYUnVFSk7gG', 3, 'DEFAULT', 1, 4, '2025-11-24 01:53:41'),
('RG009', 'BONIFACE', 'PONTIAN', 'MUTEGEYA', 'Male', 'boniface@srpontians.test', 5, '$2y$10$HwNLcEK4Ia8Valv5e/S4z.h9w0XwkZw6d59DvpORj9kuSB613Q1Mq', 3, 'DEFAULT', 1, 4, '2025-11-24 01:53:41'),
('RG010', 'BRIAN', 'ELIWAHA', 'TOMITE', 'Male', 'brian@sreliwahas.test', 5, '$2y$10$SsntilVGwPYs3SigHNijguNIXmGJ/IJAV/cI02U02ASJYdgUfeaf.', 3, 'DEFAULT', 1, 4, '2025-11-24 01:53:41'),
('RG011', 'CHARLES', 'THADEY', 'NDUVA', 'Male', 'charles@srthadeys.test', 5, '$2y$10$/oC2rBI/1kwYvDTFjGaut.DF3s15Tmmmt5vpZrNyDsruv2wvivr6m', 3, 'DEFAULT', 1, 4, '2025-11-24 01:53:41'),
('RG012', 'QUEEN ', 'JULIUS', 'BENJAMIN', 'Female', 'queen@srms.test', 6, '$2y$10$1BtsttroEwKx8Bs4Y.I36uTKJBopsjOStAco3l0JJbD3yoU.Zvs1y', 3, 'DEFAULT', 1, 4, '2025-11-24 01:53:41'),
('RG013', 'RAJABU', 'M', 'MILANZI', 'Male', 'rajabu@srms.test', 6, '$2y$10$jvfxswqLon3PcZvepUM7lOBTCZFShQidjysPvSo.6l/d.5.N2tpDC', 3, 'DEFAULT', 1, 4, '2025-11-24 01:53:41'),
('RG014', 'REHEMA', 'SILIVESTER', 'LEMABI', 'Female', 'rehema@srms.test', 6, '$2y$10$g7aNMemiSOf6j10Z9kGrEuM79Wx3j8Rs22d5bap5rnHvTtD/VRdp.', 3, 'DEFAULT', 1, 4, '2025-11-24 01:53:41'),
('RG015', 'SHAIBU', 'RASHIDI', 'MPONDA', 'Male', 'shaibu@srms.test', 6, '$2y$10$Ic.FhKjvB3dJJtiT0iUbHuRSr.i8qGEZQnDYNCdxEiybaWUjWtP5K', 3, 'DEFAULT', 1, 4, '2025-11-24 01:53:41'),
('RG016', 'UMMUKULTHUM ', 'BAKARI', 'PANGO', 'Female', 'ummukulthum@srms.test', 6, '$2y$10$PdFlObFsViSTLs.yJuxFWus7uF0.TUC16kbaVJGIBwFUbMVkDgTgC', 3, 'DEFAULT', 1, 4, '2025-11-24 01:53:41'),
('RG017', 'ANDREW', 'ISAAC', 'MABIKI', 'Male', 'andrew@srms.test', 6, '$2y$10$7aswGGxLgfkUbfYH/fwj/.WyFnm946Lxk90z9MIG7ZCLWi/k5bZ3.', 3, 'DEFAULT', 1, 1, '2025-11-24 01:53:41'),
('RG018', 'BRYSON', 'KHAMIS', 'MKHANY', 'Male', 'bryson@srms.test', 6, '$2y$10$XxHNLF6LUkq./WsMPKYP/emqMkIQ1aaH9GM/.967RgMpZzLi0a8Gy', 3, 'DEFAULT', 1, 1, '2025-11-24 01:53:41'),
('RG019', 'EMMNAUEL', 'EMMNAUEL', 'JOSEPH', 'Male', 'emmnauel@srms.test', 6, '$2y$10$t35jhCZhHAgJsDobC9gNFuOWvGc9xUPFrVTUBRc1.JT2nVWNzXCni', 3, 'DEFAULT', 1, 1, '2025-11-24 01:53:41'),
('RG020', 'FRANCO', 'FRANCO', 'MLAWA', 'Male', 'franco@srms.test', 6, '$2y$10$pqUi6rYnotgMLODlNRlSpu8rxUTJ.mtfcjUNVq/7mA5037uLFe5zO', 3, 'DEFAULT', 1, 1, '2025-11-24 01:53:41'),
('RG021', 'MICHAEL', 'GABRIEL', 'NDEKWA', 'Male', 'michael@srms.test', 6, '$2y$10$qsHSbSbQGceU8VGKlxptu.1GfwPCH9yoX7c1lKh5qdPGruIua9fTC', 3, 'DEFAULT', 1, 1, '2025-11-24 01:53:41'),
('RG022', 'NYEMO', 'WILFRED', 'SENHYINA', 'Male', 'nyemo@srms.test', 6, '$2y$10$rOhIasvPU8RW0WMqtVXxHOiLW2J66wpbE5NUd0lJBA9GbFb7nAogy', 3, 'DEFAULT', 1, 1, '2025-11-24 01:53:41'),
('RG023', 'RAMADHANI', 'JUMA', 'KIFUNTA', 'Male', 'ramadhani@srms.test', 6, '$2y$10$r81yFW5othiG5iGkKTh6GOOhmrSbhYG.B/Lhv3.hjH6hZ5tYV8E2m', 3, 'DEFAULT', 1, 1, '2025-11-24 01:53:41'),
('RG024', 'SAIDA', 'ABDULQADIR', 'MOHAMED', 'Female', 'saida@srms.test', 6, '$2y$10$Z3U9EU.ywJbQAjgtN2A5FO2Asw3qezCYhA7W6qvue4ehi2ePLeQQG', 3, 'DEFAULT', 1, 1, '2025-11-24 01:53:41'),
('RG025', 'SALMA', 'FADHIL', 'MGANGA', 'Female', 'salma@srms.test', 6, '$2y$10$IQjuYGKYXPlzgNwxow0Zd.0bZrbAZZ5ArEAUTCavfvBKPymGOOh7W', 3, 'DEFAULT', 1, 1, '2025-11-24 01:53:41'),
('RG026', 'LUCAS', 'J', 'MAIVAJI', 'Male', 'lucas@srms.test', 7, '$2y$10$yYIzQ6RtyI4xLzpAIBSg.uiJuAg6.T5jmvDQVytB2JPIUlwMtq7bi', 3, 'DEFAULT', 1, 1, '2025-11-24 01:53:41'),
('RG027', 'Mbarouk', 'Abdi', 'MBAROUK', 'Male', 'mbarouk@srms.test', 7, '$2y$10$UIcz5q8wbyCYnOFMSODjO.18PCG75DzY8yZDvtx2N4qqWMDLHdyna', 3, 'DEFAULT', 1, 1, '2025-11-24 01:53:41'),
('RG029', 'NATHAN', 'JORAM', 'MAHUNDI', 'Male', 'nathan@srms.test', 7, '$2y$10$fmyCT5/rEONcZZ7VwSvsqexW6sTMtaNU0/.UuseUxvMlUYb5bum9a', 3, 'DEFAULT', 1, 1, '2025-11-24 01:53:41'),
('RG030', 'PATRICK', 'STEVEN', 'MAPUNDA', 'Male', 'patrick@srms.test', 7, '$2y$10$mH3xVRY89bog.AXxBkZ.KedfwplTlmZzctwlMs2EfXcYQvZrGKIaO', 3, 'DEFAULT', 1, 1, '2025-11-24 01:53:41'),
('RG031', 'PHILIPO', 'A', 'KANYOKI', 'Male', 'philipo@srms.test', 7, '$2y$10$4QwWL4bPU.UBW7AkXIv7FO73zZBGkjpTWwOzEDaOU/dpqvXHyUgOC', 3, 'DEFAULT', 1, 1, '2025-11-24 01:53:41'),
('RG033', 'SADICK', 'SHARABII', 'KIBASA', 'Male', 'sadick@srms.test', 7, '$2y$10$bo3kC2paitvInM.7bmkNVeOsNJq2vDNzgQBunzh3pKtx2r389s0xS', 3, 'DEFAULT', 1, 1, '2025-11-24 01:53:41'),
('RG034', 'STEVEN', 'DAUD', 'DAUDI', 'Male', 'steven@srms.test', 7, '$2y$10$KoiNZztpY89BKf9eceJBJ.D3UEQ4Snd3DC67aIlhEG4H9lQFvGLKK', 3, 'DEFAULT', 1, 1, '2025-11-24 01:53:41'),
('RG035', 'TITUS', 'S', 'SIZIMWE', 'Male', 'titus@srms.test', 8, '$2y$10$1XfiDBvDZfDm9VWU9GtNmeO6H4nhPnhMBetS6zlxdi4E6ugm66HC6', 3, 'DEFAULT', 1, 1, '2025-11-24 01:53:41'),
('RG036', 'TUMAINIEL', 'JONA', 'MKONY', 'Male', 'tumainiel@srms.test', 8, '$2y$10$/312a8OcyO14EDLWmIA1vODEAwEMHdgyZ7R/3dx0CWSnQ0ijz6bkm', 3, 'DEFAULT', 1, 1, '2025-11-24 01:53:41'),
('RG037', 'Willfat', 'Hassan', 'SHAMS', 'Female', 'willfat@srms.test', 8, '$2y$10$WKDBlVj/lsuLqezdINtqQuxpRwgr8kZxof/tJjC6hgv06yE/58l46', 3, 'DEFAULT', 1, 1, '2025-11-24 01:53:41'),
('RG038', 'WILLIAM', 'MUJUNI', 'BALAILE', 'Male', 'william@srms.test', 8, '$2y$10$h30odqjWbQSkI3IKEDoR/u1EoBvYzzeoYl7LIgoPHD.jbpj4pyr62', 3, 'DEFAULT', 1, 1, '2025-11-24 01:53:41'),
('RG039', 'YOHANA', 'JACKSON', 'SIIMA', 'Male', 'yohana@srms.test', 8, '$2y$10$FLqe1Tf/71wHH51KbHqb1e5uqou4uzxKYfe.yhORo30kgK.2QXRlO', 3, 'DEFAULT', 1, 1, '2025-11-24 01:53:41');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_student_fees`
--

CREATE TABLE `tbl_student_fees` (
  `id` int(11) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `session_id` int(11) NOT NULL,
  `term_id` int(11) DEFAULT '1',
  `class_id` int(11) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `outstanding` decimal(12,2) DEFAULT '0.00',
  `status` enum('pending','paid') DEFAULT 'pending',
  `payment_reference` varchar(100) DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_student_fees`
--

INSERT INTO `tbl_student_fees` (`id`, `student_id`, `session_id`, `term_id`, `class_id`, `amount`, `outstanding`, `status`, `payment_reference`, `paid_at`, `created_at`, `updated_at`) VALUES
(1, '11233', 1, 0, 5, '15000.00', '0.00', 'paid', '11233_1763121001', '2025-11-14 13:01:46', '2025-11-14 13:01:46', '2025-11-14 13:01:46');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_student_subjects`
--

CREATE TABLE `tbl_student_subjects` (
  `id` int(11) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `class_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `session_id` int(11) DEFAULT NULL,
  `term_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_student_subjects`
--

INSERT INTO `tbl_student_subjects` (`id`, `student_id`, `class_id`, `subject_id`, `session_id`, `term_id`, `created_at`) VALUES
(247, '11233', 5, 3, 1, 1, '2025-12-22 22:03:08'),
(248, '11233', 5, 4, 1, 1, '2025-12-22 22:03:08'),
(249, '11233', 5, 5, 1, 1, '2025-12-22 22:03:08'),
(250, '11233', 5, 6, 1, 1, '2025-12-22 22:03:08'),
(251, '11233', 5, 7, 1, 1, '2025-12-22 22:03:08'),
(252, '11233', 5, 8, 1, 1, '2025-12-22 22:03:08'),
(253, '11233', 5, 9, 1, 1, '2025-12-22 22:03:08'),
(254, '11233', 5, 10, 1, 1, '2025-12-22 22:03:08'),
(255, '11233', 5, 11, 1, 1, '2025-12-22 22:03:08'),
(256, '11233', 5, 12, 1, 1, '2025-12-22 22:03:08'),
(257, '44T', 5, 3, 1, 1, '2025-12-22 22:03:08'),
(258, '44T', 5, 4, 1, 1, '2025-12-22 22:03:08'),
(259, '44T', 5, 5, 1, 1, '2025-12-22 22:03:08'),
(260, '44T', 5, 6, 1, 1, '2025-12-22 22:03:08'),
(261, '44T', 5, 7, 1, 1, '2025-12-22 22:03:08'),
(262, '44T', 5, 8, 1, 1, '2025-12-22 22:03:08'),
(263, '44T', 5, 9, 1, 1, '2025-12-22 22:03:08'),
(264, '44T', 5, 10, 1, 1, '2025-12-22 22:03:08'),
(265, '44T', 5, 11, 1, 1, '2025-12-22 22:03:08'),
(266, '44T', 5, 12, 1, 1, '2025-12-22 22:03:08'),
(267, 'RG001', 5, 3, 1, 1, '2025-12-22 22:03:08'),
(268, 'RG001', 5, 4, 1, 1, '2025-12-22 22:03:08'),
(269, 'RG001', 5, 5, 1, 1, '2025-12-22 22:03:08'),
(270, 'RG001', 5, 6, 1, 1, '2025-12-22 22:03:08'),
(271, 'RG001', 5, 7, 1, 1, '2025-12-22 22:03:08'),
(272, 'RG001', 5, 8, 1, 1, '2025-12-22 22:03:08'),
(273, 'RG001', 5, 9, 1, 1, '2025-12-22 22:03:08'),
(274, 'RG001', 5, 10, 1, 1, '2025-12-22 22:03:08'),
(275, 'RG001', 5, 11, 1, 1, '2025-12-22 22:03:08'),
(276, 'RG001', 5, 12, 1, 1, '2025-12-22 22:03:08'),
(277, 'RG002', 5, 3, 1, 1, '2025-12-22 22:03:08'),
(278, 'RG002', 5, 4, 1, 1, '2025-12-22 22:03:08'),
(279, 'RG002', 5, 5, 1, 1, '2025-12-22 22:03:08'),
(280, 'RG002', 5, 6, 1, 1, '2025-12-22 22:03:08'),
(281, 'RG002', 5, 7, 1, 1, '2025-12-22 22:03:08'),
(282, 'RG002', 5, 8, 1, 1, '2025-12-22 22:03:08'),
(283, 'RG002', 5, 9, 1, 1, '2025-12-22 22:03:08'),
(284, 'RG002', 5, 10, 1, 1, '2025-12-22 22:03:08'),
(285, 'RG002', 5, 11, 1, 1, '2025-12-22 22:03:08'),
(286, 'RG002', 5, 12, 1, 1, '2025-12-22 22:03:08'),
(287, 'RG003', 5, 3, 1, 1, '2025-12-22 22:03:08'),
(288, 'RG003', 5, 4, 1, 1, '2025-12-22 22:03:08'),
(289, 'RG003', 5, 5, 1, 1, '2025-12-22 22:03:08'),
(290, 'RG003', 5, 6, 1, 1, '2025-12-22 22:03:08'),
(291, 'RG003', 5, 7, 1, 1, '2025-12-22 22:03:08'),
(292, 'RG003', 5, 8, 1, 1, '2025-12-22 22:03:08'),
(293, 'RG003', 5, 9, 1, 1, '2025-12-22 22:03:08'),
(294, 'RG003', 5, 10, 1, 1, '2025-12-22 22:03:08'),
(295, 'RG003', 5, 11, 1, 1, '2025-12-22 22:03:08'),
(296, 'RG003', 5, 12, 1, 1, '2025-12-22 22:03:08'),
(297, 'RG004', 5, 3, 1, 1, '2025-12-22 22:03:08'),
(298, 'RG004', 5, 4, 1, 1, '2025-12-22 22:03:08'),
(299, 'RG004', 5, 5, 1, 1, '2025-12-22 22:03:08'),
(300, 'RG004', 5, 6, 1, 1, '2025-12-22 22:03:08'),
(301, 'RG004', 5, 7, 1, 1, '2025-12-22 22:03:08'),
(302, 'RG004', 5, 8, 1, 1, '2025-12-22 22:03:08'),
(303, 'RG004', 5, 9, 1, 1, '2025-12-22 22:03:08'),
(304, 'RG004', 5, 10, 1, 1, '2025-12-22 22:03:08'),
(305, 'RG004', 5, 11, 1, 1, '2025-12-22 22:03:08'),
(306, 'RG004', 5, 12, 1, 1, '2025-12-22 22:03:08'),
(307, 'RG005', 5, 3, 1, 1, '2025-12-22 22:03:08'),
(308, 'RG005', 5, 4, 1, 1, '2025-12-22 22:03:08'),
(309, 'RG005', 5, 5, 1, 1, '2025-12-22 22:03:08'),
(310, 'RG005', 5, 6, 1, 1, '2025-12-22 22:03:08'),
(311, 'RG005', 5, 7, 1, 1, '2025-12-22 22:03:08'),
(312, 'RG005', 5, 8, 1, 1, '2025-12-22 22:03:08'),
(313, 'RG005', 5, 9, 1, 1, '2025-12-22 22:03:08'),
(314, 'RG005', 5, 10, 1, 1, '2025-12-22 22:03:08'),
(315, 'RG005', 5, 11, 1, 1, '2025-12-22 22:03:08'),
(316, 'RG005', 5, 12, 1, 1, '2025-12-22 22:03:08'),
(317, 'RG006', 5, 3, 1, 1, '2025-12-22 22:03:08'),
(318, 'RG006', 5, 4, 1, 1, '2025-12-22 22:03:08'),
(319, 'RG006', 5, 5, 1, 1, '2025-12-22 22:03:08'),
(320, 'RG006', 5, 6, 1, 1, '2025-12-22 22:03:08'),
(321, 'RG006', 5, 7, 1, 1, '2025-12-22 22:03:08'),
(322, 'RG006', 5, 8, 1, 1, '2025-12-22 22:03:08'),
(323, 'RG006', 5, 9, 1, 1, '2025-12-22 22:03:08'),
(324, 'RG006', 5, 10, 1, 1, '2025-12-22 22:03:08'),
(325, 'RG006', 5, 11, 1, 1, '2025-12-22 22:03:08'),
(326, 'RG006', 5, 12, 1, 1, '2025-12-22 22:03:08'),
(327, 'RG007', 5, 3, 1, 1, '2025-12-22 22:03:08'),
(328, 'RG007', 5, 4, 1, 1, '2025-12-22 22:03:08'),
(329, 'RG007', 5, 5, 1, 1, '2025-12-22 22:03:08'),
(330, 'RG007', 5, 6, 1, 1, '2025-12-22 22:03:08'),
(331, 'RG007', 5, 7, 1, 1, '2025-12-22 22:03:08'),
(332, 'RG007', 5, 8, 1, 1, '2025-12-22 22:03:08'),
(333, 'RG007', 5, 9, 1, 1, '2025-12-22 22:03:08'),
(334, 'RG007', 5, 10, 1, 1, '2025-12-22 22:03:08'),
(335, 'RG007', 5, 11, 1, 1, '2025-12-22 22:03:08'),
(336, 'RG007', 5, 12, 1, 1, '2025-12-22 22:03:08'),
(337, 'RG008', 5, 3, 1, 1, '2025-12-22 22:03:08'),
(338, 'RG008', 5, 4, 1, 1, '2025-12-22 22:03:08'),
(339, 'RG008', 5, 5, 1, 1, '2025-12-22 22:03:08'),
(340, 'RG008', 5, 6, 1, 1, '2025-12-22 22:03:08'),
(341, 'RG008', 5, 7, 1, 1, '2025-12-22 22:03:08'),
(342, 'RG008', 5, 8, 1, 1, '2025-12-22 22:03:08'),
(343, 'RG008', 5, 9, 1, 1, '2025-12-22 22:03:08'),
(344, 'RG008', 5, 10, 1, 1, '2025-12-22 22:03:08'),
(345, 'RG008', 5, 11, 1, 1, '2025-12-22 22:03:08'),
(346, 'RG008', 5, 12, 1, 1, '2025-12-22 22:03:08'),
(347, 'RG009', 5, 3, 1, 1, '2025-12-22 22:03:08'),
(348, 'RG009', 5, 4, 1, 1, '2025-12-22 22:03:08'),
(349, 'RG009', 5, 5, 1, 1, '2025-12-22 22:03:08'),
(350, 'RG009', 5, 6, 1, 1, '2025-12-22 22:03:08'),
(351, 'RG009', 5, 7, 1, 1, '2025-12-22 22:03:08'),
(352, 'RG009', 5, 8, 1, 1, '2025-12-22 22:03:08'),
(353, 'RG009', 5, 9, 1, 1, '2025-12-22 22:03:08'),
(354, 'RG009', 5, 10, 1, 1, '2025-12-22 22:03:08'),
(355, 'RG009', 5, 11, 1, 1, '2025-12-22 22:03:08'),
(356, 'RG009', 5, 12, 1, 1, '2025-12-22 22:03:08'),
(357, 'RG010', 5, 3, 1, 1, '2025-12-22 22:03:08'),
(358, 'RG010', 5, 4, 1, 1, '2025-12-22 22:03:08'),
(359, 'RG010', 5, 5, 1, 1, '2025-12-22 22:03:08'),
(360, 'RG010', 5, 6, 1, 1, '2025-12-22 22:03:08'),
(361, 'RG010', 5, 7, 1, 1, '2025-12-22 22:03:08'),
(362, 'RG010', 5, 8, 1, 1, '2025-12-22 22:03:08'),
(363, 'RG010', 5, 9, 1, 1, '2025-12-22 22:03:08'),
(364, 'RG010', 5, 10, 1, 1, '2025-12-22 22:03:08'),
(365, 'RG010', 5, 11, 1, 1, '2025-12-22 22:03:08'),
(366, 'RG010', 5, 12, 1, 1, '2025-12-22 22:03:08'),
(367, 'RG011', 5, 3, 1, 1, '2025-12-22 22:03:08'),
(368, 'RG011', 5, 4, 1, 1, '2025-12-22 22:03:08'),
(369, 'RG011', 5, 5, 1, 1, '2025-12-22 22:03:08'),
(370, 'RG011', 5, 6, 1, 1, '2025-12-22 22:03:08'),
(371, 'RG011', 5, 7, 1, 1, '2025-12-22 22:03:08'),
(372, 'RG011', 5, 8, 1, 1, '2025-12-22 22:03:08'),
(373, 'RG011', 5, 9, 1, 1, '2025-12-22 22:03:08'),
(374, 'RG011', 5, 10, 1, 1, '2025-12-22 22:03:08'),
(375, 'RG011', 5, 11, 1, 1, '2025-12-22 22:03:08'),
(376, 'RG011', 5, 12, 1, 1, '2025-12-22 22:03:08');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_subjects`
--

CREATE TABLE `tbl_subjects` (
  `id` int(11) NOT NULL,
  `name` varchar(90) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_subjects`
--

INSERT INTO `tbl_subjects` (`id`, `name`) VALUES
(3, 'Mathematics'),
(4, 'English'),
(5, 'Kiswahili'),
(6, 'Geography'),
(7, 'History'),
(8, 'Civics'),
(9, 'Biology'),
(10, 'Physics'),
(11, 'Chemistry'),
(12, 'Literature'),
(15, 'Computer Studies');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_subject_combinations`
--

CREATE TABLE `tbl_subject_combinations` (
  `id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `reg_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_subject_combinations`
--

INSERT INTO `tbl_subject_combinations` (`id`, `class_id`, `subject_id`, `teacher_id`, `reg_date`) VALUES
(1, 0, 9, 24, '2025-10-19 14:19:47'),
(2, 3, 11, 24, '2025-10-19 14:26:47'),
(3, 5, 8, 24, '2025-12-23 00:38:01'),
(4, 5, 4, 6, '2025-12-23 00:37:43'),
(5, 5, 6, 24, '2025-12-23 00:38:41'),
(6, 4, 5, 13, '2024-03-18 14:03:00'),
(7, 5, 3, 24, '2025-12-23 00:39:53'),
(8, 5, 10, 11, '2025-12-23 00:40:15'),
(9, 5, 7, 16, '2024-03-18 14:04:12'),
(10, 0, 9, 24, '2025-10-19 14:20:25'),
(11, 5, 11, 20, '2024-03-18 14:07:07'),
(12, 5, 8, 24, '2025-11-08 19:42:58'),
(13, 6, 4, 13, '2024-03-18 14:07:57'),
(14, 6, 6, 7, '2024-03-18 14:08:22'),
(15, 6, 7, 5, '2024-03-18 14:09:03'),
(16, 5, 5, 21, '2025-12-23 00:39:00'),
(17, 5, 12, 24, '2025-12-23 00:39:26'),
(18, 7, 3, 10, '2024-03-18 14:11:01'),
(19, 5, 8, 3, '2025-12-23 00:38:16'),
(20, 7, 8, 3, '2025-10-19 02:15:51'),
(21, 4, 11, 24, '2025-10-19 14:22:39'),
(22, 9, 11, 24, '2025-10-19 14:22:39'),
(23, 3, 15, 24, '2025-11-08 19:12:30'),
(24, 16, 15, 24, '2025-11-08 19:12:30'),
(25, 5, 9, 24, '2025-12-23 00:37:06');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_terms`
--

CREATE TABLE `tbl_terms` (
  `id` int(11) NOT NULL,
  `name` varchar(90) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '1' COMMENT '	0 = Disabled , 1 = Enabled'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_terms`
--

INSERT INTO `tbl_terms` (`id`, `name`, `status`) VALUES
(1, 'FIRST TERM', 1),
(2, 'SECOND TERM', 0),
(3, 'THIRD TERM', 0);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_timetable`
--

CREATE TABLE `tbl_timetable` (
  `id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `day` varchar(20) NOT NULL,
  `time_slot` varchar(20) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_timetable`
--

INSERT INTO `tbl_timetable` (`id`, `session_id`, `class_id`, `date`, `day`, `time_slot`, `subject`, `created_at`, `start_time`, `end_time`) VALUES
(1, 1, 4, '0000-00-00', 'Monday', '', 'Mathematics', '2025-10-20 23:28:10', '11:00:00', '12:00:00'),
(2, 1, 4, '0000-00-00', 'Monday', '', 'English', '2025-10-20 23:30:12', '12:00:00', '01:00:00'),
(3, 1, 5, '0000-00-00', 'Monday', '', 'Civics', '2025-11-11 14:49:29', '05:00:00', '06:00:00'),
(4, 1, 4, '0000-00-00', 'Wednesday', '', 'Mathematics', '2025-11-11 14:52:12', '08:00:00', '08:08:00');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_view_result`
--

CREATE TABLE `tbl_view_result` (
  `id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `term_id` int(11) NOT NULL,
  `status` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_view_result`
--

INSERT INTO `tbl_view_result` (`id`, `session_id`, `term_id`, `status`, `created_at`) VALUES
(1, 1, 1, 1, '2025-11-12 18:21:52');

-- --------------------------------------------------------

--
-- Table structure for table `teacher_enquiries`
--

CREATE TABLE `teacher_enquiries` (
  `id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `sender_name` varchar(100) NOT NULL,
  `title` varchar(150) NOT NULL,
  `category` enum('Complain','Technical Issue','Payment Issue','Other') NOT NULL,
  `message` text NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `alt_contact` varchar(100) DEFAULT NULL,
  `status` enum('Pending','Reviewed','Resolved') DEFAULT 'Pending',
  `reply_message` text,
  `replied_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `teacher_enquiries`
--

INSERT INTO `teacher_enquiries` (`id`, `teacher_id`, `sender_name`, `title`, `category`, `message`, `file_path`, `alt_contact`, `status`, `reply_message`, `replied_at`, `created_at`) VALUES
(1, 24, 'Maxwell Halilu', 'DID NOT RECIEVED MY SALARY', 'Payment Issue', '<font color=\"#dcdcaa\" face=\"Consolas, Courier New, monospace\"><span style=\"white-space: pre;\">DID NOT RECIEVED MY SALARY</span></font>', NULL, 'mgtechs09@gmail.com', '', 'THANK YOU FOR YOUR PATIENCE YOU HAVE BEEN CREDICTED', '2025-11-11 15:29:00', '2025-11-11 13:45:30'),
(2, 24, 'Maxwell Halilu', 'RECOMMENDATION LETTER FOR BILLAH WILLSON', 'Other', '<p>Dear Admissions Committee,</p><p>I am writing to enthusiastically recommend Billah Willson for admission to the American University of Nigeria. I have had the distinct pleasure of knowing and mentoring her for the past three years in my capacity as supervisor, and I can confidently attest to her intelligence, integrity, and motivation.</p><p>Throughout our interaction, Billah Willson has consistently demonstrated a genuine passion for learning, excellent problem-solving skills, and a remarkable level of discipline and leadership. She approaches tasks with maturity and thoughtfulness, whether it’s participating in academic projects, community initiatives, or extracurricular activities.</p><p>One trait that truly sets Billah Willson apart is her commitment to excellence and desire to contribute positively to society. She exhibits the kind of</p><p>curiosity, empathy, and initiative that aligns perfectly with the values and mission of AUN. I am confident that she will thrive in AUN\'s intellectually rich and socially conscious environment.</p><p>In conclusion, I strongly recommend Billah Willson for admission to the American University of Nigeria. I am confident that she will not only benefit immensely from the education at AUN but also be a valuable contributor to the university community.</p><p>Please feel free to contact me at 08161595906 or softlinks@gmail.com if you require any further information.</p><p>Sincerely,</p><p><b>Nabisire Musa</b></p><p>08162680873</p>', 'uploads/enquiries/1763193705_Effect of Neem (Azadirachta indica) Plant Extract on Microorganism and the Importance of its Bioactive Compounds on Human Health.pdf', 'mgtechs09@gmail.com', '', 'thank you we will notify you as soon as posible', '2025-11-15 09:04:05', '2025-11-15 08:01:45');

-- --------------------------------------------------------

--
-- Table structure for table `teacher_exams`
--

CREATE TABLE `teacher_exams` (
  `id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `term_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `instructions` text,
  `question_text` text,
  `file_path` varchar(255) DEFAULT NULL,
  `status` enum('PENDING','APPROVED','REJECTED') DEFAULT 'PENDING',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `teacher_exams`
--

INSERT INTO `teacher_exams` (`id`, `teacher_id`, `session_id`, `term_id`, `class_id`, `subject_id`, `instructions`, `question_text`, `file_path`, `status`, `created_at`, `updated_at`) VALUES
(1, 24, 1, 3, 3, 9, 'g y', '<p>hhff hgv fghfg wsdeftryghjuoik esrdfnm&nbsp; &nbsp; rdftyuhij</p>', NULL, 'PENDING', '2025-11-14 16:56:55', '2025-11-14 16:56:55');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `email_api_settings`
--
ALTER TABLE `email_api_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `enquiries`
--
ALTER TABLE `enquiries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sms_api_settings`
--
ALTER TABLE `sms_api_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `staff_role`
--
ALTER TABLE `staff_role`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_academic_calendar`
--
ALTER TABLE `tbl_academic_calendar`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_announcements`
--
ALTER TABLE `tbl_announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_applications`
--
ALTER TABLE `tbl_applications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_application_settings`
--
ALTER TABLE `tbl_application_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_category`
--
ALTER TABLE `tbl_category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_classes`
--
ALTER TABLE `tbl_classes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_class_promotion`
--
ALTER TABLE `tbl_class_promotion`
  ADD PRIMARY KEY (`id`),
  ADD KEY `from_class_id` (`from_class_id`),
  ADD KEY `to_class_id` (`to_class_id`);

--
-- Indexes for table `tbl_division_system`
--
ALTER TABLE `tbl_division_system`
  ADD PRIMARY KEY (`division`);

--
-- Indexes for table `tbl_exams_questions_settings`
--
ALTER TABLE `tbl_exams_questions_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_expenses`
--
ALTER TABLE `tbl_expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `tbl_fee_mapping`
--
ALTER TABLE `tbl_fee_mapping`
  ADD PRIMARY KEY (`id`),
  ADD KEY `session_id` (`session_id`),
  ADD KEY `term_id` (`term_id`),
  ADD KEY `class_id` (`class_id`);

--
-- Indexes for table `tbl_grade_system`
--
ALTER TABLE `tbl_grade_system`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_leave_applications`
--
ALTER TABLE `tbl_leave_applications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_loan_applications`
--
ALTER TABLE `tbl_loan_applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `staff_id` (`staff_id`);

--
-- Indexes for table `tbl_login_sessions`
--
ALTER TABLE `tbl_login_sessions`
  ADD PRIMARY KEY (`session_key`),
  ADD KEY `staff` (`staff`),
  ADD KEY `student` (`student`);

--
-- Indexes for table `tbl_paystack_api_settings`
--
ALTER TABLE `tbl_paystack_api_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_promotions`
--
ALTER TABLE `tbl_promotions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_results`
--
ALTER TABLE `tbl_results`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_result` (`student_id`,`class_id`,`subject_id`,`session_id`,`term_id`);

--
-- Indexes for table `tbl_salary_mapping`
--
ALTER TABLE `tbl_salary_mapping`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_salary_staff` (`staff_id`),
  ADD KEY `fk_salary_role` (`role_id`);

--
-- Indexes for table `tbl_school`
--
ALTER TABLE `tbl_school`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_score_entry_settings`
--
ALTER TABLE `tbl_score_entry_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_session_term` (`session_id`,`term_id`),
  ADD KEY `fk_scoreentry_term` (`term_id`);

--
-- Indexes for table `tbl_sessions`
--
ALTER TABLE `tbl_sessions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_site_settings`
--
ALTER TABLE `tbl_site_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_smtp`
--
ALTER TABLE `tbl_smtp`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_staff`
--
ALTER TABLE `tbl_staff`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_staff_applications`
--
ALTER TABLE `tbl_staff_applications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_students`
--
ALTER TABLE `tbl_students`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class` (`class`);

--
-- Indexes for table `tbl_student_fees`
--
ALTER TABLE `tbl_student_fees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_student_session` (`student_id`,`session_id`),
  ADD KEY `session_id` (`session_id`),
  ADD KEY `class_id` (`class_id`);

--
-- Indexes for table `tbl_student_subjects`
--
ALTER TABLE `tbl_student_subjects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_assign` (`student_id`,`subject_id`,`class_id`,`session_id`,`term_id`),
  ADD KEY `class_id` (`class_id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `session_id` (`session_id`),
  ADD KEY `term_id` (`term_id`);

--
-- Indexes for table `tbl_subjects`
--
ALTER TABLE `tbl_subjects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_subject_combinations`
--
ALTER TABLE `tbl_subject_combinations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `teacher` (`teacher_id`),
  ADD KEY `subject` (`subject_id`);

--
-- Indexes for table `tbl_terms`
--
ALTER TABLE `tbl_terms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_timetable`
--
ALTER TABLE `tbl_timetable`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_view_result`
--
ALTER TABLE `tbl_view_result`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `teacher_enquiries`
--
ALTER TABLE `teacher_enquiries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Indexes for table `teacher_exams`
--
ALTER TABLE `teacher_exams`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_submission` (`teacher_id`,`session_id`,`term_id`,`class_id`,`subject_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `email_api_settings`
--
ALTER TABLE `email_api_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `enquiries`
--
ALTER TABLE `enquiries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sms_api_settings`
--
ALTER TABLE `sms_api_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `staff_role`
--
ALTER TABLE `staff_role`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_academic_calendar`
--
ALTER TABLE `tbl_academic_calendar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tbl_announcements`
--
ALTER TABLE `tbl_announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_applications`
--
ALTER TABLE `tbl_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_application_settings`
--
ALTER TABLE `tbl_application_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_category`
--
ALTER TABLE `tbl_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_classes`
--
ALTER TABLE `tbl_classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `tbl_class_promotion`
--
ALTER TABLE `tbl_class_promotion`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tbl_exams_questions_settings`
--
ALTER TABLE `tbl_exams_questions_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_expenses`
--
ALTER TABLE `tbl_expenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_fee_mapping`
--
ALTER TABLE `tbl_fee_mapping`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_grade_system`
--
ALTER TABLE `tbl_grade_system`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tbl_leave_applications`
--
ALTER TABLE `tbl_leave_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_loan_applications`
--
ALTER TABLE `tbl_loan_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tbl_paystack_api_settings`
--
ALTER TABLE `tbl_paystack_api_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_promotions`
--
ALTER TABLE `tbl_promotions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `tbl_results`
--
ALTER TABLE `tbl_results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT for table `tbl_salary_mapping`
--
ALTER TABLE `tbl_salary_mapping`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tbl_school`
--
ALTER TABLE `tbl_school`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_score_entry_settings`
--
ALTER TABLE `tbl_score_entry_settings`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_sessions`
--
ALTER TABLE `tbl_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_site_settings`
--
ALTER TABLE `tbl_site_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_smtp`
--
ALTER TABLE `tbl_smtp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_staff`
--
ALTER TABLE `tbl_staff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `tbl_staff_applications`
--
ALTER TABLE `tbl_staff_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_student_fees`
--
ALTER TABLE `tbl_student_fees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_student_subjects`
--
ALTER TABLE `tbl_student_subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=377;

--
-- AUTO_INCREMENT for table `tbl_subjects`
--
ALTER TABLE `tbl_subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `tbl_subject_combinations`
--
ALTER TABLE `tbl_subject_combinations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `tbl_terms`
--
ALTER TABLE `tbl_terms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tbl_timetable`
--
ALTER TABLE `tbl_timetable`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_view_result`
--
ALTER TABLE `tbl_view_result`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `teacher_enquiries`
--
ALTER TABLE `teacher_enquiries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `teacher_exams`
--
ALTER TABLE `teacher_exams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbl_class_promotion`
--
ALTER TABLE `tbl_class_promotion`
  ADD CONSTRAINT `tbl_class_promotion_ibfk_1` FOREIGN KEY (`from_class_id`) REFERENCES `tbl_classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_class_promotion_ibfk_2` FOREIGN KEY (`to_class_id`) REFERENCES `tbl_classes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_expenses`
--
ALTER TABLE `tbl_expenses`
  ADD CONSTRAINT `tbl_expenses_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `tbl_category` (`id`);

--
-- Constraints for table `tbl_fee_mapping`
--
ALTER TABLE `tbl_fee_mapping`
  ADD CONSTRAINT `tbl_fee_mapping_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `tbl_sessions` (`id`),
  ADD CONSTRAINT `tbl_fee_mapping_ibfk_2` FOREIGN KEY (`term_id`) REFERENCES `tbl_terms` (`id`),
  ADD CONSTRAINT `tbl_fee_mapping_ibfk_3` FOREIGN KEY (`class_id`) REFERENCES `tbl_classes` (`id`);

--
-- Constraints for table `tbl_loan_applications`
--
ALTER TABLE `tbl_loan_applications`
  ADD CONSTRAINT `tbl_loan_applications_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `tbl_staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_login_sessions`
--
ALTER TABLE `tbl_login_sessions`
  ADD CONSTRAINT `tbl_login_sessions_ibfk_1` FOREIGN KEY (`staff`) REFERENCES `tbl_staff` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_login_sessions_ibfk_2` FOREIGN KEY (`student`) REFERENCES `tbl_students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_results`
--
ALTER TABLE `tbl_results`
  ADD CONSTRAINT `fk_student_result` FOREIGN KEY (`student_id`) REFERENCES `tbl_students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_salary_mapping`
--
ALTER TABLE `tbl_salary_mapping`
  ADD CONSTRAINT `fk_salary_role` FOREIGN KEY (`role_id`) REFERENCES `staff_role` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_salary_staff` FOREIGN KEY (`staff_id`) REFERENCES `tbl_staff` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_score_entry_settings`
--
ALTER TABLE `tbl_score_entry_settings`
  ADD CONSTRAINT `fk_scoreentry_session` FOREIGN KEY (`session_id`) REFERENCES `tbl_sessions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_scoreentry_term` FOREIGN KEY (`term_id`) REFERENCES `tbl_terms` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_students`
--
ALTER TABLE `tbl_students`
  ADD CONSTRAINT `tbl_students_ibfk_1` FOREIGN KEY (`class`) REFERENCES `tbl_classes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_student_fees`
--
ALTER TABLE `tbl_student_fees`
  ADD CONSTRAINT `tbl_student_fees_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `tbl_students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_student_fees_ibfk_2` FOREIGN KEY (`session_id`) REFERENCES `tbl_sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_student_fees_ibfk_3` FOREIGN KEY (`class_id`) REFERENCES `tbl_classes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_student_subjects`
--
ALTER TABLE `tbl_student_subjects`
  ADD CONSTRAINT `tbl_student_subjects_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `tbl_students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_student_subjects_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `tbl_classes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_student_subjects_ibfk_3` FOREIGN KEY (`subject_id`) REFERENCES `tbl_subjects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_student_subjects_ibfk_4` FOREIGN KEY (`session_id`) REFERENCES `tbl_sessions` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_student_subjects_ibfk_5` FOREIGN KEY (`term_id`) REFERENCES `tbl_terms` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `tbl_subject_combinations`
--
ALTER TABLE `tbl_subject_combinations`
  ADD CONSTRAINT `tbl_subject_combinations_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `tbl_subjects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_subject_combinations_ibfk_3` FOREIGN KEY (`teacher_id`) REFERENCES `tbl_staff` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `teacher_enquiries`
--
ALTER TABLE `teacher_enquiries`
  ADD CONSTRAINT `teacher_enquiries_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `tbl_staff` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
