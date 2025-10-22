<?php require_once 'config/config.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vista Pública - Sesión de Votación</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .container-acceso {
            max-width: 500px;
            text-align: center;
        }
        
        .card-acceso {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            border: none;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            padding: 40px 30px;
        }
        
        .logo-publico {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #4154f1, #2c3cdd);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            color: white;
            font-size: 2rem;
        }
        
        .titulo-acceso {
            color: #495057;
            margin-bottom: 20px;
        }
        
        .descripcion-acceso {
            color: #6c757d;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        
        .btn-publico {
            background: linear-gradient(135deg, #4154f1, #2c3cdd);
            border: none;
            border-radius: 25px;
            padding: 15px 40px;
            color: white;
            font-weight: 500;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-publico:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(65, 84, 241, 0.3);
            color: white;
            text-decoration: none;
        }
        
        .features-list {
            background: rgba(65, 84, 241, 0.1);
            border-radius: 15px;
            padding: 20px;
            margin-top: 30px;
        }
        
        .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            text-align: left;
        }
        
        .feature-item:last-child {
            margin-bottom: 0;
        }
        
        .feature-icon {
            color: #28a745;
            margin-right: 10px;
            font-size: 1.2rem;
        }
    </style>
</head>
<body>
    <div class="container-acceso">
        <div class="card-acceso">
            <div class="logo-publico">
                <i class="bi bi-broadcast"></i>
            </div>
            
            <h1 class="h3 titulo-acceso">
                Vista Pública de Votación
            </h1>
            
            <p class="descripcion-acceso">
                Accede a la información en tiempo real de las sesiones de votación 
                sin necesidad de iniciar sesión. Esta ventana es de consulta pública 
                y se actualiza automáticamente.
            </p>
            
            <a href="<?= BASE_URL ?>votacion/vista-publica" class="btn-publico">
                <i class="bi bi-eye me-2"></i>
                Ver Sesión Actual
            </a>
            
            <div class="features-list">
                <h5 class="h6 mb-3 text-center">
                    <i class="bi bi-info-circle me-2"></i>
                    Información disponible
                </h5>
                
                <div class="feature-item">
                    <i class="bi bi-calendar-event feature-icon"></i>
                    <span>Datos de la sesión en curso</span>
                </div>
                
                <div class="feature-item">
                    <i class="bi bi-people feature-icon"></i>
                    <span>Miembros presentes en la sesión</span>
                </div>
                
                <div class="feature-item">
                    <i class="bi bi-hand-thumbs-up feature-icon"></i>
                    <span>Punto que se está votando</span>
                </div>
                
                <div class="feature-item">
                    <i class="bi bi-list-check feature-icon"></i>
                    <span>Estado de puntos habilitados</span>
                </div>
                
                <div class="feature-item">
                    <i class="bi bi-arrow-clockwise feature-icon"></i>
                    <span>Actualización automática</span>
                </div>
            </div>
            
            <div class="mt-4">
                <small class="text-muted">
                    <i class="bi bi-shield-check me-1"></i>
                    Acceso público sin autenticación requerida
                </small>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>