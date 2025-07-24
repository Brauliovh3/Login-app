<?php

namespace App\Http\Controllers;

use App\Models\Infraccion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InfraccionController extends Controller
{
    public function index()
    {
        $infracciones = Infraccion::with('user')->latest()->paginate(10);
        return view('infracciones.index', compact('infracciones'));
    }

    public function create()
    {
        if (Auth::user()->role !== 'fiscalizador' && Auth::user()->role !== 'administrador') {
            abort(403, 'No tienes permisos para crear infracciones.');
        }
        return view('infracciones.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'agente_infractor' => 'required|in:transportista,operador_ruta,conductor',
            'placa' => 'required|string|max:10',
            'razon_social' => 'nullable|string|max:255',
            'ruc_dni' => 'required|string|max:20',
            'fecha_inicio' => 'required|date',
            'hora_inicio' => 'required',
            'fecha_fin' => 'nullable|date',
            'hora_fin' => 'nullable',
            'nombre_conductor1' => 'required|string|max:255',
            'licencia_conductor1' => 'required|string|max:20',
            'clase_categoria' => 'required|string|max:10',
            'lugar_intervencion' => 'required|string',
            'km_via_nacional' => 'nullable|string|max:50',
            'origen_viaje' => 'required|string|max:255',
            'destino_viaje' => 'required|string|max:255',
            'tipo_servicio' => 'required|in:personas,mercancia',
            'inspector' => 'required|string|max:255',
            'descripcion_hechos' => 'required|string',
            'medios_probatorios' => 'nullable|string',
            'calificacion_infraccion' => 'required|string',
            'medidas_administrativas' => 'nullable|string',
            'sancion' => 'nullable|string',
            'observaciones_intervenido' => 'nullable|string',
            'observaciones_inspector' => 'nullable|string',
        ]);

        // Agregar el ID del usuario que está registrando la infracción
        $validated['user_id'] = Auth::id();

        // Crear la infracción en la base de datos
        Infraccion::create($validated);
        
        return redirect()->route('infracciones.index')->with('success', 'Infracción registrada exitosamente.');
    }

    public function show($id)
    {
        // Mostrar una infracción específica
        return view('infracciones.show', compact('id'));
    }

    public function edit($id)
    {
        if (Auth::user()->role !== 'fiscalizador' && Auth::user()->role !== 'administrador') {
            abort(403, 'No tienes permisos para editar infracciones.');
        }
        // Lógica para editar
        return view('infracciones.edit', compact('id'));
    }
}
