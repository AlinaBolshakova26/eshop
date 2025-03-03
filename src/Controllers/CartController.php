<?php

namespace Controllers;

use Controllers\BaseController;
use Core\Database\MySQLDatabase;
use Core\Repositories\CartRepository;
use Core\Services\CartService;

class CartController extends BaseController
{
    
    private CartService $cartService;

    public function __construct()
    {

        $database = new MySQLDatabase();
        $pdo = $database->getConnection();

        $cartRepository = new CartRepository($pdo);
        $this->cartService = new CartService($cartRepository);

    }

    public function index()
    {

        $this->checkLogin();

        $userId = $_SESSION['user_id'];
        $cartItems = $this->cartService->getCartItems($userId);

        $this->render
        (
            'cart/index', 
            [
                'cartItems' => $cartItems
            ]
        );
    }

    public function add()
    {

        $this->checkLogin();

        $userId = $_SESSION['user_id'];
        $itemId = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
        $quantity = isset($_POST['quantity']) ? max(1, (int)$_POST['quantity']) : 1;

        if ($itemId > 0) 
        {
            $this->cartService->addToCart($userId, $itemId, $quantity);

            $favoriteService = new \Core\Services\FavoriteService(new \Core\Repositories\FavoriteRepository((new \Core\Database\MySQLDatabase())->getConnection()));
            $favoriteService->removeFavorite($userId, $itemId);
        }

        $this->redirect(url('cart-index'));
    }

    public function update()
    {

        $this->checkLogin();

        $userId = $_SESSION['user_id'];
        $itemId = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
        $quantity = isset($_POST['quantity']) ? max(1, (int)$_POST['quantity']) : 1;

        if ($itemId > 0) 
        {
            $this->cartService->updateCartItem($userId, $itemId, $quantity);
        }

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') 
        {
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
            exit;
        }

        $this->redirect(url('cart-index'));
    }



    public function remove()
    {

        $this->checkLogin();

        $userId = $_SESSION['user_id'];
        $itemId = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;

        if ($itemId <= 0) 
        {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Неверный ID товара']);
            exit;
        }

        $success = $this->cartService->removeCartItem($userId, $itemId);

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') 
        {
            header('Content-Type: application/json');
            echo json_encode(['success' => $success]);
            exit;
        }

        if ($success) 
        {
            $this->redirect(url('cart-index'));
        } 
        else 
        {
            http_response_code(500);
            echo "Ошибка при удалении товара";
            exit;
        }

    }

    public function checkout()
    {
        $this->checkLogin();

        $userId = $_SESSION['user_id'];
        $cartItems = $this->cartService->getCartItems($userId);

        $this->render
        (
            'cart/checkout', 
            [
            'cartItems' => $cartItems
            ]
        );    
    }

    public function processCheckout()
    {
        $this->checkLogin();

        $userId = $_SESSION['user_id'];
        $success = $this->cartService->clearCart($userId);

        if ($success) 
        {
            $url = url('order.success');
        } 
        else 
        {
            $url = url('cart.checkout');
        }

        $this->redirect($url);
    }

}
