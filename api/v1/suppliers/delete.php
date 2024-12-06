<?php
// Aseguramos que no haya salida antes de los headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Max-Age: 3600');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

try {
    // Leemos y validamos la entrada
    $input = file_get_contents('php://input');
    $data = json_decode($input);
    
    if (!$data || !isset($data->id)) {
        throw new Exception('ID no proporcionado');
    }
    
    require_once '../../../config/database.php';
    require_once '../../src/Models/SupplierModel.php';
    
    $database = new Database();
    $db = $database->getConnection();
    $supplier = new SupplierModel($db);
    
    $supplier->id = $data->id;
    
    if ($supplier->delete()) {
        echo json_encode([
            "status" => "success",
            "message" => "Proveedor eliminado exitosamente"
        ]);
    } else {
        throw new Exception("Error al eliminar el proveedor");
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
// Es importante que no haya ningún espacio o línea después del cierre de PHP
?>