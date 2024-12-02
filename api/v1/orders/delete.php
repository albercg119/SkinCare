<?php
// Prevenir cualquier salida automática y limpiar buffer
ob_start();
if (ob_get_length()) ob_clean();

// Headers CORS y JSON
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Methods: POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 3600');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    // Leer y validar el input JSON
    $input = file_get_contents('php://input');
    $data = json_decode($input);
    
    // Verificar errores de JSON
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Error al decodificar JSON: ' . json_last_error_msg());
    }
    
    // Validar ID
    if (!$data || !isset($data->id) || !is_numeric($data->id)) {
        throw new Exception('ID del pedido no proporcionado o inválido');
    }
    
    // Incluir dependencias
    require_once '../../../config/database.php';
    require_once '../../src/Models/OrderModel.php';
    
    // Inicializar
    $database = new Database();
    $db = $database->getConnection();
    $order = new Models\OrderModel($db);
    
    // Eliminar
    if ($order->delete($data->id)) {
        // Limpiar buffer antes de enviar respuesta
        if (ob_get_length()) ob_clean();
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Pedido eliminado exitosamente'
        ]);
    } else {
        throw new Exception('Error al eliminar el pedido');
    }
} catch (Exception $e) {
    // Limpiar buffer antes de enviar error
    if (ob_get_length()) ob_clean();
    
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

// Asegurar que no haya más salida
exit();