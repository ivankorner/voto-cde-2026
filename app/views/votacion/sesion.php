<?php 
$current_page = 'votacion';
ob_start(); 
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800 section-title">
        <i class="bi bi-house-door"></i>
        <?= htmlspecialchars($sesion['nombre_sesion']) ?>
    </h1>
    <div>
        <span class="badge bg-<?= 
            $sesion['estado'] === 'activa' ? 'success' : 
            ($sesion['estado'] === 'pausada' ? 'warning' : 
            ($sesion['estado'] === 'finalizada' ? 'info' : 'primary')) 
        ?> fs-6 me-2">
            <?= ucfirst($sesion['estado']) ?>
        </span>
        <a href="<?= BASE_URL ?>votacion" class="btn btn-secondary" data-bs-toggle="tooltip" title="Volver al listado de votaciones">
            <i class="bi bi-arrow-left"></i>
            Volver
        </a>
    </div>
</div>

<!-- Información de la sesión -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <div class="border rounded p-3">
                            <h4 class="text-primary mb-1">
                                <i class="bi bi-calendar-event"></i>
                                <?= date('d/m/Y', strtotime($sesion['fecha_sesion'])) ?>
                            </h4>
                            <p class="text-muted mb-0">Fecha</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-3">
                            <h4 class="text-success mb-1">
                                <i class="bi bi-file-earmark-text"></i>
                                <?= htmlspecialchars($sesion['numero_acta']) ?>
                            </h4>
                            <p class="text-muted mb-0">Acta N°</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-3">
                            <h4 class="text-info mb-1" id="total-presentes">
                                <i class="bi bi-people"></i>
                                <?= count($presentes) ?>
                            </h4>
                            <p class="text-muted mb-0">Presentes</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-3">
                            <h4 class="text-warning mb-1">
                                <i class="bi bi-check-square"></i>
                                <?= $estadisticas['items_votados'] ?? 0 ?>
                            </h4>
                            <p class="text-muted mb-0">Ítems Votados</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0 section-title text-white"><i class="bi bi-gear"></i> Controles de Sesión</h6>
            </div>
            <div class="card-body">
                <?php if ($puede_votar): ?>
                <div class="mb-3">
                    <button id="btn-presencia" class="btn btn-success btn-sm w-100" onclick="marcarPresencia()" data-bs-toggle="tooltip" title="Registra tu asistencia para poder votar">
                        <i class="bi bi-person-check"></i>
                        Marcar Presencia
                    </button>
                </div>
                <?php endif; ?>
                
                <?php if ($es_admin): ?>
                <div class="btn-group w-100" role="group">
                    <?php if ($sesion['estado'] === 'planificada'): ?>
                    <a href="<?= BASE_URL ?>votacion/iniciarSesion/<?= $sesion['id'] ?>" 
                       class="btn btn-success btn-sm"
                       onclick="event.preventDefault(); SweetAlerts.confirm('¿Iniciar sesión de votación?', '', 'Sí, iniciar').then((result) => { if (result.isConfirmed) window.location.href = this.href; });">
                        <i class="bi bi-play"></i>
                        Iniciar
                    </a>
                    <?php elseif ($sesion['estado'] === 'activa'): ?>
                    <a href="<?= BASE_URL ?>votacion/pausarSesion/<?= $sesion['id'] ?>" 
                       class="btn btn-warning btn-sm"
                       onclick="event.preventDefault(); SweetAlerts.confirm('¿Pausar sesión?', '', 'Sí, pausar').then((result) => { if (result.isConfirmed) window.location.href = this.href; });">
                        <i class="bi bi-pause"></i>
                        Pausar
                    </a>
                    <a href="<?= BASE_URL ?>votacion/finalizarSesion/<?= $sesion['id'] ?>" 
                       class="btn btn-danger btn-sm"
                       onclick="event.preventDefault(); SweetAlerts.confirmDanger('¿Finalizar sesión?', '', 'Sí, finalizar').then((result) => { if (result.isConfirmed) window.location.href = this.href; });">
                        <i class="bi bi-stop"></i>
                        Finalizar
                    </a>
                    <?php elseif ($sesion['estado'] === 'pausada'): ?>
                    <a href="<?= BASE_URL ?>votacion/iniciarSesion/<?= $sesion['id'] ?>" 
                       class="btn btn-success btn-sm"
                       onclick="event.preventDefault(); SweetAlerts.confirm('¿Reanudar sesión?', '', 'Sí, reanudar').then((result) => { if (result.isConfirmed) window.location.href = this.href; });">
                        <i class="bi bi-play"></i>
                        Reanudar
                    </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Panel de Control Progresivo -->
        <?php if ($es_admin && in_array($sesion['estado'], ['activa', 'pausada'])): ?>
        <div class="card shadow mt-3">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0">
                    <i class="bi bi-list-task"></i>
                    Control Progresivo de Puntos
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-4">
                        <div class="metric-small">
                            <div class="metric-value text-primary"><?= $puntos_habilitados_count ?></div>
                            <div class="metric-label">Habilitados</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="metric-small">
                            <div class="metric-value text-secondary"><?= $total_puntos ?></div>
                            <div class="metric-label">Total</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="metric-small">
                            <div class="metric-value text-warning"><?= $total_puntos - $puntos_habilitados_count ?></div>
                            <div class="metric-label">Pendientes</div>
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="<?= BASE_URL ?>votacion/panel-control/<?= $sesion['id'] ?>" 
                       class="btn btn-info btn-sm w-100">
                        <i class="bi bi-gear"></i> Administrar Control Progresivo
                    </a>
                </div>
            </div>
        </div>
        <?php elseif ($_SESSION['user_role'] === 'editor'): ?>
        <div class="card shadow mt-3">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0">
                    <i class="bi bi-info-circle"></i>
                    Estado del Control Progresivo
                </h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="badge bg-success me-2"><?= $puntos_habilitados_count ?></span>
                        <small>puntos habilitados para votar</small>
                    </div>
                    <div>
                        <span class="text-muted"><?= $total_puntos - $puntos_habilitados_count ?> pendientes</span>
                    </div>
                </div>
                <?php if ($puntos_habilitados_count === 0): ?>
                <div class="alert alert-warning mt-2 mb-0">
                    <small>
                        <i class="bi bi-clock"></i>
                        El administrador aún no ha habilitado ningún punto para votación.
                    </small>
                </div>
                <?php endif; ?>
                <!-- Controles de Moción para Editores -->
                <div class="mt-3">
                    <button class="btn btn-warning btn-sm w-100 mb-2" id="btnMocion" onclick="abrirModalMocion()">
                        <i class="bi bi-megaphone"></i>
                        Solicitar Moción
                    </button>
                    <button class="btn btn-outline-warning btn-sm w-100" onclick="abrirPanelMociones()">
                        <i class="bi bi-list-ul"></i>
                        Ver Historial de Mociones
                    </button>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Panel de Mociones (visible para todos) -->
        <div class="card shadow mt-3">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0">
                    <i class="bi bi-chat-left-text"></i>
                    Comunicaciones de la Sesión
                </h6>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3 small">
                    <i class="bi bi-info-circle"></i>
                    Revise las mociones y comunicaciones realizadas durante la sesión.
                    <br><small class="text-warning">
                        <i class="bi bi-stop-circle"></i>
                        Puede detener alertas de mociones usando el botón "Parar" en el historial.
                    </small>
                </p>
                <button class="btn btn-outline-info btn-sm w-100" onclick="abrirPanelMociones()">
                    <i class="bi bi-list-ul"></i>
                    Ver Historial de Mociones
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Hemiciclo - Visualización tipo Cámara de Diputados -->
<div class="card shadow mb-4">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0 text-center section-title"><i class="bi bi-building"></i> HEMICICLO - PRESENTES EN SESIÓN</h5>
            </div>
    <div class="card-body bg-light" style="min-height: 300px;">
        <div id="hemiciclo" class="position-relative">
            <!-- Presidencia -->
            <div class="text-center mb-4">
                <div class="badge bg-dark p-3">
                    <i class="bi bi-person-badge"></i>
                    PRESIDENCIA
                </div>
            </div>
            
            <!-- Bancadas en semicírculo -->
            <div id="bancadas" class="d-flex justify-content-center flex-wrap">
                <?php if (empty($presentes)): ?>
                <div class="text-center py-4">
                    <i class="bi bi-people fa-3x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-600">No hay miembros presentes</h5>
                    <p class="text-gray-500">Los editores deben marcar presencia para aparecer aquí</p>
                </div>
                <?php else: ?>
                <?php foreach ($presentes as $index => $presente): ?>
                <div class="bancada-member m-2" data-user-id="<?= $presente['user_id'] ?>">
                    <div class="card border-primary shadow-sm" style="width: 120px;">
                        <div class="card-body text-center p-2">
                            <div class="avatar-circle bg-primary text-white mb-2 mx-auto" 
                                 style="width: 50px; height: 50px; line-height: 50px; border-radius: 50%;">
                                <?= strtoupper(substr($presente['first_name'], 0, 1) . substr($presente['last_name'], 0, 1)) ?>
                            </div>
                            <h6 class="card-title mb-0" style="font-size: 0.8rem;">
                                <?= htmlspecialchars($presente['first_name']) ?>
                            </h6>
                            <small class="text-muted"><?= htmlspecialchars($presente['last_name']) ?></small>
                            <div class="mt-1">
                                <span class="badge bg-success">Presente</span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Panel de Votación -->
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0 section-title text-white"><i class="bi bi-list-check"></i> Ítems para Votación</h5>
            </div>
            <div class="card-body">
                <!-- Lectura de Actas -->
                <div class="card mb-3 border-info">
                    <div class="card-header bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">
                                <span class="badge bg-info me-2">GLOBAL</span>
                                <?= htmlspecialchars($actas_item['titulo']) ?>
                            </h6>
                            <div id="resultado-actas">
                                <?php if (!empty($actas_item['resultados'])): ?>
                                    <?php foreach ($actas_item['resultados'] as $resultado): ?>
                                    <span class="badge bg-<?= 
                                        $resultado['tipo_voto'] === 'positivo' ? 'success' : 
                                        ($resultado['tipo_voto'] === 'negativo' ? 'danger' : 'warning') 
                                    ?> me-1">
                                        <?= ucfirst($resultado['tipo_voto']) ?>: <?= $resultado['cantidad'] ?>
                                    </span>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Mostrar información del voto si ya existe -->
                        <?php if ($actas_item['ya_voto'] && $actas_item['voto_usuario']): ?>
                        <div class="alert alert-info mb-3">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-info-circle-fill me-2"></i>
                                <div>
                                    <strong>Ya has votado la lectura de actas</strong>
                                    <br>
                                    <span class="badge bg-<?= 
                                        $actas_item['voto_usuario']['tipo_voto'] === 'positivo' ? 'success' : 
                                        ($actas_item['voto_usuario']['tipo_voto'] === 'negativo' ? 'danger' : 'warning') 
                                    ?> fs-6">
                                        <i class="bi bi-<?= 
                                            $actas_item['voto_usuario']['tipo_voto'] === 'positivo' ? 'hand-thumbs-up' : 
                                            ($actas_item['voto_usuario']['tipo_voto'] === 'negativo' ? 'hand-thumbs-down' : 'dash-circle') 
                                        ?>"></i>
                                        Voto: <?= ucfirst($actas_item['voto_usuario']['tipo_voto']) ?>
                                    </span>
                                    <small class="text-muted d-block mt-1">
                                        Votado el <?= date('d/m/Y H:i', strtotime($actas_item['voto_usuario']['fecha_voto'])) ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Botones de votación (solo si puede votar, sesión activa y NO ha votado) -->
                        <?php if ($puede_votar && $sesion['estado'] === 'activa' && !$actas_item['ya_voto']): ?>
                        <div class="btn-group w-100" role="group">
                            <button class="btn btn-success" onclick="votar('actas', 0, 'positivo', 'Lectura de Actas')">
                                <i class="bi bi-hand-thumbs-up"></i>
                                Positivo
                            </button>
                            <button class="btn btn-danger" onclick="votar('actas', 0, 'negativo', 'Lectura de Actas')">
                                <i class="bi bi-hand-thumbs-down"></i>
                                Negativo
                            </button>
                            <button class="btn btn-warning" onclick="votar('actas', 0, 'abstencion', 'Lectura de Actas')">
                                <i class="bi bi-dash-circle"></i>
                                Abstención
                            </button>
                        </div>
                        <?php else: ?>
                        <div class="alert alert-secondary mb-0">
                            <?php if ($sesion['estado'] !== 'activa'): ?>
                                <i class="bi bi-pause-circle"></i> La sesión no está activa
                            <?php elseif (!$puede_votar): ?>
                                <i class="bi bi-info-circle"></i> Solo los editores pueden votar
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Expedientes individuales -->
                <?php 
                $totalExpedientes = 0;
                foreach ($items as $item) {
                    if (!empty($item['expedientes'])) {
                        $totalExpedientes += count($item['expedientes']);
                    }
                }
                ?>
                
                <?php if ($totalExpedientes === 0 && $_SESSION['user_role'] === 'editor'): ?>
                <div class="card mb-3 border-warning">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-clock fa-3x text-warning mb-3"></i>
                        <h5 class="text-warning">No hay puntos habilitados para votación</h5>
                        <p class="text-muted">
                            El administrador de la sesión aún no ha habilitado ningún punto del orden del día para votación.
                            <br>Los expedientes aparecerán aquí cuando sean habilitados progresivamente.
                        </p>
                        <div class="mt-3">
                            <button class="btn btn-outline-primary btn-sm" onclick="location.reload()">
                                <i class="bi bi-arrow-clockwise"></i> Actualizar
                            </button>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php foreach ($items as $item): ?>
                <?php if (!empty($item['expedientes'])): ?>
                    <?php foreach ($item['expedientes'] as $expediente): ?>
                    <div class="card mb-3 border-primary">
                        <div class="card-header bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">
                                    <span class="badge bg-primary me-2"><?= $item['numero_orden'] ?>º</span>
                                    EXPTE. N° <?= htmlspecialchars($expediente['numero_expediente']) ?>
                                </h6>
                                <div id="resultado-<?= $expediente['id'] ?>">
                                    <!-- Resultados se cargan dinámicamente -->
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <p class="mb-2"><?= nl2br(htmlspecialchars($expediente['extracto'])) ?></p>
                            
                            <?php if ($expediente['bloque_autor'] || $expediente['concejal_autor']): ?>
                            <p class="mb-2">
                                <small class="text-muted">
                                    <i class="bi bi-person"></i>
                                    <?php if ($expediente['bloque_autor']): ?>
                                        Bloque <?= htmlspecialchars($expediente['bloque_autor']) ?>
                                    <?php endif; ?>
                                    <?php if ($expediente['concejal_autor']): ?>
                                        <?= $expediente['bloque_autor'] ? ' - ' : '' ?>
                                        (<?= htmlspecialchars($expediente['concejal_autor']) ?>)
                                    <?php endif; ?>
                                </small>
                            </p>
                            <?php endif; ?>
                            
                            <!-- Mostrar información del voto si ya existe -->
                            <?php if ($expediente['ya_voto'] && $expediente['voto_usuario']): ?>
                            <div class="alert alert-info mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-info-circle-fill me-2"></i>
                                    <div>
                                        <strong>Ya has votado este expediente</strong>
                                        <br>
                                        <span class="badge bg-<?= 
                                            $expediente['voto_usuario']['tipo_voto'] === 'positivo' ? 'success' : 
                                            ($expediente['voto_usuario']['tipo_voto'] === 'negativo' ? 'danger' : 'warning') 
                                        ?> fs-6">
                                            <i class="bi bi-<?= 
                                                $expediente['voto_usuario']['tipo_voto'] === 'positivo' ? 'hand-thumbs-up' : 
                                                ($expediente['voto_usuario']['tipo_voto'] === 'negativo' ? 'hand-thumbs-down' : 'dash-circle') 
                                            ?>"></i>
                                            Voto: <?= ucfirst($expediente['voto_usuario']['tipo_voto']) ?>
                                        </span>
                                        <small class="text-muted d-block mt-1">
                                            Votado el <?= date('d/m/Y H:i', strtotime($expediente['voto_usuario']['fecha_voto'])) ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Botones de votación (solo si puede votar, sesión activa y NO ha votado) -->
                            <?php if ($puede_votar && $sesion['estado'] === 'activa' && !$expediente['ya_voto']): ?>
                            <div class="btn-group w-100" role="group">
                                <button class="btn btn-success" 
                                        onclick='votar("expediente", <?= (int)$expediente["id"] ?>, "positivo", <?= json_encode($expediente["numero_expediente"] ?? "") ?>, <?= json_encode($expediente["extracto"] ?? "") ?>)'>
                                    <i class="bi bi-hand-thumbs-up"></i>
                                    Positivo
                                </button>
                                <button class="btn btn-danger" 
                                        onclick='votar("expediente", <?= (int)$expediente["id"] ?>, "negativo", <?= json_encode($expediente["numero_expediente"] ?? "") ?>, <?= json_encode($expediente["extracto"] ?? "") ?>)'>
                                    <i class="bi bi-hand-thumbs-down"></i>
                                    Negativo
                                </button>
                                <button class="btn btn-warning" 
                                        onclick='votar("expediente", <?= (int)$expediente["id"] ?>, "abstencion", <?= json_encode($expediente["numero_expediente"] ?? "") ?>, <?= json_encode($expediente["extracto"] ?? "") ?>)'>
                                    <i class="bi bi-dash-circle"></i>
                                    Abstención
                                </button>
                            </div>
                            <?php else: ?>
                            <div class="alert alert-secondary mb-0">
                                <?php if ($sesion['estado'] !== 'activa'): ?>
                                    <i class="bi bi-pause-circle"></i> La sesión no está activa
                                <?php elseif (!$puede_votar): ?>
                                    <i class="bi bi-info-circle"></i> Solo los editores pueden votar
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <!-- Panel de Resultados en Tiempo Real -->
    <div class="col-lg-4">
        <div class="card shadow">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0 section-title text-white"><i class="bi bi-bar-chart"></i> Resultados en Tiempo Real</h5>
            </div>
            <div class="card-body" id="panel-resultados">
                <div class="text-center py-4">
                    <i class="bi bi-hourglass-split fa-3x text-gray-300 mb-3"></i>
                    <h6 class="text-gray-600">Esperando votaciones...</h6>
                    <p class="text-gray-500">Los resultados aparecerán aquí en tiempo real</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript para funcionalidad en tiempo real -->
<script>
const SESION_ID = <?= $sesion['id'] ?>;
const BASE_URL = '<?= BASE_URL ?>';
const CSRF_TOKEN = '<?= $_SESSION['csrf_token'] ?? '' ?>';

// Marcar presencia
function marcarPresencia() {
    const btn = document.getElementById('btn-presencia');
    const originalText = btn.innerHTML;
    
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-clock"></i> Registrando...';
    
        fetch(BASE_URL + 'votacion/marcarPresencia/' + SESION_ID, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'csrf_token=' + CSRF_TOKEN
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('Respuesta no válida del servidor');
        }
        
        return response.json();
    })
    .then(data => {
        if (data.success) {
            SweetAlerts.success('¡Presencia registrada!', 'Ahora puede participar en las votaciones');
            btn.classList.remove('btn-success');
            btn.classList.add('btn-secondary');
            btn.innerHTML = '<i class="bi bi-person-check-fill"></i> Presente';
            btn.disabled = true;
            
            // Actualizar contador de presentes
            document.getElementById('total-presentes').innerHTML = `<i class="bi bi-people"></i> ${data.total_presentes}`;
        } else {
            SweetAlerts.error('Error', data.message || 'Error desconocido');
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        
        let errorMessage = 'No se pudo conectar con el servidor';
        if (error.message.includes('HTTP')) {
            errorMessage = 'Error del servidor: ' + error.message;
        } else if (error.message.includes('JSON')) {
            errorMessage = 'Error de formato en la respuesta del servidor';
        } else if (error.message.includes('Failed to fetch')) {
            errorMessage = 'Problema de conectividad. Intente nuevamente.';
        }
        
        SweetAlerts.error('Error de conexión', errorMessage);
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
}

// Votar
function votar(tipoItem, itemId, tipoVoto, numeroExpediente = '', extracto = '') {
    SweetAlerts.confirm(
        '¿Confirmar voto?', 
        `¿Confirma su voto ${tipoVoto.toUpperCase()}?`,
        'Sí, votar'
    ).then((result) => {
        if (!result.isConfirmed) {
            return;
        }
        
        const formData = new FormData();
        formData.append('sesion_id', SESION_ID);
        formData.append('item_tipo', tipoItem);
        formData.append('item_id', itemId);
        formData.append('tipo_voto', tipoVoto);
        formData.append('numero_expediente', numeroExpediente);
        formData.append('extracto_expediente', extracto);
        formData.append('csrf_token', CSRF_TOKEN);
        
        fetch(BASE_URL + 'votacion/votar', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            // Verificar si la respuesta es exitosa
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            // Verificar el content-type
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('Respuesta no válida del servidor');
            }
            
            return response.json();
        })
        .then(data => {
            if (data.success) {
                SweetAlerts.success('¡Voto registrado!', 'Su voto ha sido registrado exitosamente');
                // Recargar la página para mostrar estado actualizado
                location.reload();
            } else {
                SweetAlerts.error('Error al votar', data.message || 'Error desconocido');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            
            let errorMessage = 'No se pudo conectar con el servidor';
            if (error.message.includes('HTTP')) {
                errorMessage = 'Error del servidor: ' + error.message;
            } else if (error.message.includes('JSON')) {
                errorMessage = 'Error de formato en la respuesta del servidor';
            } else if (error.message.includes('Respuesta no válida')) {
                errorMessage = 'El servidor no respondió correctamente';
            } else if (error.message.includes('Failed to fetch')) {
                errorMessage = 'Problema de conectividad. Intente nuevamente.';
            }
            
            SweetAlerts.error('Error de conexión', errorMessage);
        });
    });
}

// Actualizar hemiciclo
function actualizarHemiciclo(presentes) {
    // Implementar actualización dinámica del hemiciclo
    // Por ahora solo recargar la página
    location.reload();
}

// Actualizar resultados cada 10 segundos
setInterval(function() {
    if (document.visibilityState === 'visible') {
        actualizarResultados();
    }
}, 10000);

function actualizarResultados() {
    // Implementar actualización de resultados en tiempo real
    // Se implementará en la siguiente fase
}
</script>

<style>
.bancada-member {
    animation: fadeIn 0.5s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.avatar-circle {
    font-weight: bold;
    font-size: 1.2rem;
}

#hemiciclo {
    background: radial-gradient(ellipse at center, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 15px;
    padding: 20px;
}

.card-header {
    position: relative;
}

.btn-group .btn {
    border-radius: 0;
}

.btn-group .btn:first-child {
    border-top-left-radius: 0.375rem;
    border-bottom-left-radius: 0.375rem;
}

.btn-group .btn:last-child {
    border-top-right-radius: 0.375rem;
    border-bottom-right-radius: 0.375rem;
}

/* Control Progresivo */
.metric-small {
    text-align: center;
    padding: 0.5rem;
}

.metric-small .metric-value {
    font-size: 1.5rem;
    font-weight: bold;
    margin-bottom: 0.25rem;
}

.metric-small .metric-label {
    font-size: 0.8rem;
    color: #6c757d;
}
</style>

<!-- Modal Panel de Mociones (visible para todos) -->
<div class="modal fade" id="modalPanelMociones" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">
                    <i class="bi bi-list-ul"></i>
                    Historial de Mociones - Sesión
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <small class="text-muted">
                            <i class="bi bi-info-circle"></i>
                            Mociones solicitadas durante esta sesión
                        </small>
                    </div>
                    <button class="btn btn-sm btn-outline-primary" onclick="actualizarPanelMociones()">
                        <i class="bi bi-arrow-clockwise"></i>
                        Actualizar
                    </button>
                </div>
                
                <!-- Lista de mociones -->
                <div id="lista-mociones">
                    <div class="text-center py-4" id="loading-mociones">
                        <div class="spinner-border text-warning" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <div class="mt-2">Cargando mociones...</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Área de Notificaciones de Mociones (ya no se usa, pero mantenemos por compatibilidad) -->
<div id="area-mociones" class="position-fixed" style="top: 20px; left: 20px; right: 20px; z-index: 10000; display: none;">
    <div class="alert alert-warning alert-dismissible fade show shadow-lg border-0" id="notificacion-mocion">
        <div class="d-flex align-items-center">
            <i class="bi bi-megaphone fa-2x me-3"></i>
            <div class="flex-grow-1">
                <h5 class="mb-1">
                    <i class="bi bi-exclamation-triangle"></i>
                    MOCIÓN SOLICITADA
                </h5>
                <p class="mb-1" id="texto-mocion">Texto de la moción...</p>
                <small class="text-muted">
                    <i class="bi bi-person"></i>
                    Solicitada por: <span id="autor-mocion">Editor</span> - 
                    <span id="hora-mocion">00:00:00</span>
                </small>
            </div>
        </div>
        <button type="button" class="btn-close" onclick="cerrarMocion()"></button>
    </div>
</div>

<!-- Modal para solicitar moción (solo editores) -->
<?php if ($_SESSION['user_role'] === 'editor'): ?>
<div class="modal fade" id="modalMocion" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">
                    <i class="bi bi-megaphone"></i>
                    Solicitar Moción
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i>
                    <strong>¿Qué es una moción?</strong><br>
                    Una moción permite llamar la atención de todos los participantes sin interrumpir la votación en curso.
                </div>
                
                <div class="mb-3">
                    <label for="tipoMocion" class="form-label">Tipo de moción:</label>
                    <select class="form-select" id="tipoMocion">
                        <option value="orden">Moción de orden</option>
                        <option value="aclaracion">Solicitud de aclaración</option>
                        <option value="reconsideracion">Moción de reconsideración</option>
                        <option value="cuestion_previa">Cuestión previa</option>
                        <option value="otro">Otro</option>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label for="textoMocion" class="form-label">Descripción de la moción:</label>
                    <textarea class="form-control" id="textoMocion" rows="3" 
                              placeholder="Describa brevemente el motivo de su moción..."
                              maxlength="200"></textarea>
                    <div class="form-text">Máximo 200 caracteres</div>
                </div>
                
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i>
                    <strong>Importante:</strong> La moción será visible para todos los usuarios conectados.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-warning" onclick="enviarMocion()">
                    <i class="bi bi-megaphone"></i>
                    Enviar Moción
                </button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- JavaScript para actualización automática (solo para editores) -->
<?php if ($_SESSION['user_role'] === 'editor'): ?>
<script>
// Verificar si hay nuevos puntos habilitados cada 10 segundos
let autoCheckInterval;
const sesionId = <?= $sesion['id'] ?>;
let ultimoCheckPuntos = <?= $puntos_habilitados_count ?>;

function iniciarVerificacionAutomatica() {
    autoCheckInterval = setInterval(verificarNuevosPuntos, 10000); // Cada 10 segundos
}

function verificarNuevosPuntos() {
    fetch(BASE_URL + `votacion/estado-puntos/${sesionId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const puntosActuales = data.estadisticas.puntos_habilitados;
                
                if (puntosActuales > ultimoCheckPuntos) {
                    // Hay nuevos puntos habilitados
                    mostrarNotificacionNuevosPuntos(puntosActuales - ultimoCheckPuntos);
                    ultimoCheckPuntos = puntosActuales;
                    
                    // Actualizar la página después de 3 segundos
                    setTimeout(() => {
                        location.reload();
                    }, 3000);
                }
            }
        })
        .catch(error => {
            console.error('Error al verificar puntos:', error);
        });
}

function mostrarNotificacionNuevosPuntos(cantidad) {
    // Crear notificación toast
    const toastContainer = document.getElementById('toast-container') || crearToastContainer();
    
    const toast = document.createElement('div');
    toast.className = 'toast show align-items-center text-white bg-success border-0';
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <i class="bi bi-check-circle-fill me-2"></i>
                <strong>¡Nuevos puntos habilitados!</strong><br>
                ${cantidad} punto${cantidad > 1 ? 's' : ''} disponible${cantidad > 1 ? 's' : ''} para votación
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" onclick="this.parentElement.parentElement.remove()"></button>
        </div>
    `;
    
    toastContainer.appendChild(toast);
    
    // Auto-remover después de 5 segundos
    setTimeout(() => {
        if (toast.parentElement) {
            toast.remove();
        }
    }, 5000);
}

function crearToastContainer() {
    const container = document.createElement('div');
    container.id = 'toast-container';
    container.className = 'toast-container position-fixed top-0 end-0 p-3';
    container.style.zIndex = '9999';
    document.body.appendChild(container);
    return container;
}

// Iniciar verificación automática al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    iniciarVerificacionAutomatica();
    iniciarVerificacionMociones();
});

// Limpiar interval al salir de la página
window.addEventListener('beforeunload', function() {
    if (autoCheckInterval) {
        clearInterval(autoCheckInterval);
    }
    if (mocionCheckInterval) {
        clearInterval(mocionCheckInterval);
    }
});

// === SISTEMA DE MOCIONES ===

function abrirModalMocion() {
    const modal = new bootstrap.Modal(document.getElementById('modalMocion'));
    modal.show();
    
    // Limpiar campos
    document.getElementById('tipoMocion').value = 'orden';
    document.getElementById('textoMocion').value = '';
}

function enviarMocion() {
    const tipo = document.getElementById('tipoMocion').value;
    const texto = document.getElementById('textoMocion').value.trim();
    
    if (!texto) {
        alert('Por favor, describa su moción');
        return;
    }
    
    // Deshabilitar botón mientras se envía
    const btnEnviar = event.target;
    btnEnviar.disabled = true;
    btnEnviar.innerHTML = '<i class="spinner-border spinner-border-sm me-2"></i>Enviando...';
    
    const data = {
        sesion_id: sesionId,
        tipo: tipo,
        texto: texto
    };
    
    fetch(BASE_URL + 'votacion/enviar-mocion', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Cerrar modal
            bootstrap.Modal.getInstance(document.getElementById('modalMocion')).hide();
            
            // Mostrar confirmación con SweetAlert2
            Swal.fire({
                title: '¡Moción Enviada!',
                text: 'Su moción ha sido enviada exitosamente a todos los participantes.',
                icon: 'success',
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '#28a745',
                timer: 3000,
                position: 'top-end',
                toast: true,
                showConfirmButton: false
            });
            
            // Mostrar la moción inmediatamente
            mostrarMocion(data.mocion);
            
        } else {
            Swal.fire({
                title: 'Error',
                text: 'Error al enviar la moción: ' + (data.error || 'Error desconocido'),
                icon: 'error',
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '#dc3545'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error de conexión al enviar la moción');
    })
    .finally(() => {
        // Rehabilitar botón
        btnEnviar.disabled = false;
        btnEnviar.innerHTML = '<i class="bi bi-megaphone"></i> Enviar Moción';
    });
}

</script>
<?php endif; ?>

<!-- JavaScript para verificación de mociones (para todos los usuarios) -->
<script>
let mocionCheckInterval;
let ultimaMocionId = 0;

function iniciarVerificacionMociones() {
    // Verificar mociones cada 3 segundos
    mocionCheckInterval = setInterval(verificarNuevasMociones, 3000);
}

function verificarNuevasMociones() {
    fetch(BASE_URL + `votacion/verificar-mociones/${sesionId}?desde=${ultimaMocionId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.mociones && data.mociones.length > 0) {
                // Mostrar la moción más reciente
                const mocion = data.mociones[data.mociones.length - 1];
                mostrarMocion(mocion);
                ultimaMocionId = Math.max(ultimaMocionId, mocion.id);
            }
        })
        .catch(error => {
            console.error('Error al verificar mociones:', error);
        });
}

function mostrarMocion(mocion) {
    // Reproducir sonido de notificación (opcional)
    try {
        // Crear un beep simple con Web Audio API
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();
        
        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);
        
        oscillator.frequency.value = 800;
        oscillator.type = 'sine';
        
        gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.3);
        
        oscillator.start();
        oscillator.stop(audioContext.currentTime + 0.3);
    } catch (e) {
        // Silenciar errores de audio
    }
    
    // Mostrar moción con SweetAlert2
    const fechaFormateada = new Date(mocion.fecha_creacion).toLocaleTimeString('es-ES', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });
    
    Swal.fire({
        title: '📢 MOCIÓN SOLICITADA',
        html: `
            <div class="text-start">
                <div class="alert alert-warning mb-3">
                    <strong>${mocion.tipo_texto}</strong>
                </div>
                <div class="mb-3">
                    <strong>Descripción:</strong><br>
                    <span class="text-muted">${mocion.texto}</span>
                </div>
                <div class="row">
                    <div class="col-6">
                        <small class="text-muted">
                            <i class="bi bi-person"></i> ${mocion.autor_nombre}
                        </small>
                    </div>
                    <div class="col-6 text-end">
                        <small class="text-muted">
                            <i class="bi bi-clock"></i> ${fechaFormateada}
                        </small>
                    </div>
                </div>
            </div>
        `,
        icon: 'warning',
        confirmButtonText: 'Entendido',
        confirmButtonColor: '#ffc107',
        timer: 5000,
        timerProgressBar: true,
        position: 'top',
        toast: false,
        width: 500,
        showClass: {
            popup: 'animate__animated animate__slideInDown'
        },
        hideClass: {
            popup: 'animate__animated animate__slideOutUp'
        },
        customClass: {
            container: 'swal-mocion-container',
            popup: 'swal-mocion-popup',
            title: 'swal-mocion-title'
        }
    });
    
    // Actualizar el panel de mociones si existe
    if (typeof actualizarPanelMociones === 'function') {
        actualizarPanelMociones();
    }
}

function cerrarMocion() {
    const areaMociones = document.getElementById('area-mociones');
    areaMociones.style.display = 'none';
}

function mostrarToast(mensaje, tipo = 'info') {
    const toastContainer = document.getElementById('toast-container') || crearToastContainer();
    
    const colorClass = tipo === 'success' ? 'bg-success' : 
                      tipo === 'error' ? 'bg-danger' : 
                      tipo === 'warning' ? 'bg-warning text-dark' : 'bg-info';
    
    const toast = document.createElement('div');
    toast.className = `toast show align-items-center text-white ${colorClass} border-0`;
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${mensaje}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" onclick="this.parentElement.parentElement.remove()"></button>
        </div>
    `;
    
    toastContainer.appendChild(toast);
    
    setTimeout(() => {
        if (toast.parentElement) {
            toast.remove();
        }
    }, 5000);
}



// === PANEL DE MOCIONES (DISPONIBLE PARA TODOS) ===

function abrirPanelMociones() {
    const modal = new bootstrap.Modal(document.getElementById('modalPanelMociones'));
    modal.show();
    
    // Cargar mociones al abrir el panel
    actualizarPanelMociones();
}

function actualizarPanelMociones() {
    const loading = document.getElementById('loading-mociones');
    const listaMociones = document.getElementById('lista-mociones');
    
    // Mostrar loading
    loading.style.display = 'block';
    
    fetch(BASE_URL + `votacion/obtener-mociones/${window.sesionId || <?= $sesion['id'] ?>}`)
        .then(response => response.json())
        .then(data => {
            loading.style.display = 'none';
            
            if (data.success && data.mociones) {
                mostrarListaMociones(data.mociones);
            } else {
                listaMociones.innerHTML = `
                    <div class="alert alert-info text-center">
                        <i class="bi bi-info-circle"></i>
                        No hay mociones registradas para esta sesión
                    </div>
                `;
            }
        })
        .catch(error => {
            loading.style.display = 'none';
            console.error('Error cargando mociones:', error);
            listaMociones.innerHTML = `
                <div class="alert alert-danger text-center">
                    <i class="bi bi-exclamation-triangle"></i>
                    Error al cargar las mociones
                </div>
            `;
        });
}

function mostrarListaMociones(mociones) {
    const listaMociones = document.getElementById('lista-mociones');
    
    if (mociones.length === 0) {
        listaMociones.innerHTML = `
            <div class="alert alert-info text-center">
                <i class="bi bi-info-circle"></i>
                No hay mociones registradas para esta sesión
            </div>
        `;
        return;
    }
    
    let html = '';
    
    mociones.forEach((mocion, index) => {
        const fechaFormateada = new Date(mocion.fecha_creacion).toLocaleString('es-ES', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
        
        const badgeColor = getBadgeColorForTipo(mocion.tipo);
        
        html += `
            <div class="card mb-3 shadow-sm border-start border-warning border-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <span class="badge ${badgeColor} me-2">${mocion.tipo_texto}</span>
                            <small class="text-muted">#${mocion.id}</small>
                        </div>
                        <small class="text-muted">
                            <i class="bi bi-clock"></i> ${fechaFormateada}
                        </small>
                    </div>
                    
                    <div class="mb-3">
                        <p class="mb-2">${mocion.texto}</p>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">
                                <i class="bi bi-person"></i>
                                <strong>${mocion.autor_nombre}</strong>
                            </small>
                        </div>
                        <div class="d-flex gap-2">
                            ${mocion.activa == 1 ? 
                                `<span class="badge bg-success">Activa</span>
                                 <button class="btn btn-sm btn-outline-danger" 
                                        onclick="pararMocion(${mocion.id}, '${mocion.tipo_texto}')"
                                        title="Detener alertas de esta moción">
                                    <i class="bi bi-stop-circle"></i>
                                    Parar
                                 </button>` : 
                                `<span class="badge bg-secondary">Detenida</span>
                                 <small class="text-muted">No se notifica</small>`
                            }
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    listaMociones.innerHTML = html;
}

function getBadgeColorForTipo(tipo) {
    const colores = {
        'orden': 'bg-danger',
        'aclaracion': 'bg-info',
        'reconsideracion': 'bg-warning text-dark',
        'cuestion_previa': 'bg-primary',
        'otro': 'bg-secondary'
    };
    
    return colores[tipo] || 'bg-secondary';
}

function pararMocion(mocionId, tipoMocion) {
    Swal.fire({
        title: '¿Detener esta moción?',
        html: `
            <div class="text-start">
                <p>¿Está seguro que desea detener las alertas de esta moción?</p>
                <div class="alert alert-warning">
                    <i class="bi bi-info-circle"></i>
                    <strong>Tipo:</strong> ${tipoMocion}<br>
                    <strong>Efecto:</strong> Esta moción dejará de aparecer como alerta automática para todos los usuarios.
                </div>
                <p class="text-muted small">
                    <i class="bi bi-exclamation-triangle"></i>
                    La moción seguirá visible en el historial pero no se notificará más.
                </p>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="bi bi-stop-circle"></i> Sí, Detener',
        cancelButtonText: 'Cancelar',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            ejecutarPararMocion(mocionId);
        }
    });
}

function ejecutarPararMocion(mocionId) {
    // Mostrar loading en el botón
    const btnParar = document.querySelector(`button[onclick*="${mocionId}"]`);
    if (btnParar) {
        btnParar.disabled = true;
        btnParar.innerHTML = '<i class="spinner-border spinner-border-sm me-1"></i>Deteniendo...';
    }
    
    fetch(BASE_URL + 'votacion/parar-mocion', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            mocion_id: mocionId,
            sesion_id: window.sesionId || <?= $sesion['id'] ?>
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Mostrar confirmación
            Swal.fire({
                title: '¡Moción Detenida!',
                text: 'La moción ha sido desactivada. Ya no aparecerá como alerta automática.',
                icon: 'success',
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '#28a745',
                timer: 3000,
                position: 'top-end',
                toast: true,
                showConfirmButton: false
            });
            
            // Actualizar el panel para reflejar el cambio
            actualizarPanelMociones();
            
        } else {
            Swal.fire({
                title: 'Error',
                text: 'Error al detener la moción: ' + (data.error || 'Error desconocido'),
                icon: 'error',
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '#dc3545'
            });
            
            // Restaurar botón
            if (btnParar) {
                btnParar.disabled = false;
                btnParar.innerHTML = '<i class="bi bi-stop-circle"></i> Parar';
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            title: 'Error de Conexión',
            text: 'No se pudo conectar con el servidor. Intente nuevamente.',
            icon: 'error',
            confirmButtonText: 'Aceptar',
            confirmButtonColor: '#dc3545'
        });
        
        // Restaurar botón
        if (btnParar) {
            btnParar.disabled = false;
            btnParar.innerHTML = '<i class="bi bi-stop-circle"></i> Parar';
        }
    });
}

// Iniciar verificación de mociones al cargar la página (para todos los usuarios)
document.addEventListener('DOMContentLoaded', function() {
    if (typeof sesionId === 'undefined') {
        window.sesionId = <?= $sesion['id'] ?>;
    }
    iniciarVerificacionMociones();
});
</script>

<?php 
$content = ob_get_clean();
include VIEWS_PATH . 'layouts/main.php';
?>