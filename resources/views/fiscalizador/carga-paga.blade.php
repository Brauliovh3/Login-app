@extends('layouts.dashboard')

@section('title', 'Carga y Paga')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-0">
                <i class="fas fa-truck me-2" style="color: #ff8c00;"></i>
                Gestión de Carga y Paga
            </h2>
        </div>
    </div>

    <!-- Flexbox de opciones principales -->
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card h-100" style="border-color: #ff8c00;">
                <div class="card-header text-center" style="background-color: #ff8c00; color: white;">
                    <h5><i class="fas fa-weight-hanging me-2"></i>Control de Carga</h5>
                </div>
                <div class="card-body text-center">
                    <p>Gestionar el control de peso y dimensiones de vehículos de carga</p>
                    <button class="btn btn-primary btn-lg">
                        <i class="fas fa-plus me-2"></i>Nuevo Control
                    </button>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card h-100" style="border-color: #ff8c00;">
                <div class="card-header text-center" style="background-color: #ff8c00; color: white;">
                    <h5><i class="fas fa-credit-card me-2"></i>Pagos y Multas</h5>
                </div>
                <div class="card-body text-center">
                    <p>Registro de pagos de multas y verificación de comprobantes</p>
                    <button class="btn btn-success btn-lg">
                        <i class="fas fa-dollar-sign me-2"></i>Registrar Pago
                    </button>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card h-100" style="border-color: #ff8c00;">
                <div class="card-header text-center" style="background-color: #ff8c00; color: white;">
                    <h5><i class="fas fa-balance-scale me-2"></i>Verificación</h5>
                </div>
                <div class="card-body text-center">
                    <p>Verificar estado de pagos y validar documentación</p>
                    <button class="btn btn-info btn-lg">
                        <i class="fas fa-search me-2"></i>Verificar Estado
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de registros recientes -->
    <div class="card mt-4">
        <div class="card-header" style="background-color: #fff3e0; border-color: #ff8c00;">
            <h5 class="mb-0" style="color: #ff8c00;">
                <i class="fas fa-list me-2"></i>Registros Recientes
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead style="background-color: #ff8c00; color: white;">
                        <tr>
                            <th>Fecha</th>
                            <th>Placa</th>
                            <th>Tipo</th>
                            <th>Peso/Monto</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>30/07/2025</td>
                            <td>ABC-123</td>
                            <td>Control de Carga</td>
                            <td>15.5 TN</td>
                            <td><span class="badge bg-success">Aprobado</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>30/07/2025</td>
                            <td>XYZ-789</td>
                            <td>Pago de Multa</td>
                            <td>S/ 462.00</td>
                            <td><span class="badge bg-info">Procesado</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
