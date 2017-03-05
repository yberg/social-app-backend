-- phpMyAdmin SQL Dump
-- version 4.2.12deb2+deb8u1
-- http://www.phpmyadmin.net
--
-- Värd: localhost
-- Tid vid skapande: 25 mars 2016 kl 23:20
-- Serverversion: 5.5.44-0+deb8u1
-- PHP-version: 5.6.17-0+deb8u1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+01:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Databas: `app`
--

-- --------------------------------------------------------

--
-- Tabellstruktur `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
`cid` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `text` text NOT NULL,
  `commented` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=latin1;

--
-- Dumpning av Data i tabell `comments`
--
/*
INSERT INTO `comments` (`cid`, `pid`, `uid`, `text`, `commented`) VALUES
(1, 4, 64, 'God jul på dig Arne!', '2016-03-13 14:32:08'),
(2, 4, 65, 'God jul!', '2016-03-13 14:58:27'),
(3, 2, 66, 'Va?', '2016-03-13 16:56:26'),
(18, 2, 66, 'Hallå?', '2016-03-23 19:53:58');
*/

-- --------------------------------------------------------

--
-- Tabellstruktur `posts`
--

CREATE TABLE IF NOT EXISTS `posts` (
`pid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `text` text NOT NULL,
  `image` text,
  `posted` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `edited` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=latin1;

--
-- Dumpning av Data i tabell `posts`
--

/*
INSERT INTO `posts` (`pid`, `uid`, `text`, `image`, `posted`, `edited`) VALUES
(1, 65, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', NULL, '2016-03-08 22:59:33', NULL),
(2, 65, 'Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.', NULL, '2016-03-08 22:59:44', NULL),
(4, 66, 'Från oss alla, till er alla.\r\nEn riktigt god jul.', NULL, '2016-03-08 23:08:22', NULL),
(48, 64, 'Tjo', NULL, '2016-03-25 22:55:02', NULL);
*/

-- --------------------------------------------------------

--
-- Tabellstruktur `relations`
--

CREATE TABLE IF NOT EXISTS `relations` (
`rid` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `follows` int(11) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=213 DEFAULT CHARSET=latin1;

--
-- Dumpning av Data i tabell `relations`
--

/*
INSERT INTO `relations` (`rid`, `user`, `follows`, `time`) VALUES
(1, 64, 65, '2016-03-08 22:59:17'),
(3, 65, 66, '2016-03-08 23:08:41'),
(201, 64, 66, '2016-03-25 00:19:03'),
(205, 66, 66, '2016-03-25 01:46:35'),
(212, 64, 64, '2016-03-25 23:00:34');
*/

-- --------------------------------------------------------

--
-- Tabellstruktur `users`
--

CREATE TABLE IF NOT EXISTS `users` (
`uid` int(11) NOT NULL,
  `username` varchar(16) NOT NULL,
  `first_name` varchar(20) NOT NULL,
  `last_name` varchar(20) NOT NULL,
  `email` varchar(64) NOT NULL,
  `image` varchar(128) DEFAULT NULL,
  `password` char(60) NOT NULL,
  `joined` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=76 DEFAULT CHARSET=latin1;

--
-- Dumpning av Data i tabell `users`
--

/*
INSERT INTO `users` (`uid`, `username`, `first_name`, `last_name`, `email`, `image`, `password`, `joined`) VALUES
(64, 'yberg', 'Viktor', 'Yberg', 'yberg@kth.se', NULL, '$2y$11$4.JaaBtYPUw27PPj/v8Sk.gx1XlZ00nwK/mplHj7jyNisChMoA5YC', '2016-03-24 02:54:42'),
(65, 'psvans', 'Pelle', 'Svanslös', 'pelle@svanslos.se', NULL, '$2y$11$hGFLukP6hK3ff4R3lRLnxOBThB2VsVC9pCpRT0q1N6ZTnl15y/0Iu', '2016-03-08 22:58:51'),
(66, 'aweise', 'Arne', 'Weise', 'arne@spray.se', NULL, '$2y$11$xwgfwaed3x6jKiJnBX969.P.igG3OkwWyR9etjOwF51kZj11IocXa', '2016-03-25 01:11:10'),
(67, 'alfons', 'Alfons', 'Åberg', 'alfons@åberg.se', NULL, '$2y$11$azYrxIzpIO8JIAWsQt35H.IH7mk9SlhNUOG.rhi..QA4XA9LlXsQu', '2016-03-12 16:00:32');
*/

-- --------------------------------------------------------

--
-- Tabellstruktur `votes`
--

CREATE TABLE IF NOT EXISTS `votes` (
`vid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `type` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=205 DEFAULT CHARSET=latin1;

--
-- Dumpning av Data i tabell `votes`
--

/*
INSERT INTO `votes` (`vid`, `uid`, `pid`, `type`) VALUES
(158, 66, 4, 1),
(203, 64, 4, 1);
*/

--
-- Index för dumpade tabeller
--

--
-- Index för tabell `comments`
--
ALTER TABLE `comments`
 ADD PRIMARY KEY (`cid`), ADD KEY `pid` (`pid`), ADD KEY `uid` (`uid`);

--
-- Index för tabell `posts`
--
ALTER TABLE `posts`
 ADD PRIMARY KEY (`pid`), ADD KEY `uid` (`uid`);

--
-- Index för tabell `relations`
--
ALTER TABLE `relations`
 ADD PRIMARY KEY (`rid`), ADD KEY `user` (`user`), ADD KEY `follows` (`follows`);

--
-- Index för tabell `users`
--
ALTER TABLE `users`
 ADD PRIMARY KEY (`uid`), ADD UNIQUE KEY `username` (`username`);

--
-- Index för tabell `votes`
--
ALTER TABLE `votes`
 ADD PRIMARY KEY (`vid`), ADD KEY `uid` (`uid`), ADD KEY `pid` (`pid`);

--
-- AUTO_INCREMENT för dumpade tabeller
--

--
-- AUTO_INCREMENT för tabell `comments`
--
ALTER TABLE `comments`
MODIFY `cid` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=28;
--
-- AUTO_INCREMENT för tabell `posts`
--
ALTER TABLE `posts`
MODIFY `pid` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=49;
--
-- AUTO_INCREMENT för tabell `relations`
--
ALTER TABLE `relations`
MODIFY `rid` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=213;
--
-- AUTO_INCREMENT för tabell `users`
--
ALTER TABLE `users`
MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=76;
--
-- AUTO_INCREMENT för tabell `votes`
--
ALTER TABLE `votes`
MODIFY `vid` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=205;
--
-- Restriktioner för dumpade tabeller
--

--
-- Restriktioner för tabell `comments`
--
ALTER TABLE `comments`
ADD CONSTRAINT `comments_ibfk_5` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `comments_ibfk_6` FOREIGN KEY (`pid`) REFERENCES `posts` (`pid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restriktioner för tabell `posts`
--
ALTER TABLE `posts`
ADD CONSTRAINT `posts_ibfk_2` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restriktioner för tabell `relations`
--
ALTER TABLE `relations`
ADD CONSTRAINT `relations_ibfk_8` FOREIGN KEY (`user`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `relations_ibfk_7` FOREIGN KEY (`follows`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restriktioner för tabell `votes`
--
ALTER TABLE `votes`
ADD CONSTRAINT `votes_ibfk_8` FOREIGN KEY (`pid`) REFERENCES `posts` (`pid`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `votes_ibfk_7` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
