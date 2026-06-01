-- ============================================
-- SORTEO IPHONE 17 PRO MAX - Base de Datos
-- Ejecutar este script en phpMyAdmin
-- ============================================

-- Crear base de datos (opcional, si no existe)
-- CREATE DATABASE IF NOT EXISTS sorteo_iphone CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE sorteo_iphone;

-- ============================================
-- Tabla de tickets (participantes confirmados)
-- ============================================
CREATE TABLE IF NOT EXISTS `tickets` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `nombre` VARCHAR(100) NOT NULL,
    `email` VARCHAR(150) NOT NULL,
    `telefono` VARCHAR(20) NOT NULL,
    `codigo` VARCHAR(15) NOT NULL,
    `fecha_registro` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `monto` INT(11) NOT NULL DEFAULT 1000,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_codigo` (`codigo`),
    INDEX `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabla de configuración del sorteo (clave/valor)
-- ============================================
CREATE TABLE IF NOT EXISTS `config` (
    `clave` VARCHAR(50) NOT NULL,
    `valor` TEXT NULL,
    PRIMARY KEY (`clave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Valores iniciales de configuración
-- ============================================
INSERT INTO `config` (`clave`, `valor`) VALUES
    ('sorteo_activo', '1'),
    ('fecha_cierre', '2026-03-31 23:59:59'),
    ('codigo_ganador', NULL),
    ('link_mercadopago', 'https://link.mercadopago.cl/TU_ENLACE_AQUI')
ON DUPLICATE KEY UPDATE `clave` = `clave`;
