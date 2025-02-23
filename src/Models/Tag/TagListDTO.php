<?php
namespace Models\Tag;

final class TagListDTO
{
	public function __construct(
		public readonly int $id,
		public readonly string $name,
		public readonly string $created_at,
		public readonly string $updated_at,
		public readonly string $is_active,
	) {}
}