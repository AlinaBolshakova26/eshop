<?php

namespace Controllers;

use Core\Database\MySQLDatabase;
use Core\Repositories\CartRepository;
use Core\Services\CartService;
use Core\View;

class CartController
{
    private CartService $cartService;

    public function __construct()
    {
        $db = (new MySQLDatabase())->getConnection();
        $cartRepository = new CartRepository($db);
        $this->cartService = new CartService($cartRepository);
    }

    public function index()
    {
        
        if (!isset($_SESSION['user_id'])) {
            header("Location: /user/login");
            exit;
        }

        $userId = $_SESSION['user_id'];
        $cartItems = $this->cartService->getCartItems($userId);

        $content = View::make(__DIR__ . '/../Views/cart/index.php', [
            'cartItems' => $cartItems
        ]);

        echo View::make(__DIR__ . '/../Views/layouts/main_template.php', [
            'content' => $content
        ]);
    }

    public function add()
    {
        
        if (!isset($_SESSION['user_id'])) {
            header("Location: /user/login");
            exit;
        }

        $userId = $_SESSION['user_id'];
        $itemId = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
        $quantity = isset($_POST['quantity']) ? max(1, (int)$_POST['quantity']) : 1;

        if ($itemId > 0) {
            $this->cartService->addToCart($userId, $itemId, $quantity);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Товар добавлен в корзину'];
        }

        header("Location: /cart");
        exit;
    }

    public function update()
    {
        
        if (!isset($_SESSION['user_id'])) {
            header("Location: /user/login");
            exit;
        }

        $userId = $_SESSION['user_id'];
        $itemId = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
        $quantity = isset($_POST['quantity']) ? max(1, (int)$_POST['quantity']) : 1;

        if ($itemId > 0) {
            $this->cartService->updateCartItem($userId, $itemId, $quantity);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Количество товара обновлено'];
        }

        header("Location: /cart");
        exit;
    }

    public function remove()
    {
        
        if (!isset($_SESSION['user_id'])) {
            header("Location: /user/login");
            exit;
        }

        $userId = $_SESSION['user_id'];
        $itemId = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;

        if ($itemId > 0) {
            $this->cartService->removeCartItem($userId, $itemId);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Товар удалён из корзины'];
        }

        header("Location: /cart");
        exit;
    }

    public function checkout()
    {
        
        if (!isset($_SESSION['user_id'])) {
            header("Location: /user/login");
            exit;
        }

        $userId = $_SESSION['user_id'];
        $cartItems = $this->cartService->getCartItems($userId);

        $content = View::make(__DIR__ . '/../Views/cart/checkout.php', [
            'cartItems' => $cartItems
        ]);

        echo View::make(__DIR__ . '/../Views/layouts/main_template.php', [
            'content' => $content
        ]);
    }

    public function processCheckout()
    {
        
        if (!isset($_SESSION['user_id'])) {
            header("Location: /user/login");
            exit;
        }

        $userId = $_SESSION['user_id'];
        $success = $this->cartService->clearCart($userId);

        if ($success) {
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Заказ оформлен успешно'];
            header("Location: /order/success");
        } else {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Ошибка при оформлении заказа'];
            header("Location: /cart/checkout");
        }
        exit;
    }
}