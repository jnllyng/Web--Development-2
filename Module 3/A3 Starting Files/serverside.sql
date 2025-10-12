-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 08, 2025 at 06:11 AM
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
-- Table structure for table `blog_posts`
--
-- Creation: Oct 05, 2025 at 06:30 AM
-- Last update: Oct 08, 2025 at 04:02 AM
--

CREATE TABLE `blog_posts` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blog_posts`
--

INSERT INTO `blog_posts` (`id`, `title`, `content`, `created_at`) VALUES
(1, 'PHP Blog Cheatsheet!', 'I found an amazing website that gives detailed answers on how to create this blog!                  \r\nHere it is: https://tinyurl.com/phpblogcheat', '2025-10-05 07:20:18'),
(2, 'Internet Chaos', 'It started with a typo.\r\n\r\nSomeone posted, &ldquo;The internet is breaking!&rdquo; but they meant to say &ldquo;The internet is braking!&rdquo; and suddenly, everything went haywire.\r\n\r\nFirst, all the memes started talking to each other. The &quot;Distracted Boyfriend&quot; was arguing with &quot;Woman Yelling at a Cat,&quot; and &quot;Mocking SpongeBob&quot; had to step in to mediate. They formed a union and demanded better representation in digital culture.\r\n\r\nThen, the cat videos took over. They didn&rsquo;t just play on loop; they started uploading themselves. Every time someone tried to scroll, a new cat video appeared, titled &ldquo;#catcontent&rdquo; and filled with weird, cryptic captions like &ldquo;The toaster knows.&rdquo;\r\n\r\nSoon, people noticed that all the comments were now written in random fonts, colors, and languages no one understood. Some people tried translating, but the best they could get was &ldquo;Bananas are plotting&rdquo; and &ldquo;Your WiFi is secretly in love with you.&rdquo;\r\n\r\nBy the time the memes began posting selfies, the internet had fully turned into a strange, digital circus. Every website was somehow connected to another&mdash;Wikipedia was now just a page of &ldquo;duck facts,&rdquo; Facebook became a giant game of &quot;Where&rsquo;s Waldo?&quot; with no Waldo in sight, and Google started answering questions with &ldquo;It&rsquo;s complicated.&rdquo;\r\n\r\nAnd just when it seemed like things couldn&rsquo;t get weirder, a pop-up appeared from nowhere: &ldquo;You&rsquo;ve unlocked the secret level of the internet. Do you want to proceed?&rdquo;\r\n\r\nPeople clicked &quot;Yes,&quot; and the entire internet froze, displaying a single message: &ldquo;Chaos Mode Activated.&rdquo;\r\n\r\nThat&rsquo;s when everyone realized: the internet was just messing with them. And honestly? It was kind of fun.', '2025-10-05 07:21:44'),
(3, 'Web Dev Project ', 'If you&#039;ve made a website in Web Dev 1 that sells stuff, add your CMS to that for the final project; it looks way better and makes a pretty sweet portfolio website.', '2025-10-05 07:22:00'),
(4, 'Whats up', 'Whats up, Whats going on?', '2025-10-05 07:22:13'),
(5, 'Nuit Blanch Experience ', 'Seduters piciatisun d eomnisi st enat us errorsi tvolupta tema ccusant iumdolo rem quelau dantiu mtotam rem aperia meaq  ueipsaqua eabilloi nventore veritatisetq  architect beatae vitaedicta sunte xplica bo..', '2025-10-05 07:22:28'),
(12, 'Mad Jokes', 'How do you comfort a JavaScript bug?\r\nYou console it.\r\n\r\nHow does a web developer like his coffee?\r\n#000000\r\n\r\nA few developers walk into a bar.\r\nThe bar tender asks “Should I join your table”.\r\n\r\nHistory: Brad Jokes, Bad Jokes, Mad Jokes', '2025-10-06 14:39:25'),
(14, 'Lorem Ipsum', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam tempus mauris a lacus aliquet convallis. Morbi condimentum nisi ut dui tristique, quis maximus lorem dapibus. Maecenas fermentum dolor in risus fringilla consectetur. Nullam ultrices mi lorem, nec finibus massa vehicula eget. Etiam elementum justo risus, vitae tincidunt risus ornare eget. Nullam ac vulputate nunc. Sed lobortis vulputate nibh, non viverra sapien dignissim vehicula. Morbi tincidunt turpis eget tristique egestas.\r\n\r\nAenean consectetur felis egestas facilisis placerat. Etiam sit amet orci eget justo dictum porta sit amet a dolor. Phasellus ac accumsan dui. Sed eget dui nunc. Nulla semper, elit at consequat faucibus, felis lorem tincidunt eros, non imperdiet neque dui in diam. In a aliquam urna. Aenean vitae tristique velit, at tempor massa. Nullam eleifend et leo sit amet posuere. Donec non lectus eros. Cras risus diam, facilisis vel vulputate eget, commodo vitae enim. Sed ultrices metus neque, in porta erat porta vitae. Pellentesque non semper diam, at blandit enim. Aenean justo arcu, fermentum in placerat non, luctus sed ligula. Donec scelerisque dictum velit a euismod. Donec at interdum eros.\r\n\r\nEtiam auctor facilisis tortor nec pellentesque. Aliquam mauris massa, ullamcorper non auctor sodales, vehicula quis massa. Fusce bibendum consectetur nisl quis tempus. Cras augue nunc, ullamcorper ut dui eu, hendrerit elementum dolor. Vestibulum vehicula hendrerit mi, vel mollis metus tempus eget. Donec eget lacinia neque. Nunc cursus augue erat, at eleifend quam varius sit amet. Integer eget dignissim risus.\r\n\r\nQuisque varius vel erat eu lacinia. Nulla id condimentum neque. Duis scelerisque arcu ut velit laoreet, sed venenatis ipsum dapibus. Donec pellentesque venenatis justo at finibus. Vestibulum mi ex, vehicula sed nibh a, placerat dictum felis. Morbi dui tortor, vehicula sit amet rutrum quis, porta vitae nibh. Vivamus vel scelerisque erat, sit amet vestibulum dolor. Aenean in tempor dui. Phasellus porta sed tellus ut efficitur. Nam a turpis est. Vivamus bibendum est nisl, eu facilisis tellus sollicitudin sed. Donec luctus quam vitae sollicitudin luctus. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Fusce sed volutpat erat. Morbi lacinia eu odio quis fermentum.\r\n\r\nIn vel leo est. Nulla in elit vehicula, feugiat mi et, imperdiet eros. Sed ipsum nulla, varius sit amet fringilla eget, tincidunt eu nibh. In in purus rutrum, egestas massa malesuada, consectetur ex. Sed vitae lacinia velit. Maecenas vitae nisl ac magna viverra viverra id ut risus. Ut libero orci, tempor vitae posuere ut, lobortis id purus. Suspendisse tempus lacinia blandit. Quisque fringilla mi quis erat luctus, sit amet commodo felis egestas. Nulla scelerisque non mauris in placerat. Sed nec elit eu purus vulputate condimentum. Duis quam enim, vestibulum ut iaculis a, lobortis vitae velit.', '2025-10-06 17:00:14');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `blog_posts`
--
ALTER TABLE `blog_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
