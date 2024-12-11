<?php
require_once '../config/config.php';
require_once '../config/database.php';

session_start();

// Función para verificar si un archivo existe
function view_exists($view) {
    return file_exists(__DIR__ . '/views/' . $view . '.php');
}

// Función para verificar si el usuario está autenticado
function is_authenticated() {
    return isset($_SESSION['user_id']);
}

// Función para redireccionar a login si no está autenticado
function require_auth() {
    if (!is_authenticated()) {
        header('Location: /SkinCare/public/login');
        exit();
    }
}

// Manejo básico de rutas
$request = $_SERVER['REQUEST_URI'];
$basePath = '/SkinCare/public';
$request = str_replace($basePath, '', $request);

// Router simple
try {
    switch ($request) {
        // Rutas de autenticación
        case '/login':
            require __DIR__ . '/views/auth/login.php';
            break;
            
        case '/register':
            require __DIR__ . '/views/auth/register.php';
            break;
            
        case '/logout':
            session_destroy();
            header('Location: /SkinCare/public/login');
            exit();
            break;

        // Rutas existentes - Agregamos require_auth() para protegerlas
        case '':
        case '/':
            require_auth();
            require __DIR__ . '/views/layouts/header.php';
            require __DIR__ . '/views/home.php';
            require __DIR__ . '/views/layouts/footer.php';
            break;
            
        case '/products':
            require_auth();
            require __DIR__ . '/views/layouts/header.php';
            require __DIR__ . '/views/products/index.php';
            require __DIR__ . '/views/layouts/footer.php';
            break;
            
        case '/suppliers':
            require_auth();
            require __DIR__ . '/views/layouts/header.php';
            require __DIR__ . '/views/suppliers/index.php';
            require __DIR__ . '/views/layouts/footer.php';
            break;
            
        case '/orders':
            require_auth();
            require __DIR__ . '/views/layouts/header.php';
            require __DIR__ . '/views/orders/index.php';
            require __DIR__ . '/views/layouts/footer.php';
            break;
            
        case '/inventory':
            require_auth();
            require __DIR__ . '/views/layouts/header.php';
            require __DIR__ . '/views/inventory/index.php';
            require __DIR__ . '/views/layouts/footer.php';
            break;
                
        case '/supplies':
            require_auth();
            require __DIR__ . '/views/layouts/header.php';
            require __DIR__ . '/views/supplies/index.php';
            require __DIR__ . '/views/layouts/footer.php';
            break;
            
        default:
            http_response_code(404);
            require __DIR__ . '/views/layouts/header.php';
            require __DIR__ . '/views/404.php';
            require __DIR__ . '/views/layouts/footer.php';
            break;
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    http_response_code(500);
    echo "Ha ocurrido un error en el servidor";
}