-- ============================================================
--  71º Grupo de Escoteiros Minuano — Banco de Dados MySQL
--  Arquivo: database.sql
--  Criado em: 2025
-- ============================================================

CREATE DATABASE IF NOT EXISTS minuano
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE minuano;

-- ============================================================
-- USUÁRIOS (área restrita / administradores)
-- ============================================================
CREATE TABLE usuarios (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome          VARCHAR(100)        NOT NULL,
  email         VARCHAR(150)        NOT NULL UNIQUE,
  senha         VARCHAR(255)        NOT NULL,          -- hash bcrypt
  perfil        ENUM('admin','editor','visualizador')  NOT NULL DEFAULT 'editor',
  ativo         TINYINT(1)          NOT NULL DEFAULT 1,
  ultimo_login  DATETIME            NULL,
  created_at    DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- RAMOS (Lobinhos, Escoteiros, Sênior, Pioneiros)
-- ============================================================
CREATE TABLE ramos (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome          VARCHAR(80)         NOT NULL,
  slug          VARCHAR(80)         NOT NULL UNIQUE,
  descricao     TEXT                NULL,
  idade_min     DECIMAL(4,1)        NOT NULL,           -- ex: 6.5
  idade_max     DECIMAL(4,1)        NOT NULL,
  icone         VARCHAR(10)         NULL,               -- emoji ou nome de ícone
  cor_fundo     VARCHAR(7)          NULL,               -- hex, ex: #fff3cd
  ordem         TINYINT UNSIGNED    NOT NULL DEFAULT 0,
  ativo         TINYINT(1)          NOT NULL DEFAULT 1,
  created_at    DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Dados iniciais dos ramos
INSERT INTO ramos (nome, slug, descricao, idade_min, idade_max, icone, cor_fundo, ordem) VALUES
('Lobinhos',   'lobinhos',   'Primeiros passos na aventura escoteira, com jogos e histórias da floresta.', 6.5, 10,  '🐺', '#fff3cd', 1),
('Escoteiros', 'escoteiros', 'Acampamentos, trilhas, primeiros socorros e desenvolvimento de competências.', 11,  14,  '⚜️', '#d4edda', 2),
('Sênior',     'senior',     'Desafios maiores, expedições e desenvolvimento de liderança e cidadania.',    15,  17,  '🏕️', '#cce5ff', 3),
('Pioneiros',  'pioneiros',  'Protagonismo, serviço comunitário e projetos de impacto social.',             18,  21,  '🌿', '#f8d7da', 4);

-- ============================================================
-- SLIDES DO CARROSSEL (hero)
-- ============================================================
CREATE TABLE slides (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  badge         VARCHAR(60)         NULL,               -- ex: "Desde 1981"
  titulo        VARCHAR(200)        NOT NULL,
  descricao     TEXT                NULL,
  texto_botao   VARCHAR(60)         NULL,
  link_botao    VARCHAR(255)        NULL,
  cor_fundo     VARCHAR(100)        NULL,               -- gradiente CSS ou hex
  imagem_url    VARCHAR(255)        NULL,               -- imagem de fundo opcional
  ordem         TINYINT UNSIGNED    NOT NULL DEFAULT 0,
  ativo         TINYINT(1)          NOT NULL DEFAULT 1,
  created_at    DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- NOTÍCIAS
-- ============================================================
CREATE TABLE noticias (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  titulo        VARCHAR(250)        NOT NULL,
  slug          VARCHAR(250)        NOT NULL UNIQUE,
  resumo        TEXT                NULL,
  conteudo      LONGTEXT            NOT NULL,
  imagem_url    VARCHAR(255)        NULL,
  autor_id      INT UNSIGNED        NULL,
  publicado     TINYINT(1)          NOT NULL DEFAULT 0,
  publicado_em  DATETIME            NULL,
  destaque      TINYINT(1)          NOT NULL DEFAULT 0, -- aparece na seção Destaques
  created_at    DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  CONSTRAINT fk_noticias_autor FOREIGN KEY (autor_id) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE INDEX idx_noticias_publicado    ON noticias(publicado, publicado_em);
CREATE INDEX idx_noticias_destaque     ON noticias(destaque);

-- ============================================================
-- DESTAQUES (cards da seção Destaques — podem ser independentes)
-- ============================================================
CREATE TABLE destaques (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  titulo        VARCHAR(200)        NOT NULL,
  descricao     TEXT                NULL,
  icone         VARCHAR(10)         NULL,               -- emoji
  imagem_url    VARCHAR(255)        NULL,
  link          VARCHAR(255)        NULL,
  noticia_id    INT UNSIGNED        NULL,               -- vincula a uma notícia, se existir
  ordem         TINYINT UNSIGNED    NOT NULL DEFAULT 0,
  ativo         TINYINT(1)          NOT NULL DEFAULT 1,
  created_at    DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  CONSTRAINT fk_destaques_noticia FOREIGN KEY (noticia_id) REFERENCES noticias(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================================
-- DEPOIMENTOS
-- ============================================================
CREATE TABLE depoimentos (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome          VARCHAR(100)        NOT NULL,
  papel         VARCHAR(100)        NULL,               -- ex: "Mãe de escoteiro"
  texto         TEXT                NOT NULL,
  avatar_url    VARCHAR(255)        NULL,
  aprovado      TINYINT(1)          NOT NULL DEFAULT 0, -- moderação antes de publicar
  ordem         TINYINT UNSIGNED    NOT NULL DEFAULT 0,
  created_at    DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- DOCUMENTOS (ata, regulamentos, formulários para download)
-- ============================================================
CREATE TABLE documentos (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  titulo        VARCHAR(200)        NOT NULL,
  descricao     VARCHAR(400)        NULL,
  arquivo_url   VARCHAR(255)        NOT NULL,
  categoria     VARCHAR(80)         NULL,               -- ex: "Regulamentos", "Formulários"
  restrito      TINYINT(1)          NOT NULL DEFAULT 0, -- só aparece na área restrita
  ordem         TINYINT UNSIGNED    NOT NULL DEFAULT 0,
  ativo         TINYINT(1)          NOT NULL DEFAULT 1,
  created_at    DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- EMPRESAS AMIGAS (patrocinadores / apoiadores)
-- ============================================================
CREATE TABLE empresas_amigas (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome          VARCHAR(150)        NOT NULL,
  descricao     TEXT                NULL,
  logo_url      VARCHAR(255)        NULL,
  site_url      VARCHAR(255)        NULL,
  nivel         ENUM('ouro','prata','bronze','apoio') NOT NULL DEFAULT 'apoio',
  ordem         TINYINT UNSIGNED    NOT NULL DEFAULT 0,
  ativo         TINYINT(1)          NOT NULL DEFAULT 1,
  created_at    DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- MEMBROS DO GRUPO (escoteiros cadastrados)
-- ============================================================
CREATE TABLE membros (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome_completo   VARCHAR(150)      NOT NULL,
  nome_escoteiro  VARCHAR(80)       NULL,               -- apelido/nome escoteiro
  data_nascimento DATE              NOT NULL,
  cpf             VARCHAR(14)       NULL UNIQUE,
  email           VARCHAR(150)      NULL,
  telefone        VARCHAR(20)       NULL,
  ramo_id         INT UNSIGNED      NOT NULL,
  cargo           VARCHAR(80)       NULL,               -- ex: "Chefe de Patrulha"
  data_entrada    DATE              NOT NULL,
  data_saida      DATE              NULL,
  foto_url        VARCHAR(255)      NULL,
  ativo           TINYINT(1)        NOT NULL DEFAULT 1,
  created_at      DATETIME          NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      DATETIME          NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  CONSTRAINT fk_membros_ramo FOREIGN KEY (ramo_id) REFERENCES ramos(id)
) ENGINE=InnoDB;

CREATE INDEX idx_membros_ramo  ON membros(ramo_id);
CREATE INDEX idx_membros_ativo ON membros(ativo);

-- ============================================================
-- ATIVIDADES / EVENTOS
-- ============================================================
CREATE TABLE atividades (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  titulo        VARCHAR(200)        NOT NULL,
  slug          VARCHAR(200)        NOT NULL UNIQUE,
  descricao     TEXT                NULL,
  tipo          ENUM('acampamento','reuniao','evento','curso','outro') NOT NULL DEFAULT 'evento',
  local         VARCHAR(200)        NULL,
  data_inicio   DATETIME            NOT NULL,
  data_fim      DATETIME            NULL,
  imagem_url    VARCHAR(255)        NULL,
  publicado     TINYINT(1)          NOT NULL DEFAULT 0,
  created_at    DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Ramos participantes de cada atividade (N:N)
CREATE TABLE atividade_ramo (
  atividade_id  INT UNSIGNED        NOT NULL,
  ramo_id       INT UNSIGNED        NOT NULL,
  PRIMARY KEY (atividade_id, ramo_id),
  CONSTRAINT fk_ar_atividade FOREIGN KEY (atividade_id) REFERENCES atividades(id) ON DELETE CASCADE,
  CONSTRAINT fk_ar_ramo      FOREIGN KEY (ramo_id)      REFERENCES ramos(id)      ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- GALERIA DE FOTOS
-- ============================================================
CREATE TABLE galerias (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  titulo        VARCHAR(200)        NOT NULL,
  descricao     TEXT                NULL,
  atividade_id  INT UNSIGNED        NULL,
  capa_url      VARCHAR(255)        NULL,
  publicado     TINYINT(1)          NOT NULL DEFAULT 0,
  created_at    DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  CONSTRAINT fk_galerias_atividade FOREIGN KEY (atividade_id) REFERENCES atividades(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE fotos (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  galeria_id    INT UNSIGNED        NOT NULL,
  url           VARCHAR(255)        NOT NULL,
  legenda       VARCHAR(200)        NULL,
  ordem         SMALLINT UNSIGNED   NOT NULL DEFAULT 0,
  created_at    DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,

  CONSTRAINT fk_fotos_galeria FOREIGN KEY (galeria_id) REFERENCES galerias(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- FORMULÁRIO DE CONTATO / FALE CONOSCO
-- ============================================================
CREATE TABLE contatos (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome          VARCHAR(100)        NOT NULL,
  email         VARCHAR(150)        NOT NULL,
  telefone      VARCHAR(20)         NULL,
  assunto       VARCHAR(150)        NULL,
  mensagem      TEXT                NOT NULL,
  lido          TINYINT(1)          NOT NULL DEFAULT 0,
  respondido    TINYINT(1)          NOT NULL DEFAULT 0,
  ip            VARCHAR(45)         NULL,
  created_at    DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- LOG DE AÇÕES ADMINISTRATIVAS (auditoria)
-- ============================================================
CREATE TABLE logs_admin (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  usuario_id    INT UNSIGNED        NULL,
  acao          VARCHAR(100)        NOT NULL,           -- ex: "criou notícia"
  tabela        VARCHAR(60)         NULL,
  registro_id   INT UNSIGNED        NULL,
  detalhes      JSON                NULL,
  ip            VARCHAR(45)         NULL,
  created_at    DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,

  CONSTRAINT fk_logs_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================================
-- USUÁRIO ADMINISTRADOR PADRÃO
-- Senha: admin123 (trocar imediatamente após o primeiro acesso)
-- Hash bcrypt gerado com 12 rounds
-- ============================================================
INSERT INTO usuarios (nome, email, senha, perfil) VALUES
('Administrador', 'admin@minuando.org.br',
 '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- senha: admin123
 'admin');
