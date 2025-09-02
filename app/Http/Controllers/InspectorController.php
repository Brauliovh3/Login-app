<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class InspectorController extends Controller
{
    // Show maintenance page with list of inspectors
    public function index()
    {
        $inspectores = DB::table('inspectores')
            ->select('inspectores.*', DB::raw("CONCAT(IFNULL(nombres,''), ' ', IFNULL(apellidos,'')) as nombre_completo"))
            ->orderBy('nombres')
            ->get();

        return view('administrador.mantenimiento.fiscal', compact('inspectores'));
    }

    // Store a new inspector
    public function store(Request $request)
    {
        $data = $request->only(['nombres', 'apellidos', 'dni', 'telefono', 'codigo_inspector']);
        $validator = Validator::make($data, [
            'nombres' => 'required|string|max:255',
            'apellidos' => 'nullable|string|max:255',
            'dni' => 'required|string|max:32',
            'telefono' => 'nullable|string|max:32',
            'codigo_inspector' => 'required|string|max:64',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // Check uniqueness to avoid DB exceptions and provide friendly errors
        if (!empty($data['dni']) && DB::table('inspectores')->where('dni', $data['dni'])->exists()) {
            return response()->json(['success' => false, 'message' => 'El DNI ya existe'], 409);
        }
        if (!empty($data['codigo_inspector']) && DB::table('inspectores')->where('codigo_inspector', $data['codigo_inspector'])->exists()) {
            return response()->json(['success' => false, 'message' => 'El código de inspector ya existe'], 409);
        }

        try {
            $id = DB::table('inspectores')->insertGetId(array_merge($data, [
                'estado' => 'activo',
                'created_at' => now(),
                'updated_at' => now(),
            ]));

            return response()->json(['success' => true, 'id' => $id], 201);
    } catch (\Exception $e) {
            Log::error('InspectorController::store error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error interno al guardar el inspector'], 500);
        }
    }

    // Show inspector
    public function show($id)
    {
        $inspector = DB::table('inspectores')->where('id', $id)->first();
        if (! $inspector) {
            return response()->json(['success' => false, 'message' => 'Inspector not found'], 404);
        }
        return response()->json(['success' => true, 'inspector' => $inspector]);
    }

    // Update inspector
    public function update(Request $request, $id)
    {
        $data = $request->only(['nombres', 'apellidos', 'dni', 'telefono', 'codigo_inspector']);
        $validator = Validator::make($data, [
            'nombres' => 'required|string|max:255',
            'apellidos' => 'nullable|string|max:255',
            'dni' => 'required|string|max:32',
            'telefono' => 'nullable|string|max:32',
            'codigo_inspector' => 'required|string|max:64',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // Check uniqueness excluding current record
        if (!empty($data['dni']) && DB::table('inspectores')->where('dni', $data['dni'])->where('id', '!=', $id)->exists()) {
            return response()->json(['success' => false, 'message' => 'El DNI ya pertenece a otro inspector'], 409);
        }
        if (!empty($data['codigo_inspector']) && DB::table('inspectores')->where('codigo_inspector', $data['codigo_inspector'])->where('id', '!=', $id)->exists()) {
            return response()->json(['success' => false, 'message' => 'El código de inspector ya pertenece a otro inspector'], 409);
        }

        try {
            $updated = DB::table('inspectores')->where('id', $id)->update(array_merge($data, ['updated_at' => now()]));

            if (! $updated) {
                return response()->json(['success' => false, 'message' => 'Update failed or no changes'], 400);
            }

            return response()->json(['success' => true]);
    } catch (\Exception $e) {
            Log::error('InspectorController::update error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error interno al actualizar el inspector'], 500);
        }
    }

    // Delete inspector
    public function destroy($id)
    {
        $deleted = DB::table('inspectores')->where('id', $id)->delete();
        if (! $deleted) {
            return response()->json(['success' => false, 'message' => 'Delete failed'], 400);
        }
        return response()->json(['success' => true, 'message' => 'deleted']);
    }

    // Toggle estado between 'activo' and 'inactivo'
    public function toggleStatus($id)
    {
        try {
            $inspector = DB::table('inspectores')->where('id', $id)->first();
            if (! $inspector) {
                return response()->json(['error' => 'Inspector not found'], 404);
            }

            $nuevo = (isset($inspector->estado) && $inspector->estado === 'activo') ? 'inactivo' : 'activo';
            DB::table('inspectores')->where('id', $id)->update(['estado' => $nuevo, 'updated_at' => now()]);

            return response()->json(['success' => true, 'nuevo_estado' => $nuevo]);
    } catch (\Exception $e) {
            Log::error('toggleStatus inspector error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Internal error'], 500);
        }
    }

    // Search inspectors (by nombre or dni)
    public function search(Request $request)
    {
        $q = $request->query('q');
        $query = DB::table('inspectores');
        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->whereRaw("CONCAT(IFNULL(nombres,''),' ',IFNULL(apellidos,'')) LIKE ?", ["%{$q}%"]) 
                    ->orWhere('dni', 'like', "%{$q}%")
                    ->orWhere('codigo_inspector', 'like', "%{$q}%");
            });
        }

        $inspectores = $query->select('inspectores.*', DB::raw("CONCAT(IFNULL(nombres,''), ' ', IFNULL(apellidos,'')) as nombre_completo"))
            ->orderBy('nombres')
            ->limit(50)
            ->get();
    return response()->json(['success' => true, 'inspectores' => $inspectores]);
    }
}
