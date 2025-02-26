<?php
namespace Models\Order;

final class OrderListDTO
{

	public function __construct
	(
		public readonly int $id,
		public readonly string $city,
		public readonly string $street,
		public readonly string $house,
		public readonly ?string $apartment,
		public readonly float $price,
		public readonly string $status,
		public readonly string $createdAt
	) {}

}