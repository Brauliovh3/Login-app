<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ActasFormateadasSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar actas existentes
        DB::table('actas')->truncate();
        
        $actas = [
            [
                'numero_acta' => 'DRTC-APU-2025-001',
                'inspector_id' => 1,
                'vehiculo_id' => 1,
                'conductor_id' => 1,
                'infraccion_id' => 1,
                'placa_vehiculo' => 'ABC-123',
                'ubicacion' => 'Terminal Terrestre Abancay',
                'descripcion' => "ACTA DE FISCALIZACIÓN\n\n" .
                               "DATOS DEL VEHÍCULO:\n" .
                               "Placa: ABC-123\n" .
                               "Empresa/Operador: Transportes San Miguel S.A.C.\n" .
                               "RUC/DNI: 20123456789\n\n" .
                               "DATOS DEL CONDUCTOR:\n" .
                               "Nombre: Juan Carlos Pérez López\n" .
                               "Licencia: L123456789\n\n" .
                               "DATOS DEL VIAJE:\n" .
                               "Origen: Abancay\n" .
                               "Destino: Lima\n" .
                               "Tipo de Servicio: Interprovincial\n\n" .
                               "DESCRIPCIÓN DE LOS HECHOS:\n" .
                               "Vehículo circulando sin tarjeta de operación vigente",
                'monto_multa' => 1580.00,
                'estado' => 'registrada',
                'fecha_infraccion' => Carbon::today(),
                'hora_infraccion' => '08:30:00',
                'observaciones' => 'Conductor no pudo presentar documentos requeridos',
                'user_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'numero_acta' => 'DRTC-APU-2025-002',
                'inspector_id' => 1,
                'vehiculo_id' => 2,
                'conductor_id' => 1,
                'infraccion_id' => 1,
                'placa_vehiculo' => 'XYZ-789',
                'ubicacion' => 'Av. Arenas 456, Abancay',
                'descripcion' => "ACTA DE FISCALIZACIÓN\n\n" .
                               "DATOS DEL VEHÍCULO:\n" .
                               "Placa: XYZ-789\n" .
                               "Empresa/Operador: María Elena González López\n" .
                               "RUC/DNI: 87654321\n\n" .
                               "DATOS DEL CONDUCTOR:\n" .
                               "Nombre: María Elena González López\n" .
                               "Licencia: L987654321\n\n" .
                               "DATOS DEL VIAJE:\n" .
                               "Origen: Andahuaylas\n" .
                               "Destino: Abancay\n" .
                               "Tipo de Servicio: Interprovincial\n\n" .
                               "DESCRIPCIÓN DE LOS HECHOS:\n" .
                               "Control de documentos - Licencia próxima a vencer",
                'monto_multa' => 790.00,
                'estado' => 'pendiente',
                'fecha_infraccion' => Carbon::today(),
                'hora_infraccion' => '14:15:00',
                'observaciones' => 'Licencia vence en 15 días',
                'user_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'numero_acta' => 'DRTC-APU-2025-003',
                'inspector_id' => 1,
                'vehiculo_id' => 3,
                'conductor_id' => 1,
                'infraccion_id' => 1,
                'placa_vehiculo' => 'DEF-456',
                'ubicacion' => 'Carretera Abancay-Andahuaylas Km 15',
                'descripcion' => "ACTA DE FISCALIZACIÓN\n\n" .
                               "DATOS DEL VEHÍCULO:\n" .
                               "Placa: DEF-456\n" .
                               "Empresa/Operador: Transportes Los Andes E.I.R.L.\n" .
                               "RUC/DNI: 20555666777\n\n" .
                               "DATOS DEL CONDUCTOR:\n" .
                               "Nombre: Carlos Alberto Rodríguez Silva\n" .
                               "Licencia: L555666777\n\n" .
                               "DATOS DEL VIAJE:\n" .
                               "Origen: Cusco\n" .
                               "Destino: Lima\n" .
                               "Tipo de Servicio: Interprovincial\n\n" .
                               "DESCRIPCIÓN DE LOS HECHOS:\n" .
                               "Inspección técnica vehicular - Vehículo en buenas condiciones",
                'monto_multa' => 0.00,
                'estado' => 'procesada',
                'fecha_infraccion' => Carbon::yesterday(),
                'hora_infraccion' => '10:45:00',
                'observaciones' => 'Inspección exitosa - Sin infracciones',
                'user_id' => 1,
                'created_at' => Carbon::yesterday(),
                'updated_at' => Carbon::now(),
            ],
            [
                'numero_acta' => 'DRTC-APU-2025-004',
                'inspector_id' => 1,
                'vehiculo_id' => 1,
                'conductor_id' => 1,
                'infraccion_id' => 1,
                'placa_vehiculo' => 'GHI-012',
                'ubicacion' => 'Plaza de Armas Abancay',
                'descripcion' => "ACTA DE FISCALIZACIÓN\n\n" .
                               "DATOS DEL VEHÍCULO:\n" .
                               "Placa: GHI-012\n" .
                               "Empresa/Operador: José Miguel Vargas Mendoza\n" .
                               "RUC/DNI: 46027897\n\n" .
                               "DATOS DEL CONDUCTOR:\n" .
                               "Nombre: José Miguel Vargas Mendoza\n" .
                               "Licencia: L246810135\n\n" .
                               "DATOS DEL VIAJE:\n" .
                               "Origen: Abancay\n" .
                               "Destino: Andahuaylas\n" .
                               "Tipo de Servicio: Interprovincial\n\n" .
                               "DESCRIPCIÓN DE LOS HECHOS:\n" .
                               "Estacionamiento en zona prohibida - Infracción leve",
                'monto_multa' => 395.00,
                'estado' => 'registrada',
                'fecha_infraccion' => Carbon::today(),
                'hora_infraccion' => '16:20:00',
                'observaciones' => 'Primera infracción del conductor',
                'user_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('actas')->insert($actas);
    }
}
