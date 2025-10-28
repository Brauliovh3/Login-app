-- Crear tabla infracciones
CREATE TABLE IF NOT EXISTS `infracciones` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `codigo_infraccion` varchar(255) NOT NULL,
  `aplica_sobre` varchar(255) NOT NULL,
  `reglamento` varchar(255) NOT NULL,
  `norma_modificatoria` varchar(255) NOT NULL,
  `infraccion` text NOT NULL,
  `clase_pago` enum('Pecuniaria','No pecuniaria') NOT NULL,
  `sancion` varchar(255) NOT NULL,
  `tipo` enum('Infracción') NOT NULL DEFAULT 'Infracción',
  `medida_preventiva` text,
  `gravedad` enum('Leve','Grave','Muy grave') NOT NULL,
  `otros_responsables_otros_beneficios` text,
  `estado` varchar(255) NOT NULL DEFAULT 'activo',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `infracciones_codigo_infraccion_unique` (`codigo_infraccion`),
  KEY `infracciones_codigo_infraccion_index` (`codigo_infraccion`),
  KEY `infracciones_aplica_sobre_index` (`aplica_sobre`),
  KEY `infracciones_gravedad_index` (`gravedad`),
  KEY `infracciones_estado_index` (`estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar algunas infracciones básicas
INSERT INTO `infracciones` (`codigo_infraccion`, `aplica_sobre`, `reglamento`, `norma_modificatoria`, `infraccion`, `clase_pago`, `sancion`, `medida_preventiva`, `gravedad`) VALUES
('F.1', 'Transportista', 'RENAT', 'D.S. N° 017-2009-MTC', 'Prestar servicio de transporte público sin contar con autorización', 'Pecuniaria', '1 UIT', 'Internamiento del vehículo', 'Muy grave'),
('F.2', 'Transportista', 'RENAT', 'D.S. N° 017-2009-MTC', 'Prestar servicio fuera del ámbito autorizado', 'Pecuniaria', '0.5 UIT', NULL, 'Grave'),
('F.3', 'Conductor', 'RENAT', 'D.S. N° 017-2009-MTC', 'Conducir sin licencia de conducir', 'Pecuniaria', '1 UIT', 'Retención del vehículo', 'Muy grave'),
('F.4', 'Conductor', 'RENAT', 'D.S. N° 017-2009-MTC', 'Exceder los límites de velocidad', 'Pecuniaria', '0.25 UIT', NULL, 'Leve'),
('F.5', 'Transportista', 'RENAT', 'D.S. N° 017-2009-MTC', 'No contar con SOAT vigente', 'Pecuniaria', '0.5 UIT', 'Retención del vehículo', 'Grave');