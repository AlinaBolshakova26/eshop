<?php

namespace Core\Repositories;

use PDO;
use Models\Tag\Tag;


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

		if ($stmt->execute(['name' => $name])) 
		{
			$id = $this->pdo->lastInsertId();

			return new Tag ($id, $name, date('Y-m-d H:i:s'), date('Y-m-d H:i:s'));
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

    public function getProductsByTag(int $tagId): array
    {

        $stmt = $this->pdo->prepare("
        SELECT i.*
        FROM up_item i
        JOIN up_item_tag it ON i.id = it.item_id
        WHERE it.tag_id = :tagId
    	");

        $stmt->execute(['tagId' => $tagId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
		
    }

	public function addTagToProduct(int $productId, int $tagId): void
	{
		$stmt = $this->pdo->prepare("
            INSERT INTO up_item_tag (item_id, tag_id, created_at, updated_at)
            VALUES (:item_id, :tag_id, NOW(), NOW())
        ");
		$stmt->execute([
			':item_id' => $productId,
			':tag_id' => $tagId,
		]);
	}

	public function removeTagFromProduct(int $productId, int $tagId): void
	{
		$stmt = $this->pdo->prepare("
        DELETE FROM up_item_tag 
        WHERE item_id = :item_id AND tag_id = :tag_id
    ");
		$stmt->execute([
			':item_id' => $productId,
			':tag_id' => $tagId,
		]);
	}

	public function getTagsByProductId(int $productId): array
	{
		$stmt = $this->pdo->prepare("
        SELECT tag_id 
        FROM up_item_tag 
        WHERE item_id = :item_id
    ");
		$stmt->execute([':item_id' => $productId]);
		return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
	}
}