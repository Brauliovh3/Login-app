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

        $actaId = DB::table('actas')->insertGetId([
            'numero_acta' => 'ACT-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
            'vehiculo_id' => $request->vehiculo_id,
            'conductor_id' => $request->conductor_id,
            'infraccion_id' => $request->infraccion_id,
            'inspector_id' => $request->inspector_id,
            'fecha_infraccion' => now(),
            'ubicacion' => $request->ubicacion,
            'descripcion' => $request->descripcion,
            'monto_multa' => $request->monto_multa,
            'estado' => 'pendiente',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Crear notificación
        DB::table('notifications')->insert([
            'user_id' => Auth::id(),
            'title' => 'Nueva Acta Registrada',
            'message' => 'Se ha registrado una nueva acta de infracción #ACT-' . date('Y') . '-' . str_pad($actaId, 4, '0', STR_PAD_LEFT),
            'type' => 'info',
            'read' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Acta registrada exitosamente',
            'acta_id' => $actaId
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
