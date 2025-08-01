<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inspector extends Model
{
    protected $table = 'inspectores';
    
    protected $fillable = [
        'dni', 'nombres', 'apellidos', 'codigo_inspector', 'telefono',
        'email', 'fecha_ingreso', 'estado', 'zona_asignada', 'observaciones'
    ];

    protected $dates = ['fecha_ingreso'];

    public function getNombreCompletoAttribute()
    {
        return $this->nombres . ' ' . $this->apellidos;
    }

    public function getNombreAttribute()
    {
        return $this->nombre_completo;
    }
}
