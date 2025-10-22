<?php 
$current_page = 'votacion';
ob_start(); 
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="bi bi-vote-fill"></i>
        Sala de Votaciones
    </h1>
    <div class="text-muted">
        <i class="bi bi-person-badge"></i>
        Sesión: <?= $_SESSION['user_name'] ?> (Editor)
    </div>
</div>

<!-- Estado de la sesión del usuario -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-left-primary shadow">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Tu estado en el sistema
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            Conectado y listo para votar
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-person-check fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sesiones disponibles para votar -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="bi bi-ballot"></i>
            Sesiones de Votación Disponibles
        </h6>
        <div class="text-muted small">
            <i class="bi bi-clock"></i>
            <?= date('d/m/Y H:i:s') ?>
        </div>
    </div>
    <div class="card-body">
        <?php 
        $sesionesActivas = array_filter($sesiones, function($s) { 
            return in_array($s['estado'], ['activa', 'pausada']); 
        });
        ?>
        
        <?php if (empty($sesionesActivas)): ?>
        <div class="text-center py-5">
            <i class="bi bi-hourglass-split fa-3x text-gray-300 mb-3"></i>
            <h5 class="text-gray-600">No hay sesiones de votación activas</h5>
            <p class="text-gray-500">En este momento no hay sesiones abiertas para votar. Por favor, espera a que el administrador inicie una sesión.</p>
        </div>
        <?php else: ?>
        
        <div class="row">
            <?php foreach ($sesionesActivas as $sesion): ?>
            <div class="col-lg-6 mb-4">
                <div class="card h-100 border-left-<?= $sesion['estado'] === 'activa' ? 'success' : 'warning' ?> shadow">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="mr-3">
                                <?php if ($sesion['estado'] === 'activa'): ?>
                                    <i class="bi bi-play-circle-fill text-success fa-2x"></i>
                                <?php else: ?>
                                    <i class="bi bi-pause-circle-fill text-warning fa-2x"></i>
                                <?php endif; ?>
                            </div>
                            <div>
                                <h5 class="card-title mb-0"><?= htmlspecialchars($sesion['nombre_sesion']) ?></h5>
                                <small class="text-muted">
                                    <span class="badge badge-<?= $sesion['estado'] === 'activa' ? 'success' : 'warning' ?>">
                                        <?= ucfirst($sesion['estado']) ?>
                                    </span>
                                </small>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <h6 class="text-primary">
                                <i class="bi bi-journal-text"></i>
                                Acta N° <?= htmlspecialchars($sesion['numero_acta']) ?>
                            </h6>
                            <p class="card-text text-muted small">
                                <?= ucfirst($sesion['tipo_sesion']) ?> - 
                                <?= date('d/m/Y', strtotime($sesion['fecha_sesion'])) ?>
                            </p>
                            <?php if ($sesion['descripcion']): ?>
                            <p class="card-text"><?= htmlspecialchars($sesion['descripcion']) ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="text-center">
                            <?php if ($sesion['estado'] === 'activa'): ?>
                            <a href="<?= BASE_URL ?>votacion/sesion/<?= $sesion['id'] ?>" 
                               class="btn btn-success btn-lg">
                                <i class="bi bi-vote-fill"></i>
                                Ingresar a Votar
                            </a>
                            <?php else: ?>
                            <button class="btn btn-warning btn-lg" disabled>
                                <i class="bi bi-pause"></i>
                                Sesión Pausada
                            </button>
                            <p class="small text-muted mt-2">La sesión está temporalmente pausada</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-footer text-muted small">
                        <i class="bi bi-calendar3"></i>
                        Creada: <?= date('d/m/Y H:i', strtotime($sesion['created_at'])) ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <?php endif; ?>
    </div>
</div>

<!-- Información adicional -->
<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card border-left-info shadow h-100">
            <div class="card-body">
                <h5 class="card-title text-info">
                    <i class="bi bi-info-circle"></i>
                    Instrucciones para Votar
                </h5>
                <ul class="card-text">
                    <li>Haz clic en <strong>"Ingresar a Votar"</strong> en una sesión activa</li>
                    <li>Marca tu <strong>presencia</strong> al ingresar a la sesión</li>
                    <li>Vota cada expediente de la lista</li>
                    <li>Puedes cambiar tu voto mientras la votación esté abierta</li>
                    <li>Los resultados se muestran en tiempo real</li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card border-left-success shadow h-100">
            <div class="card-body">
                <h5 class="card-title text-success">
                    <i class="bi bi-shield-check"></i>
                    Tu Perfil de Votación
                </h5>
                <div class="card-text">
                    <p><strong>Nombre:</strong> <?= $_SESSION['user_name'] ?></p>
                    <p><strong>Rol:</strong> Editor</p>
                    <p><strong>Estado:</strong> <span class="badge badge-success">Habilitado para votar</span></p>
                    <p><strong>Última conexión:</strong> <?= date('d/m/Y H:i:s') ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean();
include VIEWS_PATH . 'layouts/main.php';
?>