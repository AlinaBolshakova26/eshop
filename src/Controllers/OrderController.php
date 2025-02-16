<?php


namespace Controllers;

use Models\Order;
use Core\Database\MySQLDatabase;
use Core\Repositories\OrderRepository;
use Core\Repositories\CartRepository;
use Core\Services\CartService;
use Core\View;

class OrderController
{
    private OrderRepository $orderRepository;

    public function __construct()
    {
        $db = (new MySQLDatabase())->getConnection();
        $this->orderRepository = new OrderRepository($db);
    }

    public static function create($id)
    {
        $controller = new self();
        $product = $controller->orderRepository->getProductById($id);

        if (!$product) {
            http_response_code(404);
            echo '404 Not Found';
            return;
        }

        $quantity = isset($_GET['quantity']) ? max(1, (int)$_GET['quantity']) : 1;
        $userData = null;

        if (isset($_SESSION['user_id']))
        {
            $userData = $controller->orderRepository->getUserById($_SESSION['user_id']);
        }

        $content = View::make(__DIR__ . '/../Views/order/form.php', [
            'product' => $product,
            'quantity' => $quantity,
            'errors' => [],
            'user' => $userData,
        ]);

        echo View::make(__DIR__ . '/../Views/layouts/main_template.php', [
            'content' => $content
        ]);
    }

    public static function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        {
            return;
        }

        $controller = new self();

        $errors = [];
        $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
        $quantity = isset($_POST['quantity']) ? max(1, (int)$_POST['quantity']) : 1;
        $customer_name = trim($_POST['customer_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $city = trim($_POST['city'] ?? '');
        $street = trim($_POST['street'] ?? '');
        $house = trim($_POST['house'] ?? '');
        $apartment = trim($_POST['apartment'] ?? '');
        $email = trim($_POST['email'] ?? '');

        // Валидация данных
        if (!preg_match('/^(?:[А-ЯЁ][а-яё]+(?:\s+[А-ЯЁ][а-яё]+)+)$/u', $customer_name))
        {
            $errors['customer_name'] = 'ФИО должно содержать минимум два слова с заглавной буквы. (на русском языке)';
        }
        if (strlen($city) < 3)
        {
            $errors['city'] = 'Город должен содержать минимум 3 символа.';
        }
        if (strlen($street) < 3)
        {
            $errors['street'] = 'Улица должна содержать минимум 3 символа.';
        }
        if (strlen($house) < 1)
        {
            $errors['house'] = 'Поле "Дом" обязательно для заполнения.';
        }
        if (!preg_match('/^(\+7|8)\d{10}$/', $phone))
        {
            $errors['phone'] = 'Телефон должен быть в формате +7XXXXXXXXXX или 8XXXXXXXXXX.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            $errors['email'] = 'Неверный формат электронной почты.';
        }

        if (!empty($errors))
        {
            $product = $controller->orderRepository->getProductById($product_id);
            $content = View::make(__DIR__ . '/../Views/order/form.php', [
                'product' => $product,
                'quantity' => $quantity,
                'errors' => $errors,
                'user' => []
            ]);
            echo View::make(__DIR__ . '/../Views/layouts/main_template.php', [
                'content' => $content
            ]);
            return;
        }

        $user = $controller->orderRepository->getUserByData($phone, $email);

        if (!$user)
        {
            $user_id = $controller->orderRepository->createNewUser($customer_name, $phone, $email);
        }
        else
        {
            $user_id = $user['id'];
        }

        $price = $controller->orderRepository->getPrice($product_id);
        $total_price = $price * $quantity;

        $order = new Order($user_id, $product_id, $total_price, $city, $street, $house, $apartment);
        if ($order->saveInDb())
        {
            header("Location: /order/success");
            exit;
        }
        else
        {
            echo "<script>alert('Ошибка при оформлении заказа. Пожалуйста, попробуйте ещё раз.'); window.location.href='/order/create/{$product_id}';</script>";
        }
    }

    public static function createCartOrder()
    {
        if (!isset($_SESSION['user_id']))
        {
            header("Location: /user/login");
            exit;
        }
        $userId = $_SESSION['user_id'];

        $db = (new MySQLDatabase())->getConnection();
        $cartRepository = new CartRepository($db);
        $cartService = new CartService($cartRepository);
        $cartItems = $cartService->getCartItems($userId);

        if (empty($cartItems))
        {
            $_SESSION['flash'] = ['type' => 'warning', 'message' => 'Ваша корзина пуста'];
            header("Location: /cart");
            exit;
        }

        $total = 0;
        foreach ($cartItems as $item)
        {
            $total += ($item->product_price ?? 0) * $item->getQuantity();
        }

        $userData = null;
        if (isset($_SESSION['user_id']))
        {
            $userData = (new OrderController())->orderRepository->getUserById($_SESSION['user_id']);
        }

        $content = View::make(__DIR__ . '/../Views/order/checkout_cart.php', [
            'cartItems' => $cartItems,
            'total'     => $total,
            'user'      => $userData,
            'errors'    => []
        ]);

        echo View::make(__DIR__ . '/../Views/layouts/main_template.php', [
            'content' => $content
        ]);
    }

    public static function storeCartOrder()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        {
            return;
        }
        if (!isset($_SESSION['user_id']))
        {
            header("Location: /user/login");
            exit;
        }
        $userId = $_SESSION['user_id'];

        $customer_name = trim($_POST['customer_name'] ?? '');
        $phone         = trim($_POST['phone'] ?? '');
        $email         = trim($_POST['email'] ?? '');
        $city          = trim($_POST['city'] ?? '');
        $street        = trim($_POST['street'] ?? '');
        $house         = trim($_POST['house'] ?? '');
        $apartment     = trim($_POST['apartment'] ?? '');

        $errors = [];
        if (empty($customer_name))
        {
            $errors['customer_name'] = 'Введите ФИО';
        }
        if (!preg_match('/^(\+7|8)\d{10}$/', $phone))
        {
            $errors['phone'] = 'Телефон должен быть в формате +7XXXXXXXXXX или 8XXXXXXXXXX.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Введите корректный email';
        }
        if (strlen($city) < 3) {
            $errors['city'] = 'Город должен содержать минимум 3 символа';
        }
        if (strlen($street) < 3) {
            $errors['street'] = 'Улица должна содержать минимум 3 символа';
        }
        if (empty($house)) {
            $errors['house'] = 'Укажите номер дома';
        }

        if (!empty($errors))
        {
            $db = (new MySQLDatabase())->getConnection();
            $cartRepository = new CartRepository($db);
            $cartService = new CartService($cartRepository);
            $cartItems = $cartService->getCartItems($userId);

            $total = 0;
            foreach ($cartItems as $item)
            {
                $total += ($item->product_price ?? 0) * $item->getQuantity();
            }

            $content = View::make(__DIR__ . '/../Views/order/checkout_cart.php', [
                'cartItems' => $cartItems,
                'total'     => $total,
                'user'      => [
                    'name'  => $customer_name,
                    'phone' => $phone,
                    'email' => $email,
                    'city'  => $city,
                    'street'=> $street,
                    'house' => $house,
                    'apartment' => $apartment,
                ],
                'errors'    => $errors
            ]);

            echo View::make(__DIR__ . '/../Views/layouts/main_template.php', [
                'content' => $content
            ]);
            return;
        }

        $db = (new MySQLDatabase())->getConnection();
        $cartRepository = new CartRepository($db);
        $cartService = new CartService($cartRepository);
        $cartItems = $cartService->getCartItems($userId);

        if (empty($cartItems))
        {
            $_SESSION['flash'] = ['type' => 'warning', 'message' => 'Ваша корзина пуста'];
            header("Location: /cart");
            exit;
        }

        $orderRepository = new OrderRepository($db);
        $db->beginTransaction();
        $allSaved = true;

        foreach ($cartItems as $item)
        {
            $totalPrice = ($item->product_price ?? 0) * $item->getQuantity();
            $saved = $orderRepository->saveOrder(
                $userId,
                $item->getItemId(),
                $totalPrice,
                $city,
                $street,
                $house,
                $apartment
            );
            if (!$saved)
            {
                $allSaved = false;
                break;
            }
        }

        if ($allSaved)
        {
            $cartService->clearCart($userId);
            $db->commit();
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Заказ оформлен успешно'];
            header("Location: /order/success");
        }
        else
        {
            $db->rollBack();
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Ошибка при оформлении заказа'];
            header("Location: /order/checkout-cart");
        }
        exit;
    }

    public static function success()
    {
        $content = View::make(__DIR__ . '/../Views/order/success_order.php');
        echo View::make(__DIR__ . '/../Views/layouts/main_template.php', [
            'content' => $content
        ]);
    }
}