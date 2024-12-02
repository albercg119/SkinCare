<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');

try {
    // Debug: Verificar el directorio actual y las rutas absolutas
    $currentDir = __DIR__;
    $databasePath = $currentDir . '/../../../config/database.php';
    $modelPath = $currentDir . '/../../../api/src/Models/SuppliesModel.php';
    
    // Debug logs con más detalle
    error_log("Current directory: " . $currentDir);
    error_log("Full database path: " . $databasePath);
    error_log("Full model path: " . $modelPath);
    error_log("Database file exists: " . (file_exists($databasePath) ? 'yes' : 'no'));
    error_log("Model file exists: " . (file_exists($modelPath) ? 'yes' : 'no'));
    
    require_once $databasePath;
    require_once $modelPath;
    
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        throw new Exception("No se pudo establecer la conexión a la base de datos");
    }
    
    $supplies = new SuppliesModel($db);
    $stmt = $supplies->readWithArticleInfo();
    
    if (!$stmt) {
        throw new Exception("Error al ejecutar la consulta de suministros");
    }
    
    $result = array();
    $result["status"] = "success";
    $result["data"] = array();
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        array_push($result["data"], array(
            "id" => $row['id'],
            "article_id" => $row['article_id'],
            "article_name" => $row['article_name'],
            "quantity" => $row['quantity'],
            "supply_date" => $row['supply_date']
        ));
    }
    
    echo json_encode($result);
    
} catch (Exception $e) {
    error_log("Error detallado en read.php: " . $e->getMessage());
    error_log("Archivo: " . $e->getFile());
    error_log("Línea: " . $e->getLine());
    error_log("Stack trace: " . $e->getTraceAsString());
    
    http_response_code(500);
    echo json_encode(array(
        "status" => "error",
        "message" => $e->getMessage(),
        "file" => basename($e->getFile()),
        "line" => $e->getLine(),
        "debug_info" => [
            "current_dir" => $currentDir,
            "database_path" => $databasePath,
            "model_path" => $modelPath
        ]
    ));
}