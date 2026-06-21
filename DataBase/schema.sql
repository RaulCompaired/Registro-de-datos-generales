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
    `inscritos` INT DEFAULT 0,
    FOREIGN KEY (`laboratorio_id`) REFERENCES `laboratorios` (`id`) ON DELETE CASCADE
);

-- Crear tabla de alumnos
CREATE TABLE IF NOT EXISTS `alumnos` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `boleta` VARCHAR(10) NOT NULL UNIQUE,
    `nombre` VARCHAR(150) NOT NULL,
    `fecha_nacimiento` DATE NOT NULL,
    `genero` VARCHAR(20) NOT NULL,
    `curp` VARCHAR(18) NOT NULL,
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

-- Crear vista de detalles de alumnos
CREATE OR REPLACE VIEW `vista_alumnos_detalle` AS
SELECT 
    a.boleta, 
    a.nombre, 
    a.fecha_nacimiento, 
    a.genero, 
    a.curp, 
    a.entidad_federativa, 
    a.escuela_procedencia, 
    a.promedio, 
    g.nombre AS grupo_nombre
FROM alumnos a
LEFT JOIN grupos g ON a.grupo_id = g.id;

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





-- TRIGGERS DE VALIDACION DE DATOS

DELIMITER //
DROP PROCEDURE IF EXISTS `sp_validar_alumno`//

CREATE PROCEDURE `sp_validar_alumno`(
    IN p_boleta VARCHAR(10),
    IN p_nombre VARCHAR(150),
    IN p_fecha_nacimiento DATE,
    IN p_curp VARCHAR(18),
    IN p_promedio DECIMAL(4, 2),
    IN p_correo VARCHAR(100),
    IN p_telefono VARCHAR(15)
)
BEGIN
    -- Validar boleta
    IF p_boleta NOT REGEXP '^[0-9]{10}$|^(PE|PP)[0-9]{8}$' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El numero de boleta no es valido (debe tener 10 digitos o comenzar con PE/PP seguido de 8 digitos).';
    END IF;

    -- Validar curp
    IF p_curp NOT REGEXP BINARY '^[A-Z]{4}[0-9]{6}(H|M)[A-Z]{5}([0-9]{2}|[A-Z][0-9])$' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El CURP no es valido.';
    END IF;

    -- Validar nombre
    IF p_nombre NOT REGEXP BINARY '^[A-Z][a-z]+ [A-Z][a-z]+( |[A-Z a-z])*$' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El nombre no es valido (debe iniciar con mayusculas y contener al menos nombre y apellido).';
    END IF;

    -- Validar telefono
    IF p_telefono IS NOT NULL AND p_telefono != '' AND p_telefono NOT REGEXP '^[0-9]{10}$' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El telefono debe tener exactamente 10 digitos.';
    END IF;

    -- Validar promedio
    IF p_promedio < 6.00 OR p_promedio > 10.00 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El promedio debe ser un valor entre 6.00 y 10.00.';
    END IF;

    -- Validar correo
    IF p_correo NOT REGEXP BINARY '^[A-Za-z0-9_\\.]+@alumno\\.ipn\\.mx$' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'El correo electronico debe pertenecer al dominio @alumno.ipn.mx.';
    END IF;

    -- Validar fecha de nacimiento (edad entre 16 y 100 anos)
    IF p_fecha_nacimiento > DATE_SUB(CURDATE(), INTERVAL 16 YEAR) OR p_fecha_nacimiento < DATE_SUB(CURDATE(), INTERVAL 100 YEAR) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La edad del alumno debe estar entre 16 y 100 anos.';
    END IF;
END;
//

DROP TRIGGER IF EXISTS `trg_validar_alumno_insert`//

CREATE TRIGGER `trg_validar_alumno_insert`
BEFORE INSERT ON `alumnos`
FOR EACH ROW
BEGIN
    CALL sp_validar_alumno(NEW.boleta, NEW.nombre, NEW.fecha_nacimiento, NEW.curp, NEW.promedio, NEW.correo, NEW.telefono);
END;
//

DROP TRIGGER IF EXISTS `trg_validar_alumno_update`//

CREATE TRIGGER `trg_validar_alumno_update`
BEFORE UPDATE ON `alumnos`
FOR EACH ROW
BEGIN
    CALL sp_validar_alumno(NEW.boleta, NEW.nombre, NEW.fecha_nacimiento, NEW.curp, NEW.promedio, NEW.correo, NEW.telefono);
END;
//

DROP TRIGGER IF EXISTS `trg_alumnos_after_insert`//

CREATE TRIGGER `trg_alumnos_after_insert`
AFTER INSERT ON `alumnos`
FOR EACH ROW
BEGIN
    IF NEW.grupo_id IS NOT NULL THEN
        UPDATE grupos SET inscritos = inscritos + 1 WHERE id = NEW.grupo_id;
    END IF;
END;
//

DROP TRIGGER IF EXISTS `trg_alumnos_after_update`//

CREATE TRIGGER `trg_alumnos_after_update`
AFTER UPDATE ON `alumnos`
FOR EACH ROW
BEGIN
    -- Decrementar del grupo anterior si cambia o se quita
    IF OLD.grupo_id IS NOT NULL AND (NEW.grupo_id IS NULL OR NEW.grupo_id != OLD.grupo_id) THEN
        UPDATE grupos SET inscritos = GREATEST(0, inscritos - 1) WHERE id = OLD.grupo_id;
    END IF;
    -- Incrementar en el nuevo grupo si cambia o se asigna
    IF NEW.grupo_id IS NOT NULL AND (OLD.grupo_id IS NULL OR NEW.grupo_id != OLD.grupo_id) THEN
        UPDATE grupos SET inscritos = inscritos + 1 WHERE id = NEW.grupo_id;
    END IF;
END;
//

DROP TRIGGER IF EXISTS `trg_alumnos_after_delete`//

CREATE TRIGGER `trg_alumnos_after_delete`
AFTER DELETE ON `alumnos`
FOR EACH ROW
BEGIN
    IF OLD.grupo_id IS NOT NULL THEN
        UPDATE grupos SET inscritos = GREATEST(0, inscritos - 1) WHERE id = OLD.grupo_id;
    END IF;
END;
//

DELIMITER ;
