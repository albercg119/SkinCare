<?php
require_once '../config/config.php';
require_once '../config/database.php';

session_start();

// Función para verificar si un archivo existe
function view_exists($view) {
    return file_exists(__DIR__ . '/views/' . $view . '.php');
}

// Manejo básico de rutas
$request = $_SERVER['REQUEST_URI'];
$basePath = '/SkinCare/public';
$request = str_replace($basePath, '', $request);

// Router simple
try {
    switch ($request) {
        case '':
        case '/':
            require __DIR__ . '/views/layouts/header.php';
            require __DIR__ . '/views/home.php';
            require __DIR__ . '/views/layouts/footer.php';
            break;
            
        case '/products':
            require __DIR__ . '/views/layouts/header.php';
            require __DIR__ . '/views/products/index.php';
            require __DIR__ . '/views/layouts/footer.php';
            break;
            
        case '/suppliers':
            require __DIR__ . '/views/layouts/header.php';
            require __DIR__ . '/views/suppliers/index.php';
            require __DIR__ . '/views/layouts/footer.php';
            break;
            
        case '/orders':
            require __DIR__ . '/views/layouts/header.php';
            require __DIR__ . '/views/orders/index.php';
            require __DIR__ . '/views/layouts/footer.php';
            break;
            
        default:
            http_response_code(404);
            require __DIR__ . '/views/layouts/header.php';
            require __DIR__ . '/views/404.php';
            require __DIR__ . '/views/layouts/footer.php';
            break;

            case '/inventory':
                require __DIR__ . '/views/layouts/header.php';
                require __DIR__ . '/views/inventory/index.php';
                require __DIR__ . '/views/layouts/footer.php';
                break;
                
            case '/supplies':
                require __DIR__ . '/views/layouts/header.php';
                require __DIR__ . '/views/supplies/index.php';
                require __DIR__ . '/views/layouts/footer.php';
                break;
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    http_response_code(500);
    echo "Ha ocurrido un error en el servidor";
}