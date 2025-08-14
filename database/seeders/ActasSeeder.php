<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ActasSeeder extends Seeder
{
    public function run(): void
    {
        $actas = [
            // Actas registradas hoy (estado: registrada)
            [
                'numero_acta' => 'ACTA-001-2025',
                'inspector_id' => 1,
                'vehiculo_id' => 1,
                'conductor_id' => 1,
                'infraccion_id' => 1,
                'placa_vehiculo' => 'ABC-123',
                'ubicacion' => 'Av. Arenas 245, Abancay',
                'descripcion' => 'Vehículo circulando sin tarjeta de operación vigente',
                'monto_multa' => 1580.00,
                'estado' => 'registrada',
                'fecha_infraccion' => Carbon::today(),
                'hora_infraccion' => '08:30:00',
                'observaciones' => 'Conductor no pudo presentar documentos requeridos',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'numero_acta' => 'ACTA-002-2025',
                'inspector_id' => 1,
                'vehiculo_id' => 2,
                'conductor_id' => 1,
                'infraccion_id' => 1,
                'placa_vehiculo' => 'XYZ-789',
                'ubicacion' => 'Terminal Terrestre Abancay',
                'descripcion' => 'Exceso de pasajeros en vehículo de transporte público',
                'monto_multa' => 2370.00,
                'estado' => 'registrada',
                'fecha_infraccion' => Carbon::today(),
                'hora_infraccion' => '14:15:00',
                'observaciones' => 'Se detectaron 5 pasajeros adicionales al permitido',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'numero_acta' => 'ACTA-003-2025',
                'inspector_id' => 1,
                'vehiculo_id' => 3,
                'conductor_id' => 1,
                'infraccion_id' => 1,
                'placa_vehiculo' => 'DEF-456',
                'ubicacion' => 'Carretera Abancay-Andahuaylas Km 15',
                'descripcion' => 'Conductor sin licencia de conducir apropiada para la categoría',
                'monto_multa' => 3950.00,
                'estado' => 'registrada',
                'fecha_infraccion' => Carbon::today(),
                'hora_infraccion' => '10:45:00',
                'observaciones' => 'Licencia categoria A1, vehículo requiere A3',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            // Actas procesadas (estado: procesada)
            [
                'numero_acta' => 'ACTA-004-2025',
                'inspector_id' => 1,
                'vehiculo_id' => 1,
                'conductor_id' => 1,
                'infraccion_id' => 1,
                'placa_vehiculo' => 'ABC-123',
                'ubicacion' => 'Plaza de Armas Abancay',
                'descripcion' => 'Estacionamiento en zona prohibida',
                'monto_multa' => 790.00,
                'estado' => 'procesada',
                'fecha_infraccion' => Carbon::yesterday(),
                'hora_infraccion' => '16:20:00',
                'observaciones' => 'Multa pagada, expediente cerrado',
                'created_at' => Carbon::yesterday(),
                'updated_at' => Carbon::now(),
            ],
            [
                'numero_acta' => 'ACTA-005-2025',
                'inspector_id' => 1,
                'vehiculo_id' => 2,
                'conductor_id' => 1,
                'infraccion_id' => 1,
                'placa_vehiculo' => 'XYZ-789',
                'ubicacion' => 'Mercado Central Abancay',
                'descripcion' => 'Vehículo con documentos vencidos',
                'monto_multa' => 1185.00,
                'estado' => 'procesada',
                'fecha_infraccion' => Carbon::yesterday(),
                'hora_infraccion' => '12:10:00',
                'observaciones' => 'Documentos renovados, caso cerrado',
                'created_at' => Carbon::yesterday(),
                'updated_at' => Carbon::now(),
            ],

            // Actas pendientes (estado: pendiente)
            [
                'numero_acta' => 'ACTA-006-2025',
                'inspector_id' => 1,
                'vehiculo_id' => 3,
                'conductor_id' => 1,
                'infraccion_id' => 1,
                'placa_vehiculo' => 'DEF-456',
                'ubicacion' => 'Av. Núñez 890, Abancay',
                'descripcion' => 'Vehículo en mal estado de conservación',
                'monto_multa' => 2765.00,
                'estado' => 'pendiente',
                'fecha_infraccion' => Carbon::today()->subDays(2),
                'hora_infraccion' => '09:30:00',
                'observaciones' => 'Pendiente de revisión técnica',
                'created_at' => Carbon::today()->subDays(2),
                'updated_at' => Carbon::today()->subDays(2),
            ],
        ];

        DB::table('actas')->insert($actas);
    }
}
