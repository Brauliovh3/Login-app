<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inspeccion extends Model
{
    use HasFactory;

    protected $fillable = [
        'fecha_inspeccion',
        'hora_inicio',
        'inspector_principal',
        'tipo_agente',
        'placa_1',
        'razon_social',
        'ruc_dni',
        'fecha_hora_fin',
        'nombre_conductor_1',
        'licencia_conductor_1',
        'clase_categoria',
        'lugar_intervencion',
        'km_red_vial',
        'origen_viaje',
        'destino_viaje',
        'tipo_servicio',
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
        'fecha_inspeccion' => 'date',
        'hora_inicio' => 'datetime:H:i',
        'hora_fin' => 'datetime:H:i',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Obtener el estado general basado en las medidas aplicadas
    public function getEstadoGeneralAttribute()
    {
        switch ($this->medida_aplicada) {
            case 'ninguna':
                return 'Conforme';
            case 'orientacion':
                return 'Con Orientaciones';
            case 'observacion':
                return 'Con Observaciones';
            case 'decomiso':
                return 'Con Decomiso';
            case 'clausura_temporal':
            case 'clausura_definitiva':
                return 'Clausurado';
            case 'multa':
                return 'Multado';
            default:
                return 'Pendiente';
        }
    }

    // Obtener color para la vista basado en el estado
    public function getColorEstadoAttribute()
    {
        switch ($this->medida_aplicada) {
            case 'ninguna':
                return 'success';
            case 'orientacion':
                return 'info';
            case 'observacion':
                return 'warning';
            case 'decomiso':
                return 'warning';
            case 'clausura_temporal':
            case 'clausura_definitiva':
                return 'danger';
            case 'multa':
                return 'danger';
            default:
                return 'secondary';
        }
    }
}
