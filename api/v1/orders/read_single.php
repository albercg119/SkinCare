<?php
// Iniciamos capturando información sobre el ambiente
$debugLogs = [];
$debugLogs[] = "Inicio del script - " . date('Y-m-d H:i:s');
$debugLogs[] = "PHP Version: " . phpversion();

// Control del buffer de salida
if (ob_get_level()) ob_end_clean();
ob_start();
$debugLogs[] = "Buffer de salida limpiado e iniciado";

// Configuración de errores para desarrollo
ini_set('display_errors', '1');
error_reporting(E_ALL);
$debugLogs[] = "Configuración de errores establecida para desarrollo";

// Headers necesarios
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Max-Age: 3600');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');
$debugLogs[] = "Headers HTTP establecidos";

// Manejo de OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    $debugLogs[] = "Petición OPTIONS detectada - respondiendo con 200";
    http_response_code(200);
    exit();
}

try {
    // Registro de la petición recibida
    $debugLogs[] = "Método de la petición: " . $_SERVER['REQUEST_METHOD'];
    $debugLogs[] = "GET params recibidos: " . print_r($_GET, true);

    // Carga de dependencias
    require_once '../../../config/database.php';
    require_once '../../src/Models/OrderModel.php';
    $debugLogs[] = "Dependencias cargadas correctamente";

    // Validación del ID
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        throw new Exception('ID del pedido no proporcionado');
    }
    $debugLogs[] = "ID validado: " . $_GET['id'];

    // Conexión a la base de datos
    $database = new Database();
    $db = $database->getConnection();
    if ($db) {
        $debugLogs[] = "Conexión a base de datos establecida exitosamente";
    } else {
        $debugLogs[] = "Error al establecer conexión a base de datos";
        throw new Exception('Error de conexión a base de datos');
    }

    // Creación del modelo
    $order = new \Models\OrderModel($db);
    $debugLogs[] = "Modelo OrderModel creado correctamente";

    // Ejecución de la consulta
    $debugLogs[] = "Ejecutando consulta para ID: " . $_GET['id'];
    $stmt = $order->readSingle($_GET['id']);
    $debugLogs[] = "Consulta ejecutada - Verificando resultado";

    // Obtención de resultados
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $debugLogs[] = "Datos obtenidos: " . print_r($row, true);

    // Limpieza del buffer antes de la respuesta
    if (ob_get_length()) ob_clean();

    if ($row) {
        // Preparación de la respuesta
        $response = [
            "status" => "success",
            "data" => [
                "id" => (int)$row['id'],
                "id_proveedor" => (int)$row['id_proveedor'],
                "estado_pedido" => $row['estado_pedido']
            ],
            "debug_logs" => $debugLogs // Incluimos los logs en la respuesta
        ];

        $debugLogs[] = "Respuesta preparada: " . json_encode($response);

        http_response_code(200);
        echo json_encode($response);
    } else {
        http_response_code(404);
        echo json_encode([
            "status" => "error",
            "message" => "Pedido no encontrado",
            "debug_logs" => $debugLogs
        ]);
    }
} catch(Exception $e) {
    // En caso de error, incluimos toda la información de diagnóstico
    $debugLogs[] = "ERROR CAPTURADO: " . $e->getMessage();
    $debugLogs[] = "Archivo: " . $e->getFile();
    $debugLogs[] = "Línea: " . $e->getLine();
    $debugLogs[] = "Trace: " . $e->getTraceAsString();

    if (ob_get_length()) ob_clean();

    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage(),
        "debug_logs" => $debugLogs
    ]);
}

// Finalizamos la ejecución
ob_end_flush();
exit();
?>