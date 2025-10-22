<?php 
$current_page = 'votacion';
ob_start(); 
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="bi bi-clock-history"></i>
        Historial de Votaciones
    </h1>
    <a href="<?= BASE_URL ?>votacion" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i>
        Volver a Votaciones
    </a>
</div>

<!-- Filtros -->
<div class="card shadow mb-4">
    <div class="card-header bg-primary text-white">
        <h6 class="mb-0">
            <i class="bi bi-funnel"></i>
            Filtros de Búsqueda
        </h6>
    </div>
    <div class="card-body">
        <form method="GET" action="<?= BASE_URL ?>votacion/historial">
            <div class="row">
                <div class="col-md-3">
                    <label for="sesion_id" class="form-label">Sesión</label>
                    <select class="form-select" name="sesion_id" id="sesion_id">
                        <option value="">Todas las sesiones</option>
                        <?php foreach ($sesiones as $sesion): ?>
                        <option value="<?= $sesion['id'] ?>" 
                                <?= isset($filtros['sesion_id']) && $filtros['sesion_id'] == $sesion['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($sesion['nombre_sesion']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="resultado" class="form-label">Resultado</label>
                    <select class="form-select" name="resultado" id="resultado">
                        <option value="">Todos</option>
                        <option value="aprobado" <?= isset($filtros['resultado']) && $filtros['resultado'] == 'aprobado' ? 'selected' : '' ?>>
                            Aprobado
                        </option>
                        <option value="rechazado" <?= isset($filtros['resultado']) && $filtros['resultado'] == 'rechazado' ? 'selected' : '' ?>>
                            Rechazado
                        </option>
                        <option value="empate" <?= isset($filtros['resultado']) && $filtros['resultado'] == 'empate' ? 'selected' : '' ?>>
                            Empate
                        </option>
                        <option value="pendiente" <?= isset($filtros['resultado']) && $filtros['resultado'] == 'pendiente' ? 'selected' : '' ?>>
                            Pendiente
                        </option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="fecha_desde" class="form-label">Desde</label>
                    <input type="date" class="form-control" name="fecha_desde" id="fecha_desde"
                           value="<?= $filtros['fecha_desde'] ?? '' ?>">
                </div>
                <div class="col-md-2">
                    <label for="fecha_hasta" class="form-label">Hasta</label>
                    <input type="date" class="form-control" name="fecha_hasta" id="fecha_hasta"
                           value="<?= $filtros['fecha_hasta'] ?? '' ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i>
                            Buscar
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Resultados -->
<div class="card shadow">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">
            Historial de Votaciones 
            <span class="badge bg-primary"><?= count($historial) ?></span>
        </h6>
        <button class="btn btn-sm btn-outline-primary" onclick="exportarHistorial()">
            <i class="bi bi-download"></i>
            Exportar
        </button>
    </div>
    <div class="card-body">
        <?php if (empty($historial)): ?>
        <div class="text-center py-4">
            <i class="bi bi-archive fa-3x text-gray-300 mb-3"></i>
            <h5 class="text-gray-600">No hay resultados</h5>
            <p class="text-gray-500">No se encontraron votaciones con los filtros aplicados</p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Sesión</th>
                        <th>Ítem Votado</th>
                        <th>Votos</th>
                        <th>Resultado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($historial as $registro): ?>
                    <tr>
                        <td>
                            <div>
                                <?= date('d/m/Y', strtotime($registro['fecha_apertura'])) ?>
                            </div>
                            <small class="text-muted">
                                <?= date('H:i', strtotime($registro['fecha_apertura'])) ?>
                            </small>
                        </td>
                        <td>
                            <div class="font-weight-bold">
                                <?= htmlspecialchars($registro['nombre_sesion']) ?>
                            </div>
                            <small class="text-muted">
                                Acta N° <?= htmlspecialchars($registro['numero_acta']) ?>
                            </small>
                        </td>
                        <td>
                            <?php if ($registro['item_votacion_tipo'] === 'actas'): ?>
                                <span class="badge bg-info">GLOBAL</span>
                                Lectura de Actas
                            <?php else: ?>
                                <span class="badge bg-primary">EXPTE</span>
                                <?= htmlspecialchars($registro['numero_expediente']) ?>
                                <br>
                                <small><?= htmlspecialchars(substr($registro['extracto_expediente'], 0, 100)) ?>...</small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="d-flex justify-content-around">
                                <span class="badge bg-success">
                                    <i class="bi bi-hand-thumbs-up"></i>
                                    <?= $registro['votos_positivos'] ?>
                                </span>
                                <span class="badge bg-danger">
                                    <i class="bi bi-hand-thumbs-down"></i>
                                    <?= $registro['votos_negativos'] ?>
                                </span>
                                <span class="badge bg-warning text-dark">
                                    <i class="bi bi-dash-circle"></i>
                                    <?= $registro['votos_abstenciones'] ?>
                                </span>
                            </div>
                            <small class="text-muted">
                                Total: <?= $registro['votos_positivos'] + $registro['votos_negativos'] + $registro['votos_abstenciones'] ?> 
                                de <?= $registro['total_presentes'] ?>
                            </small>
                        </td>
                        <td>
                            <?php
                            $badgeClass = 'secondary';
                            $icon = 'bi-clock';
                            
                            switch ($registro['resultado']) {
                                case 'aprobado':
                                    $badgeClass = 'success';
                                    $icon = 'bi-check-circle';
                                    break;
                                case 'rechazado':
                                    $badgeClass = 'danger';
                                    $icon = 'bi-x-circle';
                                    break;
                                case 'empate':
                                    $badgeClass = 'warning';
                                    $icon = 'bi-pause-circle';
                                    break;
                                case 'pendiente':
                                    $badgeClass = 'secondary';
                                    $icon = 'bi-clock';
                                    break;
                            }
                            ?>
                            <span class="badge bg-<?= $badgeClass ?>">
                                <i class="<?= $icon ?>"></i>
                                <?= ucfirst($registro['resultado']) ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?= BASE_URL ?>votacion/resultados/<?= $registro['sesion_id'] ?>/<?= $registro['item_votacion_tipo'] ?>/<?= $registro['item_votacion_id'] ?>" 
                               class="btn btn-sm btn-outline-primary" title="Ver Detalle">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="<?= BASE_URL ?>votacion/sesion/<?= $registro['sesion_id'] ?>" 
                               class="btn btn-sm btn-outline-secondary" title="Ver Sesión">
                                <i class="bi bi-house-door"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function exportarHistorial() {
    // Implementar exportación a CSV/PDF
    alert('Función de exportación en desarrollo');
}
</script>

<?php 
$content = ob_get_clean();
include VIEWS_PATH . 'layouts/main.php';
?>