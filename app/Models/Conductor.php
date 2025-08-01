<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conductor extends Model
{
    protected $table = 'conductores';
    
    protected $fillable = [
        'dni', 'nombres', 'apellidos', 'fecha_nacimiento', 'direccion',
        'distrito', 'provincia', 'departamento', 'telefono', 'email',
        'numero_licencia', 'clase_categoria', 'fecha_expedicion', 
        'fecha_vencimiento', 'estado_licencia', 'empresa_id', 'estado',
        'puntos_acumulados'
    ];

    protected $dates = ['fecha_nacimiento', 'fecha_expedicion', 'fecha_vencimiento'];

    public function vehiculos()
    {
        return $this->hasMany(Vehiculo::class);
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function getNombreCompletoAttribute()
    {
        return $this->nombres . ' ' . $this->apellidos;
    }
}
