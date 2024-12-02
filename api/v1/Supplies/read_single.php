<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');

try {
    require_once '../../../config/database.php';
    require_once '../../src/Models/SuppliesModel.php';
    
    $database = new Database();
    $db = $database->getConnection();
    
    $supply = new SuppliesModel($db);
    
    // Validar el ID recibido
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        throw new Exception('ID del suministro no proporcionado');
    }
    
    $supply->id = intval($_GET['id']);
    
    // Obtener los datos del suministro
    $stmt = $supply->readOne();
    
    if ($stmt) {
        $supply_arr = array(
            "status" => "success",
            "data" => array(
                "id" => $supply->id,
                "article_id" => $supply->article_id,
                "quantity" => $supply->quantity,
                "supply_date" => $supply->supply_date
            )
        );
        
        // Asegurar que no haya salida antes
        if (ob_get_length()) ob_clean();
        
        http_response_code(200);
        echo json_encode($supply_arr);
    } else {
        throw new Exception("No se encontró el suministro");
    }
    
} catch(Exception $e) {
    if (ob_get_length()) ob_clean();
    http_response_code(404);
    echo json_encode(array(
        "status" => "error",
        "message" => $e->getMessage()
    ));
}
?>