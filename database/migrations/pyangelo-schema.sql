/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19-11.8.2-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: pyangelo
-- ------------------------------------------------------
-- Server version	11.8.2-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;

--
-- Table structure for table `autoresponder`
--

DROP TABLE IF EXISTS `autoresponder`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `autoresponder` (
  `autoresponder_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `segment_id` tinyint(3) unsigned NOT NULL,
  `from_email_id` smallint(5) unsigned NOT NULL,
  `subject` varchar(200) NOT NULL,
  `body_text` text NOT NULL,
  `body_html` text NOT NULL,
  `duration` int(10) unsigned NOT NULL,
  `period` varchar(50) NOT NULL,
  `delay_in_minutes` int(10) unsigned NOT NULL,
  `active` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`autoresponder_id`),
  KEY `segment_id` (`segment_id`),
  KEY `from_email_id` (`from_email_id`),
  CONSTRAINT `autoresponder_ibfk_1` FOREIGN KEY (`segment_id`) REFERENCES `segment` (`segment_id`),
  CONSTRAINT `autoresponder_ibfk_2` FOREIGN KEY (`from_email_id`) REFERENCES `from_email` (`from_email_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='PyAngelo Autoreponder emails.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `autoresponder_activity`
--

DROP TABLE IF EXISTS `autoresponder_activity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `autoresponder_activity` (
  `activity_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `autoresponder_id` int(10) unsigned NOT NULL,
  `person_id` int(10) unsigned NOT NULL,
  `activity_type_id` smallint(5) unsigned NOT NULL,
  `created_at` datetime NOT NULL,
  `aws_message_id` varchar(100) DEFAULT NULL,
  `link_id` int(10) unsigned DEFAULT NULL,
  `bounce_type_id` smallint(5) unsigned DEFAULT NULL,
  PRIMARY KEY (`activity_id`),
  UNIQUE KEY `aws_message` (`aws_message_id`),
  KEY `person` (`person_id`),
  KEY `autoresponder_person` (`autoresponder_id`,`person_id`,`created_at`),
  KEY `activity_type_id` (`activity_type_id`),
  KEY `link_id` (`link_id`),
  KEY `bounce_type_id` (`bounce_type_id`),
  CONSTRAINT `autoresponder_activity_ibfk_1` FOREIGN KEY (`autoresponder_id`) REFERENCES `autoresponder` (`autoresponder_id`),
  CONSTRAINT `autoresponder_activity_ibfk_2` FOREIGN KEY (`person_id`) REFERENCES `person` (`person_id`),
  CONSTRAINT `autoresponder_activity_ibfk_3` FOREIGN KEY (`activity_type_id`) REFERENCES `email_activity_type` (`activity_type_id`),
  CONSTRAINT `autoresponder_activity_ibfk_4` FOREIGN KEY (`link_id`) REFERENCES `trackable_link` (`link_id`),
  CONSTRAINT `autoresponder_activity_ibfk_5` FOREIGN KEY (`bounce_type_id`) REFERENCES `bounce_type` (`bounce_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Holds all activities that people take on our autoresponder emails.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `blog`
--

DROP TABLE IF EXISTS `blog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `blog` (
  `blog_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `person_id` int(10) unsigned NOT NULL,
  `title` varchar(100) NOT NULL,
  `preview` varchar(1000) NOT NULL,
  `content` text NOT NULL,
  `slug` varchar(105) NOT NULL,
  `blog_image` varchar(255) NOT NULL,
  `blog_category_id` smallint(5) unsigned NOT NULL,
  `featured` tinyint(1) NOT NULL,
  `published` tinyint(1) NOT NULL,
  `published_at` datetime NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`blog_id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `person` (`person_id`),
  KEY `published_at` (`published_at`),
  KEY `featured` (`featured`),
  KEY `blog_category_id` (`blog_category_id`),
  CONSTRAINT `blog_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `person` (`person_id`),
  CONSTRAINT `blog_ibfk_2` FOREIGN KEY (`blog_category_id`) REFERENCES `blog_category` (`blog_category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Holds all PyAngelo blog posts.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `blog_alert`
--

DROP TABLE IF EXISTS `blog_alert`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `blog_alert` (
  `blog_id` int(10) unsigned NOT NULL,
  `person_id` int(10) unsigned NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`blog_id`,`person_id`),
  KEY `person_id` (`person_id`),
  CONSTRAINT `blog_alert_ibfk_1` FOREIGN KEY (`blog_id`) REFERENCES `blog` (`blog_id`),
  CONSTRAINT `blog_alert_ibfk_2` FOREIGN KEY (`person_id`) REFERENCES `person` (`person_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Alert the user if a comment is added to this blog.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `blog_category`
--

DROP TABLE IF EXISTS `blog_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `blog_category` (
  `blog_category_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(30) NOT NULL,
  PRIMARY KEY (`blog_category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='The different categories a blog will be related to.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `blog_comment`
--

DROP TABLE IF EXISTS `blog_comment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `blog_comment` (
  `comment_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `blog_id` int(10) unsigned NOT NULL,
  `person_id` int(10) unsigned NOT NULL,
  `blog_comment` text NOT NULL,
  `published` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`comment_id`),
  KEY `blog_comment` (`blog_id`,`created_at`),
  KEY `person_comment` (`person_id`),
  CONSTRAINT `blog_comment_ibfk_1` FOREIGN KEY (`blog_id`) REFERENCES `blog` (`blog_id`),
  CONSTRAINT `blog_comment_ibfk_2` FOREIGN KEY (`person_id`) REFERENCES `person` (`person_id`)
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Stores comments for a blog.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `blog_image`
--

DROP TABLE IF EXISTS `blog_image`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `blog_image` (
  `image_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `image_name` varchar(255) NOT NULL,
  `image_width` smallint(5) unsigned NOT NULL,
  `image_height` smallint(5) unsigned NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`image_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Stores images which can be used in a PyAngelo blog.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bounce_type`
--

DROP TABLE IF EXISTS `bounce_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `bounce_type` (
  `bounce_type_id` smallint(5) unsigned NOT NULL,
  `bounce_type` varchar(20) NOT NULL,
  `bounce_sub_type` varchar(20) NOT NULL,
  PRIMARY KEY (`bounce_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='These are the different bounce types from Amazon SES.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `campaign`
--

DROP TABLE IF EXISTS `campaign`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `campaign` (
  `campaign_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `campaign_status_id` tinyint(3) unsigned NOT NULL,
  `segment_id` tinyint(3) unsigned NOT NULL,
  `from_email_id` smallint(5) unsigned NOT NULL,
  `subject` varchar(200) NOT NULL,
  `body_text` text NOT NULL,
  `body_html` text NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`campaign_id`),
  KEY `campaign_status_id` (`campaign_status_id`),
  KEY `from_email_id` (`from_email_id`),
  KEY `segment_id` (`segment_id`),
  CONSTRAINT `campaign_ibfk_1` FOREIGN KEY (`campaign_status_id`) REFERENCES `campaign_status` (`campaign_status_id`),
  CONSTRAINT `campaign_ibfk_3` FOREIGN KEY (`from_email_id`) REFERENCES `from_email` (`from_email_id`),
  CONSTRAINT `campaign_ibfk_4` FOREIGN KEY (`segment_id`) REFERENCES `segment` (`segment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Captions for each lesson.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `campaign_activity`
--

DROP TABLE IF EXISTS `campaign_activity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `campaign_activity` (
  `activity_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `campaign_id` int(10) unsigned NOT NULL,
  `person_id` int(10) unsigned NOT NULL,
  `activity_type_id` smallint(5) unsigned NOT NULL,
  `created_at` datetime NOT NULL,
  `aws_message_id` varchar(100) DEFAULT NULL,
  `link_id` int(10) unsigned DEFAULT NULL,
  `bounce_type_id` smallint(5) unsigned DEFAULT NULL,
  PRIMARY KEY (`activity_id`),
  UNIQUE KEY `aws_message` (`aws_message_id`),
  KEY `person` (`person_id`),
  KEY `campaign_person` (`campaign_id`,`person_id`,`created_at`),
  KEY `activity_type_id` (`activity_type_id`),
  KEY `link_id` (`link_id`),
  KEY `bounce_type_id` (`bounce_type_id`),
  CONSTRAINT `campaign_activity_ibfk_1` FOREIGN KEY (`campaign_id`) REFERENCES `campaign` (`campaign_id`),
  CONSTRAINT `campaign_activity_ibfk_2` FOREIGN KEY (`person_id`) REFERENCES `person` (`person_id`),
  CONSTRAINT `campaign_activity_ibfk_3` FOREIGN KEY (`activity_type_id`) REFERENCES `email_activity_type` (`activity_type_id`),
  CONSTRAINT `campaign_activity_ibfk_4` FOREIGN KEY (`link_id`) REFERENCES `trackable_link` (`link_id`),
  CONSTRAINT `campaign_activity_ibfk_5` FOREIGN KEY (`bounce_type_id`) REFERENCES `bounce_type` (`bounce_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Holds all activities that people take on our campaign emails.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `campaign_status`
--

DROP TABLE IF EXISTS `campaign_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `campaign_status` (
  `campaign_status_id` tinyint(3) unsigned NOT NULL,
  `status` varchar(20) NOT NULL,
  PRIMARY KEY (`campaign_status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Indicates if a campaign is a draft, in sending mode, or sent.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `caption_language`
--

DROP TABLE IF EXISTS `caption_language`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `caption_language` (
  `caption_language_id` int(10) unsigned NOT NULL,
  `language` varchar(50) NOT NULL,
  `srclang` varchar(2) NOT NULL,
  PRIMARY KEY (`caption_language_id`),
  UNIQUE KEY `language` (`language`),
  UNIQUE KEY `srclang` (`srclang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='The languages the website can accept captions in.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `class`
--

DROP TABLE IF EXISTS `class`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `class` (
  `class_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `person_id` int(10) unsigned NOT NULL,
  `class_name` varchar(100) DEFAULT NULL,
  `class_code` varchar(40) DEFAULT NULL,
  `archived` tinyint(1) NOT NULL DEFAULT 0,
  `archived_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`class_id`),
  UNIQUE KEY `class_code` (`class_code`),
  KEY `person` (`person_id`),
  CONSTRAINT `class_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `person` (`person_id`)
) ENGINE=InnoDB AUTO_INCREMENT=204 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `class_student`
--

DROP TABLE IF EXISTS `class_student`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `class_student` (
  `class_id` int(10) unsigned NOT NULL,
  `person_id` int(10) unsigned NOT NULL,
  `joined_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`class_id`,`person_id`),
  KEY `person_id` (`person_id`),
  CONSTRAINT `class_student_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `class` (`class_id`),
  CONSTRAINT `class_student_ibfk_2` FOREIGN KEY (`person_id`) REFERENCES `person` (`person_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `country`
--

DROP TABLE IF EXISTS `country`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `country` (
  `country_code` varchar(2) NOT NULL,
  `country_name` varchar(50) NOT NULL,
  `currency_code` varchar(3) NOT NULL,
  PRIMARY KEY (`country_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Countries and currencies';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `currency`
--

DROP TABLE IF EXISTS `currency`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `currency` (
  `currency_code` varchar(3) NOT NULL,
  `currency_description` varchar(30) NOT NULL,
  `currency_symbol` varchar(10) NOT NULL,
  `stripe_divisor` smallint(6) NOT NULL,
  PRIMARY KEY (`currency_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Currencies that the PyAngelo website supports.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `db_change`
--

DROP TABLE IF EXISTS `db_change`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `db_change` (
  `change_id` smallint(5) unsigned NOT NULL COMMENT 'Surrogate key',
  `change_desc` varchar(500) NOT NULL COMMENT 'Description of change',
  `script_name` varchar(50) NOT NULL COMMENT 'Name of the script that was run',
  `date_applied` datetime NOT NULL COMMENT 'The date the change was applied',
  PRIMARY KEY (`change_id`),
  KEY `date_applied` (`date_applied`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Changes to this database';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `email_activity_type`
--

DROP TABLE IF EXISTS `email_activity_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `email_activity_type` (
  `activity_type_id` smallint(5) unsigned NOT NULL,
  `activity_type` varchar(20) NOT NULL,
  PRIMARY KEY (`activity_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='These are the user actions we track for a campaign.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `email_image`
--

DROP TABLE IF EXISTS `email_image`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `email_image` (
  `image_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `image_name` varchar(255) NOT NULL,
  `image_width` smallint(5) unsigned NOT NULL,
  `image_height` smallint(5) unsigned NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`image_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Stores images which can be used in a PyAngelo email.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `email_status`
--

DROP TABLE IF EXISTS `email_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `email_status` (
  `email_status_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `email_status` varchar(10) NOT NULL,
  PRIMARY KEY (`email_status_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='You should check the email status before sending emails to a person.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `from_email`
--

DROP TABLE IF EXISTS `from_email`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `from_email` (
  `from_email_id` smallint(5) unsigned NOT NULL,
  `email` varchar(100) NOT NULL,
  PRIMARY KEY (`from_email_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Authorised email addresses for sending campaigns and autoresponders.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lesson`
--

DROP TABLE IF EXISTS `lesson`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `lesson` (
  `lesson_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tutorial_id` int(10) unsigned NOT NULL,
  `lesson_title` varchar(100) NOT NULL,
  `lesson_description` varchar(1000) NOT NULL,
  `video_name` varchar(100) NOT NULL,
  `youtube_url` varchar(255) DEFAULT NULL,
  `seconds` int(11) NOT NULL,
  `lesson_slug` varchar(105) NOT NULL,
  `lesson_security_level_id` smallint(5) unsigned NOT NULL,
  `lesson_sketch_id` varchar(32) DEFAULT NULL,
  `display_order` smallint(5) unsigned NOT NULL,
  `poster` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`lesson_id`),
  UNIQUE KEY `lesson_title_tutorial_id` (`tutorial_id`,`lesson_title`),
  UNIQUE KEY `lesson_slug_tutorial_id` (`tutorial_id`,`lesson_slug`),
  KEY `tutorial` (`tutorial_id`),
  KEY `previous_next` (`tutorial_id`,`display_order`),
  KEY `lesson_security_level_id` (`lesson_security_level_id`),
  KEY `lesson_sketch_id` (`lesson_sketch_id`),
  CONSTRAINT `lesson_ibfk_1` FOREIGN KEY (`lesson_security_level_id`) REFERENCES `lesson_security_level` (`lesson_security_level_id`),
  CONSTRAINT `lesson_ibfk_2` FOREIGN KEY (`lesson_sketch_id`) REFERENCES `sketch` (`sketch_id`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Individual video lessons to be watched.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lesson_alert`
--

DROP TABLE IF EXISTS `lesson_alert`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `lesson_alert` (
  `lesson_id` int(10) unsigned NOT NULL,
  `person_id` int(10) unsigned NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`lesson_id`,`person_id`),
  KEY `person_id` (`person_id`),
  CONSTRAINT `lesson_alert_ibfk_1` FOREIGN KEY (`lesson_id`) REFERENCES `lesson` (`lesson_id`),
  CONSTRAINT `lesson_alert_ibfk_2` FOREIGN KEY (`person_id`) REFERENCES `person` (`person_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Alert the user if a comment is added to this lesson.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lesson_caption`
--

DROP TABLE IF EXISTS `lesson_caption`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `lesson_caption` (
  `lesson_id` int(10) unsigned NOT NULL,
  `caption_language_id` int(10) unsigned NOT NULL,
  `caption_filename` varchar(255) NOT NULL,
  PRIMARY KEY (`lesson_id`,`caption_language_id`),
  KEY `caption_language_id` (`caption_language_id`),
  CONSTRAINT `lesson_caption_ibfk_1` FOREIGN KEY (`lesson_id`) REFERENCES `lesson` (`lesson_id`),
  CONSTRAINT `lesson_caption_ibfk_2` FOREIGN KEY (`caption_language_id`) REFERENCES `caption_language` (`caption_language_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Captions for each lesson.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lesson_comment`
--

DROP TABLE IF EXISTS `lesson_comment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `lesson_comment` (
  `comment_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `lesson_id` int(10) unsigned NOT NULL,
  `person_id` int(10) unsigned NOT NULL,
  `lesson_comment` text NOT NULL,
  `published` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`comment_id`),
  KEY `lesson_comment` (`lesson_id`,`created_at`),
  KEY `person_comment` (`person_id`),
  CONSTRAINT `lesson_comment_ibfk_1` FOREIGN KEY (`lesson_id`) REFERENCES `lesson` (`lesson_id`),
  CONSTRAINT `lesson_comment_ibfk_2` FOREIGN KEY (`person_id`) REFERENCES `person` (`person_id`)
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Stores comments for a lesson.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lesson_completed`
--

DROP TABLE IF EXISTS `lesson_completed`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `lesson_completed` (
  `person_id` int(10) unsigned NOT NULL,
  `lesson_id` int(10) unsigned NOT NULL,
  `completed_at` datetime NOT NULL,
  PRIMARY KEY (`person_id`,`lesson_id`),
  KEY `lesson` (`lesson_id`,`person_id`),
  CONSTRAINT `lesson_completed_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `person` (`person_id`),
  CONSTRAINT `lesson_completed_ibfk_2` FOREIGN KEY (`lesson_id`) REFERENCES `lesson` (`lesson_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Records the date a person completed a lesson.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lesson_favourited`
--

DROP TABLE IF EXISTS `lesson_favourited`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `lesson_favourited` (
  `person_id` int(10) unsigned NOT NULL,
  `lesson_id` int(10) unsigned NOT NULL,
  `favourited_at` datetime NOT NULL,
  PRIMARY KEY (`person_id`,`lesson_id`),
  KEY `lesson` (`lesson_id`,`person_id`),
  CONSTRAINT `lesson_favourited_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `person` (`person_id`),
  CONSTRAINT `lesson_favourited_ibfk_2` FOREIGN KEY (`lesson_id`) REFERENCES `lesson` (`lesson_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Records the date a person favourited a lesson.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lesson_security_level`
--

DROP TABLE IF EXISTS `lesson_security_level`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `lesson_security_level` (
  `lesson_security_level_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(20) NOT NULL,
  PRIMARY KEY (`lesson_security_level_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Anyone, Free members, or Premium members.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `list`
--

DROP TABLE IF EXISTS `list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `list` (
  `list_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `list_name` varchar(50) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`list_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Email lists that people can subscribe to.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mail_queue_status`
--

DROP TABLE IF EXISTS `mail_queue_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `mail_queue_status` (
  `mail_queue_status_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `status` varchar(10) NOT NULL,
  PRIMARY KEY (`mail_queue_status_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='The status of messages in our mail queue tables.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mail_queue_transactional`
--

DROP TABLE IF EXISTS `mail_queue_transactional`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `mail_queue_transactional` (
  `mail_queue_transactional_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `from_email` varchar(200) NOT NULL,
  `reply_email` varchar(200) DEFAULT NULL,
  `to_email` varchar(200) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `body_text` text NOT NULL,
  `body_html` text NOT NULL,
  `created_at` datetime NOT NULL,
  `sent_at` datetime DEFAULT NULL,
  `mail_queue_status_id` smallint(5) unsigned DEFAULT NULL,
  PRIMARY KEY (`mail_queue_transactional_id`),
  KEY `mail_queue_status` (`mail_queue_status_id`),
  CONSTRAINT `mail_queue_transactional_ibfk_1` FOREIGN KEY (`mail_queue_status_id`) REFERENCES `mail_queue_status` (`mail_queue_status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Mail queue to hold transactional email messages.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mastery_level`
--

DROP TABLE IF EXISTS `mastery_level`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `mastery_level` (
  `mastery_level_id` tinyint(3) unsigned NOT NULL,
  `mastery_level_desc` varchar(20) NOT NULL,
  `points` tinyint(4) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`mastery_level_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `membership_activate`
--

DROP TABLE IF EXISTS `membership_activate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `membership_activate` (
  `person_id` int(10) unsigned NOT NULL,
  `email` varchar(100) NOT NULL,
  `token` varchar(255) NOT NULL,
  `processed` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL,
  `processed_at` datetime DEFAULT NULL,
  PRIMARY KEY (`token`),
  KEY `person_id` (`person_id`),
  CONSTRAINT `membership_activate_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `person` (`person_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Free memebership requests and a token to validate.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `notification`
--

DROP TABLE IF EXISTS `notification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `notification` (
  `notification_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `notification_type_id` int(10) unsigned NOT NULL,
  `notification_type` varchar(50) NOT NULL,
  `person_id` int(10) unsigned NOT NULL,
  `data` text NOT NULL,
  `has_been_read` tinyint(1) NOT NULL DEFAULT 0,
  `read_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`notification_id`),
  KEY `person_read_at` (`person_id`,`has_been_read`),
  CONSTRAINT `notification_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `person` (`person_id`)
) ENGINE=InnoDB AUTO_INCREMENT=224 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Stores notifications for a user';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `password_reset_request`
--

DROP TABLE IF EXISTS `password_reset_request`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_request` (
  `person_id` int(10) unsigned NOT NULL,
  `token` varchar(255) NOT NULL,
  `processed` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL,
  `processed_at` datetime DEFAULT NULL,
  PRIMARY KEY (`person_id`,`token`,`created_at`),
  KEY `pass_reset_check` (`token`,`processed`,`created_at`),
  CONSTRAINT `password_reset_request_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `person` (`person_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Password reset requests and a token to validate them.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `person`
--

DROP TABLE IF EXISTS `person`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `person` (
  `person_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `given_name` varchar(100) NOT NULL,
  `family_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email_status_id` smallint(5) unsigned NOT NULL,
  `bounce_count` smallint(5) unsigned NOT NULL,
  `active` tinyint(1) NOT NULL,
  `country_code` varchar(2) NOT NULL,
  `detected_country_code` varchar(2) NOT NULL,
  `stripe_customer_id` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
  `last4` varchar(4) DEFAULT NULL,
  `premium_end_date` datetime DEFAULT NULL,
  `admin` tinyint(1) NOT NULL DEFAULT 0,
  `last_login` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`person_id`),
  UNIQUE KEY `email` (`email`),
  KEY `email_status_id` (`email_status_id`),
  KEY `country_code` (`country_code`),
  CONSTRAINT `person_ibfk_1` FOREIGN KEY (`email_status_id`) REFERENCES `email_status` (`email_status_id`),
  CONSTRAINT `person_ibfk_2` FOREIGN KEY (`country_code`) REFERENCES `country` (`country_code`)
) ENGINE=InnoDB AUTO_INCREMENT=4662 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='People with a PyAngelo account.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `question`
--

DROP TABLE IF EXISTS `question`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `question` (
  `question_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `person_id` int(10) unsigned NOT NULL,
  `question_title` varchar(100) NOT NULL,
  `question` text NOT NULL,
  `answer` text DEFAULT NULL,
  `teacher_id` int(10) unsigned NOT NULL,
  `created_at` datetime NOT NULL,
  `answered_at` datetime DEFAULT NULL,
  `updated_at` datetime NOT NULL,
  `published` tinyint(1) NOT NULL,
  `question_type_id` tinyint(3) unsigned NOT NULL,
  `slug` varchar(105) DEFAULT NULL,
  PRIMARY KEY (`question_id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `person` (`person_id`),
  KEY `updated_at` (`updated_at`,`published`),
  KEY `teacher_id` (`teacher_id`),
  KEY `question_type_id` (`question_type_id`),
  CONSTRAINT `question_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `person` (`person_id`),
  CONSTRAINT `question_ibfk_2` FOREIGN KEY (`teacher_id`) REFERENCES `person` (`person_id`),
  CONSTRAINT `question_ibfk_3` FOREIGN KEY (`question_type_id`) REFERENCES `question_type` (`question_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `question_alert`
--

DROP TABLE IF EXISTS `question_alert`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `question_alert` (
  `question_id` int(10) unsigned NOT NULL,
  `person_id` int(10) unsigned NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`question_id`,`person_id`),
  KEY `person_id` (`person_id`),
  CONSTRAINT `question_alert_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `question` (`question_id`),
  CONSTRAINT `question_alert_ibfk_2` FOREIGN KEY (`person_id`) REFERENCES `person` (`person_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Alert the user if a comment is added to this question.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `question_comment`
--

DROP TABLE IF EXISTS `question_comment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `question_comment` (
  `comment_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `question_id` int(10) unsigned NOT NULL,
  `person_id` int(10) unsigned NOT NULL,
  `question_comment` text NOT NULL,
  `published` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`comment_id`),
  KEY `question` (`question_id`,`created_at`),
  KEY `person` (`person_id`),
  CONSTRAINT `question_comment_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `question` (`question_id`),
  CONSTRAINT `question_comment_ibfk_2` FOREIGN KEY (`person_id`) REFERENCES `person` (`person_id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `question_favourited`
--

DROP TABLE IF EXISTS `question_favourited`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `question_favourited` (
  `person_id` int(10) unsigned NOT NULL,
  `question_id` int(10) unsigned NOT NULL,
  `favourited_at` datetime NOT NULL,
  PRIMARY KEY (`person_id`,`question_id`),
  KEY `question_favourite` (`question_id`,`person_id`),
  CONSTRAINT `question_favourited_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `person` (`person_id`),
  CONSTRAINT `question_favourited_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `question` (`question_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Records the date a person favourited a question.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `question_type`
--

DROP TABLE IF EXISTS `question_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `question_type` (
  `question_type_id` tinyint(3) unsigned NOT NULL,
  `description` varchar(30) DEFAULT NULL,
  `category_slug` varchar(30) NOT NULL,
  PRIMARY KEY (`question_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `quiz`
--

DROP TABLE IF EXISTS `quiz`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `quiz` (
  `quiz_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `quiz_type_id` tinyint(3) unsigned NOT NULL,
  `skill_id` int(10) unsigned DEFAULT NULL,
  `tutorial_id` int(10) unsigned DEFAULT NULL,
  `tutorial_category_id` smallint(5) unsigned DEFAULT NULL,
  `person_id` int(10) unsigned NOT NULL,
  `created_at` datetime NOT NULL,
  `started_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  PRIMARY KEY (`quiz_id`),
  KEY `tutorial_id` (`tutorial_id`),
  KEY `person_id` (`person_id`),
  KEY `quiz_ibfk_3` (`tutorial_category_id`),
  KEY `quiz_ibfk_4` (`skill_id`),
  KEY `quiz_ibfk_5` (`quiz_type_id`),
  CONSTRAINT `quiz_ibfk_1` FOREIGN KEY (`tutorial_id`) REFERENCES `tutorial` (`tutorial_id`),
  CONSTRAINT `quiz_ibfk_2` FOREIGN KEY (`person_id`) REFERENCES `person` (`person_id`),
  CONSTRAINT `quiz_ibfk_3` FOREIGN KEY (`tutorial_category_id`) REFERENCES `tutorial_category` (`tutorial_category_id`),
  CONSTRAINT `quiz_ibfk_4` FOREIGN KEY (`skill_id`) REFERENCES `skill` (`skill_id`),
  CONSTRAINT `quiz_ibfk_5` FOREIGN KEY (`quiz_type_id`) REFERENCES `quiz_type` (`quiz_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=152 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `quiz_question`
--

DROP TABLE IF EXISTS `quiz_question`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `quiz_question` (
  `quiz_id` int(10) unsigned NOT NULL,
  `skill_question_id` int(10) unsigned NOT NULL,
  `skill_question_option_id` int(10) unsigned DEFAULT NULL,
  `correct_unaided` tinyint(1) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `started_at` datetime DEFAULT NULL,
  `answered_at` datetime DEFAULT NULL,
  PRIMARY KEY (`quiz_id`,`skill_question_id`),
  KEY `skill_question_id` (`skill_question_id`),
  KEY `skill_question_option_id` (`skill_question_option_id`),
  CONSTRAINT `quiz_question_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quiz` (`quiz_id`),
  CONSTRAINT `quiz_question_ibfk_2` FOREIGN KEY (`skill_question_id`) REFERENCES `skill_question` (`skill_question_id`),
  CONSTRAINT `quiz_question_ibfk_3` FOREIGN KEY (`skill_question_option_id`) REFERENCES `skill_question_option` (`skill_question_option_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `quiz_type`
--

DROP TABLE IF EXISTS `quiz_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `quiz_type` (
  `quiz_type_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(30) NOT NULL,
  `num_questions` tinyint(3) unsigned NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`quiz_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `remember_me`
--

DROP TABLE IF EXISTS `remember_me`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `remember_me` (
  `person_id` int(10) unsigned NOT NULL,
  `session_id` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  `expires_at` datetime NOT NULL,
  PRIMARY KEY (`person_id`,`session_id`,`token`),
  CONSTRAINT `remember_me_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `person` (`person_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Remember me cookie information.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `segment`
--

DROP TABLE IF EXISTS `segment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `segment` (
  `segment_id` tinyint(3) unsigned NOT NULL,
  `segment_name` varchar(50) NOT NULL,
  `list_id` int(10) unsigned NOT NULL,
  `autoresponder_where_condition` varchar(255) NOT NULL,
  PRIMARY KEY (`segment_id`),
  KEY `list_id` (`list_id`),
  CONSTRAINT `segment_ibfk_1` FOREIGN KEY (`list_id`) REFERENCES `list` (`list_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Defines which method from the CampaignRepository that will be called to get the list of subscribers.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sketch`
--

DROP TABLE IF EXISTS `sketch`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sketch` (
  `sketch_id` varchar(32) NOT NULL,
  `person_id` int(10) unsigned NOT NULL,
  `collection_id` int(10) unsigned DEFAULT NULL,
  `lesson_id` int(10) unsigned DEFAULT NULL,
  `tutorial_id` int(10) unsigned DEFAULT NULL,
  `title` varchar(100) NOT NULL,
  `layout` varchar(4) NOT NULL DEFAULT 'cols',
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`sketch_id`),
  KEY `person_id` (`person_id`),
  KEY `lesson_id` (`lesson_id`),
  KEY `tutorial_id` (`tutorial_id`),
  KEY `collection_id` (`collection_id`),
  CONSTRAINT `sketch_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `person` (`person_id`),
  CONSTRAINT `sketch_ibfk_2` FOREIGN KEY (`lesson_id`) REFERENCES `lesson` (`lesson_id`),
  CONSTRAINT `sketch_ibfk_3` FOREIGN KEY (`tutorial_id`) REFERENCES `tutorial` (`tutorial_id`),
  CONSTRAINT `sketch_ibfk_4` FOREIGN KEY (`collection_id`) REFERENCES `sketch_collection` (`collection_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Holds all sketches.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sketch_collection`
--

DROP TABLE IF EXISTS `sketch_collection`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sketch_collection` (
  `collection_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `person_id` int(10) unsigned NOT NULL,
  `collection_name` varchar(100) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`collection_id`),
  KEY `person_id` (`person_id`),
  CONSTRAINT `sketch_collection_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `person` (`person_id`)
) ENGINE=InnoDB AUTO_INCREMENT=749 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sketch_files`
--

DROP TABLE IF EXISTS `sketch_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sketch_files` (
  `file_id` int(11) NOT NULL AUTO_INCREMENT,
  `sketch_id` varchar(32) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`file_id`),
  UNIQUE KEY `sketch_id_filename` (`sketch_id`,`filename`),
  CONSTRAINT `sketch_files_ibfk_1` FOREIGN KEY (`sketch_id`) REFERENCES `sketch` (`sketch_id`)
) ENGINE=InnoDB AUTO_INCREMENT=199793 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Holds details of all files for a sketch.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `skill`
--

DROP TABLE IF EXISTS `skill`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `skill` (
  `skill_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `skill_name` varchar(100) DEFAULT NULL,
  `slug` varchar(105) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`skill_id`),
  UNIQUE KEY `skill_slug_u1` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `skill_mastery`
--

DROP TABLE IF EXISTS `skill_mastery`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `skill_mastery` (
  `skill_id` int(10) unsigned NOT NULL,
  `person_id` int(10) unsigned NOT NULL,
  `mastery_level_id` tinyint(3) unsigned NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`skill_id`,`person_id`),
  KEY `person_id` (`person_id`),
  KEY `mastery_level_id` (`mastery_level_id`),
  CONSTRAINT `skill_mastery_ibfk_1` FOREIGN KEY (`skill_id`) REFERENCES `skill` (`skill_id`),
  CONSTRAINT `skill_mastery_ibfk_2` FOREIGN KEY (`person_id`) REFERENCES `person` (`person_id`),
  CONSTRAINT `skill_mastery_ibfk_3` FOREIGN KEY (`mastery_level_id`) REFERENCES `mastery_level` (`mastery_level_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `skill_question`
--

DROP TABLE IF EXISTS `skill_question`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `skill_question` (
  `skill_question_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `skill_id` int(10) unsigned NOT NULL,
  `skill_question_type_id` tinyint(3) unsigned NOT NULL,
  `question` text NOT NULL,
  `question_image` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`skill_question_id`),
  KEY `skill_id` (`skill_id`),
  KEY `skill_question_type_id` (`skill_question_type_id`),
  CONSTRAINT `skill_question_ibfk_1` FOREIGN KEY (`skill_id`) REFERENCES `skill` (`skill_id`),
  CONSTRAINT `skill_question_ibfk_2` FOREIGN KEY (`skill_question_type_id`) REFERENCES `skill_question_type` (`skill_question_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `skill_question_hint`
--

DROP TABLE IF EXISTS `skill_question_hint`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `skill_question_hint` (
  `hint_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `skill_question_id` int(10) unsigned NOT NULL,
  `hint` varchar(1000) NOT NULL,
  `hint_image` varchar(255) DEFAULT NULL,
  `hint_order` tinyint(4) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`hint_id`),
  KEY `skill_question` (`skill_question_id`),
  CONSTRAINT `skill_question_hint_ibfk_1` FOREIGN KEY (`skill_question_id`) REFERENCES `skill_question` (`skill_question_id`)
) ENGINE=InnoDB AUTO_INCREMENT=223 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `skill_question_option`
--

DROP TABLE IF EXISTS `skill_question_option`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `skill_question_option` (
  `skill_question_option_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `skill_question_id` int(10) unsigned NOT NULL,
  `option_text` varchar(1000) NOT NULL,
  `option_image` varchar(255) DEFAULT NULL,
  `option_order` tinyint(4) NOT NULL,
  `correct` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`skill_question_option_id`),
  KEY `skill_question_id` (`skill_question_id`),
  CONSTRAINT `skill_question_option_ibfk_1` FOREIGN KEY (`skill_question_id`) REFERENCES `skill_question` (`skill_question_id`)
) ENGINE=InnoDB AUTO_INCREMENT=297 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `skill_question_type`
--

DROP TABLE IF EXISTS `skill_question_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `skill_question_type` (
  `skill_question_type_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(100) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`skill_question_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stripe_event`
--

DROP TABLE IF EXISTS `stripe_event`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `stripe_event` (
  `event_id` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL,
  `api_version` varchar(30) NOT NULL,
  `created_at` datetime NOT NULL,
  `event_type` varchar(255) NOT NULL,
  PRIMARY KEY (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Stripe events to ensure they are not processed twice.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stripe_payment_type`
--

DROP TABLE IF EXISTS `stripe_payment_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `stripe_payment_type` (
  `payment_type_id` tinyint(3) unsigned NOT NULL,
  `payment_type_name` varchar(10) NOT NULL,
  PRIMARY KEY (`payment_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Indiciates if this is a payment or a refund.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stripe_price`
--

DROP TABLE IF EXISTS `stripe_price`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `stripe_price` (
  `stripe_price_id` varchar(255) NOT NULL,
  `stripe_product_id` varchar(255) NOT NULL,
  `currency_code` varchar(3) NOT NULL,
  `price_in_cents` int(10) unsigned NOT NULL,
  `billing_period` varchar(5) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`stripe_price_id`),
  KEY `stripe_product_id` (`stripe_product_id`),
  KEY `currency_code` (`currency_code`),
  CONSTRAINT `stripe_price_ibfk_1` FOREIGN KEY (`stripe_product_id`) REFERENCES `stripe_product` (`stripe_product_id`),
  CONSTRAINT `stripe_price_ibfk_2` FOREIGN KEY (`currency_code`) REFERENCES `currency` (`currency_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Prices in different currencies for our products';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stripe_product`
--

DROP TABLE IF EXISTS `stripe_product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `stripe_product` (
  `stripe_product_id` varchar(255) NOT NULL,
  `product_name` varchar(30) NOT NULL,
  `product_description` varchar(255) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`stripe_product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Available products on the PyAngelo website';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stripe_subscription`
--

DROP TABLE IF EXISTS `stripe_subscription`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `stripe_subscription` (
  `subscription_id` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL,
  `person_id` int(10) unsigned NOT NULL,
  `cancel_at_period_end` tinyint(3) unsigned NOT NULL,
  `canceled_at` datetime DEFAULT NULL,
  `current_period_start` datetime NOT NULL,
  `current_period_end` datetime NOT NULL,
  `stripe_customer_id` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL,
  `stripe_price_id` varchar(255) NOT NULL,
  `stripe_client_secret` varchar(255) NOT NULL,
  `start_date` datetime NOT NULL,
  `status` varchar(30) NOT NULL,
  `percent_off` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`subscription_id`),
  KEY `stripe_customer_id` (`stripe_customer_id`),
  KEY `person_id` (`person_id`),
  KEY `stripe_price_id` (`stripe_price_id`),
  CONSTRAINT `stripe_subscription_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `person` (`person_id`),
  CONSTRAINT `stripe_subscription_ibfk_2` FOREIGN KEY (`stripe_price_id`) REFERENCES `stripe_price` (`stripe_price_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Subscription details of every premium member.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stripe_subscription_payment`
--

DROP TABLE IF EXISTS `stripe_subscription_payment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `stripe_subscription_payment` (
  `subscription_id` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL,
  `payment_type_id` tinyint(3) unsigned NOT NULL,
  `currency_code` varchar(3) NOT NULL,
  `total_amount_in_cents` int(11) NOT NULL,
  `paid_at` datetime NOT NULL,
  `stripe_fee_aud_in_cents` int(11) NOT NULL,
  `tax_fee_aud_in_cents` int(11) NOT NULL,
  `net_aud_in_cents` int(11) NOT NULL,
  `charge_id` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin NOT NULL,
  `original_charge_id` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
  `refund_status` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`subscription_id`,`payment_type_id`,`paid_at`),
  KEY `payment_type_id` (`payment_type_id`),
  KEY `currency_code` (`currency_code`),
  CONSTRAINT `stripe_subscription_payment_ibfk_1` FOREIGN KEY (`subscription_id`) REFERENCES `stripe_subscription` (`subscription_id`),
  CONSTRAINT `stripe_subscription_payment_ibfk_2` FOREIGN KEY (`payment_type_id`) REFERENCES `stripe_payment_type` (`payment_type_id`),
  CONSTRAINT `stripe_subscription_payment_ibfk_3` FOREIGN KEY (`currency_code`) REFERENCES `currency` (`currency_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Subscription payments and refunds.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `subscriber`
--

DROP TABLE IF EXISTS `subscriber`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `subscriber` (
  `list_id` int(10) unsigned NOT NULL,
  `person_id` int(10) unsigned NOT NULL,
  `subscriber_status_id` smallint(5) unsigned NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `subscribed_at` datetime NOT NULL,
  `last_campaign_at` datetime NOT NULL,
  `last_autoresponder_at` datetime NOT NULL,
  PRIMARY KEY (`list_id`,`person_id`),
  KEY `subscriber_person` (`person_id`),
  KEY `subscriber_status_id` (`subscriber_status_id`),
  CONSTRAINT `subscriber_ibfk_1` FOREIGN KEY (`list_id`) REFERENCES `list` (`list_id`),
  CONSTRAINT `subscriber_ibfk_2` FOREIGN KEY (`person_id`) REFERENCES `person` (`person_id`),
  CONSTRAINT `subscriber_ibfk_3` FOREIGN KEY (`subscriber_status_id`) REFERENCES `subscriber_status` (`subscriber_status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Show which email lists a person has subscribed to.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `subscriber_status`
--

DROP TABLE IF EXISTS `subscriber_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `subscriber_status` (
  `subscriber_status_id` smallint(5) unsigned NOT NULL,
  `description` varchar(50) NOT NULL,
  PRIMARY KEY (`subscriber_status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Records the status of a subscriber';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `trackable_link`
--

DROP TABLE IF EXISTS `trackable_link`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `trackable_link` (
  `link_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `href` varchar(255) NOT NULL,
  PRIMARY KEY (`link_id`),
  UNIQUE KEY `href` (`href`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Trackable links that allow us to count how many times a link is clicked and redirect to the correct page.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tutorial`
--

DROP TABLE IF EXISTS `tutorial`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `tutorial` (
  `tutorial_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `description` varchar(1000) NOT NULL,
  `slug` varchar(105) NOT NULL,
  `thumbnail` varchar(255) NOT NULL,
  `pdf` varchar(255) DEFAULT NULL,
  `tutorial_category_id` smallint(5) unsigned NOT NULL,
  `tutorial_level_id` smallint(5) unsigned NOT NULL,
  `single_sketch` tinyint(1) NOT NULL,
  `tutorial_sketch_id` varchar(32) DEFAULT NULL,
  `display_order` smallint(5) unsigned NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`tutorial_id`),
  UNIQUE KEY `slug` (`slug`),
  UNIQUE KEY `title` (`title`),
  KEY `tutorial_category_id` (`tutorial_category_id`),
  KEY `tutorial_level_id` (`tutorial_level_id`),
  KEY `tutorial_sketch_id` (`tutorial_sketch_id`),
  CONSTRAINT `tutorial_ibfk_1` FOREIGN KEY (`tutorial_category_id`) REFERENCES `tutorial_category` (`tutorial_category_id`),
  CONSTRAINT `tutorial_ibfk_2` FOREIGN KEY (`tutorial_level_id`) REFERENCES `tutorial_level` (`tutorial_level_id`),
  CONSTRAINT `tutorial_ibfk_3` FOREIGN KEY (`tutorial_sketch_id`) REFERENCES `sketch` (`sketch_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Every tutorial will have a number of video lessons.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tutorial_category`
--

DROP TABLE IF EXISTS `tutorial_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `tutorial_category` (
  `tutorial_category_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `category` varchar(30) NOT NULL,
  `category_slug` varchar(30) NOT NULL,
  `display_order` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`tutorial_category_id`),
  UNIQUE KEY `category_slug` (`category_slug`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='A way to categorise tutorials into groups.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tutorial_level`
--

DROP TABLE IF EXISTS `tutorial_level`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `tutorial_level` (
  `tutorial_level_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(20) NOT NULL,
  PRIMARY KEY (`tutorial_level_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Beginner, intermediate, or advanced will be the levels.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tutorial_skill`
--

DROP TABLE IF EXISTS `tutorial_skill`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `tutorial_skill` (
  `tutorial_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `skill_id` int(10) unsigned NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`tutorial_id`,`skill_id`),
  KEY `skill_id` (`skill_id`),
  CONSTRAINT `tutorial_skill_ibfk_1` FOREIGN KEY (`tutorial_id`) REFERENCES `tutorial` (`tutorial_id`),
  CONSTRAINT `tutorial_skill_ibfk_2` FOREIGN KEY (`skill_id`) REFERENCES `skill` (`skill_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2025-08-05 21:20:52
