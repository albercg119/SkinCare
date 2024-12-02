<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Max-Age: 3600');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

// Log de headers recibidos
error_log("Headers recibidos: " . print_r(getallheaders(), true));

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    // Log del input raw
    $input = file_get_contents('php://input');
    error_log("Input raw recibido: " . $input);

    // Intentar decodificar el JSON
    $data = json_decode($input);
    
    // Log del resultado de la decodificación
    error_log("Resultado de json_decode: " . print_r($data, true));
    error_log("JSON last error: " . json_last_error());
    error_log("JSON last error msg: " . json_last_error_msg());

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Error al decodificar JSON: ' . json_last_error_msg());
    }

    require_once '../../../config/database.php';
    require_once '../../src/Models/ProductModel.php';
   
    $database = new Database();
    $db = $database->getConnection();
    $product = new ProductModel($db);

    // Validación de campos obligatorios
    if (!isset($data->nombre) || !isset($data->marca) || 
        !isset($data->precio) || !isset($data->cantidad_stock) || 
        !isset($data->ubicacion)) {
        error_log("Faltan campos requeridos en los datos");
        throw new Exception("Faltan datos requeridos");
    }

    // Log de los datos recibidos
    error_log("Datos a procesar: " . print_r($data, true));
   
    $product->nombre = $data->nombre;
    $product->marca = $data->marca;
    $product->precio = $data->precio;
    $product->cantidad_stock = $data->cantidad_stock;
    $product->ubicacion = $data->ubicacion;
   
    if ($product->create()) {
        http_response_code(201);
        echo json_encode([
            "status" => "success",
            "message" => "Producto creado exitosamente"
        ]);
    } else {
        throw new Exception("Error al crear el producto");
    }
} catch (Exception $e) {
    error_log("Error en create.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}