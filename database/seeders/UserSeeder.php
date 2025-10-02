<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{

    
    public function run(): void
    {
        // usuario Administrador
        User::updateOrCreate([
            'username' => 'ADMIN'
        ], [
            'name' => 'Administrador',
            'email' => 'admin2@example.com',
            'password' => Hash::make('admin123'),
            'role' => 'administrador',
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => 1,
            'email_verified_at' => now(),
        ]);
        // Nota: Se eliminaron los usuarios de prueba (ADMINISTRADOR, FISCAL, VENTA)
        // para evitar que aparezcan cuentas no deseadas en entornos de producción.
        // Si necesita cuentas adicionales para pruebas locales, agréguelo manualmente.
    }
}
