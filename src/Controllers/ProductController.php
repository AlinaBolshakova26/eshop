<?php

namespace Controllers;

use Core\View;

// use PDO;

class ProductController
{


    /* ТЕСТ */
    private static $products = 
    [
        1 => ['name' => 'Роза', 'price' => 249],
        2 => ['name' => 'Пион', 'price' => 499],
        3 => ['name' => 'Пион XXL', 'price' => 1399],
        4 => ['name' => 'Тюльпан в ассортименте', 'price' => 179],
        6 => ['name' => 'Хризантема', 'price' => 89],
        100 => ['name' => 'Фиалка', 'price' => 49],
    ];

    public static function show($id)
    {
		$product = [
			"id"=> 1,
			"name"=> "Нежность утра",
			"short_description"=> "Лёгкий и воздушный букет из розовых пионов, белых роз и эвкалипта.",
			"description"=> "Этот букет создан для тех, кто ценит нежность и утонченность. Нежно-розовые пионы гармонично сочетаются с белоснежными розами, а веточки эвкалипта добавляют свежести и легкого аромата. Идеально подойдёт для романтического подарка или утреннего признания в чувствах.",
			"price"=> 4500,
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

		$content = View::make(__DIR__ . '/../Views/product/detail.php', [
			'product' => $product,
			'productImages' => $productImages
		]);

		echo View::make(__DIR__ . '/../Views/layouts/main_template.php', [
			'content' => $content
		]);

    }
    /* ТЕСТ */
    
}