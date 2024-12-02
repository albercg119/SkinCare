<?php
//* Deshabilitar la salida del buffer *
ob_clean();

//* Asegurarnos que no haya salida antes de los headers *
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');

try {
    require_once '../../../config/database.php';
    require_once '../../src/Models/ProductModel.php';
    
    $database = new Database();
    $db = $database->getConnection();
   
    $product = new ProductModel($db);
   
    $stmt = $product->readWithInventory();
    $products_arr = array();
    $products_arr["status"] = "success";
    $products_arr["data"] = array();
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $product_item = array(
            "id_inventario" => $row['id_inventario'],
            "id_producto" => $row['id_producto'],
            "nombre" => $row['nombre'],
            "marca" => $row['marca'],
            "precio" => $row['precio'],
            "cantidad_stock" => $row['cantidad_stock'],
            "ubicacion" => $row['ubicacion']
        );
        array_push($products_arr["data"], $product_item);
    }
    
    // Asegurarnos que la salida sea limpia
    if (ob_get_length()) ob_clean();
    
    echo json_encode($products_arr);
} catch (Exception $e) {
    if (ob_get_length()) ob_clean();
    http_response_code(500);
    echo json_encode(array(
        "status" => "error",
        "message" => $e->getMessage()
    ));
}

//* Asegurarnos que no haya más salida después *
exit();