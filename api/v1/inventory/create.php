<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Max-Age: 3600');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

try {
    $input = file_get_contents('php://input');
    $data = json_decode($input);
    
    if (!$data || !isset($data->id_producto) || !isset($data->ubicacion_tienda)) {
        throw new Exception('Datos incompletos');
    }

    require_once '../../../config/database.php';
    require_once '../../src/Models/InventoryModel.php';
    
    $database = new Database();
    $db = $database->getConnection();
    $inventory = new InventoryModel($db);
    
    $inventory->id_producto = $data->id_producto;
    $inventory->ubicacion_tienda = $data->ubicacion_tienda;
    
    if ($inventory->create()) {
        http_response_code(201);
        echo json_encode(array(
            "status" => "success",
            "message" => "Registro creado exitosamente"
        ));
    } else {
        throw new Exception("Error al crear el registro");
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(array(
        "status" => "error",
        "message" => $e->getMessage()
    ));
}