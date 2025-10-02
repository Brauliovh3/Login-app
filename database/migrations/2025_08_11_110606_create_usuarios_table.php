
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
		Schema::create('usuarios', function (Blueprint $table) {
			$table->id();
			$table->string('name');
			$table->string('username')->unique();
			$table->string('email')->unique();
			$table->timestamp('email_verified_at')->nullable();
			$table->string('password');
			$table->enum('role', ['fiscalizador', 'administrador', 'superadmin', 'ventanilla'])->default('fiscalizador');
			$table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
			$table->string('approval_status')->nullable();
			$table->timestamp('approved_at')->nullable();
			$table->unsignedBigInteger('approved_by')->nullable();
			$table->timestamp('blocked_at')->nullable();
			$table->text('rejection_reason')->nullable();
			$table->rememberToken();
			$table->timestamps();
			
			// Índices para optimizar consultas
			$table->index('username');
			$table->index('email');
			$table->index('role');
			$table->index('status');
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('usuarios');
	}
};
