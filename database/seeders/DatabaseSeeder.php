<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Notification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crear usuarios de prueba con diferentes roles
        $admin = User::create([
            'name' => 'admin',
            'username' => 'admin',
            'email' => 'admin@sistema.com',
            'password' => Hash::make('admin123'),
            'role' => 'administrador',
        ]);

        $fiscalizador = User::create([
            'name' => 'fiscalizador',
            'username' => 'fiscalizador',
            'email' => 'fiscalizador@sistema.com',
            'password' => Hash::make('fiscalizador123'),
            'role' => 'fiscalizador',
        ]);

        $ventanilla = User::create([
            'name' => 'ventanilla',
            'username' => 'ventanilla',
            'email' => 'ventanilla@sistema.com',
            'password' => Hash::make('ventanilla123'),
            'role' => 'ventanilla',
        ]);

        // Ejecutar otros seeders
        $this->call([
            EmpresasSeeder::class,
            ConductoresSeeder::class,
            VehiculosSeeder::class,
            InfraccionesSeeder::class,
            InspectoresSeeder::class,
        ]);

        // Crear notificaciones de bienvenida para cada usuario
        Notification::create([
            'title' => 'Bienvenido al Sistema',
            'message' => 'Tu cuenta de administrador ha sido configurada correctamente. Tienes acceso completo al sistema.',
            'type' => 'success',
            'user_id' => $admin->id,
        ]);

        Notification::create([
            'title' => 'Cuenta de Fiscalizador Activada',
            'message' => 'Tu cuenta de fiscalizador está lista. Puedes comenzar a revisar y supervisar las operaciones del sistema.',
            'type' => 'info',
            'user_id' => $fiscalizador->id,
        ]);

        Notification::create([
            'title' => 'Cuenta de Ventanilla Configurada',
            'message' => 'Tu cuenta de ventanilla ha sido configurada. Puedes comenzar a procesar transacciones y atender usuarios.',
            'type' => 'info',
            'user_id' => $ventanilla->id,
        ]);
    }
}
