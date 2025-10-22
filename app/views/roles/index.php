<?php 
$current_page = 'roles';
ob_start(); 
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>
        <i class="bi bi-shield-check"></i>
        Gestión de Roles
    </h2>
    <a href="<?= BASE_URL ?>roles/create" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i>
        Crear Rol
    </a>
</div>

<?php if (!empty($roles)): ?>
    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Usuarios Asignados</th>
                            <th>Estado</th>
                            <th>Fecha Creación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($roles as $role): ?>
                        <tr>
                            <td><?= $role['id'] ?></td>
                            <td>
                                <strong><?= htmlspecialchars($role['name']) ?></strong>
                            </td>
                            <td>
                                <?= htmlspecialchars($role['description']) ?>
                            </td>
                            <td>
                                <span class="badge bg-info">
                                    <?= $role['user_count'] ?> usuario(s)
                                </span>
                            </td>
                            <td>
                                <?php if ($role['status'] === 'active'): ?>
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle"></i>
                                        Activo
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-danger">
                                        <i class="bi bi-x-circle"></i>
                                        Inactivo
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= date('d/m/Y H:i', strtotime($role['created_at'])) ?>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="<?= BASE_URL ?>roles/edit?id=<?= $role['id'] ?>" 
                                       class="btn btn-sm btn-outline-primary" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <?php if ($role['user_count'] == 0): ?>
                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                            onclick="deleteRole(<?= $role['id'] ?>, '<?= htmlspecialchars($role['name']) ?>')"
                                            title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    <?php else: ?>
                                    <button type="button" class="btn btn-sm btn-outline-secondary disabled" 
                                            title="No se puede eliminar: tiene usuarios asignados">
                                        <i class="bi bi-trash"></i>
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
<?php else: ?>
    <div class="card shadow">
        <div class="card-body text-center py-5">
            <i class="bi bi-shield-check fs-1 text-muted"></i>
            <h4 class="text-muted mt-3">No hay roles registrados</h4>
            <p class="text-muted">Comienza creando el primer rol del sistema</p>
            <a href="<?= BASE_URL ?>roles/create" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i>
                Crear Rol
            </a>
        </div>
    </div>
<?php endif; ?>

<!-- Modal de Confirmación de Eliminación -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle text-danger"></i>
                    Confirmar Eliminación
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar el rol <strong id="deleteRoleName"></strong>?</p>
                <p class="text-danger">
                    <i class="bi bi-exclamation-triangle"></i>
                    Esta acción no se puede deshacer.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form method="POST" action="<?= BASE_URL ?>roles/delete" style="display: inline;">
                    <input type="hidden" name="id" id="deleteRoleId">
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash"></i>
                        Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function deleteRole(id, name) {
    document.getElementById('deleteRoleId').value = id;
    document.getElementById('deleteRoleName').textContent = name;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>

<?php 
$content = ob_get_clean();
include VIEWS_PATH . 'layouts/main.php';
?>
