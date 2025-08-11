<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Renombrar tablas principales a español
        if (Schema::hasTable('users')) {
            Schema::rename('users', 'usuarios');
        }
        
        if (Schema::hasTable('password_reset_tokens')) {
            Schema::rename('password_reset_tokens', 'tokens_restablecimiento_contrasena');
        }
        
        if (Schema::hasTable('sessions')) {
            Schema::rename('sessions', 'sesiones');
        }
        
        // La tabla cache se mantiene con el mismo nombre
        
        if (Schema::hasTable('cache_locks')) {
            Schema::rename('cache_locks', 'bloqueos_cache');
        }
        
        if (Schema::hasTable('jobs')) {
            Schema::rename('jobs', 'trabajos');
        }
        
        if (Schema::hasTable('job_batches')) {
            Schema::rename('job_batches', 'lotes_trabajos');
        }
        
        if (Schema::hasTable('failed_jobs')) {
            Schema::rename('failed_jobs', 'trabajos_fallidos');
        }
        
        // Tablas específicas del sistema que ya están en español se mantienen
        // vehiculos, conductores, infracciones, inspectores, etc.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir nombres a inglés
        if (Schema::hasTable('usuarios')) {
            Schema::rename('usuarios', 'users');
        }
        
        if (Schema::hasTable('tokens_restablecimiento_contrasena')) {
            Schema::rename('tokens_restablecimiento_contrasena', 'password_reset_tokens');
        }
        
        if (Schema::hasTable('sesiones')) {
            Schema::rename('sesiones', 'sessions');
        }
        
        if (Schema::hasTable('bloqueos_cache')) {
            Schema::rename('bloqueos_cache', 'cache_locks');
        }
        
        if (Schema::hasTable('trabajos')) {
            Schema::rename('trabajos', 'jobs');
        }
        
        if (Schema::hasTable('lotes_trabajos')) {
            Schema::rename('lotes_trabajos', 'job_batches');
        }
        
        if (Schema::hasTable('trabajos_fallidos')) {
            Schema::rename('trabajos_fallidos', 'failed_jobs');
        }
    }
};
