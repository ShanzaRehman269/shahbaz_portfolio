-- --------------------------------------------------------
-- Database: `portfolio_db` - FINAL FIXED
-- --------------------------------------------------------

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
SET NAMES utf8mb4;

DROP TABLE IF EXISTS `admin`;
DROP TABLE IF EXISTS `certificates`;
DROP TABLE IF EXISTS `contact`;
DROP TABLE IF EXISTS `education`;
DROP TABLE IF EXISTS `experience`;
DROP TABLE IF EXISTS `highlights`;
DROP TABLE IF EXISTS `info`;
DROP TABLE IF EXISTS `languages`;
DROP TABLE IF EXISTS `projects`;
DROP TABLE IF EXISTS `skills`;

-- --------------------------------------------------------
-- Table `admin`
-- --------------------------------------------------------

CREATE TABLE `admin` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `admin` (`id`, `username`, `email`, `password`, `reset_token`, `reset_expires`) VALUES
(1, 'shahbazdev@gmail.com', 'shahbazdev@gmail.com', '$2y$10$nCncurJNg0edrBr6VHCnz.fsiFboMovEEJgqhE51pyD70P7Qg30/6', NULL, NULL);

-- --------------------------------------------------------
-- Table `certificates`
-- --------------------------------------------------------

CREATE TABLE `certificates` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `icon` varchar(100) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `issuer` varchar(255) DEFAULT NULL,
  `year` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `certificates` (`id`, `icon`, `title`, `issuer`, `year`, `description`) VALUES
(1, 'fa-solid fa-graduation-cap', 'BS Software Engineering', 'Thal University Bhakkar', '2022–2026', 'Bachelor of Science in Software Engineering.');

-- --------------------------------------------------------
-- Table `contact`
-- --------------------------------------------------------

CREATE TABLE `contact` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` varchar(100) NOT NULL,
  `value` varchar(255) NOT NULL,
  `link` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `contact` (`id`, `type`, `value`, `link`) VALUES
(1, 'Email', 'shahbazdev@gmail.com', 'mailto:shahbazdev@gmail.com'),
(2, 'LinkedIn', 'https://linkedin.com/in/shahbazofficial', 'https://linkedin.com/in/shahbazofficial'),
(3, 'Instagram', 'https://instagram.com/engr_shahbaze?igsi=MWQ1MTljbGlldTEyOQ==', 'https://instagram.com/engr_shahbaze?igsi=MWQ1MTljbGlldTEyOQ=='),
(4, 'phone', '02345678', '02345678'),
(5, 'github', 'https://github.com/in/shahbazofficial', 'https://github.com/in/shahbazofficial');

-- --------------------------------------------------------
-- Table `education`
-- --------------------------------------------------------

CREATE TABLE `education` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `degree` varchar(255) NOT NULL,
  `institution` varchar(255) NOT NULL,
  `duration` varchar(100) DEFAULT NULL,
  `result` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `education` (`id`, `degree`, `institution`, `duration`, `result`, `description`) VALUES
(1, 'Software Engineering', 'Thal University Bhakkar', '2022 - 2026', 'CGPA 3.68 / 4.0', 'Bachelor of Software Engineering.'),
(2, 'Intermediate', 'Pakistan Public School and College Dullewala', '2020 - 2022', '', 'Intermediate education.'),
(3, 'Matric', 'Pakistan Public School and College Dullewala', '2017 - 2019', '867 / 1100', 'Matriculation education.');

-- --------------------------------------------------------
-- Table `experience`
-- --------------------------------------------------------

CREATE TABLE `experience` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `date` varchar(100) DEFAULT NULL,
  `role` varchar(255) NOT NULL,
  `company` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `experience` (`id`, `date`, `role`, `company`, `description`) VALUES
(1, 'Current', 'Mathematics Mentor', 'Nasir Forces Academy', 'Providing mathematics mentoring and supporting students in their learning.'),
(2, 'Current', 'Flutter Developer', 'AlhaiSofts', 'Developing cross-platform mobile applications using Flutter and Dart.'),
(3, 'Current', 'Founder', 'Dev Drive Private Limited', 'Founder of Dev Drive Private Limited.'),
(4, '2025 - Present', 'Junior Developer', 'AlhaiSoft', 'Complex coding, software debugging and testing.');

-- --------------------------------------------------------
-- Table `highlights`
-- --------------------------------------------------------

CREATE TABLE `highlights` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `highlights` (`id`, `title`, `description`, `icon`) VALUES
(1, 'Flutter Development', 'Building cross-platform mobile applications using Flutter and Dart.', '📱'),
(2, 'Clean UI', 'Creating clean and user-focused interfaces with attention to usability.', '🎨'),
(3, 'Problem Solving', 'Focused on debugging, testing and solving software development problems.', '💡');

-- --------------------------------------------------------
-- Table `info`
-- --------------------------------------------------------

CREATE TABLE `info` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `title` varchar(255) NOT NULL,
  `bio` text DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `cv_file` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `info` (`id`, `name`, `title`, `bio`, `profile_pic`, `cv_file`) VALUES
(1, 'Muhammad Shahbaz', 'Flutter Developer', 'Flutter Developer skilled in building cross-platform applications using Dart, with a focus on clean user interfaces, efficient state management, performance, and user-focused design.', 'file_6a9bb7af38f4a5.24720781.jpg', 'file_6a9bb7af3b9627.52971025.pdf');

-- --------------------------------------------------------
-- Table `languages`
-- --------------------------------------------------------

CREATE TABLE `languages` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `language` varchar(100) NOT NULL,
  `level` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `languages` (`id`, `language`, `level`) VALUES
(1, 'English', 'Professional'),
(2, 'Urdu', 'Native');

-- --------------------------------------------------------
-- Table `projects`
-- --------------------------------------------------------

CREATE TABLE `projects` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `icon` varchar(100) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `tags` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `projects` (`id`, `icon`, `title`, `description`, `tags`) VALUES
(1, '💻', 'PMA Guide', 'Guiding people for preparation', 'flutter,');

-- --------------------------------------------------------
-- Table `skills`
-- --------------------------------------------------------

CREATE TABLE `skills` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `skill_name` varchar(150) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `skills` (`id`, `skill_name`) VALUES
(2, 'Python'),
(3, 'Java'),
(4, 'Database'),
(5, 'RESTful APIs'),
(6, 'SQLite'),
(7, 'MySQL'),
(8, 'OOP'),
(9, 'State Management'),
(10, 'MVC / MVVM Architecture'),
(11, 'Deployment'),
(12, 'Coding for Mobile Applications'),
(13, 'Problem-Solving & Collaboration');

COMMIT;