<?php

namespace Models;

use PDO;
use PDOException;

final class TagListDTO
{
	public function __construct(
		public readonly int $id,
		public readonly string $name,
		public readonly string $created_at,
		public readonly string $updated_at
	) {}
}

class Tag
{
	private int $id;
	private string $name;
	private string $created_at;
	private string $updated_at;

	public static function fromDatabase(array $row): self
	{
		$tag = new self();
		$tag->id = $row['id'];
		$tag->name = $row['name'];
		$tag->created_at = $row['created_at'] ?? date('Y-m-d H:i:s');
		$tag->updated_at = $row['updated_at'] ?? date('Y-m-d H:i:s');

		return $tag;
	}

	public function toListDTO(): TagListDTO
	{
		return new TagListDTO(
			$this->id,
			$this->name,
			$this->created_at,
			$this->updated_at
		);
	}

	public function rename(PDO $pdo, string $newName): bool
	{
		$this->name = $newName;
		$this->updated_at = date('Y-m-d H:i:s');
		return $this->save($pdo);
	}
}
