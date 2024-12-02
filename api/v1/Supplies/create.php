<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Max-Age: 3600');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

try {
    // Obtener el contenido raw del body
    $input = file_get_contents('php://input');
    error_log('Datos recibidos en create.php: ' . $input);
    
    $data = json_decode($input);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Error al decodificar JSON: ' . json_last_error_msg());
    }
    
    // Validar los datos recibidos
    if (!isset($data->article_id) || !isset($data->quantity)) {
        throw new Exception('Datos incompletos: Se requiere article_id y quantity');
    }
    
    // Convertir y validar los valores
    $article_id = filter_var($data->article_id, FILTER_VALIDATE_INT);
    $quantity = filter_var($data->quantity, FILTER_VALIDATE_INT);
    
    if ($article_id === false || $quantity === false) {
        throw new Exception('Los valores proporcionados no son válidos');
    }
    
    require_once __DIR__ . '/../../../config/database.php';
    require_once __DIR__ . '/../../../api/src/Models/SuppliesModel.php';
    
    $database = new Database();
    $db = $database->getConnection();
    
    $supplies = new SuppliesModel($db);
    $supplies->article_id = $article_id;
    $supplies->quantity = $quantity;
    
    if ($supplies->create()) {
        http_response_code(201);
        echo json_encode([
            "status" => "success",
            "message" => "Suministro creado exitosamente"
        ]);
    } else {
        throw new Exception("Error al crear el suministro");
    }
} catch (Exception $e) {
    error_log('Error en create.php: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
?>