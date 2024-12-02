<?php
// create.php
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
    require_once '../../../config/database.php';
    require_once '../../src/Models/SupplierModel.php';
   
    $database = new Database();
    $db = $database->getConnection();
    $supplier = new SupplierModel($db);
   
    $input = file_get_contents('php://input');
    $data = json_decode($input);
   
    if (!$data) {
        throw new Exception('Error al decodificar JSON: ' . json_last_error_msg());
    }
   
    if (!isset($data->nombre) || !isset($data->telefono) || !isset($data->correo_electronico)) {
        throw new Exception("Faltan datos requeridos");
    }
   
    $supplier->nombre = $data->nombre;
    $supplier->telefono = $data->telefono;
    $supplier->correo_electronico = $data->correo_electronico;
   
    if ($supplier->create()) {
        http_response_code(201);
        echo json_encode([
            "status" => "success",
            "message" => "Proveedor creado exitosamente"
        ]);
    } else {
        throw new Exception("Error al crear el proveedor");
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
