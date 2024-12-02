<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Methods: PUT, POST');
header('Access-Control-Max-Age: 3600');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

try {
    $input = file_get_contents('php://input');
    $data = json_decode($input);
    
    // Aquí cambiamos la validación para usar supply_id
    if (!$data || !isset($data->article_id) || !isset($data->quantity) || !isset($data->supply_id)) {
        throw new Exception('Datos incompletos');
    }
  
    require_once '../../../config/database.php';
    include_once '../../src/Models/SuppliesModel.php';
    
    $database = new Database();
    $db = $database->getConnection();
    $supplies = new SuppliesModel($db);
    
    // Asignamos usando el nuevo nombre del campo
    $supplies->id = $data->supply_id;
    $supplies->article_id = $data->article_id;
    $supplies->quantity = $data->quantity;
    
    if ($supplies->update()) {
        echo json_encode(array(
            "status" => "success",
            "message" => "Suministro actualizado exitosamente"
        ));
    } else {
        throw new Exception("Error al actualizar el suministro");
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(array(
        "status" => "error",
        "message" => $e->getMessage()
    ));
}
?>