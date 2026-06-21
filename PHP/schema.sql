-- Crear tabla de administradores
CREATE TABLE IF NOT EXISTS `admin` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `usuario` VARCHAR(50) NOT NULL UNIQUE,
    `contrasena` VARCHAR(255) NOT NULL
);

-- Crear tabla de laboratorios
CREATE TABLE IF NOT EXISTS `laboratorios` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(50) NOT NULL UNIQUE
);

-- Crear tabla de grupos
CREATE TABLE IF NOT EXISTS `grupos` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(50) NOT NULL UNIQUE,
    `laboratorio_id` INT NOT NULL,
    `hora_inicio` TIME NOT NULL,
    `hora_fin` TIME NOT NULL,
    `limite_alumnos` INT DEFAULT 30,
    FOREIGN KEY (`laboratorio_id`) REFERENCES `laboratorios` (`id`) ON DELETE CASCADE
);

-- Crear tabla de alumnos
CREATE TABLE IF NOT EXISTS `alumnos_nuevo_ingreso` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `boleta` VARCHAR(10) NOT NULL UNIQUE,
    `nombre` VARCHAR(150) NOT NULL,
    `fecha_nacimiento` DATE NOT NULL,
    `genero` VARCHAR(20) NOT NULL,
    `curp` VARCHAR(18) NOT NULL UNIQUE,
    `entidad_federativa` VARCHAR(50) NOT NULL,
    `escuela_procedencia` VARCHAR(100) NOT NULL,
    `nombre_escuela` VARCHAR(150) NOT NULL,
    `promedio` DECIMAL(4, 2) NOT NULL,
    `correo` VARCHAR(100) NOT NULL UNIQUE,
    `contrasena` VARCHAR(255) NOT NULL,
    `telefono` VARCHAR(15) DEFAULT NULL,
    `grupo_id` INT DEFAULT NULL,
    FOREIGN KEY (`grupo_id`) REFERENCES `grupos` (`id`) ON DELETE SET NULL
);

-- Insertar los 5 laboratorios
INSERT IGNORE INTO `laboratorios` (`id`, `nombre`) VALUES
(1, 'Laboratorio I'),
(2, 'Laboratorio II'),
(3, 'Laboratorio III'),
(4, 'Laboratorio IV'),
(5, 'Laboratorio V');

-- Insertar los 15 grupos con sus horarios y laboratorios asignados
-- Horario 1 (10:00 AM - 11:30 AM): Grupos I al V en Labs I al V
-- Horario 2 (11:45 AM - 01:15 PM): Grupos VI al X en Labs I al V
-- Horario 3 (01:30 PM - 03:00 PM): Grupos XI al XV en Labs I al V
INSERT IGNORE INTO `grupos` (`id`, `nombre`, `laboratorio_id`, `hora_inicio`, `hora_fin`, `limite_alumnos`) VALUES
-- Horario 1 (10:00 AM - 11:30 AM)
(1, 'Grupo I', 1, '10:00:00', '11:30:00', 30),
(2, 'Grupo II', 2, '10:00:00', '11:30:00', 30),
(3, 'Grupo III', 3, '10:00:00', '11:30:00', 30),
(4, 'Grupo IV', 4, '10:00:00', '11:30:00', 30),
(5, 'Grupo V', 5, '10:00:00', '11:30:00', 30),
-- Horario 2 (11:45 AM - 01:15 PM)
(6, 'Grupo VI', 1, '11:45:00', '13:15:00', 30),
(7, 'Grupo VII', 2, '11:45:00', '13:15:00', 30),
(8, 'Grupo VIII', 3, '11:45:00', '13:15:00', 30),
(9, 'Grupo IX', 4, '11:45:00', '13:15:00', 30),
(10, 'Grupo X', 5, '11:45:00', '13:15:00', 30),
-- Horario 3 (01:30 PM - 03:00 PM)
(11, 'Grupo XI', 1, '13:30:00', '15:00:00', 30),
(12, 'Grupo XII', 2, '13:30:00', '15:00:00', 30),
(13, 'Grupo XIII', 3, '13:30:00', '15:00:00', 30),
(14, 'Grupo XIV', 4, '13:30:00', '15:00:00', 30),
(15, 'Grupo XV', 5, '13:30:00', '15:00:00', 30);

-- Insertar administrador por defecto (usuario: admin, contraseña: admin123)
-- El hash corresponde a 'admin123'
INSERT IGNORE INTO `admin` (`id`, `usuario`, `contrasena`) VALUES
(1, 'admin', '$2y$10$wRtfP32B66qUj4F2o1sQeOcrk/K/H/7lG62Llyz8zGheK5v/L1pPe');
