<?php

use Core\Router;


$router = new Router();


$router->addRoute('GET', '/', [\Controllers\HomeController::class, 'index']);

$router->addRoute('GET', '/product/{id:\d+}', [\Controllers\ProductController::class, 'show']);

$router->addRoute('GET', '/order/create/{id:\d+}', [\Controllers\OrderController::class, 'create']);
$router->addRoute('POST', '/order/submit', [\Controllers\OrderController::class, 'store']);

$router->addRoute('GET', '/order/success', [\Controllers\OrderController::class, 'success']);

$router->addRoute('GET', '/admin/login', [\Controllers\Admin\AdminController::class, 'login']);
$router->addRoute('POST', '/admin/login', [\Controllers\Admin\AdminController::class, 'authenticate']);
$router->addRoute('GET', '/admin/logout', [\Controllers\Admin\AdminController::class, 'logout']);

$router->addRoute('GET', '/admin', function () {
	header('Location: /admin/products');
	exit;
});
$router->addRoute('GET', '/admin/products', [\Controllers\Admin\ProductsAdminController::class, 'index']);
$router->addRoute('GET', '/admin/orders', [\Controllers\Admin\OrdersAdminController::class, 'index']);
$router->addRoute('POST', '/admin/orders', [\Controllers\Admin\OrdersAdminController::class, 'handlePost']);
// $router->addRoute('GET', '/admin/products/edit/(\d+)', [\Controllers\Admin\ProductAdminController::class, 'edit']);
// $router->addRoute('POST', '/admin/products/update/(\d+)', [\Controllers\Admin\ProductAdminController::class, 'update']);
// $router->addRoute('POST', '/admin/products/delete/(\d+)', [\Controllers\Admin\ProductAdminController::class, 'delete']);

return $router;