<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Methods: POST, PUT, OPTIONS');
header('Access-Control-Max-Age: 3600');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include_once '../../../config/database.php';
include_once '../../src/Models/OrderModel.php';

use Models\OrderModel;

try {
    // Get posted data
    $data = json_decode(file_get_contents("php://input"), true);
    
    // Validate input
    if (!isset($data['id']) || !isset($data['estado_pedido'])) {
        throw new Exception("Datos incompletos");
    }

    // Create database connection
    $database = new Database();
    $db = $database->getConnection();
    
    // Initialize order model
    $order = new OrderModel($db);
    
    // Update the order
    if ($order->update($data['id'], $data)) {
        http_response_code(200);
        echo json_encode([
            "status" => "success",
            "message" => "Pedido actualizado exitosamente"
        ]);
    } else {
        throw new Exception("No se pudo actualizar el pedido");
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}