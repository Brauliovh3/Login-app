<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Acta extends Model
{
    // Estado constants
    public const ESTADO_PENDIENTE = 0;
    public const ESTADO_PROCESADA = 1;
    public const ESTADO_ANULADA = 2;
    public const ESTADO_PAGADA = 3;

    /**
     * Map numeric estado to human readable label
     * @var array<int,string>
     */
    protected static array $estadosMap = [
        self::ESTADO_PENDIENTE => 'pendiente',
        self::ESTADO_PROCESADA => 'procesada',
        self::ESTADO_ANULADA => 'anulada',
        self::ESTADO_PAGADA => 'pagada',
    ];

    /**
     * Append human readable estado to model's array/json form
     * @var array<int,string>
     */
    protected $appends = ['estado_texto'];

    protected $fillable = [
        'numero_acta',
        'codigo_ds',
        'lugar_intervencion',
        'fecha_intervencion',
        'hora_intervencion',
        'inspector_responsable',
        'tipo_servicio',
        'tipo_agente',
        'placa',
        'razon_social',
        'ruc_dni',
        'nombre_conductor',
        'licencia',
        'clase_licencia',
        'origen',
        'destino',
        'numero_personas',
        'descripcion_hechos',
        'medios_probatorios',
        'calificacion',
        'medida_administrativa',
        'sancion',
        'observaciones_intervenido',
        'observaciones_inspector',
        'estado',
        'user_id'
    ];

    protected $casts = [
        'fecha_intervencion' => 'date',
        'hora_intervencion' => 'datetime:H:i',
    'estado' => 'integer',
        'numero_personas' => 'integer'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Generar número de acta automático (se reinicia cada año)
    public static function generarNumeroActa(): string
    {
        $year = date('Y');
        
        // Buscar la última acta del año actual
        $ultimaActa = self::where('numero_acta', 'like', "DRTC-APU-{$year}-%")
                         ->orderBy('numero_acta', 'desc')
                         ->first();
        
        if ($ultimaActa) {
            // Extraer el número secuencial de la última acta del año
            $partes = explode('-', $ultimaActa->numero_acta);
            $ultimoNumero = intval(end($partes));
            $nuevoNumero = $ultimoNumero + 1;
        } else {
            // Primera acta del año
            $nuevoNumero = 1;
        }
        
        return 'DRTC-APU-' . $year . '-' . str_pad($nuevoNumero, 6, '0', STR_PAD_LEFT);
    }

    // Obtener el próximo número de acta (para mostrar en el formulario)
    public static function obtenerProximoNumero(): string
    {
        return self::generarNumeroActa();
    }

    // Scope para filtros
    public function scopeByEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    public function scopeByRucDni($query, $rucDni)
    {
        return $query->where('ruc_dni', 'like', "%{$rucDni}%");
    }

    public function scopeByPlaca($query, $placa)
    {
        return $query->where('placa', 'like', "%{$placa}%");
    }

    public function scopeByFechaRango($query, $fechaDesde, $fechaHasta)
    {
        return $query->whereBetween('fecha_intervencion', [$fechaDesde, $fechaHasta]);
    }

    /**
     * Accessor: estado_texto (human readable)
     */
    public function getEstadoTextoAttribute(): ?string
    {
        $estado = $this->attributes['estado'] ?? null;

        if ($estado === null) {
            return null;
        }

        return self::$estadosMap[(int)$estado] ?? null;
    }

    /**
     * Mutator: allow setting estado by text or numeric
     */
    public function setEstadoAttribute($value): void
    {
        if (is_string($value)) {
            $found = array_search($value, self::$estadosMap, true);
            if ($found !== false) {
                $this->attributes['estado'] = $found;
                return;
            }
            // allow numeric string
            if (is_numeric($value)) {
                $this->attributes['estado'] = (int)$value;
                return;
            }
        }

        $this->attributes['estado'] = $value;
    }
}
