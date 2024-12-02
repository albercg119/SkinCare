<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Max-Age: 3600');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    // Obtener el contenido raw y decodificarlo
    $input = file_get_contents('php://input');
    
    // Verificar si hay BOM y removerlo
    $bom = pack('H*', 'EFBBBF');
    $input = preg_replace("/^$bom/", '', $input);
    
    // Log del input recibido
    error_log("Input recibido: " . $input);
    
    $data = json_decode($input, true, 512, JSON_THROW_ON_ERROR);
    
    require_once '../../../config/database.php';
    require_once '../../src/Models/OrderModel.php';

    $database = new Database();
    $db = $database->getConnection();
    $order = new \Models\OrderModel($db);

    // Validar datos requeridos
    if (!isset($data['id_proveedor']) || !isset($data['estado_pedido']) || !isset($data['productos'])) {
        throw new Exception("Faltan datos requeridos para crear el pedido");
    }

    if ($order->create($data)) {
        http_response_code(201);
        echo json_encode([
            "status" => "success",
            "message" => "Pedido creado exitosamente"
        ]);
    } else {
        throw new Exception("Error al crear el pedido");
    }
    
} catch (JsonException $e) {
    error_log("Error JSON: " . $e->getMessage());
    error_log("Input que causó el error: " . $input);
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "message" => "Error al decodificar JSON: " . $e->getMessage()
    ]);
} catch (Exception $e) {
    error_log("Error en create.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
?>