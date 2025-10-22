<?php 
$current_page = 'orden_dia';
ob_start(); 
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="bi bi-plus-circle"></i>
        Nueva Orden del Día
    </h1>
    <a href="<?= BASE_URL ?>orden_dia" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i>
        Volver a Lista
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-file-earmark-plus"></i>
                    Información Básica
                </h6>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= BASE_URL ?>orden_dia/create">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="numero_acta" class="form-label">
                                    <i class="bi bi-hash"></i>
                                    Número de Acta *
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="numero_acta" 
                                       name="numero_acta" 
                                       placeholder="Ej: 2025-001"
                                       value="<?= htmlspecialchars($_POST['numero_acta'] ?? '') ?>"
                                       required>
                                <div class="form-text">
                                    Número único que identifica esta acta
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tipo_sesion" class="form-label">
                                    <i class="bi bi-tags"></i>
                                    Tipo de Sesión
                                </label>
                                <select class="form-select" id="tipo_sesion" name="tipo_sesion">
                                    <option value="ordinaria" <?= ($_POST['tipo_sesion'] ?? 'ordinaria') === 'ordinaria' ? 'selected' : '' ?>>
                                        Ordinaria
                                    </option>
                                    <option value="extraordinaria" <?= ($_POST['tipo_sesion'] ?? '') === 'extraordinaria' ? 'selected' : '' ?>>
                                        Extraordinaria
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fecha_sesion" class="form-label">
                                    <i class="bi bi-calendar"></i>
                                    Fecha de Sesión *
                                </label>
                                <input type="date" 
                                       class="form-control" 
                                       id="fecha_sesion" 
                                       name="fecha_sesion" 
                                       value="<?= htmlspecialchars($_POST['fecha_sesion'] ?? '') ?>"
                                       required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="hora_sesion" class="form-label">
                                    <i class="bi bi-clock"></i>
                                    Hora de Sesión *
                                </label>
                                <input type="time" 
                                       class="form-control" 
                                       id="hora_sesion" 
                                       name="hora_sesion" 
                                       value="<?= htmlspecialchars($_POST['hora_sesion'] ?? '09:00') ?>"
                                       required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="<?= BASE_URL ?>orden_dia" class="btn btn-secondary me-md-2">
                            <i class="bi bi-x-circle"></i>
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i>
                            Crear Orden del Día
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-info">
                    <i class="bi bi-info-circle"></i>
                    Información
                </h6>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h6><i class="bi bi-lightbulb"></i> ¿Qué pasa después?</h6>
                    <p class="mb-2">Una vez creada la orden del día:</p>
                    <ul class="mb-0">
                        <li>Se generarán automáticamente todos los ítems estándar</li>
                        <li>Podrás agregar expedientes, actas y detalles</li>
                        <li>El estado inicial será "Borrador"</li>
                        <li>Podrás publicarla cuando esté completa</li>
                    </ul>
                </div>
                
                <div class="mt-3">
                    <h6><i class="bi bi-list-ol"></i> Ítems que se crearán:</h6>
                    <div class="list-group list-group-flush">
                        <div class="list-group-item px-0 py-1">
                            <small>1º) Izamiento del Pabellón Nacional</small>
                        </div>
                        <div class="list-group-item px-0 py-1">
                            <small>2º) Lectura Orden del Día</small>
                        </div>
                        <div class="list-group-item px-0 py-1">
                            <small>3º) Lectura y Consideración de Actas</small>
                        </div>
                        <div class="list-group-item px-0 py-1">
                            <small>4º) Expedientes Fuera de Término</small>
                        </div>
                        <div class="text-muted text-center py-1">
                            <small>... y 10 ítems más</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean();
include VIEWS_PATH . 'layouts/main.php';
?>