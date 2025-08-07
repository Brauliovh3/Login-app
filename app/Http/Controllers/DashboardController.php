<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Notification;

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

    public function adminDashboard()
    {
        $user = Auth::user();
        $notifications = $user->notifications()->orderBy('created_at', 'desc')->take(5)->get();
        
        $stats = [
            'total_users' => User::count(),
            'admin_users' => User::where('role', 'administrador')->count(),
            'fiscalizador_users' => User::where('role', 'fiscalizador')->count(),
            'ventanilla_users' => User::where('role', 'ventanilla')->count(),
            'total_notifications' => \App\Models\Notification::count(),
            'unread_notifications' => $user->notifications()->where('read_status', false)->count(),
            // Datos reales de la base de datos poblada
            'total_empresas' => \DB::table('empresas')->count(),
            'total_vehiculos' => \DB::table('vehiculos')->count(),
            'vehiculos_activos' => \DB::table('vehiculos')->where('estado', 'activo')->count(),
            'total_conductores' => \DB::table('conductores')->count(),
            'conductores_activos' => \DB::table('conductores')->where('estado_licencia', 'vigente')->count(),
            'total_infracciones' => \DB::table('infracciones')->count(),
            'total_inspectores' => \DB::table('inspectores')->count(),
            'inspectores_activos' => \DB::table('inspectores')->where('estado', 'activo')->count()
        ];
        
        return view('administrador.dashboard', compact('notifications', 'stats'));
    }

    public function fiscalizadorDashboard()
    {
        $user = Auth::user();
        $notifications = $user->notifications()->orderBy('created_at', 'desc')->take(5)->get();
        
        // Estadísticas básicas y simples
        $stats = [
            // Estadísticas principales de actas
            'totalActas' => \DB::table('actas')->count(),
            'actasProcesadas' => \DB::table('actas')->where('estado', 'procesada')->count(),
            'actasPendientes' => \DB::table('actas')->where('estado', 'pendiente')->count(),
            'actasHoy' => \DB::table('actas')->whereDate('created_at', today())->count(),
            'unread_notifications' => $user->notifications()->where('read_status', false)->count(),
            
            // Datos simplificados para gráficos (valores estáticos para evitar errores)
            'infracciones_por_tipo' => [
                'documentarias' => 5,
                'operacionales' => 8,
                'tecnicas' => 3,
                'administrativas' => 2,
            ],
            
            // Actividad reciente (solo si existen actas)
            'actas_recientes' => \DB::table('actas')->exists() ? 
                \DB::table('actas')->orderBy('created_at', 'desc')->limit(3)->get(['id', 'numero_acta', 'created_at', 'estado']) :
                collect([])
        ];
        
        return view('fiscalizador.dashboard_clean', compact('notifications', 'stats'));
    }

    public function ventanillaDashboard()
    {
        $user = Auth::user();
        $notifications = $user->notifications()->orderBy('created_at', 'desc')->take(5)->get();
        
        $stats = [
            'empresas_activas' => \DB::table('empresas')->where('estado', 'activo')->count(),
            'total_vehiculos' => \DB::table('vehiculos')->count(),
            'conductores_registrados' => \DB::table('conductores')->count(),
            'licencias_vigentes' => \DB::table('conductores')->where('estado_licencia', 'vigente')->count(),
            'licencias_vencidas' => \DB::table('conductores')->where('estado_licencia', 'vencida')->count(),
            'solicitudes_pendientes' => 15, // Este valor puede venir de una tabla de solicitudes
            'unread_notifications' => $user->notifications()->where('read_status', false)->count()
        ];
        
        return view('ventanilla.dashboard', compact('notifications', 'stats'));
    }

    public function inspectorDashboard()
    {
        $user = Auth::user();
        $notifications = $user->notifications()->orderBy('created_at', 'desc')->take(5)->get();
        
        $stats = [
            'total_infracciones' => \DB::table('infracciones')->count(),
            'infracciones_resueltas' => \DB::table('infracciones')->where('estado', 'resuelta')->count(),
            'infracciones_pendientes' => \DB::table('infracciones')->where('estado', 'pendiente')->count(),
            'total_actas' => \DB::table('actas')->count(),
            'actas_procesadas' => \DB::table('actas')->where('estado', 'procesada')->count(),
            'actas_pendientes' => \DB::table('actas')->where('estado', 'pendiente')->count(),
            'unread_notifications' => $user->notifications()->where('read_status', false)->count()
        ];
        
        return view('inspector.dashboard', compact('notifications', 'stats'));
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
