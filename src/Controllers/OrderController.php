<?php

namespace Controllers;
class OrderController {

	public static function create($id) {
		$product = [
			"id"=> $id,
			"name"=> "Нежность утра",
			"short_description"=> "Лёгкий и воздушный букет из розовых пионов, белых роз и эвкалипта.",
			"description"=> "Этот букет создан для тех, кто ценит нежность и утонченность. Нежно-розовые пионы гармонично сочетаются с белоснежными розами, а веточки эвкалипта добавляют свежести и легкого аромата. Идеально подойдёт для романтического подарка или утреннего признания в чувствах.",
			"price"=> "4500.00",
			"main_image"=> "/assets/images/product1.jpg",
			"created_at"=> "2025-01-27 17:23:08",
			"updated_at"=> "2025-01-28 17:45:49"
		];

		if (!$product) {
			http_response_code(404);
			echo '404 Not Found';
			return;
		}

		$quantity = 1;

		ob_start();
		require_once __DIR__ . '/../Views/order/form.php';
		$content = ob_get_clean();

		require_once __DIR__ . '/../Views/layouts/main_template.php';
	}

	public static function store() {
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			if (empty($errors)) {
				echo "<script>alert('Ваш заказ успешно оформлен.'); window.location.href='/products';</script>";
				exit;
			}
		}
	}
}