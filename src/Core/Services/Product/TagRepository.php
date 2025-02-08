<?php

namespace Core\Services\Product;

use Models\Tag;
use PDO;

class TagRepository
{
	private PDO $pdo;

	public function __construct(PDO $pdo)
	{
		$this->pdo = $pdo;
	}

	public function getAll(): array
	{
		$stmt = $this->pdo->query("
            SELECT id, name, created_at, updated_at
            FROM up_tag");

		return array_map(
			fn($row) => Tag::fromDatabase($row),
			$stmt->fetchAll(PDO::FETCH_ASSOC)
		);
	}

	public function create(string $name): ?Tag
	{
		$stmt = $this->pdo->prepare("
            INSERT INTO up_tag (name, created_at, updated_at)
            VALUES (:name, NOW(), NOW())
        ");

		if ($stmt->execute(['name' => $name])) {
			$id = $this->pdo->lastInsertId();
			return new Tag($id, $name, date('Y-m-d H:i:s'), date('Y-m-d H:i:s'));
		}
		return null;
	}
	public function update(int $id, string $newName): bool
	{
		$stmt = $this->pdo->prepare("
            UPDATE up_tag SET name = :name, updated_at = NOW()
            WHERE id = :id
        ");

		return $stmt->execute(['id' => $id, 'name' => $newName]);
	}

	public function delete(int $id): bool
	{
		$stmt = $this->pdo->prepare("DELETE FROM up_tag WHERE id = :id");
		return $stmt->execute(['id' => $id]);
	}
}