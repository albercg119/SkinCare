<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');

include_once '../../../config/database.php';
include_once '../../src/Models/SupplierModel.php';

$database = new Database();
$db = $database->getConnection();

$supplier = new SupplierModel($db);

$supplier->id = isset($_GET['id']) ? $_GET['id'] : die();

if($supplier->readOne()) {
    $supplier_arr = array(
        "id" =>  $supplier->id,
        "nombre" => $supplier->nombre,
        "telefono" => $supplier->telefono,
        "correo_electronico" => $supplier->correo_electronico
    );

    http_response_code(200);
    echo json_encode(array(
        "status" => "success",
        "data" => $supplier_arr
    ));
} else {
    http_response_code(404);
    echo json_encode(array(
        "status" => "error",
        "message" => "Proveedor no encontrado."
    ));
}