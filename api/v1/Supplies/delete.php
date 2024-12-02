<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Max-Age: 3600');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

try {
    $input = file_get_contents('php://input');
    $data = json_decode($input);
    
    if (!$data || !isset($data->id)) {
        throw new Exception('ID no proporcionado');
    }

    // Corregir las rutas de los require
    require_once '../../../config/database.php';
    require_once '../../../api/src/Models/SuppliesModel.php';
    
    $database = new Database();
    $db = $database->getConnection();
    $supplies = new SuppliesModel($db);
    
    $supplies->id = $data->id;
    
    if ($supplies->delete()) {
        echo json_encode(array(
            "status" => "success",
            "message" => "Suministro eliminado exitosamente"
        ));
    } else {
        throw new Exception("Error al eliminar el suministro");
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(array(
        "status" => "error",
        "message" => $e->getMessage()
    ));
}