<?php
namespace Models\Product;

final class ProductDetailDTO
{

	public function __construct
	(
		public readonly int $id,
		public readonly string $name,
		public readonly string $price,
		public readonly string $description,
		public readonly ?string $main_image_path,
		public readonly ?array $additional_image_paths,
	) {}
	
}