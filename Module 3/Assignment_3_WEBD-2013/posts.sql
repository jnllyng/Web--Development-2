-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 07, 2025 at 03:12 AM
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
-- Database: `serverside`
--

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `title`, `content`, `timestamp`) VALUES
(1, 'Welcome to My Blog', 'This is my first blog post. I am excited to share my thoughts and experiences with you all. Stay tuned for more interesting content!', '2025-10-06 15:22:28'),
(2, 'Learning PHP and MySQL', 'Today I learned about PDO and prepared statements. It is really important to use parameterized queries to prevent SQL injection attacks. Security should always be a top priority when developing web applications.', '2025-10-06 15:22:28'),
(3, 'Web Development Tips', 'Here are some tips for aspiring web developers: Practice consistently, build real projects, learn version control with Git, understand the fundamentals before jumping to frameworks, and never stop learning. The web development landscape is always evolving.', '2025-10-06 15:22:28'),
(4, 'My Coding Journey', 'I started learning to code six months ago and it has been an incredible journey. From struggling with basic syntax to building my own applications, every challenge has been a learning opportunity.', '2025-10-06 15:22:28'),
(5, 'The Importance of Clean Code', 'Writing clean, maintainable code is just as important as making it work. Use meaningful variable names, add comments where necessary, follow coding standards, and always think about the developer who will read your code next - it might be you six months from now!', '2025-10-06 15:22:28');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
