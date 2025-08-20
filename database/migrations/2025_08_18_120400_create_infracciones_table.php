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
		if (Schema::hasTable('infracciones')) {
			// Tabla ya creada por una migración anterior; omitir para evitar errores duplicados.
			return;
		}

		Schema::create('infracciones', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->string('codigo_infraccion')->unique();
			$table->string('descripcion');
			$table->text('detalle_completo')->nullable();
			$table->string('gravedad')->nullable();
			$table->decimal('multa_uit', 8, 2)->nullable();
			$table->decimal('multa_soles', 10, 2)->nullable();
			$table->integer('puntos_licencia')->nullable();
			$table->boolean('retencion_licencia')->default(false);
			$table->boolean('retencion_vehiculo')->default(false);
			$table->boolean('internamiento_deposito')->default(false);
			$table->string('estado')->default('activo');
			$table->text('base_legal')->nullable();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('infracciones');
	}
};
