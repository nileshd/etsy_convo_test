-- phpMyAdmin SQL Dump
-- version 4.2.5
-- http://www.phpmyadmin.net
--
-- Host: localhost:8889
-- Generation Time: Mar 24, 2015 at 04:57 AM
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

CREATE TABLE `convos` (
`id` int(11) unsigned NOT NULL,
  `sender_id` int(11) unsigned NOT NULL,
  `recipient_id` int(11) unsigned NOT NULL,
  `parent_id` int(11) unsigned NOT NULL DEFAULT '0',
  `root_parent_id` int(11) unsigned NOT NULL DEFAULT '0',
  `subject` varchar(140) NOT NULL,
  `body` text NOT NULL,
  `status` enum('read','unread') NOT NULL DEFAULT 'unread',
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
`id` int(11) unsigned NOT NULL,
  `name` varchar(140) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`) VALUES
(1, 'Nilesh'),
(2, 'John');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `convos`
--
ALTER TABLE `convos`
 ADD PRIMARY KEY (`id`), ADD KEY `fk_sender_id` (`sender_id`), ADD KEY `fk_recipient_id` (`recipient_id`), ADD KEY `parent_id` (`parent_id`,`root_parent_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
 ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `convos`
--
ALTER TABLE `convos`
MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `convos`
--
ALTER TABLE `convos`
ADD CONSTRAINT `fk_recipient_id` FOREIGN KEY (`recipient_id`) REFERENCES `users` (`id`),
ADD CONSTRAINT `fk_sender_id` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`);

