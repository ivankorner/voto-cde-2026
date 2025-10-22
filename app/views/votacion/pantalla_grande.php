<?php 
// Vista pública optimizada para pantallas grandes (65+ pulgadas) - Versión integrada
$current_page = 'votacion_pantalla_grande';
ob_start(); 
?>

<style>
    /* === OPTIMIZACIÓN ESPECÍFICA PARA PANTALLAS GRANDES === */
    
    :root {
        --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #5a67d8 100%);
        --success-gradient: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        --warning-gradient: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
        --danger-gradient: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);
        --glass-bg: rgba(255, 255, 255, 0.95);
        --glass-border: rgba(255, 255, 255, 0.2);
        --shadow-xl: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        --text-primary: #1a202c;
        --text-secondary: #4a5568;
    }
    
    /* Override body styles for large screen */
    body {
        font-size: 20px !important; /* Increased base font size */
        background: var(--primary-gradient) !important;
    }
    
    /* Main container for large screens */
    .container-fluid {
        max-width: none !important;
        padding: 2rem !important;
    }
    
    /* Large screen header */
    .header-pantalla-grande {
        background: var(--glass-bg);
        backdrop-filter: blur(20px) saturate(180%);
        border-radius: 25px;
        padding: 3rem;
        margin-bottom: 3rem;
        box-shadow: var(--shadow-xl);
        border: 1px solid var(--glass-border);
    }
    
    .titulo-pantalla-grande {
        font-size: 4rem !important;
        font-weight: 800;
        background: var(--primary-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 1rem;
    }
    
    .subtitulo-pantalla-grande {
        font-size: 2rem !important;
        color: var(--text-secondary);
        font-weight: 500;
    }
    
    /* Grid layout for large screens */
    .main-grid-large {
        display: grid;
        grid-template-columns: 1.2fr 0.8fr;
        gap: 3rem;
        margin-top: 3rem;
    }
    
    /* Voting section enhanced */
    .votacion-section-large {
        background: var(--glass-bg);
        backdrop-filter: blur(20px);
        border-radius: 25px;
        padding: 3rem;
        box-shadow: var(--shadow-xl);
        border: 1px solid var(--glass-border);
    }
    
    .section-title-large {
        font-size: 3rem !important;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }
    
    .section-title-large i {
        font-size: 3rem;
        color: #667eea;
    }
    
    .punto-votacion-large {
        background: white;
        border-radius: 20px;
        padding: 3rem;
        margin-bottom: 2rem;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        border-left: 10px solid #667eea;
        transition: all 0.3s ease;
    }
    
    .punto-votacion-large:hover {
        transform: translateY(-8px);
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
    }
    
    .punto-numero-large {
        font-size: 2.5rem !important;
        font-weight: 800;
        color: #667eea;
        margin-bottom: 1.5rem;
    }
    
    .punto-expediente-large {
        font-size: 1.8rem !important;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 1.5rem;
    }
    
    .punto-descripcion-large {
        font-size: 1.4rem !important;
        color: var(--text-secondary);
        line-height: 1.6;
        margin-bottom: 2rem;
    }
    
    /* Results grid for large screens */
    .resultados-grid-large {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 2rem;
        margin-top: 2rem;
    }
    
    .resultado-item-large {
        background: white;
        border-radius: 20px;
        padding: 2.5rem;
        text-align: center;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }
    
    .resultado-item-large:hover {
        transform: translateY(-5px);
    }
    
    .resultado-positivo-large {
        border-top: 8px solid #28a745;
    }
    
    .resultado-negativo-large {
        border-top: 8px solid #dc3545;
    }
    
    .resultado-abstencion-large {
        border-top: 8px solid #ffc107;
    }
    
    .resultado-numero-large {
        font-size: 4rem !important;
        font-weight: 900;
        margin-bottom: 1rem;
    }
    
    .resultado-positivo-large .resultado-numero-large {
        color: #28a745;
    }
    
    .resultado-negativo-large .resultado-numero-large {
        color: #dc3545;
    }
    
    .resultado-abstencion-large .resultado-numero-large {
        color: #ffc107;
    }
    
    .resultado-label-large {
        font-size: 1.4rem !important;
        font-weight: 600;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 1.5px;
    }
    
    /* Hemiciclo enhanced */
    .hemiciclo-section-large {
        background: var(--glass-bg);
        backdrop-filter: blur(20px);
        border-radius: 25px;
        padding: 3rem;
        box-shadow: var(--shadow-xl);
        border: 1px solid var(--glass-border);
    }
    
    .hemiciclo-grid-large {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.5rem;
        margin-top: 2rem;
    }
    
    .miembro-hemiciclo-large {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        text-align: center;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        border: 4px solid #e9ecef;
    }
    
    .miembro-presente-large {
        border-color: #28a745;
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        animation: pulse-presente 3s infinite;
    }
    
    @keyframes pulse-presente {
        0%, 100% { box-shadow: 0 10px 25px rgba(40, 167, 69, 0.2); }
        50% { box-shadow: 0 15px 35px rgba(40, 167, 69, 0.4); }
    }
    
    .miembro-avatar-large {
        width: 80px;
        height: 80px;
        background: var(--primary-gradient);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        color: white;
        font-size: 2rem;
        font-weight: 800;
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
    }
    
    .miembro-nombre-large {
        font-size: 1.3rem !important;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 1rem;
        line-height: 1.3;
    }
    
    .miembro-estado-large {
        padding: 0.8rem 1.5rem;
        border-radius: 30px;
        font-size: 1.1rem !important;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    .presente-large {
        background: #28a745;
        color: white;
        box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
    }
    
    .ausente-large {
        background: #6c757d;
        color: white;
        box-shadow: 0 5px 15px rgba(108, 117, 125, 0.3);
    }
    
    /* Empty state for large screens */
    .sin-votacion-large {
        text-align: center;
        padding: 5rem;
        background: rgba(108, 117, 125, 0.1);
        border-radius: 25px;
        margin: 3rem 0;
        backdrop-filter: blur(10px);
    }
    
    .sin-votacion-large i {
        font-size: 6rem !important;
        color: #6c757d;
        margin-bottom: 3rem;
    }
    
    .sin-votacion-large h3 {
        font-size: 3rem !important;
        color: var(--text-secondary);
        margin-bottom: 2rem;
        font-weight: 700;
    }
    
    .sin-votacion-large p {
        font-size: 1.8rem !important;
        color: var(--text-secondary);
    }
    
    /* Status indicators */
    .status-indicator-large {
        position: fixed;
        top: 2rem;
        right: 2rem;
        background: var(--glass-bg);
        backdrop-filter: blur(20px);
        border-radius: 20px;
        padding: 2rem;
        box-shadow: var(--shadow-xl);
        border: 1px solid var(--glass-border);
        z-index: 1000;
    }
    
    .fecha-hora-large {
        font-size: 1.8rem !important;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 1rem;
    }
    
    .estado-sesion-large {
        padding: 1rem 2rem;
        border-radius: 50px;
        font-weight: 700;
        font-size: 1.3rem !important;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        text-align: center;
    }
    
    .estado-activa-large {
        background: var(--success-gradient);
        color: white;
        animation: pulse-glow-large 2s infinite;
    }
    
    .estado-pausada-large {
        background: var(--warning-gradient);
        color: white;
    }
    
    .estado-inactiva-large {
        background: var(--danger-gradient);
        color: white;
    }
    
    @keyframes pulse-glow-large {
        0%, 100% { box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3); }
        50% { box-shadow: 0 12px 35px rgba(40, 167, 69, 0.6); }
    }
    
    /* Mociones optimizadas para pantalla grande */
    .swal-mocion-large .swal2-popup {
        width: 60% !important;
        max-width: none !important;
        font-size: 1.8rem !important;
        border-radius: 25px !important;
        border-left: 10px solid #e67e22 !important;
    }
    
    .swal-mocion-large .swal2-title {
        font-size: 3rem !important;
        color: #d68910 !important;
    }
    
    .swal-mocion-large .swal2-html-container {
        font-size: 1.6rem !important;
        line-height: 1.6 !important;
    }
    
    /* Responsive adjustments for extra large screens */
    @media (min-width: 2560px) {
        .titulo-pantalla-grande {
            font-size: 6rem !important;
        }
        
        .section-title-large {
            font-size: 4rem !important;
        }
        
        .resultado-numero-large {
            font-size: 6rem !important;
        }
        
        .hemiciclo-grid-large {
            grid-template-columns: repeat(6, 1fr);
        }
    }
    
    @media (min-width: 3440px) {
        .hemiciclo-grid-large {
            grid-template-columns: repeat(8, 1fr);
        }
        
        .main-grid-large {
            grid-template-columns: 1.5fr 1fr;
            gap: 4rem;
        }
    }
</style>

<!-- Status Indicator (fixed position) -->
<div class="status-indicator-large">
    <div class="fecha-hora-large" id="fecha-hora-large"></div>
    <div class="estado-sesion-large estado-<?= $sesion['estado'] ?>-large">
        <i class="bi bi-<?= $sesion['estado'] === 'activa' ? 'play-circle' : ($sesion['estado'] === 'pausada' ? 'pause-circle' : 'stop-circle') ?>"></i>
        <?= ucfirst($sesion['estado']) ?>
    </div>
</div>

<!-- Header Principal para Pantalla Grande -->
<div class="header-pantalla-grande">
    <div class="row align-items-center">
        <div class="col-8">
            <h1 class="titulo-pantalla-grande">
                <i class="bi bi-building me-3"></i>
                CONCEJO DELIBERANTE
            </h1>
            <p class="subtitulo-pantalla-grande">
                <?= htmlspecialchars($sesion['nombre'] ?? 'Sesión de Votación') ?>
            </p>
        </div>
        <div class="col-4 text-end">
            <div class="badge bg-primary p-3" style="font-size: 1.5rem;">
                <i class="bi bi-calendar-event me-2"></i>
                <?= date('d/m/Y', strtotime($sesion['fecha_sesion'] ?? 'now')) ?>
            </div>
        </div>
    </div>
</div>

<!-- Grid Principal para Pantalla Grande -->
<div class="main-grid-large">
    <!-- Sección de Votación -->
    <div class="votacion-section-large">
        <h2 class="section-title-large">
            <i class="bi bi-check-circle"></i>
            Puntos en Votación
        </h2>
        
        <?php if (empty($puntos_habilitados)): ?>
            <div class="sin-votacion-large">
                <i class="bi bi-clock-history"></i>
                <h3>No hay votaciones activas</h3>
                <p>Los puntos aparecerán aquí cuando sean habilitados para votación</p>
            </div>
        <?php else: ?>
            <?php foreach ($puntos_habilitados as $punto): ?>
                <div class="punto-votacion-large" data-punto-id="<?= $punto['punto_id'] ?>">
                    <div class="punto-numero-large">
                        Punto <?= $punto['numero'] ?>
                    </div>
                    
                    <?php if ($punto['tipo'] === 'expediente'): ?>
                        <div class="punto-expediente-large">
                            <?= htmlspecialchars($punto['numero_expediente'] ?? 'Sin número') ?>
                        </div>
                        <div class="punto-descripcion-large">
                            <?= htmlspecialchars($punto['extracto'] ?? $punto['descripcion'] ?? 'Sin descripción') ?>
                        </div>
                        
                        <!-- Resultados en tiempo real -->
                        <?php if (!empty($punto['resultados'])): ?>
                            <div class="resultados-grid-large">
                                <div class="resultado-item-large resultado-positivo-large">
                                    <div class="resultado-numero-large"><?= $punto['resultados']['positivo'] ?? 0 ?></div>
                                    <div class="resultado-label-large">Afirmativos</div>
                                </div>
                                <div class="resultado-item-large resultado-negativo-large">
                                    <div class="resultado-numero-large"><?= $punto['resultados']['negativo'] ?? 0 ?></div>
                                    <div class="resultado-label-large">Negativos</div>
                                </div>
                                <div class="resultado-item-large resultado-abstencion-large">
                                    <div class="resultado-numero-large"><?= $punto['resultados']['abstencion'] ?? 0 ?></div>
                                    <div class="resultado-label-large">Abstenciones</div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="punto-expediente-large">
                            <?= htmlspecialchars($punto['descripcion'] ?? 'Punto de sesión') ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Sección Hemiciclo -->
    <div class="hemiciclo-section-large">
        <h2 class="section-title-large">
            <i class="bi bi-people"></i>
            Presentes en Sesión
        </h2>
        
        <div class="hemiciclo-grid-large">
            <?php foreach ($miembros_presentes as $miembro): ?>
                <div class="miembro-hemiciclo-large miembro-presente-large">
                    <div class="miembro-avatar-large">
                        <?= strtoupper(substr($miembro['first_name'] ?? 'U', 0, 1) . substr($miembro['last_name'] ?? 'N', 0, 1)) ?>
                    </div>
                    <div class="miembro-nombre-large">
                        <?= htmlspecialchars(($miembro['first_name'] ?? '') . ' ' . ($miembro['last_name'] ?? 'Usuario')) ?>
                    </div>
                    <div class="miembro-estado-large presente-large">
                        <i class="bi bi-check-circle me-2"></i>
                        Presente
                    </div>
                </div>
            <?php endforeach; ?>
            
            <?php
            // Agregar miembros ausentes simulados para completar el hemiciclo
            $totalMiembros = max(12, count($miembros_presentes));
            for ($i = count($miembros_presentes); $i < $totalMiembros; $i++):
            ?>
                <div class="miembro-hemiciclo-large">
                    <div class="miembro-avatar-large">
                        M<?= $i + 1 ?>
                    </div>
                    <div class="miembro-nombre-large">
                        Miembro <?= $i + 1 ?>
                    </div>
                    <div class="miembro-estado-large ausente-large">
                        <i class="bi bi-x-circle me-2"></i>
                        Ausente
                    </div>
                </div>
            <?php endfor; ?>
        </div>
    </div>
</div>

<script>
    // Configuración específica para pantalla grande
    const PANTALLA_GRANDE_CONFIG = {
        actualizacionRapida: 5000,    // 5 segundos
        actualizacionCompleta: 30000, // 30 segundos
        duracionMocion: 8000          // 8 segundos para mociones
    };
    
    // Actualizar fecha y hora para pantalla grande
    function actualizarFechaHoraLarge() {
        const ahora = new Date();
        const opciones = {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        };
        
        const elemento = document.getElementById('fecha-hora-large');
        if (elemento) {
            elemento.textContent = ahora.toLocaleDateString('es-ES', opciones);
        }
    }
    
    // Mostrar mociones optimizadas para pantalla grande
    function mostrarMocionPantallaGrande(mocion) {
        const fechaFormateada = new Date(mocion.fecha_creacion).toLocaleTimeString('es-ES', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
        
        Swal.fire({
            title: '📢 MOCIÓN SOLICITADA',
            html: `
                <div class="text-start">
                    <div class="alert alert-warning mb-4" style="font-size: 1.6rem;">
                        <strong>${mocion.tipo_texto}</strong>
                    </div>
                    <div class="mb-4">
                        <strong style="font-size: 1.8rem;">Descripción:</strong><br>
                        <span class="text-muted" style="font-size: 1.6rem;">${mocion.texto}</span>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <span class="text-muted" style="font-size: 1.4rem;">
                                <i class="bi bi-person"></i> ${mocion.autor_nombre}
                            </span>
                        </div>
                        <div class="col-6 text-end">
                            <span class="text-muted" style="font-size: 1.4rem;">
                                <i class="bi bi-clock"></i> ${fechaFormateada}
                            </span>
                        </div>
                    </div>
                </div>
            `,
            icon: 'warning',
            confirmButtonText: 'Entendido',
            confirmButtonColor: '#ffc107',
            timer: PANTALLA_GRANDE_CONFIG.duracionMocion,
            timerProgressBar: true,
            position: 'top',
            width: '60%',
            customClass: {
                container: 'swal-mocion-large',
                popup: 'swal-mocion-large',
                title: 'swal-mocion-large'
            },
            showClass: {
                popup: 'animate__animated animate__slideInDown'
            },
            hideClass: {
                popup: 'animate__animated animate__slideOutUp'
            }
        });
    }
    
    // Override de la función mostrarMocion para pantalla grande
    if (typeof mostrarMocion !== 'undefined') {
        mostrarMocion = mostrarMocionPantallaGrande;
    }
    
    // Inicialización para pantalla grande
    document.addEventListener('DOMContentLoaded', function() {
        // Actualizar fecha y hora inmediatamente
        actualizarFechaHoraLarge();
        
        // Configurar intervalos optimizados
        setInterval(actualizarFechaHoraLarge, 1000);
        
        // Actualización más frecuente para pantalla grande
        if (typeof iniciarVerificacionMociones !== 'undefined') {
            iniciarVerificacionMociones();
        }
        
        // Auto-refresh completo cada 30 segundos
        setInterval(function() {
            if (document.visibilityState === 'visible') {
                location.reload();
            }
        }, PANTALLA_GRANDE_CONFIG.actualizacionCompleta);
        
        console.log('Vista optimizada para pantalla grande inicializada');
    });
</script>

<?php 
$content = ob_get_clean();
include VIEWS_PATH . 'layouts/main.php';
?>