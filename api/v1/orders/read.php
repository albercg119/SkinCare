<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Content-Type: application/json; charset=UTF-8');

require_once '../../../config/database.php';
require_once '../../src/Models/OrderModel.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    $order = new \Models\OrderModel($db);
    
    // Obtener filtros si existen
    $filters = [];
    if (isset($_GET['estado'])) $filters['estado'] = $_GET['estado'];
    if (isset($_GET['fecha'])) $filters['fecha'] = $_GET['fecha'];

    $stmt = $order->read($filters);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'data' => array_map(function($row) {
            // Asegurarse de que los valores sean del tipo correcto
            return [
                'id' => (int)$row['id'],
                'fecha_pedido' => $row['fecha_pedido'],
                'estado_pedido' => $row['estado_pedido'],
                'id_proveedor' => (int)$row['id_proveedor'],
                'total' => (float)$row['total']
            ];
        }, $orders)
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}