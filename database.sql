-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 01, 2020 at 05:18 PM
-- Server version: 10.4.10-MariaDB
-- PHP Version: 7.3.12

-- SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
-- SET AUTOCOMMIT = 0;
-- START TRANSACTION;
-- SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `440_blog`
--

-- --------------------------------------------------------

--
-- Table structure for table `blogs`
--

DROP TABLE IF EXISTS `blogs`;
CREATE TABLE IF NOT EXISTS `blogs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `tags` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `blogs`
--

INSERT INTO `blogs` (`id`, `user_id`, `subject`, `description`, `tags`, `created_at`) VALUES
(1, 1, 'Rabbits', 'Rabbits are small mammals in the family Leporidae of the order Lagomorpha (along with the hare and the pika). Oryctolagus cuniculus includes the European rabbit species and its descendants, the worlds 305 breeds of domestic rabbit. Sylvilagus includes 13 wild rabbit species, among them the seven types of cottontail.', 'Cute, Fluffy, Small', '2020-12-01 16:59:55'),
(2, 2, 'Thanksgiving', 'Thanksgiving is a national holiday celebrated on various dates in the United States, Canada, Grenada, Saint Lucia, and Liberia. It began as a day of giving thanks and sacrifice for the blessing of the harvest and of the preceding year. Similarly named festival holidays occur in Germany and Japan.', 'Family, Eating, Covid-19', '2020-12-01 16:59:55'),
(3, 3, 'Christmas', 'Christmas (or Feast of the Nativity) is an annual festival commemorating the birth of Jesus Christ, observed primarily on December 25 as a religious and cultural celebration among billions of people around the world.', 'Family, Cold, Covid-19', '2020-12-01 16:59:55'),
(4, 4, 'Soobuwus', 'Subaru is the automobile manufacturing division of Japanese transportation conglomerate Subaru Corporation, the twenty-second largest automaker by production worldwide in 2012. Subaru cars are known for their use of a boxer engine layout in most vehicles above 1500 cc.', 'Car, WRX, 4-Cylinder', '2020-12-01 16:59:55'),
(5, 5, 'Twitch', 'Twitch is where millions of people come together live every day to chat, interact, and make their own entertainment together.', 'Livestream, Gaming, Community', '2020-12-02 16:59:55'),
(6, 6, 'Discord', 'Discord is an American VoIP, instant messaging and digital distribution platform designed for creating communities. Users communicate with voice calls, video calls, text messaging, media and files in private chats or as part of communities called "servers."', 'Gaming, Chatting, Community', '2020-12-02 16:59:55'),
(7, 7, 'Lorem', 'Lorem ipsum dolor sit amet, ut civibus luptatum intellegat mei, sit et affert luptatum. At falli scripta sed, vis tollit praesent eu. Et enim moderatius eos, in vix aliquid facilis, eos saepe docendi id. Feugait vulputate pro eu, et vix eripuit deleniti nominati. Et vel efficiendi comprehensam conclusionemque."', 'Lorem, Text, Filler', '2020-10-10 16:59:55'),
(8, 7, 'Bacon', 'Bacon ipsum dolor amet ribeye jowl buffalo, frankfurter cow tenderloin leberkas spare ribs turkey biltong chicken beef. Cow boudin leberkas pork corned beef swine. Ground round ball tip ham hock venison, meatball drumstick biltong tri-tip doner strip steak capicola shank.', 'Bacon, Food, Tasty', '2020-10-10 16:59:55');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
CREATE TABLE IF NOT EXISTS `comments` (
  `commentid` int(11) NOT NULL AUTO_INCREMENT,
  `description` text NOT NULL,
  `sentiment` enum('Positive','Negative') NOT NULL DEFAULT 'Positive',
  `blog_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`commentid`) USING BTREE,
  KEY `blog_id` (`blog_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`commentid`, `description`, `sentiment`, `blog_id`, `user_id`, `created_at`) VALUES
(1, 'I love Hawaii, relaxing place.', 'Positive', 2, 1, '2020-12-01 17:11:22');

-- --------------------------------------------------------

--
-- Table structure for table `followers`
--

DROP TABLE IF EXISTS `followers`;
CREATE TABLE IF NOT EXISTS `followers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(100) NOT NULL,
  `follower` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user` (`user`) USING BTREE,
  KEY `follower` (`follower`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `followers`
--

INSERT INTO `followers` (`id`, `user`, `follower`) VALUES
(1, 'Eric', 'Mitch'),
(2, 'Eric', 'Dwight'),
(3, 'DJ', 'Roni'),
(4, 'DJ', 'Matt'),
(5, 'Andy', 'Sarah'),
(6, 'Andy', 'Roni');
-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `hobbies` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `username`, `email`, `password`, `hobbies`, `created_at`) VALUES
(1, 'Eric', 'Crenshaw', 'Eric', 'sqlgod@email.com', '$2y$10$PDx9fcuXzD3KKhIemtqUIe2b02Rwz2rMbB4d1lnKvIdEm6ZHW6UVC', NULL, '2020-12-01 16:12:47'),
(2, 'Matt', 'Sercena', 'Matt', 'hawaiianvibes@email.com', '$2y$10$PDx9fcuXzD3KKhIemtqUIe2b02Rwz2rMbB4d1lnKvIdEm6ZHW6UVC', NULL, '2020-12-01 16:12:47'),
(3, 'Mitch', 'Kim', 'Mitch', 'mr.amazon@email.com', '$2y$10$2cwMD8yf.3OHS4H8vd6nIe9uQsKMo2GJcZ7rwHYnU/s4JcUBVXvT6', NULL, '2020-12-01 16:12:47'),
(4, 'Dwight', 'Marucut', 'Dwight', 'soobuwukid@email.com', '$2y$10$4EWmFI/K9uNv5vrhSPOYl.Jm2I.ZCG1sq7Q.X6CEQrWXwRTCFjhy2', NULL, '2020-12-01 16:12:47'),
(5, 'DJ', 'Ramirez', 'DJ', 'valorant_radiant@email.com', '$2y$10$9BWJ.RVuc6gBhrH4X3AhdugvxLN7LTM6Rf4REX66DO8IJa57wKVKS', NULL, '2020-12-01 16:12:47'),
(6, 'Roni', 'Hang', 'Roni', 'keyboardrager@email.com', '$2y$10$wBBT2R3w/tLgO.V19WDwAeiRwhSLlytt6rHbftQNoCoMq7QdE.ma.', NULL, '2020-12-01 16:12:47'),
(7, 'Raf', 'Chua', 'Raf', 'writeronly@email.com', '$2y$10$YBsfNQEylOCDFxysuBQVqOI2J78uJZsppZI2PfcZFkQTEv2fo2CK2', NULL, '2020-12-01 16:12:47'),
(8, 'John', 'Valan', 'John', 'genshin24/7@email.com', '$2y$10$2adAAGeGpG.F5JNBBIGkMu.Uz2chH0ZHxtrs9kakFD1GexUWxZgiC', NULL, '2020-12-01 16:12:48'),
(9, 'Sarah', 'Kim', 'Sarah', 'mitchlover@email.com', '$2y$10$lTxCL8T8j5XyprVC.7LTL.c/gqfgzcWbu9vxyvVtqoPCW2ogZV3si', NULL, '2020-12-01 16:12:48'),
(10, 'Roderick', 'Montogomery', 'Roderick', 'roderickmont12@email.com', '$2y$10$K5FguWjqr.HVSl8cLu2Qve1D/BkEi0hgXEBSH22I0zbdmp0YjtyBy', NULL, '2020-12-01 16:12:48'),
(11, 'Andy', 'Takemoto', 'Andy', 'notdiamondyetinvalorant@email.com', '$2y$10$CjR1f8PvRNO6ekBuv0JPGOl1YZEVm7Bda14CGNY6Zi3TYcU54Y8Ge', NULL, '2020-12-01 16:12:48');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
