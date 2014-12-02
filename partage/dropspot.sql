-- phpMyAdmin SQL Dump
-- version 4.1.12
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Mar 02 Décembre 2014 à 13:58
-- Version du serveur :  5.6.16
-- Version de PHP :  5.5.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données :  `bdloc`
--

-- --------------------------------------------------------

--
-- Structure de la table `dropspot`
--

CREATE TABLE IF NOT EXISTS `dropspot` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `zip` varchar(5) NOT NULL,
  `latitude` varchar(255) NOT NULL,
  `longitude` varchar(255) NOT NULL,
  `dateCreated` datetime NOT NULL,
  `dateModified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=49 ;

--
-- Contenu de la table `dropspot`
--

INSERT INTO `dropspot` (`id`, `name`, `address`, `zip`, `latitude`, `longitude`, `dateCreated`, `dateModified`) VALUES
(1, 'Libria ', ' 82 Passage Choiseul ', '75002', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(2, 'Telecom Star ', ' 15 Bd de Bonne Nouvelle ', '75002', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(3, 'Hypso Reprographie ', ' 53 rue de Montmorency ', '75003', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(4, 'BM Pressing ', ' 4 Bis Bd Morland ', '75004', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(5, 'Game Cash / CG Paris 5 ', ' 21 rue Monge ', '75005', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(6, 'Chez Florence ', ' 11 rue Dauphine ', '75006', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(7, 'Aux Fleurs du Bac ', ' 69 rue du Bac ', '75007', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(8, 'Cordonnerie Serrurerie Grenell ', ' 165 rue de Grenelle ', '75007', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(9, 'Clean Pressing ', ' 15 rue Manuel ', '75009', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(10, 'Luffy ', ' 35 rue de Clichy ', '75009', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(11, 'Les Coteaux de Saumur ', ' 10 rue Bichat ', '75010', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(12, 'Magenta Art Deco ', ' 34 Ter rue du Dunkerque ', '75010', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(13, 'Baticlean 75 ', ' 191 rue de Charonne ', '75011', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(14, 'Cala Thé A ', ' 133 rue de Montreuil ', '75011', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(15, 'A Livr'' Ouvert ', ' 171 Bis Bd Voltaire ', '75011', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(16, 'Pressing Boulle ', ' 13 rue Boulle ', '75011', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(17, 'B.C.B.G / SARL Fleuve Bleu ', ' 18 rue Jules Valles ', '75011', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(18, 'L''Atelier du Trèfles Cadeaux ', ' 13 Bis Avenue Philippe Auguste ', '75011', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(19, 'Lio Optic ', ' 44 Bd Diderot ', '75012', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(20, 'A.M Presse Bizot ', ' 116 Av Général Michel Bizot ', '75012', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(21, 'Alanpark ', ' 105 rue de Charenton ', '75013', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(22, 'Okbi Presse ', ' 91 rue de Barrault ', '75013', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(23, 'Encherexpert ', ' 51 rue de Clisson ', '75013', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(24, 'Maison de la Presse ', ' 137 Bd Auguste Blanqui ', '75013', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(25, 'Ideal Optic ', ' 101 Av de France ', '75013', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(26, 'Chryzalys ', ' 206 Bd Raspail ', '75014', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(27, 'Agip Papeterie Côté Sud ', ' 133 Av du Maine ', '75014', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(28, 'Animalerie Little Zoo ', ' 40 Bd Brune ', '75014', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(29, 'Cordonnerie B.V.F ', ' 22 rue des Volontaires ', '75015', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(30, 'Moveux ', ' 14 rue Dupleix ', '75015', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(31, 'Saveurs du Sud ', ' 14 Av Félix Faure ', '75015', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(32, 'Anwa ', ' 105 Bis rue des Entrepreneurs ', '75015', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(33, 'Mercerie Poncet ', ' 15 rue Daumier ', '75016', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(34, 'Vu du XII ', ' 96 Av de Mozart ', '75016', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(35, 'Centre Literie ', ' 2 Bd Bessières ', '75017', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(36, 'Salon Marylène ', ' 45 rue Brochant ', '75017', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(37, 'Allo Micro ', ' 117 rue Legendre ', '75017', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(38, 'Encherexpert ', ' 61 rue Guy Moquet ', '75017', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(39, 'Au Rocher de Joie ', ' 12 rue Lamarck ', '75018', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(40, 'Consoplus Informatique ', ' 8 Bd Ney ', '75018', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(41, 'Unity Génération ', ' 17 rue Simart ', '75018', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(42, 'Isabelle Cherchevsky Atelier ', ' 15 rue Lagouat ', '75018', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(43, 'Labelencre ', ' 10 Av de La porte Brunet ', '75019', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(44, 'Prim Plus ', ' 9 rue du Cher ', '75020', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(45, 'Cadeaux Chics ', ' 27 rue Saint Fargeau ', '75020', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(46, 'Optic 62 ', ' 62 rue de Belleville ', '75020', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(47, 'Pressing 113 ', ' 113 Bd Davout ', '75020', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(48, 'Copy Conforme ', ' 25 rue Gatinée ', '75020', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
