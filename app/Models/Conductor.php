<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conductor extends Model
{
    use HasFactory;

    protected $table = 'conductores';

    protected $fillable = [
        'dni',
        'nombres',
        'apellidos',
        'nombre_completo',
        'numero_licencia',
        'clase_categoria',
        'fecha_vencimiento',
        'estado',
        'puntos_acumulados',
        'empresa_id'
    ];

    protected $casts = [
        'fecha_vencimiento' => 'date'
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function getEstadoLicenciaAttribute()
    {
        if (!$this->fecha_vencimiento) {
            return 'sin_fecha';
        }
        
        return $this->fecha_vencimiento->lt(now()) ? 'vencida' : 'vigente';
    }
}