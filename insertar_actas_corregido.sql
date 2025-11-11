-- Eliminar actas con datos vacíos
DELETE FROM actas WHERE numero_acta = '' OR numero_acta IS NULL;

-- Insertar actas de prueba con datos completos
INSERT INTO actas (
    numero_acta, anio_acta, codigo_ds, lugar_intervencion, fecha_intervencion, hora_intervencion,
    inspector_responsable, fiscalizador_id, tipo_servicio, tipo_agente, placa, placa_vehiculo,
    razon_social, ruc_dni, apellidos_conductor, nombres_conductor, licencia, codigo_infraccion,
    descripcion_infraccion, calificacion, descripcion_hechos, estado, user_id, created_at
) VALUES
('0001', 2025, '017-2009-MTC', 'Av. Núñez - Abancay', '2025-01-10', '10:30:00',
 'fisca', 5, 'Urbano', 'Conductor', 'DIN4 CTMR', 'DIN4 CTMR',
 'BVH3 INDUSTRIES', '60015091', 'Velasquez Huillca', 'Juan Quispe', 'P13141321', 'I.1-a',
 'No portar manifiesto de usuarios', 'Leve', 'Vehículo intervenido sin documentación requerida', 0, 5, NOW()),

('0002', 2025, '017-2009-MTC', 'Jr. Lima - Abancay', '2025-01-11', '14:20:00',
 'fisca', 5, 'Interprovincial', 'Conductor', 'ABC-123', 'ABC-123',
 'TRANSPORTES SAC', '20123456789', 'García López', 'Carlos Alberto', 'Q98765432', 'F.8',
 'Circular en emergencia incumpliendo restricciones', 'Muy Grave', 'Vehículo circulando durante estado de emergencia sin autorización', 0, 5, NOW()),

('0003', 2025, '017-2009-MTC', 'Carretera Abancay-Cusco', '2025-01-12', '09:15:00',
 'fisca', 5, 'Turístico', 'Conductor', 'XYZ-789', 'XYZ-789',
 'TURISMO PERU', '20987654321', 'Mendoza Ríos', 'María Elena', 'A11223344', 'I.1-e',
 'No portar certificado de Inspección Técnica Vehicular', 'Grave', 'Vehículo sin certificado ITV vigente', 0, 5, NOW());
