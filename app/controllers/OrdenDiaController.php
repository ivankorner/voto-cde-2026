<?php
require_once 'core/Controller.php';

class OrdenDiaController extends Controller {
    
    public function index() {
        $this->requireAdmin();
        
        $ordenDiaModel = $this->loadModel('OrdenDia');
        $ordenes = $ordenDiaModel->getAll();
        $estadisticas = $ordenDiaModel->getEstadisticas();
        
        $data = [
            'ordenes' => $ordenes,
            'estadisticas' => $estadisticas,
            'page_title' => 'Órdenes del Día'
        ];
        
        $this->loadView('orden_dia/index', $data);
    }
    
    public function create() {
        $this->requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleCreate();
            return;
        }
        
        $data = [
            'page_title' => 'Nueva Orden del Día'
        ];
        
        $this->loadView('orden_dia/create', $data);
    }
    
    private function handleCreate() {
        $ordenDiaModel = $this->loadModel('OrdenDia');
        
        // Validaciones
        $errors = [];
        
        if (empty($_POST['numero_acta'])) {
            $errors[] = 'El número de acta es requerido';
        } elseif (!$ordenDiaModel->validarNumeroActa($_POST['numero_acta'])) {
            $errors[] = 'Este número de acta ya existe';
        }
        
        if (empty($_POST['fecha_sesion'])) {
            $errors[] = 'La fecha de sesión es requerida';
        }
        
        if (empty($_POST['hora_sesion'])) {
            $errors[] = 'La hora de sesión es requerida';
        }
        
        if (!empty($errors)) {
            $_SESSION['error_message'] = implode('<br>', $errors);
            header('Location: ' . BASE_URL . 'orden_dia/create');
            exit;
        }
        
        $data = [
            'numero_acta' => $_POST['numero_acta'],
            'fecha_sesion' => $_POST['fecha_sesion'],
            'hora_sesion' => $_POST['hora_sesion'],
            'tipo_sesion' => $_POST['tipo_sesion'] ?? 'ordinaria',
            'created_by' => $_SESSION['user_id']
        ];
        
        $ordenId = $ordenDiaModel->create($data);
        
        if ($ordenId) {
            $_SESSION['success_message'] = 'Orden del día creada exitosamente';
            header('Location: ' . BASE_URL . 'orden_dia/edit/' . $ordenId);
        } else {
            $_SESSION['error_message'] = 'Error al crear la orden del día';
            header('Location: ' . BASE_URL . 'orden_dia/create');
        }
        exit;
    }
    
    public function edit($id) {
        $this->requireAdmin();
        
        $ordenDiaModel = $this->loadModel('OrdenDia');
        $ordenDiaItemModel = $this->loadModel('OrdenDiaItem');
        
        $orden = $ordenDiaModel->getById($id);
        
        if (!$orden) {
            $_SESSION['error_message'] = 'Orden del día no encontrada';
            header('Location: ' . BASE_URL . 'orden_dia');
            exit;
        }
        
        // Asegurar que los ítems estándar existan
        $ordenDiaModel->inicializarItemsEstandar($id);
        
        $items = $ordenDiaItemModel->getItemsCompletos($id);
        
        $data = [
            'orden' => $orden,
            'items' => $items,
            'page_title' => 'Editar Orden del Día - Acta ' . $orden['numero_acta']
        ];
        
        $this->loadView('orden_dia/edit', $data);
    }
    
    public function update($id) {
        $this->requireAdmin();
        
        $ordenDiaModel = $this->loadModel('OrdenDia');
        
        $errors = [];
        
        if (empty($_POST['numero_acta'])) {
            $errors[] = 'El número de acta es requerido';
        } elseif (!$ordenDiaModel->validarNumeroActa($_POST['numero_acta'], $id)) {
            $errors[] = 'Este número de acta ya existe';
        }
        
        if (empty($_POST['fecha_sesion'])) {
            $errors[] = 'La fecha de sesión es requerida';
        }
        
        if (empty($_POST['hora_sesion'])) {
            $errors[] = 'La hora de sesión es requerida';
        }
        
        if (!empty($errors)) {
            $_SESSION['error_message'] = implode('<br>', $errors);
            header('Location: ' . BASE_URL . 'orden_dia/edit/' . $id);
            exit;
        }
        
        $data = [
            'numero_acta' => $_POST['numero_acta'],
            'fecha_sesion' => $_POST['fecha_sesion'],
            'hora_sesion' => $_POST['hora_sesion'],
            'tipo_sesion' => $_POST['tipo_sesion'] ?? 'ordinaria',
            'estado' => $_POST['estado'] ?? 'borrador'
        ];
        
        if ($ordenDiaModel->update($id, $data)) {
            $_SESSION['success_message'] = 'Orden del día actualizada exitosamente';
        } else {
            $_SESSION['error_message'] = 'Error al actualizar la orden del día';
        }
        
        header('Location: ' . BASE_URL . 'orden_dia/edit/' . $id);
        exit;
    }
    
    public function delete($id) {
        $this->requireAdmin();
        
        $ordenDiaModel = $this->loadModel('OrdenDia');
        
        if ($ordenDiaModel->delete($id)) {
            $_SESSION['success_message'] = 'Orden del día eliminada exitosamente';
        } else {
            $_SESSION['error_message'] = 'Error al eliminar la orden del día';
        }
        
        header('Location: ' . BASE_URL . 'orden_dia');
        exit;
    }
    
    public function view($id) {
        $this->requireLogin(); // Cualquier usuario logueado puede ver
        
        $ordenDiaModel = $this->loadModel('OrdenDia');
        $ordenDiaItemModel = $this->loadModel('OrdenDiaItem');
        
        $orden = $ordenDiaModel->getById($id);
        
        if (!$orden) {
            $_SESSION['error_message'] = 'Orden del día no encontrada';
            header('Location: ' . BASE_URL . 'orden_dia');
            exit;
        }
        
        // Asegurar que los ítems estándar existan para la vista también
        $ordenDiaModel->inicializarItemsEstandar($id);
        
        $items = $ordenDiaItemModel->getItemsCompletos($id);
        
        $data = [
            'orden' => $orden,
            'items' => $items,
            'page_title' => 'Orden del Día - Acta ' . $orden['numero_acta']
        ];
        
        $this->loadView('orden_dia/view', $data);
    }
    
    // Gestión de ítems
    public function updateItem($itemId) {
        $this->requireAdmin();
        
        $ordenDiaItemModel = $this->loadModel('OrdenDiaItem');
        
        $data = [
            'descripcion' => $_POST['descripcion'] ?? ''
        ];
        
        if ($ordenDiaItemModel->update($itemId, $data)) {
            $_SESSION['success_message'] = 'Ítem actualizado exitosamente';
        } else {
            $_SESSION['error_message'] = 'Error al actualizar el ítem';
        }
        
        // Redirigir de vuelta a la edición de la orden del día
        $item = $ordenDiaItemModel->getById($itemId);
        header('Location: ' . BASE_URL . 'orden_dia/edit/' . $item['orden_dia_id']);
        exit;
    }
    
    // Gestión de expedientes
    public function addExpediente($itemId) {
        $this->requireAdmin();
        
        $ordenDiaItemModel = $this->loadModel('OrdenDiaItem');
        
        $data = [
            'numero_expediente' => $_POST['numero_expediente'] ?? '',
            'extracto' => $_POST['extracto'] ?? '',
            'comision' => $_POST['comision'] ?? '',
            'tipo_instrumento' => $_POST['tipo_instrumento'] ?? null,
            'bloque_autor' => $_POST['bloque_autor'] ?? '',
            'concejal_autor' => $_POST['concejal_autor'] ?? '',
            'nombre_ciudadano' => $_POST['nombre_ciudadano'] ?? ''
        ];
        
        if ($ordenDiaItemModel->addExpediente($itemId, $data)) {
            $_SESSION['success_message'] = 'Expediente agregado exitosamente';
        } else {
            $_SESSION['error_message'] = 'Error al agregar el expediente';
        }
        
        // Redirigir de vuelta a la edición
        $item = $ordenDiaItemModel->getById($itemId);
        header('Location: ' . BASE_URL . 'orden_dia/edit/' . $item['orden_dia_id'] . '#item-' . $itemId);
        exit;
    }
    
    public function deleteExpediente($expedienteId) {
        $this->requireAdmin();
        
        $ordenDiaItemModel = $this->loadModel('OrdenDiaItem');
        $expediente = $ordenDiaItemModel->getExpedienteById($expedienteId);
        
        if (!$expediente) {
            $_SESSION['error_message'] = 'Expediente no encontrado';
            header('Location: ' . BASE_URL . 'orden_dia');
            exit;
        }
        
        if ($ordenDiaItemModel->deleteExpediente($expedienteId)) {
            $_SESSION['success_message'] = 'Expediente eliminado exitosamente';
        } else {
            $_SESSION['error_message'] = 'Error al eliminar el expediente';
        }
        
        // Redirigir de vuelta
        $item = $ordenDiaItemModel->getById($expediente['orden_dia_item_id']);
        header('Location: ' . BASE_URL . 'orden_dia/edit/' . $item['orden_dia_id'] . '#item-' . $expediente['orden_dia_item_id']);
        exit;
    }
    
    // Gestión de actas
    public function addActa($itemId) {
        $this->requireAdmin();
        
        $ordenDiaItemModel = $this->loadModel('OrdenDiaItem');
        
        $data = [
            'numero_acta' => $_POST['numero_acta'] ?? '',
            'tipo_sesion' => $_POST['tipo_sesion'] ?? '',
            'fecha_acta' => $_POST['fecha_acta'] ?? ''
        ];
        
        if ($ordenDiaItemModel->addActa($itemId, $data)) {
            $_SESSION['success_message'] = 'Acta agregada exitosamente';
        } else {
            $_SESSION['error_message'] = 'Error al agregar el acta';
        }
        
        // Redirigir de vuelta
        $item = $ordenDiaItemModel->getById($itemId);
        header('Location: ' . BASE_URL . 'orden_dia/edit/' . $item['orden_dia_id'] . '#item-' . $itemId);
        exit;
    }
    
    public function deleteActa($actaId) {
        $this->requireAdmin();
        
        $ordenDiaItemModel = $this->loadModel('OrdenDiaItem');
        $acta = $ordenDiaItemModel->getActaById($actaId);
        
        if (!$acta) {
            $_SESSION['error_message'] = 'Acta no encontrada';
            header('Location: ' . BASE_URL . 'orden_dia');
            exit;
        }
        
        if ($ordenDiaItemModel->deleteActa($actaId)) {
            $_SESSION['success_message'] = 'Acta eliminada exitosamente';
        } else {
            $_SESSION['error_message'] = 'Error al eliminar el acta';
        }
        
        // Redirigir de vuelta
        $item = $ordenDiaItemModel->getById($acta['orden_dia_item_id']);
        header('Location: ' . BASE_URL . 'orden_dia/edit/' . $item['orden_dia_id'] . '#item-' . $acta['orden_dia_item_id']);
        exit;
    }
    
    public function cambiarEstado($id) {
        $this->requireAdmin();
        
        $ordenDiaModel = $this->loadModel('OrdenDia');
        $nuevoEstado = $_POST['estado'] ?? 'borrador';
        
        if ($ordenDiaModel->updateEstado($id, $nuevoEstado)) {
            $_SESSION['success_message'] = 'Estado actualizado exitosamente';
        } else {
            $_SESSION['error_message'] = 'Error al actualizar el estado';
        }
        
        header('Location: ' . BASE_URL . 'orden_dia/edit/' . $id);
        exit;
    }
    
    // Lista pública (para sidebar)
    public function lista() {
        $this->requireLogin();
        
        $ordenDiaModel = $this->loadModel('OrdenDia');
        $ordenes = $ordenDiaModel->getByEstado('publicado');
        
        $data = [
            'ordenes' => $ordenes,
            'page_title' => 'Órdenes del Día Publicadas'
        ];
        
        $this->loadView('orden_dia/lista', $data);
    }
}
?>