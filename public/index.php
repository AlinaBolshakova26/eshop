<?php


require_once __DIR__ . '/../src/Core/Autoloader.php';


\Core\Autoloader::register();


\Core\Autoloader::addPath(__DIR__ . '/../src/');


$router = require_once __DIR__ . '/../src/config/routes.php';


$router->dispatch();
