<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sesión de Votación - Vista Pública</title>
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
        
        .container-sin-sesion {
            max-width: 600px;
            text-align: center;
        }
        
        .card-sin-sesion {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            border: none;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            padding: 50px 30px;
        }
        
        .icono-grande {
            font-size: 4rem;
            color: #6c757d;
            margin-bottom: 30px;
        }
        
        .titulo-sin-sesion {
            color: #495057;
            margin-bottom: 20px;
        }
        
        .descripcion-sin-sesion {
            color: #6c757d;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        
        .refresh-button {
            background: linear-gradient(135deg, #4154f1, #2c3cdd);
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            color: white;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .refresh-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(65, 84, 241, 0.3);
            color: white;
        }
        
        .info-adicional {
            background: rgba(65, 84, 241, 0.1);
            border-radius: 15px;
            padding: 20px;
            margin-top: 30px;
        }
        
        .timestamp {
            color: #6c757d;
            font-size: 0.9rem;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container-sin-sesion">
        <div class="card-sin-sesion">
            <div class="icono-grande">
                <i class="bi bi-pause-circle"></i>
            </div>
            
            <h1 class="h3 titulo-sin-sesion">
                No hay sesión activa
            </h1>
            
            <p class="descripcion-sin-sesion">
                En este momento no hay ninguna sesión de votación en curso. 
                Esta ventana pública mostrará automáticamente la información 
                cuando se inicie una nueva sesión.
            </p>
            
            <button class="btn refresh-button" onclick="location.reload()">
                <i class="bi bi-arrow-clockwise me-2"></i>
                Actualizar
            </button>
            
            <div class="info-adicional">
                <h5 class="h6 mb-3">
                    <i class="bi bi-info-circle me-2"></i>
                    ¿Qué verás cuando haya una sesión activa?
                </h5>
                <ul class="list-unstyled text-start">
                    <li class="mb-2">
                        <i class="bi bi-check text-success me-2"></i>
                        Información de la sesión en curso
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check text-success me-2"></i>
                        Lista de miembros presentes
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check text-success me-2"></i>
                        Punto que se está votando actualmente
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check text-success me-2"></i>
                        Estado de los puntos habilitados
                    </li>
                    <li class="mb-0">
                        <i class="bi bi-check text-success me-2"></i>
                        Actualización automática en tiempo real
                    </li>
                </ul>
            </div>
            
            <div class="timestamp">
                <i class="bi bi-clock me-1"></i>
                Consultado: <?= date('d/m/Y H:i:s') ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Auto-refresh cada 30 segundos para verificar si hay sesión activa
        setTimeout(() => {
            location.reload();
        }, 30000);
    </script>
</body>
</html>