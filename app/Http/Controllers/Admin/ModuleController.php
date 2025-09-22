<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Acta;
use App\Models\Conductor;
use App\Models\Empresa;
use App\Models\Inspector;

class ModuleController extends Controller
{
    /**
     * Manejar la carga de módulos de administración
     */
    public function handleModule($module)
    {
        switch($module) {
            case 'gestionar-usuarios':
                return $this->gestionarUsuarios();
                
            case 'aprobar-usuarios':
                return $this->aprobarUsuarios();
                
            case 'infracciones':
                return $this->infracciones();
                
            case 'mantenimiento-conductores':
                return $this->mantenimientoConductores();
                
            case 'mantenimiento-inspectores':
                return $this->mantenimientoInspectores();
                
            default:
                abort(404);
        }
    }

    private function gestionarUsuarios()
    {
        $usuarios = User::all();
        return view('administrador.gestionar-usuarios', compact('usuarios'));
    }

    private function aprobarUsuarios()
    {
        $usuarios_pendientes_lista = User::whereNull('approved_at')
            ->whereNull('approval_status')
            ->where('created_at', '>', now()->subDays(7))
            ->orderBy('created_at', 'desc')
            ->get();
        $usuarios_pendientes = $usuarios_pendientes_lista->count();
        return view('administrador.aprobar-usuarios', compact('usuarios_pendientes_lista', 'usuarios_pendientes'));
    }

    private function infracciones()
    {
        $infracciones = Acta::all();
        $total_infracciones = $infracciones->count();
        $pendientes = $infracciones->where('estado', 1)->count();
        $resueltas = $infracciones->where('estado', 0)->count();
        $este_mes = $infracciones->where('created_at', '>=', now()->startOfMonth())->count();
        return view('administrador.infracciones', compact('infracciones', 'total_infracciones', 'pendientes', 'resueltas', 'este_mes'));
    }

    private function mantenimientoConductores()
    {
        $conductores = Conductor::with('empresa')->get();
        $empresas = Empresa::all();
        return view('administrador.mantenimiento.conductores', compact('conductores', 'empresas'));
    }

    private function mantenimientoInspectores()
    {
        $inspectores = Inspector::all();
        return view('administrador.mantenimiento.fiscal', compact('inspectores'));
    }
}