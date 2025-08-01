<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class InspeccionVehicularController extends Controller
{
    public function iniciar(Request $request)
    {
        $request->validate([
            'placa' => 'required|string|max:10'
        ]);

        $vehiculo = DB::table('vehiculos')
            ->join('empresas', 'vehiculos.empresa_id', '=', 'empresas.id')
            ->select(
                'vehiculos.*',
                'empresas.razon_social as empresa_nombre'
            )
            ->where('vehiculos.placa', $request->placa)
            ->first();

        if (!$vehiculo) {
            return response()->json([
                'success' => false,
                'message' => 'Vehículo no encontrado con la placa proporcionada'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'vehiculo' => $vehiculo
        ]);
    }

    public function registrarInspeccion(Request $request)
    {
        $request->validate([
            'vehiculo_id' => 'required|exists:vehiculos,id',
            'inspector_id' => 'required|exists:inspectores,id',
            'tipo_inspeccion' => 'required|in:rutina,especial,emergencia',
            'observaciones' => 'required|string',
            'estado_vehiculo' => 'required|in:optimo,bueno,regular,deficiente',
            'items_inspeccion' => 'required|array',
            'items_inspeccion.*.item' => 'required|string',
            'items_inspeccion.*.estado' => 'required|in:conforme,no_conforme',
            'items_inspeccion.*.observacion' => 'nullable|string'
        ]);

        // Crear registro de inspección
        $inspeccionId = DB::table('inspecciones')->insertGetId([
            'numero_inspeccion' => 'INS-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
            'vehiculo_id' => $request->vehiculo_id,
            'inspector_id' => $request->inspector_id,
            'fecha_inspeccion' => now(),
            'tipo_inspeccion' => $request->tipo_inspeccion,
            'observaciones' => $request->observaciones,
            'estado_vehiculo' => $request->estado_vehiculo,
            'estado' => 'completada',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Registrar items de inspección
        foreach ($request->items_inspeccion as $item) {
            DB::table('inspeccion_items')->insert([
                'inspeccion_id' => $inspeccionId,
                'item' => $item['item'],
                'estado' => $item['estado'],
                'observacion' => $item['observacion'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Actualizar estado del vehículo si es necesario
        if ($request->estado_vehiculo === 'deficiente') {
            DB::table('vehiculos')
                ->where('id', $request->vehiculo_id)
                ->update(['estado' => 'mantenimiento']);
        }

        // Crear notificación
        DB::table('notifications')->insert([
            'user_id' => Auth::id(),
            'title' => 'Inspección Vehicular Completada',
            'message' => 'Se ha completado la inspección vehicular #INS-' . date('Y') . '-' . str_pad($inspeccionId, 4, '0', STR_PAD_LEFT),
            'type' => 'success',
            'read' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Inspección registrada exitosamente',
            'inspeccion_id' => $inspeccionId
        ]);
    }

    public function verificarLicencia(Request $request)
    {
        $request->validate([
            'licencia' => 'required|string|max:20'
        ]);

        $conductor = DB::table('conductores')
            ->where('licencia', $request->licencia)
            ->first();

        if (!$conductor) {
            return response()->json([
                'success' => false,
                'message' => 'Licencia no encontrada en el sistema'
            ], 404);
        }

        // Verificar si la licencia está vigente
        $fechaVencimiento = \Carbon\Carbon::parse($conductor->fecha_vencimiento);
        $diasParaVencer = $fechaVencimiento->diffInDays(now(), false);

        $estado = 'vigente';
        if ($diasParaVencer > 0) {
            $estado = 'vencida';
        } elseif ($diasParaVencer >= -30) {
            $estado = 'por_vencer';
        }

        // Actualizar estado si es necesario
        if ($conductor->estado_licencia !== $estado) {
            DB::table('conductores')
                ->where('id', $conductor->id)
                ->update(['estado_licencia' => $estado]);
        }

        return response()->json([
            'success' => true,
            'conductor' => [
                'id' => $conductor->id,
                'nombre' => $conductor->nombre,
                'dni' => $conductor->dni,
                'licencia' => $conductor->licencia,
                'categoria' => $conductor->categoria,
                'fecha_emision' => $conductor->fecha_emision,
                'fecha_vencimiento' => $conductor->fecha_vencimiento,
                'estado_licencia' => $estado,
                'dias_para_vencer' => $diasParaVencer < 0 ? abs($diasParaVencer) : 0
            ]
        ]);
    }
}
