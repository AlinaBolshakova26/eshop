<?php


namespace Controllers;

use Models\Order\Order;
use Core\Database\MySQLDatabase;
use Core\Repositories\OrderRepository;
use Core\Repositories\CartRepository;
use Controllers\BaseController;
use Core\Services\CartService;
;

class OrderController extends BaseController
{
    
    private OrderRepository $orderRepository;

    public function __construct()
    {
        $database = new MySQLDatabase();
        $pdo = $database->getConnection();

        $this->orderRepository = new OrderRepository($pdo);
    }

    public function create($id)
    {
        $controller = new self();

        $product = $controller->orderRepository->getProductById($id);
        
        $quantity = isset($_GET['quantity']) ? max(1, (int)$_GET['quantity']) : 1;
        $userData = null;

        if (isset($_SESSION['user_id']))
        {
            $userData = $controller->orderRepository->getUserById($_SESSION['user_id']);
        }

        $this->render
        (
            'order/form',
            [
                'product' => $product,
                'quantity' => $quantity,
                'errors' => [],
                'user' => $userData,
            ]
        );
    }

    public function store()
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

            $this->render
            (
                'order/form',
                [
                    'product' => $product,
                    'quantity' => $quantity,
                    'errors' => $errors,
                    'user' => []
                ]
            );

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
            $this->redirect(url('order.success'));
        }
        else
        {
            echo "<script>alert('Ошибка при оформлении заказа. Пожалуйста, попробуйте ещё раз.'); window.location.href='" . url('order.create', ['id' => $product_id]) . "';</script>";
        }

    }


    public  function createCartOrder()
    {

        $this->checkLogin();

        $userId = $_SESSION['user_id'];

        $db = (new MySQLDatabase())->getConnection();
        $cartRepository = new CartRepository($db);
        $cartService = new CartService($cartRepository);
        $cartItems = $cartService->getCartItems($userId);

        if (empty($cartItems))
        {
            $_SESSION['flash'] = ['type' => 'warning', 'message' => 'Ваша корзина пуста'];
            header("Location: " . url('cart-index'));
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

        $this->render(
            'order/checkout_cart', 
            [
                'cartItems' => $cartItems,
                'total'     => $total,
                'user'      => $userData,
                'errors'    => []
            ]
        );

    }

    public function storeCartOrder()
    {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') 
        {
            return;
        }
        
        $this->checkLogin();

        $userId = $_SESSION['user_id'];

        $customer_name = trim($_POST['customer_name'] ?? '');
        $phone         = trim($_POST['phone'] ?? '');
        $email         = trim($_POST['email'] ?? '');
        $city          = trim($_POST['city'] ?? '');
        $street        = trim($_POST['street'] ?? '');
        $house         = trim($_POST['house'] ?? '');
        $apartment     = trim($_POST['apartment'] ?? '');

        $errors = [];
        if (empty($customer_name)) {
            $errors['customer_name'] = 'Введите ФИО';
        }
        if (!preg_match('/^(\+7|8)\d{10}$/', $phone)) 
        {
            $errors['phone'] = 'Телефон должен быть в формате +7XXXXXXXXXX или 8XXXXXXXXXX.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) 
        {
            $errors['email'] = 'Введите корректный email';
        }
        if (strlen($city) < 3) 
        {
            $errors['city'] = 'Город должен содержать минимум 3 символа';
        }
        if (strlen($street) < 3) 
        {
            $errors['street'] = 'Улица должна содержать минимум 3 символа';
        }
        if (empty($house)) 
        {
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

            $this->render
            (
                'order/checkout_cart', 
                [
                    'cartItems' => $cartItems,
                    'total'     => $total,
                    'user'      => 
                    [
                        'name'      => $customer_name,
                        'phone'     => $phone,
                        'email'     => $email,
                        'city'      => $city,
                        'street'    => $street,
                        'house'     => $house,
                        'apartment' => $apartment,
                    ],
                    'errors'    => $errors
                ]
            );
            return;
        }

        $db = (new MySQLDatabase())->getConnection();
        $cartRepository = new CartRepository($db);
        $cartService = new CartService($cartRepository);
        $cartItems = $cartService->getCartItems($userId);


        if (empty($cartItems)) 
        {
            $_SESSION['flash'] = ['type' => 'warning', 'message' => 'Ваша корзина пуста'];
            $this->redirect(url('cart-index'));
        }

        $orderRepository = new OrderRepository($db);
        $db->beginTransaction();
        $allSaved = true;

        foreach ($cartItems as $item) {
            $quantity = $item->getQuantity();
            $totalPrice = ($item->product_price ?? 0) * $quantity;

            $saved = $orderRepository->saveOrder
            (
                $userId,
                $item->getItemId(),
                $quantity,
                (float)$totalPrice,
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
            $this->redirect(url('order.success'));
        } 
        else 
        {
            $db->rollBack();
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Ошибка при оформлении заказа'];
            $this->redirect(url('order.checkout-cart'));
        }

        exit;

    }

    public function success()
    {
        $this->render
        (
            'order/success_order'
        );
    }

}
