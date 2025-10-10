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

        // Usuario Fiscalizador
        User::updateOrCreate([
            'username' => 'FISCAL'
        ], [
            'name' => 'Fiscalizador',
            'email' => 'fiscalizador@example.com',
            'password' => Hash::make('fiscal123'),
            'role' => 'fiscalizador',
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => 1,
            'email_verified_at' => now(),
        ]);

        // Usuario Ventanilla
        User::updateOrCreate([
            'username' => 'VENTANILLA'
        ], [
            'name' => 'Operador Ventanilla',
            'email' => 'ventanilla@example.com',
            'password' => Hash::make('ventanilla123'),
            'role' => 'ventanilla',
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => 1,
            'email_verified_at' => now(),
        ]);

        // Nota: Se agregaron usuarios básicos del sistema para funcionalidad completa.
        // Fiscalizador: Para gestión de actas e infracciones
        // Ventanilla: Para atención al público y consultas
    }
}
