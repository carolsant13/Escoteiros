-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 28/05/2026 às 17:47
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `minuano`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `atividades`
--

CREATE TABLE `atividades` (
  `id` int(10) UNSIGNED NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `slug` varchar(200) NOT NULL,
  `descricao` text DEFAULT NULL,
  `tipo` enum('acampamento','reuniao','evento','curso','outro') NOT NULL DEFAULT 'evento',
  `local` varchar(200) DEFAULT NULL,
  `data_inicio` datetime NOT NULL,
  `data_fim` datetime DEFAULT NULL,
  `imagem_url` varchar(255) DEFAULT NULL,
  `publicado` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `atividade_ramo`
--

CREATE TABLE `atividade_ramo` (
  `atividade_id` int(10) UNSIGNED NOT NULL,
  `ramo_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `contatos`
--

CREATE TABLE `contatos` (
  `id` int(10) UNSIGNED NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `assunto` varchar(150) DEFAULT NULL,
  `mensagem` text NOT NULL,
  `lido` tinyint(1) NOT NULL DEFAULT 0,
  `respondido` tinyint(1) NOT NULL DEFAULT 0,
  `ip` varchar(45) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `depoimentos`
--

CREATE TABLE `depoimentos` (
  `id` int(10) UNSIGNED NOT NULL,
  `nome` varchar(100) NOT NULL,
  `papel` varchar(100) DEFAULT NULL,
  `texto` text NOT NULL,
  `avatar_url` varchar(255) DEFAULT NULL,
  `aprovado` tinyint(1) NOT NULL DEFAULT 0,
  `ordem` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `destaques`
--

CREATE TABLE `destaques` (
  `id` int(10) UNSIGNED NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `descricao` text DEFAULT NULL,
  `icone` varchar(10) DEFAULT NULL,
  `imagem_url` varchar(255) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `noticia_id` int(10) UNSIGNED DEFAULT NULL,
  `ordem` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `documentos`
--

CREATE TABLE `documentos` (
  `id` int(11) NOT NULL,
  `categoria` varchar(255) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  `icone` varchar(10) DEFAULT '?',
  `tipo_fonte` varchar(50) NOT NULL,
  `arquivo_path` text DEFAULT NULL,
  `ordem` int(11) DEFAULT 0,
  `ativo` tinyint(1) DEFAULT 1,
  `criado_por` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `documentos`
--

INSERT INTO `documentos` (`id`, `categoria`, `titulo`, `descricao`, `icone`, `tipo_fonte`, `arquivo_path`, `ordem`, `ativo`, `criado_por`, `created_at`) VALUES
(1, 'teste1', 'Testando', 'teste1', '📄', 'upload', 'uploads/documentos/20260528_174416_documento_escoteiros_teste.pdf', 0, 1, 4, '2026-05-28 15:44:16');

-- --------------------------------------------------------

--
-- Estrutura para tabela `empresas_amigas`
--

CREATE TABLE `empresas_amigas` (
  `id` int(10) UNSIGNED NOT NULL,
  `nome` varchar(150) NOT NULL,
  `descricao` text DEFAULT NULL,
  `logo_url` varchar(255) DEFAULT NULL,
  `site_url` varchar(255) DEFAULT NULL,
  `nivel` enum('ouro','prata','bronze','apoio') NOT NULL DEFAULT 'apoio',
  `ordem` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `fotos`
--

CREATE TABLE `fotos` (
  `id` int(10) UNSIGNED NOT NULL,
  `galeria_id` int(10) UNSIGNED NOT NULL,
  `url` varchar(255) NOT NULL,
  `legenda` varchar(200) DEFAULT NULL,
  `ordem` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `fotos`
--

INSERT INTO `fotos` (`id`, `galeria_id`, `url`, `legenda`, `ordem`, `created_at`) VALUES
(1, 1, 'assets/uploads/galeria/foto_6a15fa600eae89.38918830.png', 'teste', 0, '2026-05-26 16:54:08');

-- --------------------------------------------------------

--
-- Estrutura para tabela `galerias`
--

CREATE TABLE `galerias` (
  `id` int(10) UNSIGNED NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `descricao` text DEFAULT NULL,
  `tipo` enum('acampamento','reuniao','evento','servico','outro') NOT NULL DEFAULT 'outro',
  `atividade_id` int(10) UNSIGNED DEFAULT NULL,
  `capa_url` varchar(255) DEFAULT NULL,
  `publicado` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `galerias`
--

INSERT INTO `galerias` (`id`, `titulo`, `descricao`, `tipo`, `atividade_id`, `capa_url`, `publicado`, `created_at`, `updated_at`) VALUES
(1, 'Testando', 'teste', 'acampamento', NULL, NULL, 1, '2026-05-26 16:53:55', '2026-05-26 16:53:55');

-- --------------------------------------------------------

--
-- Estrutura para tabela `logs_admin`
--

CREATE TABLE `logs_admin` (
  `id` int(10) UNSIGNED NOT NULL,
  `usuario_id` int(10) UNSIGNED DEFAULT NULL,
  `acao` varchar(100) NOT NULL,
  `tabela` varchar(60) DEFAULT NULL,
  `registro_id` int(10) UNSIGNED DEFAULT NULL,
  `detalhes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`detalhes`)),
  `ip` varchar(45) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `logs_admin`
--

INSERT INTO `logs_admin` (`id`, `usuario_id`, `acao`, `tabela`, `registro_id`, `detalhes`, `ip`, `created_at`) VALUES
(1, 1, 'inseriu usuário', 'usuarios', 3, '{\"nome\":\"maria\"}', '::1', '2026-05-22 17:01:01'),
(2, 1, 'excluiu usuário', 'usuarios', 3, '{\"nome\":\"maria\"}', '::1', '2026-05-22 17:09:25'),
(3, 1, 'inseriu usuário', 'usuarios', 4, '{\"nome\":\"ana\"}', '::1', '2026-05-22 17:16:03'),
(4, 1, 'inseriu atividade', 'atividades', 1, '{\"titulo\":\"teste de atividade\"}', '::1', '2026-05-25 22:09:51'),
(5, 4, 'inseriu atividade', 'atividades', 2, '{\"titulo\":\"testee\"}', '::1', '2026-05-25 22:16:00'),
(6, 4, 'inseriu atividade', 'atividades', 3, '{\"titulo\":\"dsfef\"}', '::1', '2026-05-25 22:17:16'),
(7, 4, 'atualizou atividade', 'atividades', 2, '{\"titulo\":\"testee\"}', '::1', '2026-05-25 22:22:35'),
(8, 4, 'atualizou atividade', 'atividades', 2, '{\"titulo\":\"testee\"}', '::1', '2026-05-25 22:22:42'),
(9, 4, 'atualizou atividade', 'atividades', 3, '{\"titulo\":\"dsfef\"}', '::1', '2026-05-25 22:22:48'),
(10, 4, 'inseriu atividade', 'atividades', 4, '{\"titulo\":\"fezwfrt\"}', '::1', '2026-05-25 22:34:10'),
(11, 4, 'excluiu atividade', 'atividades', 3, '{\"titulo\":\"dsfef\"}', '::1', '2026-05-26 14:44:25'),
(12, 4, 'excluiu atividade', 'atividades', 4, '{\"titulo\":\"fezwfrt\"}', '::1', '2026-05-26 14:44:27'),
(13, 4, 'excluiu atividade', 'atividades', 2, '{\"titulo\":\"testee\"}', '::1', '2026-05-26 14:44:29'),
(14, 4, 'excluiu atividade', 'atividades', 1, '{\"titulo\":\"teste de atividade\"}', '::1', '2026-05-26 14:44:31'),
(15, 1, 'atualizou usuário', 'usuarios', 4, '{\"nome\":\"ana\"}', '::1', '2026-05-26 16:36:27'),
(16, 4, 'adicionou documento', 'documentos', 1, '{\"titulo\":\"Testando\"}', '::1', '2026-05-28 12:44:16');

-- --------------------------------------------------------

--
-- Estrutura para tabela `membros`
--

CREATE TABLE `membros` (
  `id` int(10) UNSIGNED NOT NULL,
  `nome_completo` varchar(150) NOT NULL,
  `nome_escoteiro` varchar(80) DEFAULT NULL,
  `data_nascimento` date NOT NULL,
  `cpf` varchar(14) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `ramo_id` int(10) UNSIGNED NOT NULL,
  `cargo` varchar(80) DEFAULT NULL,
  `data_entrada` date NOT NULL,
  `data_saida` date DEFAULT NULL,
  `foto_url` varchar(255) DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `noticias`
--

CREATE TABLE `noticias` (
  `id` int(10) UNSIGNED NOT NULL,
  `titulo` varchar(250) NOT NULL,
  `slug` varchar(250) NOT NULL,
  `resumo` text DEFAULT NULL,
  `conteudo` longtext NOT NULL,
  `imagem_url` varchar(255) DEFAULT NULL,
  `autor_id` int(10) UNSIGNED DEFAULT NULL,
  `publicado` tinyint(1) NOT NULL DEFAULT 0,
  `publicado_em` datetime DEFAULT NULL,
  `destaque` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `ramos`
--

CREATE TABLE `ramos` (
  `id` int(10) UNSIGNED NOT NULL,
  `nome` varchar(80) NOT NULL,
  `slug` varchar(80) NOT NULL,
  `descricao` text DEFAULT NULL,
  `idade_min` decimal(4,1) NOT NULL,
  `idade_max` decimal(4,1) NOT NULL,
  `icone` varchar(10) DEFAULT NULL,
  `cor_fundo` varchar(7) DEFAULT NULL,
  `ordem` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `ramos`
--

INSERT INTO `ramos` (`id`, `nome`, `slug`, `descricao`, `idade_min`, `idade_max`, `icone`, `cor_fundo`, `ordem`, `ativo`, `created_at`, `updated_at`) VALUES
(1, 'Lobinhos', 'lobinhos', 'Primeiros passos na aventura escoteira, com jogos e histórias da floresta.', 6.5, 10.0, '🐺', '#fff3cd', 1, 1, '2026-05-12 22:17:36', '2026-05-12 22:17:36'),
(2, 'Escoteiros', 'escoteiros', 'Acampamentos, trilhas, primeiros socorros e desenvolvimento de competências.', 11.0, 14.0, '⚜️', '#d4edda', 2, 1, '2026-05-12 22:17:36', '2026-05-12 22:17:36'),
(3, 'Sênior', 'senior', 'Desafios maiores, expedições e desenvolvimento de liderança e cidadania.', 15.0, 17.0, '🏕️', '#cce5ff', 3, 1, '2026-05-12 22:17:36', '2026-05-12 22:17:36'),
(4, 'Pioneiros', 'pioneiros', 'Protagonismo, serviço comunitário e projetos de impacto social.', 18.0, 21.0, '🌿', '#f8d7da', 4, 1, '2026-05-12 22:17:36', '2026-05-12 22:17:36');

-- --------------------------------------------------------

--
-- Estrutura para tabela `slides`
--

CREATE TABLE `slides` (
  `id` int(10) UNSIGNED NOT NULL,
  `badge` varchar(60) DEFAULT NULL,
  `titulo` varchar(200) NOT NULL,
  `descricao` text DEFAULT NULL,
  `texto_botao` varchar(60) DEFAULT NULL,
  `link_botao` varchar(255) DEFAULT NULL,
  `cor_fundo` varchar(100) DEFAULT NULL,
  `imagem_url` varchar(255) DEFAULT NULL,
  `ordem` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(10) UNSIGNED NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `perfil` enum('admin','editor','visualizador') NOT NULL DEFAULT 'editor',
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `ultimo_login` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `perfil`, `ativo`, `ultimo_login`, `created_at`, `updated_at`) VALUES
(1, 'Administrador', 'admin@minuando.org.br', '$2y$10$979xnyw8bjXSiUWYG4L8Au1u8OoRAky21Va941VzW.qEoYnW0iSIC', 'admin', 1, '2026-05-26 16:51:22', '2026-05-12 22:17:37', '2026-05-26 16:51:22'),
(4, 'ana', 'ana@gmail.com', '$2y$12$tNWNAkboVO4Q0eKdKP6YVeeX0IrL2TTwiujwJ2H2QNYkKi5aDfEvi', 'editor', 1, '2026-05-28 12:36:15', '2026-05-22 17:16:03', '2026-05-28 12:36:15');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `atividades`
--
ALTER TABLE `atividades`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Índices de tabela `atividade_ramo`
--
ALTER TABLE `atividade_ramo`
  ADD PRIMARY KEY (`atividade_id`,`ramo_id`),
  ADD KEY `fk_ar_ramo` (`ramo_id`);

--
-- Índices de tabela `contatos`
--
ALTER TABLE `contatos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `depoimentos`
--
ALTER TABLE `depoimentos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `destaques`
--
ALTER TABLE `destaques`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_destaques_noticia` (`noticia_id`);

--
-- Índices de tabela `documentos`
--
ALTER TABLE `documentos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `empresas_amigas`
--
ALTER TABLE `empresas_amigas`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `fotos`
--
ALTER TABLE `fotos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_fotos_galeria` (`galeria_id`);

--
-- Índices de tabela `galerias`
--
ALTER TABLE `galerias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_galerias_atividade` (`atividade_id`);

--
-- Índices de tabela `logs_admin`
--
ALTER TABLE `logs_admin`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_logs_usuario` (`usuario_id`);

--
-- Índices de tabela `membros`
--
ALTER TABLE `membros`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cpf` (`cpf`),
  ADD KEY `idx_membros_ramo` (`ramo_id`),
  ADD KEY `idx_membros_ativo` (`ativo`);

--
-- Índices de tabela `noticias`
--
ALTER TABLE `noticias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `fk_noticias_autor` (`autor_id`),
  ADD KEY `idx_noticias_publicado` (`publicado`,`publicado_em`),
  ADD KEY `idx_noticias_destaque` (`destaque`);

--
-- Índices de tabela `ramos`
--
ALTER TABLE `ramos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Índices de tabela `slides`
--
ALTER TABLE `slides`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `atividades`
--
ALTER TABLE `atividades`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `contatos`
--
ALTER TABLE `contatos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `depoimentos`
--
ALTER TABLE `depoimentos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `destaques`
--
ALTER TABLE `destaques`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `documentos`
--
ALTER TABLE `documentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `empresas_amigas`
--
ALTER TABLE `empresas_amigas`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `fotos`
--
ALTER TABLE `fotos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `galerias`
--
ALTER TABLE `galerias`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `logs_admin`
--
ALTER TABLE `logs_admin`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de tabela `membros`
--
ALTER TABLE `membros`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `noticias`
--
ALTER TABLE `noticias`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `ramos`
--
ALTER TABLE `ramos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `slides`
--
ALTER TABLE `slides`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `atividade_ramo`
--
ALTER TABLE `atividade_ramo`
  ADD CONSTRAINT `fk_ar_atividade` FOREIGN KEY (`atividade_id`) REFERENCES `atividades` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ar_ramo` FOREIGN KEY (`ramo_id`) REFERENCES `ramos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `destaques`
--
ALTER TABLE `destaques`
  ADD CONSTRAINT `fk_destaques_noticia` FOREIGN KEY (`noticia_id`) REFERENCES `noticias` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `fotos`
--
ALTER TABLE `fotos`
  ADD CONSTRAINT `fk_fotos_galeria` FOREIGN KEY (`galeria_id`) REFERENCES `galerias` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `galerias`
--
ALTER TABLE `galerias`
  ADD CONSTRAINT `fk_galerias_atividade` FOREIGN KEY (`atividade_id`) REFERENCES `atividades` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `logs_admin`
--
ALTER TABLE `logs_admin`
  ADD CONSTRAINT `fk_logs_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `membros`
--
ALTER TABLE `membros`
  ADD CONSTRAINT `fk_membros_ramo` FOREIGN KEY (`ramo_id`) REFERENCES `ramos` (`id`);

--
-- Restrições para tabelas `noticias`
--
ALTER TABLE `noticias`
  ADD CONSTRAINT `fk_noticias_autor` FOREIGN KEY (`autor_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


