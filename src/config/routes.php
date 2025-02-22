<?php

use Core\Router;

$router = new Router();


$router->addRoute('GET', '/', [\Controllers\HomeController::class, 'index']);
$router->addRoute('GET', '/tag', [\Controllers\HomeController::class, 'index']);
$router->addRoute('GET', '/tag/{id:\d+}', [\Controllers\HomeController::class, 'index'])
;$router->addRoute('GET', '/search', [\Controllers\HomeController::class, 'index']);
$router->addRoute('GET', '/search/{query:[^/]+}', [\Controllers\HomeController::class, 'index']);


$router->addRoute('GET', '/product/{id:\d+}', [\Controllers\ProductController::class, 'show']);

$router->addRoute('GET', '/order/create/{id:\d+}', [\Controllers\OrderController::class, 'create']);
$router->addRoute('POST', '/order/submit', [\Controllers\OrderController::class, 'store']);
$router->addRoute('GET', '/order/success', [\Controllers\OrderController::class, 'success']);
$router->addRoute('GET', '/order/checkout-cart', [\Controllers\OrderController::class, 'createCartOrder']);
$router->addRoute('POST', '/order/submit-cart', [\Controllers\OrderController::class, 'storeCartOrder']);


$router->addRoute('GET', '/user/login', [\Controllers\UserController::class, 'index']);
$router->addRoute('POST', '/user/login', [\Controllers\UserController::class, 'authenticate']);
$router->addRoute('GET', '/user/register', [\Controllers\UserController::class, 'register']);
$router->addRoute('POST', '/user/register', [\Controllers\UserController::class, 'store']);
$router->addRoute('GET', '/user/profile', [\Controllers\UserProfileController::class, 'profile']);
$router->addRoute('POST', '/user/update', [\Controllers\UserProfileController::class, 'update']);
$router->addRoute('GET', '/user/logout', [\Controllers\UserController::class, 'logout']);
$router->addRoute('POST', '/user/update-avatar', [\Controllers\UserProfileController::class, 'updateAvatar']);

$router->addRoute('GET', '/cart', [\Controllers\CartController::class, 'index']);
$router->addRoute('POST', '/cart/add', [\Controllers\CartController::class, 'add']);
$router->addRoute('POST', '/cart/update', [\Controllers\CartController::class, 'update']);
$router->addRoute('POST', '/cart/remove', [\Controllers\CartController::class, 'remove']);
$router->addRoute('GET', '/cart/checkout', [\Controllers\CartController::class, 'checkout']);
$router->addRoute('POST', '/cart/checkout', [\Controllers\CartController::class, 'processCheckout']);

$router->addRoute('GET', '/admin/login', [\Controllers\Admin\AdminController::class, 'login']);
$router->addRoute('POST', '/admin/login', [\Controllers\Admin\AdminController::class, 'authenticate']);
$router->addRoute('GET', '/admin/logout', [\Controllers\Admin\AdminController::class, 'logout']);
//$router->addRoute('GET', '/admin', [\Controllers\Admin\AdminController::class, 'index']);

$router->addRoute('GET', '/admin', function()
	{
		header('Location: /admin/products');
		exit;
	}
);

$router->addRoute('GET', '/admin/products', [\Controllers\Admin\ProductsAdminController::class, 'index']);
$router->addRoute('POST', '/admin/products/process', [\Controllers\Admin\ProductsAdminController::class, 'process']);
$router->addRoute('GET', '/admin/products/search/{query:[^/]+}', [\Controllers\Admin\ProductsAdminController::class, 'index']);
$router->addRoute('GET', '/admin/products/edit/{id:\d+}', [\Controllers\Admin\ProductDetailAdminController::class, 'edit']);
$router->addRoute('POST', '/admin/products/update/{id:\d+}', [\Controllers\Admin\ProductDetailAdminController::class, 'update']);
$router->addRoute('GET', '/admin/products/create', [\Controllers\Admin\ProductCreateController::class, 'create']);
$router->addRoute('POST', '/admin/products/create', [\Controllers\Admin\ProductCreateController::class, 'store']);

$router->addRoute('GET', '/admin/orders', [\Controllers\Admin\OrdersAdminController::class, 'index']);
$router->addRoute('POST', '/admin/orders', [\Controllers\Admin\OrdersAdminController::class, 'handlePost']);
$router->addRoute('GET', '/admin/orders/{id:\d+}', [\Controllers\Admin\OrderDetailAdminController::class, 'show']);

$router->addRoute('GET', '/favorites', [\Controllers\FavoriteController::class, 'index']);
$router->addRoute('POST', '/favorites/add/{id:\d+}', [\Controllers\FavoriteController::class, 'add']);
$router->addRoute('POST', '/favorites/remove/{id:\d+}', [\Controllers\FavoriteController::class, 'remove']);
$router->addRoute('POST', '/favorites/toggle', [\Controllers\FavoriteController::class, 'toggle']);
$router->addRoute('GET', '/favorites/data', [\Controllers\FavoriteController::class, 'data']);



return $router;