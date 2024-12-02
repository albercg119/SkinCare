<?php
// Definición de constantes globales
define('BASE_URL', 'http://localhost/skincare-management');
define('API_URL', BASE_URL . '/api/v1');

// Configuración de zona horaria
date_default_timezone_set('America/Bogota');

// Configuración de errores en desarrollo
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Funciones auxiliares globales
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Función para manejar respuestas JSON
function json_response($status, $message, $data = null) {
    header('Content-Type: application/json');
    echo json_encode([
        'status' => $status,
        'message' => $message,
        'data' => $data
    ]);
}

// Función para verificar si es una petición AJAX
function is_ajax_request() {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}