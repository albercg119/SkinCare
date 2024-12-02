<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Max-Age: 3600');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

try {
    $input = file_get_contents('php://input');
    $data = json_decode($input);
    
    if (!$data || !isset($data->id_inventario) || !isset($data->id_producto) || !isset($data->ubicacion_tienda)) {
        throw new Exception('Datos incompletos');
    }

    require_once '../../../config/database.php';
    require_once '../../src/Models/InventoryModel.php';
    
    $database = new Database();
    $db = $database->getConnection();
    $inventory = new InventoryModel($db);
    
    $inventory->id_inventario = $data->id_inventario;
    $inventory->id_producto = $data->id_producto;
    $inventory->ubicacion_tienda = $data->ubicacion_tienda;
    
    if ($inventory->update()) {
        echo json_encode(array(
            "status" => "success",
            "message" => "Registro actualizado exitosamente"
        ));
    } else {
        throw new Exception("Error al actualizar el registro");
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(array(
        "status" => "error",
        "message" => $e->getMessage()
    ));
}