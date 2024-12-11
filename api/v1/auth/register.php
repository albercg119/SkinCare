<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Max-Age: 3600');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

try {
    $input = file_get_contents('php://input');
    $data = json_decode($input);
    
    if (!$data || !isset($data->username) || !isset($data->email) || !isset($data->password)) {
        throw new Exception('Datos de registro incompletos');
    }

    require_once __DIR__ . '/../../../config/database.php';
    require_once __DIR__ . '/../../src/Models/UserModel.php';
    
    $database = new Database();
    $db = $database->getConnection();
    $user = new Models\UserModel($db);
    
    // Verificar si el email ya existe
    if ($user->emailExists($data->email)) {
        throw new Exception('El email ya está registrado');
    }
    
    $userData = [
        'username' => $data->username,
        'email' => $data->email,
        'password' => $data->password  // Guardamos la contraseña sin hash
    ];
    
    if ($user->create($userData)) {
        http_response_code(201);
        echo json_encode([
            "status" => "success",
            "message" => "Usuario registrado exitosamente"
        ]);
    } else {
        throw new Exception("Error al registrar el usuario");
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}