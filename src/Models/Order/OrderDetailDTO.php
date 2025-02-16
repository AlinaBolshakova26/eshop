<?php
namespace Models\Order;

final class OrderDetailDTO
{
	public function __construct(
		public readonly int $id,
		public readonly int $user_id,
		public readonly int $item_id,
		public readonly float $price,
		public readonly string $city,
		public readonly string $street,
		public readonly string $house,
		public readonly ?string $apartment,
		public readonly string $status,
		public readonly string $createdAt,
		public readonly string $updatedAt
	) {}
}