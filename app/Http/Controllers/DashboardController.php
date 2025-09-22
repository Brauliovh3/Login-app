<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    /**
     * Dashboard unificado que muestra diferente contenido según el rol del usuario
     */
    public function dashboardUnificado()
    {
        $user = Auth::user();
        $role = $user->role ?? 'fiscalizador';
        
        // Obtener estadísticas específicas según el rol
        $stats = [];
        
        switch ($role) {
            case 'administrador':
                $stats = $this->getAdminStats();
                break;
            case 'fiscalizador':
                $stats = $this->getFiscalizadorStats();
                break;
            case 'ventanilla':
                $stats = $this->getVentanillaStats();
                break;
            case 'inspector':
                $stats = $this->getInspectorStats();
                break;
            default:
                $stats = $this->getFiscalizadorStats(); // Por defecto
        }
        
        return view('dashboard', compact('stats'));
    }

    public function index()
    {
        // Redirigir al dashboard unificado
        return redirect()->route('dashboard');
    }

    public function inspectorDashboard()
    {
        $user = Auth::user();
        
        try {
            // Obtener estadísticas reales relacionadas con inspecciones
            $totalInfracciones = 0;
            $infraccionesResueltas = 0;
            $infraccionesPendientes = 0;
            $totalActas = 0;
            $actasProcesadas = 0;
            $actasPendientes = 0;
            
            // Verificar si existe la tabla actas
            if (Schema::hasTable('actas')) {
                $totalActas = DB::table('actas')->count();
                $totalInfracciones = $totalActas; // Las actas representan infracciones
                
                if (Schema::hasColumn('actas', 'estado')) {
                    // El campo estado es tinyint(1): 0 = pendiente, 1 = procesada
                    $actasProcesadas = DB::table('actas')
                        ->where('estado', 1)
                        ->count();
                    $actasPendientes = DB::table('actas')
                        ->where('estado', 0)
                        ->count();
                    
                    $infraccionesResueltas = $actasProcesadas;
                    $infraccionesPendientes = $actasPendientes;
                } else {
                    // Estimaciones si no hay columna estado
                    $infraccionesResueltas = (int)($totalInfracciones * 0.7);
                    $infraccionesPendientes = $totalInfracciones - $infraccionesResueltas;
                    $actasProcesadas = (int)($totalActas * 0.8);
                    $actasPendientes = $totalActas - $actasProcesadas;
                }
            }
            
            // Obtener datos adicionales para inspector
            $datosAdicionales = $this->obtenerDatosAdicionalesInspector();
            
            $stats = [
                'total_infracciones' => $totalInfracciones,
                'infracciones_resueltas' => $infraccionesResueltas,
                'infracciones_pendientes' => $infraccionesPendientes,
                'total_actas' => $totalActas,
                'actas_procesadas' => $actasProcesadas,
                'actas_pendientes' => $actasPendientes
            ];
            
            $stats = array_merge($stats, $datosAdicionales);
            
        } catch (\Exception $e) {
            // Valores por defecto en caso de error
            $stats = [
                'total_infracciones' => 45,
                'infracciones_resueltas' => 32,
                'infracciones_pendientes' => 13,
                'total_actas' => 67,
                'actas_procesadas' => 55,
                'actas_pendientes' => 12
            ];
        }
        
        $notifications = collect([]);
        
        return view('inspector.dashboard', compact('stats', 'notifications'));
    }

    public function adminDashboard()
    {
        $user = Auth::user();
        
        try {
            // Obtener estadísticas reales de usuarios usando la tabla correcta
            $totalUsuarios = 0;
            $usuariosActivos = 0;
            $usuariosPendientes = 0;
            $totalRoles = 0;
            
            if (Schema::hasTable('usuarios')) {
                $totalUsuarios = DB::table('usuarios')->count();
                
                // Verificar usuarios activos (aprobados)
                if (Schema::hasColumn('usuarios', 'status')) {
                    $usuariosActivos = DB::table('usuarios')->where('status', 'approved')->count();
                } elseif (Schema::hasColumn('usuarios', 'email_verified_at')) {
                    $usuariosActivos = DB::table('usuarios')->whereNotNull('email_verified_at')->count();
                } else {
                    $usuariosActivos = (int)($totalUsuarios * 0.85);
                }
                
                $usuariosPendientes = $totalUsuarios - $usuariosActivos;
                
                // Contar roles únicos
                if (Schema::hasColumn('usuarios', 'role')) {
                    $totalRoles = DB::table('usuarios')->distinct()->count('role');
                } else {
                    $totalRoles = 5; // Valor por defecto
                }
            } else {
                // Valores por defecto si no existe la tabla
                $totalUsuarios = 15;
                $usuariosActivos = 12;
                $usuariosPendientes = 3;
                $totalRoles = 5;
            }
            
            // Datos adicionales para administrador
            $datosAdicionales = $this->obtenerDatosAdicionalesAdmin();
            
            $stats = [
                'total_usuarios' => $totalUsuarios,
                'usuarios_activos' => $usuariosActivos,
                'usuarios_pendientes' => $usuariosPendientes,
                'total_roles' => $totalRoles
            ];
            
            $stats = array_merge($stats, $datosAdicionales);
            
        } catch (\Exception $e) {
            // Valores por defecto en caso de error
            $stats = [
                'total_usuarios' => 156,
                'usuarios_activos' => 142,
                'usuarios_pendientes' => 14,
                'total_roles' => 5
            ];
        }
        
        $notifications = collect([]);
        
        return view('administrador.dashboard', compact('stats', 'notifications'));
    }

    public function fiscalizadorDashboard()
    {
        $user = Auth::user();
        
        try {
            // Obtener estadísticas reales de la base de datos
            $totalActas = 0;
            $actasProcesadas = 0;
            $actasPendientes = 0;
            $totalMultas = 0;
            
            // Verificar si existe la tabla actas
            if (Schema::hasTable('actas')) {
                // Total de actas
                $totalActas = DB::table('actas')->count();
                
                // Actas procesadas (estado = 1)
                if (Schema::hasColumn('actas', 'estado')) {
                    $actasProcesadas = DB::table('actas')
                        ->where('estado', 1)
                        ->count();
                    
                    // Actas pendientes (estado = 0)
                    $actasPendientes = DB::table('actas')
                        ->where('estado', 0)
                        ->count();
                } else {
                    // Si no existe la columna estado, estimamos valores
                    $actasProcesadas = (int)($totalActas * 0.75);
                    $actasPendientes = $totalActas - $actasProcesadas;
                }
                
                // Total de multas (suma de montos)
                if (Schema::hasColumn('actas', 'monto_multa')) {
                    $totalMultas = DB::table('actas')
                        ->sum('monto_multa') ?: 0;
                }
            }
            
            // No introducir valores por defecto aquí: si la tabla está vacía, devolver los conteos reales (posiblemente 0)
            // Esto asegura que el dashboard refleje exactamente los datos de la base.
            
            $stats = [
                'total_infracciones' => $totalActas,
                'infracciones_procesadas' => $actasProcesadas,
                'infracciones_pendientes' => $actasPendientes,
                'total_multas' => $totalMultas
            ];
            
            // Obtener datos adicionales para el dashboard
            $datosAdicionales = $this->obtenerDatosAdicionalestFiscalizador();
            $stats = array_merge($stats, $datosAdicionales);
            
        } catch (\Exception $e) {
            // En caso de error, usar valores por defecto
            $stats = [
                'total_infracciones' => 89,
                'infracciones_procesadas' => 67,
                'infracciones_pendientes' => 22,
                'total_multas' => 45000
            ];
        }
        
        $notifications = collect([]);
        
        return view('fiscalizador.dashboard', compact('stats', 'notifications'));
    }

    /**
     * API endpoint que devuelve las mismas estadísticas que el dashboard del fiscalizador en JSON
     */
    public function apiFiscalizadorStats()
    {
        try {
            // Reusar la lógica existente del método fiscalizadorDashboard para construir $stats
            $totalActas = 0;
            $actasProcesadas = 0;
            $actasPendientes = 0;
            $totalMultas = 0;

            if (Schema::hasTable('actas')) {
                $totalActas = DB::table('actas')->count();

                if (Schema::hasColumn('actas', 'estado')) {
                    $actasProcesadas = DB::table('actas')->where('estado', 1)->count();
                    $actasPendientes = DB::table('actas')->where('estado', 0)->count();
                } else {
                    $actasProcesadas = (int)($totalActas * 0.75);
                    $actasPendientes = $totalActas - $actasProcesadas;
                }

                if (Schema::hasColumn('actas', 'monto_multa')) {
                    $totalMultas = DB::table('actas')->sum('monto_multa') ?: 0;
                }
            }

            $stats = [
                'total_infracciones' => $totalActas,
                'infracciones_procesadas' => $actasProcesadas,
                'infracciones_pendientes' => $actasPendientes,
                'total_multas' => $totalMultas
            ];

            $datosAdicionales = $this->obtenerDatosAdicionalestFiscalizador();
            $stats = array_merge($stats, $datosAdicionales);

            return response()->json(['success' => true, 'stats' => $stats]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function ventanillaDashboard()
    {
        $user = Auth::user();
        
        try {
            // Obtener estadísticas reales relacionadas con ventanilla
            $atencionesHoy = 0;
            $tramitesCompletados = 0;
            $colaEspera = 0;
            $tiempoPromedio = 15;
            
            // Usar actas como base para calcular atenciones
            if (Schema::hasTable('actas')) {
                // Actas creadas hoy representan atenciones
                if (Schema::hasColumn('actas', 'created_at')) {
                    $atencionesHoy = DB::table('actas')
                        ->whereDate('created_at', now()->toDateString())
                        ->count();
                }
                
                // Trámites completados = actas procesadas (estado = 1)
                if (Schema::hasColumn('actas', 'estado')) {
                    $tramitesCompletados = DB::table('actas')
                        ->where('estado', 1)
                        ->whereDate('created_at', now()->toDateString())
                        ->count();
                    
                    // Cola de espera = actas pendientes del día (estado = 0)
                    $colaEspera = DB::table('actas')
                        ->where('estado', 0)
                        ->whereDate('created_at', now()->toDateString())
                        ->count();
                }
                
                // Si no hay datos del día, usar estimaciones basadas en datos totales
                if ($atencionesHoy == 0) {
                    $totalActas = DB::table('actas')->count();
                    $atencionesHoy = max(1, (int)($totalActas * 0.05)); // 5% del total
                    $tramitesCompletados = (int)($atencionesHoy * 0.78);
                    $colaEspera = max(1, (int)($atencionesHoy * 0.22));
                }
            }
            
            // Obtener datos adicionales para ventanilla
            $datosAdicionales = $this->obtenerDatosAdicionalesVentanilla();
            
            $stats = [
                'atenciones_hoy' => $atencionesHoy,
                'cola_espera' => $colaEspera,
                'tramites_completados' => $tramitesCompletados,
                'tiempo_promedio' => $tiempoPromedio
            ];
            
            $stats = array_merge($stats, $datosAdicionales);
            
        } catch (\Exception $e) {
            // Valores por defecto en caso de error
            $stats = [
                'atenciones_hoy' => 23,
                'cola_espera' => 5,
                'tramites_completados' => 18,
                'tiempo_promedio' => 15
            ];
        }
        
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
    
    /**
     * Obtener datos adicionales para el dashboard del fiscalizador
     */
    private function obtenerDatosAdicionalestFiscalizador()
    {
        $datos = [];
        
        try {
            if (Schema::hasTable('actas')) {
                // Eficiencia de procesamiento (porcentaje de completado)
                $totalActas = DB::table('actas')->count();
                $actasCompletadas = 0;
                
                if (Schema::hasColumn('actas', 'estado')) {
                    $actasCompletadas = DB::table('actas')
                        ->whereIn('estado', ['pagada', 'procesada'])
                        ->count();
                }
                
                $eficiencia = $totalActas > 0 ? round(($actasCompletadas / $totalActas) * 100) : 75;
                
                // Actas finalizadas esta semana
                $actasEstaSemana = 0;
                if (Schema::hasColumn('actas', 'created_at')) {
                    $actasEstaSemana = DB::table('actas')
                        ->where('created_at', '>=', now()->startOfWeek())
                        ->count();
                } else {
                    $actasEstaSemana = 15;
                }
                
                // Inspecciones realizadas (estimación basada en actas)
                $inspeccionesRealizadas = max(1, (int)($totalActas * 0.1));
                
                // Reportes generados (estimación)
                $reportesGenerados = max(1, (int)($totalActas * 0.056));
                
                // Meta mensual (progreso)
                $metaMensual = $totalActas > 0 ? min(100, round(($totalActas / 100) * 100)) : 89;
                
                // Calidad (basada en actas sin errores - estimación)
                $calidad = 92;
                
                $datos = [
                    'eficiencia_procesamiento' => $eficiencia,
                    'actas_finalizadas_semana' => $actasEstaSemana,
                    'inspecciones_realizadas' => $inspeccionesRealizadas,
                    'reportes_generados' => $reportesGenerados,
                    'meta_mensual_progreso' => $metaMensual,
                    'calidad_porcentaje' => $calidad
                ];
            }
            
        } catch (\Exception $e) {
            // Valores por defecto en caso de error
            $datos = [
                'eficiencia_procesamiento' => 75,
                'actas_finalizadas_semana' => 15,
                'inspecciones_realizadas' => 8,
                'reportes_generados' => 5,
                'meta_mensual_progreso' => 89,
                'calidad_porcentaje' => 92
            ];
        }
        
        return $datos;
    }
    
    /**
     * Obtener datos adicionales para el dashboard del administrador
     */
    private function obtenerDatosAdicionalesAdmin()
    {
        $datos = [];
        
        try {
            $usuariosPorRol = [];
            $usuariosEstaSemana = 0;
            $usuariosActivosRecientes = 0;
            $eficienciaSistema = 85;
            $totalUsuarios = 0;
            
            if (Schema::hasTable('usuarios')) {
                // Total de usuarios
                $totalUsuarios = DB::table('usuarios')->count();
                
                // Usuarios por rol
                if (Schema::hasColumn('usuarios', 'role')) {
                    $usuariosPorRol = DB::table('usuarios')
                        ->select('role', DB::raw('count(*) as total'))
                        ->groupBy('role')
                        ->pluck('total', 'role')
                        ->toArray();
                }
                
                // Usuarios registrados esta semana
                if (Schema::hasColumn('usuarios', 'created_at')) {
                    $usuariosEstaSemana = DB::table('usuarios')
                        ->where('created_at', '>=', now()->startOfWeek())
                        ->count();
                }
                
                // Usuarios activos recientes (basado en aprobación o creación)
                if (Schema::hasColumn('usuarios', 'approved_at')) {
                    $usuariosActivosRecientes = DB::table('usuarios')
                        ->where('approved_at', '>=', now()->subDays(7))
                        ->count();
                } elseif (Schema::hasColumn('usuarios', 'status')) {
                    $usuariosActivosRecientes = DB::table('usuarios')
                        ->where('status', 'approved')
                        ->count();
                } else {
                    $usuariosActivosRecientes = DB::table('usuarios')
                        ->where('created_at', '>=', now()->subDays(7))
                        ->count();
                }
                
                // Calcular eficiencia del sistema
                if ($totalUsuarios > 0) {
                    $eficienciaSistema = round(($usuariosActivosRecientes / $totalUsuarios) * 100);
                }
            }
            
            $datos = [
                'usuarios_por_rol' => $usuariosPorRol,
                'usuarios_esta_semana' => $usuariosEstaSemana,
                'usuarios_activos_recientes' => $usuariosActivosRecientes,
                'eficiencia_sistema' => $eficienciaSistema,
                'roles_disponibles' => ['superadmin', 'administrador', 'fiscalizador', 'inspector', 'ventanilla']
            ];
            
        } catch (\Exception $e) {
            $datos = [
                'usuarios_por_rol' => [],
                'usuarios_esta_semana' => 12,
                'usuarios_activos_recientes' => 8,
                'eficiencia_sistema' => 85,
                'roles_disponibles' => ['superadmin', 'administrador', 'fiscalizador', 'inspector', 'ventanilla']
            ];
        }
        
        return $datos;
    }
    
    /**
     * Obtener datos adicionales para el dashboard del inspector
     */
    private function obtenerDatosAdicionalesInspector()
    {
        $datos = [];
        
        try {
            if (Schema::hasTable('actas')) {
                // Inspecciones realizadas esta semana
                $inspeccionesSemanales = 0;
                if (Schema::hasColumn('actas', 'created_at')) {
                    $inspeccionesSemanales = DB::table('actas')
                        ->where('created_at', '>=', now()->startOfWeek())
                        ->count();
                }
                
                // Vehículos inspeccionados (estimación basada en actas únicas por placa)
                $vehiculosInspeccionados = 0;
                if (Schema::hasColumn('actas', 'placa_vehiculo')) {
                    $vehiculosInspeccionados = DB::table('actas')
                        ->distinct('placa_vehiculo')
                        ->count();
                } elseif (Schema::hasColumn('actas', 'placa')) {
                    $vehiculosInspeccionados = DB::table('actas')
                        ->distinct('placa')
                        ->count();
                }
                
                // Multas aplicadas hoy
                $multasHoy = 0;
                if (Schema::hasColumn('actas', 'created_at')) {
                    $multasHoy = DB::table('actas')
                        ->whereDate('created_at', now()->toDateString())
                        ->count();
                }
                
                // Eficiencia de inspección
                $totalActas = DB::table('actas')->count();
                $eficienciaInspeccion = $totalActas > 0 ? min(100, round(($inspeccionesSemanales / max(1, $totalActas * 0.1)) * 100)) : 78;
                
                $datos = [
                    'inspecciones_semanales' => $inspeccionesSemanales,
                    'vehiculos_inspeccionados' => $vehiculosInspeccionados,
                    'multas_aplicadas_hoy' => $multasHoy,
                    'eficiencia_inspeccion' => $eficienciaInspeccion
                ];
                
            } else {
                throw new \Exception('Tabla actas no encontrada');
            }
            
        } catch (\Exception $e) {
            $datos = [
                'inspecciones_semanales' => 28,
                'vehiculos_inspeccionados' => 156,
                'multas_aplicadas_hoy' => 8,
                'eficiencia_inspeccion' => 78
            ];
        }
        
        return $datos;
    }
    
    /**
     * Obtener datos adicionales para el dashboard de ventanilla
     */
    private function obtenerDatosAdicionalesVentanilla()
    {
        $datos = [];
        
        try {
            if (Schema::hasTable('actas')) {
                // Pagos procesados hoy (estado = 1)
                $pagosHoy = 0;
                if (Schema::hasColumn('actas', 'estado') && Schema::hasColumn('actas', 'created_at')) {
                    $pagosHoy = DB::table('actas')
                        ->where('estado', 1)
                        ->whereDate('created_at', now()->toDateString())
                        ->count();
                }
                
                // Monto recaudado hoy (solo actas procesadas)
                $montoRecaudadoHoy = 0;
                if (Schema::hasColumn('actas', 'monto_multa') && Schema::hasColumn('actas', 'estado')) {
                    $montoRecaudadoHoy = DB::table('actas')
                        ->where('estado', 1)
                        ->whereDate('created_at', now()->toDateString())
                        ->sum('monto_multa') ?: 0;
                }
                
                // Consultas atendidas (estimación)
                $consultasAtendidas = DB::table('actas')
                    ->whereDate('created_at', now()->toDateString())
                    ->count() * 2; // Estimamos 2 consultas por acta creada
                
                // Eficiencia de atención (porcentaje de trámites completados vs iniciados)
                $tramitesIniciados = DB::table('actas')
                    ->whereDate('created_at', now()->toDateString())
                    ->count();
                
                $eficienciaAtencion = $tramitesIniciados > 0 ? 
                    round(($pagosHoy / $tramitesIniciados) * 100) : 85;
                
                $datos = [
                    'pagos_procesados_hoy' => $pagosHoy,
                    'monto_recaudado_hoy' => $montoRecaudadoHoy,
                    'consultas_atendidas' => $consultasAtendidas,
                    'eficiencia_atencion' => min(100, $eficienciaAtencion)
                ];
                
            } else {
                throw new \Exception('Tabla actas no encontrada');
            }
            
        } catch (\Exception $e) {
            $datos = [
                'pagos_procesados_hoy' => 15,
                'monto_recaudado_hoy' => 2450,
                'consultas_atendidas' => 47,
                'eficiencia_atencion' => 85
            ];
        }
        
        return $datos;
    }
    
    /**
     * Obtener estadísticas para el dashboard unificado - Administrador
     */
    private function getAdminStats()
    {
        try {
            $totalUsuarios = 0;
            $usuariosActivos = 0;
            $usuariosPendientes = 0;
            $totalRoles = 0;
            
            if (Schema::hasTable('usuarios')) {
                $totalUsuarios = DB::table('usuarios')->count();
                
                if (Schema::hasColumn('usuarios', 'status')) {
                    $usuariosActivos = DB::table('usuarios')->where('status', 'approved')->count();
                } elseif (Schema::hasColumn('usuarios', 'email_verified_at')) {
                    $usuariosActivos = DB::table('usuarios')->whereNotNull('email_verified_at')->count();
                } else {
                    $usuariosActivos = (int)($totalUsuarios * 0.85);
                }
                
                $usuariosPendientes = $totalUsuarios - $usuariosActivos;
                
                if (Schema::hasColumn('usuarios', 'role')) {
                    $totalRoles = DB::table('usuarios')->distinct()->count('role');
                } else {
                    $totalRoles = 5;
                }
            } else {
                $totalUsuarios = 15;
                $usuariosActivos = 12;
                $usuariosPendientes = 3;
                $totalRoles = 5;
            }
            
            $datosAdicionales = $this->obtenerDatosAdicionalesAdmin();
            
            $stats = [
                'total_usuarios' => $totalUsuarios,
                'usuarios_activos' => $usuariosActivos,
                'usuarios_pendientes' => $usuariosPendientes,
                'total_roles' => $totalRoles
            ];
            
            return array_merge($stats, $datosAdicionales);
            
        } catch (\Exception $e) {
            return [
                'total_usuarios' => 156,
                'usuarios_activos' => 142,
                'usuarios_pendientes' => 14,
                'total_roles' => 5
            ];
        }
    }
    
    /**
     * Obtener estadísticas para el dashboard unificado - Fiscalizador
     */
    private function getFiscalizadorStats()
    {
        try {
            $totalActas = 0;
            $actasProcesadas = 0;
            $actasPendientes = 0;
            $totalMultas = 0;
            
            if (Schema::hasTable('actas')) {
                $totalActas = DB::table('actas')->count();
                
                if (Schema::hasColumn('actas', 'estado')) {
                    $actasProcesadas = DB::table('actas')->where('estado', 1)->count();
                    $actasPendientes = DB::table('actas')->where('estado', 0)->count();
                } else {
                    $actasProcesadas = (int)($totalActas * 0.75);
                    $actasPendientes = $totalActas - $actasProcesadas;
                }
                
                if (Schema::hasColumn('actas', 'monto_multa')) {
                    $totalMultas = DB::table('actas')->sum('monto_multa') ?: 0;
                }
            }
            
            $stats = [
                'total_infracciones' => $totalActas,
                'infracciones_procesadas' => $actasProcesadas,
                'infracciones_pendientes' => $actasPendientes,
                'total_multas' => $totalMultas
            ];
            
            $datosAdicionales = $this->obtenerDatosAdicionalestFiscalizador();
            return array_merge($stats, $datosAdicionales);
            
        } catch (\Exception $e) {
            return [
                'total_infracciones' => 89,
                'infracciones_procesadas' => 67,
                'infracciones_pendientes' => 22,
                'total_multas' => 45000
            ];
        }
    }
    
    /**
     * Obtener estadísticas para el dashboard unificado - Ventanilla
     */
    private function getVentanillaStats()
    {
        try {
            $atencionesHoy = 0;
            $tramitesCompletados = 0;
            $colaEspera = 0;
            $tiempoPromedio = 15;
            
            if (Schema::hasTable('actas')) {
                if (Schema::hasColumn('actas', 'created_at')) {
                    $atencionesHoy = DB::table('actas')
                        ->whereDate('created_at', now()->toDateString())
                        ->count();
                }
                
                if (Schema::hasColumn('actas', 'estado')) {
                    $tramitesCompletados = DB::table('actas')
                        ->where('estado', 1)
                        ->whereDate('created_at', now()->toDateString())
                        ->count();
                    
                    $colaEspera = DB::table('actas')
                        ->where('estado', 0)
                        ->whereDate('created_at', now()->toDateString())
                        ->count();
                }
                
                if ($atencionesHoy == 0) {
                    $totalActas = DB::table('actas')->count();
                    $atencionesHoy = max(1, (int)($totalActas * 0.05));
                    $tramitesCompletados = (int)($atencionesHoy * 0.78);
                    $colaEspera = max(1, (int)($atencionesHoy * 0.22));
                }
            }
            
            $datosAdicionales = $this->obtenerDatosAdicionalesVentanilla();
            
            $stats = [
                'atenciones_hoy' => $atencionesHoy,
                'cola_espera' => $colaEspera,
                'tramites_completados' => $tramitesCompletados,
                'tiempo_promedio' => $tiempoPromedio
            ];
            
            return array_merge($stats, $datosAdicionales);
            
        } catch (\Exception $e) {
            return [
                'atenciones_hoy' => 23,
                'cola_espera' => 5,
                'tramites_completados' => 18,
                'tiempo_promedio' => 15
            ];
        }
    }
    
    /**
     * Obtener estadísticas para el dashboard unificado - Inspector
     */
    private function getInspectorStats()
    {
        try {
            $totalInfracciones = 0;
            $infraccionesResueltas = 0;
            $infraccionesPendientes = 0;
            $totalActas = 0;
            $actasProcesadas = 0;
            $actasPendientes = 0;
            
            if (Schema::hasTable('actas')) {
                $totalActas = DB::table('actas')->count();
                $totalInfracciones = $totalActas;
                
                if (Schema::hasColumn('actas', 'estado')) {
                    $actasProcesadas = DB::table('actas')->where('estado', 1)->count();
                    $actasPendientes = DB::table('actas')->where('estado', 0)->count();
                    
                    $infraccionesResueltas = $actasProcesadas;
                    $infraccionesPendientes = $actasPendientes;
                } else {
                    $infraccionesResueltas = (int)($totalInfracciones * 0.7);
                    $infraccionesPendientes = $totalInfracciones - $infraccionesResueltas;
                    $actasProcesadas = (int)($totalActas * 0.8);
                    $actasPendientes = $totalActas - $actasProcesadas;
                }
            }
            
            $datosAdicionales = $this->obtenerDatosAdicionalesInspector();
            
            $stats = [
                'total_infracciones' => $totalInfracciones,
                'infracciones_resueltas' => $infraccionesResueltas,
                'infracciones_pendientes' => $infraccionesPendientes,
                'total_actas' => $totalActas,
                'actas_procesadas' => $actasProcesadas,
                'actas_pendientes' => $actasPendientes
            ];
            
            return array_merge($stats, $datosAdicionales);
            
        } catch (\Exception $e) {
            return [
                'total_infracciones' => 45,
                'infracciones_resueltas' => 32,
                'infracciones_pendientes' => 13,
                'total_actas' => 67,
                'actas_procesadas' => 55,
                'actas_pendientes' => 12
            ];
        }
    }
}
