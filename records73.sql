-- MySQL dump 10.19  Distrib 10.3.34-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: records
-- ------------------------------------------------------
-- Server version	10.3.34-MariaDB-0ubuntu0.20.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `Armorials`
--

DROP TABLE IF EXISTS `Armorials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Armorials` (
  `id_armorial` int(11) NOT NULL AUTO_INCREMENT,
  `blazon_armorial` text DEFAULT NULL,
  `image_armorial` mediumblob DEFAULT NULL,
  `fname_armorial` varchar(128) DEFAULT NULL,
  `fsize_armorial` int(11) DEFAULT NULL,
  `ftype_armorial` varchar(45) DEFAULT NULL,
  `timestamp_armorial` datetime DEFAULT NULL,
  PRIMARY KEY (`id_armorial`)
) ENGINE=MyISAM AUTO_INCREMENT=82 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Authorizations`
--

DROP TABLE IF EXISTS `Authorizations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Authorizations` (
  `id_auth` int(11) NOT NULL AUTO_INCREMENT,
  `id_combat` int(11) DEFAULT NULL,
  `name_auth` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id_auth`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Awards`
--

DROP TABLE IF EXISTS `Awards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Awards` (
  `id_award` int(11) NOT NULL AUTO_INCREMENT,
  `name_award` varchar(45) DEFAULT NULL,
  `award_replaced_by` int(11) DEFAULT NULL,
  `id_kingdom` int(11) DEFAULT NULL,
  `id_group` int(11) DEFAULT NULL,
  `id_rank` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_award`)
) ENGINE=InnoDB AUTO_INCREMENT=315 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Combat`
--

DROP TABLE IF EXISTS `Combat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Combat` (
  `id_combat` int(11) NOT NULL AUTO_INCREMENT,
  `name_combat` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id_combat`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Events`
--

DROP TABLE IF EXISTS `Events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Events` (
  `id_event` int(11) NOT NULL AUTO_INCREMENT,
  `name_event` varchar(128) NOT NULL,
  `date_event_start` date DEFAULT NULL,
  `date_event_stop` date DEFAULT NULL,
  `id_site` int(11) DEFAULT 0,
  `id_group` int(11) DEFAULT 0,
  PRIMARY KEY (`id_event`)
) ENGINE=MyISAM AUTO_INCREMENT=322 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Groups`
--

DROP TABLE IF EXISTS `Groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Groups` (
  `id_group` int(11) NOT NULL AUTO_INCREMENT,
  `name_group` varchar(45) DEFAULT NULL,
  `id_kingdom` int(11) DEFAULT 0,
  PRIMARY KEY (`id_group`)
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Kingdoms`
--

DROP TABLE IF EXISTS `Kingdoms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Kingdoms` (
  `id_kingdom` int(11) NOT NULL AUTO_INCREMENT,
  `name_kingdom` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id_kingdom`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Marshals`
--

DROP TABLE IF EXISTS `Marshals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Marshals` (
  `id_marshal` int(11) NOT NULL AUTO_INCREMENT,
  `id_combat` int(11) DEFAULT NULL,
  `name_marshal` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id_marshal`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Persons`
--

DROP TABLE IF EXISTS `Persons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Persons` (
  `id_person` int(11) NOT NULL AUTO_INCREMENT,
  `name_person` varchar(128) DEFAULT NULL,
  `id_group` int(11) DEFAULT 0,
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
  `active_rapier_person` enum('Yes','No') DEFAULT 'No',
  `waiver_person` enum('Yes','No','Parent') DEFAULT 'No',
  `youth_person` enum('Yes','No') DEFAULT 'No',
  `birthdate_person` date DEFAULT NULL,
  PRIMARY KEY (`id_person`)
) ENGINE=InnoDB AUTO_INCREMENT=2062 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Persons_Armorials`
--

DROP TABLE IF EXISTS `Persons_Armorials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Persons_Armorials` (
  `id_person_armorial` int(11) NOT NULL AUTO_INCREMENT,
  `id_person` int(11) NOT NULL,
  `id_armorial` int(11) NOT NULL,
  `type_armorial` enum('device','badge','household') NOT NULL DEFAULT 'device',
  PRIMARY KEY (`id_person_armorial`)
) ENGINE=MyISAM AUTO_INCREMENT=82 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Persons_Authorizations`
--

DROP TABLE IF EXISTS `Persons_Authorizations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Persons_Authorizations` (
  `id_person_auth` int(11) NOT NULL AUTO_INCREMENT,
  `id_person` int(11) DEFAULT NULL,
  `id_auth` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_person_auth`)
) ENGINE=InnoDB AUTO_INCREMENT=1176 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Persons_Awards`
--

DROP TABLE IF EXISTS `Persons_Awards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Persons_Awards` (
  `id_person_award` int(11) NOT NULL AUTO_INCREMENT,
  `id_person` int(11) DEFAULT NULL,
  `id_award` int(11) DEFAULT NULL,
  `date_award` date DEFAULT NULL,
  `date_expire` date DEFAULT NULL,
  `date_added` datetime DEFAULT NULL,
  `id_kingdom` int(11) DEFAULT NULL,
  `id_event` int(11) DEFAULT -1,
  PRIMARY KEY (`id_person_award`)
) ENGINE=InnoDB AUTO_INCREMENT=7734 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Persons_CombatCards`
--

DROP TABLE IF EXISTS `Persons_CombatCards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Persons_CombatCards` (
  `id_person_combat_card` int(11) NOT NULL AUTO_INCREMENT,
  `id_person` int(11) DEFAULT NULL,
  `id_combat` int(11) DEFAULT NULL,
  `card_authorize` int(11) DEFAULT NULL,
  `card_marshal` int(11) DEFAULT NULL,
  `expire_authorize` date DEFAULT NULL,
  `expire_marshal` date DEFAULT NULL,
  `issue_authorize` date DEFAULT NULL,
  `issue_marshal` date DEFAULT NULL,
  `note_marshal` text DEFAULT NULL,
  `note_authorize` text DEFAULT NULL,
  `active_authorize` enum('Yes','No') DEFAULT 'No',
  `active_marshal` enum('Yes','No') DEFAULT 'No',
  PRIMARY KEY (`id_person_combat_card`)
) ENGINE=InnoDB AUTO_INCREMENT=913 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Persons_Marshals`
--

DROP TABLE IF EXISTS `Persons_Marshals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Persons_Marshals` (
  `id_person_marshal` int(11) NOT NULL AUTO_INCREMENT,
  `id_person` int(11) DEFAULT NULL,
  `id_marshal` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_person_marshal`)
) ENGINE=InnoDB AUTO_INCREMENT=663 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Ranks`
--

DROP TABLE IF EXISTS `Ranks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Ranks` (
  `id_rank` int(11) NOT NULL,
  `name_rank` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id_rank`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `RoleTypes`
--

DROP TABLE IF EXISTS `RoleTypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `RoleTypes` (
  `id_roletype` int(11) NOT NULL AUTO_INCREMENT,
  `name_roletype` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id_roletype`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Roles`
--

DROP TABLE IF EXISTS `Roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Roles` (
  `id_role` int(11) NOT NULL AUTO_INCREMENT,
  `name_role` varchar(45) DEFAULT NULL,
  `desc_role` varchar(128) DEFAULT NULL,
  `id_roletype` int(11) DEFAULT NULL,
  `perm_role` varchar(45) DEFAULT '00',
  PRIMARY KEY (`id_role`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Sites`
--

DROP TABLE IF EXISTS `Sites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Sites` (
  `id_site` int(11) NOT NULL AUTO_INCREMENT,
  `name_site` varchar(45) DEFAULT NULL,
  `url_site` varchar(256) DEFAULT NULL,
  `facilities_site` text DEFAULT NULL,
  `capacity_site` text DEFAULT NULL,
  `rates_site` text DEFAULT NULL,
  `area_site` text DEFAULT NULL,
  `contact_site` text DEFAULT NULL,
  `lat_site` decimal(11,8) DEFAULT NULL,
  `long_site` decimal(11,8) DEFAULT NULL,
  `street_site` tinytext DEFAULT NULL,
  `city_site` tinytext DEFAULT NULL,
  `state_site` varchar(2) DEFAULT NULL,
  `zip_site` varchar(5) DEFAULT NULL,
  `verified_site` datetime DEFAULT NULL,
  `active_site` int(11) DEFAULT 1,
  `verify_phone_site` date DEFAULT NULL,
  `verify_web_site` date DEFAULT NULL,
  `verify_visit_site` date DEFAULT NULL,
  `kingdom_level_site` enum('Yes','No') DEFAULT 'No',
  `notes_site` text DEFAULT NULL,
  PRIMARY KEY (`id_site`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Sites_Old`
--

DROP TABLE IF EXISTS `Sites_Old`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Sites_Old` (
  `id_site` int(11) NOT NULL AUTO_INCREMENT,
  `name_site` varchar(45) DEFAULT NULL,
  `url_site` varchar(256) DEFAULT NULL,
  `facilities_site` text DEFAULT NULL,
  `capacity_site` text DEFAULT NULL,
  `rates_site` text DEFAULT NULL,
  `area_site` text DEFAULT NULL,
  `contact_site` text DEFAULT NULL,
  `lat_site` decimal(11,8) DEFAULT NULL,
  `long_site` decimal(11,8) DEFAULT NULL,
  `street_site` tinytext DEFAULT NULL,
  `city_site` tinytext DEFAULT NULL,
  `state_site` varchar(2) DEFAULT NULL,
  `zip_site` varchar(5) DEFAULT NULL,
  `verified_site` datetime DEFAULT NULL,
  `active_site` int(11) DEFAULT 1,
  `verify_phone_site` date DEFAULT NULL,
  `verify_web_site` date DEFAULT NULL,
  `verify_visit_site` date DEFAULT NULL,
  `kingdom_level_site` enum('Yes','No') DEFAULT 'No',
  PRIMARY KEY (`id_site`)
) ENGINE=MyISAM AUTO_INCREMENT=192 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Transaction_Log`
--

DROP TABLE IF EXISTS `Transaction_Log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Transaction_Log` (
  `id_transaction` int(11) NOT NULL AUTO_INCREMENT,
  `datetime_transaction` datetime NOT NULL,
  `id_webuser` int(11) DEFAULT NULL,
  `id_role` int(11) DEFAULT NULL,
  `sql_transaction` text DEFAULT NULL,
  PRIMARY KEY (`id_transaction`)
) ENGINE=MyISAM AUTO_INCREMENT=5877 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `WebUsers`
--

DROP TABLE IF EXISTS `WebUsers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `WebUsers` (
  `id_webuser` int(11) NOT NULL AUTO_INCREMENT,
  `name_webuser` varchar(45) DEFAULT NULL,
  `password_webuser` varchar(45) DEFAULT NULL,
  `name_mundane_webuser` varchar(45) DEFAULT NULL,
  `email_webuser` varchar(45) DEFAULT NULL,
  `id_person` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_webuser`)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Webusers_Roles`
--

DROP TABLE IF EXISTS `Webusers_Roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Webusers_Roles` (
  `id_webuser_role` int(11) NOT NULL AUTO_INCREMENT,
  `id_webuser` int(11) DEFAULT NULL,
  `id_role` int(11) DEFAULT NULL,
  `expire_role` date DEFAULT NULL,
  PRIMARY KEY (`id_webuser_role`)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2022-06-06 19:47:07
