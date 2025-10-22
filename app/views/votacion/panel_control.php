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
                                            <span class="badge bg-<?= $punto['item_tipo'] === 'global' ? 'info' : 'secondary' ?>">
                                                <?= $punto['item_tipo'] === 'global' ? 'Global' : 'Expediente' ?>
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
    iniciarAutoRefresh();
    
    // Configurar auto-refresh
    document.getElementById('auto-refresh').addEventListener('change', function() {
        if (this.checked) {
            iniciarAutoRefresh();
        } else {
            detenerAutoRefresh();
        }
    });
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
</script>

<?php 
// Cerrar el buffer y renderizar dentro del layout principal
$content = ob_get_clean();
include VIEWS_PATH . 'layouts/main.php';
?>