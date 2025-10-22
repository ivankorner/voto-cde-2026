<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'Sistema de Votación' ?> - <?= APP_NAME ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.1/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?= BASE_URL ?>assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <button class="sidebar-toggle-btn me-2 d-md-none" onclick="document.querySelector('.sidebar').classList.toggle('collapsed')">
                <i class="bi bi-list"></i>
            </button>
            <a class="navbar-brand" href="<?= BASE_URL ?>dashboard">
                <i class="bi bi-box-seam"></i>
                <?= APP_NAME ?>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page === 'dashboard' ? 'active' : '' ?>" href="<?= BASE_URL ?>dashboard">
                            <i class="bi bi-speedometer2"></i>
                            Dashboard
                        </a>
                    </li>
                    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page === 'users' ? 'active' : '' ?>" href="<?= BASE_URL ?>users">
                            <i class="bi bi-people"></i>
                            Usuarios
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page === 'roles' ? 'active' : '' ?>" href="<?= BASE_URL ?>roles">
                            <i class="bi bi-shield-check"></i>
                            Roles
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page === 'orden_dia' ? 'active' : '' ?>" href="<?= BASE_URL ?>orden_dia">
                            <i class="bi bi-journal-text"></i>
                            Orden del Día
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['user_role'])): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page === 'orden_dia_lista' ? 'active' : '' ?>" href="<?= BASE_URL ?>orden_dia/lista">
                            <i class="bi bi-list-ul"></i>
                            Ver Órdenes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page === 'votacion' ? 'active' : '' ?>" href="<?= BASE_URL ?>votacion">
                            <i class="bi bi-vote-fill"></i>
                            Votaciones
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
                
                <ul class="navbar-nav">
                    <!-- User Switcher para desarrollo -->
                    <li class="nav-item dropdown me-3">
                        <a class="nav-link dropdown-toggle btn btn-outline-light btn-sm" href="#" id="userSwitcher" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-arrow-repeat"></i>
                            <span class="badge bg-<?= 
                                $_SESSION['user_role'] === 'admin' ? 'danger' : 
                                ($_SESSION['user_role'] === 'editor' ? 'warning' : 'info') 
                            ?>"><?= strtoupper($_SESSION['user_role']) ?></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><h6 class="dropdown-header">Cambiar Usuario</h6></li>
                            <li>
                                <form method="POST" action="<?= BASE_URL ?>auth/switchUser" class="d-inline">
                                    <input type="hidden" name="user_id" value="1">
                                    <input type="hidden" name="redirect" value="<?= $_SERVER['REQUEST_URI'] ?>">
                                    <button type="submit" class="dropdown-item <?= $_SESSION['user_id'] == 1 ? 'active' : '' ?>">
                                        <i class="bi bi-shield-fill text-danger"></i> Admin
                                    </button>
                                </form>
                            </li>
                            <li>
                                <form method="POST" action="<?= BASE_URL ?>auth/switchUser" class="d-inline">
                                    <input type="hidden" name="user_id" value="2">
                                    <input type="hidden" name="redirect" value="<?= $_SERVER['REQUEST_URI'] ?>">
                                    <button type="submit" class="dropdown-item <?= $_SESSION['user_id'] == 2 ? 'active' : '' ?>">
                                        <i class="bi bi-pencil-fill text-warning"></i> Editor1
                                    </button>
                                </form>
                            </li>
                            <li>
                                <form method="POST" action="<?= BASE_URL ?>auth/switchUser" class="d-inline">
                                    <input type="hidden" name="user_id" value="3">
                                    <input type="hidden" name="redirect" value="<?= $_SERVER['REQUEST_URI'] ?>">
                                    <button type="submit" class="dropdown-item <?= $_SESSION['user_id'] == 3 ? 'active' : '' ?>">
                                        <i class="bi bi-eye-fill text-info"></i> Viewer1
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i>
                            <?= $_SESSION['user_name'] ?? 'Usuario' ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#"><i class="bi bi-person"></i> Perfil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>logout"><i class="bi bi-box-arrow-right"></i> Cerrar Sesión</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link <?= $current_page === 'dashboard' ? 'active' : '' ?>" href="<?= BASE_URL ?>dashboard">
                                <i class="bi bi-speedometer2"></i>
                                Dashboard
                            </a>
                        </li>
                        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= $current_page === 'users' ? 'active' : '' ?>" href="<?= BASE_URL ?>users">
                                <i class="bi bi-people"></i>
                                Usuarios
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= $current_page === 'roles' ? 'active' : '' ?>" href="<?= BASE_URL ?>roles">
                                <i class="bi bi-shield-check"></i>
                                Roles
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= $current_page === 'orden_dia' ? 'active' : '' ?>" href="<?= BASE_URL ?>orden_dia">
                                <i class="bi bi-journal-text"></i>
                                Orden del Día
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if (isset($_SESSION['user_role'])): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= $current_page === 'orden_dia_lista' ? 'active' : '' ?>" href="<?= BASE_URL ?>orden_dia/lista">
                                <i class="bi bi-list-ul"></i>
                                Ver Órdenes
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $current_page === 'votacion' ? 'active' : '' ?>" href="<?= BASE_URL ?>votacion">
                                <i class="bi bi-vote-fill"></i>
                                Votaciones
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </nav>

            <!-- Page Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2"><?= $page_title ?? 'Dashboard' ?></h1>
                </div>

                <!-- Flash Messages -->
                <?php if (isset($_SESSION['success']) || isset($_SESSION['success_message'])): ?>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            SweetAlerts.success('¡Éxito!', '<?= addslashes($_SESSION['success'] ?? $_SESSION['success_message']) ?>');
                        });
                    </script>
                    <?php 
                        unset($_SESSION['success']); 
                        unset($_SESSION['success_message']); 
                    ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['error']) || isset($_SESSION['error_message'])): ?>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            SweetAlerts.error('¡Error!', '<?= addslashes($_SESSION['error'] ?? $_SESSION['error_message']) ?>');
                        });
                    </script>
                    <?php 
                        unset($_SESSION['error']); 
                        unset($_SESSION['error_message']); 
                    ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['info']) || isset($_SESSION['info_message'])): ?>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            SweetAlerts.info('Información', '<?= addslashes($_SESSION['info'] ?? $_SESSION['info_message']) ?>');
                        });
                    </script>
                    <?php 
                        unset($_SESSION['info']); 
                        unset($_SESSION['info_message']); 
                    ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['warning']) || isset($_SESSION['warning_message'])): ?>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            SweetAlerts.warning('Advertencia', '<?= addslashes($_SESSION['warning'] ?? $_SESSION['warning_message']) ?>');
                        });
                    </script>
                    <?php 
                        unset($_SESSION['warning']); 
                        unset($_SESSION['warning_message']); 
                    ?>
                <?php endif; ?>

                <!-- Page Content -->
                <?= $content ?>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.1/dist/sweetalert2.all.min.js"></script>
    <!-- Custom JS -->
    <script src="<?= BASE_URL ?>assets/js/script.js"></script>
</body>
</html>
