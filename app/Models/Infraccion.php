<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Infraccion extends Model
{
    protected $fillable = [
        'agente_infractor',
        'placa',
        'razon_social',
        'ruc_dni',
        'fecha_inicio',
        'hora_inicio',
        'fecha_fin',
        'hora_fin',
        'nombre_conductor1',
        'licencia_conductor1',
        'clase_categoria',
        'lugar_intervencion',
        'km_via_nacional',
        'origen_viaje',
        'destino_viaje',
        'tipo_servicio',
        'inspector',
        'descripcion_hechos',
        'medios_probatorios',
        'calificacion_infraccion',
        'medidas_administrativas',
        'sancion',
        'observaciones_intervenido',
        'observaciones_inspector',
        'user_id'
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'hora_inicio' => 'datetime:H:i',
        'hora_fin' => 'datetime:H:i',
    ];

    /**
     * Relación con el usuario que registró la infracción
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
