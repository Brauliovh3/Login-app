<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CargaPasajeroController extends Controller
{
    // Listar todos los registros
    public function index()
    {
        //Solo fiscalizador puede ver la lista 
        if (!auth()->user()->hasRole('fiscalizador')) {
            abort(403, 'No autorizado');
        }
        $registros = CargaPasajero::all();
        return view('carga_pasajero.index', compact('registros'));
    }

    //Mostrar formulario de creacion 
    public function create()
    {
        if (!auth()->user()->hasRole('fiscalizador')) {
            abort(403, 'No autorizado');
        }
        return view('carga_pasajero.create');
    }

    // Guarda nuevo registro 
    public function store(Request $request)
    {
        if (!auth()->user()->hasRole('fiscalizador')) {
            abort(403, 'No autorizado');
        }
        $request->validate([
            'informe' => 'required|string',
            'resolucion' => 'required|string',
            'conductor' => 'required|string',
            'licencia_conductor' => 'required|string',
        ]);

        //Ejemplo condicional: solo guardar si es estado es valido
        if (!in_array($request->estado, ['pendiente', 'aprobado', 'procesado'])) {
            return back()->with('error', 'Estado no valido');
        }
        CargaPasajero::create($request->all());
        return redirect()->route('carga_pasajero.index')->with('success', 'Registro creado');
    }

    //Mostrar registro especifico
    public function show(string $id)
    {
        $registro = CargaPasajero::findOrFail($id);
        // Solo fiscalizador puede ver
        if (!auth()->user()->hasRole('fiscalizador')) {
            abort(403, 'No autorizado');
        }
        return view('carga_pasajero.show', compact('registro'));
    }

    // Mostrar formulario de edicion
    public function edit(string $id)
    {
        $registro = CargaPasajero::findOrFail($id);
        if (!auth()->user()->hasRole('fiscalizador')) {
            abort(403, 'No autorizado');
        }
        // Solo editar si esta pendiente
        if ($registro->estado !== 'pendiente') {
            return back()->with('error', 'Solo se pueden editar registros pendientes');
        }
        return view('carga_pasajero.edit', compact('registro'));
    }

    // Actualizar registro
    public function update(Request $request, string $id)
    {
        $registro = CargaPasajero::findOrFail($id);
        if(!auth()->user()->hasRole('fiscalizador')) {
            abort(403, 'No autorizado');
        }
        if ($registro->estado !== 'pendiente') {
            return back()->with('error', 'Solo se puede actualizar registros pendientes');

        }
        $request->validate([
            'informe' => 'required|string',
            'resolucion' => 'required|string',
            'conductor' => 'required|string',
        ]);
        $registro->update($request->all());
        return redirect()->route('carga_pasajero.index')->with('success', 'Registro actualizado');
    }

    // Eliminar registro
    public function destroy(string $id)
    {
        $registro = CargaPasajero::findOrFail($id);
        if (!auth()->user()->hasRole('fiscalizador')) {
            abort(403, 'No autorizado');
        }

        // Solo eliminar si esta pendiente
        if ($registro->estado !== 'pendiente') {
            return back()->with('error', 'Solo se puede eliminar registros pendientes');
        }

        $registro->delete();
        return redirect()->route('carga_pasajero.index')->with('success', 'Registro eliminado');
    }
}
