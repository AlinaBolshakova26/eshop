<?php
namespace Models\Product;

use Models\Rating\RatingListDTO;

final class ProductListDTO
{
	public function __construct(
		public readonly int $id,
		public readonly string $name,
		public readonly string $desc_short,
		public readonly string $price,
		public readonly int $is_active,
		public readonly ?string $main_image_path,
		public readonly ?array $additional_image_paths,
        public readonly ?RatingListDTO $rating = null
    ) {}
}