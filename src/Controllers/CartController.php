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

            $favoriteService = new \Core\Services\FavoriteService(new \Core\Repositories\FavoriteRepository((new \Core\Database\MySQLDatabase())->getConnection()));
            $favoriteService->removeFavorite($userId, $itemId);
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
        }

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
            exit;
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

        if ($itemId <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Неверный ID товара']);
            exit;
        }

        $success = $this->cartService->removeCartItem($userId, $itemId);

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            header('Content-Type: application/json');
            echo json_encode(['success' => $success]);
            exit;
        }

        if ($success) {
            header("Location: /cart");
            exit;
        } else {
            http_response_code(500);
            echo "Ошибка при удалении товара";
            exit;
        }
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
            header("Location: /order/success");
        } else {
            header("Location: /cart/checkout");
        }
        exit;
    }
}