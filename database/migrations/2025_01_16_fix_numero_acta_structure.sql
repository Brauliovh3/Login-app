-- Migración para arreglar la estructura del número de acta
-- Separar el numero_acta en campos individuales para facilitar consultas

-- Paso 1: Agregar nuevas columnas
ALTER TABLE `actas` 
ADD COLUMN IF NOT EXISTS `acta_prefijo` VARCHAR(10) DEFAULT 'ACT' COMMENT 'Prefijo del acta (ACT)';

ALTER TABLE `actas` 
ADD COLUMN IF NOT EXISTS `acta_anio` YEAR DEFAULT NULL COMMENT 'Año del acta';

ALTER TABLE `actas` 
ADD COLUMN IF NOT EXISTS `acta_numero` INT UNSIGNED DEFAULT NULL COMMENT 'Número secuencial del acta';

-- Paso 2: Agregar índices
ALTER TABLE `actas` 
ADD INDEX IF NOT EXISTS `idx_acta_anio_numero` (`acta_anio`, `acta_numero`);

ALTER TABLE `actas` 
ADD INDEX IF NOT EXISTS `idx_numero_acta` (`numero_acta`);

-- Paso 3: Actualizar registros existentes que tengan formato ACT-YYYY-NNNN
UPDATE `actas` 
SET 
    `acta_prefijo` = 'ACT',
    `acta_anio` = CAST(SUBSTRING(`numero_acta`, 5, 4) AS UNSIGNED),
    `acta_numero` = CAST(SUBSTRING(`numero_acta`, 10) AS UNSIGNED)
WHERE `numero_acta` REGEXP '^ACT-[0-9]{4}-[0-9]+$';

-- Paso 4: Para registros que no tengan el formato correcto, generar número secuencial
SET @counter = 0;
UPDATE `actas` 
SET 
    `acta_prefijo` = 'ACT',
    `acta_anio` = YEAR(COALESCE(`fecha_intervencion`, NOW())),
    `acta_numero` = (@counter := @counter + 1),
    `numero_acta` = CONCAT('ACT-', YEAR(COALESCE(`fecha_intervencion`, NOW())), '-', LPAD(@counter, 4, '0'))
WHERE `acta_anio` IS NULL OR `acta_numero` IS NULL;

-- Paso 5: Eliminar columnas obsoletas si existen
ALTER TABLE `actas` 
DROP COLUMN IF EXISTS `numero_secuencial`;

ALTER TABLE `actas` 
DROP COLUMN IF EXISTS `anio_acta`;