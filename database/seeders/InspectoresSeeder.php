<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InspectoresSeeder extends Seeder
{
    public function run(): void
    {
        $inspectores = [
            [
                'nombres' => 'Luis Fernando',
                'apellidos' => 'García Morales',
                'dni' => '67890123',
                'codigo_inspector' => 'INS-001',
                'telefono' => '932109876',
                'email' => 'l.garcia@drtc.gob.pe',
                'fecha_ingreso' => '2020-03-15',
                'zona_asignada' => 'Abancay Centro',
                'estado' => 'activo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombres' => 'Carmen Rosa',
                'apellidos' => 'Quispe Mamani',
                'dni' => '78901234',
                'codigo_inspector' => 'INS-002',
                'telefono' => '921098765',
                'email' => 'c.quispe@drtc.gob.pe',
                'fecha_ingreso' => '2021-07-10',
                'zona_asignada' => 'Andahuaylas',
                'estado' => 'activo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombres' => 'Miguel Angel',
                'apellidos' => 'Condori Flores',
                'dni' => '89012345',
                'codigo_inspector' => 'INS-003',
                'telefono' => '910987654',
                'email' => 'm.condori@drtc.gob.pe',
                'fecha_ingreso' => '2019-11-20',
                'zona_asignada' => 'Chincheros',
                'estado' => 'activo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombres' => 'Patricia Elena',
                'apellidos' => 'Ramos Vilca',
                'dni' => '90123456',
                'codigo_inspector' => 'INS-004',
                'telefono' => '909876543',
                'email' => 'p.ramos@drtc.gob.pe',
                'fecha_ingreso' => '2022-01-05',
                'zona_asignada' => 'Antabamba',
                'estado' => 'activo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('inspectores')->insert($inspectores);
    }
}
