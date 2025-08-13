<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Acta;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ActaController extends Controller
{
    public function index()
    {
        $actas = Acta::with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('fiscalizador.actas-contra', compact('actas'));
    }

    public function store(Request $request)
    {
        try {
            // Log de datos recibidos para debug
            \Log::info('Datos recibidos para crear acta:', $request->all());

            $request->validate([
                'lugar_intervencion' => 'required|string',
                'fecha_intervencion' => 'required|date',
                'hora_intervencion' => 'required',
                'inspector_responsable' => 'required|string',
                'tipo_servicio' => 'required|string',
                'tipo_agente' => 'required|in:Transportista,Operador de Ruta,Conductor',
                'placa' => 'nullable|string',
                'razon_social' => 'nullable|string',
                'ruc_dni' => 'required|string',
                'descripcion_hechos' => 'required|string',
                'calificacion' => 'required|in:Leve,Grave,Muy Grave',
            ]);

            $acta = new Acta();
            
            // Generar número de acta si no viene incluido
            $acta->numero_acta = $request->numero_acta ?: Acta::generarNumeroActa();
            
            $acta->lugar_intervencion = $request->lugar_intervencion;
            $acta->fecha_intervencion = $request->fecha_intervencion;
            $acta->hora_intervencion = $request->hora_intervencion;
            $acta->inspector_responsable = $request->inspector_responsable;
            $acta->tipo_servicio = $request->tipo_servicio;
            $acta->tipo_agente = $request->tipo_agente;
            $acta->placa = strtoupper($request->placa);
            $acta->razon_social = strtoupper($request->razon_social);
            $acta->ruc_dni = $request->ruc_dni;
            $acta->nombre_conductor = $request->nombre_conductor;
            $acta->licencia = $request->licencia;
            $acta->clase_licencia = $request->clase_licencia;
            $acta->origen = $request->origen;
            $acta->destino = $request->destino;
            $acta->numero_personas = $request->numero_personas;
            $acta->descripcion_hechos = $request->descripcion_hechos;
            $acta->medios_probatorios = $request->medios_probatorios;
            $acta->calificacion = $request->calificacion;
            $acta->medida_administrativa = $request->medida_administrativa;
            $acta->sancion = $request->sancion;
            $acta->observaciones_intervenido = $request->observaciones_intervenido;
            $acta->observaciones_inspector = $request->observaciones_inspector;
            $acta->user_id = Auth::id();
            
            // Log antes de guardar
            \Log::info('Intentando guardar acta:', $acta->toArray());
            
            $acta->save();

            // Log de éxito
            \Log::info('Acta guardada exitosamente con ID:', ['id' => $acta->id, 'numero' => $acta->numero_acta]);

            return response()->json([
                'success' => true,
                'message' => 'Acta registrada exitosamente',
                'numero_acta' => $acta->numero_acta,
                'id' => $acta->id
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Error de validación al crear acta:', $e->errors());
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            \Log::error('Error general al crear acta:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor: ' . $e->getMessage(),
                'debug' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }

    public function show($id)
    {
        $acta = Acta::with('user')->findOrFail($id);
        return response()->json(['acta' => $acta]);
    }

    public function update(Request $request, $id)
    {
        $acta = Acta::findOrFail($id);
        
        $request->validate([
            'lugar_intervencion' => 'required|string',
            'inspector_responsable' => 'required|string',
            'tipo_servicio' => 'required|string',
            'tipo_agente' => 'required|in:Transportista,Operador de Ruta,Conductor',
            'placa' => 'required|string',
            'razon_social' => 'required|string',
            'ruc_dni' => 'required|string',
            'descripcion_hechos' => 'required|string',
            'calificacion' => 'required|in:Leve,Grave,Muy Grave',
        ]);

        $acta->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Acta actualizada exitosamente'
        ]);
    }

    public function destroy($id)
    {
        $acta = Acta::findOrFail($id);
        $acta->estado = 'anulada';
        $acta->save();

        return response()->json([
            'success' => true,
            'message' => 'Acta anulada exitosamente'
        ]);
    }

    public function consultas(Request $request)
    {
        $query = Acta::with('user');

        // Búsqueda unificada en una sola casilla
        if ($request->buscar) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->where('numero_acta', 'like', '%' . $buscar . '%')
                  ->orWhere('ruc_dni', 'like', '%' . $buscar . '%')
                  ->orWhere('licencia', 'like', '%' . $buscar . '%')
                  ->orWhere('placa', 'like', '%' . $buscar . '%')
                  ->orWhere('nombre_conductor', 'like', '%' . $buscar . '%')
                  ->orWhere('razon_social', 'like', '%' . $buscar . '%');
            });
        }

        // Filtro por estado
        if ($request->estado) {
            $query->byEstado($request->estado);
        }

        // Filtro por fecha
        if ($request->fecha) {
            $query->whereDate('fecha_intervencion', $request->fecha);
        }

        // Filtro por rango de fechas
        if ($request->fecha_desde && $request->fecha_hasta) {
            $query->byFechaRango($request->fecha_desde, $request->fecha_hasta);
        }

        $actas = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'actas' => $actas,
            'total' => $actas->count(),
            'estadisticas' => [
                'total_actas' => $actas->count(),
                'procesadas' => $actas->where('estado', 'procesada')->count(),
                'pendientes' => $actas->where('estado', 'pendiente')->count(),
                'anuladas' => $actas->where('estado', 'anulada')->count(),
            ]
        ]);
    }

    public function exportarExcel(Request $request)
    {
        $query = Acta::with('user');

        // Búsqueda unificada
        if ($request->buscar) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->where('numero_acta', 'like', '%' . $buscar . '%')
                  ->orWhere('ruc_dni', 'like', '%' . $buscar . '%')
                  ->orWhere('licencia', 'like', '%' . $buscar . '%')
                  ->orWhere('placa', 'like', '%' . $buscar . '%')
                  ->orWhere('nombre_conductor', 'like', '%' . $buscar . '%')
                  ->orWhere('razon_social', 'like', '%' . $buscar . '%');
            });
        }

        // Aplicar otros filtros
        if ($request->estado) {
            $query->byEstado($request->estado);
        }
        if ($request->fecha) {
            $query->whereDate('fecha_intervencion', $request->fecha);
        }
        if ($request->fecha_desde && $request->fecha_hasta) {
            $query->byFechaRango($request->fecha_desde, $request->fecha_hasta);
        }

        $actas = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'actas' => $actas,
            'filename' => 'Actas_DRTC_' . date('Y-m-d') . '.xlsx'
        ]);
    }

    public function proximoNumero()
    {
        $proximoNumero = Acta::obtenerProximoNumero();
        
        return response()->json([
            'success' => true,
            'numero' => $proximoNumero,
            'solo_numero' => str_pad(explode('-', $proximoNumero)[3], 6, '0', STR_PAD_LEFT),
            'year' => date('Y')
        ]);
    }
}

