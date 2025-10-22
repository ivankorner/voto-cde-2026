<?php 
$current_page = 'votacion';
ob_start(); 
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="bi bi-vote-fill"></i>
        Gestión de Votaciones
    </h1>
    <a href="<?= BASE_URL ?>index.php?url=votacion/crear" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i>
        Nueva Sesión de Votación
    </a>
</div>

<!-- Mensajes Flash -->
<?php if (isset($_SESSION['flash_success'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            SweetAlerts.success('¡Éxito!', '<?= htmlspecialchars($_SESSION['flash_success']) ?>');
        });
    </script>
    <?php unset($_SESSION['flash_success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['flash_error'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            SweetAlerts.error('Error', '<?= htmlspecialchars($_SESSION['flash_error']) ?>');
        });
    </script>
    <?php unset($_SESSION['flash_error']); ?>
<?php endif; ?>

<!-- Estadísticas rápidas -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Sesiones Activas
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= count(array_filter($sesiones, function($s) { return in_array($s['estado'], ['planificada', 'preparacion', 'activa', 'pausada']); })) ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-play-circle fa-2x text-gray-300"></i>
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
                            Sesiones Finalizadas
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= count(array_filter($sesiones, function($s) { return $s['estado'] === 'finalizada'; })) ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-check-circle fa-2x text-gray-300"></i>
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
                            Total Sesiones
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= count($sesiones) ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-collection fa-2x text-gray-300"></i>
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
                            <a href="<?= BASE_URL ?>index.php?url=votacion/historial" class="text-decoration-none">Historial</a>
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <i class="bi bi-clock-history"></i>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-archive fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Lista de sesiones -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Sesiones de Votación</h6>
    </div>
    <div class="card-body">
        <?php if (empty($sesiones)): ?>
        <div class="text-center py-4">
            <i class="bi bi-inbox fa-3x text-gray-300 mb-3"></i>
            <h5 class="text-gray-600">No hay sesiones de votación</h5>
            <p class="text-gray-500">Crea una nueva sesión para comenzar a votar</p>
            <a href="<?= BASE_URL ?>index.php?url=votacion/crear" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i>
                Crear Primera Sesión
            </a>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Sesión</th>
                        <th>Orden del Día</th>
                        <th>Estado</th>
                        <th>Fecha Creación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sesiones as $sesion): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="mr-3">
                                    <?php
                                    $iconClass = 'bi bi-circle';
                                    $iconColor = 'text-secondary';
                                    
                                    switch ($sesion['estado']) {
                                        case 'activa':
                                            $iconClass = 'bi bi-play-circle-fill';
                                            $iconColor = 'text-success';
                                            break;
                                        case 'pausada':
                                            $iconClass = 'bi bi-pause-circle-fill';
                                            $iconColor = 'text-warning';
                                            break;
                                        case 'finalizada':
                                            $iconClass = 'bi bi-check-circle-fill';
                                            $iconColor = 'text-info';
                                            break;
                                        case 'planificada':
                                        case 'preparacion':
                                            $iconClass = 'bi bi-clock-fill';
                                            $iconColor = 'text-primary';
                                            break;
                                    }
                                    ?>
                                    <i class="<?= $iconClass ?> <?= $iconColor ?>"></i>
                                </div>
                                <div>
                                    <div class="font-weight-bold"><?= htmlspecialchars($sesion['nombre']) ?></div>
                                    <?php if (!empty($sesion['descripcion'])): ?>
                                    <small class="text-gray-600"><?= htmlspecialchars($sesion['descripcion']) ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php if (!empty($sesion['numero_acta'])): ?>
                            <div>
                                <strong>Acta N° <?= htmlspecialchars($sesion['numero_acta']) ?></strong>
                            </div>
                            <small class="text-gray-600">
                                <?= ucfirst($sesion['tipo_sesion'] ?? 'ordinaria') ?> - 
                                <?= date('d/m/Y', strtotime($sesion['fecha_sesion'])) ?>
                            </small>
                            <?php else: ?>
                            <small class="text-muted">Sin orden del día asignada</small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge badge-<?= 
                                $sesion['estado'] === 'activa' ? 'success' : 
                                ($sesion['estado'] === 'pausada' ? 'warning' : 
                                ($sesion['estado'] === 'finalizada' ? 'info' : 'primary')) 
                            ?>">
                                <?= $sesion['estado'] === 'preparacion' ? 'Planificada' : ucfirst($sesion['estado']) ?>
                            </span>
                        </td>
                        <td>
                            <?= date('d/m/Y H:i', strtotime($sesion['created_at'])) ?>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <!-- Botón Ver siempre visible -->
                                <a href="<?= BASE_URL ?>index.php?url=votacion/sesion/<?= $sesion['id'] ?>" 
                                   class="btn btn-sm btn-primary" title="Ver Sala de Votación">
                                    <i class="bi bi-eye"></i>
                                </a>
                                
                                <!-- Panel de control para sesiones activas o pausadas -->
                                <?php if (in_array($sesion['estado'], ['activa', 'pausada'])): ?>
                                <a href="<?= BASE_URL ?>index.php?url=votacion/panel-control/<?= $sesion['id'] ?>" 
                                   class="btn btn-sm btn-info" title="Control Progresivo de Puntos">
                                    <i class="bi bi-list-task"></i>
                                </a>
                                <?php endif; ?>
                                
                                <!-- Botones para sesiones en PREPARACION o PLANIFICADA -->
                                <?php if (in_array($sesion['estado'], ['planificada', 'preparacion'])): ?>
                                <a href="<?= BASE_URL ?>index.php?url=votacion/iniciarSesion/<?= $sesion['id'] ?>" 
                                   class="btn btn-sm btn-success" title="Iniciar Sesión"
                                   onclick="event.preventDefault(); SweetAlerts.confirm('¿Iniciar sesión de votación?', '¿Está seguro de iniciar esta sesión de votación?', 'Sí, iniciar').then((result) => { if (result.isConfirmed) window.location.href = this.href; });">
                                    <i class="bi bi-play-fill"></i>
                                </a>
                                
                                <!-- Botón eliminar para sesiones planificadas -->
                                <form method="POST" action="<?= BASE_URL ?>index.php?url=votacion/eliminarSesion/<?= $sesion['id'] ?>" class="d-inline">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                    <button type="button" class="btn btn-sm btn-danger" title="Eliminar Sesión"
                                            onclick="SweetAlerts.confirmDanger('¿Eliminar sesión?', '¿Está seguro de eliminar esta sesión? Esta acción no se puede deshacer.', 'Sí, eliminar').then((result) => { if (result.isConfirmed) this.form.submit(); });">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                
                                <!-- Botones para sesiones ACTIVAS -->
                                <?php elseif ($sesion['estado'] === 'activa'): ?>
                                <a href="<?= BASE_URL ?>index.php?url=votacion/pausarSesion/<?= $sesion['id'] ?>" 
                                   class="btn btn-sm btn-warning" title="Pausar Sesión"
                                   onclick="event.preventDefault(); SweetAlerts.confirm('¿Pausar sesión?', '¿Está seguro de pausar esta sesión?', 'Sí, pausar').then((result) => { if (result.isConfirmed) window.location.href = this.href; });">
                                    <i class="bi bi-pause-fill"></i>
                                </a>
                                <a href="<?= BASE_URL ?>index.php?url=votacion/finalizarSesion/<?= $sesion['id'] ?>" 
                                   class="btn btn-sm btn-danger" title="Finalizar Sesión"
                                   onclick="event.preventDefault(); SweetAlerts.confirmDanger('¿Finalizar sesión?', '¿Está seguro de finalizar esta sesión? Esta acción no se puede deshacer.', 'Sí, finalizar').then((result) => { if (result.isConfirmed) window.location.href = this.href; });">
                                    <i class="bi bi-stop-fill"></i>
                                </a>
                                
                                <!-- Botones para sesiones PAUSADAS -->
                                <?php elseif ($sesion['estado'] === 'pausada'): ?>
                                <a href="<?= BASE_URL ?>index.php?url=votacion/iniciarSesion/<?= $sesion['id'] ?>" 
                                   class="btn btn-sm btn-success" title="Reanudar Sesión"
                                   onclick="event.preventDefault(); SweetAlerts.confirm('¿Reanudar sesión?', '¿Está seguro de reanudar esta sesión?', 'Sí, reanudar').then((result) => { if (result.isConfirmed) window.location.href = this.href; });">
                                    <i class="bi bi-play-fill"></i>
                                </a>
                                <a href="<?= BASE_URL ?>index.php?url=votacion/finalizarSesion/<?= $sesion['id'] ?>" 
                                   class="btn btn-sm btn-danger" title="Finalizar Sesión"
                                   onclick="event.preventDefault(); SweetAlerts.confirmDanger('¿Finalizar sesión?', '¿Está seguro de finalizar esta sesión? Esta acción no se puede deshacer.', 'Sí, finalizar').then((result) => { if (result.isConfirmed) window.location.href = this.href; });">
                                    <i class="bi bi-stop-fill"></i>
                                </a>
                                
                                <!-- Botones para sesiones FINALIZADAS -->
                                <?php elseif ($sesion['estado'] === 'finalizada'): ?>
                                <!-- Botón eliminar solo para sesiones finalizadas sin votos -->
                                <form method="POST" action="<?= BASE_URL ?>index.php?url=votacion/eliminarSesion/<?= $sesion['id'] ?>" class="d-inline">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                    <button type="button" class="btn btn-sm btn-danger" title="Eliminar Sesión"
                                            onclick="SweetAlerts.confirmDanger('¿Eliminar sesión?', '⚠️ Esta acción eliminará permanentemente la sesión y todos sus datos relacionados.\\n\\n¿Está seguro de continuar?', 'Sí, eliminar').then((result) => { if (result.isConfirmed) this.form.submit(); });">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php 
$content = ob_get_clean();
include VIEWS_PATH . 'layouts/main.php';
?>