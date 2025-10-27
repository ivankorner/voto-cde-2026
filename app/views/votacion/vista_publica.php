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
    <!-- Estilos globales del sistema -->
    <link href="<?= BASE_URL ?>assets/css/style.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #5a67d8 100%);
            font-family: 'Inter', 'Segoe UI', -apple-system, BlinkMacSystemFont, sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
        }
        
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 80%, rgba(120, 119, 198, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(102, 126, 234, 0.2) 0%, transparent 50%);
            pointer-events: none;
            z-index: -1;
        }
        
        .header-publico {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px) saturate(180%);
            border-bottom: 3px solid rgba(65, 84, 241, 0.8);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .header-publico:hover {
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.2);
            transform: translateY(-1px);
        }
        
        .container-publico {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .card-publico {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 24px;
            box-shadow: 
                0 20px 40px rgba(0, 0, 0, 0.1),
                0 1px 3px rgba(0, 0, 0, 0.05),
                inset 0 1px 0 rgba(255, 255, 255, 0.8);
            margin-bottom: 24px;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            transform: translateY(0);
        }
        
        .card-publico:hover {
            transform: translateY(-4px);
            box-shadow: 
                0 32px 64px rgba(0, 0, 0, 0.15),
                0 1px 3px rgba(0, 0, 0, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.9);
        }
        
        .sesion-titulo {
            background: linear-gradient(135deg, #4154f1 0%, #2c3cdd 50%, #667eea 100%);
            color: white;
            padding: 32px;
            margin: -1px -1px 24px -1px;
            border-radius: 24px 24px 0 0;
            position: relative;
            overflow: hidden;
        }
        
        .sesion-titulo::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, transparent 50%);
            pointer-events: none;
        }
        
        .sesion-titulo h2 {
            font-weight: 700;
            font-size: 1.75rem;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .sesion-titulo p {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 0;
        }
        
        .estado-badge {
            font-size: 0.9rem;
            padding: 8px 16px;
            border-radius: 25px;
        }
        
        .votacion-activa {
            background: linear-gradient(135deg, #28a745 0%, #20c997 50%, #17a2b8 100%);
            color: white;
            border-radius: 20px;
            padding: 28px;
            animation: pulse-suave 3s infinite ease-in-out;
            position: relative;
            overflow: hidden;
            box-shadow: 
                0 16px 32px rgba(40, 167, 69, 0.3),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
        }
        
        .votacion-activa::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            animation: shine 3s infinite;
        }
        
        @keyframes shine {
            0% { left: -100%; }
            50% { left: 100%; }
            100% { left: 100%; }
        }
        
        @keyframes pulse-suave {
            0%, 100% { transform: scale(1); box-shadow: 0 16px 32px rgba(40, 167, 69, 0.3); }
            50% { transform: scale(1.02); box-shadow: 0 20px 40px rgba(40, 167, 69, 0.4); }
        }
        
        .miembro-presente {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 16px;
            padding: 16px 20px;
            margin: 8px;
            display: inline-block;
            border-left: 4px solid #28a745;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        
        .miembro-presente:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
            border-left-color: #20c997;
        }
        
        .miembro-presente::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 0;
            background: linear-gradient(to bottom, #28a745, #20c997);
            transition: height 0.3s ease;
        }
        
        .miembro-presente:hover::before {
            height: 100%;
        }
        
        .punto-habilitado {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            border-left: 4px solid #28a745;
            border-radius: 8px;
            padding: 15px;
            margin: 10px 0;
        }
            .punto-actual {
                background: linear-gradient(135deg, #b7e4c7, #95d5b2);
                border-left-color: #138f5b;
            }
            .punto-votado {
                background: linear-gradient(135deg, #e2e3e5, #f8f9fa);
                border-left-color: #6c757d;
                opacity: 0.85;
            }
            .badges-votos .badge { margin-right: 6px; }
        
        .timestamp {
            color: #6c757d;
            font-size: 1rem;
            font-weight: 500;
            text-align: center;
            margin-top: 32px;
            padding: 16px 24px;
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
        }
        
        .sin-datos {
            text-align: center;
            color: #6c757d;
            font-style: italic;
            padding: 30px;
        }
        
        .logo-institucion {
            max-height: 60px;
            margin-right: 20px;
        }
        
        .actualizacion-automatica {
            position: fixed;
            top: 24px;
            right: 24px;
            background: linear-gradient(135deg, rgba(40, 167, 69, 0.95) 0%, rgba(32, 201, 151, 0.95) 100%);
            backdrop-filter: blur(16px);
            color: white;
            padding: 12px 20px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            z-index: 1000;
            box-shadow: 
                0 8px 24px rgba(40, 167, 69, 0.3),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .actualizacion-automatica:hover {
            transform: translateY(-2px);
            box-shadow: 
                0 12px 32px rgba(40, 167, 69, 0.4),
                inset 0 1px 0 rgba(255, 255, 255, 0.3);
        }
        
        .estado-conexion {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 10px;
            animation: pulse-dot 2s infinite;
            box-shadow: 0 0 0 0 currentColor;
        }
        
        @keyframes pulse-dot {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.7);
            }
            70% {
                transform: scale(1);
                box-shadow: 0 0 0 8px rgba(255, 255, 255, 0);
            }
            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(255, 255, 255, 0);
            }
        }
        
        .conectado { 
            background-color: #00ff88; 
        }
        .desconectado { 
            background-color: #ff6b6b; 
            animation: pulse-error 1s infinite;
        }
        
        @keyframes pulse-error {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        /* Modo alto contraste para vista pública */
        body.high-contrast {
            background: #000 !important;
            color: #fff !important;
        }
        body.high-contrast .header-publico {
            background: #000 !important;
            border-bottom-color: #fff !important;
        }
        body.high-contrast .card-publico {
            background: #0f0f0f !important;
        }
        body.high-contrast .text-muted { color: #ddd !important; }
        body.high-contrast .punto-habilitado { background: #121212 !important; border-left-color: #9be89b !important; }
        body.high-contrast .punto-actual { background: #1a2a1f !important; border-left-color: #7ad97a !important; }
        body.high-contrast .punto-votado { background: #1c1c1c !important; border-left-color: #bbb !important; opacity: 1; }
        body.high-contrast .badge { filter: none; }

        /* Modo sala (para proyección) */
        body.modo-sala .header-publico { display: none !important; }
        body.modo-sala .container-publico { max-width: 100%; padding: 10px 20px; }
    body.modo-sala #puntos-habilitados-container { display: none !important; }
        body.modo-sala #estadisticas-container { display: none !important; }
    body.modo-sala .actualizacion-automatica { display: none !important; }
        body.modo-sala .votacion-activa { padding: 28px; }
        body.modo-sala .votacion-activa .fs-5 { font-size: 1.75rem !important; }
        body.modo-sala h2.h4, 
        body.modo-sala h3.h5, 
        body.modo-sala h4.h6 { font-size: 1.5rem; }
        body.modo-sala .timestamp { font-size: 1.2rem; }

        /* Resumen de conteos grandes */
        .resumen-conteos .conteo-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border-radius: 20px;
            padding: 20px;
            text-align: center;
            border: 1px solid rgba(0,0,0,0.06);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            box-shadow: 
                0 8px 24px rgba(0,0,0,0.08),
                inset 0 1px 0 rgba(255,255,255,0.8);
        }
        
        .resumen-conteos .conteo-card:hover {
            transform: translateY(-4px) scale(1.02);
            box-shadow: 
                0 16px 40px rgba(0,0,0,0.12),
                inset 0 1px 0 rgba(255,255,255,0.9);
        }
        
        .resumen-conteos .conteo-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, transparent, var(--color, #6c757d), transparent);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .resumen-conteos .conteo-card:hover::before {
            opacity: 1;
        }
        
        .resumen-conteos .conteo-card:nth-child(1) { --color: #28a745; }
        .resumen-conteos .conteo-card:nth-child(2) { --color: #dc3545; }
        .resumen-conteos .conteo-card:nth-child(3) { --color: #ffc107; }
        
        .resumen-conteos .conteo-valor {
            font-size: 2.25rem;
            font-weight: 800;
            line-height: 1;
            background: linear-gradient(135deg, var(--color, #6c757d), color-mix(in srgb, var(--color, #6c757d) 80%, black));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 8px;
            transition: all 0.3s ease;
        }
        
        .resumen-conteos .conteo-card:hover .conteo-valor {
            transform: scale(1.1);
        }
        
        .resumen-conteos .conteo-label {
            font-size: 0.95rem;
            color: #6c757d;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        body.modo-sala .resumen-conteos .conteo-valor { font-size: 2.2rem; }
        body.high-contrast .resumen-conteos .conteo-card { background: #111; border-color: #333; }
        body.high-contrast .resumen-conteos .conteo-label { color: #bbb; }

        /* Indicador de mayoría */
        .mayoria-indicador {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            padding: 12px 20px;
            border-radius: 50px;
            background: linear-gradient(135deg, rgba(40,167,69,0.15) 0%, rgba(32,201,151,0.15) 100%);
            color: #198754;
            border: 2px solid rgba(40,167,69,0.3);
            font-weight: 700;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            animation: glow-success 2s infinite ease-in-out;
            box-shadow: 
                0 4px 16px rgba(40,167,69,0.2),
                inset 0 1px 0 rgba(255,255,255,0.3);
            position: relative;
            overflow: hidden;
        }
        
        .mayoria-indicador::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            animation: shine-success 3s infinite;
        }
        
        @keyframes glow-success {
            0%, 100% { 
                box-shadow: 0 4px 16px rgba(40,167,69,0.2), inset 0 1px 0 rgba(255,255,255,0.3);
            }
            50% { 
                box-shadow: 0 8px 24px rgba(40,167,69,0.4), inset 0 1px 0 rgba(255,255,255,0.5);
            }
        }
        
        @keyframes shine-success {
            0% { left: -100%; }
            50% { left: 100%; }
            100% { left: 100%; }
        }
        body.high-contrast .mayoria-indicador { background: #0f2b16; color: #9be89b; border-color: #1c3b26; }
        
        /* Resultado final del expediente */
        .resultado-final-badge {
            position: relative;
            overflow: hidden;
            text-transform: uppercase;
            font-family: 'Inter', sans-serif;
            font-weight: 800 !important;
            text-shadow: 0 1px 2px rgba(0,0,0,0.1);
            border: 2px solid rgba(255,255,255,0.2);
        }
        
        .resultado-final-badge::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            animation: shine-resultado 3s infinite;
        }
        
        @keyframes pulse-resultado {
            0%, 100% { 
                transform: scale(1);
                box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            }
            50% { 
                transform: scale(1.05);
                box-shadow: 0 6px 20px rgba(0,0,0,0.3);
            }
        }
        
        @keyframes shine-resultado {
            0% { left: -100%; }
            50% { left: 100%; }
            100% { left: 100%; }
        }
        
        body.high-contrast .resultado-final-badge {
            border-color: #fff !important;
            text-shadow: none;
        }
        
        /* ===== SALA DE SESIONES - ESTILOS MEJORADOS ===== */
        .hemiciclo-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border: none;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .hemiciclo-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
        }

        .hemiciclo-header {
            position: relative;
        }

        .hemiciclo-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent 0%, #dee2e6 50%, transparent 100%);
        }

        .icon-wrapper {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #0d6efd, #6610f2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
        }

        .hemiciclo-stats .badge {
            font-size: 0.85rem;
            font-weight: 500;
            box-shadow: 0 2px 8px rgba(13, 110, 253, 0.2);
        }

        .hemiciclo-subtitle {
            margin-top: 8px;
        }

        .hemiciclo-wrapper {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 16px;
            padding: 2rem;
            position: relative;
            overflow: hidden;
        }

        .hemiciclo-wrapper::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at center bottom, rgba(13, 110, 253, 0.05) 0%, transparent 60%);
            pointer-events: none;
        }

        .hemiciclo-container {
            position: relative;
            width: 100%;
            height: 200px;
            margin: 0 auto 1.5rem;
            background: linear-gradient(180deg, transparent 0%, rgba(13, 110, 253, 0.03) 100%);
            border-radius: 12px;
            border: 2px dashed rgba(13, 110, 253, 0.2);
        }

        .hemiciclo {
            position: relative;
            width: 100%;
            height: 100%;
            border-radius: 50% 50% 0 0;
            overflow: hidden;
        }

        .miembro-hemiciclo {
            position: absolute;
            z-index: 10;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
        }

        .miembro-avatar {
            position: relative;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.9rem;
            border: 3px solid;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .miembro-hemiciclo.presente .miembro-avatar {
            background: linear-gradient(135deg, #28a745, #20c997);
            border-color: #fff;
            color: white;
        }

        .miembro-hemiciclo.ausente .miembro-avatar {
            background: linear-gradient(135deg, #6c757d, #adb5bd);
            border-color: #fff;
            color: white;
        }

        /* Estilos especiales para el presidente (posición #1) */
        .miembro-hemiciclo.presidente .miembro-avatar {
            width: 60px;
            height: 60px;
            font-size: 1rem;
            border-width: 4px;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
        }

        .miembro-hemiciclo.presidente.presente .miembro-avatar {
            background: linear-gradient(135deg, #dc3545, #ffc107);
            border-color: #fff;
            animation: pulse-presidente 3s infinite ease-in-out;
        }

        .miembro-hemiciclo.presidente.ausente .miembro-avatar {
            background: linear-gradient(135deg, #6c757d, #495057);
            border-color: #fff;
        }

        @keyframes pulse-presidente {
            0%, 100% { 
                transform: scale(1);
                box-shadow: 0 6px 16px rgba(220, 53, 69, 0.4), 0 0 0 0 rgba(220, 53, 69, 0.3);
            }
            50% { 
                transform: scale(1.05);
                box-shadow: 0 8px 20px rgba(220, 53, 69, 0.5), 0 0 0 8px rgba(220, 53, 69, 0.1);
            }
        }

        .miembro-hemiciclo:hover .miembro-avatar {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
        }

        .miembro-hemiciclo.presidente:hover .miembro-avatar {
            transform: scale(1.15);
            box-shadow: 0 8px 24px rgba(220, 53, 69, 0.4);
        }

        .estado-indicator {
            position: absolute;
            top: -2px;
            right: -2px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            border: 2px solid white;
        }

        .presente-indicator {
            background: #28a745;
            animation: pulse-indicator 2s infinite;
        }

        @keyframes pulse-indicator {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        .miembro-tooltip {
            position: absolute;
            bottom: 60px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, 0.9);
            color: white;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 0.8rem;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 1000;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        .miembro-tooltip::after {
            content: '';
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
            border: 6px solid transparent;
            border-top-color: rgba(0, 0, 0, 0.9);
        }

        .miembro-hemiciclo:hover .miembro-tooltip {
            opacity: 1;
            visibility: visible;
            transform: translateX(-50%) translateY(-5px);
        }

        .tooltip-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 4px;
        }

        .posicion-badge {
            background: rgba(255, 255, 255, 0.2);
            padding: 2px 6px;
            border-radius: 6px;
            font-size: 0.7rem;
            font-weight: 500;
        }

        .tooltip-role {
            margin-bottom: 4px;
            opacity: 0.9;
        }

        .tooltip-status {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 4px;
        }

        .hemiciclo-label {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            text-align: center;
            z-index: 5;
        }

        .label-content {
            background: linear-gradient(135deg, #fff, #f8f9fa);
            padding: 12px 20px;
            border-radius: 20px;
            font-weight: 600;
            color: #495057;
            border: 2px solid rgba(13, 110, 253, 0.2);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .label-text {
            font-size: 0.9rem;
        }

        .label-status {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: 0.75rem;
            color: #28a745;
            font-weight: 500;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }

        .status-dot.live {
            background: #28a745;
            animation: pulse-live 1.5s infinite;
        }

        @keyframes pulse-live {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.7; transform: scale(1.2); }
        }

        .hemiciclo-leyenda-wrapper {
            margin-top: 1rem;
        }

        .hemiciclo-leyenda {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            padding: 16px;
            border: 1px solid rgba(13, 110, 253, 0.1);
        }

        .leyenda-title {
            margin-bottom: 12px;
            text-align: center;
        }

        .leyenda-items {
            display: flex;
            justify-content: center;
            gap: 24px;
        }

        .leyenda-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .leyenda-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            border: 2px solid white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .leyenda-dot.presente {
            background: linear-gradient(135deg, #28a745, #20c997);
        }

        .leyenda-dot.ausente {
            background: linear-gradient(135deg, #6c757d, #adb5bd);
        }

        .count-badge {
            background: rgba(13, 110, 253, 0.1);
            color: #0d6efd;
            padding: 2px 6px;
            border-radius: 6px;
            font-size: 0.7rem;
            font-weight: 600;
            margin-left: 4px;
        }

        .hemiciclo-info {
            background: linear-gradient(135deg, rgba(13, 110, 253, 0.05), rgba(102, 16, 242, 0.05));
            border-radius: 12px;
            border: 1px solid rgba(13, 110, 253, 0.1);
        }

        .info-card {
            padding: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .info-content {
            display: flex;
            align-items: center;
            font-size: 0.9rem;
            color: #495057;
        }

        .info-stats .badge {
            font-size: 0.8rem;
            padding: 6px 12px;
            border-radius: 8px;
        }

        .quorum-indicator {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            padding: 16px;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .quorum-info {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .quorum-status .badge {
            font-size: 0.8rem;
            padding: 6px 12px;
            border-radius: 8px;
        }

        /* Responsivo mejorado */
        @media (max-width: 768px) {
            .hemiciclo-wrapper {
                padding: 1.5rem;
            }
            
            .hemiciclo-container {
                height: 160px;
            }
            
            .miembro-avatar {
                width: 40px;
                height: 40px;
                font-size: 0.8rem;
            }
            
            .leyenda-items {
                gap: 16px;
            }
            
            .info-card {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }
        }

        @media (max-width: 576px) {
            .hemiciclo-header {
                text-align: center;
            }
            
            .hemiciclo-stats {
                margin-top: 8px;
            }
            
            .leyenda-items {
                flex-direction: column;
                align-items: center;
                gap: 8px;
            }
        }

        /* Modo alto contraste para hemiciclo */
        body.high-contrast .hemiciclo-card {
            background: #000;
            border: 2px solid #fff;
        }

        body.high-contrast .hemiciclo-wrapper {
            background: #111;
        }

        body.high-contrast .hemiciclo-container {
            border-color: #fff;
            background: #000;
        }

        body.high-contrast .miembro-avatar {
            border-color: #fff !important;
        }

        body.high-contrast .hemiciclo-leyenda {
            background: #000;
            border-color: #fff;
        }

        body.high-contrast .label-content {
            background: #000;
            border-color: #fff;
            color: #fff;
        }

        body.high-contrast .miembro-hemiciclo.presente {
            background: #9be89b;
            color: #000;
        }
        
        body.high-contrast .miembro-hemiciclo.ausente {
            background: #666;
            color: #ddd;
        }
        
        body.high-contrast .hemiciclo-label {
            background: #000;
            color: #fff;
            border-color: #fff;
        }
        
        /* Controles flotantes - siempre visibles */
        .controles-flotantes {
            position: fixed;
            top: 50%;
            right: -60px; /* Inicialmente oculto fuera de la pantalla */
            transform: translateY(-50%);
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.8) 0%, rgba(0, 0, 0, 0.9) 100%);
            backdrop-filter: blur(16px) saturate(180%);
            border-radius: 16px 0 0 16px; /* Solo esquinas izquierdas redondeadas */
            padding: 12px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-right: none; /* Sin borde derecho cuando está oculto */
            box-shadow: 
                0 16px 32px rgba(0, 0, 0, 0.4),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
            z-index: 2000;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            flex-direction: column;
            opacity: 0.7;
        }
        
        /* Estado visible cuando se activa */
        .controles-flotantes.visible {
            right: 0;
            border-radius: 16px 0 0 16px;
            border-right: none;
            opacity: 1;
            box-shadow: 
                0 20px 40px rgba(0, 0, 0, 0.5),
                inset 0 1px 0 rgba(255, 255, 255, 0.15);
        }
        
        /* Pestaña indicadora siempre visible */
        .controles-flotantes::after {
            content: '⚙️';
            position: absolute;
            right: -25px;
            top: 50%;
            transform: translateY(-50%);
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.7) 0%, rgba(0, 0, 0, 0.8) 100%);
            color: rgba(255, 255, 255, 0.8);
            padding: 8px 6px;
            border-radius: 0 8px 8px 0;
            font-size: 12px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-left: none;
            backdrop-filter: blur(8px);
            transition: all 0.3s ease;
            cursor: pointer;
            z-index: -1;
        }
        
        .controles-flotantes:hover::after,
        .controles-flotantes.visible::after {
            background: linear-gradient(135deg, rgba(65, 84, 241, 0.8) 0%, rgba(102, 126, 234, 0.8) 100%);
            color: white;
        }
        
        /* Área de activación invisible en el borde derecho */
        .activation-zone {
            position: fixed;
            top: 0;
            right: 0;
            width: 20px;
            height: 100vh;
            z-index: 1999;
            background: transparent;
            cursor: pointer;
        }
        
        .controles-flotantes .d-flex {
            flex-direction: column;
            gap: 8px !important;
        }
        
        .controles-flotantes .btn {
            border-radius: 12px;
            border-color: rgba(255, 255, 255, 0.3);
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.8rem;
            min-width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            backdrop-filter: blur(8px);
        }
        
        .controles-flotantes .btn:hover {
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.5);
            color: white;
            transform: scale(1.05);
        }
        
        .controles-flotantes .btn:active {
            transform: scale(0.95);
        }
        
        .controles-flotantes .btn-group {
            border-radius: 12px;
            overflow: hidden;
        }
        
        .controles-flotantes .btn-group .btn {
            border-radius: 0;
            min-width: 40px;
            height: 40px;
            font-size: 0.75rem;
        }
        
        .controles-flotantes .btn-group .btn:first-child {
            border-top-left-radius: 12px;
            border-bottom-left-radius: 12px;
        }
        
        .controles-flotantes .btn-group .btn:last-child {
            border-top-right-radius: 12px;
            border-bottom-right-radius: 12px;
        }
        
        /* Animación suave al aparecer */
        @keyframes fadeInRight {
            from {
                opacity: 0;
                transform: translateY(-50%) translateX(20px);
            }
            to {
                opacity: 1;
                transform: translateY(-50%) translateX(0);
            }
        }
        
        .controles-flotantes {
            animation: fadeInRight 0.5s ease-out;
        }
        
        /* Responsive: En pantallas pequeñas, mover a esquina inferior */
        @media (max-width: 768px) {
            .controles-flotantes {
                top: auto;
                bottom: -50px; /* Oculto inicialmente hacia abajo */
                right: 20px;
                transform: none;
                flex-direction: row;
                border-radius: 16px 16px 0 0; /* Solo esquinas superiores redondeadas */
                border-bottom: none;
            }
            
            .controles-flotantes.visible {
                bottom: 0;
                border-radius: 16px 16px 0 0;
                border-bottom: none;
            }
            
            /* Pestaña en móviles - arriba del panel */
            .controles-flotantes::after {
                content: '⚙️';
                right: 50%;
                top: -25px;
                transform: translateX(50%);
                border-radius: 8px 8px 0 0;
                border-bottom: none;
                border-top: 1px solid rgba(255, 255, 255, 0.1);
                padding: 6px 12px;
            }
            
            .activation-zone {
                top: auto;
                bottom: 0;
                width: 100vw;
                height: 20px;
                right: 0;
            }
            
            .controles-flotantes .d-flex {
                flex-direction: row;
                gap: 6px !important;
            }
            
            .controles-flotantes .btn {
                min-width: 36px;
                height: 36px;
                font-size: 0.7rem;
            }
            
            .controles-flotantes .btn-group .btn {
                min-width: 32px;
                height: 32px;
            }
        }
        
        /* ===== Estilos mejorados para el título de votación ===== */
        .votacion-header-mejorado {
            background: linear-gradient(135deg, 
                rgba(13, 110, 253, 0.08) 0%, 
                rgba(102, 16, 242, 0.08) 50%, 
                rgba(214, 51, 132, 0.08) 100%);
            border: 2px solid rgba(13, 110, 253, 0.1);
            border-radius: 20px;
            padding: 25px;
            position: relative;
            overflow: hidden;
        }
        
        .votacion-header-mejorado::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, 
                transparent 30%, 
                rgba(255, 255, 255, 0.1) 50%, 
                transparent 70%);
            pointer-events: none;
        }
        
        .votacion-titulo-wrapper {
            display: flex;
            align-items: center;
            gap: 20px;
            position: relative;
            z-index: 2;
        }
        
        .votacion-icono-principal {
            background: linear-gradient(135deg, #0d6efd, #6610f2);
            color: white;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            box-shadow: 0 8px 20px rgba(13, 110, 253, 0.3);
            flex-shrink: 0;
            position: relative;
        }
        
        .votacion-icono-principal::after {
            content: '';
            position: absolute;
            inset: -3px;
            border-radius: 50%;
            background: linear-gradient(45deg, #0d6efd, #6610f2, #d63384);
            z-index: -1;
            opacity: 0.7;
            animation: pulse-border 2s ease-in-out infinite;
        }
        
        .votacion-titulo-content {
            flex: 1;
        }
        
        .votacion-titulo-principal {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2c3e50;
            margin: 0;
            background: linear-gradient(135deg, #0d6efd, #6610f2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1.3;
        }
        
        .votacion-subtitulo {
            margin-top: 5px;
            opacity: 0.8;
        }
        
        .votacion-estado-live {
            display: flex;
            align-items: center;
            flex-shrink: 0;
        }
        
        .live-indicator {
            background: linear-gradient(135deg, #dc3545, #fd7e14);
            color: white;
            padding: 8px 16px;
            border-radius: 25px;
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
            animation: pulse-live 2s ease-in-out infinite;
        }
        
        .live-dot {
            width: 8px;
            height: 8px;
            background: white;
            border-radius: 50%;
            animation: blink-dot 1s ease-in-out infinite;
        }
        
        @keyframes pulse-border {
            0%, 100% {
                transform: scale(1);
                opacity: 0.7;
            }
            50% {
                transform: scale(1.05);
                opacity: 0.9;
            }
        }
        
        @keyframes pulse-live {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
            }
            50% {
                transform: scale(1.02);
                box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4);
            }
        }
        
        @keyframes blink-dot {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.3;
            }
        }
        
        /* Responsivo */
        @media (max-width: 768px) {
            .votacion-titulo-wrapper {
                gap: 15px;
            }
            
            .votacion-icono-principal {
                width: 50px;
                height: 50px;
                font-size: 20px;
            }
            
            .votacion-titulo-principal {
                font-size: 1.3rem;
            }
            
            .live-indicator {
                padding: 6px 12px;
                font-size: 0.75rem;
            }
        }
        
        @media (max-width: 480px) {
            .votacion-titulo-wrapper {
                flex-direction: column;
                text-align: center;
                gap: 15px;
            }
            
            .votacion-estado-live {
                justify-content: center;
            }
        }
        
        /* Modo alto contraste */
        body.high-contrast .votacion-header-mejorado {
            background: #ffffff;
            border: 3px solid #000000;
        }
        
        body.high-contrast .votacion-icono-principal {
            background: #000000;
            color: #ffffff;
        }
        
        body.high-contrast .votacion-titulo-principal {
            color: #000000;
            -webkit-text-fill-color: #000000;
        }
        
        body.high-contrast .live-indicator {
            background: #000000;
            color: #ffffff;
        }
        
        /* ===== Estilos mejorados para el título de puntos habilitados ===== */
        .puntos-header-mejorado {
            background: linear-gradient(135deg, 
                rgba(25, 135, 84, 0.08) 0%, 
                rgba(13, 110, 253, 0.08) 50%, 
                rgba(102, 16, 242, 0.08) 100%);
            border: 2px solid rgba(25, 135, 84, 0.1);
            border-radius: 18px;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }
        
        .puntos-header-mejorado::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, 
                transparent 30%, 
                rgba(255, 255, 255, 0.08) 50%, 
                transparent 70%);
            pointer-events: none;
        }
        
        .puntos-titulo-wrapper {
            display: flex;
            align-items: center;
            gap: 18px;
            position: relative;
            z-index: 2;
        }
        
        .puntos-icono-principal {
            background: linear-gradient(135deg, #198754, #20c997);
            color: white;
            width: 55px;
            height: 55px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            box-shadow: 0 6px 18px rgba(25, 135, 84, 0.3);
            flex-shrink: 0;
            position: relative;
        }
        
        .puntos-icono-principal::after {
            content: '';
            position: absolute;
            inset: -2px;
            border-radius: 50%;
            background: linear-gradient(45deg, #198754, #20c997, #0dcaf0);
            z-index: -1;
            opacity: 0.6;
            animation: pulse-border-green 2.5s ease-in-out infinite;
        }
        
        .puntos-titulo-content {
            flex: 1;
        }
        
        .puntos-titulo-principal {
            font-size: 1.4rem;
            font-weight: 600;
            color: #2c3e50;
            margin: 0;
            background: linear-gradient(135deg, #198754, #20c997);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1.3;
        }
        
        .puntos-subtitulo {
            margin-top: 4px;
            opacity: 0.8;
        }
        
        .puntos-contador {
            display: flex;
            align-items: center;
            flex-shrink: 0;
        }
        
        .contador-badge {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border: 2px solid rgba(25, 135, 84, 0.2);
            padding: 12px 16px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            min-width: 70px;
        }
        
        .contador-numero {
            display: block;
            font-size: 1.6rem;
            font-weight: 700;
            color: #198754;
            line-height: 1;
        }
        
        .contador-badge small {
            font-size: 0.8rem;
            color: #6c757d;
            font-weight: 500;
        }
        
        @keyframes pulse-border-green {
            0%, 100% {
                transform: scale(1);
                opacity: 0.6;
            }
            50% {
                transform: scale(1.04);
                opacity: 0.8;
            }
        }
        
        /* Responsivo */
        @media (max-width: 768px) {
            .puntos-titulo-wrapper {
                gap: 14px;
            }
            
            .puntos-icono-principal {
                width: 48px;
                height: 48px;
                font-size: 18px;
            }
            
            .puntos-titulo-principal {
                font-size: 1.2rem;
            }
            
            .contador-badge {
                padding: 10px 12px;
                min-width: 60px;
            }
            
            .contador-numero {
                font-size: 1.4rem;
            }
        }
        
        @media (max-width: 480px) {
            .puntos-titulo-wrapper {
                flex-direction: column;
                text-align: center;
                gap: 12px;
            }
            
            .puntos-contador {
                justify-content: center;
            }
        }
        
        /* Modo alto contraste */
        body.high-contrast .puntos-header-mejorado {
            background: #ffffff;
            border: 3px solid #000000;
        }
        
        body.high-contrast .puntos-icono-principal {
            background: #000000;
            color: #ffffff;
        }
        
        body.high-contrast .puntos-titulo-principal {
            color: #000000;
            -webkit-text-fill-color: #000000;
        }
        
        body.high-contrast .contador-badge {
            background: #ffffff;
            border: 2px solid #000000;
        }
        
        body.high-contrast .contador-numero {
            color: #000000;
        }
        
        /* ===== Estilos mejorados para el título de estadísticas ===== */
        .estadisticas-header-mejorado {
            background: linear-gradient(135deg, 
                rgba(255, 193, 7, 0.08) 0%, 
                rgba(253, 126, 20, 0.08) 50%, 
                rgba(220, 53, 69, 0.08) 100%);
            border: 2px solid rgba(255, 193, 7, 0.1);
            border-radius: 16px;
            padding: 18px;
            position: relative;
            overflow: hidden;
        }
        
        .estadisticas-header-mejorado::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, 
                transparent 30%, 
                rgba(255, 255, 255, 0.06) 50%, 
                transparent 70%);
            pointer-events: none;
        }
        
        .estadisticas-titulo-wrapper {
            display: flex;
            align-items: center;
            gap: 16px;
            position: relative;
            z-index: 2;
        }
        
        .estadisticas-icono-principal {
            background: linear-gradient(135deg, #ffc107, #fd7e14);
            color: white;
            width: 52px;
            height: 52px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            box-shadow: 0 5px 16px rgba(255, 193, 7, 0.3);
            flex-shrink: 0;
            position: relative;
        }
        
        .estadisticas-icono-principal::after {
            content: '';
            position: absolute;
            inset: -2px;
            border-radius: 50%;
            background: linear-gradient(45deg, #ffc107, #fd7e14, #dc3545);
            z-index: -1;
            opacity: 0.5;
            animation: pulse-border-warning 3s ease-in-out infinite;
        }
        
        .estadisticas-titulo-content {
            flex: 1;
        }
        
        .estadisticas-titulo-principal {
            font-size: 1.35rem;
            font-weight: 600;
            color: #2c3e50;
            margin: 0;
            background: linear-gradient(135deg, #ffc107, #fd7e14);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1.3;
        }
        
        .estadisticas-subtitulo {
            margin-top: 3px;
            opacity: 0.8;
        }
        
        .estadisticas-indicador {
            display: flex;
            align-items: center;
            flex-shrink: 0;
        }
        
        .indicador-estado {
            background: linear-gradient(135deg, #e9ecef, #f8f9fa);
            border: 2px solid rgba(255, 193, 7, 0.2);
            padding: 8px 12px;
            border-radius: 12px;
            text-align: center;
            display: flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.08);
        }
        
        .estado-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #ffc107;
        }
        
        .estado-dot.pulse {
            animation: pulse-dot-warning 2s ease-in-out infinite;
        }
        
        .indicador-estado small {
            font-size: 0.75rem;
            color: #495057;
            font-weight: 600;
            letter-spacing: 0.3px;
        }
        
        @keyframes pulse-border-warning {
            0%, 100% {
                transform: scale(1);
                opacity: 0.5;
            }
            50% {
                transform: scale(1.03);
                opacity: 0.7;
            }
        }
        
        @keyframes pulse-dot-warning {
            0%, 100% {
                opacity: 1;
                transform: scale(1);
            }
            50% {
                opacity: 0.4;
                transform: scale(1.2);
            }
        }
        
        /* Responsivo */
        @media (max-width: 768px) {
            .estadisticas-titulo-wrapper {
                gap: 12px;
            }
            
            .estadisticas-icono-principal {
                width: 46px;
                height: 46px;
                font-size: 18px;
            }
            
            .estadisticas-titulo-principal {
                font-size: 1.2rem;
            }
            
            .indicador-estado {
                padding: 6px 10px;
            }
        }
        
        @media (max-width: 480px) {
            .estadisticas-titulo-wrapper {
                flex-direction: column;
                text-align: center;
                gap: 10px;
            }
            
            .estadisticas-indicador {
                justify-content: center;
            }
        }
        
        /* Modo alto contraste */
        body.high-contrast .estadisticas-header-mejorado {
            background: #ffffff;
            border: 3px solid #000000;
        }
        
        body.high-contrast .estadisticas-icono-principal {
            background: #000000;
            color: #ffffff;
        }
        
        body.high-contrast .estadisticas-titulo-principal {
            color: #000000;
            -webkit-text-fill-color: #000000;
        }
        
        body.high-contrast .indicador-estado {
            background: #ffffff;
            border: 2px solid #000000;
        }
        
        body.high-contrast .estado-dot {
            background: #000000;
        }
        
        body.high-contrast .indicador-estado small {
            color: #000000;
        }
    </style>
</head>
<body>
    <!-- Zona de activación invisible -->
    <div class="activation-zone" id="activation-zone"></div>
    
    <!-- Barra de controles flotante - siempre visible -->
    <div class="controles-flotantes" id="controles-flotantes">
        <div class="d-flex align-items-center gap-2">
            <button id="btn-contraste-flotante" class="btn btn-sm btn-outline-light" type="button" title="Alternar alto contraste" aria-label="Alternar alto contraste">
                <i class="bi bi-circle-half"></i>
            </button>
            <div class="btn-group" role="group" aria-label="Tamaño de texto">
                <button id="font-dec-flotante" class="btn btn-sm btn-outline-light" type="button" title="Disminuir tamaño de texto" aria-label="Disminuir tamaño de texto">A-</button>
                <button id="font-inc-flotante" class="btn btn-sm btn-outline-light" type="button" title="Aumentar tamaño de texto" aria-label="Aumentar tamaño de texto">A+</button>
            </div>
            <button id="btn-sala-flotante" class="btn btn-sm btn-outline-light" type="button" title="Alternar modo sala" aria-label="Alternar modo sala">
                <i class="bi bi-easel"></i>
            </button>
            <button id="btn-fullscreen-flotante" class="btn btn-sm btn-outline-light" type="button" title="Pantalla completa" aria-label="Pantalla completa">
                <i class="bi bi-arrows-fullscreen"></i>
            </button>
        </div>
    </div>

    <!-- Indicador de actualización automática -->
   

    <!-- Header -->
    <div class="header-publico">
        <div class="container-publico">
            <div class="row align-items-center py-3">
                <div class="col-md-8">
                    <h1 class="h3 mb-0">
                        <i class="bi bi-broadcast text-primary me-2"></i>
                        Sesión de Votación en Vivo
                    </h1>
                    <p class="text-muted mb-0">Consulta pública - Información en tiempo real</p>
                </div>
                <div class="col-md-4 text-end">
                    
                </div>
            </div>
        </div>
    </div>

    <div class="container-publico">
        <!-- Información de la Sesión -->
        <div class="card-publico">
            <div class="sesion-titulo">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h2 class="h4 mb-1">
                            <i class="bi bi-calendar-event me-2"></i>
                            <?= htmlspecialchars($sesion['nombre_sesion']) ?>
                        </h2>
                        <p class="mb-0 opacity-75">
                            Acta N° <?= htmlspecialchars($sesion['numero_acta']) ?> - 
                            <?= date('d/m/Y', strtotime($sesion['fecha_sesion'])) ?>
                        </p>
                    </div>
                    <div class="col-md-4 text-end">
                        <?php
                        $estadoColor = '';
                        $estadoTexto = '';
                        switch($sesion['estado']) {
                            case 'activa':
                                $estadoColor = 'success';
                                $estadoTexto = 'En Sesión';
                                break;
                            case 'pausada':
                                $estadoColor = 'warning';
                                $estadoTexto = 'Pausada';
                                break;
                            case 'planificada':
                                $estadoColor = 'info';
                                $estadoTexto = 'Planificada';
                                break;
                            default:
                                $estadoColor = 'secondary';
                                $estadoTexto = 'Finalizada';
                        }
                        ?>
                        <span class="badge estado-badge bg-<?= $estadoColor ?> fs-6">
                            <i class="bi bi-circle-fill me-1"></i>
                            <?= $estadoTexto ?>
                        </span>
                    </div>
                </div>
            </div>
            
            <?php if(!empty($sesion['descripcion'])): ?>
            <div class="card-body pt-0">
                <p class="text-muted mb-0">
                    <i class="bi bi-info-circle me-1"></i>
                    <?= htmlspecialchars($sesion['descripcion']) ?>
                </p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Sala de Sesiones - Sección Prominente -->
        <div class="row justify-content-center mb-4">
            <div class="col-lg-8">
                <div class="card-publico hemiciclo-card">
                    <div class="card-body px-4 py-4" id="miembros-presentes-container">
                        <!-- Header mejorado con más información -->
                        <div class="hemiciclo-header mb-4">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h3 class="h4 mb-0 section-title d-flex align-items-center">
                                    <div class="icon-wrapper me-3">
                                        <i class="bi bi-people-fill"></i>
                                    </div>
                                    <span>Sala de Sesiones</span>
                                </h3>
                                
                            </div>
                            <div class="hemiciclo-subtitle">
                                <small class="text-muted d-flex align-items-center">
                                    <i class="bi bi-broadcast me-1"></i>
                                    Visualización en tiempo real de Concejales presentes
                                </small>
                            </div>
                        </div>
                        
                        <!-- Contenedor principal del hemiciclo -->
                        <div class="hemiciclo-wrapper">
                            <div class="hemiciclo-container">
                                <div class="hemiciclo" id="hemiciclo">
                                    <?php 
                                    // Posiciones predefinidas para 7 miembros: #1 preside arriba, los otros 6 en semicírculo uniforme
                                    $posiciones = [
                                        ['left' => '50%', 'top' => '10px', 'transform' => 'translateX(-50%)'], // #1 - Presidente (arriba)
                                        ['left' => '10%', 'bottom' => '30px', 'transform' => 'translateX(-50%)'], // #2 - Extremo izquierdo
                                        ['left' => '26%', 'bottom' => '15px', 'transform' => 'translateX(-50%)'], // #3 - Izquierda
                                        ['left' => '42%', 'bottom' => '8px', 'transform' => 'translateX(-50%)'], // #4 - Centro izquierda
                                        ['left' => '58%', 'bottom' => '8px', 'transform' => 'translateX(-50%)'], // #5 - Centro derecha
                                        ['left' => '74%', 'bottom' => '15px', 'transform' => 'translateX(-50%)'], // #6 - Derecha
                                        ['left' => '90%', 'bottom' => '30px', 'transform' => 'translateX(-50%)'], // #7 - Extremo derecho
                                    ];
                                    
                                    $maxMiembros = 7;
                                    
                                    // Reorganizar miembros: Presidente/Vice en posición #1, solo Concejales en el resto
                                    $miembrosOrganizados = [];
                                    $presidente = null;
                                    $vicePresidente = null;
                                    $concejales = [];
                                    
                                    // Separar miembros por puesto
                                    if (!empty($miembros_presentes)) {
                                        foreach ($miembros_presentes as $miembro) {
                                            if (isset($miembro['puesto']) && !empty(trim($miembro['puesto']))) {
                                                $puestoLimpio = trim($miembro['puesto']);
                                                if ($puestoLimpio === 'Presidente') {
                                                    $presidente = $miembro;
                                                } elseif ($puestoLimpio === 'Vice Presidente') {
                                                    $vicePresidente = $miembro;
                                                } elseif ($puestoLimpio === 'Concejal') {
                                                    $concejales[] = $miembro;
                                                }
                                                // Los demás puestos (Secretario, Pro Secretario, etc.) NO se muestran
                                            }
                                        }
                                    }
                                    
                                    // Asignar posición #1 (presidencial) - SOLO Presidente o Vice Presidente
                                    if ($presidente) {
                                        $miembrosOrganizados[0] = $presidente; // Presidente en posición #1
                                    } elseif ($vicePresidente) {
                                        $miembrosOrganizados[0] = $vicePresidente; // Vice Presidente en posición #1 si no hay Presidente
                                    }
                                    
                                    // Llenar las demás posiciones SOLO con Concejales
                                    $posicionActual = 1; // Empezar desde posición #2
                                    
                                    foreach ($concejales as $concejal) {
                                        if ($posicionActual < $maxMiembros) {
                                            $miembrosOrganizados[$posicionActual] = $concejal;
                                            $posicionActual++;
                                        }
                                    }
                                    
                                    for ($i = 0; $i < $maxMiembros; $i++):
                                        $miembro = isset($miembrosOrganizados[$i]) ? $miembrosOrganizados[$i] : null;
                                        $posicion = $posiciones[$i];
                                        $claseEstado = $miembro ? 'presente' : 'ausente';
                                        $iniciales = $miembro ? 
                                            strtoupper(substr($miembro['first_name'], 0, 1) . substr($miembro['last_name'], 0, 1)) : 
                                            '?';
                                        $nombreCompleto = $miembro ? 
                                            htmlspecialchars($miembro['first_name'] . ' ' . $miembro['last_name']) : 
                                            'Vacante';
                                        $rol = $miembro ? htmlspecialchars($miembro['role_name']) : '';
                                        $puesto = $miembro && isset($miembro['puesto']) ? htmlspecialchars($miembro['puesto']) : '';
                                        $posicionNumero = $i + 1;
                                        $estilosPosicion = "left: {$posicion['left']}; transform: {$posicion['transform']};";
                                        if (isset($posicion['top'])) {
                                            $estilosPosicion .= " top: {$posicion['top']};";
                                        } else {
                                            $estilosPosicion .= " bottom: {$posicion['bottom']};";
                                        }
                                    ?>
                                    <div class="miembro-hemiciclo <?= $claseEstado ?> <?= $posicionNumero === 1 ? 'Presidente' : '' ?>" 
                                         style="<?= $estilosPosicion ?>"
                                         data-miembro-id="<?= $miembro ? $miembro['id'] : 'vacio-' . $i ?>"
                                         data-posicion="<?= $posicionNumero ?>">
                                        <div class="miembro-avatar">
                                            <span class="iniciales"><?= $iniciales ?></span>
                                            <?php if ($miembro): ?>
                                            <div class="estado-indicator presente-indicator"></div>
                                            <?php endif; ?>
                                        </div>
                                        <!-- Tooltip con información del miembro -->
                                        <div class="miembro-tooltip">
                                            <div class="tooltip-header">
                                                <strong><?= $nombreCompleto ?></strong>
                                                
                                            </div>
                                            <?php if ($puesto): ?>
                                            <div class="tooltip-puesto">
                                                <small><i class="bi bi-award me-1"></i><?= $puesto ?></small>
                                            </div>
                                            <?php endif; ?>
                                            <?php if ($rol): ?>
                                            <div class="tooltip-role">
                                                <small><i class="bi bi-person-badge me-1"></i><?= $puesto ? $puesto : $rol ?></small>
                                            </div>
                                            <?php endif; ?>
                                            <div class="tooltip-status">
                                                <small class="<?= $miembro ? 'text-success' : 'text-muted' ?>">
                                                    <i class="bi <?= $miembro ? 'bi-circle-fill' : 'bi-circle' ?> me-1"></i>
                                                    <?= $miembro ? 'Presente' : 'Ausente' ?>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endfor; ?>
                                    
                                    <!-- Etiqueta central mejorada -->
                                    <div class="hemiciclo-label">
                                        <div class="label-content">
                                            <span class="label-text">Sala de Sesiones</span>
                                        </div>
                                        <div class="label-status">
                                            <div class="status-dot live"></div>
                                            <small>En vivo</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Leyenda mejorada -->
                            <div class="hemiciclo-leyenda-wrapper">
                                <div class="hemiciclo-leyenda">
                                    <div class="leyenda-title">
                                        <small class="text-muted fw-medium">Estado de miembros:</small>
                                    </div>
                                    <div class="leyenda-items">
                                        <div class="leyenda-item">
                                            <div class="leyenda-dot presente"></div>
                                            <span>Presente</span>
                                            <small class="count-badge"><?= count(array_filter($miembrosOrganizados)) ?></small>
                                        </div>
                                        <div class="leyenda-item">
                                            <div class="leyenda-dot ausente"></div>
                                            <span>Ausente</span>
                                            <small class="count-badge"><?= max(0, 7 - count(array_filter($miembrosOrganizados))) ?></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Información adicional mejorada -->
                        <?php if (count($miembros_presentes) > $maxMiembros): ?>
                        <div class="hemiciclo-info mt-4">
                            <div class="info-card">
                                <div class="info-content">
                                    <i class="bi bi-info-circle-fill text-primary me-2"></i>
                                    <span>Se muestran los primeros <strong><?= $maxMiembros ?></strong> miembros del pleno.</span>
                                </div>
                                <div class="info-stats">
                                    <span class="badge bg-light text-dark">
                                        Total: <strong><?= count($miembros_presentes) ?></strong> presentes
                                    </span>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Indicador de quórum -->
                        <?php 
                        $totalMiembros = count($miembros_presentes);
                        $quorumMinimo = 4; // Asumiendo quórum mínimo de 4 para un concejo de 7
                        $tieneQuorum = $totalMiembros >= $quorumMinimo;
                        ?>
                        <div class="quorum-indicator mt-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="quorum-info">
                                    <small class="text-muted">Quórum:</small>
                                    <span class="fw-medium <?= $tieneQuorum ? 'text-success' : 'text-warning' ?>">
                                        <?= $totalMiembros ?>/<?= $quorumMinimo ?> mínimo
                                    </span>
                                </div>
                                <div class="quorum-status">
                                    <?php if ($tieneQuorum): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">
                                        <i class="bi bi-check-circle-fill me-1"></i>
                                        Quórum alcanzado
                                    </span>
                                    <?php else: ?>
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                        Sin quórum
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Columna izquierda: Votación Actual y Puntos -->
            <div class="col-lg-8">
                <!-- Votación Actual -->
                <div class="card-publico">
                    <div class="card-body" id="votacion-actual-container">
                        <div class="votacion-header-mejorado mb-4">
                            <div class="votacion-titulo-wrapper">
                                <div class="votacion-icono-principal">
                                    <i class="bi bi-hand-index-thumb"></i>
                                </div>
                                <div class="votacion-titulo-content">
                                    <h3 class="votacion-titulo-principal">
                                        ¿Qué se está votando ahora?
                                    </h3>
                                    <div class="votacion-subtitulo">
                                        <small class="text-muted">
                                            <i class="bi bi-broadcast me-1"></i>
                                            Estado en tiempo real de la votación actual
                                        </small>
                                    </div>
                                </div>
                               
                            </div>
                        </div>
                        
                        <?php if($votacion_actual): ?>
                        <div class="votacion-activa">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h4 class="h6 mb-1">
                                        <i class="bi bi-play-circle me-2"></i>
                                        Punto <?= htmlspecialchars($votacion_actual['numero'] ?? ($votacion_actual['punto']['numero'] ?? '')) ?>
                                    </h4>
                                    <p class="mb-0">
                                        <?= htmlspecialchars($votacion_actual['descripcion'] ?? ($votacion_actual['punto']['descripcion'] ?? '')) ?>
                                    </p>
                                </div>
                                <div class="col-md-4 text-end">
                                    <div class="fs-5">
                                        <i class="bi bi-vote-fill me-2"></i>
                                        <strong>VOTANDO</strong>
                                    </div>
                                    <?php if(!empty($estado_votacion['created_at'])): ?>
                                    <small>Desde: <?= date('H:i', strtotime($estado_votacion['created_at'])) ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if (!empty($votacion_actual['resultados'])): ?>
                            <div class="resumen-conteos mt-3">
                                <div class="row g-3">
                                    <div class="col-4">
                                        <div class="conteo-card">
                                            <div class="conteo-valor text-success"><?= (int)($votacion_actual['resultados']['positivo'] ?? 0) ?></div>
                                            <div class="conteo-label">Aprobaciones</div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="conteo-card">
                                            <div class="conteo-valor text-danger"><?= (int)($votacion_actual['resultados']['negativo'] ?? 0) ?></div>
                                            <div class="conteo-label">Rechazos</div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="conteo-card">
                                            <div class="conteo-valor text-warning"><?= (int)($votacion_actual['resultados']['abstencion'] ?? 0) ?></div>
                                            <div class="conteo-label">Abstenciones</div>
                                        </div>
                                    </div>
                                </div>
                                <?php 
                                $totalPresentesInit = isset($miembros_presentes) ? count($miembros_presentes) : 0; 
                                $umbralInit = $totalPresentesInit > 0 ? floor($totalPresentesInit/2) + 1 : 0; 
                                $aprobInit = (int)($votacion_actual['resultados']['positivo'] ?? 0);
                                ?>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <small class="text-muted">Presentes: <?= $totalPresentesInit ?> · Mayoría: <?= $umbralInit ?></small>
                                    <div class="d-flex align-items-center gap-2">
                                        <?php if ($umbralInit > 0 && $aprobInit >= $umbralInit): ?>
                                            <span class="mayoria-indicador"><i class="bi bi-check2-circle"></i> Mayoría alcanzada</span>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($votacion_actual['resultado_final'])): ?>
                                            <?php 
                                            $resultado = strtolower($votacion_actual['resultado_final']);
                                            $badgeClass = '';
                                            $icono = '';
                                            $texto = '';
                                            
                                            switch($resultado) {
                                                case 'positivo':
                                                case 'aprobado':
                                                    $badgeClass = 'bg-success';
                                                    $icono = 'bi-check-circle-fill';
                                                    $texto = 'APROBADO';
                                                    break;
                                                case 'negativo':
                                                case 'rechazado':
                                                    $badgeClass = 'bg-danger';
                                                    $icono = 'bi-x-circle-fill';
                                                    $texto = 'RECHAZADO';
                                                    break;
                                                case 'abstencion':
                                                case 'empate':
                                                    $badgeClass = 'bg-warning text-dark';
                                                    $icono = 'bi-dash-circle-fill';
                                                    $texto = 'ABSTENCIÓN';
                                                    break;
                                                default:
                                                    $badgeClass = 'bg-secondary';
                                                    $icono = 'bi-info-circle-fill';
                                                    $texto = strtoupper($votacion_actual['resultado_final']);
                                            }
                                            ?>
                                            <span class="resultado-final-badge badge <?= $badgeClass ?> fs-6 px-3 py-2" style="
                                                border-radius: 20px;
                                                font-weight: 700;
                                                letter-spacing: 0.5px;
                                                box-shadow: 0 4px 12px rgba(0,0,0,0.2);
                                                animation: pulse-resultado 2s infinite ease-in-out;
                                            ">
                                                <i class="bi <?= $icono ?> me-2"></i>
                                                <?= $texto ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php else: ?>
                        <div class="sin-datos">
                            <i class="bi bi-pause-circle fs-1 text-muted mb-3"></i>
                            <p class="mb-0">No hay votación activa en este momento</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Puntos del Orden del Día Habilitados -->
                <div class="card-publico">
                    <div class="card-body" id="puntos-habilitados-container">
                        <div class="puntos-header-mejorado mb-4">
                            <div class="puntos-titulo-wrapper">
                                <div class="puntos-icono-principal">
                                    <i class="bi bi-list-check"></i>
                                </div>
                                <div class="puntos-titulo-content">
                                    <h3 class="puntos-titulo-principal">
                                        Puntos Habilitados para Votación
                                    </h3>
                                    <div class="puntos-subtitulo">
                                        <small class="text-muted">
                                            <i class="bi bi-clock-history me-1"></i>
                                            Agenda del orden del día habilitada para consulta
                                        </small>
                                    </div>
                                </div>
                                <div class="puntos-contador">
                                    <div class="contador-badge">
                                        <span class="contador-numero" id="contador-puntos-badge">
                                            <?= count($puntos_habilitados ?? []) ?>
                                        </span>
                                        <small>puntos</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <?php if(!empty($puntos_habilitados)): ?>
                            <?php foreach($puntos_habilitados as $punto): ?>
                            <div class="punto-habilitado">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h6 class="mb-1">
                                            <span class="badge bg-primary me-2"><?= htmlspecialchars($punto['numero']) ?></span>
                                            <?= htmlspecialchars($punto['descripcion'] ?? '') ?>
                                        </h6>
                                        <?php if(!empty($punto['ponente'])): ?>
                                        <small class="text-muted">
                                            <i class="bi bi-person-speak me-1"></i>
                                            Ponente: <?= htmlspecialchars($punto['ponente']) ?>
                                        </small>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <?php if(!empty($punto['fecha_habilitacion'])): ?>
                                        <small class="text-muted">
                                            <i class="bi bi-clock me-1"></i>
                                            <?= date('d/m H:i', strtotime($punto['fecha_habilitacion'])) ?>
                                        </small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                        <div class="sin-datos">
                            <i class="bi bi-list fs-1 text-muted mb-3"></i>
                            <p class="mb-0">Aún no se han habilitado puntos para votación</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Columna derecha: Estadísticas -->
            <div class="col-lg-4">
                <!-- Estadísticas -->
                <div class="card-publico">
                    <div class="card-body" id="estadisticas-container">
                        <div class="estadisticas-header-mejorado mb-4">
                            <div class="estadisticas-titulo-wrapper">
                                <div class="estadisticas-icono-principal">
                                    <i class="bi bi-bar-chart-fill"></i>
                                </div>
                                <div class="estadisticas-titulo-content">
                                    <h3 class="estadisticas-titulo-principal">
                                        Estadísticas de la Sesión
                                    </h3>
                                    <div class="estadisticas-subtitulo">
                                        <small class="text-muted">
                                            <i class="bi bi-graph-up me-1"></i>
                                            Métricas en tiempo real del estado de la sesión
                                        </small>
                                    </div>
                                </div>
                                <div class="estadisticas-indicador">
                                    <div class="indicador-estado">
                                        <div class="estado-dot pulse"></div>
                                        <small>ACTIVO</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="text-center p-3 bg-light rounded">
                                    <div class="fs-4 text-primary fw-bold" id="stat-total-presentes"><?= $estadisticas['total_miembros'] ?></div>
                                    <small class="text-muted">Presentes</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-3 bg-light rounded">
                                    <div class="fs-4 text-success fw-bold" id="stat-puntos-habilitados"><?= $estadisticas['puntos_habilitados'] ?></div>
                                    <small class="text-muted">Puntos Habilitados</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-3 text-center">
                            <span class="badge p-2 <?= $estadisticas['sesion_activa'] ? 'bg-success' : 'bg-secondary' ?>" id="badge-estado-sesion">
                                <i class="bi <?= $estadisticas['sesion_activa'] ? 'bi-broadcast' : 'bi-stop-circle' ?> me-1" id="icono-estado-sesion"></i>
                                <span id="texto-estado-sesion"><?= $estadisticas['sesion_activa'] ? 'Sesión Activa' : 'Sesión Inactiva' ?></span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Timestamp de última actualización -->
        <div class="timestamp">
            <i class="bi bi-clock me-1"></i>
            Última actualización: <span id="ultima-actualizacion"><?= date('d/m/Y H:i:s') ?></span>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    // Actualización automática cada 10 segundos
        let actualizacionActiva = true;
        const sesionId = <?= $sesion['id'] ?>;
        const BASE_URL = '<?= BASE_URL ?>';
    // Preferencias de accesibilidad (persisten en localStorage)
    const PREF_CONTRASTE_KEY = 'publica_high_contrast';
    const PREF_FONT_KEY = 'publica_font_scale';
    const PREF_SALA_KEY = 'publica_modo_sala';
        
        function actualizarDatos() {
            if (!actualizacionActiva) return;
            
            const icono = document.getElementById('icono-actualizacion');
            const estadoConexion = document.getElementById('estado-conexion');
            
            // Mostrar animación de carga
            icono.style.animation = 'spin 1s linear infinite';
            
            fetch(BASE_URL + 'votacion/estado-publico-json/' + sesionId)
                .then(response => response.json())
                .then(data => {
                    // Actualizar timestamp
                    document.getElementById('ultima-actualizacion').textContent = 
                        new Date().toLocaleString('es-ES');
                    
                    // Conexión exitosa
                    estadoConexion.className = 'estado-conexion conectado';
                    icono.style.animation = '';
                    
                    // Actualizar DOM con datos en tiempo real
                    if (data) {
                        actualizarVistaPublica(data);
                    }
                })
                .catch(error => {
                    console.error('Error al actualizar:', error);
                    estadoConexion.className = 'estado-conexion desconectado';
                    icono.style.animation = '';
                });
        }
        
        // Configurar actualización automática
        setInterval(actualizarDatos, 10000); // Cada 10 segundos
        
        // CSS para animación de giro
        const style = document.createElement('style');
        style.textContent = `
            @keyframes spin {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }
        `;
        document.head.appendChild(style);

        function escapeHTML(str) {
            if (typeof str !== 'string') return '';
            return str
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function actualizarVistaPublica(data) {
            // Actualizar estado sesión
            const activa = data.sesion && data.sesion.estado === 'activa';
            const badgeEstado = document.getElementById('badge-estado-sesion');
            const iconoEstado = document.getElementById('icono-estado-sesion');
            const textoEstado = document.getElementById('texto-estado-sesion');
            if (badgeEstado && iconoEstado && textoEstado) {
                badgeEstado.className = 'badge p-2 ' + (activa ? 'bg-success' : 'bg-secondary');
                iconoEstado.className = 'bi ' + (activa ? 'bi-broadcast' : 'bi-stop-circle') + ' me-1';
                textoEstado.textContent = activa ? 'Sesión Activa' : 'Sesión Inactiva';
            }

            // Actualizar presentes
            const totalPresentes = (data.miembros_presentes || []).length;
            const contPresBadge = document.getElementById('stat-total-presentes-badge');
            const contPres = document.getElementById('stat-total-presentes');
            if (contPresBadge) contPresBadge.textContent = totalPresentes;
            if (contPres) contPres.textContent = totalPresentes;
            const contListaPres = document.getElementById('miembros-presentes-container');
            if (contListaPres) {
                const miembros = data.miembros_presentes || [];
                const maxMiembros = 7;
                
                // Reorganizar miembros para priorizar Presidente y Vice Presidente en posición #1 (igual que en PHP)
                const miembrosOrganizados = [];
                let presidente = null;
                let vicePresidente = null;
                const concejales = [];
                
                // Separar miembros por puesto
                miembros.forEach(miembro => {
                    if (miembro.puesto && miembro.puesto.trim()) {
                        const puestoLimpio = miembro.puesto.trim();
                        if (puestoLimpio === 'Presidente') {
                            presidente = miembro;
                        } else if (puestoLimpio === 'Vice Presidente') {
                            vicePresidente = miembro;
                        } else if (puestoLimpio === 'Concejal') {
                            concejales.push(miembro);
                        }
                        // Los demás puestos (Secretario, Pro Secretario, etc.) NO se muestran
                    }
                });
                
                // Asignar posición #1 (presidencial) - SOLO Presidente o Vice Presidente
                if (presidente) {
                    miembrosOrganizados[0] = presidente; // Presidente en posición #1
                } else if (vicePresidente) {
                    miembrosOrganizados[0] = vicePresidente; // Vice Presidente en posición #1 si no hay Presidente
                }
                
                // Llenar las demás posiciones SOLO con Concejales
                let posicionActual = 1; // Empezar desde posición #2
                
                concejales.forEach(concejal => {
                    if (posicionActual < maxMiembros) {
                        miembrosOrganizados[posicionActual] = concejal;
                        posicionActual++;
                    }
                });
                
                // Posiciones predefinidas para 7 miembros: #1 preside arriba, los otros 6 en semicírculo uniforme
                const posiciones = [
                    {left: '50%', top: '10px', transform: 'translateX(-50%)'}, // #1 - Presidente (arriba)
                    {left: '10%', bottom: '30px', transform: 'translateX(-50%)'}, // #2 - Extremo izquierdo
                    {left: '26%', bottom: '15px', transform: 'translateX(-50%)'}, // #3 - Izquierda
                    {left: '42%', bottom: '8px', transform: 'translateX(-50%)'}, // #4 - Centro izquierda
                    {left: '58%', bottom: '8px', transform: 'translateX(-50%)'}, // #5 - Centro derecha
                    {left: '74%', bottom: '15px', transform: 'translateX(-50%)'}, // #6 - Derecha
                    {left: '90%', bottom: '30px', transform: 'translateX(-50%)'}, // #7 - Extremo derecho
                ];
                
                let hemicicloHtml = '';
                for (let i = 0; i < maxMiembros; i++) {
                    const miembro = miembrosOrganizados[i] || null;
                    const posicion = posiciones[i];
                    const claseEstado = miembro ? 'presente' : 'ausente';
                    const iniciales = miembro ? 
                        (miembro.first_name.charAt(0) + miembro.last_name.charAt(0)).toUpperCase() : 
                        '?';
                    const nombreCompleto = miembro ? 
                        escapeHTML((miembro.first_name || '') + ' ' + (miembro.last_name || '')) : 
                        'Vacante';
                    const rol = miembro ? escapeHTML(miembro.role_name || '') : '';
                    const puesto = miembro && miembro.puesto ? escapeHTML(miembro.puesto) : '';
                    const posicionNumero = i + 1;
                    
                    // Construir estilos de posición (top o bottom)
                    let estilosPosicion = `left: ${posicion.left}; transform: ${posicion.transform};`;
                    if (posicion.top) {
                        estilosPosicion += ` top: ${posicion.top};`;
                    } else {
                        estilosPosicion += ` bottom: ${posicion.bottom};`;
                    }
                    
                    hemicicloHtml += `
                        <div class="miembro-hemiciclo ${claseEstado} ${posicionNumero === 1 ? 'presidente' : ''}" 
                             style="${estilosPosicion}"
                             data-miembro-id="${miembro ? miembro.id : 'vacio-' + i}"
                             data-posicion="${posicionNumero}">
                            <div class="miembro-avatar">
                                <span class="iniciales">${iniciales}</span>
                                ${miembro ? '<div class="estado-indicator presente-indicator"></div>' : ''}
                            </div>
                            <div class="miembro-tooltip">
                                <div class="tooltip-header">
                                    <strong>${nombreCompleto}</strong>
                                    <span class="posicion-badge">#${posicionNumero}</span>
                                </div>
                                ${puesto ? '<div class="tooltip-puesto"><small><i class="bi bi-award me-1"></i>' + puesto + '</small></div>' : ''}
                                ${rol ? '<div class="tooltip-role"><small><i class="bi bi-person-badge me-1"></i>' + (puesto || rol) + '</small></div>' : ''}
                                <div class="tooltip-status">
                                    <small class="${miembro ? 'text-success' : 'text-muted'}">
                                        <i class="bi ${miembro ? 'bi-circle-fill' : 'bi-circle'} me-1"></i>
                                        ${miembro ? 'Presente' : 'Ausente'}
                                    </small>
                                </div>
                            </div>
                        </div>`;
                }
                
                let html = `
                    <!-- Header mejorado con más información -->
                    <div class="hemiciclo-header mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h3 class="h5 mb-0 section-title d-flex align-items-center">
                                <div class="icon-wrapper me-3">
                                    <i class="bi bi-people-fill"></i>
                                </div>
                                <span>Sala de Sesiones</span>
                            </h3>
                            <div class="hemiciclo-stats">
                                <span class="badge bg-gradient-info px-3 py-2 rounded-pill">
                                    <i class="bi bi-person-check-fill me-1"></i>
                                    ${totalPresentes} presentes
                                </span>
                            </div>
                        </div>
                        <div class="hemiciclo-subtitle">
                            <small class="text-muted d-flex align-items-center">
                                <i class="bi bi-broadcast me-1"></i>
                                Visualización en tiempo real del pleno municipal
                            </small>
                        </div>
                    </div>
                    
                    <!-- Contenedor principal del hemiciclo -->
                    <div class="hemiciclo-wrapper">
                        <div class="hemiciclo-container">
                            <div class="hemiciclo" id="hemiciclo">
                                ${hemicicloHtml}
                                <!-- Etiqueta central mejorada -->
                                <div class="hemiciclo-label">
                                    <div class="label-content">
                                        <i class="bi bi-geo-alt-fill me-2"></i>
                                        <span class="label-text">Sesión Virtual</span>
                                    </div>
                                    <div class="label-status">
                                        <div class="status-dot live"></div>
                                        <small>En vivo</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Leyenda mejorada -->
                        <div class="hemiciclo-leyenda-wrapper">
                            <div class="hemiciclo-leyenda">
                                <div class="leyenda-title">
                                    <small class="text-muted fw-medium">Estado de miembros:</small>
                                </div>
                                <div class="leyenda-items">
                                    <div class="leyenda-item">
                                        <div class="leyenda-dot presente"></div>
                                        <span>Presente</span>
                                        <small class="count-badge">${miembrosOrganizados.filter(m => m).length}</small>
                                    </div>
                                    <div class="leyenda-item">
                                        <div class="leyenda-dot ausente"></div>
                                        <span>Ausente</span>
                                        <small class="count-badge">${Math.max(0, maxMiembros - miembrosOrganizados.filter(m => m).length)}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    ${(presidente ? 1 : 0) + (vicePresidente ? 1 : 0) + concejales.length > maxMiembros ? `
                        <div class="hemiciclo-info mt-4">
                            <div class="info-card">
                                <div class="info-content">
                                    <i class="bi bi-info-circle-fill text-primary me-2"></i>
                                    <span>Se muestran los primeros <strong>${maxMiembros}</strong> miembros del pleno.</span>
                                </div>
                                <div class="info-stats">
                                    <span class="badge bg-light text-dark">
                                        Total: <strong>${miembros.length}</strong> presentes
                                    </span>
                                </div>
                            </div>
                        </div>` : ''}
                    
                    <!-- Indicador de quórum -->
                    <div class="quorum-indicator mt-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="quorum-info">
                                <small class="text-muted">Quórum:</small>
                                <span class="fw-medium ${totalPresentes >= 4 ? 'text-success' : 'text-warning'}">
                                    ${totalPresentes}/4 mínimo
                                </span>
                            </div>
                            <div class="quorum-status">
                                ${totalPresentes >= 4 ? `
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">
                                        <i class="bi bi-check-circle-fill me-1"></i>
                                        Quórum alcanzado
                                    </span>
                                ` : `
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                        Sin quórum
                                    </span>
                                `}
                            </div>
                        </div>
                    </div>
                    
                    ${(presidente ? 1 : 0) + (vicePresidente ? 1 : 0) + concejales.length > maxMiembros ? `
                        <div class="hemiciclo-info mt-4">
                            <div class="info-card">
                                <div class="info-content">
                                    <i class="bi bi-info-circle-fill text-primary me-2"></i>
                                    <span>Se muestran los primeros <strong>${maxMiembros}</strong> miembros del pleno.</span>
                                </div>
                                <div class="info-stats">
                                    <span class="badge bg-light text-dark">
                                        Total: <strong>${(presidente ? 1 : 0) + (vicePresidente ? 1 : 0) + concejales.length}</strong> presentes
                                    </span>
                                </div>
                            </div>
                        </div>` : ''}
                    
                    <!-- Indicador de quórum -->
                    <div class="quorum-indicator mt-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="quorum-info">
                                <small class="text-muted">Quórum:</small>
                                <span class="fw-medium ${totalPresentes >= 4 ? 'text-success' : 'text-warning'}">
                                    ${totalPresentes}/4 mínimo
                                </span>
                            </div>
                            <div class="quorum-status">
                                ${totalPresentes >= 4 ? `
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">
                                        <i class="bi bi-check-circle-fill me-1"></i>
                                        Quórum alcanzado
                                    </span>
                                ` : `
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                        Sin quórum
                                    </span>
                                `}
                            </div>
                        </div>
                    </div>
                `;
                
                contListaPres.innerHTML = html;
            }

            // Actualizar puntos habilitados
            const puntos = data.puntos_habilitados || [];
            const contPuntos = document.getElementById('puntos-habilitados-container');
            const statPuntos = document.getElementById('stat-puntos-habilitados');
            const contadorPuntos = document.getElementById('contador-puntos-badge');
            if (statPuntos) statPuntos.textContent = puntos.length;
            if (contadorPuntos) contadorPuntos.textContent = puntos.length;
            if (contPuntos) {
                let html = `
                    <div class="puntos-header-mejorado mb-4">
                        <div class="puntos-titulo-wrapper">
                            <div class="puntos-icono-principal">
                                <i class="bi bi-list-check"></i>
                            </div>
                            <div class="puntos-titulo-content">
                                <h3 class="puntos-titulo-principal">
                                    Puntos Habilitados para Votación
                                </h3>
                                <div class="puntos-subtitulo">
                                    <small class="text-muted">
                                        <i class="bi bi-clock-history me-1"></i>
                                        Agenda del orden del día habilitada para consulta
                                    </small>
                                </div>
                            </div>
                            <div class="puntos-contador">
                                <div class="contador-badge">
                                    <span class="contador-numero" id="contador-puntos-badge">
                                        ${puntos.length}
                                    </span>
                                    <small>puntos</small>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                if (puntos.length === 0) {
                    html += `
                        <div class="sin-datos">
                            <i class="bi bi-list fs-1 text-muted mb-3"></i>
                            <p class="mb-0">Aún no se han habilitado puntos para votación</p>
                        </div>`;
                } else {
                    const puntoActualId = data.punto_actual_id;
                    puntos.forEach(p => {
                        const num = escapeHTML(String(p.numero ?? ''));
                        const desc = escapeHTML(String(p.descripcion ?? ''));
                        const fecha = p.fecha_habilitacion ? new Date(p.fecha_habilitacion) : null;
                        const fechaStr = fecha ? `${fecha.toLocaleDateString('es-ES', {day:'2-digit', month:'2-digit'})} ${fecha.toLocaleTimeString('es-ES', {hour:'2-digit', minute:'2-digit'})}` : '';

                        // Classes para estado visual
                        let clases = 'punto-habilitado';
                        if (p.punto_id === puntoActualId) {
                            clases += ' punto-actual';
                        } else if (p.fecha_habilitacion) {
                            clases += ' punto-votado';
                        }

                        // Badge de resultado final si existe
                        let resultadoBadge = '';
                        if (p.resultado_final) {
                            let badgeClass = 'bg-secondary';
                            let texto = '';
                            switch (p.resultado_final) {
                                case 'aprobado': badgeClass = 'bg-success'; texto = 'Aprobado'; break;
                                case 'rechazado': badgeClass = 'bg-danger'; texto = 'Rechazado'; break;
                                case 'empate': badgeClass = 'bg-warning text-dark'; texto = 'Empate'; break;
                                default: badgeClass = 'bg-secondary'; texto = p.resultado_final; break;
                            }
                            resultadoBadge = `<span class="badge ${badgeClass} ms-2">${escapeHTML(texto)}</span>`;
                        }

                        // Conteos en la fila si es el punto actual y hay resultados
                        let conteosActual = '';
                        if (p.punto_id === puntoActualId && data.votacion_actual && data.votacion_actual.resultados) {
                            const res = data.votacion_actual.resultados || {};
                            conteosActual = `
                                <span class="badge bg-secondary rounded-pill ms-2">A: ${res.positivo ?? 0}</span>
                                <span class="badge bg-secondary rounded-pill ms-2">R: ${res.negativo ?? 0}</span>
                                <span class="badge bg-secondary rounded-pill ms-2">Abs: ${res.abstencion ?? 0}</span>`;
                        }

                        html += `
                            <div class="${clases}">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h6 class="mb-1">
                                            <span class="badge bg-primary me-2">${num}</span>
                                            ${desc}
                                        </h6>
                                    </div>
                                    <div class="col-md-4 text-end d-flex align-items-center justify-content-end flex-wrap">
                                        ${fechaStr ? `<small class=\"text-muted\"><i class=\"bi bi-clock me-1\"></i>${fechaStr}</small>` : ''}
                                        ${resultadoBadge}
                                        ${conteosActual}
                                    </div>
                                </div>
                            </div>`;
                    });
                }
                contPuntos.innerHTML = html;
            }

            // Actualizar "votación actual"
            const contVotacion = document.getElementById('votacion-actual-container');
            if (contVotacion) {
                let html = `
                    <h3 class="h5 mb-3">
                        <i class="bi bi-hand-thumbs-up text-primary me-2"></i>
                        ¿Qué se está votando ahora?
                    </h3>
                `;
                const va = data.votacion_actual;
                if (va && (va.punto || (va.numero || va.descripcion))) {
                    const punto = va.punto || va;
                    const num = escapeHTML(String(punto.numero ?? ''));
                    const desc = escapeHTML(String(punto.descripcion ?? ''));
                    const desde = (va.votacion && va.votacion.created_at) ? new Date(va.votacion.created_at) : null;
                    html += `
                        <div class="votacion-activa">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h4 class="h6 mb-1"><i class="bi bi-play-circle me-2"></i>Punto ${num}</h4>
                                    <p class="mb-0">${desc}</p>
                                </div>
                                <div class="col-md-4 text-end">
                                    <div class="fs-5"><i class="bi bi-vote-fill me-2"></i><strong>VOTANDO</strong></div>
                                    ${desde ? `<small>Desde: ${desde.toLocaleTimeString('es-ES', {hour:'2-digit', minute:'2-digit'})}</small>` : ''}
                                </div>
                            </div>
                            ${(() => {
                                const res = va.resultados || null;
                                if (!res) return '';
                                const positivos = res.positivo ?? 0;
                                const negativos = res.negativo ?? 0;
                                const abst = res.abstencion ?? 0;
                                const totalPres = (data.miembros_presentes || []).length;
                                const umbral = totalPres > 0 ? Math.floor(totalPres/2) + 1 : 0;
                                const mayoria = umbral > 0 && positivos >= umbral;
                                
                                // Resultado final del expediente
                                let resultadoFinal = '';
                                if (va.resultado_final) {
                                    const resultado = va.resultado_final.toLowerCase();
                                    let badgeClass = 'bg-secondary';
                                    let icono = 'bi-info-circle-fill';
                                    let texto = va.resultado_final.toUpperCase();
                                    
                                    switch(resultado) {
                                        case 'positivo':
                                        case 'aprobado':
                                            badgeClass = 'bg-success';
                                            icono = 'bi-check-circle-fill';
                                            texto = 'APROBADO';
                                            break;
                                        case 'negativo':
                                        case 'rechazado':
                                            badgeClass = 'bg-danger';
                                            icono = 'bi-x-circle-fill';
                                            texto = 'RECHAZADO';
                                            break;
                                        case 'abstencion':
                                        case 'empate':
                                            badgeClass = 'bg-warning text-dark';
                                            icono = 'bi-dash-circle-fill';
                                            texto = 'ABSTENCIÓN';
                                            break;
                                    }
                                    
                                    resultadoFinal = `
                                        <span class="resultado-final-badge badge ${badgeClass} fs-6 px-3 py-2" style="
                                            border-radius: 20px;
                                            font-weight: 700;
                                            letter-spacing: 0.5px;
                                            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
                                            animation: pulse-resultado 2s infinite ease-in-out;
                                        ">
                                            <i class="bi ${icono} me-2"></i>
                                            ${texto}
                                        </span>`;
                                }
                                
                                return `
                                    <div class="resumen-conteos mt-3">
                                        <div class="row g-3">
                                            <div class="col-4">
                                                <div class="conteo-card">
                                                    <div class="conteo-valor text-success">${positivos}</div>
                                                    <div class="conteo-label">Aprobaciones</div>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="conteo-card">
                                                    <div class="conteo-valor text-danger">${negativos}</div>
                                                    <div class="conteo-label">Rechazos</div>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="conteo-card">
                                                    <div class="conteo-valor text-warning">${abst}</div>
                                                    <div class="conteo-label">Abstenciones</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mt-2">
                                            <small class="text-muted">Presentes: ${totalPres} · Mayoría: ${umbral}</small>
                                            <div class="d-flex align-items-center gap-2">
                                                ${mayoria ? '<span class="mayoria-indicador"><i class="bi bi-check2-circle"></i> Mayoría alcanzada</span>' : ''}
                                                ${resultadoFinal}
                                            </div>
                                        </div>
                                    </div>`;
                            })()}
                        </div>`;
                } else {
                    html += `
                        <div class="sin-datos">
                            <i class="bi bi-pause-circle fs-1 text-muted mb-3"></i>
                            <p class="mb-0">No hay votación activa en este momento</p>
                        </div>`;
                }
                contVotacion.innerHTML = html;
            }
        }

        // ===== Accesibilidad: Alto contraste y tamaño de texto =====
        function aplicarContraste(enabled) {
            document.body.classList.toggle('high-contrast', !!enabled);
            localStorage.setItem(PREF_CONTRASTE_KEY, enabled ? '1' : '0');
        }

        function aplicarFontScale(scale) {
            const clamped = Math.max(90, Math.min(180, scale));
            document.documentElement.style.fontSize = clamped + '%';
            localStorage.setItem(PREF_FONT_KEY, String(clamped));
        }

        // Modo sala: oculta secciones y aumenta tamaños
        function aplicarModoSala(enabled) {
            document.body.classList.toggle('modo-sala', !!enabled);
            if (enabled) {
                // Aumenta un poco la base tipográfica al entrar a modo sala
                const current = parseInt(getComputedStyle(document.documentElement).fontSize) / 16 * 100;
                const target = 130;
                if (current < target) {
                    document.documentElement.style.fontSize = target + '%';
                    localStorage.setItem(PREF_FONT_KEY, String(target));
                }
            }
            localStorage.setItem(PREF_SALA_KEY, enabled ? '1' : '0');
        }

        // Inicializar controles de accesibilidad
        (function initAccesibilidad() {
            const savedContrast = localStorage.getItem(PREF_CONTRASTE_KEY) === '1';
            const savedFont = parseInt(localStorage.getItem(PREF_FONT_KEY) || '100', 10);
            const savedSala = localStorage.getItem(PREF_SALA_KEY) === '1';
            aplicarContraste(savedContrast);
            aplicarFontScale(isNaN(savedFont) ? 100 : savedFont);
            aplicarModoSala(savedSala);

            // Controles del header
            const btnContraste = document.getElementById('btn-contraste');
            const btnFontDec = document.getElementById('font-dec');
            const btnFontInc = document.getElementById('font-inc');
            const btnSala = document.getElementById('btn-sala');
            const btnFullscreen = document.getElementById('btn-fullscreen');
            
            // Controles flotantes
            const btnContrasteFlotante = document.getElementById('btn-contraste-flotante');
            const btnFontDecFlotante = document.getElementById('font-dec-flotante');
            const btnFontIncFlotante = document.getElementById('font-inc-flotante');
            const btnSalaFlotante = document.getElementById('btn-sala-flotante');
            const btnFullscreenFlotante = document.getElementById('btn-fullscreen-flotante');
            
            // Funciones de control
            const toggleContraste = () => aplicarContraste(!document.body.classList.contains('high-contrast'));
            const decrementFont = () => {
                const current = parseInt(getComputedStyle(document.documentElement).fontSize) / 16 * 100;
                aplicarFontScale(Math.round(current - 10));
            };
            const incrementFont = () => {
                const current = parseInt(getComputedStyle(document.documentElement).fontSize) / 16 * 100;
                aplicarFontScale(Math.round(current + 10));
            };
            const toggleSala = () => aplicarModoSala(!document.body.classList.contains('modo-sala'));
            const toggleFullscreen = async (btn) => {
                try {
                    if (!document.fullscreenElement) {
                        await document.documentElement.requestFullscreen();
                        if (btn) btn.innerHTML = '<i class="bi bi-fullscreen-exit"></i>';
                        // Actualizar ambos botones
                        if (btnFullscreen) btnFullscreen.innerHTML = '<i class="bi bi-fullscreen-exit"></i>';
                        if (btnFullscreenFlotante) btnFullscreenFlotante.innerHTML = '<i class="bi bi-fullscreen-exit"></i>';
                    } else {
                        await document.exitFullscreen();
                        if (btn) btn.innerHTML = '<i class="bi bi-arrows-fullscreen"></i>';
                        // Actualizar ambos botones
                        if (btnFullscreen) btnFullscreen.innerHTML = '<i class="bi bi-arrows-fullscreen"></i>';
                        if (btnFullscreenFlotante) btnFullscreenFlotante.innerHTML = '<i class="bi bi-arrows-fullscreen"></i>';
                    }
                } catch (e) {
                    console.warn('No fue posible alternar pantalla completa:', e);
                }
            };
            
            // Event listeners del header
            if (btnContraste) btnContraste.addEventListener('click', toggleContraste);
            if (btnFontDec) btnFontDec.addEventListener('click', decrementFont);
            if (btnFontInc) btnFontInc.addEventListener('click', incrementFont);
            if (btnSala) btnSala.addEventListener('click', toggleSala);
            if (btnFullscreen) btnFullscreen.addEventListener('click', () => toggleFullscreen(btnFullscreen));
            
            // Event listeners de los controles flotantes
            if (btnContrasteFlotante) btnContrasteFlotante.addEventListener('click', toggleContraste);
            if (btnFontDecFlotante) btnFontDecFlotante.addEventListener('click', decrementFont);
            if (btnFontIncFlotante) btnFontIncFlotante.addEventListener('click', incrementFont);
            if (btnSalaFlotante) btnSalaFlotante.addEventListener('click', toggleSala);
            if (btnFullscreenFlotante) btnFullscreenFlotante.addEventListener('click', () => toggleFullscreen(btnFullscreenFlotante));
            
            // Listener para cambios de fullscreen (para actualizar iconos)
            document.addEventListener('fullscreenchange', () => {
                const icon = document.fullscreenElement ? '<i class="bi bi-fullscreen-exit"></i>' : '<i class="bi bi-arrows-fullscreen"></i>';
                if (btnFullscreen) btnFullscreen.innerHTML = icon;
                if (btnFullscreenFlotante) btnFullscreenFlotante.innerHTML = icon;
            });
        })();
        
        // ===== Auto-ocultado de controles flotantes =====
        (function initAutoHide() {
            const controlesFlotantes = document.getElementById('controles-flotantes');
            const activationZone = document.getElementById('activation-zone');
            let hideTimeout;
            let isVisible = false;
            
            function showControls() {
                if (!isVisible) {
                    controlesFlotantes.classList.add('visible');
                    isVisible = true;
                }
                // Limpiar timeout anterior
                if (hideTimeout) {
                    clearTimeout(hideTimeout);
                }
            }
            
            function hideControls() {
                hideTimeout = setTimeout(() => {
                    controlesFlotantes.classList.remove('visible');
                    isVisible = false;
                }, 2000); // Ocultar después de 2 segundos
            }
            
            // Mostrar cuando el mouse entra en la zona de activación
            if (activationZone) {
                activationZone.addEventListener('mouseenter', showControls);
            }
            
            // Mostrar cuando el mouse entra en los controles
            if (controlesFlotantes) {
                controlesFlotantes.addEventListener('mouseenter', () => {
                    showControls();
                });
                
                // Ocultar cuando el mouse sale de los controles
                controlesFlotantes.addEventListener('mouseleave', hideControls);
                
                // También activar al hacer click en la pestaña
                controlesFlotantes.addEventListener('click', (e) => {
                    if (e.target === controlesFlotantes) {
                        showControls();
                    }
                });
            }
            
            // También mostrar cuando el mouse se acerca al borde derecho de la pantalla
            document.addEventListener('mousemove', (e) => {
                const screenWidth = window.innerWidth;
                const threshold = 50; // Distancia en píxeles desde el borde
                
                // En desktop: activar desde el borde derecho
                if (window.innerWidth > 768) {
                    if (e.clientX >= screenWidth - threshold) {
                        showControls();
                    }
                } else {
                    // En móviles: activar desde el borde inferior
                    const screenHeight = window.innerHeight;
                    if (e.clientY >= screenHeight - threshold) {
                        showControls();
                    }
                }
            });
            
            // Ocultar inicialmente después de 3 segundos
            setTimeout(() => {
                if (!isVisible) {
                    hideControls();
                }
            }, 3000);
        })();
    </script>
</body>
</html>