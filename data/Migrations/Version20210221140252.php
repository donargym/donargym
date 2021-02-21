<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210221140252 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql(
            <<<EOQ
# ************************************************************
# Sequel Pro SQL dump
# Version 5446
#
# https://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.0.1 (MySQL 5.5.5-10.3.27-MariaDB-0+deb10u1-log)
# Database: donargym
# Generation Time: 2021-02-21 14:01:30 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
SET NAMES utf8mb4;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table aanwezigheid
# ------------------------------------------------------------

CREATE TABLE `aanwezigheid` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `persoon_id` int(11) NOT NULL,
  `trainingdata_id` int(11) NOT NULL,
  `aanwezig` varchar(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_980042D290FBB45F` (`persoon_id`),
  KEY `IDX_980042D2FD2A81D0` (`trainingdata_id`),
  CONSTRAINT `FK_980042D290FBB45F` FOREIGN KEY (`persoon_id`) REFERENCES `persoon` (`id`),
  CONSTRAINT `FK_980042D2FD2A81D0` FOREIGN KEY (`trainingdata_id`) REFERENCES `trainingsdata` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table about_competition_group_member
# ------------------------------------------------------------

CREATE TABLE `about_competition_group_member` (
  `id` char(36) COLLATE utf8mb4_unicode_520_ci NOT NULL COMMENT '(DC2Type:guid)',
  `competition_group_member_id` char(36) COLLATE utf8mb4_unicode_520_ci NOT NULL COMMENT '(DC2Type:guid)',
  `most_fun_apparatus` longtext COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `explanation_about_most_fun_apparatus` longtext COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `most_fun_competition` longtext COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `most_fun_or_hardest_skill` longtext COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `would_like_to_learn` longtext COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `example_gymnast` longtext COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `anything_else` longtext COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;



# Dump of table account_phone_number
# ------------------------------------------------------------

CREATE TABLE `account_phone_number` (
  `id` char(36) COLLATE utf8mb4_unicode_520_ci NOT NULL COMMENT '(DC2Type:guid)',
  `account_id` char(36) COLLATE utf8mb4_unicode_520_ci NOT NULL COMMENT '(DC2Type:guid)',
  `phone_number` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;



# Dump of table afmeldingen
# ------------------------------------------------------------

CREATE TABLE `afmeldingen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datum` datetime NOT NULL,
  `bericht` longtext DEFAULT NULL,
  `turnster` longtext DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table calendar
# ------------------------------------------------------------

CREATE TABLE `calendar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datum` date NOT NULL,
  `activiteit` varchar(156) NOT NULL,
  `locatie` longtext DEFAULT NULL,
  `tijd` longtext DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table cijfers
# ------------------------------------------------------------

CREATE TABLE `cijfers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subdoel_id` int(11) DEFAULT NULL,
  `cijfer` int(11) NOT NULL,
  `date` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_5F1F229EE9770E6C` (`subdoel_id`),
  CONSTRAINT `FK_5F1F229EE9770E6C` FOREIGN KEY (`subdoel_id`) REFERENCES `subdoelen` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table clubblad
# ------------------------------------------------------------

CREATE TABLE `clubblad` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datum` date NOT NULL,
  `locatie` varchar(300) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table competition_group
# ------------------------------------------------------------

CREATE TABLE `competition_group` (
  `id` char(36) COLLATE utf8mb4_unicode_520_ci NOT NULL COMMENT '(DC2Type:guid)',
  `name` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `sort_order` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;



# Dump of table competition_group_account
# ------------------------------------------------------------

CREATE TABLE `competition_group_account` (
  `id` char(36) COLLATE utf8mb4_unicode_520_ci NOT NULL COMMENT '(DC2Type:guid)',
  `user_id` int(11) NOT NULL,
  `street_house_number` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `zip_code` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;



# Dump of table competition_group_calendar
# ------------------------------------------------------------

CREATE TABLE `competition_group_calendar` (
  `id` char(36) COLLATE utf8mb4_unicode_520_ci NOT NULL COMMENT '(DC2Type:guid)',
  `competition_group_id` char(36) COLLATE utf8mb4_unicode_520_ci NOT NULL COMMENT '(DC2Type:guid)',
  `file_name` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `competition_date` date NOT NULL COMMENT '(DC2Type:date_immutable)',
  `name` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;



# Dump of table competition_group_competition_result
# ------------------------------------------------------------

CREATE TABLE `competition_group_competition_result` (
  `id` char(36) COLLATE utf8mb4_unicode_520_ci NOT NULL COMMENT '(DC2Type:guid)',
  `competition_group_id` char(36) COLLATE utf8mb4_unicode_520_ci NOT NULL COMMENT '(DC2Type:guid)',
  `file_name` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `competition_date` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;



# Dump of table competition_group_member
# ------------------------------------------------------------

CREATE TABLE `competition_group_member` (
  `id` char(36) COLLATE utf8mb4_unicode_520_ci NOT NULL COMMENT '(DC2Type:guid)',
  `competition_group_account_id` char(36) COLLATE utf8mb4_unicode_520_ci NOT NULL COMMENT '(DC2Type:guid)',
  `first_name` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `date_of_birth` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `picture_file_name` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `floor_music_file_name` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;



# Dump of table competition_group_member_role
# ------------------------------------------------------------

CREATE TABLE `competition_group_member_role` (
  `id` char(36) COLLATE utf8mb4_unicode_520_ci NOT NULL COMMENT '(DC2Type:guid)',
  `competition_group_member_id` char(36) COLLATE utf8mb4_unicode_520_ci NOT NULL COMMENT '(DC2Type:guid)',
  `competition_group_id` char(36) COLLATE utf8mb4_unicode_520_ci NOT NULL COMMENT '(DC2Type:guid)',
  `role` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;



# Dump of table competition_group_member_training_participation
# ------------------------------------------------------------

CREATE TABLE `competition_group_member_training_participation` (
  `id` char(36) COLLATE utf8mb4_unicode_520_ci NOT NULL COMMENT '(DC2Type:guid)',
  `competition_group_member_id` char(36) COLLATE utf8mb4_unicode_520_ci NOT NULL COMMENT '(DC2Type:guid)',
  `competition_group_training_id` char(36) COLLATE utf8mb4_unicode_520_ci NOT NULL COMMENT '(DC2Type:guid)',
  `presence` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;



# Dump of table competition_group_member_training_time_subscription
# ------------------------------------------------------------

CREATE TABLE `competition_group_member_training_time_subscription` (
  `id` char(36) COLLATE utf8mb4_unicode_520_ci NOT NULL COMMENT '(DC2Type:guid)',
  `competition_group_training_time_id` char(36) COLLATE utf8mb4_unicode_520_ci NOT NULL COMMENT '(DC2Type:guid)',
  `competition_group_member_id` char(36) COLLATE utf8mb4_unicode_520_ci NOT NULL COMMENT '(DC2Type:guid)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;



# Dump of table competition_group_training
# ------------------------------------------------------------

CREATE TABLE `competition_group_training` (
  `id` char(36) COLLATE utf8mb4_unicode_520_ci NOT NULL COMMENT '(DC2Type:guid)',
  `competition_group_training_time_id` char(36) COLLATE utf8mb4_unicode_520_ci NOT NULL COMMENT '(DC2Type:guid)',
  `training_date` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;



# Dump of table competition_group_training_time
# ------------------------------------------------------------

CREATE TABLE `competition_group_training_time` (
  `id` char(36) COLLATE utf8mb4_unicode_520_ci NOT NULL COMMENT '(DC2Type:guid)',
  `competition_group_id` char(36) COLLATE utf8mb4_unicode_520_ci NOT NULL COMMENT '(DC2Type:guid)',
  `training_day` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `training_start_time` time NOT NULL COMMENT '(DC2Type:time_immutable)',
  `training_end_time` time NOT NULL COMMENT '(DC2Type:time_immutable)',
  `sort_order` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;



# Dump of table content
# ------------------------------------------------------------

CREATE TABLE `content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gewijzigd` datetime NOT NULL,
  `pagina` varchar(156) NOT NULL,
  `content` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table doelen
# ------------------------------------------------------------

CREATE TABLE `doelen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `naam` varchar(156) NOT NULL,
  `toestel` varchar(156) NOT NULL,
  `subdoelen` varchar(512) DEFAULT NULL,
  `trede` varchar(156) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table fileupload
# ------------------------------------------------------------

CREATE TABLE `fileupload` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `naam` varchar(300) NOT NULL,
  `locatie` varchar(300) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table formulieren
# ------------------------------------------------------------

CREATE TABLE `formulieren` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `naam` varchar(300) NOT NULL,
  `locatie` varchar(300) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table fotoupload
# ------------------------------------------------------------

CREATE TABLE `fotoupload` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `naam` varchar(300) NOT NULL,
  `locatie` varchar(300) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table functie
# ------------------------------------------------------------

CREATE TABLE `functie` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `persoon_id` int(11) NOT NULL,
  `groepen_id` int(11) NOT NULL,
  `functie` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_FB6B2EA790FBB45F` (`persoon_id`),
  KEY `IDX_FB6B2EA7C6A82497` (`groepen_id`),
  CONSTRAINT `FK_FB6B2EA790FBB45F` FOREIGN KEY (`persoon_id`) REFERENCES `persoon` (`id`),
  CONSTRAINT `FK_FB6B2EA7C6A82497` FOREIGN KEY (`groepen_id`) REFERENCES `groepen` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table groepen
# ------------------------------------------------------------

CREATE TABLE `groepen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table inschrijvingen
# ------------------------------------------------------------

CREATE TABLE `inschrijvingen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(300) COLLATE utf8_unicode_ci NOT NULL,
  `lastname` varchar(300) COLLATE utf8_unicode_ci NOT NULL,
  `nameletters` varchar(300) COLLATE utf8_unicode_ci NOT NULL,
  `dateofbirth` datetime NOT NULL,
  `gender` varchar(300) COLLATE utf8_unicode_ci NOT NULL,
  `address` varchar(300) COLLATE utf8_unicode_ci NOT NULL,
  `postcode` varchar(300) COLLATE utf8_unicode_ci NOT NULL,
  `city` varchar(300) COLLATE utf8_unicode_ci NOT NULL,
  `phone1` varchar(300) COLLATE utf8_unicode_ci NOT NULL,
  `phone2` varchar(300) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bankaccountnumber` varchar(300) COLLATE utf8_unicode_ci NOT NULL,
  `bankaccountholder` varchar(300) COLLATE utf8_unicode_ci NOT NULL,
  `emailaddress` varchar(300) COLLATE utf8_unicode_ci NOT NULL,
  `havebeensubscribed` varchar(300) COLLATE utf8_unicode_ci NOT NULL,
  `subscribedfrom` datetime DEFAULT NULL,
  `subscribeduntil` datetime DEFAULT NULL,
  `otherclub` varchar(300) COLLATE utf8_unicode_ci NOT NULL,
  `whatotherclub` varchar(300) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bondscontributiebetaald` varchar(300) COLLATE utf8_unicode_ci NOT NULL,
  `category` varchar(300) COLLATE utf8_unicode_ci NOT NULL,
  `days` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT '(DC2Type:array)',
  `locations` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT '(DC2Type:array)',
  `starttime` varchar(300) COLLATE utf8_unicode_ci NOT NULL,
  `trainer` varchar(300) COLLATE utf8_unicode_ci NOT NULL,
  `how` varchar(300) COLLATE utf8_unicode_ci DEFAULT NULL,
  `vrijwilligerstaken` text CHARACTER SET utf8mb4 NOT NULL,
  `accept` tinyint(1) NOT NULL,
  `accept_privacy_policy` tinyint(1) NOT NULL,
  `accept_name_published` tinyint(1) NOT NULL,
  `accept_pictures_published` tinyint(1) NOT NULL,
  `subscribed_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table nieuwsbericht
# ------------------------------------------------------------

CREATE TABLE `nieuwsbericht` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datumtijd` varchar(156) NOT NULL,
  `jaar` int(11) NOT NULL,
  `titel` varchar(156) NOT NULL,
  `bericht` text NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table personen_trainingen
# ------------------------------------------------------------

CREATE TABLE `personen_trainingen` (
  `trainingen_id` int(11) NOT NULL,
  `persoon_id` int(11) NOT NULL,
  PRIMARY KEY (`trainingen_id`,`persoon_id`),
  KEY `IDX_2E7DA94387B6A46A` (`trainingen_id`),
  KEY `IDX_2E7DA94390FBB45F` (`persoon_id`),
  CONSTRAINT `FK_2E7DA94387B6A46A` FOREIGN KEY (`trainingen_id`) REFERENCES `trainingen` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_2E7DA94390FBB45F` FOREIGN KEY (`persoon_id`) REFERENCES `persoon` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table personen_wedstrijdkalender
# ------------------------------------------------------------

CREATE TABLE `personen_wedstrijdkalender` (
  `wedstrijdkalender_id` int(11) NOT NULL,
  `persoon_id` int(11) NOT NULL,
  PRIMARY KEY (`wedstrijdkalender_id`,`persoon_id`),
  KEY `IDX_197AD8B51962A065` (`wedstrijdkalender_id`),
  KEY `IDX_197AD8B590FBB45F` (`persoon_id`),
  CONSTRAINT `FK_197AD8B51962A065` FOREIGN KEY (`wedstrijdkalender_id`) REFERENCES `wedstrijdkalender` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_197AD8B590FBB45F` FOREIGN KEY (`persoon_id`) REFERENCES `persoon` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table persoon
# ------------------------------------------------------------

CREATE TABLE `persoon` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `voornaam` varchar(255) NOT NULL,
  `achternaam` varchar(255) NOT NULL,
  `geboortedatum` varchar(255) NOT NULL,
  `foto_id` int(11) DEFAULT NULL,
  `stukje_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `vloermuziek_id` int(11) DEFAULT NULL,
  `voortgang_sprong` int(11) DEFAULT NULL,
  `voortgang_brug` int(11) DEFAULT NULL,
  `voortgang_balk` int(11) DEFAULT NULL,
  `voortgang_vloer` int(11) DEFAULT NULL,
  `voortgang_totaal` int(11) DEFAULT NULL,
  `last_updated_at_seizoen` varchar(255) DEFAULT NULL,
  `updated_cijfers_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_D8419A4B7ABFA656` (`foto_id`),
  UNIQUE KEY `UNIQ_D8419A4B9D24003E` (`stukje_id`),
  UNIQUE KEY `UNIQ_D8419A4BD42CC2DA` (`vloermuziek_id`),
  KEY `IDX_D8419A4BA76ED395` (`user_id`),
  CONSTRAINT `FK_D8419A4B7ABFA656` FOREIGN KEY (`foto_id`) REFERENCES `selectiefoto` (`id`),
  CONSTRAINT `FK_D8419A4B9D24003E` FOREIGN KEY (`stukje_id`) REFERENCES `stukje` (`id`),
  CONSTRAINT `FK_D8419A4BA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_D8419A4BD42CC2DA` FOREIGN KEY (`vloermuziek_id`) REFERENCES `vloermuziek` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table scores
# ------------------------------------------------------------

CREATE TABLE `scores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `wedstrijdnummer` int(11) DEFAULT NULL,
  `wedstrijddag` varchar(55) COLLATE utf8_unicode_ci DEFAULT NULL,
  `wedstrijdronde` varchar(55) COLLATE utf8_unicode_ci DEFAULT NULL,
  `baan` varchar(55) COLLATE utf8_unicode_ci DEFAULT NULL,
  `groep` varchar(55) COLLATE utf8_unicode_ci DEFAULT NULL,
  `begintoestel` varchar(55) COLLATE utf8_unicode_ci DEFAULT NULL,
  `d_sprong1` decimal(5,3) NOT NULL,
  `e_sprong1` decimal(5,3) NOT NULL,
  `n_sprong1` decimal(5,3) NOT NULL,
  `d_sprong2` decimal(5,3) NOT NULL,
  `e_sprong2` decimal(5,3) NOT NULL,
  `n_sprong2` decimal(5,3) NOT NULL,
  `getoond_sprong` int(11) NOT NULL,
  `gepubliceerd_sprong` tinyint(1) NOT NULL,
  `updated_sprong` datetime DEFAULT NULL,
  `d_brug` decimal(5,3) NOT NULL,
  `e_brug` decimal(5,3) NOT NULL,
  `n_brug` decimal(5,3) NOT NULL,
  `getoond_brug` int(11) NOT NULL,
  `gepubliceerd_brug` tinyint(1) NOT NULL,
  `updated_brug` datetime DEFAULT NULL,
  `d_balk` decimal(5,3) NOT NULL,
  `e_balk` decimal(5,3) NOT NULL,
  `n_balk` decimal(5,3) NOT NULL,
  `getoond_balk` int(11) NOT NULL,
  `gepubliceerd_balk` tinyint(1) NOT NULL,
  `updated_balk` datetime DEFAULT NULL,
  `d_vloer` decimal(5,3) NOT NULL,
  `e_vloer` decimal(5,3) NOT NULL,
  `n_vloer` decimal(5,3) NOT NULL,
  `getoond_vloer` int(11) NOT NULL,
  `gepubliceerd_vloer` tinyint(1) NOT NULL,
  `updated_vloer` datetime DEFAULT NULL,
  `geturnd_vloer` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table seizoensdoelen
# ------------------------------------------------------------

CREATE TABLE `seizoensdoelen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `doel_id` int(11) DEFAULT NULL,
  `persoon_id` int(11) DEFAULT NULL,
  `seizoen` varchar(156) NOT NULL,
  `cijfer` int(11) DEFAULT NULL,
  `tachtig_procent` tinyint(1) DEFAULT NULL,
  `negentig_procent` tinyint(1) DEFAULT NULL,
  `updated_cijfers_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_A45472CB90FBB45F` (`persoon_id`),
  KEY `IDX_A45472CBA215A6A3` (`doel_id`),
  CONSTRAINT `FK_A45472CB90FBB45F` FOREIGN KEY (`persoon_id`) REFERENCES `persoon` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_A45472CBA215A6A3` FOREIGN KEY (`doel_id`) REFERENCES `doelen` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table selectiefoto
# ------------------------------------------------------------

CREATE TABLE `selectiefoto` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `locatie` varchar(300) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table send_mail
# ------------------------------------------------------------

CREATE TABLE `send_mail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datum` datetime NOT NULL,
  `bericht` longtext COLLATE utf8_unicode_ci NOT NULL,
  `aan` varchar(300) COLLATE utf8_unicode_ci NOT NULL,
  `van` varchar(300) COLLATE utf8_unicode_ci NOT NULL,
  `onderwerp` varchar(300) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table stukje
# ------------------------------------------------------------

CREATE TABLE `stukje` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `toestelleuk` longtext DEFAULT NULL,
  `omdattoestelleuk` longtext DEFAULT NULL,
  `wedstrijd` longtext DEFAULT NULL,
  `element` longtext DEFAULT NULL,
  `leren` longtext DEFAULT NULL,
  `voorbeeld` longtext DEFAULT NULL,
  `overig` longtext DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table subdoelen
# ------------------------------------------------------------

CREATE TABLE `subdoelen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `doel_id` int(11) DEFAULT NULL,
  `persoon_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_A00C629E90FBB45F` (`persoon_id`),
  KEY `IDX_A00C629EA215A6A3` (`doel_id`),
  CONSTRAINT `FK_A00C629E90FBB45F` FOREIGN KEY (`persoon_id`) REFERENCES `persoon` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_A00C629EA215A6A3` FOREIGN KEY (`doel_id`) REFERENCES `doelen` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table toegestane_niveaus
# ------------------------------------------------------------

CREATE TABLE `toegestane_niveaus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `categorie` varchar(156) COLLATE utf8_unicode_ci NOT NULL,
  `niveau` varchar(156) COLLATE utf8_unicode_ci NOT NULL,
  `uitslag_gepubliceerd` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table trainingen
# ------------------------------------------------------------

CREATE TABLE `trainingen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `groepen_id` int(11) NOT NULL,
  `dag` varchar(255) NOT NULL,
  `tijdvan` varchar(255) NOT NULL,
  `tijdtot` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_422FFAEAC6A82497` (`groepen_id`),
  CONSTRAINT `FK_422FFAEAC6A82497` FOREIGN KEY (`groepen_id`) REFERENCES `groepen` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table trainingsdata
# ------------------------------------------------------------

CREATE TABLE `trainingsdata` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `trainingdata_id` int(11) NOT NULL,
  `lesdatum` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_A6587625FD2A81D0` (`trainingdata_id`),
  CONSTRAINT `FK_A6587625FD2A81D0` FOREIGN KEY (`trainingdata_id`) REFERENCES `trainingen` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table trainingsplan
# ------------------------------------------------------------

CREATE TABLE `trainingsplan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `trainingsdatum_id` int(11) NOT NULL,
  `trainingsplan` longtext DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table trainingsstage
# ------------------------------------------------------------

CREATE TABLE `trainingsstage` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(300) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `dateofbirth` date NOT NULL,
  `emailaddress` varchar(300) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `phone1` varchar(300) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `phone2` varchar(300) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `insurance_company` varchar(300) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `insurance_card` varchar(300) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `huis_arts` varchar(300) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `bankaccountholder` varchar(300) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `diet` longtext COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `medicines` longtext COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `other` longtext COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `accept` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;



# Dump of table trainingsstage_trainer
# ------------------------------------------------------------

CREATE TABLE `trainingsstage_trainer` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(300) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `emailaddress` varchar(300) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `phone1` varchar(300) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `phone2` varchar(300) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `insurance_company` varchar(300) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `insurance_card` varchar(300) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `huis_arts` varchar(300) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `diet` longtext COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;



# Dump of table turnster
# ------------------------------------------------------------

CREATE TABLE `turnster` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `vloermuziek_id` int(11) DEFAULT NULL,
  `score_id` int(11) DEFAULT NULL,
  `voornaam` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `achternaam` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `geboortajaar` int(11) NOT NULL,
  `niveau` varchar(12) COLLATE utf8_unicode_ci NOT NULL,
  `categorie` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `afgemeld` tinyint(1) NOT NULL,
  `wachtlijst` tinyint(1) NOT NULL,
  `ingevuld` tinyint(1) NOT NULL,
  `creation_date` datetime NOT NULL,
  `expiration_date` datetime DEFAULT NULL,
  `opmerking` longtext COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_1F739A65D42CC2DA` (`vloermuziek_id`),
  UNIQUE KEY `UNIQ_1F739A6512EB0A51` (`score_id`),
  KEY `IDX_1F739A65A76ED395` (`user_id`),
  CONSTRAINT `FK_1F739A65A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_1F739A65D42CC2DA` FOREIGN KEY (`vloermuziek_id`) REFERENCES `vloermuziek` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table user
# ------------------------------------------------------------

CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email2` varchar(190) DEFAULT NULL,
  `email3` varchar(190) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `isActive` tinyint(1) NOT NULL,
  `username` varchar(190) NOT NULL,
  `role` varchar(60) NOT NULL,
  `straatnr` varchar(255) NOT NULL,
  `postcode` varchar(255) NOT NULL,
  `plaats` varchar(255) NOT NULL,
  `tel1` varchar(255) NOT NULL,
  `tel2` varchar(255) DEFAULT NULL,
  `tel3` varchar(255) DEFAULT NULL,
  `vereniging_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8D93D649F85E0677` (`username`),
  UNIQUE KEY `UNIQ_8D93D6494D5A9954` (`email2`),
  UNIQUE KEY `email3` (`email3`),
  KEY `IDX_8D93D64917080D2E` (`vereniging_id`),
  CONSTRAINT `FK_8D93D64917080D2E` FOREIGN KEY (`vereniging_id`) REFERENCES `vereniging` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table user_credentials
# ------------------------------------------------------------

CREATE TABLE `user_credentials` (
  `username` varchar(150) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `encrypted_password` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `set_password_token` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `token_expires_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;



# Dump of table vakanties
# ------------------------------------------------------------

CREATE TABLE `vakanties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `naam` varchar(300) NOT NULL,
  `van` date NOT NULL,
  `tot` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table veelgesteldevragen
# ------------------------------------------------------------

CREATE TABLE `veelgesteldevragen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vraag` longtext NOT NULL,
  `antwoord` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table vereniging
# ------------------------------------------------------------

CREATE TABLE `vereniging` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `naam` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `plaats` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table vloermuziek
# ------------------------------------------------------------

CREATE TABLE `vloermuziek` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `locatie` varchar(300) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table voedsel
# ------------------------------------------------------------

CREATE TABLE `voedsel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `persoon_id` int(11) NOT NULL,
  `voedsel` varchar(255) NOT NULL,
  `hoeveelheid` varchar(255) NOT NULL,
  `overig` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_4D97B1190FBB45F` (`persoon_id`),
  CONSTRAINT `FK_4D97B1190FBB45F` FOREIGN KEY (`persoon_id`) REFERENCES `persoon` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table wedstrijdkalender
# ------------------------------------------------------------

CREATE TABLE `wedstrijdkalender` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datum` date NOT NULL,
  `tijden` longtext DEFAULT NULL,
  `wedstrijdnaam` varchar(156) NOT NULL,
  `locatie` longtext DEFAULT NULL,
  `groepen_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_7270E041C6A82497` (`groepen_id`),
  CONSTRAINT `FK_7270E041C6A82497` FOREIGN KEY (`groepen_id`) REFERENCES `groepen` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table wedstrijduitslagen
# ------------------------------------------------------------

CREATE TABLE `wedstrijduitslagen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `groepen_id` int(11) NOT NULL,
  `locatie` varchar(300) NOT NULL,
  `datum` date NOT NULL,
  `naam` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_4E1E0E7C6A82497` (`groepen_id`),
  CONSTRAINT `FK_4E1E0E7C6A82497` FOREIGN KEY (`groepen_id`) REFERENCES `groepen` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

EOQ
        );
    }

    public function down(Schema $schema) : void
    {
    }
}
