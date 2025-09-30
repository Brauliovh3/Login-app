<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetalleInfraccion extends Model
{
    protected $table = 'detalle_infraccion';
    
    protected $fillable = [
        'infraccion_id',
        'descripcion',
        'subcategoria',
        'descripcion_detallada',
        'condiciones_especiales'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación con infracciones
     */
    public function infraccion(): BelongsTo
    {
        return $this->belongsTo(Infraccion::class, 'infraccion_id');
    }

    /**
     * Scope para filtrar por subcategoría
     */
    public function scopeBySubcategoria($query, $subcategoria)
    {
        return $query->where('subcategoria', $subcategoria);
    }

    /**
     * Accessor para mostrar subcategoría formateada
     */
    public function getSubcategoriaFormateadaAttribute()
    {
        return $this->subcategoria ? "({$this->subcategoria})" : '';
    }
}
