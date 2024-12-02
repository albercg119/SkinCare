// details.php
<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once '../../config/Database.php';
include_once '../../models/SuppliesModel.php';

try {
    $database = new Database();
    $db = $database->connect();

    $supplies = new SuppliesModel($db);
    
    if(!isset($_GET['id'])) {
        throw new Exception('ID no proporcionado');
    }

    $result = $supplies->readSingle($_GET['id']);
    $row = $result->fetch(PDO::FETCH_ASSOC);

    if($row) {
        echo json_encode([
            'status' => 'success',
            'data' => $row
        ]);
    } else {
        throw new Exception('Suministro no encontrado');
    }
} catch(Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}