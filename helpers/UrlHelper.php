<?php
/**
 * Helper para generar URLs compatibles con servidores sin mod_rewrite
 */

class UrlHelper {
    
    /**
     * Genera una URL compatible
     * @param string $path La ruta (ej: 'votacion/crear', 'dashboard')
     * @return string URL completa
     */
    public static function url($path = '') {
        if (empty($path)) {
            return BASE_URL;
        }
        
        // Si la URL ya es absoluta, devolverla tal como está
        if (strpos($path, 'http') === 0) {
            return $path;
        }
        
        // Si contiene index.php, devolverla tal como está
        if (strpos($path, 'index.php') !== false) {
            return BASE_URL . $path;
        }
        
        // Para rutas internas, usar formato compatible
        return BASE_URL . 'index.php?url=' . ltrim($path, '/');
    }
    
    /**
     * Genera una URL de acción (para formularios)
     * @param string $path
     * @return string
     */
    public static function action($path) {
        return self::url($path);
    }
    
    /**
     * Genera URL para assets (CSS, JS, imágenes)
     * @param string $path
     * @return string
     */
    public static function asset($path) {
        return BASE_URL . ltrim($path, '/');
    }
}

/**
 * Función global para generar URLs
 * @param string $path
 * @return string
 */
function url($path = '') {
    return UrlHelper::url($path);
}

/**
 * Función global para assets
 * @param string $path
 * @return string
 */
function asset($path) {
    return UrlHelper::asset($path);
}
?>