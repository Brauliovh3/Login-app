<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CargaPasajero extends Model
{
	protected $fillable = [
		'informe',
		'resolucion',
		'conductor',
		'licencia_conductor',
		'estado'
	];
}
