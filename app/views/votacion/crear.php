<?php 
$current_page = 'votacion';
ob_start(); 
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="bi bi-plus-circle"></i>
        Crear Sesión de Votación
    </h1>
    <a href="<?= BASE_URL ?>index.php?url=votacion" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i>
        Volver a Votaciones
    </a>
</div>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-gear"></i>
                    Configuración de la Sesión
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= BASE_URL ?>index.php?url=votacion/crear">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>"><?php if (!isset($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); ?>
                    
                    <div class="mb-3">
                        <label for="orden_dia_id" class="form-label">
                            <i class="bi bi-list-ol"></i>
                            Orden del Día <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="orden_dia_id" name="orden_dia_id" required>
                            <option value="">Seleccione un orden del día...</option>
                            <?php foreach ($ordenes_del_dia as $orden): ?>
                            <option value="<?= $orden['id'] ?>"
                                    <?= (isset($_GET['orden_dia_id']) && $_GET['orden_dia_id'] == $orden['id']) ? 'selected' : '' ?>
                                    data-fecha="<?= date('d/m/Y', strtotime($orden['fecha_sesion'])) ?>"
                                    data-tipo="<?= ucfirst($orden['tipo_sesion']) ?>">
                                Acta N° <?= htmlspecialchars($orden['numero_acta']) ?> - 
                                <?= ucfirst($orden['tipo_sesion']) ?> 
                                (<?= date('d/m/Y', strtotime($orden['fecha_sesion'])) ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">
                            Seleccione el orden del día que se votará en esta sesión
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="nombre_sesion" class="form-label">
                            <i class="bi bi-tag"></i>
                            Nombre de la Sesión <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control" 
                               id="nombre_sesion" 
                               name="nombre_sesion"
                               placeholder="Ej: Sesión Ordinaria - Septiembre 2025"
                               maxlength="255"
                               required>
                        <div class="form-text">
                            Nombre identificativo para esta sesión de votación
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">
                            <i class="bi bi-text-paragraph"></i>
                            Descripción
                        </label>
                        <textarea class="form-control" 
                                  id="descripcion" 
                                  name="descripcion"
                                  rows="3"
                                  placeholder="Descripción opcional de la sesión..."></textarea>
                        <div class="form-text">
                            Información adicional sobre la sesión (opcional)
                        </div>
                    </div>
                    
                    <!-- Vista previa del orden seleccionado -->
                    <div id="orden-preview" class="mb-3" style="display: none;">
                        <div class="alert alert-info">
                            <h6><i class="bi bi-info-circle"></i> Vista Previa del Orden del Día</h6>
                            <div id="preview-content"></div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="alert alert-warning">
                                <h6><i class="bi bi-exclamation-triangle"></i> Información Importante</h6>
                                <ul class="mb-0">
                                    <li>Solo usuarios con rol de <strong>Editor</strong> pueden votar</li>
                                    <li>Cada expediente se vota individualmente</li>
                                    <li>Las actas se votan de forma global</li>
                                    <li>Los votos se registran en tiempo real</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="alert alert-success">
                                <h6><i class="bi bi-check-circle"></i> Características</h6>
                                <ul class="mb-0">
                                    <li>Visualización tipo cámara de diputados</li>
                                    <li>Resultados en tiempo real</li>
                                    <li>Control de presencia automático</li>
                                    <li>Historial completo de votaciones</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="<?= BASE_URL ?>index.php?url=votacion" class="btn btn-secondary me-md-2">
                            <i class="bi bi-x-circle"></i>
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i>
                            Crear Sesión de Votación
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ordenSelect = document.getElementById('orden_dia_id');
    const nombreInput = document.getElementById('nombre_sesion');
    const previewDiv = document.getElementById('orden-preview');
    const previewContent = document.getElementById('preview-content');
    
    // Trigger automático si viene pre-seleccionado
    if (ordenSelect.value) {
        ordenSelect.dispatchEvent(new Event('change'));
    }
    
    ordenSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if (selectedOption.value) {
            const fecha = selectedOption.getAttribute('data-fecha');
            const tipo = selectedOption.getAttribute('data-tipo');
            const numeroActa = selectedOption.text.match(/Acta N° (\d+)/)[1];
            
            // Auto-llenar nombre de sesión
            if (!nombreInput.value) {
                nombreInput.value = `Sesión ${tipo} - Acta N° ${numeroActa}`;
            }
            
            // Mostrar vista previa
            previewContent.innerHTML = `
                <div class="row">
                    <div class="col-md-4">
                        <strong>Número de Acta:</strong><br>
                        <span class="badge bg-primary">${numeroActa}</span>
                    </div>
                    <div class="col-md-4">
                        <strong>Tipo de Sesión:</strong><br>
                        <span class="badge bg-info">${tipo}</span>
                    </div>
                    <div class="col-md-4">
                        <strong>Fecha:</strong><br>
                        <span class="badge bg-success">${fecha}</span>
                    </div>
                </div>
            `;
            previewDiv.style.display = 'block';
        } else {
            previewDiv.style.display = 'none';
        }
    });
});
</script>

<?php 
$content = ob_get_clean();
include VIEWS_PATH . 'layouts/main.php';
?>