<?php


use Core\Router;

$router = new Router();


$router->addRoute('GET', '/', [\Controllers\HomeController::class, 'index'], 'catalog-index');

$router->addRoute('GET', '/product/{id:\d+}', [\Controllers\ProductController::class, 'show'], 'product-show');

$router->addRoute('GET', '/order/create/{id:\d+}', [\Controllers\OrderController::class, 'create'], 'order.create');
$router->addRoute('POST', '/order/submit', [\Controllers\OrderController::class, 'store'], 'order.submit');
$router->addRoute('GET', '/order/success', [\Controllers\OrderController::class, 'success'], 'order.success');
$router->addRoute('GET', '/order/checkout-cart', [\Controllers\OrderController::class, 'createCartOrder'], 'order.checkout-cart');
$router->addRoute('POST', '/order/submit-cart', [\Controllers\OrderController::class, 'storeCartOrder'], 'order.submit-cart');

$router->addRoute('GET', '/user/login', [\Controllers\UserController::class, 'index'], 'user.login');
$router->addRoute('POST', '/user/login', [\Controllers\UserController::class, 'authenticate'], 'user.auth');
$router->addRoute('GET', '/user/register', [\Controllers\UserController::class, 'register'], 'user.register');
$router->addRoute('POST', '/user/register', [\Controllers\UserController::class, 'store'], 'user.register-store');
$router->addRoute('GET', '/user/profile', [\Controllers\UserProfileController::class, 'profile'], 'user.profile');
$router->addRoute('POST', '/user/update', [\Controllers\UserProfileController::class, 'update'], 'user.update');
$router->addRoute('GET', '/user/logout', [\Controllers\UserController::class, 'logout'], 'user.logout');
$router->addRoute('POST', '/user/update-avatar', [\Controllers\UserProfileController::class, 'updateAvatar'], 'user.update-avatar');

$router->addRoute('GET', '/cart', [\Controllers\CartController::class, 'index'], 'cart-index');
$router->addRoute('POST', '/cart/add', [\Controllers\CartController::class, 'add'], 'cart.add');
$router->addRoute('POST', '/cart/update', [\Controllers\CartController::class, 'update'], 'cart.update');
$router->addRoute('POST', '/cart/remove', [\Controllers\CartController::class, 'remove'], 'cart.remove');
$router->addRoute('GET', '/cart/checkout', [\Controllers\CartController::class, 'checkout'], 'cart.checkout');
$router->addRoute('POST', '/cart/checkout', [\Controllers\CartController::class, 'processCheckout'], 'cart.process-checkout');

$router->addRoute('POST', '/rating/create', [\Controllers\RatingController::class, 'create'], 'rating.create');

$router->addRoute('GET', '/admin/login', [\Controllers\Admin\AdminController::class, 'login'], 'admin.login');
$router->addRoute('POST', '/admin/login', [\Controllers\Admin\AdminController::class, 'authenticate'], 'admin.auth');
$router->addRoute('GET', '/admin/logout', [\Controllers\Admin\AdminController::class, 'logout'], 'admin.logout');
//$router->addRoute('GET', '/admin', [\Controllers\Admin\AdminController::class, 'index']);

$router->addRoute
('GET', '/admin', 
function()
	{
		header('Location: /admin/products');
		exit;
	}
);

$router->addRoute('GET', '/admin/products', [\Controllers\Admin\ProductsAdminController::class, 'index'], 'admin.products');
$router->addRoute('POST', '/admin/products/process', [\Controllers\Admin\ProductsAdminController::class, 'process'], 'admin.products.process');
$router->addRoute('GET', '/admin/products/search/{query:[^/]+}', [\Controllers\Admin\ProductsAdminController::class, 'index'], 'admin.products.search');
$router->addRoute('GET', '/admin/products/edit/{id:\d+}', [\Controllers\Admin\ProductDetailAdminController::class, 'edit'], 'admin.products.edit');
$router->addRoute('POST', '/admin/products/update/{id:\d+}', [\Controllers\Admin\ProductDetailAdminController::class, 'update'], 'admin.products.update');
$router->addRoute('GET', '/admin/products/create', [\Controllers\Admin\ProductCreateController::class, 'create'], 'admin.products.create');
$router->addRoute('POST', '/admin/products/create', [\Controllers\Admin\ProductCreateController::class, 'store'], 'admin.products.store');

$router->addRoute('GET', '/admin/orders', [\Controllers\Admin\OrdersAdminController::class, 'index'], 'admin.orders-index');
$router->addRoute('POST', '/admin/orders', [\Controllers\Admin\OrdersAdminController::class, 'handlePost'], 'admin.orders-handlePost');
$router->addRoute('GET', '/admin/orders/{id:\d+}', [\Controllers\Admin\OrderDetailAdminController::class, 'show'], 'admin.orders-show');

$router->addRoute('GET', '/favorites', [\Controllers\FavoriteController::class, 'index'], 'favorites-index');
$router->addRoute('POST', '/favorites/add/{id:\d+}', [\Controllers\FavoriteController::class, 'add'], 'favorites.add');
$router->addRoute('POST', '/favorites/remove/{id:\d+}', [\Controllers\FavoriteController::class, 'remove'], 'favorites.remove');
$router->addRoute('POST', '/favorites/toggle', [\Controllers\FavoriteController::class, 'toggle'], 'favorites.toggle');
$router->addRoute('GET', '/favorites/data', [\Controllers\FavoriteController::class, 'data'], 'favorites.data');

$router->addRoute('GET', '/admin/tags', [\Controllers\Admin\TagAdminController::class, 'index'], 'admin.tags');
$router->addRoute('GET', '/admin/tags/create', [\Controllers\Admin\TagAdminController::class, 'create'], 'admin.tags.create');
$router->addRoute('POST', '/admin/tags/create', [\Controllers\Admin\TagAdminController::class, 'store'], 'admin.tags.store');
$router->addRoute('GET', '/admin/tags/edit/{id:\d+}', [\Controllers\Admin\TagAdminController::class, 'edit'], 'admin.tags.edit');
$router->addRoute('POST', '/admin/tags/edit/{id:\d+}', [\Controllers\Admin\TagAdminController::class, 'update'], 'admin.tags.update');
$router->addRoute('POST', '/admin/tags/process', [\Controllers\Admin\TagAdminController::class, 'process'], 'admin.tags.process');

$router->addRoute('GET', '/admin/ratings', [\Controllers\Admin\RatingAdminController::class, 'index'], 'admin.ratings-index');
$router->addRoute('GET', '/admin/ratings/{id:\d+}', [\Controllers\Admin\RatingAdminController::class, 'show'], 'admin.ratings-show');
$router->addRoute('POST', '/admin/ratings/delete', [\Controllers\Admin\RatingAdminController::class, 'delete'], 'admin.ratings-delete');

return $router;