<?php 
$current_page = 'votacion';
ob_start(); 
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header del Panel -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="mb-0">
                                <i class="bi bi-list-task"></i> 
                                Control Progresivo de Puntos
                            </h4>
                            <small class="opacity-75">
                                Sesión: <?= htmlspecialchars($sesion['nombre_sesion']) ?> | 
                                Acta: <?= htmlspecialchars($sesion['numero_acta']) ?> |
                                Estado: <span class="badge bg-<?= $sesion['estado'] === 'activa' ? 'success' : 'warning' ?> ms-1">
                                    <?= ucfirst($sesion['estado']) ?>
                                </span>
                            </small>
                        </div>
                        <div class="col-md-4 text-end">
                            <button class="btn btn-outline-light btn-sm" onclick="actualizarEstado()" title="Actualizar Estado" data-bs-toggle="tooltip" data-bs-placement="bottom">
                                <i class="bi bi-arrow-clockwise"></i> Actualizar
                            </button>
                            <a href="<?= BASE_URL ?>votacion" class="btn btn-light btn-sm ms-2">
                                <i class="bi bi-arrow-left"></i> Volver
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Estadísticas -->
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="metric-card">
                                <div class="metric-value text-primary" id="total-puntos">
                                    <?= $estadisticas['total_puntos'] ?? 0 ?>
                                </div>
                                <div class="metric-label">Total de Puntos</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="metric-card">
                                <div class="metric-value text-success" id="puntos-habilitados">
                                    <?= $estadisticas['puntos_habilitados'] ?? 0 ?>
                                </div>
                                <div class="metric-label">Puntos Habilitados</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="metric-card">
                                <div class="metric-value text-warning" id="puntos-pendientes">
                                    <?= ($estadisticas['total_puntos'] ?? 0) - ($estadisticas['puntos_habilitados'] ?? 0) ?>
                                </div>
                                <div class="metric-label">Puntos Pendientes</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="metric-card">
                                <div class="progress" style="height: 8px;">
                                    <?php 
                                    $porcentaje = $estadisticas['total_puntos'] > 0 ? 
                                        round(($estadisticas['puntos_habilitados'] / $estadisticas['total_puntos']) * 100, 1) : 0;
                                    ?>
                                    <div class="progress-bar bg-success" role="progressbar" 
                                         style="width: <?= $porcentaje ?>%" id="progreso-barra">
                                    </div>
                                </div>
                                <div class="metric-label">Progreso (<span id="progreso-porcentaje"><?= $porcentaje ?>%</span>)</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones de Acción Rápida -->
            <?php if (empty($puntos)): ?>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i>
                    <strong>Los puntos del orden del día no han sido inicializados.</strong>
                    <p class="mb-0 mt-2">
                        Para comenzar el control progresivo, debe inicializar los puntos basados en el orden del día de esta sesión.
                    </p>
                    <div class="mt-3">
                        <button class="btn btn-warning" onclick="inicializarPuntos()">
                            <i class="bi bi-play-circle"></i> Inicializar Puntos del Orden del Día
                        </button>
                    </div>
                </div>
            <?php else: ?>
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <button class="btn btn-success me-2" onclick="habilitarSiguiente()" id="btn-siguiente" data-bs-toggle="tooltip" title="Habilita el primer punto pendiente" 
                                        <?= $estadisticas['puntos_habilitados'] === $estadisticas['total_puntos'] ? 'disabled' : '' ?>>
                                    <i class="bi bi-play-circle"></i> Habilitar Siguiente Punto
                                </button>
                                <button class="btn btn-warning me-2" onclick="pausarTodos()" data-bs-toggle="tooltip" title="Deshabilita temporalmente todos los puntos">
                                    <i class="bi bi-pause-circle"></i> Pausar Todos
                                </button>
                            </div>
                            <div class="col-md-6 text-end">
                                <button class="btn btn-info me-2" onclick="reinicializarPuntos()" data-bs-toggle="tooltip" title="Regenera la grilla desde el Orden del Día">
                                    <i class="bi bi-arrow-clockwise"></i> Reinicializar Puntos
                                </button>
                                <div class="form-check form-switch d-inline-block">
                                    <input class="form-check-input" type="checkbox" id="auto-refresh" checked>
                                    <label class="form-check-label" for="auto-refresh">Auto-actualizar</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Control de Mociones -->
                <div class="card shadow-sm mb-4" id="panel-control-mociones">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-megaphone"></i> Control de Mociones
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <button class="btn btn-danger me-2" onclick="pararTodasLasMociones()" data-bs-toggle="tooltip" title="Detiene todas las mociones activas">
                                    <i class="bi bi-stop-circle"></i> Parar Todas las Mociones
                                </button>
                                <button class="btn btn-info me-2" onclick="refrescarMociones()" data-bs-toggle="tooltip" title="Actualiza la lista de mociones">
                                    <i class="bi bi-arrow-clockwise"></i> Refrescar
                                </button>
                            </div>
                            <div class="col-md-6 text-end">
                                <button class="btn btn-warning" onclick="limpiarHistorialMociones()" data-bs-toggle="tooltip" title="Limpia el historial de mociones antiguas">
                                    <i class="bi bi-trash"></i> Limpiar Historial
                                </button>
                            </div>
                        </div>
                        
                        <!-- Lista de Mociones Activas -->
                        <div class="row">
                            <div class="col-12">
                                <h6><i class="bi bi-broadcast"></i> Mociones Activas</h6>
                                <div class="table-responsive mb-4" style="max-height: 300px; overflow-y: auto;">
                                    <table class="table table-sm table-hover" id="tabla-mociones-activas">
                                        <thead class="table-dark sticky-top">
                                            <tr>
                                                <th>Usuario</th>
                                                <th>Mensaje</th>
                                                <th>Hora</th>
                                                <th>Estado</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Se llenará dinámicamente -->
                                        </tbody>
                                    </table>
                                </div>
                                
                                <h6><i class="bi bi-clock-history"></i> Historial de Mociones</h6>
                                <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                                    <table class="table table-sm" id="tabla-historial-mociones">
                                        <thead class="table-secondary sticky-top">
                                            <tr>
                                                <th>Usuario</th>
                                                <th>Mensaje</th>
                                                <th>Hora</th>
                                                <th>Estado Final</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Se llenará dinámicamente -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lista de Puntos -->
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-list-ol"></i> Puntos del Orden del Día
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="tabla-puntos">
                                <thead class="table-light">
                                    <tr>
                                        <th width="80">Orden</th>
                                        <th width="120">Tipo</th>
                                        <th>Descripción</th>
                                        <th width="150">Estado</th>
                                        <th width="180">Habilitado Por</th>
                                        <th width="150">Fecha/Hora</th>
                                        <th width="120">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($puntos as $punto): ?>
                                    <tr data-punto-id="<?= $punto['id'] ?>" 
                                        class="<?= $punto['habilitado'] ? 'table-success' : '' ?>">
                                        <td>
                                            <span class="badge bg-primary fs-6">
                                                <?= $punto['orden_punto'] ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php 
                                            $tipo = $punto['item_tipo'];
                                            $label = 'Otro';
                                            $bg = 'secondary';
                                            if ($tipo === 'global') { $label = 'Global'; $bg = 'info'; }
                                            elseif ($tipo === 'expediente') { $label = 'Expediente'; $bg = 'secondary'; }
                                            elseif ($tipo === 'actas') { $label = 'Actas'; $bg = 'info'; }
                                            ?>
                                            <span class="badge bg-<?= $bg ?>">
                                                <?= $label ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div>
                                                <strong><?= htmlspecialchars($punto['numero_expediente']) ?></strong>
                                                <?php if (!empty($punto['extracto'])): ?>
                                                    <br><small class="text-muted"><?= htmlspecialchars($punto['extracto']) ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $punto['habilitado'] ? 'success' : 'secondary' ?> punto-estado">
                                                <?= $punto['habilitado'] ? 'Habilitado' : 'Pendiente' ?>
                                            </span>
                                        </td>
                                        <td class="punto-usuario">
                                            <?php if ($punto['habilitado'] && $punto['first_name']): ?>
                                                <small>
                                                    <?= htmlspecialchars($punto['first_name'] . ' ' . $punto['last_name']) ?>
                                                </small>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="punto-fecha">
                                            <?php if ($punto['fecha_habilitacion']): ?>
                                                <small>
                                                    <?= date('d/m H:i', strtotime($punto['fecha_habilitacion'])) ?>
                                                </small>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm punto-acciones" role="group">
                                                <?php if ($punto['habilitado']): ?>
                                                    <button class="btn btn-outline-warning" 
                                                            onclick="deshabilitarPunto(<?= $punto['id'] ?>)"
                                                            title="Deshabilitar">
                                                        <i class="bi bi-pause-circle"></i>
                                                    </button>
                                                <?php else: ?>
                                                    <button class="btn btn-outline-success" 
                                                            onclick="habilitarPunto(<?= $punto['id'] ?>)"
                                                            title="Habilitar">
                                                        <i class="bi bi-play-circle"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- CSS personalizado -->
<style>
.metric-card {
    padding: 1rem;
    text-align: center;
}

.metric-value {
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.metric-label {
    font-size: 0.9rem;
    color: #6c757d;
    margin-bottom: 0;
}

.table th {
    border-top: none;
    font-weight: 600;
    font-size: 0.9rem;
}

.badge {
    font-size: 0.8rem;
}

.punto-acciones .btn {
    padding: 0.25rem 0.5rem;
}

.table-success {
    background-color: rgba(25, 135, 84, 0.1) !important;
}

.form-check-input:checked {
    background-color: #198754;
    border-color: #198754;
}
</style>

<!-- JavaScript para el control progresivo -->
<script>
let autoRefreshInterval;
const sesionId = <?= $sesion['id'] ?>;

// Inicializar al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    console.log('=== Inicializando Panel de Control ===');
    
    // Inicializar auto-refresh de puntos
    iniciarAutoRefresh();
    
    // Inicializar auto-refresh de mociones
    iniciarAutoRefreshMociones();
    
    // Configurar auto-refresh de puntos
    document.getElementById('auto-refresh').addEventListener('change', function() {
        if (this.checked) {
            iniciarAutoRefresh();
        } else {
            detenerAutoRefresh();
        }
    });
    
    console.log('Panel de Control Inicializado');
});

function iniciarAutoRefresh() {
    detenerAutoRefresh();
    autoRefreshInterval = setInterval(actualizarEstado, 5000); // Cada 5 segundos
}

function detenerAutoRefresh() {
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
    }
}

function actualizarEstado() {
    fetch(`<?= BASE_URL ?>votacion/estado-puntos/${sesionId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                actualizarInterfaz(data.puntos, data.estadisticas);
            }
        })
        .catch(error => {
            console.error('Error al actualizar estado:', error);
        });
}

function actualizarInterfaz(puntos, estadisticas) {
    // Actualizar estadísticas
    document.getElementById('total-puntos').textContent = estadisticas.total_puntos;
    document.getElementById('puntos-habilitados').textContent = estadisticas.puntos_habilitados;
    document.getElementById('puntos-pendientes').textContent = estadisticas.total_puntos - estadisticas.puntos_habilitados;
    
    const porcentaje = estadisticas.total_puntos > 0 ? 
        Math.round((estadisticas.puntos_habilitados / estadisticas.total_puntos) * 100) : 0;
    
    document.getElementById('progreso-porcentaje').textContent = porcentaje + '%';
    document.getElementById('progreso-barra').style.width = porcentaje + '%';
    
    // Actualizar botón siguiente
    const btnSiguiente = document.getElementById('btn-siguiente');
    if (estadisticas.puntos_habilitados === estadisticas.total_puntos) {
        btnSiguiente.disabled = true;
        btnSiguiente.innerHTML = '<i class="bi bi-check-circle"></i> Todos los Puntos Habilitados';
    } else {
        btnSiguiente.disabled = false;
        btnSiguiente.innerHTML = '<i class="bi bi-play-circle"></i> Habilitar Siguiente Punto';
    }
    
    // Actualizar tabla de puntos
    puntos.forEach(punto => {
        const fila = document.querySelector(`tr[data-punto-id="${punto.id}"]`);
        if (fila) {
            // Actualizar clase de fila
            if (punto.habilitado) {
                fila.classList.add('table-success');
            } else {
                fila.classList.remove('table-success');
            }
            
            // Actualizar badge de estado
            const estadoBadge = fila.querySelector('.punto-estado');
            estadoBadge.className = `badge bg-${punto.habilitado ? 'success' : 'secondary'} punto-estado`;
            estadoBadge.textContent = punto.habilitado ? 'Habilitado' : 'Pendiente';
            
            // Actualizar usuario
            const usuarioCell = fila.querySelector('.punto-usuario');
            if (punto.habilitado && punto.first_name) {
                usuarioCell.innerHTML = `<small>${punto.first_name} ${punto.last_name}</small>`;
            } else {
                usuarioCell.innerHTML = '<span class="text-muted">-</span>';
            }
            
            // Actualizar fecha
            const fechaCell = fila.querySelector('.punto-fecha');
            if (punto.fecha_habilitacion) {
                const fecha = new Date(punto.fecha_habilitacion);
                fechaCell.innerHTML = `<small>${fecha.toLocaleDateString('es-ES', {day: '2-digit', month: '2-digit'})} ${fecha.toLocaleTimeString('es-ES', {hour: '2-digit', minute: '2-digit'})}</small>`;
            } else {
                fechaCell.innerHTML = '<span class="text-muted">-</span>';
            }
            
            // Actualizar botones de acción
            const accionesDiv = fila.querySelector('.punto-acciones');
            if (punto.habilitado) {
                accionesDiv.innerHTML = `
                    <button class="btn btn-outline-warning" 
                            onclick="deshabilitarPunto(${punto.id})"
                            title="Deshabilitar">
                        <i class="bi bi-pause-circle"></i>
                    </button>
                `;
            } else {
                accionesDiv.innerHTML = `
                    <button class="btn btn-outline-success" 
                            onclick="habilitarPunto(${punto.id})"
                            title="Habilitar">
                        <i class="bi bi-play-circle"></i>
                    </button>
                `;
            }
        }
    });
}

function habilitarPunto(puntoId) {
    console.log('Intentando habilitar punto:', puntoId);
    console.log('SesionId:', sesionId);
    console.log('CSRF Token:', '<?= $_SESSION['csrf_token'] ?>');
    console.log('URL:', '<?= BASE_URL ?>votacion/habilitar-punto');
    
    const formData = new FormData();
    formData.append('sesion_id', sesionId);
    formData.append('punto_id', puntoId);
    formData.append('csrf_token', '<?= $_SESSION['csrf_token'] ?>');
    
    fetch('<?= BASE_URL ?>votacion/habilitar-punto', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Punto Habilitado',
                text: data.message,
                timer: 2000,
                showConfirmButton: false
            });
            actualizarEstado();
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message
            });
        }
    })
    .catch(error => {
        console.error('Error completo:', error);
        console.error('Error stack:', error.stack);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error de conexión: ' + error.message
        });
    });
}

function deshabilitarPunto(puntoId) {
    Swal.fire({
        title: '¿Deshabilitar punto?',
        text: 'Este punto ya no estará disponible para votación',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ffc107',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, deshabilitar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append('sesion_id', sesionId);
            formData.append('punto_id', puntoId);
            formData.append('csrf_token', '<?= $_SESSION['csrf_token'] ?>');
            
            fetch('<?= BASE_URL ?>votacion/deshabilitar-punto', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Punto Deshabilitado',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    actualizarEstado();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message
                    });
                }
            });
        }
    });
}

function habilitarSiguiente() {
    // Buscar el siguiente punto no habilitado
    const filas = document.querySelectorAll('tr[data-punto-id]');
    let siguientePunto = null;
    
    for (let fila of filas) {
        if (!fila.classList.contains('table-success')) {
            siguientePunto = fila.getAttribute('data-punto-id');
            break;
        }
    }
    
    if (siguientePunto) {
        habilitarPunto(siguientePunto);
    }
}

function pausarTodos() {
    Swal.fire({
        title: '¿Pausar todos los puntos?',
        text: 'Esto deshabilitará todos los puntos habilitados',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ffc107',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, pausar todos',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const filasHabilitadas = document.querySelectorAll('tr.table-success[data-punto-id]');
            
            filasHabilitadas.forEach(fila => {
                const puntoId = fila.getAttribute('data-punto-id');
                deshabilitarPuntoSilencioso(puntoId);
            });
            
            setTimeout(() => {
                actualizarEstado();
                Swal.fire({
                    icon: 'success',
                    title: 'Puntos Pausados',
                    text: 'Todos los puntos han sido deshabilitados',
                    timer: 2000,
                    showConfirmButton: false
                });
            }, 1000);
        }
    });
}

function deshabilitarPuntoSilencioso(puntoId) {
    const formData = new FormData();
    formData.append('sesion_id', sesionId);
    formData.append('punto_id', puntoId);
    formData.append('csrf_token', '<?= $_SESSION['csrf_token'] ?>');
    
    fetch('<?= BASE_URL ?>votacion/deshabilitar-punto', {
        method: 'POST',
        body: formData
    });
}

function inicializarPuntos() {
    Swal.fire({
        title: '¿Inicializar puntos del orden del día?',
        text: 'Esto creará los puntos basados en el orden del día de la sesión',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#ffc107',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, inicializar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `<?= BASE_URL ?>votacion/inicializar-puntos/${sesionId}`;
        }
    });
}

function reinicializarPuntos() {
    Swal.fire({
        title: '¿Reinicializar puntos?',
        text: 'Esto volverá a crear todos los puntos y reiniciará el control progresivo',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, reinicializar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `<?= BASE_URL ?>votacion/inicializar-puntos/${sesionId}`;
        }
    });
}

// Limpiar intervals al salir de la página
window.addEventListener('beforeunload', function() {
    detenerAutoRefresh();
});

// ======== FUNCIONES DE CONTROL DE MOCIONES ========

let intervalMociones = null;

function iniciarAutoRefreshMociones() {
    if (intervalMociones) clearInterval(intervalMociones);
    intervalMociones = setInterval(cargarMociones, 3000); // Actualizar cada 3 segundos
    cargarMociones(); // Carga inicial
}

function cargarMociones() {
    console.log('=== CARGANDO MOCIONES ===');
    console.log('SesionId actual:', sesionId);
    console.log('CSRF Token:', '<?= $_SESSION['csrf_token'] ?>');
    
    const formData = new FormData();
    formData.append('sesion_id', sesionId);
    formData.append('csrf_token', '<?= $_SESSION['csrf_token'] ?>');
    
    fetch('<?= BASE_URL ?>votacion/admin-mociones', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('Status de respuesta:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('=== RESPUESTA COMPLETA ===');
        console.log('Data completa:', data);
        
        if (data.success) {
            console.log('=== ANÁLISIS DE MOCIONES ===');
            console.log('Mociones activas encontradas:', data.mociones_activas.length);
            console.log('Mociones activas detalle:', data.mociones_activas);
            console.log('Historial mociones encontradas:', data.historial_mociones.length);
            console.log('Historial mociones detalle:', data.historial_mociones);
            
            if (data.debug_info) {
                console.log('=== INFO DEBUG ===');
                console.log('Debug info:', data.debug_info);
            }
            
            actualizarTablaMociones(data.mociones_activas, data.historial_mociones);
        } else {
            console.error('=== ERROR EN RESPUESTA ===');
            console.error('Error:', data.error);
            console.error('Debug adicional:', data.debug);
        }
    })
    .catch(error => {
        console.error('=== ERROR DE CONEXIÓN ===');
        console.error('Error completo:', error);
    });
}

function actualizarTablaMociones(mocionesActivas, historialMociones) {
    // Actualizar tabla de mociones activas
    const tablaActivas = document.querySelector('#tabla-mociones-activas tbody');
    tablaActivas.innerHTML = '';
    
    mocionesActivas.forEach(mocion => {
        console.log('=== GENERANDO FILA PARA MOCIÓN ===');
        console.log('Moción:', mocion);
        console.log('ID de moción:', mocion.id);
        console.log('Estado activa:', mocion.activa);
        
        const fila = document.createElement('tr');
        fila.innerHTML = `
            <td><strong>${mocion.usuario}</strong></td>
            <td>${mocion.mensaje}</td>
            <td>${formatearHora(mocion.created_at)}</td>
            <td>
                <span class="badge bg-${mocion.activa == 1 ? 'success' : 'secondary'}">
                    ${mocion.activa == 1 ? 'Activa' : 'Pausada'}
                </span>
            </td>
            <td style="min-width: 80px;">
                <button class="btn btn-sm ${mocion.activa == 1 ? 'btn-danger' : 'btn-success'}" 
                        data-mocion-id="${mocion.id}" 
                        data-action="${mocion.activa == 1 ? 'parar' : 'reactivar'}"
                        onclick="handleMocionAction(this)"
                        title="${mocion.activa == 1 ? 'Parar esta moción' : 'Reactivar moción'}">
                    <i class="bi bi-${mocion.activa == 1 ? 'stop' : 'play'}-circle"></i> 
                    ${mocion.activa == 1 ? 'Parar' : 'Activar'}
                </button>
            </td>
        `;
        
        console.log('HTML generado para botón:', fila.querySelector('td:last-child').innerHTML);
        tablaActivas.appendChild(fila);
    });
    
    // Actualizar tabla de historial
    const tablaHistorial = document.querySelector('#tabla-historial-mociones tbody');
    tablaHistorial.innerHTML = '';
    
    historialMociones.forEach(mocion => {
        const fila = document.createElement('tr');
        fila.innerHTML = `
            <td>${mocion.usuario}</td>
            <td>${mocion.mensaje}</td>
            <td>${formatearHora(mocion.created_at)}</td>
            <td>
                <span class="badge bg-${mocion.activa == 0 ? 'secondary' : 'info'}">
                    ${mocion.activa == 0 ? 'Finalizada' : 'Expirada'}
                </span>
            </td>
        `;
        tablaHistorial.appendChild(fila);
    });
}

function formatearHora(fechaString) {
    const fecha = new Date(fechaString);
    return fecha.toLocaleTimeString('es-ES', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });
}

function pararTodasLasMociones() {
    Swal.fire({
        title: '¿Parar todas las mociones?',
        text: 'Esto detendrá todas las mociones activas de los editores',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, parar todas',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append('sesion_id', sesionId);
            formData.append('csrf_token', '<?= $_SESSION['csrf_token'] ?>');
            
            fetch('<?= BASE_URL ?>votacion/admin-parar-todas-mociones', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Mociones Detenidas',
                        text: `Se han detenido ${data.mociones_paradas} mociones`,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    cargarMociones();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Error al parar las mociones'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error de conexión'
                });
            });
        }
    });
}

// Nueva función que maneja ambas acciones desde los data attributes
function handleMocionAction(button) {
    console.log('=== HANDLE MOCIÓN ACTION ===');
    console.log('Button recibido:', button);
    
    const mocionId = button.getAttribute('data-mocion-id');
    const action = button.getAttribute('data-action');
    
    console.log('Moción ID extraído:', mocionId);
    console.log('Tipo de mocionId:', typeof mocionId);
    console.log('Action extraído:', action);
    console.log('Button element:', button);
    
    // Validaciones
    if (!mocionId) {
        console.error('ERROR: No se pudo obtener data-mocion-id del botón');
        return;
    }
    
    if (!action) {
        console.error('ERROR: No se pudo obtener data-action del botón');
        return;
    }
    
    console.log('Ejecutando acción:', action, 'para moción:', mocionId);
    
    if (action === 'parar') {
        console.log('Llamando a pararMocionIndividual con ID:', mocionId);
        pararMocionIndividual(mocionId);
    } else if (action === 'reactivar') {
        console.log('Llamando a reactivarMocion con ID:', mocionId);
        reactivarMocion(mocionId);
    } else {
        console.error('ERROR: Acción no reconocida:', action);
    }
}

function pararMocionIndividual(mocionId) {
    console.log('=== PARAR MOCIÓN INDIVIDUAL ===');
    console.log('Moción ID recibido:', mocionId);
    console.log('Tipo de mocionId:', typeof mocionId);
    
    if (!mocionId) {
        console.error('ERROR: mocionId está vacío o undefined');
        return;
    }
    
    console.log('Validación pasada, mocionId es válido');
    console.log('Iniciando SweetAlert...');
    console.log('Verificando disponibilidad de Swal:', typeof Swal);
    
    if (typeof Swal === 'undefined') {
        console.error('ERROR: SweetAlert2 no está cargado');
        alert('Error: SweetAlert2 no está disponible. Por favor recarga la página.');
        return;
    }
    
    console.log('Swal disponible, llamando a Swal.fire()...');
    
    try {
        const swalPromise = Swal.fire({
            title: '¿Parar esta moción?',
            text: 'Esta moción dejará de mostrarse a los usuarios',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, parar',
            cancelButtonText: 'Cancelar'
        });
        
        console.log('Swal.fire() llamado, promesa creada:', swalPromise);
        
        swalPromise.then((result) => {
            console.log('Resultado del diálogo:', result);
            
            if (result.isConfirmed) {
                console.log('Usuario confirmó parar la moción');
                console.log('Preparando petición para moción ID:', mocionId);
                
                const requestData = {
                    mocion_id: mocionId,
                    sesion_id: sesionId
                };
                
                console.log('Datos JSON preparados:', requestData);
                console.log('Enviando petición a:', '<?= BASE_URL ?>votacion/parar-mocion');
                
                fetch('<?= BASE_URL ?>votacion/parar-mocion', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(requestData)
                })
                .then(response => {
                    console.log('Respuesta recibida:', response.status, response.statusText);
                    return response.json();
                })
                .then(data => {
                    console.log('Data de respuesta:', data);
                    if (data.success) {
                        console.log('Moción parada exitosamente, recargando mociones...');
                        cargarMociones();
                        Swal.fire({
                            icon: 'success',
                            title: 'Moción Detenida',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    } else {
                        console.error('Error en la respuesta del servidor:', data);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'Error al parar la moción'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error en la petición:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de conexión',
                        text: 'No se pudo conectar con el servidor'
                    });
                });
            } else {
                console.log('Usuario canceló la acción');
            }
        });
    } catch (error) {
        console.error('ERROR en Swal.fire():', error);
        console.error('Stack:', error.stack);
    }
}

function reactivarMocion(mocionId) {
    const formData = new FormData();
    formData.append('mocion_id', mocionId);
    formData.append('csrf_token', '<?= $_SESSION['csrf_token'] ?>');
    
    fetch('<?= BASE_URL ?>votacion/reactivar-mocion', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            cargarMociones();
            Swal.fire({
                icon: 'success',
                title: 'Moción Reactivada',
                timer: 1500,
                showConfirmButton: false
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function refrescarMociones() {
    cargarMociones();
    Swal.fire({
        icon: 'info',
        title: 'Actualizado',
        text: 'Lista de mociones actualizada',
        timer: 1000,
        showConfirmButton: false
    });
}

function limpiarHistorialMociones() {
    Swal.fire({
        title: '¿Limpiar historial de mociones?',
        text: 'Esto eliminará todas las mociones inactivas/finalizadas de la base de datos',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ffc107',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, limpiar historial',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append('sesion_id', sesionId);
            formData.append('csrf_token', '<?= $_SESSION['csrf_token'] ?>');
            
            fetch('<?= BASE_URL ?>votacion/limpiar-historial-mociones', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    cargarMociones();
                    Swal.fire({
                        icon: 'success',
                        title: 'Historial Limpiado',
                        text: `Se eliminaron ${data.mociones_eliminadas} mociones del historial`,
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    });
}

// Detener auto-refresh de mociones al salir
window.addEventListener('beforeunload', function() {
    if (intervalMociones) clearInterval(intervalMociones);
});
</script>

<?php 
// Cerrar el buffer y renderizar dentro del layout principal
$content = ob_get_clean();
include VIEWS_PATH . 'layouts/main.php';
?>