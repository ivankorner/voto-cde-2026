<?php 
$current_page = 'orden_dia_lista';
ob_start(); 
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="bi bi-list-ul"></i>
        Órdenes del Día Publicadas
    </h1>
</div>

<?php if (!empty($ordenes)): ?>
    <div class="row">
        <?php foreach ($ordenes as $orden): ?>
        <div class="col-lg-6 col-xl-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="bi bi-journal-text"></i>
                            Acta N° <?= htmlspecialchars($orden['numero_acta']) ?>
                        </h6>
                        <span class="badge <?= $orden['tipo_sesion'] === 'extraordinaria' ? 'bg-warning' : 'bg-light text-dark' ?>">
                            <?= ucfirst($orden['tipo_sesion']) ?>
                        </span>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="row text-center mb-3">
                        <div class="col-6">
                            <div class="border rounded p-2">
                                <h5 class="text-primary mb-0">
                                    <i class="bi bi-calendar"></i>
                                    <?= date('d/m', strtotime($orden['fecha_sesion'])) ?>
                                </h5>
                                <small class="text-muted"><?= strftime('%B %Y', strtotime($orden['fecha_sesion'])) ?></small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-2">
                                <h5 class="text-success mb-0">
                                    <i class="bi bi-clock"></i>
                                    <?= date('H:i', strtotime($orden['hora_sesion'])) ?>
                                </h5>
                                <small class="text-muted">Hora</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center">
                        <span class="badge bg-success mb-2">
                            <i class="bi bi-check-circle"></i>
                            <?= ucfirst($orden['estado']) ?>
                        </span>
                        <p class="text-muted mb-0">
                            <small>
                                <i class="bi bi-person"></i>
                                Creada por: <?= htmlspecialchars($orden['created_by_name']) ?>
                            </small>
                        </p>
                    </div>
                </div>
                
                <div class="card-footer">
                    <div class="d-grid">
                        <a href="<?= BASE_URL ?>orden_dia/view/<?= $orden['id'] ?>" 
                           class="btn btn-primary">
                            <i class="bi bi-eye"></i>
                            Ver Orden del Día
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <div class="text-center mt-4">
        <p class="text-muted">
            <i class="bi bi-info-circle"></i>
            Solo se muestran las órdenes del día con estado "Publicado"
        </p>
    </div>
    
<?php else: ?>
    <div class="card shadow">
        <div class="card-body text-center py-5">
            <i class="bi bi-journal-x fs-1 text-muted mb-3"></i>
            <h4 class="text-muted">No hay órdenes del día publicadas</h4>
            <p class="text-muted">
                Las órdenes del día aparecerán aquí una vez que sean publicadas por el administrador.
            </p>
            
            <?php if ($_SESSION['user_role'] === 'admin'): ?>
            <div class="mt-4">
                <a href="<?= BASE_URL ?>orden_dia" class="btn btn-primary">
                    <i class="bi bi-gear"></i>
                    Ir a Gestión de Órdenes
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<?php 
$content = ob_get_clean();
include VIEWS_PATH . 'layouts/main.php';
?>