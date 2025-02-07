<?php

use Core\Migration\Migrator;

require_once __DIR__ . '/../src/Core/Autoloader.php';


\Core\Autoloader::register();


\Core\Autoloader::addPath(__DIR__ . '/../src/');


$router = require_once __DIR__ . '/../src/config/routes.php';


$router->dispatch();

// $pdo = require_once __DIR__ . "/../src/config/database.php";
// $migrationsPath = __DIR__ . "/../sql/migrations";

// $migrator = new Migrator($pdo, $migrationsPath);

// Migrator::migrate();