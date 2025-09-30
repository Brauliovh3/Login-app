<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DetalleInfraccionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $detalles = [
            // Detalles para F.1
            [
                'infraccion_id' => 1, // ID de F.1
                'descripcion' => 'Infracciones contra la formalización del transporte',
                'subcategoria' => null,
                'descripcion_detallada' => 'Prestar el servicio de transporte de personas, de mercancías o mixto, sin contar con autorización otorgada por la autoridad competente o en una modalidad o ámbito diferente al autorizado',
                'condiciones_especiales' => 'Sobre quien realiza la actividad de transporte sin autorización',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Detalles para F.2
            [
                'infraccion_id' => 2, // ID de F.2
                'descripcion' => 'Infracciones contra la formalización del transporte',
                'subcategoria' => null,
                'descripcion_detallada' => 'Permitir la utilización o utilizar, intencionalmente, los vehículos destinados a la prestación del servicio, en acciones de bloqueo, interrupción u otras que impidan el libre tránsito por las calles, carreteras, puentes, vías férreas y otras vías públicas terrestres.',
                'condiciones_especiales' => 'Aplica a transportistas que permitan uso indebido de vehículos',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Detalles para F.3
            [
                'infraccion_id' => 3, // ID de F.3
                'descripcion' => 'Infracciones contra la formalización del transporte',
                'subcategoria' => null,
                'descripcion_detallada' => 'Participar como conductor de vehículos que sean utilizados en acciones de bloqueo, interrupción u otras que impidan el libre tránsito por las calles, carreteras, puentes, vías férreas y otras vías públicas terrestres',
                'condiciones_especiales' => 'Conductor que participa activamente en bloqueos de vías públicas',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Detalles para S.1 - Múltiples subcategorías
            [
                'infraccion_id' => 4, // ID de S.1
                'descripcion' => 'Infracciones contra la Seguridad en el Servicio de Transporte',
                'subcategoria' => 'a)',
                'descripcion_detallada' => 'no tenga licencia de conducir válida para operar vehículos de transporte público',
                'condiciones_especiales' => 'Utilizar conductores que no cuenten con la documentación requerida',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'infraccion_id' => 4, // ID de S.1
                'descripcion' => 'Infracciones contra la Seguridad en el Servicio de Transporte',
                'subcategoria' => 'b)',
                'descripcion_detallada' => 'cuya licencia de conducir se encuentre vencida o no esté vigente',
                'condiciones_especiales' => 'Utilizar conductores que no cumplan con requisitos de vigencia',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'infraccion_id' => 4, // ID de S.1
                'descripcion' => 'Infracciones contra la Seguridad en el Servicio de Transporte',
                'subcategoria' => 'c)',
                'descripcion_detallada' => 'Cuya licencia de conducir no corresponde a la clase y categoría requerida por las características del vehículo y del servicio a prestar',
                'condiciones_especiales' => 'Utilizar conductores que no tengan la categoría de licencia apropiada para el tipo de vehículo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
            // Detalles para I.1 - Múltiples subcategorías
            [
                'infraccion_id' => 5, // ID de I.1
                'descripcion' => 'Infracciones a la Información o documentación',
                'subcategoria' => 'a)',
                'descripcion_detallada' => 'el manifiesto de usuarios, en el transporte de personas, cuando este no sea electrónico y sea requerido por la autoridad',
                'condiciones_especiales' => 'No portar durante la prestación del servicio de transporte los documentos obligatorios',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'infraccion_id' => 5, // ID de I.1
                'descripcion' => 'Infracciones a la Información o documentación',
                'subcategoria' => 'b)',
                'descripcion_detallada' => 'la hoja de ruta manual o electrónica, según corresponda al tipo de servicio prestado',
                'condiciones_especiales' => 'No portar durante la prestación del servicio de transporte los documentos de control de ruta',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'infraccion_id' => 5, // ID de I.1
                'descripcion' => 'Infracciones a la Información o documentación',
                'subcategoria' => 'c)',
                'descripcion_detallada' => 'en el servicio de transporte de mercancías la guía de remisión del transportista y, de ser el caso, el manifiesto de carga correspondiente',
                'condiciones_especiales' => 'No portar durante la prestación del servicio de transporte de mercancías la documentación de carga requerida',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($detalles as $detalle) {
            DB::table('detalle_infraccion')->updateOrInsert(
                [
                    'infraccion_id' => $detalle['infraccion_id'],
                    'subcategoria' => $detalle['subcategoria']
                ],
                $detalle
            );
        }
    }
}
