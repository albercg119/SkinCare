<?php
ob_start();
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Methods: PUT, POST, OPTIONS');
header('Access-Control-Max-Age: 3600');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    $input = file_get_contents('php://input');
    error_log("Input raw recibido: " . $input);
   
    $data = json_decode($input);
   
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Error al decodificar JSON: ' . json_last_error_msg());
    }

    require_once '../../../config/database.php';
    require_once '../../src/Models/ProductModel.php';
   
    $database = new Database();
    $db = $database->getConnection();
    $product = new ProductModel($db);

    if (!isset($data->nombre) || !isset($data->marca) ||
        !isset($data->precio) || !isset($data->cantidad_stock) ||
        !isset($data->ubicacion)) {
        throw new Exception("Faltan datos requeridos");
    }

    $id = isset($_GET['id']) ? $_GET['id'] : (isset($data->id) ? $data->id : null);
   
    if (!$id) {
        throw new Exception("ID del producto no proporcionado");
    }

    $product->nombre = $data->nombre;
    $product->marca = $data->marca;
    $product->precio = $data->precio;
    $product->cantidad_stock = $data->cantidad_stock;
    $product->ubicacion = $data->ubicacion;

    if ($product->update($id)) {
        http_response_code(200);
        echo json_encode([
            "status" => "success",
            "message" => "Producto actualizado exitosamente"
        ]);
    } else {
        throw new Exception("Error al actualizar el producto");
    }
} catch (Exception $e) {
    error_log("Error en update.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
ob_end_flush();