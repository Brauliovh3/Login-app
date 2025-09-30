<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InfraccionesNuevaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $infracciones = [
            [
                'codigo_infraccion' => 'G.01',
                'base_legal' => 'Art. 318° Reglamento Nacional de Tránsito',
                'descripcion' => 'Exceso de velocidad en zona urbana',
                'detalle_completo' => 'Conducir a velocidad superior a la permitida en zona urbana (más de 60 km/h)',
                'estado' => 'activo',
                'gravedad' => 'grave',
                'multa_soles' => '420',
                'multa_uit' => '0.84',
                'puntos_licencia' => 8,
                'retencion_licencia' => false,
                'retencion_vehiculo' => false,
                'internamiento_deposito' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo_infraccion' => 'G.02',
                'base_legal' => 'Art. 281° Reglamento Nacional de Tránsito',
                'descripcion' => 'No respetar señal de alto',
                'detalle_completo' => 'No detenerse completamente ante una señal de alto o semáforo en rojo',
                'estado' => 'activo',
                'gravedad' => 'grave',
                'multa_soles' => '420',
                'multa_uit' => '0.84',
                'puntos_licencia' => 8,
                'retencion_licencia' => false,
                'retencion_vehiculo' => false,
                'internamiento_deposito' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo_infraccion' => 'MG.01',
                'base_legal' => 'Art. 288° Reglamento Nacional de Tránsito',
                'descripcion' => 'Conducir sin licencia vigente',
                'detalle_completo' => 'Conducir vehículo sin portar licencia vigente o con licencia vencida',
                'estado' => 'activo',
                'gravedad' => 'muy_grave',
                'multa_soles' => '840',
                'multa_uit' => '1.68',
                'puntos_licencia' => 20,
                'retencion_licencia' => true,
                'retencion_vehiculo' => true,
                'internamiento_deposito' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo_infraccion' => 'L.01',
                'base_legal' => 'Art. 295° Reglamento Nacional de Tránsito',
                'descripcion' => 'Estacionar en lugar prohibido',
                'detalle_completo' => 'Estacionar vehículo en zona prohibida, frente a garajes o en espacios reservados',
                'estado' => 'activo',
                'gravedad' => 'leve',
                'multa_soles' => '168',
                'multa_uit' => '0.336',
                'puntos_licencia' => 4,
                'retencion_licencia' => false,
                'retencion_vehiculo' => false,
                'internamiento_deposito' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo_infraccion' => 'L.02',
                'base_legal' => 'Art. 267° Reglamento Nacional de Tránsito',
                'descripcion' => 'No usar cinturón de seguridad',
                'detalle_completo' => 'Conductor o pasajeros no utilizan cinturón de seguridad durante la conducción',
                'estado' => 'activo',
                'gravedad' => 'leve',
                'multa_soles' => '168',
                'multa_uit' => '0.336',
                'puntos_licencia' => 4,
                'retencion_licencia' => false,
                'retencion_vehiculo' => false,
                'internamiento_deposito' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo_infraccion' => 'MG.02',
                'base_legal' => 'Art. 274° Reglamento Nacional de Tránsito',
                'descripcion' => 'Conducir bajo efectos del alcohol',
                'detalle_completo' => 'Conducir vehículo con grado de alcohol superior al permitido por ley',
                'estado' => 'activo',
                'gravedad' => 'muy_grave',
                'multa_soles' => '4200',
                'multa_uit' => '8.4',
                'puntos_licencia' => 50,
                'retencion_licencia' => true,
                'retencion_vehiculo' => true,
                'internamiento_deposito' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo_infraccion' => 'G.03',
                'base_legal' => 'Art. 230° Reglamento Nacional de Tránsito',
                'descripcion' => 'Exceso de carga en transporte público',
                'detalle_completo' => 'Transportar mayor cantidad de pasajeros o carga que la autorizada',
                'estado' => 'activo',
                'gravedad' => 'grave',
                'multa_soles' => '840',
                'multa_uit' => '1.68',
                'puntos_licencia' => 15,
                'retencion_licencia' => false,
                'retencion_vehiculo' => true,
                'internamiento_deposito' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo_infraccion' => 'G.04',
                'base_legal' => 'Art. 291° Reglamento Nacional de Tránsito',
                'descripcion' => 'No portar SOAT vigente',
                'detalle_completo' => 'Circular sin portar el Seguro Obligatorio de Accidentes de Tránsito vigente',
                'estado' => 'activo',
                'gravedad' => 'grave',
                'multa_soles' => '420',
                'multa_uit' => '0.84',
                'puntos_licencia' => 8,
                'retencion_licencia' => false,
                'retencion_vehiculo' => true,
                'internamiento_deposito' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($infracciones as $infraccion) {
            DB::table('infracciones')->updateOrInsert(
                ['codigo_infraccion' => $infraccion['codigo_infraccion']], // Buscar por código único
                $infraccion // Datos a insertar/actualizar
            );
        }
    }
}
