<?php 
$current_page = 'users';
ob_start(); 
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>
        <i class="bi bi-person-plus"></i>
        Crear Usuario
    </h2>
    <a href="<?= BASE_URL ?>users" class="btn btn-secondary">
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
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="username" class="form-label">
                                    <i class="bi bi-person"></i>
                                    Usuario *
                                </label>
                                <input type="text" class="form-control" id="username" name="username" 
                                       value="<?= htmlspecialchars($data['username'] ?? '') ?>" required>
                                <div class="form-text">Nombre único para el acceso al sistema</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    <i class="bi bi-envelope"></i>
                                    Email *
                                </label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?= htmlspecialchars($data['email'] ?? '') ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="first_name" class="form-label">
                                    <i class="bi bi-person-badge"></i>
                                    Nombre *
                                </label>
                                <input type="text" class="form-control" id="first_name" name="first_name" 
                                       value="<?= htmlspecialchars($data['first_name'] ?? '') ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="last_name" class="form-label">
                                    <i class="bi bi-person-badge"></i>
                                    Apellido *
                                </label>
                                <input type="text" class="form-control" id="last_name" name="last_name" 
                                       value="<?= htmlspecialchars($data['last_name'] ?? '') ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <i class="bi bi-lock"></i>
                                    Contraseña *
                                </label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <div class="form-text">Mínimo 6 caracteres</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="puesto" class="form-label">
                                    <i class="bi bi-briefcase"></i>
                                    Puesto
                                </label>
                                <select class="form-select" id="puesto" name="puesto">
                                    <option value="">Seleccionar puesto...</option>
                                    <option value="Presidente" <?= (isset($data['puesto']) && $data['puesto'] === 'Presidente') ? 'selected' : '' ?>>
                                        Presidente
                                    </option>
                                    <option value="Vice Presidente" <?= (isset($data['puesto']) && $data['puesto'] === 'Vice Presidente') ? 'selected' : '' ?>>
                                        Vice Presidente
                                    </option>
                                    <option value="Concejal" <?= (isset($data['puesto']) && $data['puesto'] === 'Concejal') ? 'selected' : '' ?>>
                                        Concejal
                                    </option>
                                    <option value="Secretario" <?= (isset($data['puesto']) && $data['puesto'] === 'Secretario') ? 'selected' : '' ?>>
                                        Secretario
                                    </option>
                                    <option value="Pro Secretario" <?= (isset($data['puesto']) && $data['puesto'] === 'Pro Secretario') ? 'selected' : '' ?>>
                                        Pro Secretario
                                    </option>
                                </select>
                                <div class="form-text">Cargo o posición del usuario en el consejo</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="role_id" class="form-label">
                                    <i class="bi bi-shield"></i>
                                    Rol *
                                </label>
                                <select class="form-select" id="role_id" name="role_id" required>
                                    <option value="">Seleccionar rol...</option>
                                    <?php foreach ($roles as $role): ?>
                                        <option value="<?= $role['id'] ?>" 
                                                <?= (isset($data['role_id']) && $data['role_id'] == $role['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($role['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
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
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="<?= BASE_URL ?>users" class="btn btn-secondary me-md-2">
                            <i class="bi bi-x-circle"></i>
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i>
                            Crear Usuario
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
