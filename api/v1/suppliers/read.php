<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

try {
    require_once '../../../config/database.php';
    require_once '../../src/Models/SupplierModel.php';

    $database = new Database();
    $db = $database->getConnection();
   
    $supplier = new SupplierModel($db);
   
    $stmt = $supplier->read();
    $suppliers_arr = array();
    $suppliers_arr["status"] = "success";
    $suppliers_arr["data"] = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $supplier_item = array(
            "id" => $row['id'],
            "nombre" => $row['nombre'],
            "telefono" => $row['telefono'],
            "correo_electronico" => $row['correo_electronico']
        );
        array_push($suppliers_arr["data"], $supplier_item);
    }

    echo json_encode($suppliers_arr);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(array(
        "status" => "error",
        "message" => $e->getMessage(),
        "file" => $e->getFile(),
        "line" => $e->getLine()
    ));
}