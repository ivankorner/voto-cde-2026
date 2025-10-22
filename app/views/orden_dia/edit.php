<?php 
$current_page = 'orden_dia';
ob_start(); 
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="bi bi-pencil"></i>
        Editar Orden del Día - Acta <?= htmlspecialchars($orden['numero_acta']) ?>
    </h1>
    <div>
        <a href="<?= BASE_URL ?>orden_dia/view/<?= $orden['id'] ?>" class="btn btn-info">
            <i class="bi bi-eye"></i>
            Vista Previa
        </a>
        <a href="<?= BASE_URL ?>orden_dia" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i>
            Volver a Lista
        </a>
    </div>
</div>

<!-- Información básica de la orden del día -->
<div class="row mb-4">
    <div class="col-lg-8">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-info-circle"></i>
                    Información Básica
                </h6>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= BASE_URL ?>orden_dia/update/<?= $orden['id'] ?>">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="numero_acta" class="form-label">Número de Acta</label>
                                <input type="text" class="form-control" id="numero_acta" name="numero_acta" 
                                       value="<?= htmlspecialchars($orden['numero_acta']) ?>" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tipo_sesion" class="form-label">Tipo de Sesión</label>
                                <select class="form-select" id="tipo_sesion" name="tipo_sesion">
                                    <option value="ordinaria" <?= $orden['tipo_sesion'] === 'ordinaria' ? 'selected' : '' ?>>
                                        Ordinaria
                                    </option>
                                    <option value="extraordinaria" <?= $orden['tipo_sesion'] === 'extraordinaria' ? 'selected' : '' ?>>
                                        Extraordinaria
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fecha_sesion" class="form-label">Fecha de Sesión</label>
                                <input type="date" class="form-control" id="fecha_sesion" name="fecha_sesion" 
                                       value="<?= $orden['fecha_sesion'] ?>" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="hora_sesion" class="form-label">Hora de Sesión</label>
                                <input type="time" class="form-control" id="hora_sesion" name="hora_sesion" 
                                       value="<?= $orden['hora_sesion'] ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="estado" class="form-label">Estado</label>
                                <select class="form-select" id="estado" name="estado">
                                    <option value="borrador" <?= $orden['estado'] === 'borrador' ? 'selected' : '' ?>>
                                        Borrador
                                    </option>
                                    <option value="publicado" <?= $orden['estado'] === 'publicado' ? 'selected' : '' ?>>
                                        Publicado
                                    </option>
                                    <option value="archivado" <?= $orden['estado'] === 'archivado' ? 'selected' : '' ?>>
                                        Archivado
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i>
                        Actualizar Información
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-info">
                    <i class="bi bi-calendar-event"></i>
                    Datos de la Sesión
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border rounded p-2">
                            <h5 class="text-primary mb-0"><?= date('d', strtotime($orden['fecha_sesion'])) ?></h5>
                            <small class="text-muted"><?= strftime('%B', strtotime($orden['fecha_sesion'])) ?></small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-2">
                            <h5 class="text-success mb-0"><?= date('H:i', strtotime($orden['hora_sesion'])) ?></h5>
                            <small class="text-muted">Hora</small>
                        </div>
                    </div>
                </div>
                
                <div class="mt-3">
                    <p class="mb-1"><strong>Estado:</strong> 
                        <span class="badge <?= $orden['estado'] === 'publicado' ? 'bg-success' : ($orden['estado'] === 'borrador' ? 'bg-warning' : 'bg-secondary') ?>">
                            <?= ucfirst($orden['estado']) ?>
                        </span>
                    </p>
                    <p class="mb-1"><strong>Creada por:</strong> <?= htmlspecialchars($orden['created_by_name']) ?></p>
                    <p class="mb-0"><strong>Tipo:</strong> <?= ucfirst($orden['tipo_sesion']) ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Ítems de la orden del día -->
<div class="card shadow">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="bi bi-list-ol"></i>
            Ítems de la Orden del Día
        </h6>
    </div>
    <div class="card-body">
        <?php foreach ($items as $item): ?>
        <div class="card mb-3" id="item-<?= $item['id'] ?>">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <span class="badge bg-primary me-2"><?= $item['numero_orden'] ?>º</span>
                        <?= htmlspecialchars($item['titulo']) ?>
                    </h6>
                    <button class="btn btn-sm btn-outline-secondary" type="button" 
                            data-bs-toggle="collapse" 
                            data-bs-target="#collapse-<?= $item['id'] ?>" 
                            aria-expanded="false">
                        <i class="bi bi-chevron-down"></i>
                    </button>
                </div>
            </div>
            
            <div class="collapse" id="collapse-<?= $item['id'] ?>">
                <div class="card-body">
                    <!-- Descripción del ítem -->
                    <form method="POST" action="<?= BASE_URL ?>orden_dia/updateItem/<?= $item['id'] ?>" class="mb-3">
                        <div class="mb-3">
                            <label class="form-label">Descripción/Observaciones</label>
                            <textarea class="form-control" name="descripcion" rows="2" 
                                      placeholder="Observaciones adicionales para este ítem..."><?= htmlspecialchars($item['descripcion'] ?? '') ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-check"></i> Actualizar Descripción
                        </button>
                    </form>
                    
                    <?php if ($item['tipo_item'] === 'lectura_actas'): ?>
                        <!-- Gestión de Actas (solo para ítem 3) -->
                        <div class="border rounded p-3 mb-3">
                            <h6><i class="bi bi-file-earmark-text"></i> Actas a Considerar</h6>
                            
                            <!-- Lista de actas existentes -->
                            <?php if (!empty($item['actas'])): ?>
                                <div class="mb-3">
                                    <?php foreach ($item['actas'] as $acta): ?>
                                    <div class="d-flex justify-content-between align-items-center border rounded p-2 mb-2">
                                        <div>
                                            <strong>Acta N° <?= htmlspecialchars($acta['numero_acta']) ?></strong>
                                            <span class="badge bg-info ms-2"><?= htmlspecialchars($acta['tipo_sesion']) ?></span>
                                            <br>
                                            <small class="text-muted">Fecha: <?= date('d/m/Y', strtotime($acta['fecha_acta'])) ?></small>
                                        </div>
                                        <a href="<?= BASE_URL ?>orden_dia/deleteActa/<?= $acta['id'] ?>" 
                                           class="btn btn-sm btn-outline-danger"
                                           onclick="event.preventDefault(); SweetAlerts.confirmDelete('esta acta').then((result) => { if (result.isConfirmed) window.location.href = this.href; });">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Formulario para agregar nueva acta -->
                            <form method="POST" action="<?= BASE_URL ?>orden_dia/addActa/<?= $item['id'] ?>">
                                <div class="row">
                                    <div class="col-md-4">
                                        <input type="text" class="form-control form-control-sm" 
                                               name="numero_acta" placeholder="N° Acta" required>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control form-control-sm" 
                                               name="tipo_sesion" placeholder="Tipo de Sesión" required>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <input type="date" class="form-control form-control-sm" 
                                                   name="fecha_acta" required>
                                            <button type="submit" class="btn btn-sm btn-success">
                                                <i class="bi bi-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        
                    <?php elseif (in_array($item['tipo_item'], ['expedientes_fuera_termino', 'tratamientos', 'proyectos_concejales', 'proyectos_ejecutivo', 'notas_ejecutivo', 'notas_oficiales', 'notas_particulares', 'dictamenes_comisiones', 'temas_internos'])): ?>
                        <!-- Gestión de Expedientes -->
                        <div class="border rounded p-3 mb-3">
                            <h6><i class="bi bi-folder"></i> Expedientes</h6>
                            
                            <!-- Lista de expedientes existentes -->
                            <?php if (!empty($item['expedientes'])): ?>
                                <div class="mb-3">
                                    <?php foreach ($item['expedientes'] as $expediente): ?>
                                    <div class="border rounded p-3 mb-2">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">
                                                    <i class="bi bi-file-earmark"></i>
                                                    EXPTE. N° <?= htmlspecialchars($expediente['numero_expediente']) ?>
                                                </h6>
                                                <?php if ($expediente['bloque_autor']): ?>
                                                    <p class="mb-1"><strong>Bloque:</strong> <?= htmlspecialchars($expediente['bloque_autor']) ?></p>
                                                <?php endif; ?>
                                                <?php if ($expediente['concejal_autor']): ?>
                                                    <p class="mb-1"><strong>Autor:</strong> <?= htmlspecialchars($expediente['concejal_autor']) ?></p>
                                                <?php endif; ?>
                                                <p class="mb-1"><?= nl2br(htmlspecialchars($expediente['extracto'])) ?></p>
                                                <?php if ($expediente['comision']): ?>
                                                    <p class="mb-1"><strong>Comisión:</strong> <?= htmlspecialchars($expediente['comision']) ?></p>
                                                <?php endif; ?>
                                                <?php if ($expediente['tipo_instrumento']): ?>
                                                    <span class="badge bg-secondary"><?= ucfirst($expediente['tipo_instrumento']) ?></span>
                                                <?php endif; ?>
                                            </div>
                                            <a href="<?= BASE_URL ?>orden_dia/deleteExpediente/<?= $expediente['id'] ?>" 
                                               class="btn btn-sm btn-outline-danger ms-2"
                                               onclick="event.preventDefault(); SweetAlerts.confirmDelete('este expediente').then((result) => { if (result.isConfirmed) window.location.href = this.href; });">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Formulario para agregar nuevo expediente -->
                            <form method="POST" action="<?= BASE_URL ?>orden_dia/addExpediente/<?= $item['id'] ?>">
                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <input type="text" class="form-control form-control-sm" 
                                               name="numero_expediente" placeholder="N° de Expediente (ej: 253-B-205/2.025)" required>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control form-control-sm" 
                                               name="comision" placeholder="Comisión">
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <input type="text" class="form-control form-control-sm" 
                                               name="bloque_autor" placeholder="Bloque (ej: Frente Renovador)">
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control form-control-sm" 
                                               name="concejal_autor" placeholder="Concejal Autor">
                                    </div>
                                </div>
                                <?php if ($item['tipo_item'] === 'dictamenes_comisiones'): ?>
                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <select class="form-select form-select-sm" name="tipo_instrumento">
                                            <option value="">Seleccionar tipo de instrumento</option>
                                            <option value="ordenanza">Ordenanza</option>
                                            <option value="resolucion">Resolución</option>
                                            <option value="comunicacion">Comunicación</option>
                                            <option value="declaracion">Declaración</option>
                                        </select>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <div class="mb-2">
                                    <textarea class="form-control form-control-sm" name="extracto" rows="3" 
                                              placeholder="Extracto del proyecto..." required></textarea>
                                </div>
                                <button type="submit" class="btn btn-sm btn-success">
                                    <i class="bi bi-plus-circle"></i> Agregar Expediente
                                </button>
                            </form>
                        </div>
                        
                    <?php elseif ($item['tipo_item'] === 'espacio_ciudadano'): ?>
                        <!-- Gestión de Espacio Ciudadano -->
                        <div class="border rounded p-3 mb-3">
                            <h6><i class="bi bi-people"></i> Ciudadanos Inscriptos</h6>
                            
                            <!-- Lista de ciudadanos -->
                            <?php if (!empty($item['expedientes'])): ?>
                                <div class="mb-3">
                                    <?php foreach ($item['expedientes'] as $ciudadano): ?>
                                    <div class="d-flex justify-content-between align-items-center border rounded p-2 mb-2">
                                        <div>
                                            <i class="bi bi-person"></i>
                                            <?= htmlspecialchars($ciudadano['nombre_ciudadano']) ?>
                                        </div>
                                        <a href="<?= BASE_URL ?>orden_dia/deleteExpediente/<?= $ciudadano['id'] ?>" 
                                           class="btn btn-sm btn-outline-danger"
                                           onclick="event.preventDefault(); SweetAlerts.confirmDelete('este ciudadano').then((result) => { if (result.isConfirmed) window.location.href = this.href; });">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Formulario para agregar ciudadano -->
                            <form method="POST" action="<?= BASE_URL ?>orden_dia/addExpediente/<?= $item['id'] ?>">
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm" 
                                           name="nombre_ciudadano" placeholder="Apellido, Nombre" required>
                                    <button type="submit" class="btn btn-sm btn-success">
                                        <i class="bi bi-plus"></i> Agregar
                                    </button>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
// Script para mejorar la experiencia de usuario
document.addEventListener('DOMContentLoaded', function() {
    // Auto-expandir el ítem si hay un hash en la URL
    if (window.location.hash) {
        const itemId = window.location.hash.replace('#item-', '');
        const collapseElement = document.getElementById('collapse-' + itemId);
        if (collapseElement) {
            const collapse = new bootstrap.Collapse(collapseElement, {show: true});
        }
    }
});
</script>

<?php 
$content = ob_get_clean();
include VIEWS_PATH . 'layouts/main.php';
?>