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
        // Crear usuario administrador
        User::create([
            'name' => 'Administrador Principal',
            'username' => 'admin',
            'email' => 'admin@sistema.com',
            'password' => Hash::make('admin123'),
            'role' => 'administrador',
        ]);

        // Crear usuario fiscalizador
        User::create([
            'name' => 'Juan Fiscalizador',
            'username' => 'fiscalizador1',
            'email' => 'fiscalizador@sistema.com',
            'password' => Hash::make('fiscal123'),
            'role' => 'fiscalizador',
        ]);

        // Crear usuario ventanilla
        User::create([
            'name' => 'María Ventanilla',
            'username' => 'ventanilla1',
            'email' => 'ventanilla@sistema.com',
            'password' => Hash::make('ventanilla123'),
            'role' => 'ventanilla',
        ]);

        // Crear usuario inspector
        User::create([
            'name' => 'Carlos Inspector',
            'username' => 'inspector1',
            'email' => 'inspector@sistema.com',
            'password' => Hash::make('inspector123'),
            'role' => 'inspector',
        ]);
    }
}
