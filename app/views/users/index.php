<?php 
$current_page = 'users';
ob_start(); 
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>
        <i class="bi bi-people"></i>
        Gestión de Usuarios
    </h2>
    <?php if ($_SESSION['user_role'] === 'admin'): ?>
    <a href="<?= BASE_URL ?>users/create" class="btn btn-primary">
        <i class="bi bi-person-plus"></i>
        Crear Usuario
    </a>
    <?php endif; ?>
</div>

<?php if (!empty($users)): ?>
    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Nombre Completo</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Fecha Creación</th>
                            <?php if ($_SESSION['user_role'] === 'admin'): ?>
                            <th>Acciones</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= $user['id'] ?></td>
                            <td>
                                <strong><?= htmlspecialchars($user['username']) ?></strong>
                            </td>
                            <td>
                                <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($user['email']) ?>
                            </td>
                            <td>
                                <span class="badge bg-primary">
                                    <?= htmlspecialchars($user['role_name'] ?? 'Sin rol') ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($user['status'] === 'active'): ?>
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
                                <?= date('d/m/Y H:i', strtotime($user['created_at'])) ?>
                            </td>
                            <?php if ($_SESSION['user_role'] === 'admin'): ?>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="<?= BASE_URL ?>users/edit?id=<?= $user['id'] ?>" 
                                       class="btn btn-sm btn-outline-primary" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                            onclick="deleteUser(<?= $user['id'] ?>, '<?= htmlspecialchars($user['username']) ?>')"
                                            title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <?php endif; ?>
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
            <i class="bi bi-people fs-1 text-muted"></i>
            <h4 class="text-muted mt-3">No hay usuarios registrados</h4>
            <p class="text-muted">Comienza creando el primer usuario del sistema</p>
            <?php if ($_SESSION['user_role'] === 'admin'): ?>
            <a href="<?= BASE_URL ?>users/create" class="btn btn-primary">
                <i class="bi bi-person-plus"></i>
                Crear Usuario
            </a>
            <?php endif; ?>
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
                <p>¿Estás seguro de que deseas eliminar el usuario <strong id="deleteUsername"></strong>?</p>
                <p class="text-danger">
                    <i class="bi bi-exclamation-triangle"></i>
                    Esta acción no se puede deshacer.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form method="POST" action="<?= BASE_URL ?>users/delete" style="display: inline;">
                    <input type="hidden" name="id" id="deleteUserId">
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
function deleteUser(id, username) {
    document.getElementById('deleteUserId').value = id;
    document.getElementById('deleteUsername').textContent = username;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>

<?php 
$content = ob_get_clean();
include VIEWS_PATH . 'layouts/main.php';
?>
