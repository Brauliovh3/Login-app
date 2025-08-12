<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Redirigir a la URL específica del rol
        switch ($user->role) {
            case 'administrador':
                return redirect()->route('admin.dashboard');
            case 'fiscalizador':
                return redirect()->route('fiscalizador.dashboard');
            case 'ventanilla':
                return redirect()->route('ventanilla.dashboard');
            case 'inspector':
                return redirect()->route('inspector.dashboard');
            default:
                abort(403, 'Rol no válido.');
        }
    }

    public function inspectorDashboard()
    {
        $user = Auth::user();
        
        $stats = [
            'total_infracciones' => 45,
            'infracciones_resueltas' => 32,
            'infracciones_pendientes' => 13,
            'total_actas' => 67,
            'actas_procesadas' => 55,
            'actas_pendientes' => 12
        ];
        
        // Agregar notificaciones (por ahora vacías para evitar el error)
        $notifications = collect([]); // Una colección vacía
        
        return view('inspector.dashboard', compact('stats', 'notifications'));
    }

    public function adminDashboard()
    {
        $user = Auth::user();
        
        $stats = [
            'total_usuarios' => 156,
            'usuarios_activos' => 142,
            'usuarios_pendientes' => 14,
            'total_roles' => 4
        ];
        
        $notifications = collect([]);
        
        return view('administrador.dashboard', compact('stats', 'notifications'));
    }

    public function fiscalizadorDashboard()
    {
        $user = Auth::user();
        
        $stats = [
            'total_infracciones' => 89,
            'infracciones_procesadas' => 67,
            'infracciones_pendientes' => 22,
            'total_multas' => 45000
        ];
        
        $notifications = collect([]);
        
        return view('fiscalizador.dashboard', compact('stats', 'notifications'));
    }

    public function ventanillaDashboard()
    {
        $user = Auth::user();
        
        $stats = [
            'atenciones_hoy' => 23,
            'cola_espera' => 5,
            'tramites_completados' => 18,
            'tiempo_promedio' => 15
        ];
        
        $notifications = collect([]);
        
        return view('ventanilla.dashboard', compact('stats', 'notifications'));
    }

    public function inspectorNuevaInspeccion()
    {
        return view('inspector.nueva-inspeccion');
    }

    public function inspectorNuevaInspeccionStore(Request $request)
    {
        // Validar datos de entrada
        $request->validate([
            'placa' => 'required|string|max:10',
            'tipo_vehiculo' => 'required|string',
            'conductor_dni' => 'required|string|size:8',
            'conductor_nombre' => 'required|string|max:255',
            'fecha_inspeccion' => 'required|date',
            'ubicacion' => 'required|string|max:255',
            'tipo_inspeccion' => 'required|string',
            'estado' => 'required|string|in:pendiente,completada,observada',
            'infracciones' => 'nullable|array',
            'observaciones' => 'nullable|string',
            'multa_aplicada' => 'nullable|numeric|min:0'
        ]);

        // Aquí iría la lógica para guardar la inspección en la base de datos
        // Por ahora simulamos éxito
        
        return redirect()->route('inspector.inspecciones')->with('success', 'Inspección registrada correctamente');
    }

    public function inspectorInspecciones()
    {
        // Aquí cargarías las inspecciones desde la base de datos
        return view('inspector.inspecciones');
    }

    public function inspectorVehiculos()
    {
        // Aquí cargarías los vehículos desde la base de datos
        return view('inspector.vehiculos');
    }

    public function inspectorReportes()
    {
        // Aquí cargarías los datos para los reportes
        return view('inspector.reportes');
    }
}
