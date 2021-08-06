-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 06-Ago-2021 às 18:04
-- Versão do servidor: 10.4.20-MariaDB
-- versão do PHP: 8.0.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `forum_pap`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `ci_sessions`
--

CREATE TABLE `ci_sessions` (
  `id` varchar(40) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `data` blob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `contacto`
--

CREATE TABLE `contacto` (
  `id` int(11) NOT NULL,
  `tipo` varchar(30) NOT NULL,
  `msg` text NOT NULL,
  `idUser` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `idForum` int(11) DEFAULT NULL,
  `idTopic` int(11) DEFAULT NULL,
  `idPost` int(11) DEFAULT NULL,
  `img` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `contacto`
--

INSERT INTO `contacto` (`id`, `tipo`, `msg`, `idUser`, `created_at`, `idForum`, `idTopic`, `idPost`, `img`) VALUES
(37, 'TopicReport', 'Reported Content', 28, '2021-07-21 14:23:20', NULL, 48, NULL, NULL),
(38, 'ForumReport', 'Reported Content', 28, '2021-07-21 14:50:16', 21, NULL, NULL, NULL),
(39, 'Question', 'Olaaa', 25, '2021-07-21 16:36:13', NULL, NULL, NULL, NULL),
(40, 'PostReport', 'Reported Content', 24, '2021-07-21 16:45:00', NULL, NULL, 103, NULL),
(41, 'ForumReport', 'Reported Content', 24, '2021-07-21 16:45:09', 22, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `feed`
--

CREATE TABLE `feed` (
  `id` int(11) NOT NULL,
  `tipo_notificacao` varchar(30) NOT NULL,
  `msg` text NOT NULL,
  `user_id` int(11) NOT NULL,
  `sent_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `feed`
--

INSERT INTO `feed` (`id`, `tipo_notificacao`, `msg`, `user_id`, `sent_at`) VALUES
(8, 'Support Reply', 'Example Message to user', 25, '2021-07-21 11:27:46'),
(11, 'Support Reply', 'Hello jonhy444 how are you', 25, '2021-07-21 16:37:53');

-- --------------------------------------------------------

--
-- Estrutura da tabela `forums`
--

CREATE TABLE `forums` (
  `id` int(11) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `slug` varchar(255) NOT NULL DEFAULT '',
  `description` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `id_user` int(11) NOT NULL,
  `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '[]' CHECK (json_valid(`tags`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `forums`
--

INSERT INTO `forums` (`id`, `title`, `slug`, `description`, `created_at`, `updated_at`, `id_user`, `tags`) VALUES
(22, 'Games', 'games', 'Something about videogames', '2021-07-20 21:11:39', NULL, 24, '[\"games\"]');

-- --------------------------------------------------------

--
-- Estrutura da tabela `posts`
--

CREATE TABLE `posts` (
  `id` int(11) UNSIGNED NOT NULL,
  `content` longtext NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `topic_id` int(11) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `replying_to` int(11) DEFAULT NULL,
  `deleted` tinyint(4) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `img` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `posts`
--

INSERT INTO `posts` (`id`, `content`, `user_id`, `topic_id`, `created_at`, `updated_at`, `replying_to`, `deleted`, `updated_by`, `img`) VALUES
(100, 'Minecraft is a sandbox construction video game developed by Mojang Studios where players interact with a fully modifiable three-dimensional environment made of blocks and entities. Its diverse gameplay lets players choose the way they play, allowing for countless possibilities.\r\nThere are three actively maintained editions of Minecraft: Java Edition, Bedrock Edition, and Education Edition.', 24, 47, '2021-07-20 21:12:53', NULL, NULL, 0, 0, NULL),
(101, 'If you like yourself don´t play the game... Just don\'t......', 27, 48, '2021-07-20 21:15:10', NULL, NULL, 0, 0, '27-posts-1626808510.jpg'),
(102, 'Wow, you know a lot about minecraft!', 27, 47, '2021-07-20 21:15:59', NULL, 100, 0, 0, NULL),
(103, 'Yah i play it since 2010', 24, 47, '2021-07-20 21:17:07', NULL, 102, 0, 0, NULL),
(104, 'WOOW! You have been playing it for a while...', 27, 47, '2021-07-20 21:18:14', NULL, 103, 0, 0, NULL),
(105, 'Jokes on you! I play it since 2007', 25, 47, '2021-07-20 21:19:50', NULL, 103, 0, 0, NULL),
(107, 'Why?', 25, 48, '2021-07-20 22:35:12', NULL, 101, 0, 0, NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `topics`
--

CREATE TABLE `topics` (
  `id` int(11) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `slug` varchar(255) NOT NULL DEFAULT '',
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `forum_id` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `topics`
--

INSERT INTO `topics` (`id`, `title`, `slug`, `created_at`, `updated_at`, `user_id`, `forum_id`) VALUES
(47, 'Minecraft', 'minecraft', '2021-07-20 21:12:53', '2021-07-20 21:19:50', 24, 22),
(48, 'League of Legends', 'league-of-legends', '2021-07-20 21:15:10', '2021-07-20 22:35:12', 27, 22);

-- --------------------------------------------------------

--
-- Estrutura da tabela `users`
--

CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL,
  `username` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL DEFAULT '',
  `avatar` varchar(255) DEFAULT 'default.jpg',
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` int(11) UNSIGNED NOT NULL,
  `is_admin` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `is_moderator` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `is_confirmed` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `fame` int(4) NOT NULL DEFAULT 0,
  `users_helped` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`users_helped`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `avatar`, `created_at`, `updated_at`, `updated_by`, `is_admin`, `is_moderator`, `is_confirmed`, `fame`, `users_helped`) VALUES
(24, 'Admin', 'markinhot2003@gmail.com', '$2y$10$mVQQJqMZNvw.WQDkJ967k.Df0fYxj852AUQ8Jy39fielCYVBsQhPm', '24-avatar1626807271.png', '2021-07-18 23:45:14', '2021-07-20 23:54:32', 24, 1, 0, 0, 1001, '[\"24\"]'),
(25, 'Jonhy444', 'joaos92@gmail.com', '$2y$10$yZtqPbP0aeSvPT1hMwIhRenNQOEa60.5kS0BT2nsyjCDihNisWbni', '25-avatar1626730388.png', '2021-07-19 23:32:16', '2021-07-21 16:36:55', 24, 1, 0, 0, 36, '[\"25\",\"29\"]'),
(26, 'Marko2003', 'markinhot@gmail.com', '$2y$10$0U41DpP3Wr1t8fhoW5ggx.xYHnxQdax7p3s1WQsE0It9MxTBcwSYy', 'default.jpg', '2021-07-19 23:51:04', NULL, 0, 0, 0, 0, 0, '[]'),
(27, 'Bernard', 'marcao@gmail.com', '$2y$10$5UWUKH06hRAgHUWcNT4JcO8XUxaRVJL2ezl57FfhSydov2KzTtho.', '27-avatar1626871501.png', '2021-07-20 21:10:32', NULL, 0, 0, 0, 0, 0, '[]'),
(28, 'Paulinho22', 'paulo@gmail.com', '$2y$10$MY9nCTTPp9mwmH.crp6Nau0CzLuqdmfrG.jod3kIuRwVwLo9yrqoe', 'default.jpg', '2021-07-21 12:40:35', NULL, 0, 0, 0, 0, 1, '[\"28\"]');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `ci_sessions`
--
ALTER TABLE `ci_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ci_sessions_timestamp` (`timestamp`);

--
-- Índices para tabela `contacto`
--
ALTER TABLE `contacto`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `feed`
--
ALTER TABLE `feed`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `forums`
--
ALTER TABLE `forums`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `topics`
--
ALTER TABLE `topics`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `contacto`
--
ALTER TABLE `contacto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT de tabela `feed`
--
ALTER TABLE `feed`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `forums`
--
ALTER TABLE `forums`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de tabela `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=112;

--
-- AUTO_INCREMENT de tabela `topics`
--
ALTER TABLE `topics`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
