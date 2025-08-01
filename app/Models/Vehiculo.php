<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehiculo extends Model
{
    protected $table = 'vehiculos';
    
    protected $fillable = [
        'placa', 'marca', 'modelo', 'color', 'año', 'numero_motor', 
        'numero_chasis', 'clase', 'categoria', 'combustible', 'asientos',
        'peso_bruto', 'carga_util', 'empresa_id', 'conductor_id', 'estado',
        'fecha_soat', 'fecha_revision_tecnica'
    ];

    protected $dates = ['fecha_soat', 'fecha_revision_tecnica'];

    public function conductor()
    {
        return $this->belongsTo(Conductor::class);
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }
}
