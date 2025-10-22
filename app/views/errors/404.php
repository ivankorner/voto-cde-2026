<?php 
$current_page = 'error';
ob_start(); 
?>

<div class="text-center py-5">
    <div class="error-content">
        <div class="error-code">
            <span class="display-1 text-muted">4</span>
            <i class="bi bi-exclamation-triangle display-1 text-warning"></i>
            <span class="display-1 text-muted">4</span>
        </div>
        
        <h2 class="mt-4 mb-3">¡Ups! Página no encontrada</h2>
        
        <p class="lead text-muted mb-4">
            La página que buscas no existe o ha sido movida.
        </p>
        
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bi bi-lightbulb text-primary"></i>
                            ¿Qué puedes hacer?
                        </h5>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="bi bi-check text-success"></i>
                                Verificar la URL escrita
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check text-success"></i>
                                Regresar a la página anterior
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-check text-success"></i>
                                Ir al Dashboard principal
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <a href="javascript:history.back()" class="btn btn-outline-secondary me-2">
                <i class="bi bi-arrow-left"></i>
                Volver Atrás
            </a>
            <a href="<?= BASE_URL ?>dashboard" class="btn btn-primary">
                <i class="bi bi-house"></i>
                Ir al Dashboard
            </a>
        </div>
    </div>
</div>

<style>
.error-code {
    font-size: 8rem;
    font-weight: bold;
    line-height: 1;
    opacity: 0.8;
}

@media (max-width: 768px) {
    .error-code {
        font-size: 4rem;
    }
}
</style>

<?php 
$content = ob_get_clean();
include VIEWS_PATH . 'layouts/main.php';
?>
