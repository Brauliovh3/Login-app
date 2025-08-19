<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            EmpresasSeeder::class,
            ConductoresSeeder::class,
            VehiculosSeeder::class,
            InspectoresSeeder::class,
            InfraccionesSeeder::class,
            DatosBasicosSeeder::class,
            InspeccionesSeeder::class,
            ActasSeeder::class,
            ActasFormateadasSeeder::class,
        ]);
    }
}
