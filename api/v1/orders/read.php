<?php
try {
    // Verificar si el archivo existe
    if (!file_exists('../../../config/database.php')) {
        throw new Exception('El archivo database.php no existe');
    }
    require_once '../../../config/database.php';
    
    // Verificar si el archivo del modelo existe
    if (!file_exists('../../../api/src/Models/OrderModel.php')) {
        throw new Exception('El archivo OrderModel.php no existe');
    }

    // Registrar el autoloader
    spl_autoload_register(function ($class) {
        $base_dir = __DIR__ . '/../../../api/src/';
        $file = $base_dir . str_replace('\\', '/', $class) . '.php';
        if (file_exists($file)) {
            require $file;
        } else {
            throw new Exception("No se puede cargar la clase: $class ($file)");
        }
    });

    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        throw new Exception('No se pudo establecer la conexión a la base de datos');
    }

    $order = new \Models\OrderModel($db);
    
    // Recoger los filtros de la URL si existen
    $filters = [];
    if (isset($_GET['estado']) && !empty($_GET['estado'])) {
        $filters['estado'] = $_GET['estado'];
    }
    if (isset($_GET['fecha']) && !empty($_GET['fecha'])) {
        $filters['fecha'] = $_GET['fecha'];
    }
    
    // Usar los filtros solo si existen
    $stmt = empty($filters) ? $order->read() : $order->read($filters);
    
    if (!$stmt) {
        throw new Exception('Error al ejecutar la consulta');
    }

    $orders_arr = array();
    $orders_arr["status"] = "success";
    $orders_arr["data"] = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $order_item = array(
            "id" => $row['id'],
            "fecha_pedido" => $row['fecha_pedido'],
            "id_proveedor" => $row['id_proveedor'],
            "estado_pedido" => $row['estado_pedido']
        );
        array_push($orders_arr["data"], $order_item);
    }

    echo json_encode($orders_arr);
    
} catch (Exception $e) {  
    // Devolver respuesta de error con más información
    http_response_code(500);
    echo json_encode(array(
        "status" => "error",
        "message" => $e->getMessage(),
        "file" => $e->getFile(),
        "line" => $e->getLine(),
        "trace" => $e->getTraceAsString()
    ));
}