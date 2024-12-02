<?php
// Agregar control de buffer al inicio
ob_start();

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Max-Age: 3600');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    require_once '../../../config/database.php';
    require_once '../../src/Models/ProductModel.php';
   
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        throw new Exception('ID del producto no proporcionado');
    }
   
    $database = new Database();
    $db = $database->getConnection();
   
    if (!$db) {
        throw new Exception('Error de conexión a la base de datos');
    }
   
    $product = new ProductModel($db);
   
    $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
    if ($id === false || $id <= 0) {
        throw new Exception('ID del producto inválido');
    }
   
    $product->id = $id;
   
    if (!$product->readOne()) {
        throw new Exception('Error al leer el producto');
    }
   
    if ($product->nombre !== null) {
        $productData = [
            "id" => (int)$product->id,
            "nombre" => htmlspecialchars(strip_tags($product->nombre)),
            "marca" => htmlspecialchars(strip_tags($product->marca)),
            "precio" => (float)$product->precio,
            "cantidad_stock" => (int)$product->cantidad_stock,
            "ubicacion" => $product->ubicacion
        ];
       
        http_response_code(200);
        echo json_encode([
            "status" => "success",
            "data" => $productData
        ]);
    } else {
        http_response_code(404);
        echo json_encode([
            "status" => "error",
            "message" => "Producto no encontrado"
        ]);
    }
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage(),
        "file" => basename($e->getFile()),
        "line" => $e->getLine()
    ]);
}

// Agregar limpieza del buffer al final
ob_end_flush();