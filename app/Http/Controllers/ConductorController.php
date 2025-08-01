<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Conductor;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ConductorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $conductores = Conductor::with('empresa')->get();
        $empresas = Empresa::all();
        return view('administrador.mantenimiento.conductor', compact('conductores', 'empresas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dni' => 'required|unique:conductores|digits:8',
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'fecha_nacimiento' => 'required|date',
            'numero_licencia' => 'required|unique:conductores',
            'clase_categoria' => 'required|string',
            'fecha_expedicion' => 'required|date',
            'fecha_vencimiento' => 'required|date|after:fecha_expedicion',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|unique:conductores',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $conductor = Conductor::create($request->all());
            
            return response()->json([
                'success' => true,
                'message' => 'Conductor creado exitosamente',
                'conductor' => $conductor->load('empresa')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el conductor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $conductor = Conductor::with('empresa')->findOrFail($id);
            return response()->json([
                'success' => true,
                'conductor' => $conductor
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Conductor no encontrado'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $conductor = Conductor::findOrFail($id);
            
            $validator = Validator::make($request->all(), [
                'dni' => 'required|digits:8|unique:conductores,dni,' . $id,
                'nombres' => 'required|string|max:255',
                'apellidos' => 'required|string|max:255',
                'fecha_nacimiento' => 'required|date',
                'numero_licencia' => 'required|unique:conductores,numero_licencia,' . $id,
                'clase_categoria' => 'required|string',
                'fecha_expedicion' => 'required|date',
                'fecha_vencimiento' => 'required|date|after:fecha_expedicion',
                'telefono' => 'nullable|string|max:20',
                'email' => 'nullable|email|unique:conductores,email,' . $id,
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $conductor->update($request->all());
            
            return response()->json([
                'success' => true,
                'message' => 'Conductor actualizado exitosamente',
                'conductor' => $conductor->load('empresa')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el conductor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $conductor = Conductor::findOrFail($id);
            $conductor->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Conductor eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el conductor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cambiar estado del conductor
     */
    public function toggleStatus(string $id)
    {
        try {
            $conductor = Conductor::findOrFail($id);
            
            $nuevoEstado = $conductor->estado === 'activo' ? 'suspendido' : 'activo';
            $conductor->update(['estado' => $nuevoEstado]);
            
            return response()->json([
                'success' => true,
                'message' => 'Estado del conductor actualizado exitosamente',
                'nuevo_estado' => $nuevoEstado
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar el estado del conductor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Buscar conductores con filtros
     */
    public function search(Request $request)
    {
        $query = Conductor::with('empresa');

        if ($request->filled('dni')) {
            $query->where('dni', 'LIKE', '%' . $request->dni . '%');
        }

        if ($request->filled('nombre')) {
            $query->where(function($q) use ($request) {
                $q->where('nombres', 'LIKE', '%' . $request->nombre . '%')
                  ->orWhere('apellidos', 'LIKE', '%' . $request->nombre . '%');
            });
        }

        if ($request->filled('licencia')) {
            $query->where('numero_licencia', 'LIKE', '%' . $request->licencia . '%');
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $conductores = $query->get();

        return response()->json([
            'success' => true,
            'conductores' => $conductores
        ]);
    }
}
