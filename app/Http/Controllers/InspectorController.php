<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Inspector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InspectorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $inspectores = Inspector::all();
        return view('administrador.mantenimiento.fiscal', compact('inspectores'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dni' => 'required|unique:inspectores|digits:8',
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'codigo_inspector' => 'required|unique:inspectores',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|unique:inspectores',
            'fecha_ingreso' => 'required|date',
            'zona_asignada' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $inspector = Inspector::create($request->all());
            
            return response()->json([
                'success' => true,
                'message' => 'Inspector creado exitosamente',
                'inspector' => $inspector
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el inspector: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $inspector = Inspector::findOrFail($id);
            return response()->json([
                'success' => true,
                'inspector' => $inspector
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Inspector no encontrado'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $inspector = Inspector::findOrFail($id);
            
            $validator = Validator::make($request->all(), [
                'dni' => 'required|digits:8|unique:inspectores,dni,' . $id,
                'nombres' => 'required|string|max:255',
                'apellidos' => 'required|string|max:255',
                'codigo_inspector' => 'required|unique:inspectores,codigo_inspector,' . $id,
                'telefono' => 'nullable|string|max:20',
                'email' => 'nullable|email|unique:inspectores,email,' . $id,
                'fecha_ingreso' => 'required|date',
                'zona_asignada' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $inspector->update($request->all());
            
            return response()->json([
                'success' => true,
                'message' => 'Inspector actualizado exitosamente',
                'inspector' => $inspector
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el inspector: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $inspector = Inspector::findOrFail($id);
            $inspector->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Inspector eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el inspector: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cambiar estado del inspector
     */
    public function toggleStatus(string $id)
    {
        try {
            $inspector = Inspector::findOrFail($id);
            
            $nuevoEstado = $inspector->estado === 'activo' ? 'inactivo' : 'activo';
            $inspector->update(['estado' => $nuevoEstado]);
            
            return response()->json([
                'success' => true,
                'message' => 'Estado del inspector actualizado exitosamente',
                'nuevo_estado' => $nuevoEstado
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar el estado del inspector: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Buscar inspectores con filtros
     */
    public function search(Request $request)
    {
        $query = Inspector::query();

        if ($request->filled('dni')) {
            $query->where('dni', 'LIKE', '%' . $request->dni . '%');
        }

        if ($request->filled('nombre')) {
            $query->where(function($q) use ($request) {
                $q->where('nombres', 'LIKE', '%' . $request->nombre . '%')
                  ->orWhere('apellidos', 'LIKE', '%' . $request->nombre . '%');
            });
        }

        if ($request->filled('codigo')) {
            $query->where('codigo_inspector', 'LIKE', '%' . $request->codigo . '%');
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $inspectores = $query->get();

        return response()->json([
            'success' => true,
            'inspectores' => $inspectores
        ]);
    }
}
