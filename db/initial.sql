-- phpMyAdmin SQL Dump
-- version 4.2.5
-- http://www.phpmyadmin.net
--
-- Host: localhost:8889
-- Generation Time: Mar 24, 2015 at 07:40 PM
-- Server version: 5.5.38
-- PHP Version: 5.5.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `etsy_ws`
--

-- --------------------------------------------------------

--
-- Table structure for table `convos`
--

DROP TABLE IF EXISTS `convos`;
CREATE TABLE `convos` (
`id` int(11) unsigned NOT NULL,
  `sender_id` int(11) unsigned NOT NULL,
  `recipient_id` int(11) unsigned NOT NULL,
  `parent_id` int(11) unsigned DEFAULT NULL,
  `root_parent_id` int(11) unsigned DEFAULT NULL,
  `subject` varchar(140) NOT NULL,
  `body` text NOT NULL,
  `status` enum('read','unread') NOT NULL DEFAULT 'unread',
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=22 ;

--
-- Dumping data for table `convos`
--

INSERT INTO `convos` (`id`, `sender_id`, `recipient_id`, `parent_id`, `root_parent_id`, `subject`, `body`, `status`, `date_created`) VALUES
(12, 1, 2, NULL, NULL, 'Message 1 - Top Level', 'Message 1 - Top Level', 'unread', '2015-03-24 18:30:44'),
(13, 2, 1, 12, 12, 'Message 2 - Reply to Message 1 (level 1)', 'Message 2 - Reply to Message 1 (level 1)', 'unread', '2015-03-24 18:31:14'),
(14, 1, 2, 13, 12, 'Message 3 - Reply to Message 2 (level 2)', 'Message 3 - Reply to Message 2 (level 2)', 'unread', '2015-03-24 18:31:52'),
(15, 2, 1, 14, 12, 'Message 4 - Reply to Message 3 (level 3)', 'Message 4 - Reply to Message 3 (level 3)', 'unread', '2015-03-24 18:32:58'),
(16, 1, 2, 15, 12, 'Message 5 - Reply to Message 4 (level 4 (1))', 'Message 5 - Reply to Message 4 (level 4) (1)', 'unread', '2015-03-24 18:35:40'),
(17, 2, 1, 15, 12, 'Message 6 - Reply to Message 4 (level 4 (2))', 'Message 5 - Reply to Message 4 (level 4) (2)', 'unread', '2015-03-24 18:35:40'),
(18, 4, 3, NULL, NULL, 'Second Thread - user 4 to 3', 'Second Thread - user 4 to 3', 'unread', '2015-03-24 18:37:50'),
(19, 3, 4, 18, 18, 'Reply to Second Thread ', '', 'unread', '2015-03-24 18:38:38'),
(20, 3, 2, NULL, NULL, 'User 3 to User 2 - Top Message', 'User 3 to User 2 - Top Message', 'unread', '2015-03-24 18:39:29'),
(21, 2, 3, 20, 20, 'User 2 to User 3 Reply - Level 1', 'User 2 to User 3 - Reply Level 1', 'unread', '2015-03-24 18:40:14');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `convos`
--
ALTER TABLE `convos`
 ADD PRIMARY KEY (`id`), ADD KEY `fk_sender_id` (`sender_id`),
 ADD KEY `fk_recipient_id` (`recipient_id`),
 ADD KEY `parent_id` (`parent_id`,`root_parent_id`),
 ADD KEY `fk_convo_root_parent_id` (`root_parent_id`),
 ADD KEY `status` (`status`) ;


--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `convos`
--
ALTER TABLE `convos`
MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=22;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `convos`
--
ALTER TABLE `convos`
ADD CONSTRAINT `fk_convo_parent_id` FOREIGN KEY (`parent_id`) REFERENCES `convos` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `fk_convo_root_parent_id` FOREIGN KEY (`root_parent_id`) REFERENCES `convos` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `fk_recipient_id` FOREIGN KEY (`recipient_id`) REFERENCES `users` (`id`),
ADD CONSTRAINT `fk_sender_id` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`);
