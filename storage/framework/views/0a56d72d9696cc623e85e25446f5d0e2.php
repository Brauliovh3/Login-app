<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h4><i class="fas fa-calendar"></i> Calendario de Actividades</h4>
                <button class="btn btn-secondary" onclick="hideModules()">
                    <i class="fas fa-arrow-left"></i> Volver al Dashboard
                </button>
            </div>
            <hr>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h6><i class="fas fa-calendar-alt"></i> Vista de Calendario</h6>
                </div>
                <div class="card-body">
                    <div class="calendar-container">
                        <div class="text-center mb-4">
                            <h5>Septiembre 2025</h5>
                            <div class="btn-group">
                                <button class="btn btn-outline-primary btn-sm">← Anterior</button>
                                <button class="btn btn-primary btn-sm">Hoy</button>
                                <button class="btn btn-outline-primary btn-sm">Siguiente →</button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Dom</th>
                                        <th>Lun</th>
                                        <th>Mar</th>
                                        <th>Mié</th>
                                        <th>Jue</th>
                                        <th>Vie</th>
                                        <th>Sáb</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-muted">1</td>
                                        <td>2</td>
                                        <td>3</td>
                                        <td>4</td>
                                        <td>5</td>
                                        <td>6</td>
                                        <td>7</td>
                                    </tr>
                                    <tr>
                                        <td>8</td>
                                        <td>9</td>
                                        <td>10</td>
                                        <td>11</td>
                                        <td>12</td>
                                        <td>13</td>
                                        <td>14</td>
                                    </tr>
                                    <tr>
                                        <td>15</td>
                                        <td>16</td>
                                        <td>17</td>
                                        <td class="bg-warning">18<br><small>Hoy</small></td>
                                        <td>19</td>
                                        <td>20</td>
                                        <td>21</td>
                                    </tr>
                                    <tr>
                                        <td>22</td>
                                        <td>23</td>
                                        <td>24</td>
                                        <td>25</td>
                                        <td>26</td>
                                        <td>27</td>
                                        <td>28</td>
                                    </tr>
                                    <tr>
                                        <td>29</td>
                                        <td>30</td>
                                        <td class="text-muted">1</td>
                                        <td class="text-muted">2</td>
                                        <td class="text-muted">3</td>
                                        <td class="text-muted">4</td>
                                        <td class="text-muted">5</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h6><i class="fas fa-tasks"></i> Actividades Próximas</h6>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <div class="list-group-item">
                            <strong>Inspección Rutinaria</strong><br>
                            <small class="text-muted">Mañana 09:00 - Av. Principal</small>
                        </div>
                        <div class="list-group-item">
                            <strong>Reunión de Coordinación</strong><br>
                            <small class="text-muted">Viernes 14:00 - Oficina</small>
                        </div>
                        <div class="list-group-item">
                            <strong>Operativo Especial</strong><br>
                            <small class="text-muted">Sábado 08:00 - Terminal</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header">
                    <h6><i class="fas fa-plus"></i> Nueva Actividad</h6>
                </div>
                <div class="card-body">
                    <form>
                        <div class="mb-3">
                            <label class="form-label">Título</label>
                            <input type="text" class="form-control" placeholder="Nombre de la actividad">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Fecha</label>
                            <input type="date" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Hora</label>
                            <input type="time" class="form-control">
                        </div>
                        <button type="button" class="btn btn-primary btn-sm" onclick="agregarActividad()">
                            <i class="fas fa-plus"></i> Agregar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function agregarActividad() {
    alert('Actividad agregada al calendario');
}
</script><?php /**PATH C:\xampp\htdocs\Login-app\resources\views\partials\modulos\calendario.blade.php ENDPATH**/ ?>