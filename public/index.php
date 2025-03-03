<?php

use Core\Migration\Migrator;
use Core\Autoloader;
use Core\View;
use Core\Exceptions\AppException;

require_once __DIR__ . '/../src/Core/Autoloader.php';
require_once __DIR__ . '/../src/config/helpers.php';

Autoloader::register();
Autoloader::addPath(ROOT . '/');

session_start();

$router = require_once ROOT . '/config/routes.php';

try {
    $router->dispatch();
} catch (\Throwable $e) {
    $statusCode = $e instanceof AppException ? $e->getCode() : 500;
    $userMessage = $e instanceof AppException ? $e->getUserMessage() : 'Произошла ошибка';
    
    http_response_code($statusCode);
    
    echo View::make
    (
        'error', 
        [
            'error' => $userMessage,
            'code' => $statusCode
        ]
    );
    
}


// $migrationsPath = ROOT . "/sql/migrations";

// $migrator = new Migrator($pdo, $migrationsPath);

// Migrator::migrate();
