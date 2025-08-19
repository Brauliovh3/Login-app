<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class ActaController extends Controller
{
    public function index()
    {
        $actas = DB::table('actas')
            ->join('inspectores', 'actas.inspector_id', '=', 'inspectores.id')
            ->join('vehiculos', 'actas.vehiculo_id', '=', 'vehiculos.id')
            ->join('conductores', 'actas.conductor_id', '=', 'conductores.id')
            ->join('infracciones', 'actas.infraccion_id', '=', 'infracciones.id')
            ->select(
                'actas.*',
                'inspectores.nombre as inspector_nombre',
                'vehiculos.placa',
                'conductores.nombre as conductor_nombre',
                'infracciones.descripcion as infraccion_descripcion'
            )
            ->orderBy('actas.created_at', 'desc')
            ->paginate(15);

        return view('fiscalizador.actas.index', compact('actas'));
    }

    public function create()
    {
        $vehiculos = DB::table('vehiculos')->where('estado', 'activo')->get();
        $conductores = DB::table('conductores')->where('estado_licencia', 'vigente')->get();
        $infracciones = DB::table('infracciones')->get();
        $inspectores = DB::table('inspectores')->where('estado', 'activo')->get();

        // Calcular el siguiente número de acta y pasarlo a la vista (solo para mostrar)
        try {
            $proximoNumero = $this->generarNumeroActaUnico();
        } catch (\Exception $e) {
            $proximoNumero = 'DRTC-APU-' . date('Y') . '-000';
        }

        return view('fiscalizador.actas.create', compact('vehiculos', 'conductores', 'infracciones', 'inspectores'))->with('proximo_numero_acta', $proximoNumero);
    }

    public function store(Request $request)
    {
        // Verificar si es un formulario de acta libre (sin IDs) o estructurado
        if ($request->has('placa_1') && $request->has('nombre_conductor_1')) {
            return $this->storeActaLibre($request);
        }
        
        // Método original para formularios estructurados
        $request->validate([
            'vehiculo_id' => 'required|exists:vehiculos,id',
            'conductor_id' => 'required|exists:conductores,id',
            'infraccion_id' => 'required|exists:infracciones,id',
            'inspector_id' => 'required|exists:inspectores,id',
            'ubicacion' => 'required|string|max:255',
            'descripcion_hechos' => 'required|string',
            'monto_multa' => 'required|numeric|min:0',
        ]);

        // Obtener la hora actual exacta para el registro
        $horaActual = Carbon::now();
        
    // Generar número de acta único
    $numeroActa = $this->generarNumeroActaUnico();

        // Preparar valores adicionales (nombre, dni, licencia) para guardar en la tabla actas
        $nombreConductorParaDB = $request->nombre_conductor ?? $request->nombre_conductor_1 ?? null;
        $rucDniParaDB = $request->ruc_dni ?? null;
        $licenciaParaDB = $request->licencia_conductor ?? $request->licencia_conductor_1 ?? null;

        // Si nos pasan conductor_id y no vienen los datos, intentar obtenerlos de la tabla conductores
        if (empty($nombreConductorParaDB) && $request->filled('conductor_id')) {
            try {
                $cond = DB::table('conductores')->where('id', $request->conductor_id)->first();
                if ($cond) {
                    $nombreConductorParaDB = $cond->nombre ?? $nombreConductorParaDB;
                    // algunos esquemas usan 'dni' o 'documento'
                    $rucDniParaDB = $rucDniParaDB ?? ($cond->dni ?? $cond->documento ?? null);
                    $licenciaParaDB = $licenciaParaDB ?? ($cond->licencia ?? null);
                }
            } catch (\Exception $e) {
                // no bloquear el flujo si la tabla conductores no existe
            }
        }

        // Determinar nombres de columnas según el esquema actual y mapear valores
        $lugar = $request->ubicacion ?? $request->lugar_intervencion ?? null;
        $descripcionVal = $request->descripcion ?? $request->descripcion_hechos ?? null;

        $insertData = [
            'numero_acta' => $numeroActa,
            'vehiculo_id' => $request->vehiculo_id,
            'conductor_id' => $request->conductor_id,
            'infraccion_id' => $request->infraccion_id,
            'inspector_id' => $request->inspector_id,
            // Campos redundantes para facilitar búsquedas y reportes
            'nombre_conductor' => $nombreConductorParaDB,
            'ruc_dni' => $rucDniParaDB,
            'licencia_conductor' => $licenciaParaDB,
            'fecha_intervencion' => $horaActual->toDateString(),
            'hora_intervencion' => $horaActual->toTimeString(),
            'hora_inicio_registro' => $horaActual->toDateTimeString(), // Hora exacta del inicio
            'monto_multa' => $request->monto_multa,
            'estado' => 'registrada', // Cambiar de 'pendiente' a 'registrada'
            'user_id' => Auth::id() ?? 1, // Usar 1 como fallback si no hay usuario autenticado
            'created_at' => $horaActual->toDateTimeString(),
            'updated_at' => $horaActual->toDateTimeString(),
        ];

        // Ubicación / lugar_intervencion
        if ($lugar !== null) {
            if (Schema::hasColumn('actas', 'lugar_intervencion')) {
                $insertData['lugar_intervencion'] = $lugar;
            } else {
                // fallback a 'ubicacion'
                $insertData['ubicacion'] = $lugar;
            }
        }

        // Descripción (descripcion / descripcion_hechos)
        if ($descripcionVal !== null) {
            if (Schema::hasColumn('actas', 'descripcion_hechos')) {
                $insertData['descripcion_hechos'] = $descripcionVal;
            } else {
                $insertData['descripcion'] = $descripcionVal;
            }
        }

        // Intentar insertar con reintentos si existe colisión en numero_acta
        $actaId = null;
        $attempts = 0;
        while ($attempts < 5) {
            try {
                $insertData['numero_acta'] = $numeroActa = $this->generarNumeroActaUnico();
                $actaId = DB::table('actas')->insertGetId($insertData);
                break;
            } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
                // Colisión, reintentar con nuevo número
                $attempts++;
                logger()->warning('Colisión en numero_acta, reintentando generación', ['attempt' => $attempts, 'error' => $e->getMessage()]);
                usleep(100000); // esperar 100ms
            }
        }
        if (!$actaId) {
            // Si no se pudo insertar después de reintentos, lanzar excepción
            throw new \Exception('No se pudo generar un numero_acta único después de varios intentos.');
        }

        // Crear notificación solo si el usuario está autenticado
        if (Auth::id()) {
            DB::table('notifications')->insert([
                'user_id' => Auth::id(),
                'title' => 'Nueva Acta Registrada',
                'message' => "Se ha registrado una nueva acta de infracción #{$numeroActa} a las {$horaActual->format('H:i:s')}",
                'type' => 'info',
                'read' => false,
                'created_at' => $horaActual->toDateTimeString(),
                'updated_at' => $horaActual->toDateTimeString(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Acta registrada exitosamente',
            'acta_id' => $actaId,
            'numero_acta' => $numeroActa,
            'hora_registro' => $horaActual->format('d/m/Y H:i:s')
        ]);
    }

    /**
     * Método para guardar actas con formato libre (datos de texto)
     */
    public function storeActaLibre(Request $request)
    {
        $request->validate([
            'placa_1' => 'required|string|max:10',
            'nombre_conductor_1' => 'required|string|max:255',
            'licencia_conductor_1' => 'required|string|max:20',
            'razon_social' => 'nullable|string|max:255',
            'ruc_dni' => 'required|string|max:20',
            'lugar_intervencion' => 'required|string|max:500',
            // Origen/Destino ya no son obligatorios - pueden enviarse o quedar vacíos
            'origen_viaje' => 'nullable|string|max:255',
            'destino_viaje' => 'nullable|string|max:255',
            'tipo_servicio' => 'required|string|max:50',
            'descripcion_hechos' => 'required|string',
        ]);

        // Obtener la hora actual exacta para el registro
        $horaActual = Carbon::now();
        
    // Generar número de acta único
    $numeroActa = $this->generarNumeroActaUnico();

        // Preparar descripción completa
        $descripcionCompleta = "ACTA DE FISCALIZACIÓN\n\n";
        $descripcionCompleta .= "DATOS DEL VEHÍCULO:\n";
        $descripcionCompleta .= "Placa: " . $request->placa_1 . "\n";
        
        // Solo agregar empresa/operador si hay razón social
        if (!empty($request->razon_social)) {
            $descripcionCompleta .= "Empresa/Operador: " . $request->razon_social . "\n";
        }
        
        $descripcionCompleta .= "RUC/DNI: " . $request->ruc_dni . "\n\n";
        
        $descripcionCompleta .= "DATOS DEL CONDUCTOR:\n";
        $descripcionCompleta .= "Nombre: " . $request->nombre_conductor_1 . "\n";
        $descripcionCompleta .= "Licencia: " . $request->licencia_conductor_1 . "\n\n";
        
        $descripcionCompleta .= "DATOS DEL VIAJE:\n";
        if (!empty($request->origen_viaje)) {
            $descripcionCompleta .= "Origen: " . $request->origen_viaje . "\n";
        }
        if (!empty($request->destino_viaje)) {
            $descripcionCompleta .= "Destino: " . $request->destino_viaje . "\n";
        }
        $descripcionCompleta .= "Tipo de Servicio: " . $request->tipo_servicio . "\n\n";
        
        $descripcionCompleta .= "DESCRIPCIÓN DE LOS HECHOS:\n";
        $descripcionCompleta .= $request->descripcion_hechos;

        // Defensive: truncar valores que puedan superar la longitud de la columna en la BD
        try {
            $db = env('DB_DATABASE');
            $maxPlaca = null;
            // Intentar obtener el tamaño máximo de la columna desde information_schema
            $col = DB::selectOne("SELECT CHARACTER_MAXIMUM_LENGTH as len FROM information_schema.columns WHERE table_schema = ? AND table_name = 'actas' AND column_name = 'placa_vehiculo'", [$db]);
            if ($col && isset($col->len) && is_numeric($col->len)) {
                $maxPlaca = (int) $col->len;
            }
        } catch (\Exception $e) {
            // Si falla la consulta, no interrumpimos: usaremos una longitud por defecto
            $maxPlaca = null;
        }

        $placaOriginal = (string)($request->placa_1 ?? '');
        if ($maxPlaca && $maxPlaca > 0) {
            $placaParaDB = mb_substr($placaOriginal, 0, $maxPlaca);
        } else {
            // Fallback conservador: limitar a 50 caracteres en memoria
            $placaParaDB = mb_substr($placaOriginal, 0, 50);
        }

        try {
            // Preparar insert adaptable al esquema de la tabla
            $insertData = [
                'numero_acta' => $numeroActa,
                'vehiculo_id' => null, // No vinculado a tabla vehiculos
                'conductor_id' => null, // No vinculado a tabla conductores
                'infraccion_id' => 1, // ID genérico de infracción
                'inspector_id' => 1, // ID genérico de inspector
                // Guardar nombre completo y documento (RUC/DNI) enviados desde el formulario libre
                'nombre_conductor' => $request->nombre_conductor_1 ?? null,
                'ruc_dni' => $request->ruc_dni ?? null,
                'monto_multa' => $request->monto_multa ?? 0,
                    'estado' => 'registrada',
                    // Hora de inicio del registro siempre guardamos si existe la columna
                    'hora_inicio_registro' => $horaActual->toDateTimeString(),
                    'observaciones' => $request->observaciones_inspector ?? null,
                    'user_id' => Auth::id() ?? 1, // Usar 1 como fallback
                    'created_at' => $horaActual->toDateTimeString(),
                    'updated_at' => $horaActual->toDateTimeString(),
            ];

            // Placa: placa_vehiculo o placa
            if (Schema::hasColumn('actas', 'placa_vehiculo')) {
                $insertData['placa_vehiculo'] = $placaParaDB;
            } elseif (Schema::hasColumn('actas', 'placa')) {
                $insertData['placa'] = $placaParaDB;
            }

            // Fecha / Hora: mapear a columnas existentes para evitar SQL errors en esquemas distintos
            $fechaVal = $request->fecha_intervencion ?? $horaActual->toDateString();
            $horaVal = $request->hora_intervencion ?? $horaActual->toTimeString();
            if (Schema::hasColumn('actas', 'fecha_intervencion')) {
                $insertData['fecha_intervencion'] = $fechaVal;
            } elseif (Schema::hasColumn('actas', 'fecha_infraccion')) {
                $insertData['fecha_infraccion'] = $fechaVal;
            }

            if (Schema::hasColumn('actas', 'hora_intervencion')) {
                $insertData['hora_intervencion'] = $horaVal;
            } elseif (Schema::hasColumn('actas', 'hora_infraccion')) {
                $insertData['hora_infraccion'] = $horaVal;
            }

            // Lugar / ubicacion
            $lugar = $request->lugar_intervencion ?? null;
            if ($lugar !== null) {
                if (Schema::hasColumn('actas', 'lugar_intervencion')) {
                    $insertData['lugar_intervencion'] = $lugar;
                } else {
                    $insertData['ubicacion'] = $lugar;
                }
            }

            // Descripción
            if (Schema::hasColumn('actas', 'descripcion_hechos')) {
                $insertData['descripcion_hechos'] = $descripcionCompleta;
            } else {
                $insertData['descripcion'] = $descripcionCompleta;
            }

            // Intentar insertar con reintentos si existe colisión en numero_acta
            $actaId = null;
            $attempts = 0;
            while ($attempts < 5) {
                try {
                    $insertData['numero_acta'] = $numeroActa = $this->generarNumeroActaUnico();
                    $actaId = DB::table('actas')->insertGetId($insertData);
                    break;
                } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
                    $attempts++;
                    logger()->warning('Colisión en numero_acta (libre), reintentando generación', ['attempt' => $attempts, 'error' => $e->getMessage()]);
                    usleep(100000);
                }
            }
            if (!$actaId) {
                throw new \Exception('No se pudo generar un numero_acta único después de varios intentos (libre).');
            }
        } catch (\Exception $e) {
            // Loguear y devolver JSON de error legible para evitar 500 silencioso
            logger()->error('Error al insertar acta libre: ' . $e->getMessage(), [
                'exception' => $e,
                'placa_original' => $placaOriginal,
                'placa_para_db' => $placaParaDB
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error general al crear acta: ' . $e->getMessage()
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Acta de fiscalización registrada exitosamente',
            'acta_id' => $actaId,
            'numero_acta' => $numeroActa,
            'hora_registro' => $horaActual->format('d/m/Y H:i:s'),
            'ubicacion' => $request->lugar_intervencion
        ]);
    }

    /**
     * Actualizar el acta con la hora final de registro
     */
    public function finalizarRegistro(Request $request, $id)
    {
        $horaFinal = Carbon::now();
        
        $acta = DB::table('actas')->where('id', $id)->first();
        if (!$acta) {
            return response()->json(['error' => 'Acta no encontrada'], 404);
        }

        // Calcular tiempo total de registro
        $horaInicio = Carbon::parse($acta->hora_inicio_registro ?? $acta->created_at);
        $tiempoTotal = $horaInicio->diffInMinutes($horaFinal);

        DB::table('actas')
            ->where('id', $id)
            ->update([
                'hora_fin_registro' => $horaFinal->toDateTimeString(),
                'tiempo_total_registro' => $tiempoTotal, // en minutos
                'estado' => 'completada',
                'updated_at' => $horaFinal->toDateTimeString()
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Registro de acta finalizado',
            'hora_finalizacion' => $horaFinal->format('d/m/Y H:i:s'),
            'tiempo_total' => $tiempoTotal . ' minutos'
        ]);
    }

    /**
     * Guardar progreso del llenado en tiempo real
     */
    public function guardarProgreso(Request $request, $id)
    {
        $horaActual = Carbon::now();
        
        // Preparar datos de progreso
        $datosProgreso = $request->only([
            'vehiculo_id', 'conductor_id', 'infraccion_id', 'inspector_id',
            'ubicacion', 'descripcion', 'monto_multa'
        ]);
        
        $datosProgreso['ultima_actualizacion'] = $horaActual->toDateTimeString();
        $datosProgreso['updated_at'] = $horaActual->toDateTimeString();

        DB::table('actas')
            ->where('id', $id)
            ->update($datosProgreso);

        return response()->json([
            'success' => true,
            'hora_actualizacion' => $horaActual->format('H:i:s'),
            'mensaje' => 'Progreso guardado automáticamente'
        ]);
    }

    public function show($id)
    {
        $acta = DB::table('actas')
            ->join('inspectores', 'actas.inspector_id', '=', 'inspectores.id')
            ->join('vehiculos', 'actas.vehiculo_id', '=', 'vehiculos.id')
            ->join('conductores', 'actas.conductor_id', '=', 'conductores.id')
            ->join('infracciones', 'actas.infraccion_id', '=', 'infracciones.id')
            ->join('empresas', 'vehiculos.empresa_id', '=', 'empresas.id')
            ->select(
                'actas.*',
                'inspectores.nombre as inspector_nombre',
                'vehiculos.placa',
                'vehiculos.modelo',
                'conductores.nombre as conductor_nombre',
                'conductores.dni',
                'conductores.licencia',
                'infracciones.descripcion as infraccion_descripcion',
                'infracciones.codigo as infraccion_codigo',
                'empresas.razon_social as empresa_nombre'
            )
            ->where('actas.id', $id)
            ->first();

        if (!$acta) {
            return response()->json(['error' => 'Acta no encontrada'], 404);
        }

        return response()->json(['acta' => $acta]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,procesada,anulada'
        ]);

        DB::table('actas')
            ->where('id', $id)
            ->update([
                'estado' => $request->estado,
                'updated_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Estado del acta actualizado exitosamente'
        ]);
    }

    public function getPendientes()
    {
        $pendientes = DB::table('actas')
            ->join('vehiculos', 'actas.vehiculo_id', '=', 'vehiculos.id')
            ->join('conductores', 'actas.conductor_id', '=', 'conductores.id')
            ->join('infracciones', 'actas.infraccion_id', '=', 'infracciones.id')
            ->select(
                'actas.*',
                'vehiculos.placa',
                'conductores.nombre as conductor_nombre',
                'infracciones.descripcion as infraccion_descripcion'
            )
            ->where('actas.estado', 'pendiente')
            ->orderBy('actas.fecha_infraccion', 'desc')
            ->get();

        return response()->json(['actas' => $pendientes]);
    }

    /**
     * Consultar actas por DNI/RUC - Sistema simple
     */
    public function consultarPorDocumento($documento)
    {
        try {
            // Limpiar el documento
            $documento = trim($documento);
            
            // Buscar en la tabla actas con múltiples patrones
            $actas = DB::table('actas')
                ->where(function($query) use ($documento) {
                    $query->where('descripcion', 'LIKE', "%RUC/DNI: {$documento}%")
                          ->orWhere('descripcion', 'LIKE', "%DNI: {$documento}%")
                          ->orWhere('descripcion', 'LIKE', "%RUC: {$documento}%")
                          ->orWhere('descripcion', 'LIKE', "%{$documento}%"); // Búsqueda más amplia
                })
                ->select([
                    'id',
                    'numero_acta',
                    'placa_vehiculo',
                    'ubicacion',
                    'monto_multa',
                    'estado',
                    'fecha_infraccion',
                    'hora_infraccion',
                    'descripcion',
                    'created_at'
                ])
                ->orderBy('created_at', 'desc')
                ->get();

            // Si no encuentra nada, intentar búsqueda por patrones más específicos
            if ($actas->isEmpty()) {
                $actas = DB::table('actas')
                    ->whereRaw("REPLACE(REPLACE(descripcion, ' ', ''), '\n', '') LIKE ?", ["%{$documento}%"])
                    ->select([
                        'id',
                        'numero_acta',
                        'placa_vehiculo',
                        'ubicacion',
                        'monto_multa',
                        'estado',
                        'fecha_infraccion',
                        'hora_infraccion',
                        'descripcion',
                        'created_at'
                    ])
                    ->orderBy('created_at', 'desc')
                    ->get();
            }

            // Extraer datos del campo descripción para cada acta
            $actasFormatadas = $actas->map(function($acta) {
                $descripcion = $acta->descripcion;
                
                // Extraer empresa/operador
                $empresa = '';
                if (preg_match('/Empresa\/Operador:\s*(.+?)[\n\r]/', $descripcion, $matches)) {
                    $empresa = trim($matches[1]);
                }
                
                // Extraer conductor
                $conductor = '';
                if (preg_match('/Nombre:\s*(.+?)[\n\r]/', $descripcion, $matches)) {
                    $conductor = trim($matches[1]);
                }
                
                // Extraer licencia
                $licencia = '';
                if (preg_match('/Licencia:\s*(.+?)[\n\r]/', $descripcion, $matches)) {
                    $licencia = trim($matches[1]);
                }
                
                // Extraer origen y destino
                $origen = '';
                $destino = '';
                if (preg_match('/Origen:\s*(.+?)[\n\r]/', $descripcion, $matches)) {
                    $origen = trim($matches[1]);
                }
                if (preg_match('/Destino:\s*(.+?)[\n\r]/', $descripcion, $matches)) {
                    $destino = trim($matches[1]);
                }

                // Extraer RUC/DNI
                $documento_extraido = '';
                if (preg_match('/RUC\/DNI:\s*(\d+)/', $descripcion, $matches)) {
                    $documento_extraido = $matches[1];
                }

                return [
                    'id' => $acta->id,
                    'numero_acta' => $acta->numero_acta,
                    'placa' => $acta->placa_vehiculo,
                    'empresa' => $empresa,
                    'conductor' => $conductor,
                    'licencia' => $licencia,
                    'origen' => $origen,
                    'destino' => $destino,
                    'documento' => $documento_extraido,
                    'ubicacion' => $acta->ubicacion,
                    'monto_multa' => $acta->monto_multa,
                    'estado' => $acta->estado,
                    'fecha' => $acta->fecha_infraccion,
                    'hora' => $acta->hora_infraccion,
                    'fecha_registro' => $acta->created_at
                ];
            });

            return response()->json([
                'success' => true,
                'total' => $actasFormatadas->count(),
                'documento' => $documento,
                'actas' => $actasFormatadas,
                'debug' => [
                    'total_sin_formato' => $actas->count(),
                    'query_usado' => "Búsqueda con documento: {$documento}"
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al consultar las actas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Consultar actas con filtros múltiples
     */
    public function consultarActas(Request $request)
    {
        try {
            $query = DB::table('actas');
            
            // Filtrar por número de acta
            if ($request->numero_acta) {
                $query->where('numero_acta', 'LIKE', '%' . $request->numero_acta . '%');
            }
            
            // Filtrar por RUC/DNI
            if ($request->ruc_dni) {
                $query->where('descripcion', 'LIKE', '%' . $request->ruc_dni . '%');
            }
            
            // Filtrar por placa
            if ($request->placa) {
                $query->where('placa_vehiculo', 'LIKE', '%' . strtoupper($request->placa) . '%');
            }
            
            // Filtrar por estado
            if ($request->estado) {
                $query->where('estado', $request->estado);
            }
            
            // Filtrar por fecha desde
            if ($request->fecha_desde) {
                $query->where('fecha_infraccion', '>=', $request->fecha_desde);
            }
            
            // Filtrar por fecha hasta
            if ($request->fecha_hasta) {
                $query->where('fecha_infraccion', '<=', $request->fecha_hasta);
            }

            $actas = $query->select([
                'id',
                'numero_acta',
                'placa_vehiculo',
                'ubicacion',
                'monto_multa',
                'estado',
                'fecha_infraccion',
                'hora_infraccion',
                'descripcion',
                'created_at'
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

            // Formatear resultados
            $actasFormatadas = $actas->getCollection()->map(function($acta) {
                $descripcion = $acta->descripcion;
                
                // Extraer datos
                $empresa = '';
                $conductor = '';
                if (preg_match('/Empresa\/Operador:\s*(.+?)\\n/', $descripcion, $matches)) {
                    $empresa = trim($matches[1]);
                }
                if (preg_match('/Nombre:\s*(.+?)\\n/', $descripcion, $matches)) {
                    $conductor = trim($matches[1]);
                }

                return [
                    'id' => $acta->id,
                    'numero_acta' => $acta->numero_acta,
                    'placa' => $acta->placa_vehiculo,
                    'empresa' => $empresa,
                    'conductor' => $conductor,
                    'ubicacion' => $acta->ubicacion,
                    'monto_multa' => $acta->monto_multa,
                    'estado' => $acta->estado,
                    'fecha' => $acta->fecha_infraccion,
                    'hora' => $acta->hora_infraccion
                ];
            });

            return response()->json([
                'success' => true,
                'total' => $actas->total(),
                'current_page' => $actas->currentPage(),
                'last_page' => $actas->lastPage(),
                'actas' => $actasFormatadas
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al consultar las actas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Genera un número de acta único con formato DRTC-APU-YYYY-XXX
     * Busca los existentes para el año y calcula el siguiente sufijo numérico disponible.
     */
    private function generarNumeroActaUnico()
    {
        $year = date('Y');
        $prefix = 'DRTC-APU-' . $year . '-';

        try {
            $rows = DB::table('actas')
                ->where('numero_acta', 'like', $prefix . '%')
                ->pluck('numero_acta');

            $max = 0;
            foreach ($rows as $num) {
                $parts = explode('-', $num);
                $suf = end($parts);
                if (is_numeric($suf)) {
                    $n = (int) $suf;
                    if ($n > $max) {
                        $max = $n;
                    }
                }
            }

            // Si no hay registros para el año actual, comenzar en 0 (000000)
            if (count($rows) === 0) {
                $next = 0;
            } else {
                $next = $max + 1;
            }

            // Usar padding de 6 dígitos para consistencia con la vista
            return $prefix . str_pad($next, 6, '0', STR_PAD_LEFT);
        } catch (\Exception $e) {
            // En caso de fallo al consultar la BD, usar fallback simple
            $count = DB::table('actas')->count();
            $next = $count === 0 ? 0 : ($count + 1);
            return $prefix . str_pad($next, 6, '0', STR_PAD_LEFT);
        }
    }

}
