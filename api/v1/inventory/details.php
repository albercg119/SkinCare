// details.php
<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

require_once '../../../config/database.php';
    require_once '../../src/Models/InventoryModel.php';

try {
    $database = new Database();
    $db = $database->connect();

    $inventory = new InventoryModel($db);
    
    if(!isset($_GET['id'])) {
        throw new Exception('ID no proporcionado');
    }

    $result = $inventory->readSingle($_GET['id']);
    $row = $result->fetch(PDO::FETCH_ASSOC);

    if($row) {
        echo json_encode([
            'status' => 'success',
            'data' => $row
        ]);
    } else {
        throw new Exception('Registro no encontrado');
    }
} catch(Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

