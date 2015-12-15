-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Dec 15, 2015 at 02:00 AM
-- Server version: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `sample`
--

-- --------------------------------------------------------

--
-- Table structure for table `authorizations`
--

CREATE TABLE IF NOT EXISTS `authorizations` (
  `id_auth` int(11) NOT NULL AUTO_INCREMENT,
  `id_combat` int(11) DEFAULT NULL,
  `name_auth` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id_auth`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `authorizations`
--

INSERT INTO `authorizations` (`id_auth`, `id_combat`, `name_auth`) VALUES
(1, 1, 'Rapier'),
(2, 1, 'Secondary'),
(3, 1, 'Spear'),
(4, 1, 'Cut and Thrust');

-- --------------------------------------------------------

--
-- Table structure for table `awards`
--

CREATE TABLE IF NOT EXISTS `awards` (
  `id_award` int(11) NOT NULL AUTO_INCREMENT,
  `name_award` varchar(45) DEFAULT NULL,
  `award_replaced_by` int(11) DEFAULT NULL,
  `id_kingdom` int(11) DEFAULT NULL,
  `id_group` int(11) DEFAULT NULL,
  `id_rank` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_award`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=214 ;

--
-- Dumping data for table `awards`
--

INSERT INTO `awards` (`id_award`, `name_award`, `award_replaced_by`, `id_kingdom`, `id_group`, `id_rank`) VALUES
(1, 'Sample Award', NULL, 1, -1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `combat`
--

CREATE TABLE IF NOT EXISTS `combat` (
  `id_combat` int(11) NOT NULL AUTO_INCREMENT,
  `name_combat` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id_combat`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `combat`
--

INSERT INTO `combat` (`id_combat`, `name_combat`) VALUES
(1, 'Rapier'),
(2, 'Archery');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE IF NOT EXISTS `events` (
  `id_event` int(11) NOT NULL AUTO_INCREMENT,
  `name_event` varchar(128) NOT NULL,
  `date_event_start` date DEFAULT NULL,
  `date_event_stop` date DEFAULT NULL,
  `id_site` int(11) DEFAULT '0',
  `id_group` int(11) DEFAULT '0',
  PRIMARY KEY (`id_event`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=22 ;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id_event`, `name_event`, `date_event_start`, `date_event_stop`, `id_site`, `id_group`) VALUES
(-1, 'The Very First Event', '1966-05-01', '1966-05-01', -1, -1);

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE IF NOT EXISTS `groups` (
  `id_group` int(11) NOT NULL AUTO_INCREMENT,
  `name_group` varchar(45) DEFAULT NULL,
  `id_kingdom` int(11) DEFAULT '0',
  PRIMARY KEY (`id_group`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=69 ;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id_group`, `name_group`, `id_kingdom`) VALUES
(-1, 'No Group', 0),
(0, 'Unknown', 0),
(1, 'Sample, Barony of', 1);

-- --------------------------------------------------------

--
-- Table structure for table `kingdoms`
--

CREATE TABLE IF NOT EXISTS `kingdoms` (
  `id_kingdom` int(11) NOT NULL AUTO_INCREMENT,
  `name_kingdom` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id_kingdom`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=21 ;

--
-- Dumping data for table `kingdoms`
--

INSERT INTO `kingdoms` (`id_kingdom`, `name_kingdom`) VALUES
(-1, 'All Known Kingdoms'),
(0, 'Unknown'),
(1, 'Æthelmearc'),
(2, 'An Tir'),
(3, 'Ansteorra'),
(4, 'Artemisia'),
(5, 'Atenveldt'),
(6, 'Atlantia'),
(7, 'Avacal'),
(8, 'Caid'),
(9, 'Calontir'),
(10, 'Drachenwald'),
(11, 'Ealdormere'),
(12, 'East'),
(13, 'Gleann Abhann'),
(14, 'Lochac'),
(15, 'Meridies'),
(16, 'Middle'),
(17, 'Northshield'),
(18, 'Outlands'),
(19, 'Trimaris'),
(20, 'West');

-- --------------------------------------------------------

--
-- Table structure for table `marshals`
--

CREATE TABLE IF NOT EXISTS `marshals` (
  `id_marshal` int(11) NOT NULL AUTO_INCREMENT,
  `id_combat` int(11) DEFAULT NULL,
  `name_marshal` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id_marshal`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `marshals`
--

INSERT INTO `marshals` (`id_marshal`, `id_combat`, `name_marshal`) VALUES
(1, 1, 'Rapier Marshal in Training'),
(2, 1, 'Rapier Marshal'),
(3, 1, 'Group Rapier Marshal'),
(4, 1, 'Rapier Authorization Marshal'),
(5, 1, 'Youth Rapier Marshal'),
(6, 1, 'Cut and Thrust Marshal'),
(7, 1, 'Cut and Thrust Authorization Marshal');

-- --------------------------------------------------------

--
-- Table structure for table `persons`
--

CREATE TABLE IF NOT EXISTS `persons` (
  `id_person` int(11) NOT NULL AUTO_INCREMENT,
  `name_person` varchar(128) DEFAULT NULL,
  `id_group` int(11) DEFAULT '0',
  `date_deceased` date DEFAULT NULL,
  `email_person` varchar(128) DEFAULT NULL,
  `name_mundane_person` varchar(128) DEFAULT NULL,
  `membership_person` int(11) DEFAULT NULL,
  `membership_expire_person` date DEFAULT NULL,
  `phone_person` varchar(45) DEFAULT NULL,
  `street_person` varchar(128) DEFAULT NULL,
  `city_person` varchar(45) DEFAULT NULL,
  `state_person` varchar(45) DEFAULT NULL,
  `postcode_person` varchar(45) DEFAULT NULL,
  `active_person` int(11) DEFAULT '1',
  PRIMARY KEY (`id_person`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1439 ;

--
-- Dumping data for table `persons`
--

INSERT INTO `persons` (`id_person`, `name_person`, `id_group`, `date_deceased`, `email_person`, `name_mundane_person`, `membership_person`, `membership_expire_person`, `phone_person`, `street_person`, `city_person`, `state_person`, `postcode_person`, `active_person`) VALUES
(1438, 'Sample Person', 1, NULL, NULL, 'Mundane Person', 1, NULL, NULL, NULL, NULL, NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `persons_authorizations`
--

CREATE TABLE IF NOT EXISTS `persons_authorizations` (
  `id_person_auth` int(11) NOT NULL AUTO_INCREMENT,
  `id_person` int(11) DEFAULT NULL,
  `id_auth` int(11) DEFAULT NULL,
  `expire_auth` date DEFAULT NULL,
  `card_number` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_person_auth`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=198 ;

--
-- Dumping data for table `persons_authorizations`
--

INSERT INTO `persons_authorizations` (`id_person_auth`, `id_person`, `id_auth`, `expire_auth`, `card_number`) VALUES
(1, 1, 4, '2016-04-11', 6);

-- --------------------------------------------------------

--
-- Table structure for table `persons_awards`
--

CREATE TABLE IF NOT EXISTS `persons_awards` (
  `id_person_award` int(11) NOT NULL AUTO_INCREMENT,
  `id_person` int(11) DEFAULT NULL,
  `id_award` int(11) DEFAULT NULL,
  `date_award` date DEFAULT NULL,
  `date_expire` date DEFAULT NULL,
  `date_added` datetime DEFAULT NULL,
  `id_kingdom` int(11) DEFAULT NULL,
  `id_event` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_person_award`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5623 ;

--
-- Dumping data for table `persons_awards`
--

INSERT INTO `persons_awards` (`id_person_award`, `id_person`, `id_award`, `date_award`, `date_expire`, `date_added`, `id_kingdom`, `id_event`) VALUES
(1, 1, 1, '2014-04-04', NULL, '2015-09-29 15:13:24', NULL, -1);

-- --------------------------------------------------------

--
-- Table structure for table `persons_marshals`
--

CREATE TABLE IF NOT EXISTS `persons_marshals` (
  `id_person_marshal` int(11) NOT NULL AUTO_INCREMENT,
  `id_person` int(11) DEFAULT NULL,
  `id_marshal` int(11) DEFAULT NULL,
  `expire_marshal` date DEFAULT NULL,
  `card_number` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_person_marshal`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=82 ;

--
-- Dumping data for table `persons_marshals`
--

INSERT INTO `persons_marshals` (`id_person_marshal`, `id_person`, `id_marshal`, `expire_marshal`, `card_number`) VALUES
(1, 1, 1, '2016-07-24', 1);

-- --------------------------------------------------------

--
-- Table structure for table `ranks`
--

CREATE TABLE IF NOT EXISTS `ranks` (
  `id_rank` int(11) NOT NULL,
  `name_rank` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id_rank`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ranks`
--

INSERT INTO `ranks` (`id_rank`, `name_rank`) VALUES
(1, 'None'),
(2, 'Arms'),
(3, 'Grant'),
(4, 'Patent');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE IF NOT EXISTS `roles` (
  `id_role` int(11) NOT NULL AUTO_INCREMENT,
  `name_role` varchar(45) DEFAULT NULL,
  `desc_role` varchar(128) DEFAULT NULL,
  `id_roletype` int(11) DEFAULT NULL,
  `perm_role` varchar(45) DEFAULT '00',
  PRIMARY KEY (`id_role`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14 ;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id_role`, `name_role`, `desc_role`, `id_roletype`, `perm_role`) VALUES
(1, 'Diamond', 'Principal Herald', 1, '5'),
(2, 'Coal', 'Deputy Principal Herald', 1, '5'),
(3, 'Ruby', 'Heraldic Submissions', 1, '3'),
(4, 'Sardonyx', 'Deputy Heraldic Submissions', 1, '3'),
(5, 'Obsidian', 'Precedence & Protocol', 1, '3'),
(6, 'Emerald', 'Minister of Lists', 1, '3'),
(7, 'Amethyst', 'Regalia', 1, '1'),
(8, 'Topaz', 'Scribal Arts', 1, '3'),
(9, 'Blue Saphyre', 'Heraldic Education', 1, '1'),
(10, 'Webmin', 'Kingdom Webminister', 3, '7'),
(11, 'Squirrel', 'Programming Team', 3, '6'),
(12, 'Rapier', 'Rapier Marshaling', 2, '3'),
(13, 'SiteUpdater', 'Updates sites', 4, '3');

-- --------------------------------------------------------

--
-- Table structure for table `roletypes`
--

CREATE TABLE IF NOT EXISTS `roletypes` (
  `id_roletype` int(11) NOT NULL AUTO_INCREMENT,
  `name_roletype` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id_roletype`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `roletypes`
--

INSERT INTO `roletypes` (`id_roletype`, `name_roletype`) VALUES
(1, 'Herald'),
(2, 'Marshal'),
(3, 'Admin'),
(4, 'Sites');

-- --------------------------------------------------------

--
-- Table structure for table `sites`
--

CREATE TABLE IF NOT EXISTS `sites` (
  `id_site` int(11) NOT NULL AUTO_INCREMENT,
  `name_site` varchar(45) DEFAULT NULL,
  `url_site` varchar(256) DEFAULT NULL,
  `facilities_site` text,
  `capacity_site` int(11) DEFAULT NULL,
  `rates_site` text,
  `area_site` text,
  `contact_site` text,
  `lat_site` decimal(11,8) DEFAULT NULL,
  `long_site` decimal(11,8) DEFAULT NULL,
  `street_site` tinytext,
  `city_site` tinytext,
  `state_site` varchar(2) DEFAULT NULL,
  `zip_site` varchar(5) DEFAULT NULL,
  `verified_site` datetime DEFAULT NULL,
  `active_site` int(11) DEFAULT '1',
  PRIMARY KEY (`id_site`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=177 ;

--
-- Dumping data for table `sites`
--

INSERT INTO `sites` (`id_site`, `name_site`, `url_site`, `facilities_site`, `capacity_site`, `rates_site`, `area_site`, `contact_site`, `lat_site`, `long_site`, `street_site`, `city_site`, `state_site`, `zip_site`, `verified_site`, `active_site`) VALUES
(1, 'Sample Campground', '', 'pool, 4 x 15-man cabins, tenting, hall seats 100, trails, fires in braziers only', 60, '$800+', 'Sample area', '555-1212', NULL, NULL, '123 Any Street', 'Sample City', 'MS', '12345', '2015-11-22 00:00:00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `transaction_log`
--

CREATE TABLE IF NOT EXISTS `transaction_log` (
  `id_transaction` int(11) NOT NULL AUTO_INCREMENT,
  `datetime_transaction` datetime NOT NULL,
  `id_webuser` int(11) DEFAULT NULL,
  `id_role` int(11) DEFAULT NULL,
  `sql_transaction` text,
  PRIMARY KEY (`id_transaction`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=199 ;

--
-- Dumping data for table `transaction_log`
--

INSERT INTO `transaction_log` (`id_transaction`, `datetime_transaction`, `id_webuser`, `id_role`, `sql_transaction`) VALUES
(1, '2015-10-22 10:36:32', 1, 0, 'INSERT INTO Persons_Awards VALUES (NULL, 1309, 67,''2015-07-11'','''',''2015-10-22'', 13 )');

-- --------------------------------------------------------

--
-- Table structure for table `webusers`
--

CREATE TABLE IF NOT EXISTS `webusers` (
  `id_webuser` int(11) NOT NULL AUTO_INCREMENT,
  `name_webuser` varchar(45) DEFAULT NULL,
  `password_webuser` varchar(45) DEFAULT NULL,
  `name_mundane_webuser` varchar(45) DEFAULT NULL,
  `email_webuser` varchar(45) DEFAULT NULL,
  `id_person` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_webuser`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `webusers`
--

INSERT INTO `webusers` (`id_webuser`, `name_webuser`, `password_webuser`, `name_mundane_webuser`, `email_webuser`, `id_person`) VALUES
(1, 'sample_account', '', 'Sample User', 'email@domain.com', 0);

-- --------------------------------------------------------

--
-- Table structure for table `webusers_roles`
--

CREATE TABLE IF NOT EXISTS `webusers_roles` (
  `id_webuser_role` int(11) NOT NULL AUTO_INCREMENT,
  `id_webuser` int(11) DEFAULT NULL,
  `id_role` int(11) DEFAULT NULL,
  `expire_role` date DEFAULT NULL,
  PRIMARY KEY (`id_webuser_role`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `webusers_roles`
--

INSERT INTO `webusers_roles` (`id_webuser_role`, `id_webuser`, `id_role`, `expire_role`) VALUES
(1, 1, 5, '2018-01-01');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
