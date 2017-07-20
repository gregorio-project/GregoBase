-- phpMyAdmin SQL Dump
-- version 4.7.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le :  jeu. 20 juil. 2017 à 22:05
-- Version du serveur :  10.0.29-MariaDB-0ubuntu0.16.04.1
-- Version de PHP :  7.0.18-0ubuntu0.16.04.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `grego`
--

-- --------------------------------------------------------

--
-- Structure de la table `gregobase_changes`
--

CREATE TABLE `gregobase_changes` (
  `changeset` varchar(32) NOT NULL,
  `field` varchar(32) NOT NULL,
  `changed` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `gregobase_changesets`
--

CREATE TABLE `gregobase_changesets` (
  `user_id` int(11) NOT NULL,
  `chant_id` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `comment` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `gregobase_chants`
--

CREATE TABLE `gregobase_chants` (
  `id` int(11) NOT NULL,
  `cantusid` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `version` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `incipit` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `initial` tinyint(4) NOT NULL DEFAULT '1',
  `office-part` varchar(16) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mode` varchar(8) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mode_var` varchar(16) COLLATE utf8_unicode_ci DEFAULT NULL,
  `transcriber` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `commentary` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gabc` text COLLATE utf8_unicode_ci,
  `gabc_verses` text COLLATE utf8_unicode_ci,
  `tex_verses` text COLLATE utf8_unicode_ci,
  `remarks` text COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `gregobase_chant_sources`
--

CREATE TABLE `gregobase_chant_sources` (
  `chant_id` int(11) NOT NULL,
  `source` int(11) NOT NULL,
  `page` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `sequence` tinyint(4) NOT NULL DEFAULT '1',
  `extent` tinyint(4) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `gregobase_chant_tags`
--

CREATE TABLE `gregobase_chant_tags` (
  `chant_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `gregobase_pleasefix`
--

CREATE TABLE `gregobase_pleasefix` (
  `id` int(11) NOT NULL,
  `chant_id` int(11) NOT NULL,
  `pleasefix` text COLLATE utf8_unicode_ci NOT NULL,
  `time` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `ip` varchar(16) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fixed` tinyint(1) NOT NULL DEFAULT '0',
  `fixed_by` int(11) NOT NULL DEFAULT '0',
  `fixed_time` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `gregobase_proofreading`
--

CREATE TABLE `gregobase_proofreading` (
  `chant_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `gregobase_sources`
--

CREATE TABLE `gregobase_sources` (
  `id` int(11) NOT NULL,
  `year` smallint(6) NOT NULL,
  `editor` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `caption` text COLLATE utf8_unicode_ci NOT NULL,
  `pages` mediumtext COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `gregobase_tags`
--

CREATE TABLE `gregobase_tags` (
  `id` int(11) NOT NULL,
  `tag` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `gregobase_changes`
--
ALTER TABLE `gregobase_changes`
  ADD PRIMARY KEY (`changeset`,`field`);

--
-- Index pour la table `gregobase_changesets`
--
ALTER TABLE `gregobase_changesets`
  ADD PRIMARY KEY (`user_id`,`chant_id`,`time`);

--
-- Index pour la table `gregobase_chants`
--
ALTER TABLE `gregobase_chants`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `gregobase_chant_sources`
--
ALTER TABLE `gregobase_chant_sources`
  ADD PRIMARY KEY (`chant_id`,`source`,`page`);

--
-- Index pour la table `gregobase_chant_tags`
--
ALTER TABLE `gregobase_chant_tags`
  ADD PRIMARY KEY (`chant_id`,`tag_id`);

--
-- Index pour la table `gregobase_pleasefix`
--
ALTER TABLE `gregobase_pleasefix`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `gregobase_proofreading`
--
ALTER TABLE `gregobase_proofreading`
  ADD PRIMARY KEY (`time`,`user_id`,`chant_id`);

--
-- Index pour la table `gregobase_sources`
--
ALTER TABLE `gregobase_sources`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `gregobase_tags`
--
ALTER TABLE `gregobase_tags`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tag` (`tag`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `gregobase_chants`
--
ALTER TABLE `gregobase_chants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7907;
--
-- AUTO_INCREMENT pour la table `gregobase_pleasefix`
--
ALTER TABLE `gregobase_pleasefix`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2849;
--
-- AUTO_INCREMENT pour la table `gregobase_sources`
--
ALTER TABLE `gregobase_sources`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;
--
-- AUTO_INCREMENT pour la table `gregobase_tags`
--
ALTER TABLE `gregobase_tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=109;COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
