<?php
require_once 'core/Controller.php';

class ErrorController extends Controller {
    
    public function notFound() {
        http_response_code(404);
        $this->loadView('errors/404', ['page_title' => 'Página no encontrada']);
    }
}
?>
