-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : sam. 20 juin 2026 à 15:18
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `tomtroc`
--

-- --------------------------------------------------------

--
-- Structure de la table `livre`
--

CREATE TABLE `livre` (
  `id` int(11) NOT NULL,
  `titre` varchar(128) NOT NULL,
  `auteur` varchar(128) NOT NULL,
  `propriétaire` varchar(128) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(256) NOT NULL,
  `propriétaireId` int(11) NOT NULL,
  `disponibilité` text NOT NULL DEFAULT 'disponible'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `livre`
--

INSERT INTO `livre` (`id`, `titre`, `auteur`, `propriétaire`, `description`, `image`, `propriétaireId`, `disponibilité`) VALUES
(1, 'The Kinkfolk Table', 'par Nathan Williams', 'Alexlecture', 'J\'ai récemment plongé dans les pages de \'The Kinfolk Table\' et j\'ai été enchanté par cette œuvre captivante. Ce livre va bien au-delà d\'une simple collection de recettes ; il célèbre l\'art de partager des moments authentiques autour de la table. \r\n\r\nLes photographies magnifiques et le ton chaleureux captivent dès le départ, transportant le lecteur dans un voyage à travers des recettes et des histoires qui mettent en avant la beauté de la simplicité et de la convivialité. \r\n\r\nChaque page est une invitation à ralentir, à savourer et à créer des souvenirs durables avec les êtres chers. \r\n\r\n\'The Kinfolk Table\' incarne parfaitement l\'esprit de la cuisine et de la camaraderie, et il est certain que ce livre trouvera une place spéciale dans le cœur de tout amoureux de la cuisine et des rencontres inspirantes.', 'book_6a072f9ae32942.43635941.webp', 1, 'indisponible'),
(2, 'Esther', 'Alabaster', 'CamilleClubLit', 'The Book of Esther: Curious and exciting, the brilliance of Esther is in its mysterious, unique intermingling of chance and divine providence. While its plot appears random, and chance-filled at first, it is an invitation to see it as a fateful encounter with God. It is an encouragement to all of us, to watch and to listen—with curiosity and attentiveness—for the implicit workings of God in serendipitous moments. It may not be explicit or how we would expect, but certainly God is there', 'book_6a07311e5d53f4.42550087.webp', 1, 'disponible'),
(3, 'milk and honey', 'Rupi Kaur', 'Hugo1990_12', 'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA', 'book_6a0731369d4510.08915522.png', 1, 'disponible'),
(6, 'The Sun and Her Flowers', 'Rupi kaur', 'P1', 'From Rupi Kaur, the bestselling author of Milk and Honey, comes her long-awaited second collection of poetry.\r\nIllustrated by Kaur, The Sun and Her Flowers is a journey of wilting, falling, rooting, rising and blooming. It is a celebration of love in all its forms.', 'book_6a073152d46a99.30781544.jpg', 1, 'disponible'),
(7, 'La Vague', 'Todd Strasser', 'P2', 'Pour faire comprendre les mécanismes du nazisme à ses élèves, Ben Ross, professeur d\'histoire, crée un mouvement expérimental au slogan fort : \" La Force par la Discipline, la Force par la Communauté, la Force par l\'Action. \" En l\'espace de quelques jours, l\'atmosphère du paisible lycée californien se transforme en microcosme totalitaire : avec une docilité effrayante, les élèves abandonnent leur libre arbitre pour répondre aux ordres de leur nouveau leader.\r\nQuel choc pourra être assez violent pour réveiller leurs consciences et mettre fin à la démonstration ?\r\n', 'book_6a07316f16a8e4.99718897.jpg', 1, 'disponible'),
(8, 'Antigone', 'Jean Anouilh', 'P3', '\"L\'Antigone de Sophocle, lue et relue et que je connaissais par coeur depuis toujours, a été un choc soudain pour moi pendant la guerre, le jour des petites affiches rouges. Je l\'ai réécrite à ma façon, avec la résonance de la tragédie que nous étions alors en train de vivre.\" Jean Anouilh.', 'book_6a073183999b72.39663613.jpg', 1, 'disponible'),
(9, 'L\'Art de la guerre', 'Sun Tzu', 'AAAA', 'L\'Art de la guerre de Sun Tzu est bien plus qu’un ancien traité de stratégie militaire : c’est un guide universel pour affronter les défis, prendre des décisions éclairées et triompher avec intelligence. Cette édition complète, traduite en français moderne et enrichie de notes et d’analyses, a été conçue pour rendre ce chef-d\'œuvre compréhensible, pertinent et vivant dans le monde d’aujourd’hui.', 'book_6a072e69e87122.39274926.jpg', 3, 'disponible'),
(10, 'Le Porteur de lumière, T1 : Le Prisme noir', 'Brent Weeks', 'AAAA', 'Gavin Guile est le Prisme, l\'homme le plus puissant du monde. Empereur et magicien, il est le gardien d\'une paix bien fragile.\r\nEt d\'un terrible secret.\r\nLes Prismes ne vivent jamais vieux, et Gavin sait exactement combien de temps il lui reste : cinq ans... et cinq missions impossibles a accomplir.\r\nPlus la lumière est vive, plus l\'ombre est profonde.', 'book_6a072ed8d40844.80797046.jpg', 3, 'disponible'),
(17, 'RE:ZERO - Re:vivre dans un autre monde à partir de zéro - tome 1', 'Tappei Nagatsuki', 'AAAA', 'Endurer les douleurs engendrées par la mort pour affronter les difficultés de la vie...\r\nUne réalité sans fin...\r\nSubaru Natsuki fait la connaissance d\'Émilia, une jeune fille aux longs cheveux d\'argent qui l\'entraîne dans une dimension peuplée de monstres et d\'ennemis en tous genres particulièrement hostiles. Le jeune homme a juré de la protéger, mais il ne résiste pas longtemps dans ce monde violent où il est tué rapidement.\r\n\r\nPourtant, il revient d\'entre les morts à l\'aide d\'un pouvoir qui le ramènera toujours à son point de départ. Subaru entame alors un combat perpétuel dans lequel il essaie, peu à peu, de changer le futur, où chaque fois les souvenirs sont à reconstruire...', 'book_6a369115e5b7b1.32242479.jpg', 3, 'indisponible');

-- --------------------------------------------------------

--
-- Structure de la table `messagerie`
--

CREATE TABLE `messagerie` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `read` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `content` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `messagerie`
--

INSERT INTO `messagerie` (`id`, `sender_id`, `receiver_id`, `read`, `created_at`, `content`) VALUES
(1, 5, 3, 1, '2026-05-22 00:42:43', 'test'),
(2, 3, 1, 1, '2026-05-23 11:39:13', 'test test'),
(3, 3, 5, 1, '2026-05-23 12:35:19', 'aaaa'),
(4, 3, 1, 1, '2026-06-14 13:06:52', 'Salut');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `mail` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nickname` varchar(255) NOT NULL,
  `avatar` varchar(256) NOT NULL,
  `member_since` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `mail`, `password`, `nickname`, `avatar`, `member_since`) VALUES
(1, 'U0@mail.com', '$2y$10$f27Zrb3Ev3EoetOn4SMFWO9GBbDJeXGxG4Kw8LUA2/YrQya1PWMO2', 'U0', '', '2025-04-03'),
(3, 'AA@mail.com', '$2y$10$cR7ATeGFXoIk3la6sisTgeba1Csb0.1B1z5DB5o3Ol2rHwEF3NsZ6', 'AAAA', 'assets/users/3_1779979053.png', '2025-04-03'),
(5, '1@mail.com', '$2y$10$B.n7ivuvM5XemiFdzx2gpuoKFjIp3UBMWHmua5yNcZVxg/0GBHA6K', 'u1', 'assets/users/5_1779979489.jpg', '2026-05-30'),
(7, 'test@test.com', '$2y$10$4bOKpiFKWiGHKsX7mdQ6auZ3EmK1qib/ilGLQmiqxZCad/Dbyl/86', 'test', 'assets/users/7_1780750804.png', '2026-06-06'),
(8, 'u2@mail.com', '$2y$10$YuZfSxCrFyume5bv/WK8xuZ6Arf0OzZbjTTlucmF7cDAlsmd4vIYK', 'u3', 'assets/users/8_1781443024.png', '2026-06-14');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `livre`
--
ALTER TABLE `livre`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `messagerie`
--
ALTER TABLE `messagerie`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `livre`
--
ALTER TABLE `livre`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT pour la table `messagerie`
--
ALTER TABLE `messagerie`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
