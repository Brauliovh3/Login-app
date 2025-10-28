-- Migración para limpiar campos duplicados de nombres de conductor
-- Mantener solo nombres_conductor y apellidos_conductor

-- Paso 1: Migrar datos de nombre_conductor a nombres_conductor si está vacío
UPDATE `actas` 
SET `nombres_conductor` = `nombre_conductor`
WHERE (`nombres_conductor` IS NULL OR `nombres_conductor` = '') 
AND (`nombre_conductor` IS NOT NULL AND `nombre_conductor` != '');

-- Paso 2: Eliminar el campo duplicado nombre_conductor
ALTER TABLE `actas` 
DROP COLUMN IF EXISTS `nombre_conductor`;