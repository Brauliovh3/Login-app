<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
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

        return view('fiscalizador.actas.create', compact('vehiculos', 'conductores', 'infracciones', 'inspectores'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'vehiculo_id' => 'required|exists:vehiculos,id',
            'conductor_id' => 'required|exists:conductores,id',
            'infraccion_id' => 'required|exists:infracciones,id',
            'inspector_id' => 'required|exists:inspectores,id',
            'ubicacion' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'monto_multa' => 'required|numeric|min:0',
        ]);

        // Obtener la hora actual exacta para el registro
        $horaActual = Carbon::now();
        
        // Generar número de acta único
        $numeroActa = 'DRTC-APU-' . date('Y') . '-' . str_pad(DB::table('actas')->count() + 1, 3, '0', STR_PAD_LEFT);

        $actaId = DB::table('actas')->insertGetId([
            'numero_acta' => $numeroActa,
            'vehiculo_id' => $request->vehiculo_id,
            'conductor_id' => $request->conductor_id,
            'infraccion_id' => $request->infraccion_id,
            'inspector_id' => $request->inspector_id,
            'fecha_infraccion' => $horaActual->toDateString(),
            'hora_infraccion' => $horaActual->toTimeString(),
            'hora_inicio_registro' => $horaActual->toDateTimeString(), // Hora exacta del inicio
            'ubicacion' => $request->ubicacion,
            'descripcion' => $request->descripcion,
            'monto_multa' => $request->monto_multa,
            'estado' => 'pendiente',
            'user_id' => Auth::id(),
            'created_at' => $horaActual->toDateTimeString(),
            'updated_at' => $horaActual->toDateTimeString(),
        ]);

        // Crear notificación
        DB::table('notifications')->insert([
            'user_id' => Auth::id(),
            'title' => 'Nueva Acta Registrada',
            'message' => "Se ha registrado una nueva acta de infracción #{$numeroActa} a las {$horaActual->format('H:i:s')}",
            'type' => 'info',
            'read' => false,
            'created_at' => $horaActual->toDateTimeString(),
            'updated_at' => $horaActual->toDateTimeString(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Acta registrada exitosamente',
            'acta_id' => $actaId,
            'numero_acta' => $numeroActa,
            'hora_registro' => $horaActual->format('d/m/Y H:i:s')
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
}
