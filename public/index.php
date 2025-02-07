<?php

use Core\Migration\Migrator;

require_once __DIR__ . '/../src/Core/Autoloader.php';

\Core\Autoloader::register();
\Core\Autoloader::addPath(__DIR__ . '/../src/');

session_start();

$router = require_once __DIR__ . '/../src/config/routes.php';
$router->dispatch();

// $migrationsPath = __DIR__ . "/../sql/migrations";

// $migrator = new Migrator($pdo, $migrationsPath);

// Migrator::migrate();