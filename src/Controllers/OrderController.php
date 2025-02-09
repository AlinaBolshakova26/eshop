<?php

namespace Controllers;

use Models\Order;
use Core\Database\MySQLDatabase;
use Core\View;
use PDO;

class OrderController
{
    public static function create($id)
    {
        $db = (new MySQLDatabase())->getConnection();
        // Картинка из бд (главная)
        $stmt = $db->prepare("
            SELECT i.*, im.path AS main_image 
            FROM up_item i 
            LEFT JOIN up_image im ON i.id = im.item_id AND im.is_main = 1 
            WHERE i.id = :id
        ");
        $stmt->execute(['id' => $id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            http_response_code(404);
            echo '404 Not Found';
            return;
        }

        $quantity = isset($_GET['quantity']) ? max(1, (int)$_GET['quantity']) : 1;

        $content = View::make(__DIR__ . '/../Views/order/form.php', [
            'product' => $product,
            'quantity' => $quantity,
            'errors' => []
        ]);

        echo View::make(__DIR__ . '/../Views/layouts/main_template.php', [
            'content' => $content
        ]);
    }
    // Обрабатываем заказ
    public static function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = [];

            $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
            $quantity = isset($_POST['quantity']) ? max(1, (int)$_POST['quantity']) : 1;
            $customer_name = trim($_POST['customer_name'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $address = trim($_POST['address'] ?? '');
            $email = trim($_POST['email'] ?? '');


            if (!preg_match('/^(?:[А-ЯЁ][а-яё]+(?:\s+[А-ЯЁ][а-яё]+)+)$/u', $customer_name)) {
                $errors['customer_name'] = 'ФИО должно содержать минимум два слова с заглавной буквы.';
            }
            if (strlen($address) < 10) {
                $errors['address'] = 'Адрес должен содержать минимум 10 символов.';
            }
            if (!preg_match('/^(\+7|8)\d{10}$/', $phone)) {
                $errors['phone'] = 'Телефон должен быть в формате +7XXXXXXXXXX или 8XXXXXXXXXX.';
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Неверный формат электронной почты.';
            }

            if (!empty($errors)) {
                $db = (new MySQLDatabase())->getConnection();
                $stmt = $db->prepare("
                    SELECT i.*, im.path AS main_image 
                    FROM up_item i 
                    LEFT JOIN up_image im ON i.id = im.item_id AND im.is_main = 1 
                    WHERE i.id = :id
                ");
                $stmt->execute(['id' => $product_id]);
                $product = $stmt->fetch(PDO::FETCH_ASSOC);

                $content = View::make(__DIR__ . '/../Views/order/form.php', [
                    'product' => $product,
                    'quantity' => $quantity,
                    'errors' => $errors
                ]);

                echo View::make(__DIR__ . '/../Views/layouts/main_template.php', [
                    'content' => $content
                ]);
                return;
            }

            // Подключение к бд и проверка на то, есть ли пользователь уже
            $db = (new MySQLDatabase())->getConnection();
            $stmt = $db->prepare("SELECT * FROM up_user WHERE phone = :phone OR email = :email");
            $stmt->execute(['phone' => $phone, 'email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                $stmt = $db->prepare("INSERT INTO up_user (name, phone, email, role) VALUES (:name, :phone, :email, 'customer')");
                $stmt->execute(['name' => $customer_name, 'phone' => $phone, 'email' => $email]);
                $user_id = $db->lastInsertId();
            } else {
                $user_id = $user['id'];
            }

            // Цена на товар из бд
            $stmt = $db->prepare("SELECT price FROM up_item WHERE id = :id");
            $stmt->execute(['id' => $product_id]);
            $productData = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$productData) {
                echo "<script>alert('Товар не найден.'); window.location.href='/';</script>";
                exit;
            }
            $price = isset($productData['price']) ? (float)$productData['price'] : 0.0;
            $total_price = $price * $quantity;

            $order = new Order($user_id, $product_id, $total_price, $address);
            if ($order->save()) {
                header("Location: /order/success");
                exit;
            } else {
                echo "<script>alert('Ошибка при оформлении заказа. Пожалуйста, попробуйте ещё раз.'); window.location.href='/order/create/{$product_id}';</script>";
            }
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

