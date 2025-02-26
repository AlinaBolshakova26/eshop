<?php

namespace Models\Cart;

final class CartListDTO
{
	public function __construct
	(
		public readonly int $id,
		public readonly int $user_id,
		public readonly int $item_id,
		public readonly int $quantity,
		public readonly string $created_at,
		public readonly string $updated_at
	) 
	{}

	public function getFormattedCreatedAt(): string
	{
		return date('d.m.Y', strtotime($this->created_at));
	}

	public function getFormattedUpdatedAt(): string
	{
		return date('d.m.Y', strtotime($this->updated_at));
	}
}