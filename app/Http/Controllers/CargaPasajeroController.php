<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CargaPasajero;

class CargaPasajeroController extends Controller
{
    // Listar todos los registros
    public function index()
    {
        // Solo administrador y fiscalizador pueden ver la lista
        if (!auth()->user()->hasRole('fiscalizador') && !auth()->user()->hasRole('administrador')) {
            abort(403, 'No autorizado');
        }
        $registros = CargaPasajero::all();
        return view('carga_pasajero.index', compact('registros'));
    }

    //Mostrar formulario de creacion 
    public function create()
    {
        if (!auth()->user()->hasRole('fiscalizador') && !auth()->user()->hasRole('administrador')) {
            abort(403, 'No autorizado');
        }
        return view('carga_pasajero.create');
    }

    // Guarda nuevo registro 
    public function store(Request $request)
    {
        if (!auth()->user()->hasRole('fiscalizador') && !auth()->user()->hasRole('administrador')) {
            abort(403, 'No autorizado');
        }
        $request->validate([
            'informe' => 'required|string',
            'resolucion' => 'required|string',
            'conductor' => 'required|string',
            'licencia_conductor' => 'required|string',
        ]);

        // Ejemplo condicional: validar que el estado exista y sea texto
        if (!is_string($request->estado) || empty($request->estado)) {
            return back()->with('error', 'Estado no válido');
        }
        CargaPasajero::create($request->all());
    return redirect()->route('carga-pasajero.index')->with('success', 'Registro creado');
    }

    //Mostrar registro especifico
    public function show(string $id)
    {
        $registro = CargaPasajero::findOrFail($id);
        // Solo administrador y fiscalizador pueden ver
        if (!auth()->user()->hasRole('fiscalizador') && !auth()->user()->hasRole('administrador')) {
            abort(403, 'No autorizado');
        }
        return view('carga_pasajero.show', compact('registro'));
    }

    // Mostrar formulario de edicion
    public function edit(string $id)
    {
        $registro = CargaPasajero::findOrFail($id);
        if (!auth()->user()->hasRole('fiscalizador') && !auth()->user()->hasRole('administrador')) {
            abort(403, 'No autorizado');
        }
        return view('carga_pasajero.edit', compact('registro'));
    }

    // Actualizar registro
    public function update(Request $request, string $id)
    {
        $registro = CargaPasajero::findOrFail($id);
        if(!auth()->user()->hasRole('fiscalizador') && !auth()->user()->hasRole('administrador')) {
            abort(403, 'No autorizado');
        }
        $request->validate([
            'informe' => 'required|string',
            'resolucion' => 'required|string',
            'conductor' => 'required|string',
        ]);
        $registro->update($request->all());
    return redirect()->route('carga-pasajero.index')->with('success', 'Registro actualizado');
    }

    // Eliminar registro
    public function destroy(string $id)
    {
        $registro = CargaPasajero::findOrFail($id);
        if (!auth()->user()->hasRole('fiscalizador') && !auth()->user()->hasRole('administrador')) {
            abort(403, 'No autorizado');
        }

        $registro->delete();
    return redirect()->route('carga-pasajero.index')->with('success', 'Registro eliminado');
    }
}
