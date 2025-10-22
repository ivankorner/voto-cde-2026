<?php 
$current_page = 'votacion';
ob_start(); 
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="bi bi-bar-chart"></i>
        Resultados de Votación
    </h1>
    <div>
        <a href="<?= BASE_URL ?>votacion/sesion/<?= $sesion['id'] ?>" class="btn btn-primary">
            <i class="bi bi-house-door"></i>
            Volver a Sala
        </a>
        <a href="<?= BASE_URL ?>votacion/historial" class="btn btn-secondary">
            <i class="bi bi-clock-history"></i>
            Historial
        </a>
    </div>
</div>

<!-- Información del ítem votado -->
<div class="card shadow mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
            <i class="bi bi-info-circle"></i>
            Información de la Votación
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6><i class="bi bi-building"></i> Sesión</h6>
                <p><?= htmlspecialchars($sesion['nombre_sesion']) ?></p>
                
                <h6><i class="bi bi-file-earmark-text"></i> Orden del Día</h6>
                <p>Acta N° <?= htmlspecialchars($sesion['numero_acta']) ?> - <?= ucfirst($sesion['tipo_sesion']) ?></p>
            </div>
            <div class="col-md-6">
                <h6><i class="bi bi-list-check"></i> Ítem Votado</h6>
                <p>
                    <?php if ($tipo_item === 'actas'): ?>
                        <span class="badge bg-info">GLOBAL</span>
                        Lectura y Consideración de Actas
                    <?php else: ?>
                        <span class="badge bg-primary">EXPEDIENTE</span>
                        Ítem del orden del día
                    <?php endif; ?>
                </p>
                
                <h6><i class="bi bi-people"></i> Presentes en Sesión</h6>
                <p><strong><?= $total_presentes ?></strong> miembros con derecho a voto</p>
            </div>
        </div>
    </div>
</div>

<!-- Resultados generales -->
<div class="row mb-4">
    <?php
    $totalVotos = array_sum(array_column($resultados, 'cantidad'));
    $porcentajeParticipacion = $total_presentes > 0 ? round(($totalVotos / $total_presentes) * 100, 1) : 0;
    
    $positivos = 0;
    $negativos = 0;
    $abstenciones = 0;
    
    foreach ($resultados as $resultado) {
        switch ($resultado['tipo_voto']) {
            case 'positivo':
                $positivos = $resultado['cantidad'];
                break;
            case 'negativo':
                $negativos = $resultado['cantidad'];
                break;
            case 'abstencion':
                $abstenciones = $resultado['cantidad'];
                break;
        }
    }
    
    // Determinar resultado final
    $resultadoFinal = 'pendiente';
    if ($totalVotos == $total_presentes) {
        if ($positivos > $negativos) {
            $resultadoFinal = 'aprobado';
        } elseif ($negativos > $positivos) {
            $resultadoFinal = 'rechazado';
        } else {
            $resultadoFinal = 'empate';
        }
    }
    ?>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Votos Positivos
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $positivos ?></div>
                        <div class="text-xs">
                            <?= $total_presentes > 0 ? round(($positivos / $total_presentes) * 100, 1) : 0 ?>% del total
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-hand-thumbs-up fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            Votos Negativos
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $negativos ?></div>
                        <div class="text-xs">
                            <?= $total_presentes > 0 ? round(($negativos / $total_presentes) * 100, 1) : 0 ?>% del total
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-hand-thumbs-down fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Abstenciones
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $abstenciones ?></div>
                        <div class="text-xs">
                            <?= $total_presentes > 0 ? round(($abstenciones / $total_presentes) * 100, 1) : 0 ?>% del total
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-dash-circle fa-2x text-gray-300"></i>
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
                            Resultado Final
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php
                            $badgeClass = 'secondary';
                            switch ($resultadoFinal) {
                                case 'aprobado':
                                    $badgeClass = 'success';
                                    break;
                                case 'rechazado':
                                    $badgeClass = 'danger';
                                    break;
                                case 'empate':
                                    $badgeClass = 'warning';
                                    break;
                            }
                            ?>
                            <span class="badge bg-<?= $badgeClass ?> fs-6">
                                <?= ucfirst($resultadoFinal) ?>
                            </span>
                        </div>
                        <div class="text-xs">
                            Participación: <?= $porcentajeParticipacion ?>%
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-trophy fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Gráfico de resultados -->
<div class="row">
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0">
                    <i class="bi bi-pie-chart"></i>
                    Distribución de Votos
                </h6>
            </div>
            <div class="card-body">
                <canvas id="graficoVotos" width="400" height="400"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0">
                    <i class="bi bi-bar-chart"></i>
                    Comparación de Votos
                </h6>
            </div>
            <div class="card-body">
                <div class="progress mb-3">
                    <div class="progress-bar bg-success" role="progressbar" 
                         style="width: <?= $total_presentes > 0 ? ($positivos / $total_presentes) * 100 : 0 ?>%">
                        Positivos (<?= $positivos ?>)
                    </div>
                </div>
                <div class="progress mb-3">
                    <div class="progress-bar bg-danger" role="progressbar" 
                         style="width: <?= $total_presentes > 0 ? ($negativos / $total_presentes) * 100 : 0 ?>%">
                        Negativos (<?= $negativos ?>)
                    </div>
                </div>
                <div class="progress mb-3">
                    <div class="progress-bar bg-warning" role="progressbar" 
                         style="width: <?= $total_presentes > 0 ? ($abstenciones / $total_presentes) * 100 : 0 ?>%">
                        Abstenciones (<?= $abstenciones ?>)
                    </div>
                </div>
                
                <?php if ($totalVotos < $total_presentes): ?>
                <div class="progress">
                    <div class="progress-bar bg-secondary" role="progressbar" 
                         style="width: <?= $total_presentes > 0 ? (($total_presentes - $totalVotos) / $total_presentes) * 100 : 0 ?>%">
                        Sin votar (<?= $total_presentes - $totalVotos ?>)
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Detalle de votos -->
<div class="card shadow">
    <div class="card-header bg-secondary text-white">
        <h6 class="mb-0">
            <i class="bi bi-list-ul"></i>
            Detalle de Votaciones
        </h6>
    </div>
    <div class="card-body">
        <?php if (empty($votos_detallados)): ?>
        <div class="text-center py-4">
            <i class="bi bi-inbox fa-3x text-gray-300 mb-3"></i>
            <h5 class="text-gray-600">No hay votos registrados</h5>
            <p class="text-gray-500">Aún no se han registrado votos para este ítem</p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Orden</th>
                        <th>Votante</th>
                        <th>Voto</th>
                        <th>Fecha y Hora</th>
                        <th>Observaciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($votos_detallados as $index => $voto): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-circle bg-primary text-white me-2" 
                                     style="width: 40px; height: 40px; line-height: 40px; border-radius: 50%; text-align: center;">
                                    <?= strtoupper(substr($voto['first_name'], 0, 1) . substr($voto['last_name'], 0, 1)) ?>
                                </div>
                                <div>
                                    <div class="font-weight-bold">
                                        <?= htmlspecialchars($voto['first_name'] . ' ' . $voto['last_name']) ?>
                                    </div>
                                    <small class="text-muted">@<?= htmlspecialchars($voto['username']) ?></small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php
                            $badgeClass = 'secondary';
                            $icon = 'bi-circle';
                            
                            switch ($voto['tipo_voto']) {
                                case 'positivo':
                                    $badgeClass = 'success';
                                    $icon = 'bi-hand-thumbs-up';
                                    break;
                                case 'negativo':
                                    $badgeClass = 'danger';
                                    $icon = 'bi-hand-thumbs-down';
                                    break;
                                case 'abstencion':
                                    $badgeClass = 'warning';
                                    $icon = 'bi-dash-circle';
                                    break;
                            }
                            ?>
                            <span class="badge bg-<?= $badgeClass ?>">
                                <i class="<?= $icon ?>"></i>
                                <?= ucfirst($voto['tipo_voto']) ?>
                            </span>
                        </td>
                        <td>
                            <div><?= date('d/m/Y', strtotime($voto['fecha_voto'])) ?></div>
                            <small class="text-muted"><?= date('H:i:s', strtotime($voto['fecha_voto'])) ?></small>
                        </td>
                        <td>
                            <?php if ($voto['observaciones']): ?>
                                <?= htmlspecialchars($voto['observaciones']) ?>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Chart.js para gráficos -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Gráfico de torta
const ctx = document.getElementById('graficoVotos').getContext('2d');
const graficoVotos = new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: ['Positivos', 'Negativos', 'Abstenciones'<?= $totalVotos < $total_presentes ? ', \'Sin votar\'' : '' ?>],
        datasets: [{
            data: [<?= $positivos ?>, <?= $negativos ?>, <?= $abstenciones ?><?= $totalVotos < $total_presentes ? ', ' . ($total_presentes - $totalVotos) : '' ?>],
            backgroundColor: [
                '#28a745',
                '#dc3545', 
                '#ffc107'<?= $totalVotos < $total_presentes ? ', \'#6c757d\'' : '' ?>
            ],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const total = <?= $total_presentes ?>;
                        const value = context.parsed;
                        const percentage = ((value / total) * 100).toFixed(1);
                        return context.label + ': ' + value + ' (' + percentage + '%)';
                    }
                }
            }
        }
    }
});
</script>

<style>
.avatar-circle {
    font-weight: bold;
    font-size: 0.9rem;
}

.progress {
    height: 25px;
}

.progress-bar {
    font-weight: bold;
    line-height: 25px;
}

#graficoVotos {
    max-height: 400px;
}
</style>

<?php 
$content = ob_get_clean();
include VIEWS_PATH . 'layouts/main.php';
?>