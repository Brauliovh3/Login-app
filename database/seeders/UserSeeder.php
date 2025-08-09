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
            'name' => 'Administrador',
            'username' => 'admin',
            'email' => 'admin@sistema.com',
            'password' => Hash::make('admin123'),
            'role' => 'administrador',
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => 1,
            'email_verified_at' => now(),
        ]);

        // Crear usuario fiscalizador
        User::create([
            'name' => 'Fiscalizador',
            'username' => 'fiscalizador',
            'email' => 'fiscalizador@sistema.com',
            'password' => Hash::make('fiscal123'),
            'role' => 'fiscalizador',
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => 1,
            'email_verified_at' => now(),
        ]);

        // Crear usuario ventanilla
        User::create([
            'name' => 'Ventanilla',
            'username' => 'ventanilla',
            'email' => 'ventanilla@sistema.com',
            'password' => Hash::make('ventanilla123'),
            'role' => 'ventanilla',
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => 1,
            'email_verified_at' => now(),
        ]);

        // Crear usuario inspector
        User::create([
            'name' => 'Inspector',
            'username' => 'inspector',
            'email' => 'inspector@sistema.com',
            'password' => Hash::make('inspector123'),
            'role' => 'inspector',
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => 1,
            'email_verified_at' => now(),
        ]);
    }
}
