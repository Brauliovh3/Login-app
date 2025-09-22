<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inspector extends Model
{
    use HasFactory;

    protected $table = 'inspectores';

    protected $fillable = [
        'dni',
        'nombres',
        'apellidos',
        'nombre_completo',
        'codigo_inspector',
        'telefono',
        'email',
        'estado'
    ];
}