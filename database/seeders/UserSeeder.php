<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Asegurar que el usuario principal sea DEPOOL (idempotente)
        User::updateOrCreate([
            'username' => 'DEPOOL'
        ], [
            'name' => 'DEPOOL',
            'email' => 'velasquezhuillcab@gmail.com',
            // Cambia la contraseña por la que prefieras
            'password' => Hash::make('depool123'),
            'role' => 'administrador',
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => 1,
            'email_verified_at' => now(),
        ]);
    }
}
