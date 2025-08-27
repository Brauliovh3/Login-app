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
        // Solo crear la tabla si no existe
        if (!Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->id();
                $table->string('type');
                $table->string('title');
                $table->text('message');
                // Use a plain unsignedBigInteger for user_id and index it.
                // Avoid adding a hard foreign-key constraint here because
                // the project uses a single canonical table named `usuarios`
                // and some environments may already contain a physical
                // `users` table (or may run this migration before that table
                // exists). A strict FK causes migration failures in those cases.
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->json('data')->nullable();
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
                
                $table->index(['user_id', 'read_at']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
