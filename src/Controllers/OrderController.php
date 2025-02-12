<?php


namespace Controllers;

use Models\Order;
use Core\Database\MySQLDatabase;
use Core\Repositories\OrderRepository;
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

        if (isset($_SESSION['user_id'])) {
            $userData = $controller->orderRepository->getUserById($_SESSION['user_id']);
        }

        $content = \Core\View::make(__DIR__ . '/../Views/order/form.php', [
            'product' => $product,
            'quantity' => $quantity,
            'errors' => [],
            'user' => $userData,
        ]);

        echo \Core\View::make(__DIR__ . '/../Views/layouts/main_template.php', [
            'content' => $content
        ]);
    }

    public static function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
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
        if (!preg_match('/^(?:[А-ЯЁ][а-яё]+(?:\s+[А-ЯЁ][а-яё]+)+)$/u', $customer_name)) {
            $errors['customer_name'] = 'ФИО должно содержать минимум два слова с заглавной буквы. (на русском языке)';
        }
        if (strlen($city) < 3) {
            $errors['city'] = 'Город должен содержать минимум 3 символа.';
        }
        if (strlen($street) < 3) {
            $errors['street'] = 'Улица должна содержать минимум 3 символа.';
        }
        if (strlen($house) < 1) {
            $errors['house'] = 'Поле "Дом" обязательно для заполнения.';
        }
        if (!preg_match('/^(\+7|8)\d{10}$/', $phone)) {
            $errors['phone'] = 'Телефон должен быть в формате +7XXXXXXXXXX или 8XXXXXXXXXX.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Неверный формат электронной почты.';
        }

        if (!empty($errors)) {
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

        if (!$user) {
            $user_id = $controller->orderRepository->createNewUser($customer_name, $phone, $email);
        } else {
            $user_id = $user['id'];
        }

        // Получение цены товара
        $price = $controller->orderRepository->getPrice($product_id);
        $total_price = $price * $quantity;

        // Создание заказа
        $order = new Order($user_id, $product_id, $total_price, $city, $street, $house, $apartment);
        if ($order->saveInDb()) {
            header("Location: /order/success");
            exit;
        } else {
            echo "<script>alert('Ошибка при оформлении заказа. Пожалуйста, попробуйте ещё раз.'); window.location.href='/order/create/{$product_id}';</script>";
        }
    }

    public static function success()
    {
        $content = View::make(__DIR__ . '/../Views/order/success_order.php');
        echo View::make(__DIR__ . '/../Views/layouts/main_template.php', [
            'content' => $content
        ]);
    }
}