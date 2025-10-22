<?php 
$current_page = 'orden_dia_lista';
ob_start(); 
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="bi bi-eye"></i>
        Orden del Día - Acta N° <?= htmlspecialchars($orden['numero_acta']) ?>
    </h1>
    <div>
        <?php if ($_SESSION['user_role'] === 'admin'): ?>
        <a href="<?= BASE_URL ?>orden_dia/edit/<?= $orden['id'] ?>" class="btn btn-primary">
            <i class="bi bi-pencil"></i>
            Editar
        </a>
        <a href="<?= BASE_URL ?>votacion/crear?orden_dia_id=<?= $orden['id'] ?>" class="btn btn-success">
            <i class="bi bi-vote-fill"></i>
            Crear Sesión de Votación
        </a>
        <?php endif; ?>
        <a href="<?= BASE_URL ?>orden_dia/lista" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i>
            Volver a Lista
        </a>
    </div>
</div>

<!-- Información de la sesión -->
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="row text-center">
            <div class="col-md-3">
                <div class="border rounded p-3">
                    <h4 class="text-primary mb-1">
                        <i class="bi bi-calendar-event"></i>
                        <?= date('d/m/Y', strtotime($orden['fecha_sesion'])) ?>
                    </h4>
                    <p class="text-muted mb-0">Fecha de Sesión</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="border rounded p-3">
                    <h4 class="text-success mb-1">
                        <i class="bi bi-clock"></i>
                        <?= date('H:i', strtotime($orden['hora_sesion'])) ?>
                    </h4>
                    <p class="text-muted mb-0">Hora</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="border rounded p-3">
                    <h4 class="text-info mb-1">
                        <i class="bi bi-tag"></i>
                        <?= ucfirst($orden['tipo_sesion']) ?>
                    </h4>
                    <p class="text-muted mb-0">Tipo de Sesión</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="border rounded p-3">
                    <h4 class="mb-1">
                        <span class="badge <?= $orden['estado'] === 'publicado' ? 'bg-success' : ($orden['estado'] === 'borrador' ? 'bg-warning' : 'bg-secondary') ?> fs-6">
                            <?= ucfirst($orden['estado']) ?>
                        </span>
                    </h4>
                    <p class="text-muted mb-0">Estado</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Orden del día -->
<div class="card shadow">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0 text-center">
            <i class="bi bi-list-ol"></i>
            ORDEN DEL DÍA
        </h5>
    </div>
    <div class="card-body">
        <?php foreach ($items as $item): ?>
        <div class="mb-4">
            <div class="d-flex align-items-start">
                <div class="flex-shrink-0">
                    <span class="badge bg-primary fs-6 me-3"><?= $item['numero_orden'] ?>º</span>
                </div>
                <div class="flex-grow-1">
                    <h5 class="mb-2"><?= htmlspecialchars($item['titulo']) ?></h5>
                    
                    <div class="alert alert-light py-2">
                        <small><i class="bi bi-info-circle"></i> 
                            <?php 
                            $descripcion = isset($item['descripcion']) ? trim($item['descripcion']) : '';
                            if (!empty($descripcion)): 
                            ?>
                                <?= nl2br(htmlspecialchars($descripcion)) ?>
                            <?php else: ?>
                                <em class="text-muted">Sin descripción adicional</em>
                            <?php endif; ?>
                        </small>
                    </div>
                    
                    <?php if ($item['tipo_item'] === 'lectura_actas' && !empty($item['actas'])): ?>
                        <div class="ms-3">
                            <h6 class="text-muted mb-2"><i class="bi bi-file-earmark-text"></i> Actas a considerar:</h6>
                            <?php foreach ($item['actas'] as $acta): ?>
                            <div class="ps-3 mb-2">
                                <i class="bi bi-dot"></i>
                                <strong>Acta N° <?= htmlspecialchars($acta['numero_acta']) ?></strong>
                                (<?= htmlspecialchars($acta['tipo_sesion']) ?> - <?= date('d/m/Y', strtotime($acta['fecha_acta'])) ?>)
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                    <?php elseif ($item['tipo_item'] === 'espacio_ciudadano' && !empty($item['expedientes'])): ?>
                        <div class="ms-3">
                            <?php foreach ($item['expedientes'] as $ciudadano): ?>
                            <div class="ps-3 mb-1">
                                <i class="bi bi-person"></i>
                                <?= htmlspecialchars($ciudadano['nombre_ciudadano']) ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                    <?php elseif (!empty($item['expedientes'])): ?>
                        <div class="ms-3">
                            <?php foreach ($item['expedientes'] as $expediente): ?>
                            <div class="card border-start border-3 border-primary mb-3">
                                <div class="card-body py-2">
                                    <h6 class="card-title mb-1">
                                        <i class="bi bi-file-earmark"></i>
                                        EXPTE. N° <?= htmlspecialchars($expediente['numero_expediente']) ?>:
                                    </h6>
                                    
                                    <?php if ($expediente['bloque_autor'] || $expediente['concejal_autor']): ?>
                                    <p class="mb-1">
                                        <?php if ($expediente['bloque_autor']): ?>
                                            <strong>Bloque <?= htmlspecialchars($expediente['bloque_autor']) ?></strong>
                                        <?php endif; ?>
                                        <?php if ($expediente['concejal_autor']): ?>
                                            <?= $expediente['bloque_autor'] ? ' - ' : '' ?>
                                            (<?= htmlspecialchars($expediente['concejal_autor']) ?>)
                                        <?php endif; ?>
                                        .
                                    </p>
                                    <?php endif; ?>
                                    
                                    <p class="mb-1"><?= nl2br(htmlspecialchars($expediente['extracto'])) ?></p>
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <?php if ($expediente['comision']): ?>
                                        <small class="text-muted">
                                            <i class="bi bi-archive"></i>
                                            <?= htmlspecialchars($expediente['comision']) ?>
                                        </small>
                                        <?php endif; ?>
                                        
                                        <?php if ($expediente['tipo_instrumento']): ?>
                                        <span class="badge bg-secondary">
                                            <?= ucfirst($expediente['tipo_instrumento']) ?>
                                        </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if ($item !== end($items)): ?>
            <hr class="my-4">
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <div class="card-footer bg-light text-center">
        <small class="text-muted">
            <i class="bi bi-calendar"></i>
            Sesión <?= ucfirst($orden['tipo_sesion']) ?> - 
            <?= date('d/m/Y', strtotime($orden['fecha_sesion'])) ?> a las <?= date('H:i', strtotime($orden['hora_sesion'])) ?>
        </small>
    </div>
</div>

<!-- Botón para imprimir -->
<div class="text-center mt-4">
    <button onclick="window.print()" class="btn btn-outline-primary">
        <i class="bi bi-printer"></i>
        Imprimir Orden del Día
    </button>
</div>

<style>
@media print {
    .btn, .breadcrumb, .sidebar, .navbar {
        display: none !important;
    }
    
    .card {
        border: 1px solid #000 !important;
        box-shadow: none !important;
    }
    
    .card-header {
        background-color: #f8f9fa !important;
        color: #000 !important;
    }
    
    .badge {
        border: 1px solid #000 !important;
    }
    
    body {
        font-size: 12pt;
    }
    
    h1, h2, h3, h4, h5, h6 {
        color: #000 !important;
    }
}
</style>

<?php 
$content = ob_get_clean();
include VIEWS_PATH . 'layouts/main.php';
?>