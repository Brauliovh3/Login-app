<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Infraccion extends Model
{
    protected $table = 'infracciones';
    
    protected $fillable = [
        'codigo',
        'aplica_sobre',
        'reglamento',
        'norma_modificatoria',
        'clase_pago',
        'sancion',
        'tipo',
        'medida_preventiva',
        'gravedad',
        'otros_responsables__otros_beneficios'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación con detalles de infracciones
     */
    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleInfraccion::class, 'infraccion_id');
    }

    /**
     * Scope para filtrar por gravedad
     */
    public function scopeByGravedad($query, $gravedad)
    {
        return $query->where('gravedad', $gravedad);
    }

    /**
     * Scope para filtrar por tipo de aplicación
     */
    public function scopeByAplicaSobre($query, $aplica_sobre)
    {
        return $query->where('aplica_sobre', $aplica_sobre);
    }

    /**
     * Accessor para formatear el código
     */
    public function getCodigoFormateadoAttribute()
    {
        return strtoupper($this->codigo);
    }
}
