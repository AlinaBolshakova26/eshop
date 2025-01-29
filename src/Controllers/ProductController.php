<?php

namespace Controllers;
class ProductController {

	public static function show() {
		$product = [
			"id"=> 1,
			"name"=> "Нежность утра",
			"short_description"=> "Лёгкий и воздушный букет из розовых пионов, белых роз и эвкалипта.",
			"description"=> "Этот букет создан для тех, кто ценит нежность и утонченность. Нежно-розовые пионы гармонично сочетаются с белоснежными розами, а веточки эвкалипта добавляют свежести и легкого аромата. Идеально подойдёт для романтического подарка или утреннего признания в чувствах.",
			"price"=> "4500.00",
			"main_image"=> "/assets/images/product1.jpg",
			"created_at"=> "2025-01-27 17:23:08",
			"updated_at"=> "2025-01-28 17:45:49"
		];
		$productImages = [
			[
				"id"=> 1,
				"product_id"=> 1,
				"url"=> "/assets/images/product1-1.jpg",
				"sort_order"=> 1,
				"created_at"=> "2025-01-27 17:23:08"
			],
			[
				"id"=> 2,
				"product_id"=> 1,
				"url"=> "/assets/images/product1-2.jpg",
				"sort_order"=> 2,
				"created_at"=> "2025-01-27 17:23:08"
			]
		];


		if (!$product) {
			http_response_code(404);
			echo '404 Not Found';
			return;
		}

		ob_start();
		require_once __DIR__ . '/../Views/product/detail.php';
		$content = ob_get_clean();

		require_once __DIR__ . '/../Views/layouts/main_template.php';
	}
}