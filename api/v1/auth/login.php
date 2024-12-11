<?php
// Asegurarnos de que no hay espacios ni líneas en blanco antes de <?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Activar el reporte de errores para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verificar el método de la petición
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    // Obtener y validar los datos de entrada
    $json = file_get_contents('php://input');
    if (!$json) {
        throw new Exception('No se recibieron datos');
    }

    $data = json_decode($json);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Error al decodificar JSON: ' . json_last_error_msg());
    }

    // Para depuración
    error_log('Datos recibidos: ' . print_r($data, true));

    // Validar campos requeridos
    if (empty($data->email) || empty($data->password)) {
        throw new Exception('Email y contraseña son requeridos');
    }

    // Incluir archivos necesarios
    require_once __DIR__ . '/../../../config/database.php';

    // Conectar a la base de datos
    $database = new Database();
    $db = $database->getConnection();

    // Consultar usuario
    $query = "SELECT * FROM usuario WHERE email = ? AND password = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$data->email, $data->password]);
    
    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];

        echo json_encode([
            'success' => true,
            'message' => 'Login exitoso',
            'user' => [
                'id' => $user['id'],
                'username' => $user['username']
            ]
        ]);
    } else {
        throw new Exception('Credenciales inválidas');
    }

} catch (Exception $e) {
    error_log('Error en login: ' . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
// No debe haber ningún código después de aquí