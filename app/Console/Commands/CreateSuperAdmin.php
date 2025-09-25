<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateSuperAdmin extends Command
{
    protected $signature = 'create:superadmin';
    protected $description = 'Crear usuario super admin Brauliovh3';

    public function handle()
    {
        // Verificar si el usuario ya existe
        $user = User::where('name', 'Brauliovh3')->orWhere('username', 'Brauliovh3')->first();
        
        if ($user) {
            $this->info('El usuario Brauliovh3 ya existe.');
            
            // Actualizar los datos para asegurar que estén correctos
            $user->update([
                'name' => 'Brauliovh3',
                'username' => 'Brauliovh3',
                'email' => 'brauliovh3@drtc.gob.pe',
                'password' => Hash::make('1Leucemia1'),
                'role' => 'superadmin',
                'status' => 'approved',
                'email_verified_at' => now()
            ]);
            
            $this->info('Datos del usuario actualizados correctamente.');
        } else {
            // Crear el usuario super admin
            User::create([
                'name' => 'Brauliovh3',
                'username' => 'Brauliovh3', 
                'email' => 'brauliovh3@drtc.gob.pe', 
                'password' => Hash::make('1Leucemia1'),
                'role' => 'superadmin',
                'status' => 'approved',
                'email_verified_at' => now()
            ]);
            
            $this->info('Usuario super admin Brauliovh3 creado exitosamente.');
        }
        
        $this->info('Credenciales:');
        $this->info('Usuario: Brauliovh3');
        $this->info('Contraseña: 1Leucemia1');
        $this->info('Rol: superadmin');
        
        return 0;
    }
}