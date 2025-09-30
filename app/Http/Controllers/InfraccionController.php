<?php

namespace App\Http\Controllers;

use App\Models\Infraccion;
use App\Models\DetalleInfraccion;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class InfraccionController extends Controller
{
    /**
     * Constructor - Middleware de autenticación
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (auth()->user()->role !== 'fiscalizador' && auth()->user()->role !== 'administrador') {
                abort(403, 'No tienes permisos para acceder a esta sección.');
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $infracciones = Infraccion::with('detalles')
            ->orderBy('codigo')
            ->paginate(10);

        return view('fiscalizador.infracciones.index', compact('infracciones'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'codigo' => 'required|string|max:10|unique:infracciones,codigo',
            'aplica_sobre' => 'required|string|max:255',
            'reglamento' => 'required|string',
            'norma_modificatoria' => 'required|string|max:255',
            'clase_pago' => 'required|string|max:255',
            'sancion' => 'required|string|max:255',
            'tipo' => 'required|string|max:255',
            'medida_preventiva' => 'required|string',
            'gravedad' => 'required|in:leve,grave,muy_grave',
            'otros_responsables__otros_beneficios' => 'nullable|string',
            'detalles' => 'required|array|min:1',
            'detalles.*.descripcion' => 'required|string',
            'detalles.*.subcategoria' => 'nullable|string|max:10',
            'detalles.*.descripcion_detallada' => 'required|string',
            'detalles.*.condiciones_especiales' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $infraccion = Infraccion::create($request->only([
                'codigo', 'aplica_sobre', 'reglamento', 'norma_modificatoria',
                'clase_pago', 'sancion', 'tipo', 'medida_preventiva',
                'gravedad', 'otros_responsables__otros_beneficios'
            ]));

            // Crear detalles
            foreach ($request->detalles as $detalle) {
                $infraccion->detalles()->create($detalle);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Infracción creada exitosamente',
                'data' => $infraccion->load('detalles')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la infracción: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Infraccion $infraccion): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $infraccion->load('detalles')
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Infraccion $infraccion): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'codigo' => 'required|string|max:10|unique:infracciones,codigo,' . $infraccion->id,
            'aplica_sobre' => 'required|string|max:255',
            'reglamento' => 'required|string',
            'norma_modificatoria' => 'required|string|max:255',
            'clase_pago' => 'required|string|max:255',
            'sancion' => 'required|string|max:255',
            'tipo' => 'required|string|max:255',
            'medida_preventiva' => 'required|string',
            'gravedad' => 'required|in:leve,grave,muy_grave',
            'otros_responsables__otros_beneficios' => 'nullable|string',
            'detalles' => 'required|array|min:1',
            'detalles.*.id' => 'nullable|exists:detalle_infraccion,id',
            'detalles.*.descripcion' => 'required|string',
            'detalles.*.subcategoria' => 'nullable|string|max:10',
            'detalles.*.descripcion_detallada' => 'required|string',
            'detalles.*.condiciones_especiales' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $infraccion->update($request->only([
                'codigo', 'aplica_sobre', 'reglamento', 'norma_modificatoria',
                'clase_pago', 'sancion', 'tipo', 'medida_preventiva',
                'gravedad', 'otros_responsables__otros_beneficios'
            ]));

            // Eliminar detalles existentes y crear nuevos
            $infraccion->detalles()->delete();
            
            foreach ($request->detalles as $detalle) {
                $infraccion->detalles()->create($detalle);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Infracción actualizada exitosamente',
                'data' => $infraccion->load('detalles')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la infracción: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Infraccion $infraccion): JsonResponse
    {
        try {
            $infraccion->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Infracción eliminada exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la infracción: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get infracciones for DataTables AJAX
     */
    public function datatables(Request $request): JsonResponse
    {
        $query = Infraccion::with('detalles');

        // Filtros
        if ($request->filled('gravedad')) {
            $query->where('gravedad', $request->gravedad);
        }

        if ($request->filled('aplica_sobre')) {
            $query->where('aplica_sobre', 'LIKE', '%' . $request->aplica_sobre . '%');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('codigo', 'LIKE', '%' . $search . '%')
                  ->orWhere('aplica_sobre', 'LIKE', '%' . $search . '%')
                  ->orWhere('sancion', 'LIKE', '%' . $search . '%');
            });
        }

        $infracciones = $query->orderBy('codigo')->get();

        return response()->json([
            'data' => $infracciones
        ]);
    }
}
