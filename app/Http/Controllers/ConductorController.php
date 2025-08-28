<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ConductorController extends Controller
{
    public function index()
    {
        // Usar paginación para la vista
        $conductores = DB::table('conductores')
            ->select('conductores.*', DB::raw("CONCAT(IFNULL(nombres,''), ' ', IFNULL(apellidos,'')) as nombre_completo"))
            ->orderBy('nombres')
            ->paginate(10);

        return view('administrador.mantenimiento.conductores', compact('conductores'));
    }

    public function store(Request $request)
    {
        // aceptar 'licencia' desde el formulario pero mapear a 'numero_licencia' en la BD
        $data = $request->only(['nombres', 'apellidos', 'dni', 'telefono', 'licencia']);

        $validator = Validator::make($data, [
            'nombres' => 'required|string|max:255',
            'apellidos' => 'nullable|string|max:255',
            'dni' => 'nullable|string|max:32',
            'telefono' => 'nullable|string|max:32',
            'licencia' => 'nullable|string|max:64',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $dbData = [
            'nombres' => $data['nombres'] ?? null,
            'apellidos' => $data['apellidos'] ?? null,
            'dni' => $data['dni'] ?? null,
            'telefono' => $data['telefono'] ?? null,
            'numero_licencia' => $data['licencia'] ?? null,
            'estado_licencia' => 'vigente',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $id = DB::table('conductores')->insertGetId($dbData);

        return response()->json(['id' => $id], 201);
    }

    public function show($id)
    {
        $conductor = DB::table('conductores')->where('id', $id)->first();
        if (! $conductor) {
            return response()->json(['error' => 'Conductor no encontrado'], 404);
        }
        return response()->json(['conductor' => $conductor]);
    }

    public function update(Request $request, $id)
    {
    $data = $request->only(['nombres', 'apellidos', 'dni', 'telefono', 'licencia']);

        $validator = Validator::make($data, [
            'nombres' => 'required|string|max:255',
            'apellidos' => 'nullable|string|max:255',
            'dni' => 'nullable|string|max:32',
            'telefono' => 'nullable|string|max:32',
            'licencia' => 'nullable|string|max:64',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $dbData = [
            'nombres' => $data['nombres'] ?? null,
            'apellidos' => $data['apellidos'] ?? null,
            'dni' => $data['dni'] ?? null,
            'telefono' => $data['telefono'] ?? null,
            'numero_licencia' => $data['licencia'] ?? null,
            'updated_at' => now(),
        ];

        $updated = DB::table('conductores')->where('id', $id)->update($dbData);

        if (! $updated) {
            return response()->json(['error' => 'Update failed or no changes'], 400);
        }

        return response()->json(['status' => 'ok']);
    }

    public function destroy($id)
    {
        $deleted = DB::table('conductores')->where('id', $id)->delete();
        if (! $deleted) {
            return response()->json(['error' => 'Delete failed'], 400);
        }
        return response()->json(['status' => 'deleted']);
    }

    public function search(Request $request)
    {
        $q = $request->query('q');
    $query = DB::table('conductores');
        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->whereRaw("CONCAT(IFNULL(nombres,''),' ',IFNULL(apellidos,'')) LIKE ?", ["%{$q}%"]) 
                    ->orWhere('dni', 'like', "%{$q}%")
                    ->orWhere('numero_licencia', 'like', "%{$q}%");
            });
        }
    $conductores = $query->select('conductores.*', DB::raw("CONCAT(IFNULL(nombres,''), ' ', IFNULL(apellidos,'')) as nombre_completo"))
        ->orderBy('nombres')
        ->limit(50)
        ->get();
        return response()->json(['conductores' => $conductores]);
    }

    public function eliminarActa(Reequest $request, $id)
    {
        $request->validate([
            'fiscalizador_autorizante' => 'required|string|max:255',
        ]);

        $acta = DB ::table('actas')->where('id', $id)->first();
        if (!$acta) {
            return response()->json(['exito' => false, 'mensaje' => 'Acta no encontrada'], 404);
        }
        // guardar solo el nombre del fiscalizador autorizante
        DB::table('auditoria_eliminacion')->insert([
            'acta_id' => $id,
            'fiscalizador_autorizante' => $request->fiscalizador_autorizante,
            'fecha'=>now(),
        ]);

        // Elimina el acta
        DB::table('actas')->where('id', $id)->delete();

        return response()->json(['exito' => true, 'mensaje' => 'acta eliminada correctamente']);
    }

}
