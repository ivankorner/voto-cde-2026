<?php 
$current_page = 'roles';
ob_start(); 
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>
        <i class="bi bi-plus-circle"></i>
        Crear Rol
    </h2>
    <a href="<?= BASE_URL ?>roles" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i>
        Volver
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-body">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="bi bi-exclamation-triangle"></i>
                        <?= $error ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="alert alert-success" role="alert">
                        <i class="bi bi-check-circle"></i>
                        <?= $success ?>
                    </div>
                <?php endif; ?>

                <form method="POST" novalidate>
                    <div class="mb-3">
                        <label for="name" class="form-label">
                            <i class="bi bi-shield"></i>
                            Nombre del Rol *
                        </label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?= htmlspecialchars($data['name'] ?? '') ?>" required>
                        <div class="form-text">Nombre único para identificar el rol (ej: admin, editor, viewer)</div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">
                            <i class="bi bi-file-text"></i>
                            Descripción *
                        </label>
                        <textarea class="form-control" id="description" name="description" rows="3" required><?= htmlspecialchars($data['description'] ?? '') ?></textarea>
                        <div class="form-text">Descripción detallada de las funciones y permisos del rol</div>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">
                            <i class="bi bi-toggle-on"></i>
                            Estado
                        </label>
                        <select class="form-select" id="status" name="status">
                            <option value="active" <?= (isset($data['status']) && $data['status'] === 'active') ? 'selected' : 'selected' ?>>
                                Activo
                            </option>
                            <option value="inactive" <?= (isset($data['status']) && $data['status'] === 'inactive') ? 'selected' : '' ?>>
                                Inactivo
                            </option>
                        </select>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="<?= BASE_URL ?>roles" class="btn btn-secondary me-md-2">
                            <i class="bi bi-x-circle"></i>
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i>
                            Crear Rol
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean();
include VIEWS_PATH . 'layouts/main.php';
?>
