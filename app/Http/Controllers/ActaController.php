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
            ->leftJoin('inspectores', 'actas.inspector_id', '=', 'inspectores.id')
            ->leftJoin('vehiculos', 'actas.vehiculo_id', '=', 'vehiculos.id')
            ->leftJoin('conductores', 'actas.conductor_id', '=', 'conductores.id')
            ->leftJoin('infracciones', 'actas.infraccion_id', '=', 'infracciones.id')
            ->select(
                'actas.*',
                DB::raw("COALESCE(actas.inspector_responsable, inspectores.nombre) as inspector_nombre"),
                DB::raw("COALESCE(actas.placa_vehiculo, vehiculos.placa, actas.placa) as placa"),
                DB::raw("COALESCE(actas.nombre_conductor, conductores.nombre) as conductor_nombre"),
                DB::raw("COALESCE(infracciones.descripcion, actas.descripcion, actas.descripcion_hechos) as infraccion_descripcion")
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
        // Si es un formulario libre (placa_1 + nombre_conductor_1) delegar
        if ($request->has('placa_1') && $request->has('nombre_conductor_1')) {
            return $this->storeActaLibre($request);
        }

        // Ignorar cualquier numero_acta enviado por el cliente: el servidor genera el número definitivo
        try {
            if ($request->has('numero_acta')) {
                $request->request->remove('numero_acta');
            }
        } catch (\Exception $e) {
            // no bloquear si no se puede manipular el request
        }

        // Validar los campos que envía el formulario actual (evitar exigir IDs que no se envían)
        $request->validate([
            'descripcion_hechos' => 'required|string',
            'tipo_servicio' => 'required|string',
            'tipo_infraccion' => 'nullable|string',
            'gravedad' => 'nullable|string',
            'fecha_intervencion' => 'nullable|date',
            'hora_intervencion' => 'nullable',
            'inspector' => 'nullable|string',
            'inspector_principal' => 'nullable|string',
            'placa' => 'nullable|string',
            'placa_vehiculo' => 'nullable|string',
            'razon_social' => 'nullable|string',
            'ruc_dni' => 'nullable|string',
            'nombre_conductor' => 'nullable|string',
            'licencia_conductor' => 'nullable|string',
            'clase_licencia' => 'nullable|string',
            'codigo_infraccion' => 'nullable|string',
            'monto_multa' => 'nullable|numeric',
            'estado' => 'nullable',
        ]);

        // Obtener la hora actual exacta para el registro
        $horaActual = Carbon::now();

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
        // El formulario puede enviar 'lugar_intervencion' y/o 'direccion_especifica'
        $lugar = $request->lugar_intervencion ?? $request->ubicacion ?? null;
        if (empty($lugar) && $request->filled('direccion_especifica')) {
            $lugar = $request->direccion_especifica;
        }
        $descripcionVal = $request->descripcion ?? $request->descripcion_hechos ?? null;

        // Construir insert solo con columnas que existan en la tabla para evitar SQL errors
        // Construir insert con los campos que vienen del formulario (no forzar IDs)
        $insertData = [
            'nombre_conductor' => $nombreConductorParaDB,
            'ruc_dni' => $rucDniParaDB,
            'monto_multa' => $request->monto_multa ?? null,
            'user_id' => Auth::id() ?? null,
            'created_at' => $horaActual->toDateTimeString(),
            'updated_at' => $horaActual->toDateTimeString(),
        ];

        // Si el request trae un vehiculo_id/conductor_id/infraccion_id opcional, solamente añadirlos
        if ($request->filled('vehiculo_id') && Schema::hasColumn('actas', 'vehiculo_id')) {
            $insertData['vehiculo_id'] = $request->vehiculo_id;
        }
        if ($request->filled('conductor_id') && Schema::hasColumn('actas', 'conductor_id')) {
            $insertData['conductor_id'] = $request->conductor_id;
        }
        if ($request->filled('infraccion_id') && Schema::hasColumn('actas', 'infraccion_id')) {
            $insertData['infraccion_id'] = $request->infraccion_id;
        }

        // Mapear licencia: algunos esquemas usan 'licencia' en vez de 'licencia_conductor'
        if ($licenciaParaDB !== null) {
            if (Schema::hasColumn('actas', 'licencia')) {
                $insertData['licencia'] = $licenciaParaDB;
            } elseif (Schema::hasColumn('actas', 'licencia_conductor')) {
                $insertData['licencia_conductor'] = $licenciaParaDB;
            }
        }

        // Inspector responsable: formulario envía 'inspector' (nombre), guardarlo en inspector_responsable
        $inspectorNombreFormulario = $request->inspector ?? $request->inspector_principal ?? null;
        if ($inspectorNombreFormulario !== null && Schema::hasColumn('actas', 'inspector_responsable')) {
            $insertData['inspector_responsable'] = $inspectorNombreFormulario;
        }

        // Guardar nombre del inspector responsable si el formulario lo envía (campo 'inspector')
        $inspectorNombreFormulario = $request->inspector ?? $request->inspector_principal ?? null;
        if ($inspectorNombreFormulario !== null) {
            if (Schema::hasColumn('actas', 'inspector_responsable')) {
                $insertData['inspector_responsable'] = $inspectorNombreFormulario;
            }
        }

        // Fecha / Hora solo si las columnas existen
        if (Schema::hasColumn('actas', 'fecha_intervencion')) {
            $insertData['fecha_intervencion'] = $horaActual->toDateString();
        }
        if (Schema::hasColumn('actas', 'hora_intervencion')) {
            $insertData['hora_intervencion'] = $horaActual->toTimeString();
        }

        // Hora de inicio de registro: comprobar columna antes de usarla
        if (Schema::hasColumn('actas', 'hora_inicio_registro')) {
            $insertData['hora_inicio_registro'] = $horaActual->toDateTimeString();
        }

        // Ubicación / lugar_intervencion
        if ($lugar !== null) {
            if (Schema::hasColumn('actas', 'lugar_intervencion')) {
                $insertData['lugar_intervencion'] = $lugar;
            } elseif (Schema::hasColumn('actas', 'ubicacion')) {
                $insertData['ubicacion'] = $lugar;
            }
        }

        // Descripción (descripcion / descripcion_hechos)
        if ($descripcionVal !== null) {
            if (Schema::hasColumn('actas', 'descripcion_hechos')) {
                $insertData['descripcion_hechos'] = $descripcionVal;
            } elseif (Schema::hasColumn('actas', 'descripcion')) {
                $insertData['descripcion'] = $descripcionVal;
            }
        }

        // Otros campos del formulario: tipo_servicio, tipo_agente, codigo_infraccion, gravedad, placa
        if ($request->filled('tipo_servicio') && Schema::hasColumn('actas', 'tipo_servicio')) {
            $insertData['tipo_servicio'] = $request->tipo_servicio;
        }
        if ($request->filled('tipo_agente') && Schema::hasColumn('actas', 'tipo_agente')) {
            $insertData['tipo_agente'] = $request->tipo_agente;
        }
        if ($request->filled('codigo_infraccion') && Schema::hasColumn('actas', 'codigo_infraccion')) {
            $insertData['codigo_infraccion'] = $request->codigo_infraccion;
        }
        if ($request->filled('gravedad') && Schema::hasColumn('actas', 'gravedad')) {
            $insertData['gravedad'] = $request->gravedad;
        }
        // placa puede venir en 'placa' o 'placa_vehiculo'
        if ($request->filled('placa') && Schema::hasColumn('actas', 'placa')) {
            $insertData['placa'] = strtoupper($request->placa);
        }
        if ($request->filled('placa_vehiculo') && Schema::hasColumn('actas', 'placa_vehiculo')) {
            $insertData['placa_vehiculo'] = strtoupper($request->placa_vehiculo);
        }

        // No forzar un estado no declarado en la migración; si la columna existe dejar que el default se aplique
        if (Schema::hasColumn('actas', 'estado') && in_array('pendiente', ['pendiente'])) {
            // omitimos para usar el valor por defecto de la migración
        }

        // Filtrar el payload para incluir solo columnas que realmente existen en la tabla
        try {
            foreach (array_keys($insertData) as $col) {
                if (!Schema::hasColumn('actas', $col)) {
                    unset($insertData[$col]);
                }
            }
        } catch (\Exception $e) {
            // Si Schema falla (posible en DB muy inconsistente), intentar continuar con lo que se tenga
            logger()->warning('No se pudo verificar esquema antes de insert: ' . $e->getMessage());
        }

        // Asegurar que hay al menos una columna para insertar
        if (empty($insertData)) {
            logger()->error('InsertData vacío - no hay columnas válidas para insertar en actas', ['original' => $request->all()]);
            return response()->json([
                'success' => false,
                'message' => 'No se pudo crear acta: esquema de base de datos incompatible (faltan columnas).'
            ], 500);
        }

        // Insertar primero y luego generar numero_acta basado en el id (id-1) para que el primer registro sea sufijo 000000
        try {
            // Si la columna numero_acta existe y la BD requiere un valor (sin default), colocar un valor provisional
            if (Schema::hasColumn('actas', 'numero_acta') && empty($insertData['numero_acta'])) {
                try {
                    $insertData['numero_acta'] = $this->generarNumeroActaUnico();
                } catch (\Exception $e) {
                    $insertData['numero_acta'] = 'DRTC-APU-' . date('Y') . '-TEMP';
                }
            }

            // Si la columna user_id existe y no hay usuario autenticado, intentar usar el primer usuario para respetar la FK
            if (Schema::hasColumn('actas', 'user_id') && empty($insertData['user_id'])) {
                try {
                    $userId = Auth::id();
                    if (!$userId) {
                        $firstUser = DB::table('usuarios')->orderBy('id')->first();
                        $userId = $firstUser->id ?? null;
                    }
                    if ($userId) {
                        $insertData['user_id'] = $userId;
                    }
                } catch (\Exception $e) {
                    // no hacer nada si no se puede obtener user fallback
                }
            }

            $actaId = DB::table('actas')->insertGetId($insertData);
        } catch (\Exception $e) {
            logger()->error('Error al insertar acta: ' . $e->getMessage(), ['payload' => $insertData]);
            return response()->json([
                'success' => false,
                'message' => 'Error al insertar acta en la base de datos: ' . $e->getMessage()
            ], 500);
        }

        // Construir numero_acta usando el id insertado menos 1 (primer registro -> 0)
        $sufijo = max(0, $actaId - 1);
        $sufijo_padded = str_pad($sufijo, 6, '0', STR_PAD_LEFT);
        $numeroActa = 'DRTC-APU-' . date('Y') . '-' . $sufijo_padded;

        // Actualizar el registro con el numero_acta generado
        try {
            DB::table('actas')->where('id', $actaId)->update([
                'numero_acta' => $numeroActa,
                'updated_at' => $horaActual->toDateTimeString()
            ]);
        } catch (\Exception $e) {
            logger()->error('Error al actualizar numero_acta tras insert: ' . $e->getMessage(), ['id' => $actaId]);
        }

        // Guardar evidencias (archivos) si vienen en la petición y existe la tabla de evidencias
        if (Schema::hasTable('actas_evidencias')) {
            try {
                $files = $request->file('evidencias') ?: null;
                if ($files) {
                    // asegurar array
                    if (!is_array($files)) {
                        $files = [$files];
                    }

                    $tiene = false;
                    foreach ($files as $file) {
                        if ($file && $file->isValid()) {
                            // Guardar en disco público (storage/app/public/actas_evidencias)
                            $path = $file->store('actas_evidencias', 'public');
                            DB::table('actas_evidencias')->insert([
                                'acta_id' => $actaId,
                                'filename' => $file->getClientOriginalName(),
                                'path' => $path,
                                'mime' => $file->getClientMimeType(),
                                'size' => $file->getSize(),
                                'created_at' => $horaActual->toDateTimeString(),
                                'updated_at' => $horaActual->toDateTimeString(),
                            ]);
                            $tiene = true;
                        }
                    }

                    if ($tiene && Schema::hasColumn('actas', 'has_evidencias')) {
                        DB::table('actas')->where('id', $actaId)->update(['has_evidencias' => 1, 'updated_at' => $horaActual->toDateTimeString()]);
                    }
                }
            } catch (\Exception $e) {
                logger()->warning('No se pudieron guardar evidencias para acta ' . $actaId . ': ' . $e->getMessage());
            }
        }

        // Crear notificación solo si el usuario está autenticado y la tabla existe
        if (Auth::id() && Schema::hasTable('notifications')) {
            try {
                DB::table('notifications')->insert([
                    'user_id' => Auth::id(),
                    'title' => 'Nueva Acta Registrada',
                    'message' => "Se ha registrado una nueva acta de infracción #{$numeroActa} a las {$horaActual->format('H:i:s')}",
                    'type' => 'info',
                    'read' => false,
                    'created_at' => $horaActual->toDateTimeString(),
                    'updated_at' => $horaActual->toDateTimeString(),
                ]);
            } catch (\Exception $e) {
                logger()->warning('No se pudo insertar notificación: ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Acta registrada exitosamente',
            'acta_id' => $actaId,
            'numero_acta' => $numeroActa,
            'hora_registro' => $horaActual->format('d/m/Y H:i:s'),
            'sufijo_padded' => $sufijo_padded,
            'sufijo' => $sufijo
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
<<<<<<< HEAD
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
                'fecha_infraccion' => $request->fecha_intervencion ?? $horaActual->toDateString(),
                'hora_infraccion' => $request->hora_intervencion ?? $horaActual->toTimeString(),
                'hora_inicio_registro' => $horaActual->toDateTimeString(),
                'observaciones' => $request->observaciones_inspector ?? null,
                'user_id' => Auth::id() ?? 1, // Usar 1 como fallback
                'created_at' => $horaActual->toDateTimeString(),
                'updated_at' => $horaActual->toDateTimeString(),
            ];
=======
            $insertData = [];

            // Valores fijos que deberían existir en la mayoría de esquemas
            if (Schema::hasColumn('actas', 'vehiculo_id')) {
                $insertData['vehiculo_id'] = null; // No vinculado a tabla vehiculos
            }
            if (Schema::hasColumn('actas', 'conductor_id')) {
                $insertData['conductor_id'] = null; // No vinculado a tabla conductores
            }
            if (Schema::hasColumn('actas', 'infraccion_id')) {
                // No usar ID hardcodeado; dejar null para respetar FK si no hay infracciones cargadas
                $insertData['infraccion_id'] = null;
            }
            if (Schema::hasColumn('actas', 'inspector_id')) {
                // Dejar null para evitar violaciones de clave foránea si no existen inspectores
                $insertData['inspector_id'] = null;
            }

            // Guardar nombre completo y documento (RUC/DNI) enviados desde el formulario libre
            if (Schema::hasColumn('actas', 'nombre_conductor')) {
                $insertData['nombre_conductor'] = $request->nombre_conductor_1 ?? null;
            }
            if (Schema::hasColumn('actas', 'ruc_dni')) {
                $insertData['ruc_dni'] = $request->ruc_dni ?? null;
            }

            if (Schema::hasColumn('actas', 'monto_multa')) {
                $insertData['monto_multa'] = $request->monto_multa ?? 0;
            }

            // Estado: solo si la columna existe y acepta strings; si la columna es numérica (tinyint) usamos el valor por defecto
            if (Schema::hasColumn('actas', 'estado')) {
                try {
                    $db = env('DB_DATABASE');
                    $col = DB::selectOne("SELECT DATA_TYPE as dtype FROM information_schema.columns WHERE table_schema = ? AND table_name = 'actas' AND column_name = 'estado'", [$db]);
                    if ($col && in_array(strtolower($col->dtype), ['tinyint','int','smallint','bigint'])) {
                        // columna numérica: no insertar string, dejar que el default de la BD aplique
                    } else {
                        $insertData['estado'] = 'registrada';
                    }
                } catch (\Exception $e) {
                    // Si falla la comprobación, usar comportamiento seguro: no insertar estado para evitar valores inválidos
                    logger()->warning('No se pudo determinar tipo de columna estado en actas: ' . $e->getMessage());
                }
            }

            // Hora de inicio del registro: sólo si la columna existe
            if (Schema::hasColumn('actas', 'hora_inicio_registro')) {
                $insertData['hora_inicio_registro'] = $horaActual->toDateTimeString();
            }

            // Observaciones solo si la columna existe
            if (Schema::hasColumn('actas', 'observaciones')) {
                $insertData['observaciones'] = $request->observaciones_inspector ?? null;
            }

            // Guardar inspector_responsable si viene en formulario libre
            $inspectorNombreFormulario = $request->inspector ?? $request->inspector_principal ?? null;
            if ($inspectorNombreFormulario !== null && Schema::hasColumn('actas', 'inspector_responsable')) {
                $insertData['inspector_responsable'] = $inspectorNombreFormulario;
            }

            // user_id y timestamps si existen
            if (Schema::hasColumn('actas', 'user_id')) {
                // Si no hay un usuario autenticado, usar el primer usuario disponible para respetar la FK
                $userId = Auth::id();
                if (!$userId) {
                    try {
                        $firstUser = DB::table('usuarios')->orderBy('id')->first();
                        $userId = $firstUser->id ?? null;
                    } catch (\Exception $e) {
                        $userId = null;
                    }
                }
                if ($userId) {
                    $insertData['user_id'] = $userId;
                }
            }
            if (Schema::hasColumn('actas', 'created_at')) {
                $insertData['created_at'] = $horaActual->toDateTimeString();
            }
            if (Schema::hasColumn('actas', 'updated_at')) {
                $insertData['updated_at'] = $horaActual->toDateTimeString();
            }

            // Añadir numero_acta si la columna existe (es requerida en muchos esquemas)
            if (Schema::hasColumn('actas', 'numero_acta')) {
                $insertData['numero_acta'] = $numeroActa;
            }
>>>>>>> 4a8a6c7 (CAMBIOS EN ACTAS)

            // Placa: placa_vehiculo o placa
            if (Schema::hasColumn('actas', 'placa_vehiculo')) {
                $insertData['placa_vehiculo'] = $placaParaDB;
            } elseif (Schema::hasColumn('actas', 'placa')) {
                $insertData['placa'] = $placaParaDB;
            }

<<<<<<< HEAD
=======
            
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

>>>>>>> 4a8a6c7 (CAMBIOS EN ACTAS)
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

            // Insertar primero sin numero_acta y luego actualizar usando id-1 para que el primer registro tenga sufijo 000000
            // Filtrar el payload para incluir solo columnas que realmente existen en la tabla
            try {
                foreach (array_keys($insertData) as $col) {
                    if (!Schema::hasColumn('actas', $col)) {
                        unset($insertData[$col]);
                    }
                }
            } catch (\Exception $e) {
                logger()->warning('No se pudo verificar esquema antes de insert (libre): ' . $e->getMessage());
            }

            // Asegurar que hay al menos una columna para insertar
            if (empty($insertData)) {
                logger()->error('InsertData vacío - no hay columnas válidas para insertar en actas (libre)', ['original' => $request->all()]);
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudo crear acta: esquema de base de datos incompatible (faltan columnas).'
                ], 500);
            }

            $actaId = DB::table('actas')->insertGetId($insertData);

            // Construir numero_acta usando el id insertado menos 1
            $sufijo = max(0, $actaId - 1);
            $sufijo_padded = str_pad($sufijo, 6, '0', STR_PAD_LEFT);
            $numeroActa = 'DRTC-APU-' . date('Y') . '-' . $sufijo_padded;

            // Actualizar el registro con el numero_acta generado
            try {
                DB::table('actas')->where('id', $actaId)->update([
                    'numero_acta' => $numeroActa,
                    'updated_at' => $horaActual->toDateTimeString()
                ]);
            } catch (\Exception $e) {
                logger()->error('Error al actualizar numero_acta tras insert (libre): ' . $e->getMessage(), ['id' => $actaId]);
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
            ->leftJoin('vehiculos', 'actas.vehiculo_id', '=', 'vehiculos.id')
            ->leftJoin('conductores', 'actas.conductor_id', '=', 'conductores.id')
            ->leftJoin('infracciones', 'actas.infraccion_id', '=', 'infracciones.id')
            ->select(
                'actas.*',
                DB::raw("COALESCE(actas.placa_vehiculo, vehiculos.placa, actas.placa) as placa"),
                DB::raw("COALESCE(actas.nombre_conductor, conductores.nombre) as conductor_nombre"),
                DB::raw("COALESCE(infracciones.descripcion, actas.descripcion, actas.descripcion_hechos) as infraccion_descripcion")
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

            // Filtrar por RUC/DNI (busca en descripcion si existe)
            if ($request->ruc_dni) {
                // Si la columna descripcion existe, usarla; si no, buscar en ruc_dni
                if (Schema::hasColumn('actas', 'descripcion')) {
                    $query->where('descripcion', 'LIKE', '%' . $request->ruc_dni . '%');
                } else {
                    $query->where('ruc_dni', 'LIKE', '%' . $request->ruc_dni . '%');
                }
            }

            // Filtrar por placa
            if ($request->placa) {
                if (Schema::hasColumn('actas', 'placa_vehiculo')) {
                    $query->where('placa_vehiculo', 'LIKE', '%' . strtoupper($request->placa) . '%');
                } else {
                    $query->where('placa', 'LIKE', '%' . strtoupper($request->placa) . '%');
                }
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
                Schema::hasColumn('actas', 'descripcion') ? 'descripcion' : 'descripcion_hechos',
                'created_at'
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

            // Formatear resultados
            $actasFormatadas = $actas->getCollection()->map(function($acta) {
                $descripcion = $acta->descripcion ?? ($acta->descripcion_hechos ?? '');

                // Extraer datos
                $empresa = '';
                $conductor = '';
                if (preg_match('/Empresa\/Operador:\s*(.+?)[\n\r]/', $descripcion, $matches)) {
                    $empresa = trim($matches[1]);
                }
                if (preg_match('/Nombre:\s*(.+?)[\n\r]/', $descripcion, $matches)) {
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
            // Seguir la regla: sufijo = id - 1. Obtener el próximo id auto-incremental y calcular sufijo = next_id - 1
            $dbName = env('DB_DATABASE');
            $tbl = DB::selectOne("SELECT AUTO_INCREMENT as next_id FROM information_schema.TABLES WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'actas'", [$dbName]);
            $nextId = ($tbl && isset($tbl->next_id) && is_numeric($tbl->next_id)) ? (int)$tbl->next_id : null;
            if ($nextId === null) {
                // Fallback: usar conteo de registros
                $count = DB::table('actas')->count();
                $next = $count === 0 ? 0 : ($count + 1);
            } else {
                $sufijo = max(0, $nextId - 1);
                $next = $sufijo;
            }

            // Usar padding de 6 dígitos para consistencia con la vista
            return $prefix . str_pad($next, 6, '0', STR_PAD_LEFT);
        } catch (\Exception $e) {
            // En caso de fallo al consultar la BD, usar fallback simple
            return $prefix . str_pad(DB::table('actas')->count() + 1, 6, '0', STR_PAD_LEFT);
        }
    }

}
