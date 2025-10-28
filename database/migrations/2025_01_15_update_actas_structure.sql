-- Migración para actualizar estructura de actas
-- Fecha: 2025-01-15
-- Descripción: Agregar campos de provincia, distrito, separar nombres y mejorar estructura

-- Agregar nuevas columnas si no existen
ALTER TABLE actas 
ADD COLUMN IF NOT EXISTS provincia VARCHAR(100) DEFAULT NULL AFTER lugar_intervencion,
ADD COLUMN IF NOT EXISTS distrito VARCHAR(100) DEFAULT NULL AFTER provincia,
ADD COLUMN IF NOT EXISTS apellidos_conductor VARCHAR(150) DEFAULT NULL AFTER nombre_conductor,
ADD COLUMN IF NOT EXISTS nombres_conductor VARCHAR(150) DEFAULT NULL AFTER apellidos_conductor,
ADD COLUMN IF NOT EXISTS numero_secuencial INT DEFAULT NULL AFTER numero_acta,
ADD COLUMN IF NOT EXISTS anio_acta YEAR DEFAULT NULL AFTER numero_secuencial;

-- Actualizar registros existentes para separar nombres
UPDATE actas 
SET 
    apellidos_conductor = CASE 
        WHEN nombre_conductor LIKE '%,%' THEN TRIM(SUBSTRING_INDEX(nombre_conductor, ',', 1))
        ELSE TRIM(SUBSTRING_INDEX(nombre_conductor, ' ', 1))
    END,
    nombres_conductor = CASE 
        WHEN nombre_conductor LIKE '%,%' THEN TRIM(SUBSTRING_INDEX(nombre_conductor, ',', -1))
        ELSE TRIM(SUBSTRING(nombre_conductor, LOCATE(' ', nombre_conductor) + 1))
    END
WHERE nombre_conductor IS NOT NULL AND nombre_conductor != '';

-- Extraer año del número de acta existente y número secuencial
UPDATE actas 
SET 
    anio_acta = CASE 
        WHEN numero_acta REGEXP '[0-9]{4}' THEN 
            CAST(SUBSTRING(numero_acta, LOCATE('-', numero_acta) + 1, 4) AS UNSIGNED)
        ELSE YEAR(COALESCE(fecha_intervencion, created_at))
    END,
    numero_secuencial = CASE 
        WHEN numero_acta REGEXP '^[0-9]+' THEN 
            CAST(SUBSTRING_INDEX(numero_acta, '-', 1) AS UNSIGNED)
        ELSE id
    END
WHERE numero_acta IS NOT NULL;

-- Establecer valores por defecto para registros sin provincia/distrito
UPDATE actas 
SET 
    provincia = 'Abancay',
    distrito = 'Abancay'
WHERE provincia IS NULL OR distrito IS NULL;

-- Crear índices para mejorar consultas
CREATE INDEX IF NOT EXISTS idx_actas_provincia ON actas(provincia);
CREATE INDEX IF NOT EXISTS idx_actas_distrito ON actas(distrito);
CREATE INDEX IF NOT EXISTS idx_actas_anio ON actas(anio_acta);
CREATE INDEX IF NOT EXISTS idx_actas_numero_secuencial ON actas(numero_secuencial);
CREATE INDEX IF NOT EXISTS idx_actas_apellidos ON actas(apellidos_conductor);
CREATE INDEX IF NOT EXISTS idx_actas_nombres ON actas(nombres_conductor);

-- Insertar datos de ejemplo si la tabla está vacía
INSERT INTO actas (
    numero_acta, numero_secuencial, anio_acta, placa, placa_vehiculo, 
    nombre_conductor, apellidos_conductor, nombres_conductor, 
    ruc_dni, razon_social, tipo_agente, tipo_servicio, 
    lugar_intervencion, provincia, distrito, 
    fecha_intervencion, hora_intervencion, inspector_responsable,
    codigo_infraccion, descripcion_infraccion, estado, created_at
) 
SELECT * FROM (
    SELECT 
        'ACT-2025-0001' as numero_acta, 1 as numero_secuencial, 2025 as anio_acta,
        'ABC-123' as placa, 'ABC-123' as placa_vehiculo,
        'García López, Juan Carlos' as nombre_conductor, 
        'García López' as apellidos_conductor, 'Juan Carlos' as nombres_conductor,
        '12345678' as ruc_dni, 'Transportes García SAC' as razon_social,
        'Conductor' as tipo_agente, 'Interprovincial' as tipo_servicio,
        'Av. Núñez - Abancay, Apurímac' as lugar_intervencion, 
        'Abancay' as provincia, 'Abancay' as distrito,
        CURDATE() as fecha_intervencion, '10:30:00' as hora_intervencion, 
        'Inspector Demo' as inspector_responsable,
        'F.4-a' as codigo_infraccion, 
        'Negarse a entregar información o documentación al ser requerido' as descripcion_infraccion,
        0 as estado, NOW() as created_at
    UNION ALL
    SELECT 
        'ACT-2025-0002', 2, 2025, 'XYZ-456', 'XYZ-456',
        'Mendoza Silva, María Elena', 'Mendoza Silva', 'María Elena',
        '87654321', 'Empresa Mendoza EIRL', 'Conductor', 'Urbano',
        'Jr. Lima - Tamburco, Apurímac', 'Abancay', 'Tamburco',
        CURDATE(), '14:15:00', 'Inspector Demo',
        'I.1-e', 'No portar certificado de Inspección Técnica Vehicular',
        0, NOW()
    UNION ALL
    SELECT 
        'ACT-2025-0003', 3, 2025, 'DEF-789', 'DEF-789',
        'Quispe Huamán, Pedro Antonio', 'Quispe Huamán', 'Pedro Antonio',
        '11223344', 'Transportes Quispe SAC', 'Conductor', 'Interdistrital',
        'Carretera Abancay-Andahuaylas Km 15', 'Andahuaylas', 'Andahuaylas',
        CURDATE(), '16:45:00', 'Inspector Demo',
        'F.6-c', 'Realizar maniobras evasivas con vehículo',
        0, NOW()
) AS sample_data
WHERE NOT EXISTS (SELECT 1 FROM actas LIMIT 1);

-- Verificar estructura final
SELECT 
    'Migración completada' as status,
    COUNT(*) as total_actas,
    COUNT(CASE WHEN provincia IS NOT NULL THEN 1 END) as con_provincia,
    COUNT(CASE WHEN distrito IS NOT NULL THEN 1 END) as con_distrito,
    COUNT(CASE WHEN apellidos_conductor IS NOT NULL THEN 1 END) as con_apellidos,
    COUNT(CASE WHEN nombres_conductor IS NOT NULL THEN 1 END) as con_nombres
FROM actas;