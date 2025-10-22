<?php 
$current_page = 'dashboard';
ob_start(); 
?>

<!-- Dashboard Stats -->
<div class="row mb-4">
    <?php if ($_SESSION['user_role'] === 'admin'): ?>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Usuarios
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= $stats['total_users'] ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-people fs-2 text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Total Roles
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= $stats['total_roles'] ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-shield-check fs-2 text-info"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="<?= ($_SESSION['user_role'] === 'admin') ? 'col-xl-3' : 'col-xl-6' ?> col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Usuario Activo
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= $_SESSION['user_name'] ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-person-circle fs-2 text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="<?= ($_SESSION['user_role'] === 'admin') ? 'col-xl-3' : 'col-xl-6' ?> col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Rol
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= ucfirst($_SESSION['user_role']) ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-award fs-2 text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<?php if ($_SESSION['user_role'] === 'admin'): ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-lightning"></i>
                    Acciones Rápidas
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <a href="<?= BASE_URL ?>users" class="btn btn-outline-primary btn-lg w-100">
                            <i class="bi bi-people"></i>
                            <br>
                            Gestionar Usuarios
                        </a>
                    </div>
                    <div class="col-md-4 mb-3">
                        <a href="<?= BASE_URL ?>users/create" class="btn btn-outline-success btn-lg w-100">
                            <i class="bi bi-person-plus"></i>
                            <br>
                            Crear Usuario
                        </a>
                    </div>
                    <div class="col-md-4 mb-3">
                        <a href="<?= BASE_URL ?>roles" class="btn btn-outline-info btn-lg w-100">
                            <i class="bi bi-shield-check"></i>
                            <br>
                            Gestionar Roles
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php else: ?>
<!-- Welcome Message for Non-Admin Users -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-house"></i>
                    Bienvenido al Sistema
                </h6>
            </div>
            <div class="card-body">
                <div class="text-center py-4">
                    <i class="bi bi-person-circle fs-1 text-primary mb-3"></i>
                    <h4>¡Hola, <?= $_SESSION['user_name'] ?>!</h4>
                    <p class="text-muted">
                        Estás conectado como <strong><?= ucfirst($_SESSION['user_role']) ?></strong>
                    </p>
                    <p class="lead">
                        <?php if ($_SESSION['user_role'] === 'editor'): ?>
                            Tienes permisos de edición en el sistema. Puedes consultar y modificar contenido según tus permisos asignados.
                        <?php elseif ($_SESSION['user_role'] === 'viewer'): ?>
                            Tienes permisos de solo lectura. Puedes consultar información del sistema.
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Recent Users -->
<?php if ($_SESSION['user_role'] === 'admin'): ?>
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-clock-history"></i>
                    Usuarios Recientes
                </h6>
            </div>
            <div class="card-body">
                <?php if (!empty($recent_users)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Usuario</th>
                                    <th>Nombre Completo</th>
                                    <th>Email</th>
                                    <th>Rol</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_users as $user): ?>
                                <tr>
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
                                            <span class="badge bg-success">Activo</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Inactivo</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center mt-3">
                        <a href="<?= BASE_URL ?>users" class="btn btn-primary">
                            Ver Todos los Usuarios
                        </a>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="bi bi-people fs-1 text-muted"></i>
                        <p class="text-muted mt-2">No hay usuarios registrados</p>
                        <a href="<?= BASE_URL ?>users/create" class="btn btn-primary">
                            Crear Primer Usuario
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php else: ?>
<!-- Activity Information for Non-Admin Users -->
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-activity"></i>
                    Información del Sistema
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card border-left-info h-100">
                            <div class="card-body">
                                <h5 class="card-title text-info">
                                    <i class="bi bi-info-circle"></i>
                                    Tu Perfil
                                </h5>
                                <p class="card-text">
                                    <strong>Usuario:</strong> <?= $_SESSION['user_name'] ?><br>
                                    <strong>Rol:</strong> <?= ucfirst($_SESSION['user_role']) ?><br>
                                    <strong>Estado:</strong> <span class="badge bg-success">Activo</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-left-success h-100">
                            <div class="card-body">
                                <h5 class="card-title text-success">
                                    <i class="bi bi-check-circle"></i>
                                    Sistema Operativo
                                </h5>
                                <p class="card-text">
                                    El sistema está funcionando correctamente.<br>
                                    <strong>Fecha:</strong> <?= date('d/m/Y H:i') ?><br>
                                    <strong>Estado:</strong> <span class="badge bg-success">En línea</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php 
$content = ob_get_clean();
include VIEWS_PATH . 'layouts/main.php';
?>
