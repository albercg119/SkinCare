<?php
// Prevenir cualquier salida automática
ob_start();

// Limpiar cualquier salida anterior y buffer
if (ob_get_length()) ob_clean();

// Headers CORS y JSON
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Methods: POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 3600');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

// Manejar preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    // Leer el body de la petición
    $input = file_get_contents('php://input');
    
    // Decodificar JSON
    $data = json_decode($input);
    
    // Verificar errores de JSON
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Error al decodificar JSON: ' . json_last_error_msg());
    }
    
    // Validar ID
    if (!$data || !isset($data->id) || !is_numeric($data->id)) {
        throw new Exception('ID del producto no proporcionado o inválido');
    }
    
    // Incluir archivos necesarios
    require_once '../../../config/database.php';
    require_once '../../src/Models/ProductModel.php';
    
    // Inicializar conexión
    $database = new Database();
    $db = $database->getConnection();
    $product = new ProductModel($db);
    
    // Asignar ID y eliminar
    $product->id = $data->id;
    
    // Intentar eliminar
    if ($product->delete($data->id)) { 
        // Limpiar cualquier salida antes de enviar la respuesta
        if (ob_get_length()) ob_clean();
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Producto eliminado exitosamente'
        ]);
    } else {
        throw new Exception('Error al eliminar el producto');
    }
} catch (Exception $e) {
    // Limpiar cualquier salida antes de enviar el error
    if (ob_get_length()) ob_clean();
    
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

// Asegurar que no haya más salida después del JSON
exit();