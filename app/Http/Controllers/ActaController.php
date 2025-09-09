<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class ActaController extends Controller
{

    /**
     * Normaliza un valor lógico de estado ('pendiente','procesada'...) al tipo real
     * usado en la columna `actas.estado`. Si la columna es numérica, devuelve
     * el entero mapeado. Si es enum/textual, devuelve la cadena.
     */
    private function normalizeEstadoValue(string $estado)
    {
        try {
            // Si la columna no existe, devolver el valor tal cual (no hay dónde insertarlo)
            if (!Schema::hasColumn('actas', 'estado')) return $estado;

            // Intentar leer el tipo desde information_schema (más consistente que SHOW COLUMNS en algunos entornos)
            $db = env('DB_DATABASE');
            $colInfo = null;
            try {
                $colInfo = DB::selectOne("SELECT DATA_TYPE, COLUMN_TYPE FROM information_schema.columns WHERE table_schema = ? AND table_name = 'actas' AND column_name = 'estado'", [$db]);
            } catch (\Exception $e) {
                // fallback a SHOW COLUMNS si information_schema no está disponible
                try {
                    $col = DB::selectOne("SHOW COLUMNS FROM `actas` LIKE 'estado'");
                    if ($col) {
                        $colInfo = (object)[ 'DATA_TYPE' => null, 'COLUMN_TYPE' => $col->Type ?? null ];
                    }
                } catch (\Exception $e2) {
                    // no podemos determinar el tipo
                    $colInfo = null;
                }
            }

            $map = [
                'pendiente' => 0,
                'procesada' => 1,
                'anulada' => 2,
                'pagada' => 3,
            ];

            if ($colInfo) {
                $dtype = strtolower($colInfo->DATA_TYPE ?? '');
                $ctype = strtolower($colInfo->COLUMN_TYPE ?? '');

                // Si es enum devolver la cadena sólo si está declarada
                if (strpos($ctype, 'enum(') === 0) {
                    if (strpos($ctype, "'{$estado}'") !== false) return $estado;
                    return null;
                }

                // Tipos numéricos
                if (in_array($dtype, ['tinyint','smallint','mediumint','int','bigint']) || strpos($ctype, 'int') !== false) {
                    return $map[$estado] ?? null;
                }
            }

            // Si no pudimos determinar tipo, intentar devolver el mapeo si existe
            if (isset($map[$estado])) return $map[$estado];

            // Último recurso: devolver null para evitar insertar una cadena en una columna numérica desconocida
            return null;
        } catch (\Exception $e) {
            // En caso de error inesperado preferimos null para evitar errores de tipo en la BD
            return null;
        }
    }

    /**
     * Obtener el valor apropiado para marcar una fila como eliminada en la columna estado.
     */
    private function estadoValueForDeletion()
    {
        try {
            if (!Schema::hasColumn('actas', 'estado')) return null;
            $col = DB::selectOne("SHOW COLUMNS FROM `actas` LIKE 'estado'");
            $type = strtolower($col->Type ?? '');

            if ($type && (strpos($type, 'int') !== false || strpos($type, 'tinyint') !== false || strpos($type, 'smallint') !== false || strpos($type, 'bigint') !== false || strpos($type, 'mediumint') !== false)) {
                return null;
            }

            if ($type && strpos($type, 'enum(') === 0) {
                if (strpos($type, "'eliminada'") !== false) return 'eliminada';
                if (strpos($type, "'anulada'") !== false) return 'anulada';
                return null;
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }
    public function index()
    {
    // Construir expresión segura para la descripción de infracción respetando el esquema
        $infraccionParts = ['infracciones.descripcion'];
        if (Schema::hasColumn('actas', 'descripcion')) {
            $infraccionParts[] = 'actas.descripcion';
        }
        if (Schema::hasColumn('actas', 'descripcion_hechos')) {
            $infraccionParts[] = 'actas.descripcion_hechos';
        }
        // Asegurar al menos un fallback vacío
        $infraccionExpr = "COALESCE(" . implode(', ', $infraccionParts) . ", '') as infraccion_descripcion";

        $actas = DB::table('actas')
            ->leftJoin('inspectores', 'actas.inspector_id', '=', 'inspectores.id')
            ->leftJoin('vehiculos', 'actas.vehiculo_id', '=', 'vehiculos.id')
            ->leftJoin('conductores', 'actas.conductor_id', '=', 'conductores.id')
            ->leftJoin('infracciones', 'actas.infraccion_id', '=', 'infracciones.id')
            ->select(
                'actas.*',
                DB::raw("COALESCE(actas.inspector_responsable, CONCAT_WS(' ', inspectores.nombres, inspectores.apellidos)) as inspector_nombre"),
                DB::raw("COALESCE(actas.placa_vehiculo, vehiculos.placa, actas.placa) as placa"),
                DB::raw("COALESCE(actas.nombre_conductor, CONCAT_WS(' ', conductores.nombres, conductores.apellidos)) as conductor_nombre"),
                DB::raw($infraccionExpr)
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
        // Normalizar nombres comunes que el formulario puede enviar y proveer fallbacks
        try {
            // clase_categoria (form) -> clase_licencia (DB)
            if ($request->has('clase_categoria') && !$request->has('clase_licencia')) {
                $request->merge(['clase_licencia' => $request->input('clase_categoria')]);
            }

            // Inspector fallback: usar inspector_principal o usuario autenticado
            if (!$request->filled('inspector') && $request->has('inspector_principal')) {
                $request->merge(['inspector' => $request->input('inspector_principal')]);
            }
            if (!$request->filled('inspector') && Auth::check()) {
                $request->merge(['inspector' => Auth::user()->name, 'inspector_principal' => Auth::user()->name]);
            }

            // tipo_agente / clase_licencia hidden fallbacks
            if (!$request->filled('tipo_agente') && $request->has('tipo_agente_hidden')) {
                $request->merge(['tipo_agente' => $request->input('tipo_agente_hidden')]);
            }
            if (!$request->filled('clase_licencia') && $request->has('clase_licencia_hidden')) {
                $request->merge(['clase_licencia' => $request->input('clase_licencia_hidden')]);
            }

            // placa fallback
            if (!$request->filled('placa_1') && $request->filled('placa')) {
                $request->merge(['placa_1' => $request->input('placa')]);
            }
            // Aceptar placa enviada como 'placa_vehiculo' o 'placa_1' y normalizar a 'placa'
            if (!$request->filled('placa') && $request->filled('placa_vehiculo')) {
                $request->merge(['placa' => $request->input('placa_vehiculo')]);
            }
            if (!$request->filled('placa') && $request->filled('placa_1')) {
                $request->merge(['placa' => $request->input('placa_1')]);
            }
            // Normalizar ruc/dni y licencia también
            if (!$request->filled('ruc_dni') && $request->filled('dni')) {
                $request->merge(['ruc_dni' => $request->input('dni')]);
            }
            if (!$request->filled('ruc_dni') && $request->filled('dni_conductor')) {
                $request->merge(['ruc_dni' => $request->input('dni_conductor')]);
            }
            if (!$request->filled('licencia_conductor') && $request->filled('licencia_conductor_1')) {
                $request->merge(['licencia_conductor' => $request->input('licencia_conductor_1')]);
            }
        } catch (\Exception $e) {
            // no bloquear si la normalizaci f3n falla
        }

        // Si es un formulario libre (placa_1 + nombre_conductor_1) delegar
        if ($request->filled('placa_1') && $request->filled('nombre_conductor_1')) {
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
            'licencia_conductor' => $licenciaParaDB,
            'fecha_intervencion' => $horaActual->toDateString(),
            'hora_intervencion' => $horaActual->toTimeString(),
            'hora_inicio_registro' => $horaActual->toDateTimeString(), // Hora exacta del inicio
            'monto_multa' => $request->monto_multa,
            'estado' => $this->normalizeEstadoValue('pendiente'),
            'user_id' => Auth::id() ?? 1, // Usar 1 como fallback si no hay usuario autenticado
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

        if ($licenciaParaDB !== null) {
            if (Schema::hasColumn('actas', 'licencia')) {
                $insertData['licencia'] = $licenciaParaDB;
            } elseif (Schema::hasColumn('actas', 'licencia_conductor')) {
                $insertData['licencia_conductor'] = $licenciaParaDB;
            }
        }

        // Inspector responsable: preferir el valor enviado, si no usar el usuario autenticado
        $inspectorNombreFormulario = $request->input('inspector', $request->input('inspector_principal', Auth::check() ? Auth::user()->name : null));
        if ($inspectorNombreFormulario !== null && Schema::hasColumn('actas', 'inspector_responsable')) {
            $insertData['inspector_responsable'] = $inspectorNombreFormulario;
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
        // Descripción (descripcion / descripcion_hechos)
        if ($descripcionVal !== null) {
            if (Schema::hasColumn('actas', 'descripcion_hechos')) {
                $insertData['descripcion_hechos'] = $descripcionVal;
            } elseif (Schema::hasColumn('actas', 'descripcion')) {
                $insertData['descripcion'] = $descripcionVal;
            }
        }

        // Otros campos del formulario: tipo_servicio, tipo_agente, codigo_infraccion, gravedad, placa
        // Guardar campos aunque el cliente los envíe como cadenas vacías (usar has() para incluirlos)
        if ($request->has('tipo_servicio') && Schema::hasColumn('actas', 'tipo_servicio')) {
            $insertData['tipo_servicio'] = $request->input('tipo_servicio', '');
        }
        if ($request->has('tipo_agente') && Schema::hasColumn('actas', 'tipo_agente')) {
            $insertData['tipo_agente'] = $request->input('tipo_agente', '');
        }
        if ($request->has('codigo_infraccion') && Schema::hasColumn('actas', 'codigo_infraccion')) {
            $insertData['codigo_infraccion'] = $request->input('codigo_infraccion', '');
        }
        if ($request->has('gravedad') && Schema::hasColumn('actas', 'gravedad')) {
            $insertData['gravedad'] = $request->input('gravedad', '');
        }
        // placa puede venir en 'placa' o 'placa_vehiculo' - usar has() para aceptar valores enviados
        if ($request->has('placa') && Schema::hasColumn('actas', 'placa')) {
            $insertData['placa'] = strtoupper($request->input('placa', ''));
        }
        if ($request->has('placa_vehiculo') && Schema::hasColumn('actas', 'placa_vehiculo')) {
            $insertData['placa_vehiculo'] = strtoupper($request->input('placa_vehiculo', ''));
        }

        // razon_social si viene en el formulario
        if ($request->has('razon_social') && Schema::hasColumn('actas', 'razon_social')) {
            $insertData['razon_social'] = $request->input('razon_social', '');
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
            // No establecer numero_acta antes del INSERT para evitar violaciones de UNIQUE.
            // Se generará y actualizará inmediatamente tras obtener el id insertado.

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

                // Si la columna numero_acta existe y es NOT NULL sin default, establecer un valor temporal único
                if (Schema::hasColumn('actas', 'numero_acta') && empty($insertData['numero_acta'])) {
                    try {
                        $col = DB::selectOne("SELECT IS_NULLABLE, COLUMN_DEFAULT FROM information_schema.columns WHERE table_schema = ? AND table_name = 'actas' AND column_name = 'numero_acta'", [env('DB_DATABASE')]);
                        $isNullable = ($col && isset($col->IS_NULLABLE) && strtoupper($col->IS_NULLABLE) === 'YES');
                        $hasDefault = ($col && isset($col->COLUMN_DEFAULT) && $col->COLUMN_DEFAULT !== null);
                    } catch (\Exception $e) {
                        $isNullable = true;
                        $hasDefault = false;
                    }

                    if (!$isNullable && !$hasDefault) {
                        try {
                            $insertData['numero_acta'] = 'TEMP-' . date('YmdHis') . '-' . bin2hex(random_bytes(4)) . '-' . uniqid();
                        } catch (\Exception $e) {
                            $insertData['numero_acta'] = 'TEMP-' . microtime(true) . '-' . mt_rand(1000,9999);
                        }
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

    // Construir numero_acta usando el id insertado (primer registro -> 1)
    $sufijo = max(1, (int)$actaId);
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
            // Lugar enviado en formulario
            $lugar = $request->lugar_intervencion ?? null;

            // Preparar insert adaptable al esquema de la tabla (consolidado)
            $insertData = [];

            // Campos básicos y condicionales
            if (Schema::hasColumn('actas', 'nombre_conductor')) {
                $insertData['nombre_conductor'] = $request->nombre_conductor_1 ?? null;
            }
            if (Schema::hasColumn('actas', 'ruc_dni')) {
                $insertData['ruc_dni'] = $request->ruc_dni ?? null;
            }
            if (Schema::hasColumn('actas', 'monto_multa')) {
                $insertData['monto_multa'] = $request->monto_multa ?? 0;
            }

            // Campos de relación opcionales
            if (Schema::hasColumn('actas', 'vehiculo_id')) { $insertData['vehiculo_id'] = null; }
            if (Schema::hasColumn('actas', 'conductor_id')) { $insertData['conductor_id'] = null; }
            if (Schema::hasColumn('actas', 'infraccion_id')) { $insertData['infraccion_id'] = null; }
            if (Schema::hasColumn('actas', 'inspector_id')) { $insertData['inspector_id'] = null; }

            // Estado si se puede insertar string
            if (Schema::hasColumn('actas', 'estado')) {
                try {
                    $col = DB::selectOne("SELECT DATA_TYPE as dtype FROM information_schema.columns WHERE table_schema = ? AND table_name = 'actas' AND column_name = 'estado'", [env('DB_DATABASE')]);
                    if (!($col && in_array(strtolower($col->dtype), ['tinyint','int','smallint','bigint']))) {
                        $insertData['estado'] = $this->normalizeEstadoValue('pendiente');
                    }
                } catch (\Exception $e) {
                    logger()->warning('No se pudo determinar tipo de columna estado en actas (libre): ' . $e->getMessage());
                }
            }

            // Timestamps, user y numero_acta
            if (Schema::hasColumn('actas', 'user_id')) {
                $userId = Auth::id();
                if (!$userId) { try { $firstUser = DB::table('usuarios')->orderBy('id')->first(); $userId = $firstUser->id ?? null; } catch (\Exception $e) { $userId = null; } }
                if ($userId) { $insertData['user_id'] = $userId; }
            }
            if (Schema::hasColumn('actas', 'created_at')) { $insertData['created_at'] = $horaActual->toDateTimeString(); }
            if (Schema::hasColumn('actas', 'updated_at')) { $insertData['updated_at'] = $horaActual->toDateTimeString(); }
            if (Schema::hasColumn('actas', 'hora_inicio_registro')) { $insertData['hora_inicio_registro'] = $horaActual->toDateTimeString(); }
            // No establecer numero_acta antes del INSERT para evitar violaciones de UNIQUE.

            // Placa: poblar ambas columnas si existen (placa_vehiculo y placa)
            try {
                $pvExists = Schema::hasColumn('actas', 'placa_vehiculo');
                $pExists = Schema::hasColumn('actas', 'placa');
                if ($pvExists && $pExists) {
                    $insertData['placa_vehiculo'] = $placaParaDB;
                    $insertData['placa'] = $placaParaDB;
                } elseif ($pvExists) {
                    $insertData['placa_vehiculo'] = $placaParaDB;
                } elseif ($pExists) {
                    $insertData['placa'] = $placaParaDB;
                }
            } catch (\Exception $e) {
                // fallback
                if (Schema::hasColumn('actas', 'placa_vehiculo')) { $insertData['placa_vehiculo'] = $placaParaDB; }
                elseif (Schema::hasColumn('actas', 'placa')) { $insertData['placa'] = $placaParaDB; }
            }

            // Licencia: tomar de licencia_conductor_1 o licencia_conductor y poblar la columna existente
            $licPara = $request->input('licencia_conductor_1', $request->input('licencia_conductor', null));
            if ($licPara !== null) {
                if (Schema::hasColumn('actas', 'licencia_conductor')) {
                    $insertData['licencia_conductor'] = $licPara;
                } elseif (Schema::hasColumn('actas', 'licencia')) {
                    $insertData['licencia'] = $licPara;
                }
            }

            // Guardar inspector responsable si viene en el request
            $inspectorNombre = $request->input('inspector', $request->input('inspector_principal', Auth::check() ? Auth::user()->name : null));
            if ($inspectorNombre !== null && Schema::hasColumn('actas', 'inspector_responsable')) {
                $insertData['inspector_responsable'] = $inspectorNombre;
            }

            // Tipo de servicio / tipo_agente / razon_social / ruc_dni si vienen
            if ($request->has('tipo_servicio') && Schema::hasColumn('actas', 'tipo_servicio')) {
                $insertData['tipo_servicio'] = $request->input('tipo_servicio', '');
            }
            if ($request->has('tipo_agente') && Schema::hasColumn('actas', 'tipo_agente')) {
                $insertData['tipo_agente'] = $request->input('tipo_agente', '');
            }
            if ($request->has('razon_social') && Schema::hasColumn('actas', 'razon_social')) {
                $insertData['razon_social'] = $request->input('razon_social', '');
            }
            if ($request->has('ruc_dni') && Schema::hasColumn('actas', 'ruc_dni')) {
                $insertData['ruc_dni'] = $request->input('ruc_dni', '');
            }

            // Fecha/hora
            $fechaVal = $request->fecha_intervencion ?? $horaActual->toDateString();
            $horaVal = $request->hora_intervencion ?? $horaActual->toTimeString();
            if (Schema::hasColumn('actas', 'fecha_intervencion')) { $insertData['fecha_intervencion'] = $fechaVal; }
            elseif (Schema::hasColumn('actas', 'fecha_infraccion')) { $insertData['fecha_infraccion'] = $fechaVal; }
            if (Schema::hasColumn('actas', 'hora_intervencion')) { $insertData['hora_intervencion'] = $horaVal; }
            elseif (Schema::hasColumn('actas', 'hora_infraccion')) { $insertData['hora_infraccion'] = $horaVal; }

            // Lugar / descripcion
            if ($lugar !== null) { if (Schema::hasColumn('actas', 'lugar_intervencion')) { $insertData['lugar_intervencion'] = $lugar; } else { $insertData['ubicacion'] = $lugar; } }
            if (Schema::hasColumn('actas', 'descripcion_hechos')) { $insertData['descripcion_hechos'] = $descripcionCompleta; } else { $insertData['descripcion'] = $descripcionCompleta; }

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

            // Si la columna numero_acta existe y es NOT NULL sin default, establecer un valor temporal único
            if (Schema::hasColumn('actas', 'numero_acta') && empty($insertData['numero_acta'])) {
                try {
                    $col = DB::selectOne("SELECT IS_NULLABLE, COLUMN_DEFAULT FROM information_schema.columns WHERE table_schema = ? AND table_name = 'actas' AND column_name = 'numero_acta'", [env('DB_DATABASE')]);
                    $isNullable = ($col && isset($col->IS_NULLABLE) && strtoupper($col->IS_NULLABLE) === 'YES');
                    $hasDefault = ($col && isset($col->COLUMN_DEFAULT) && $col->COLUMN_DEFAULT !== null);
                } catch (\Exception $e) {
                    $isNullable = true;
                    $hasDefault = false;
                }

                if (!$isNullable && !$hasDefault) {
                    try {
                        $insertData['numero_acta'] = 'TEMP-' . date('YmdHis') . '-' . bin2hex(random_bytes(4)) . '-' . uniqid();
                    } catch (\Exception $e) {
                        $insertData['numero_acta'] = 'TEMP-' . microtime(true) . '-' . mt_rand(1000,9999);
                    }
                }
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

            // Construir numero_acta usando el id insertado (primer registro -> 1)
            $sufijo = max(1, (int)$actaId);
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
            'ubicacion' => $request->lugar_intervencion,
            'sufijo_padded' => (function($num){ $parts = explode('-', $num); return end($parts); })($numeroActa),
            'sufijo' => (function($num){ $parts = explode('-', $num); return (int)end($parts); })($numeroActa)
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
                'estado' => $this->normalizeEstadoValue('procesada'),
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
        // Usar LEFT JOINs y COALESCE para ser tolerante a esquemas incompletos
        // Construir select dinámico y evitar referencias a columnas que puedan no existir
        $select = [
            'actas.*',
            DB::raw("COALESCE(actas.inspector_responsable, CONCAT_WS(' ', inspectores.nombres, inspectores.apellidos), '') as inspector_nombre"),
            DB::raw("COALESCE(actas.placa_vehiculo, vehiculos.placa, actas.placa, '') as placa"),
            DB::raw("COALESCE(vehiculos.modelo, '') as modelo"),
            DB::raw("COALESCE(actas.nombre_conductor, CONCAT_WS(' ', conductores.nombres, conductores.apellidos), '') as conductor_nombre"),
            DB::raw("COALESCE(conductores.dni, actas.ruc_dni, '') as conductor_dni"),
        ];

        // Incluir columna de licencia solo si existe la columna 'licencia' en la tabla actas
        $licenciaExpr = "COALESCE(conductores.numero_licencia, actas.licencia_conductor";
        if (Schema::hasColumn('actas', 'licencia')) {
            $licenciaExpr .= ", actas.licencia";
        }
        $licenciaExpr .= ", '') as licencia";
        $select[] = DB::raw($licenciaExpr);

        // Selección segura para descripción de infracción
        $infraccionParts = ['infracciones.descripcion'];
        if (Schema::hasColumn('actas', 'descripcion')) {
            $infraccionParts[] = 'actas.descripcion';
        }
        if (Schema::hasColumn('actas', 'descripcion_hechos')) {
            $infraccionParts[] = 'actas.descripcion_hechos';
        }
        $select[] = DB::raw("COALESCE(" . implode(', ', $infraccionParts) . ", '') as infraccion_descripcion");
        $select[] = DB::raw("COALESCE(infracciones.codigo, '') as infraccion_codigo");
        $select[] = DB::raw("COALESCE(empresas.razon_social, '') as empresa_nombre");

        $acta = DB::table('actas')
            ->leftJoin('inspectores', 'actas.inspector_id', '=', 'inspectores.id')
            ->leftJoin('vehiculos', 'actas.vehiculo_id', '=', 'vehiculos.id')
            ->leftJoin('conductores', 'actas.conductor_id', '=', 'conductores.id')
            ->leftJoin('infracciones', 'actas.infraccion_id', '=', 'infracciones.id')
            ->leftJoin('empresas', 'vehiculos.empresa_id', '=', 'empresas.id')
            ->select($select)
            ->where('actas.id', $id)
            ->first();

        if (!$acta) {
            return response()->json(['success' => false, 'message' => 'Acta no encontrada'], 404);
        }

        return response()->json(['success' => true, 'acta' => $acta]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,procesada,anulada'
        ]);

        // Normalizar el valor de estado según el tipo real de la columna
        $normalized = $this->normalizeEstadoValue($request->estado);

        DB::table('actas')
            ->where('id', $id)
            ->update([
                'estado' => $normalized,
                'updated_at' => now()
            ]);

        // Buscar el acta
    $acta = DB::table('actas')->where('id', $id)->first();
        if (!$acta) {
            logger()->warning('Eliminaci\u00f3n fallida - acta no encontrada', ['acta_id' => $id, 'user_id' => Auth::id()]);
            return response()->json(['success' => false, 'message' => 'Acta no encontrada'], 404);
        }

        // Obtener datos de la petición (el frontend envía motivo/observaciones/supervisor)
        $motivo = $request->input('motivo') ?? $request->input('motivo_eliminacion') ?? null;
        $observaciones = $request->input('observaciones') ?? null;
        $supervisor = $request->input('supervisor') ?? null;

        if (empty($motivo) || empty($supervisor)) {
            return response()->json(['success' => false, 'message' => 'Motivo y supervisor son requeridos para la eliminación'], 400);
        }

        try {
            $hora = Carbon::now()->toDateTimeString();

            // Si existe una tabla de auditoría 'actas_eliminadas', copiar la fila original para trazabilidad
            try {
                if (Schema::hasTable('actas_eliminadas')) {
                    $original = (array) $acta;
                    // añadir metadatos de eliminación
                    $original['motivo_eliminacion'] = $motivo;
                    $original['observaciones_eliminacion'] = $observaciones;
                    $original['supervisor_eliminante'] = $supervisor;
                    $original['deleted_at'] = $hora;
                    $original['deleted_by'] = Auth::id() ?? null;
                    // Insertar copia
                    DB::table('actas_eliminadas')->insert($original);
                }
            } catch (\Exception $e) {
                logger()->warning('No se pudo insertar registro en actas_eliminadas: ' . $e->getMessage());
            }

            // Preparar actualización que deja sólo la nota de eliminación
            $update = [
                'updated_at' => $hora
            ];

            // Poner nota de eliminación en la columna de descripción disponible
            $nota = "REGISTRO MARCADO COMO ELIMINADO el {$hora} por: " . ($supervisor ?: (Auth::check() ? Auth::user()->name : 'Sistema')) . ". Motivo: {$motivo}. Observaciones: " . ($observaciones ?: 'N/A');

            if (Schema::hasColumn('actas', 'motivo_eliminacion')) {
                $update['motivo_eliminacion'] = $motivo;
            }
            if (Schema::hasColumn('actas', 'observaciones_eliminacion')) {
                $update['observaciones_eliminacion'] = $observaciones;
            }
            if (Schema::hasColumn('actas', 'supervisor_eliminante')) {
                $update['supervisor_eliminante'] = $supervisor;
            }

            // Escribir la nota en la columna de descripcion disponible
            if (Schema::hasColumn('actas', 'descripcion_hechos')) {
                $update['descripcion_hechos'] = $nota;
            } elseif (Schema::hasColumn('actas', 'descripcion')) {
                $update['descripcion'] = $nota;
            } else {
                // Si no hay columna de descripcion, intentar usar razon_social como último recurso
                if (Schema::hasColumn('actas', 'razon_social')) {
                    $update['razon_social'] = $nota;
                }
            }

            // Limpiar campos identificables para que sólo quede la razón de eliminación
            $colsToClear = [
                'numero_acta','nombre_conductor','ruc_dni','placa','placa_vehiculo','monto_multa',
                'licencia','licencia_conductor','razon_social','fecha_infraccion','hora_infraccion',
                'fecha_intervencion','hora_intervencion','ubicacion','lugar_intervencion','inspector_responsable'
            ];
            foreach ($colsToClear as $c) {
                if (Schema::hasColumn('actas', $c)) {
                    // usar NULL para campos que puedan ser null y cadena vacía para otros según tipo
                    $update[$c] = null;
                }
            }

            // Marcar estado si existe la columna 'estado'
            if (Schema::hasColumn('actas', 'estado')) {
                $val = $this->estadoValueForDeletion();
                if ($val !== null) $update['estado'] = $val;
            }

            // Actualizar la fila (eliminación lógica / limpieza)
            DB::table('actas')->where('id', $id)->update($update);

            logger()->info('Acta marcada como eliminada (eliminación lógica)', ['acta_id' => $id, 'user_id' => Auth::id(), 'motivo' => $motivo]);

            return response()->json(['success' => true, 'message' => 'Acta eliminada (registro conservado con motivo)']);

        } catch (\Exception $e) {
            logger()->error('Error al procesar eliminación lógica de acta: ' . $e->getMessage(), ['acta_id' => $id, 'user_id' => Auth::id()]);
            return response()->json(['success' => false, 'message' => 'Error procesando la eliminación: ' . $e->getMessage()], 500);
        }

        // Ordenar por la mejor columna de fecha disponible para evitar errores de columna inexistente
        if (Schema::hasColumn('actas', 'fecha_infraccion')) {
            $pendientes = $pendientes->orderBy('actas.fecha_infraccion', 'desc')->get();
        } elseif (Schema::hasColumn('actas', 'fecha_intervencion')) {
            $pendientes = $pendientes->orderBy('actas.fecha_intervencion', 'desc')->get();
        } else {
            $pendientes = $pendientes->orderBy('actas.created_at', 'desc')->get();
        }

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

            // Determinar qué columna de descripción está disponible
            $descripcionCol = null;
            if (Schema::hasColumn('actas', 'descripcion')) {
                $descripcionCol = 'descripcion';
            } elseif (Schema::hasColumn('actas', 'descripcion_hechos')) {
                $descripcionCol = 'descripcion_hechos';
            }

            // Preparar lista de columnas a seleccionar evitando columnas inexistentes
            $select = [
                'id',
                'numero_acta',
                'placa_vehiculo',
                'monto_multa',
                'estado',
                'created_at'
            ];

            // Fecha/hora con fallback
            if (Schema::hasColumn('actas', 'fecha_infraccion')) {
                $select[] = 'fecha_infraccion';
            } else {
                $select[] = DB::raw("'' as fecha_infraccion");
            }
            if (Schema::hasColumn('actas', 'hora_infraccion')) {
                $select[] = 'hora_infraccion';
            } else {
                $select[] = DB::raw("'' as hora_infraccion");
            }

            // Ubicación / lugar_intervencion fallback
            if (Schema::hasColumn('actas', 'ubicacion')) {
                $select[] = 'ubicacion';
            } elseif (Schema::hasColumn('actas', 'lugar_intervencion')) {
                $select[] = DB::raw('lugar_intervencion as ubicacion');
            } else {
                $select[] = DB::raw("'' as ubicacion");
            }

            // Incluir la columna de descripción (alias a 'descripcion' para simplificar el mapeo)
            if ($descripcionCol === 'descripcion_hechos') {
                $select[] = DB::raw('descripcion_hechos as descripcion');
            } elseif ($descripcionCol === 'descripcion') {
                $select[] = 'descripcion';
            } else {
                $select[] = DB::raw("'' as descripcion");
            }

            // Construir la consulta condicionalmente según las columnas disponibles
            $query = DB::table('actas');

            if ($descripcionCol) {
                // Buscar dentro de la columna de descripción disponible
                $query->where(function($q) use ($descripcionCol, $documento) {
                    $q->where($descripcionCol, 'LIKE', "%RUC/DNI: {$documento}%")
                      ->orWhere($descripcionCol, 'LIKE', "%DNI: {$documento}%")
                      ->orWhere($descripcionCol, 'LIKE', "%RUC: {$documento}%")
                      ->orWhere($descripcionCol, 'LIKE', "%{$documento}%");
                });
            } elseif (Schema::hasColumn('actas', 'ruc_dni')) {
                // Buscar en la columna ruc_dni si existe
                $query->where('ruc_dni', 'LIKE', "%{$documento}%");
            } elseif (Schema::hasColumn('actas', 'razon_social')) {
                // Buscar en razon_social como último recurso
                $query->where('razon_social', 'LIKE', "%{$documento}%");
            } else {
                // No hay columna conveniente para buscar por documento: devolver vacio seguro
                return response()->json([
                    'success' => true,
                    'total' => 0,
                    'documento' => $documento,
                    'actas' => [],
                    'debug' => ['reason' => 'no_description_or_ruc_column']
                ]);
            }

            $actas = $query->select($select)
                ->orderBy('created_at', 'desc')
                ->get();

            // Si no encuentra nada y existe la columna de descripción, intentar una búsqueda sin espacios/retornos
            if ($actas->isEmpty() && $descripcionCol) {
                $col = $descripcionCol;
                $actas = DB::table('actas')
                    ->whereRaw("REPLACE(REPLACE({$col}, ' ', ''), '\n', '') LIKE ?", ["%{$documento}%"]) 
                    ->select($select)
                    ->orderBy('created_at', 'desc')
                    ->get();
            }

            // Formatear resultados
            $actasFormatadas = $actas->map(function($acta) {
                $descripcion = $acta->descripcion ?? '';

                // Extraer empresa/operador
                $empresa = '';
                if ($descripcion && preg_match('/Empresa\/Operador:\s*(.+?)[\n\r]/', $descripcion, $matches)) {
                    $empresa = trim($matches[1]);
                }

                // Extraer conductor
                $conductor = '';
                if ($descripcion && preg_match('/Nombre:\s*(.+?)[\n\r]/', $descripcion, $matches)) {
                    $conductor = trim($matches[1]);
                }

                // Extraer licencia
                $licencia = '';
                if ($descripcion && preg_match('/Licencia:\s*(.+?)[\n\r]/', $descripcion, $matches)) {
                    $licencia = trim($matches[1]);
                }

                // Extraer origen y destino
                $origen = '';
                $destino = '';
                if ($descripcion && preg_match('/Origen:\s*(.+?)[\n\r]/', $descripcion, $matches)) {
                    $origen = trim($matches[1]);
                }
                if ($descripcion && preg_match('/Destino:\s*(.+?)[\n\r]/', $descripcion, $matches)) {
                    $destino = trim($matches[1]);
                }

                // Extraer RUC/DNI
                $documento_extraido = '';
                if ($descripcion && preg_match('/RUC\/DNI:\s*(\d+)/', $descripcion, $matches)) {
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

            // Filtrar por estado: normalizar para evitar comparar con strings en columnas numéricas
            if ($request->estado) {
                $normalized = $this->normalizeEstadoValue($request->estado);
                if ($normalized === null) {
                    // Si no podemos normalizar, devolver sin resultados para evitar errores
                    return response()->json(['success' => true, 'total' => 0, 'current_page' => 1, 'last_page' => 1, 'actas' => []]);
                }
                $query->where('estado', $normalized);
            }

            // Filtrar por fecha desde/hasta: usar la columna de fecha disponible para evitar errores
            if ($request->fecha_desde) {
                if (Schema::hasColumn('actas', 'fecha_infraccion')) {
                    $query->where('fecha_infraccion', '>=', $request->fecha_desde);
                } elseif (Schema::hasColumn('actas', 'fecha_intervencion')) {
                    $query->where('fecha_intervencion', '>=', $request->fecha_desde);
                }
            }

            if ($request->fecha_hasta) {
                if (Schema::hasColumn('actas', 'fecha_infraccion')) {
                    $query->where('fecha_infraccion', '<=', $request->fecha_hasta);
                } elseif (Schema::hasColumn('actas', 'fecha_intervencion')) {
                    $query->where('fecha_intervencion', '<=', $request->fecha_hasta);
                }
            }

            // Construir select dinámico para consultarActas

            $select = [
                'id',
                'numero_acta',
                'placa_vehiculo',
                'monto_multa',
                'estado',
                'created_at'
            ];

            // Fecha/hora
            if (Schema::hasColumn('actas', 'fecha_infraccion')) {
                $select[] = 'fecha_infraccion';
            } else {
                $select[] = DB::raw("'' as fecha_infraccion");
            }
            if (Schema::hasColumn('actas', 'hora_infraccion')) {
                $select[] = 'hora_infraccion';
            } else {
                $select[] = DB::raw("'' as hora_infraccion");
            }

            if (Schema::hasColumn('actas', 'ubicacion')) {
                $select[] = 'ubicacion';
            } elseif (Schema::hasColumn('actas', 'lugar_intervencion')) {
                $select[] = DB::raw('lugar_intervencion as ubicacion');
            } else {
                $select[] = DB::raw("'' as ubicacion");
            }

            if (Schema::hasColumn('actas', 'descripcion')) {
                $select[] = 'descripcion';
            } elseif (Schema::hasColumn('actas', 'descripcion_hechos')) {
                $select[] = DB::raw('descripcion_hechos as descripcion');
            } else {
                $select[] = DB::raw("'' as descripcion");
            }

            $actas = $query->select($select)
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
                // Fallback: si no se puede leer AUTO_INCREMENT usar MAX del sufijo para el año
                try {
                    $res = DB::selectOne("SELECT MAX(CAST(SUBSTRING_INDEX(numero_acta, '-', -1) AS UNSIGNED)) as max_suf FROM `actas` WHERE numero_acta LIKE ?", [$prefix . '%']);
                    $max = ($res && isset($res->max_suf) && is_numeric($res->max_suf)) ? (int)$res->max_suf : null;
                    $next = ($max === null) ? 0 : ($max + 1);
                } catch (\Exception $e) {
                    $count = DB::table('actas')->count();
                    $next = $count === 0 ? 0 : ($count + 1);
                }
            } else {
                // Usar regla sufijo = next_id - 1
                $sufijo = max(0, $nextId - 1);
                $next = $sufijo;
            }

            // Usar padding de 6 dígitos para consistencia con la vista
            return $prefix . str_pad($next, 6, '0', STR_PAD_LEFT);
        } catch (\Exception $e) {
            $count = DB::table('actas')->count();
            $next = $count === 0 ? 0 : ($count + 1);
            return $prefix . str_pad($next, 6, '0', STR_PAD_LEFT);
        }
    }

    public function buscar(Request $request)
    {
        $criterio = $request->query('criterio');
        if (!$criterio) {
            return response()->json(['success' => false, 'message' => 'Criterio vacio'], 400);
        }
        // Buscar por varias columnas relevantes
        $like = "%{$criterio}%";
        $acta = \DB::table('actas')
            ->where(function($q) use ($criterio, $like) {
                $q->where('numero_acta', 'LIKE', $like)
                  ->orWhere('placa', 'LIKE', $like)
                  ->orWhere('placa_vehiculo', 'LIKE', $like)
                  ->orWhere('ruc_dni', 'LIKE', $like);
            })
            ->first();

        if (!$acta) {
            return response()->json(['success' => false, 'message' => 'Acta no encontrada', 400]);
        }
        return response()->json(['success' => true, 'acta' =>[
            'id' => $acta->id,
            'numero_acta' => $acta->numero_acta,
            'lugar_intervencion' => $acta->lugar_intervencion,
            'fecha_intervencion' => $acta->fecha_intervencion,
            'hora_intervencion' => $acta->hora_intervencion,
            'inspector_responsable' => $acta->inspector_responsable,
            'tipo_servicio' => $acta->tipo_servicio,
            'tipo_agente' => $acta->tipo_agente,
            'placa' => $acta->placa,
            'placa_vehiculo' => $acta->placa_vehiculo,
            'razon_social' => $acta->razon_social,
            'ruc_dni' => $acta->ruc_dni,
            'licencia_conductor' => $acta->licencia_conductor,
            'nombre_conductor' => $acta->nombre_conductor,
            'clase_licencia' => $acta->clase_licencia,
            'monto_multa' => $acta->monto_multa,
            'estado' => $acta->estado,
            'user_id' => $acta->user_id,
            'has_evidencias' => $acta->has_evidencias,
            'created_at' => $acta->created_at,
            'updated_at' => $acta->updated_at,

        ]]);
    }

    public function destroy(Request $request, $id)
    {
        // Registrar intento de eliminación para facilitar diagnóstico
        try {
            logger()->info('Intento de eliminación de acta', [
                'acta_id' => $id,
                'user_id' => Auth::id(),
                'payload' => $request->all()
            ]);
        } catch (\Exception $e) {
            // no bloquear si falla el logging
        }

        $acta = DB::table('actas')->where('id', $id)->first();
        if (!$acta) {
            logger()->warning('Eliminación fallida - acta no encontrada', ['acta_id' => $id, 'user_id' => Auth::id()]);
            return response()->json(['success' => false, 'message' => 'Acta no encontrada'], 404);
        }

        // Validar motivo y supervisor (vienen del frontend)
        $motivo = $request->input('motivo') ?? $request->input('motivo_eliminacion') ?? null;
        $observaciones = $request->input('observaciones') ?? null;
        $supervisor = $request->input('supervisor') ?? null;

        if (empty($motivo) || empty($supervisor)) {
            return response()->json(['success' => false, 'message' => 'Motivo y supervisor son requeridos para la eliminación'], 400);
        }

        $hora = Carbon::now()->toDateTimeString();

        try {
            DB::transaction(function() use ($acta, $id, $motivo, $observaciones, $supervisor, $hora) {
                // Asegurar existencia de tabla actas_eliminadas; si falta, crearla como copia de actas
                try {
                    if (!Schema::hasTable('actas_eliminadas')) {
                        DB::statement('CREATE TABLE `actas_eliminadas` LIKE `actas`');
                    }
                } catch (\Exception $e) {
                    logger()->warning('No se pudo crear tabla actas_eliminadas: ' . $e->getMessage());
                }

                // Asegurar columnas de metadatos en actas_eliminadas
                try {
                    if (Schema::hasTable('actas_eliminadas')) {
                        if (!Schema::hasColumn('actas_eliminadas', 'motivo_eliminacion')) {
                            DB::statement("ALTER TABLE `actas_eliminadas` ADD COLUMN motivo_eliminacion TEXT NULL");
                        }
                        if (!Schema::hasColumn('actas_eliminadas', 'observaciones_eliminacion')) {
                            DB::statement("ALTER TABLE `actas_eliminadas` ADD COLUMN observaciones_eliminacion TEXT NULL");
                        }
                        if (!Schema::hasColumn('actas_eliminadas', 'supervisor_eliminante')) {
                            DB::statement("ALTER TABLE `actas_eliminadas` ADD COLUMN supervisor_eliminante VARCHAR(255) NULL");
                        }
                        if (!Schema::hasColumn('actas_eliminadas', 'deleted_at')) {
                            DB::statement("ALTER TABLE `actas_eliminadas` ADD COLUMN deleted_at DATETIME NULL");
                        }
                        if (!Schema::hasColumn('actas_eliminadas', 'deleted_by')) {
                            DB::statement("ALTER TABLE `actas_eliminadas` ADD COLUMN deleted_by INT NULL");
                        }
                        if (!Schema::hasColumn('actas_eliminadas', 'original_acta_id')) {
                            DB::statement("ALTER TABLE `actas_eliminadas` ADD COLUMN original_acta_id INT NULL");
                        }
                    }
                } catch (\Exception $e) {
                    logger()->warning('No se pudo asegurar columnas en actas_eliminadas: ' . $e->getMessage());
                }

                // Preparar copia a insertar en actas_eliminadas
                $original = (array) $acta;
                $original['original_acta_id'] = $original['id'];
                // No sobrescribir la PK en la tabla destino
                unset($original['id']);

                // Añadir metadatos de eliminación
                $original['motivo_eliminacion'] = $motivo;
                $original['observaciones_eliminacion'] = $observaciones;
                $original['supervisor_eliminante'] = $supervisor;
                $original['deleted_at'] = $hora;
                $original['deleted_by'] = Auth::id() ?? null;

                // Insertar en actas_eliminadas (si la tabla existe)
                if (Schema::hasTable('actas_eliminadas')) {
                    // filtrar columnas que realmente existen en la tabla destino
                    $cols = Schema::getColumnListing('actas_eliminadas');
                    $toInsert = array_intersect_key($original, array_flip($cols));
                    DB::table('actas_eliminadas')->insert($toInsert);
                }

                // Finalmente eliminar la fila original de la tabla actas para no dejar campos nulos
                DB::table('actas')->where('id', $id)->delete();
            });

            logger()->info('Acta eliminada y respaldada en actas_eliminadas', ['acta_id' => $id, 'backup_original_id' => $acta->id, 'user_id' => Auth::id(), 'motivo' => $motivo]);

            return response()->json(['success' => true, 'message' => 'Acta eliminada y respaldada en actas_eliminadas']);
        } catch (\Exception $e) {
            logger()->error('Error procesando eliminación de acta: ' . $e->getMessage(), ['acta_id' => $id, 'user_id' => Auth::id()]);
            return response()->json(['success' => false, 'message' => 'Error procesando la eliminación: ' . $e->getMessage()], 500);
        }
    }

}
