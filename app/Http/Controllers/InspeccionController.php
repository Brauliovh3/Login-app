<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Inspeccion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InspeccionController extends Controller
{
    public function index()
    {
        $inspecciones = Inspeccion::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('inspecciones.index', compact('inspecciones'));
    }

    public function create()
    {
        if (Auth::user()->role !== 'fiscalizador' && Auth::user()->role !== 'ventanilla' && Auth::user()->role !== 'administrador') {
            abort(403, 'No tienes permisos para crear inspecciones.');
        }
        return view('inspecciones.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fecha_inspeccion' => 'required|date',
            'hora_inicio' => 'required',
            'inspector_principal' => 'required|string|max:255',
            'tipo_agente' => 'required|string|max:255',
            'placa_1' => 'nullable|string|max:20',
            'razon_social' => 'required|string|max:255',
            'ruc_dni' => 'required|string|max:20',
            'fecha_hora_fin' => 'nullable|date',
            'nombre_conductor_1' => 'required|string|max:255',
            'licencia_conductor_1' => 'required|string|max:20',
            'clase_categoria' => 'nullable|string|max:50',
            'lugar_intervencion' => 'required|string',
            'km_red_vial' => 'nullable|string|max:100',
            'origen_viaje' => 'required|string|max:255',
            'destino_viaje' => 'required|string|max:255',
            'tipo_servicio' => 'required|string|max:50',
            'descripcion_hechos' => 'required|string',
            'medios_probatorios' => 'nullable|string',
            'calificacion_infraccion' => 'nullable|string|max:50',
            'medidas_administrativas' => 'nullable|string',
            'sancion' => 'nullable|string',
            'observaciones_intervenido' => 'nullable|string',
            'observaciones_inspector' => 'nullable|string',
        ]);

        $validated['user_id'] = Auth::id();

        Inspeccion::create($validated);

        return redirect()->route('inspecciones.index')
            ->with('success', '¡Acta de inspección registrada exitosamente!');
    }

    public function show(Inspeccion $inspeccion)
    {
        if ($inspeccion->user_id !== Auth::id()) {
            abort(403, 'No tienes permisos para ver esta inspección.');
        }

        return view('inspecciones.show', compact('inspeccion'));
    }

    public function edit(Inspeccion $inspeccion)
    {
        if ($inspeccion->user_id !== Auth::id()) {
            abort(403, 'No tienes permisos para editar esta inspección.');
        }

        return view('inspecciones.edit', compact('inspeccion'));
    }

    public function update(Request $request, Inspeccion $inspeccion)
    {
        if ($inspeccion->user_id !== Auth::id()) {
            abort(403, 'No tienes permisos para actualizar esta inspección.');
        }

        $validated = $request->validate([
            'fecha_inspeccion' => 'required|date',
            'hora_inicio' => 'required',
            'hora_fin' => 'nullable',
            'inspector_principal' => 'required|string|max:255',
            'inspector_acompanante' => 'nullable|string|max:255',
            'tipo_establecimiento' => 'required|string|max:255',
            'nombre_establecimiento' => 'required|string|max:255',
            'ruc_dni_establecimiento' => 'required|string|max:20',
            'departamento' => 'required|string|max:255',
            'provincia' => 'required|string|max:255',
            'distrito' => 'required|string|max:255',
            'direccion' => 'required|string',
            'representante_legal' => 'required|string|max:255',
            'dni_representante' => 'required|string|max:8',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'tipo_inspeccion' => 'required|string|max:255',
            'area_inspeccion' => 'required|string|max:255',
            'infraestructura' => 'required|string|max:255',
            'saneamiento' => 'required|string|max:255',
            'equipos_utensilios' => 'required|string|max:255',
            'personal' => 'required|string|max:255',
            'almacenamiento' => 'required|string|max:255',
            'preparacion_alimentos' => 'required|string|max:255',
            'documentacion' => 'required|string|max:255',
            'control_plagas' => 'required|string|max:255',
            'medida_aplicada' => 'required|string|max:255',
            'calificacion_infraccion' => 'nullable|string|max:255',
            'plazo_cumplimiento_select' => 'nullable|string|max:255',
            'observaciones_detalladas' => 'required|string',
            'observaciones_intervenido' => 'nullable|string',
            'observaciones_inspector' => 'nullable|string',
        ]);

        $inspeccion->update($validated);

        return redirect()->route('inspecciones.index')
            ->with('success', '¡Acta de inspección actualizada exitosamente!');
    }

    public function destroy(Inspeccion $inspeccion)
    {
        if ($inspeccion->user_id !== Auth::id()) {
            abort(403, 'No tienes permisos para eliminar esta inspección.');
        }

        $inspeccion->delete();

        return redirect()->route('inspecciones.index')
            ->with('success', '¡Acta de inspección eliminada exitosamente!');
    }
}
