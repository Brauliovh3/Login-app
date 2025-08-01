<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    protected $table = 'empresas';
    
    protected $fillable = [
        'razon_social', 'ruc', 'direccion', 'telefono', 'email', 'estado'
    ];

    public function conductores()
    {
        return $this->hasMany(Conductor::class);
    }
}
