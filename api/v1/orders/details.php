<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../src/Models/OrderModel.php';

try {
    if (!isset($_GET['id'])) {
        throw new Exception('ID de pedido no proporcionado');
    }

    $database = new Database();
    $db = $database->getConnection();
    $order = new \Models\OrderModel($db);
    
    $result = $order->getDetails($_GET['id']);
    
    // Debug - Log la respuesta
    error_log('Respuesta: ' . print_r($result, true));
    
    echo json_encode([
        'status' => 'success',
        'data' => [
            'detalles' => $result['data']['detalles'] ?? [],
            'total' => $result['data']['total'] ?? '0.00'
        ]
    ]);

} catch (Exception $e) {
    error_log("Error en details.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}