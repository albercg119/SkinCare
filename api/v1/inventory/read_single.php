<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');

try {
    require_once '../../../config/database.php';
    require_once '../../src/Models/InventoryModel.php';
    
    if (!isset($_GET['id'])) {
        throw new Exception('ID no proporcionado');
    }
    
    $database = new Database();
    $db = $database->getConnection();
    
    $inventory = new InventoryModel($db);
    $inventory->id_inventario = $_GET['id'];
    
    if ($inventory->readOne()) {
        echo json_encode(array(
            "status" => "success",
            "data" => array(
                "id_inventario" => $inventory->id_inventario,
                "id_producto" => $inventory->id_producto,
                "ubicacion_tienda" => $inventory->ubicacion_tienda,
                "fecha_ultima_actualizacion" => $inventory->fecha_ultima_actualizacion
            )
        ));
    } else {
        throw new Exception('Registro no encontrado');
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(array(
        "status" => "error",
        "message" => $e->getMessage()
    ));
}