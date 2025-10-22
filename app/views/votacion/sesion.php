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
            </div>
        </div>
        <?php endif; ?>
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
});

// Limpiar interval al salir de la página
window.addEventListener('beforeunload', function() {
    if (autoCheckInterval) {
        clearInterval(autoCheckInterval);
    }
});
</script>
<?php endif; ?>

<?php 
$content = ob_get_clean();
include VIEWS_PATH . 'layouts/main.php';
?>