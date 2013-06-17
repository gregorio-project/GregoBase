-- phpMyAdmin SQL Dump
-- version 4.0.0
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le: Lun 17 Juin 2013 à 09:37
-- Version du serveur: 5.5.31-0ubuntu0.12.04.2
-- Version de PHP: 5.4.15-1~precise+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `grego`
--

-- --------------------------------------------------------

--
-- Structure de la table `gregobase_booklets`
--

CREATE TABLE IF NOT EXISTS `gregobase_booklets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `filename` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `code` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `title` text COLLATE utf8_unicode_ci,
  `content` text COLLATE utf8_unicode_ci,
  `options` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=432 ;

-- --------------------------------------------------------

--
-- Structure de la table `gregobase_changes`
--

CREATE TABLE IF NOT EXISTS `gregobase_changes` (
  `changeset` varchar(32) NOT NULL,
  `field` varchar(32) NOT NULL,
  `changed` text NOT NULL,
  PRIMARY KEY (`changeset`,`field`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `gregobase_changesets`
--

CREATE TABLE IF NOT EXISTS `gregobase_changesets` (
  `user_id` int(11) NOT NULL,
  `chant_id` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `comment` text,
  PRIMARY KEY (`user_id`,`chant_id`,`time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `gregobase_chants`
--

CREATE TABLE IF NOT EXISTS `gregobase_chants` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `version` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `incipit` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `initial` tinyint(4) NOT NULL DEFAULT '1',
  `office-part` varchar(16) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mode` varchar(8) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mode_var` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `transcriber` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `commentary` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gabc` text COLLATE utf8_unicode_ci,
  `gabc_verses` text COLLATE utf8_unicode_ci,
  `tex_verses` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3302 ;

-- --------------------------------------------------------

--
-- Structure de la table `gregobase_chant_sources`
--

CREATE TABLE IF NOT EXISTS `gregobase_chant_sources` (
  `chant_id` int(11) NOT NULL,
  `source` int(11) NOT NULL,
  `page` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `sequence` tinyint(4) NOT NULL DEFAULT '1',
  `extent` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`chant_id`,`source`,`page`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `gregobase_proofreading`
--

CREATE TABLE IF NOT EXISTS `gregobase_proofreading` (
  `chant_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `gregobase_sources`
--

CREATE TABLE IF NOT EXISTS `gregobase_sources` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `year` smallint(6) NOT NULL,
  `editor` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `caption` text COLLATE utf8_unicode_ci NOT NULL,
  `pages` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=10 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
