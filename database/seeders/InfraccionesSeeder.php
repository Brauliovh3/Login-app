<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InfraccionesSeeder extends Seeder
{
    public function run(): void
    {
        $infracciones = [
            [
                'codigo' => 'F.1',
                'aplica_sobre' => 'Transportista',
                'reglamento' => 'Reglamento Nacional de Administración de Transportes - RENAT',
                'norma_modificatoria' => 'D.S. N° 017-2009-MTC / D.S. N° 063-2010-MTC / D.S. N° 005-2016-MTC',
                'clase_pago' => 'Pecuniaria',
                'sancion' => '1 UIT',
                'tipo' => 'Infracción',
                'medida_preventiva' => 'Retención de la licencia de conducir / Internamiento preventivo del vehículo',
                'gravedad' => 'muy_grave',
                'otros_responsables__otros_beneficios' => 'Responsabilidad solidaria del propietario del vehículo / Aplica descuento de 50% (Hasta 05 días hábiles) y 30% (15 días)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo' => 'F.2',
                'aplica_sobre' => 'Transportista',
                'reglamento' => 'Reglamento Nacional de Administración de Transportes - RENAT',
                'norma_modificatoria' => 'D.S. N° 017-2009-MTC',
                'clase_pago' => 'No pecuniaria',
                'sancion' => 'Inhabilitación por 1 año para prestar el servicio de transporte',
                'tipo' => 'Infracción',
                'medida_preventiva' => 'En forma sucesiva: Remoción del vehículo. Internamiento del vehículo',
                'gravedad' => 'muy_grave',
                'otros_responsables__otros_beneficios' => 'Aplica descuento de 50% (Hasta 05 días hábiles) y 30% (15 días)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo' => 'F.3',
                'aplica_sobre' => 'Conductor',
                'reglamento' => 'Reglamento Nacional de Administración de Transportes - RENAT',
                'norma_modificatoria' => 'D.S. N° 017-2009-MTC',
                'clase_pago' => 'No pecuniaria',
                'sancion' => 'Suspensión 90 días de habilitación para conducir vehículos del servicio de transporte',
                'tipo' => 'Infracción',
                'medida_preventiva' => 'Al conductor: Retención de licencia de conducir',
                'gravedad' => 'muy_grave',
                'otros_responsables__otros_beneficios' => 'Aplica descuento de 50% (Hasta 05 días hábiles) y 30% (15 días)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo' => 'S.1',
                'aplica_sobre' => 'Transportista',
                'reglamento' => 'Reglamento Nacional de Administración de Transportes - RENAT',
                'norma_modificatoria' => 'D.S. N° 009-2004-MTC / D.S. N° 017-2009-MTC / D.S. N° 063-2010-MTC',
                'clase_pago' => 'Pecuniaria',
                'sancion' => '0.5 UIT',
                'tipo' => 'Infracción',
                'medida_preventiva' => 'En forma sucesiva: interrupción de viaje / Retención del vehículo / internamiento del vehículo',
                'gravedad' => 'muy_grave',
                'otros_responsables__otros_beneficios' => 'Aplica descuento de 50% (Hasta 05 días hábiles) y 30% (15 días)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo' => 'I.1',
                'aplica_sobre' => 'Transportista',
                'reglamento' => 'Reglamento Nacional de Administración de Transportes - RENAT',
                'norma_modificatoria' => 'D.S. N° 009-2004-MTC / D.S. N° 017-2009-MTC / D.S. N° 063-2010-MTC',
                'clase_pago' => 'Pecuniaria',
                'sancion' => '0.1 UIT',
                'tipo' => 'Infracción',
                'medida_preventiva' => 'En forma sucesiva: interrupción de viaje / Retención del vehículo / internamiento del vehículo',
                'gravedad' => 'grave',
                'otros_responsables__otros_beneficios' => 'Aplica descuento de 50% (Hasta 05 días hábiles) y 30% (15 días)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($infracciones as $infraccion) {
            DB::table('infracciones')->updateOrInsert(
                ['codigo' => $infraccion['codigo']], // Buscar por código único
                $infraccion // Datos a insertar/actualizar
            );
        }
    }
}
