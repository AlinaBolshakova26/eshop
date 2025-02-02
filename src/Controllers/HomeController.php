<?php

namespace Controllers;

use Core\View;

class HomeController {

	public static function index() {
		$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
		$products = [
			[
				"id" => 1,
				"name" => "Нежность утра",
				"short_description" => "Лёгкий и воздушный букет из розовых пионов, белых роз и эвкалипта.",
				"description" => "Этот букет создан для тех, кто ценит нежность и утонченность. Нежно-розовые пионы гармонично сочетаются с белоснежными розами, а веточки эвкалипта добавляют свежести и легкого аромата. Идеально подойдёт для романтического подарка или утреннего признания в чувствах.",
				"price" => "4500.00",
				"main_image" => "/assets/images/product1.jpg",
				"created_at" => "2025-01-27 17:23:08",
				"updated_at" => "2025-01-28 17:45:49",
				"images" => [
					"/assets/images/product1-2.jpg",
					"/assets/images/product1-1.jpg"
				]
			],
			[
				"id" => 2,
				"name" => "Солнечное настроение",
				"short_description" => "Яркий букет из желтых тюльпанов, оранжевых гербер и зелени.",
				"description" => "Этот букет наполнен солнечным светом и радостью. Желтые тюльпаны символизируют счастье и удачу, а яркие герберы создают акцент энергии и позитивного настроения. Украшенный свежей зеленью, этот букет станет ярким дополнением любого события.",
				"price" => "3200.00",
				"main_image" => "/assets/images/product2.jpg",
				"created_at" => "2025-01-27 17:23:08",
				"updated_at" => "2025-01-28 17:45:49",
				"images" => [
					"/assets/images/product2-2.jpg",
					"/assets/images/product2-1.jpg"
				]
			]
		];

		$totalProducts = 2;
		define("ITEMS_PER_PAGE", 10);
		$totalPages = ceil($totalProducts / ITEMS_PER_PAGE);


		$content = View::make(__DIR__ . '/../Views/home/catalog.php', [
			'products' => $products,
			'totalPages' => $totalPages,
			'currentPage' => $currentPage,

		]);

		echo View::make(__DIR__ . '/../Views/layouts/main_template.php', [
			'content' => $content
		]);
	}
}