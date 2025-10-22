<?php 
$current_page = 'orden_dia';
ob_start(); 
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="bi bi-journal-text"></i>
        Órdenes del Día
    </h1>
    <a href="<?= BASE_URL ?>orden_dia/create" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i>
        Nueva Orden del Día
    </a>
</div>

<!-- Estadísticas -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Órdenes
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= $estadisticas['total'] ?? 0 ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-journal-text fs-2 text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Borradores
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= $estadisticas['borradores'] ?? 0 ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-file-earmark-text fs-2 text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Publicadas
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= $estadisticas['publicados'] ?? 0 ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-check-circle fs-2 text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Próximas Sesiones
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= $estadisticas['proximas_sesiones'] ?? 0 ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-calendar-event fs-2 text-info"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Lista de órdenes del día -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="bi bi-list"></i>
            Lista de Órdenes del Día
        </h6>
    </div>
    <div class="card-body">
        <?php if (!empty($ordenes)): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Acta N°</th>
                            <th>Fecha/Hora</th>
                            <th>Tipo</th>
                            <th>Estado</th>
                            <th>Creada por</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ordenes as $orden): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($orden['numero_acta']) ?></strong>
                            </td>
                            <td>
                                <div>
                                    <i class="bi bi-calendar"></i>
                                    <?= date('d/m/Y', strtotime($orden['fecha_sesion'])) ?>
                                </div>
                                <div class="text-muted">
                                    <i class="bi bi-clock"></i>
                                    <?= date('H:i', strtotime($orden['hora_sesion'])) ?>
                                </div>
                            </td>
                            <td>
                                <span class="badge <?= $orden['tipo_sesion'] === 'extraordinaria' ? 'bg-warning' : 'bg-info' ?>">
                                    <?= ucfirst($orden['tipo_sesion']) ?>
                                </span>
                            </td>
                            <td>
                                <?php
                                $badgeClass = 'bg-secondary';
                                switch($orden['estado']) {
                                    case 'borrador': $badgeClass = 'bg-warning'; break;
                                    case 'publicado': $badgeClass = 'bg-success'; break;
                                    case 'archivado': $badgeClass = 'bg-secondary'; break;
                                }
                                ?>
                                <span class="badge <?= $badgeClass ?>">
                                    <?= ucfirst($orden['estado']) ?>
                                </span>
                            </td>
                            <td>
                                <?= htmlspecialchars($orden['created_by_name'] ?? 'N/A') ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="<?= BASE_URL ?>orden_dia/view/<?= $orden['id'] ?>" 
                                       class="btn btn-outline-info" title="Ver">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="<?= BASE_URL ?>orden_dia/edit/<?= $orden['id'] ?>" 
                                       class="btn btn-outline-primary" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="<?= BASE_URL ?>orden_dia/delete/<?= $orden['id'] ?>" 
                                       class="btn btn-outline-danger" title="Eliminar"
                                       onclick="event.preventDefault(); SweetAlerts.confirmDelete('esta orden del día').then((result) => { if (result.isConfirmed) window.location.href = this.href; });">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-4">
                <i class="bi bi-journal-text fs-1 text-muted"></i>
                <p class="text-muted mt-2">No hay órdenes del día registradas</p>
                <a href="<?= BASE_URL ?>orden_dia/create" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i>
                    Crear Primera Orden del Día
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php 
$content = ob_get_clean();
include VIEWS_PATH . 'layouts/main.php';
?>