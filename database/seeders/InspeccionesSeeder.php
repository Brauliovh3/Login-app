<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InspeccionesSeeder extends Seeder
{
    public function run(): void
    {
        $inspecciones = [
            [
                'numero_inspeccion' => 'INS-001-2025',
                'vehiculo_id' => 1,
                'inspector_id' => 1,
                'fecha_inspeccion' => Carbon::today(),
                'tipo_inspeccion' => 'rutina',
                'observaciones' => 'Inspección de rutina - Todo conforme',
                'estado_vehiculo' => 'bueno',
                'estado' => 'completada',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'numero_inspeccion' => 'INS-002-2025',
                'vehiculo_id' => 2,
                'inspector_id' => 1,
                'fecha_inspeccion' => Carbon::today(),
                'tipo_inspeccion' => 'especial',
                'observaciones' => 'Control de documentos - Licencia próxima a vencer',
                'estado_vehiculo' => 'bueno',
                'estado' => 'completada',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'numero_inspeccion' => 'INS-003-2025',
                'vehiculo_id' => 3,
                'inspector_id' => 1,
                'fecha_inspeccion' => Carbon::yesterday(),
                'tipo_inspeccion' => 'rutina',
                'observaciones' => 'Inspección técnica vehicular - Vehículo aprobado',
                'estado_vehiculo' => 'optimo',
                'estado' => 'completada',
                'created_at' => Carbon::yesterday(),
                'updated_at' => Carbon::yesterday(),
            ],
            [
                'numero_inspeccion' => 'INS-004-2025',
                'vehiculo_id' => 1,
                'inspector_id' => 1,
                'fecha_inspeccion' => Carbon::today(),
                'tipo_inspeccion' => 'emergencia',
                'observaciones' => 'Inspección por denuncia - En proceso',
                'estado_vehiculo' => 'regular',
                'estado' => 'pendiente',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('inspecciones')->insert($inspecciones);
    }
}
