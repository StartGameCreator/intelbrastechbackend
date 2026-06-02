-- =====================================================================
-- SCRIPT DE BANCO DE DADOS - PLATAFORMA INTELBRASTECH (FASE 1)
-- COMPATÍVEL COM MYSQL 8+ E ESTRUTURA LARAVEL SANCTUM
-- =====================================================================

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `reports_rat`;
DROP TABLE IF EXISTS `meetings`;
DROP TABLE IF EXISTS `likes`;
DROP TABLE IF EXISTS `comments`;
DROP TABLE IF EXISTS `posts`;
DROP TABLE IF EXISTS `tickets`;
DROP TABLE IF EXISTS `category_technician`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `technicians`;
DROP TABLE IF EXISTS `users`;
SET FOREIGN_KEY_CHECKS = 1;

-- ---------------------------------------------------------------------
-- 1. TABELA: users (Autenticação Centralizada e Socialite)
-- ---------------------------------------------------------------------
CREATE TABLE `users` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `password` VARCHAR(255) DEFAULT NULL,
  `role` ENUM('master', 'regional', 'technician', 'client') NOT NULL DEFAULT 'client',
  `google_id` VARCHAR(255) DEFAULT NULL,
  `microsoft_id` VARCHAR(255) DEFAULT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 0,
  `remember_token` VARCHAR(100) DEFAULT NULL,
  `fcm_token` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_google_id_index` (`google_id`),
  KEY `users_microsoft_id_index` (`microsoft_id`),
  KEY `users_is_active_index` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 2. TABELA: technicians (Perfil do Profissional Parceiro)
-- ---------------------------------------------------------------------
CREATE TABLE `technicians` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `cpf` VARCHAR(14) DEFAULT NULL,
  `rg` VARCHAR(20) DEFAULT NULL,
  `cnpj` VARCHAR(18) DEFAULT NULL,
  `company_name` VARCHAR(255) DEFAULT NULL,
  `crea` VARCHAR(50) DEFAULT NULL,
  `crt` VARCHAR(50) DEFAULT NULL,
  `cft` VARCHAR(50) DEFAULT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `whatsapp` VARCHAR(20) NOT NULL,
  `avatar_url` VARCHAR(255) DEFAULT NULL,
  `bio` TEXT DEFAULT NULL,
  `cep` VARCHAR(9) NOT NULL,
  `state` CHAR(2) NOT NULL,
  `city` VARCHAR(255) NOT NULL,
  `neighborhood` VARCHAR(255) NOT NULL,
  `location` POINT NOT NULL SRID 4326, -- Geometria GPS WGS84 Obligatória no MySQL 8
  `rating_cache` DECIMAL(3,2) NOT NULL DEFAULT '5.00',
  `jobs_completed` INT UNSIGNED NOT NULL DEFAULT '0',
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `technicians_cpf_unique` (`cpf`),
  UNIQUE KEY `technicians_cnpj_unique` (`cnpj`),
  CONSTRAINT `technicians_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  SPATIAL INDEX `technicians_location_spatial` (`location`), -- Índice de alta performance para o mapa
  KEY `technicians_regional_index` (`state`, `city`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 3. TABELA: categories (Especialidades Atendidas)
-- ---------------------------------------------------------------------
CREATE TABLE `categories` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_name_unique` (`name`),
  UNIQUE KEY `categories_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 4. TABELA PIVÔ: category_technician (Relacionamento N:M Especialidades)
-- ---------------------------------------------------------------------
CREATE TABLE `category_technician` (
  `technician_id` BIGINT UNSIGNED NOT NULL,
  `category_id` BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`technician_id`, `category_id`),
  CONSTRAINT `cat_tech_technician_id_foreign` FOREIGN KEY (`technician_id`) REFERENCES `technicians` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cat_tech_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 5. TABELA: tickets (Ordens de Serviço / Chamados)
-- ---------------------------------------------------------------------
CREATE TABLE `tickets` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `client_id` BIGINT UNSIGNED NOT NULL,
  `technician_id` BIGINT UNSIGNED DEFAULT NULL,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `status` ENUM('open', 'assigned', 'accepted', 'confirmed', 'in_progress', 'completed', 'cancelled') NOT NULL DEFAULT 'open',
  `contact_released` TINYINT(1) NOT NULL DEFAULT 0,
  `cep` VARCHAR(9) DEFAULT NULL,
  `state` CHAR(2) DEFAULT NULL,
  `city` VARCHAR(255) DEFAULT NULL,
  `location` POINT DEFAULT NULL SRID 4326,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `tickets_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tickets_technician_id_foreign` FOREIGN KEY (`technician_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  SPATIAL INDEX `tickets_location_spatial` (`location`),
  KEY `tickets_lookup_index` (`status`, `client_id`, `state`, `city`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 6. TABELA: reports_rat (Relatório de Atendimento Técnico pós-fechamento)
-- ---------------------------------------------------------------------
CREATE TABLE `reports_rat` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `ticket_id` BIGINT UNSIGNED NOT NULL,
  `technician_id` BIGINT UNSIGNED NOT NULL,
  `start_time` DATETIME NOT NULL,
  `end_time` DATETIME NOT NULL,
  `services_performed` TEXT NOT NULL,
  `materials_used` TEXT DEFAULT NULL,
  `client_signature` LONGTEXT NOT NULL, -- Recebe String Base64 do SignaturePad JS
  `technician_signature` LONGTEXT NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `reports_rat_ticket_id_foreign` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reports_rat_technician_id_foreign` FOREIGN KEY (`technician_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 7. TABELA: posts (Rede Social Técnica - Publicações)
-- ---------------------------------------------------------------------
CREATE TABLE `posts` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `title` VARCHAR(255) DEFAULT NULL,
  `content` TEXT NOT NULL,
  `media_url` VARCHAR(255) DEFAULT NULL,
  `media_type` ENUM('image', 'video', 'none') NOT NULL DEFAULT 'none',
  `likes_count` INT UNSIGNED NOT NULL DEFAULT '0',
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `posts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 8. TABELA: comments (Rede Social Técnica - Comentários)
-- ---------------------------------------------------------------------
CREATE TABLE `comments` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `post_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `comment` TEXT NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `comments_post_id_foreign` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 9. TABELA: likes (Rede Social Técnica - Curtidas Únicas)
-- ---------------------------------------------------------------------
CREATE TABLE `likes` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `post_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `likes_post_user_unique` (`post_id`, `user_id`),
  CONSTRAINT `likes_post_id_foreign` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `likes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 10. TABELA: meetings (Videoconferências Remotas via Meet / Teams)
-- ---------------------------------------------------------------------
CREATE TABLE `meetings` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT,
  `ticket_id` BIGINT UNSIGNED NOT NULL,
  `platform` ENUM('google_meet', 'ms_teams') NOT NULL,
  `meeting_id` VARCHAR(255) DEFAULT NULL,
  `join_url` TEXT NOT NULL,
  `scheduled_at` DATETIME NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `meetings_ticket_id_foreign` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;