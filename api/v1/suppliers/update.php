<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Methods: PUT');
header('Access-Control-Max-Age: 3600');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

include_once '../../../config/database.php';
include_once '../../src/Models/SupplierModel.php';

$database = new Database();
$db = $database->getConnection();

$supplier = new SupplierModel($db);

$data = json_decode(file_get_contents("php://input"));

if(!empty($data->id) && !empty($data->nombre) && !empty($data->telefono) && !empty($data->correo_electronico)) {
    $supplier->id = $data->id;
    $supplier->nombre = $data->nombre;
    $supplier->telefono = $data->telefono;
    $supplier->correo_electronico = $data->correo_electronico;
    
    if($supplier->update()) {
        http_response_code(200);
        echo json_encode(array(
            "status" => "success",
            "message" => "Proveedor actualizado."
        ));
    } else {
        http_response_code(503);
        echo json_encode(array(
            "status" => "error",
            "message" => "No se pudo actualizar el proveedor."
        ));
    }
} else {
    http_response_code(400);
    echo json_encode(array(
        "status" => "error",
        "message" => "No se puede actualizar el proveedor. Datos incompletos."
    ));
}