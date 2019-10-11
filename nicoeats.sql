-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le :  mer. 04 sep. 2019 à 12:34
-- Version du serveur :  5.7.26
-- Version de PHP :  7.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `nicoeats`
--

-- --------------------------------------------------------

--
-- Structure de la table `adress`
--

DROP TABLE IF EXISTS `adress`;
CREATE TABLE IF NOT EXISTS `adress` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `street_number` int(11) NOT NULL,
  `road` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `administrative_area` varchar(255) NOT NULL,
  `postal_code` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL,
  `comment` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `adress`
--

INSERT INTO `adress` (`id`, `id_user`, `name`, `street_number`, `road`, `city`, `administrative_area`, `postal_code`, `country`, `comment`) VALUES
(1, 1, 'Maison', 43, 'Rue Jacques Wagnon', 'Halluin', 'Hauts-de-France', '59250', 'France', ''),
(3, 1, 'Isen', 41, 'Boulevard Vauban', 'Lille', 'Hauts-de-France', '59000', 'France', 'Ecole d’ingénieur ISEN Lille'),
(4, 1, 'Maison (Mouvaux)', 31, 'Rue de Bir Hakeim', 'Mouvaux', 'Hauts-de-France', '59420', 'France', ''),
(5, 1, 'Maison (Canada)', 89, 'South Town Centre Boulevard', 'Markham', 'Ontario', 'L6G 1C3', 'Canada', 'je sais pas où c\'est démerde toi');

-- --------------------------------------------------------

--
-- Structure de la table `order`
--

DROP TABLE IF EXISTS `order`;
CREATE TABLE IF NOT EXISTS `order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `adress` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `order_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `order_code` int(255) DEFAULT NULL,
  `comment` text,
  `step` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `order`
--

INSERT INTO `order` (`id`, `id_user`, `adress`, `date`, `time`, `order_time`, `order_code`, `comment`, `step`) VALUES
(2, 1, NULL, NULL, NULL, '2019-08-08 11:06:02', NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `login` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `avatar_extension` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `firstname`, `name`, `login`, `password`, `avatar_extension`) VALUES
(1, 'Delattre', 'Benjamin', 'benjkap', '8a1086ad052a2a883f97716635179874edce6a34', 'jpg'),
(2, 'Delattre', 'Clemence', 'clemdelattre', '6194e4aa6b40253832893527f4872f4e4e6b11e0', 'png'),
(3, 'Delattre', 'Philippe', 'pilou', '8eeaa48923a52059402cdb8d0d4ce8a387c772ac', NULL),
(4, 'Delattre', 'Catherine', 'cath', '6404f3af8d5f24edd67b9457e5c5ae6f3ecbd1c8', NULL);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
