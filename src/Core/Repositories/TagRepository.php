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
		$sql = "
        SELECT id, name, is_active, created_at, updated_at
        FROM up_tag
        WHERE is_active = 1;
    ";


		$stmt = $this->pdo->prepare($sql);
		$stmt->execute();

		return array_map([Tag::class, 'fromDatabase'], $stmt->fetchAll(PDO::FETCH_ASSOC));
	}

	public function create(string $name): int
	{
		$stmt = $this->pdo->prepare("
        INSERT INTO up_tag (name, is_active, created_at, updated_at)
        VALUES (:name, 1, NOW(), NOW())
    ");

		$stmt->execute([':name' => $name]);

		return (int)$this->pdo->lastInsertId();
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

	public function findAllPaginated(int $limit, int $offset): array
	{
		$sql = "
        SELECT id, name, is_active, created_at, updated_at
        FROM up_tag
    	";
		
		$sql .= " ORDER BY id ASC LIMIT :limit OFFSET :offset";

		$stmt = $this->pdo->prepare($sql);

		$params = [];

		$params[':limit'] = $limit;
		$params[':offset'] = $offset;

		$stmt->execute($params);

		return array_map([Tag::class, 'fromDatabase'], $stmt->fetchAll(PDO::FETCH_ASSOC));
	}

	public function getTotalCount(): int
	{
		$sql = "SELECT COUNT(*) FROM up_tag";

		$stmt = $this->pdo->prepare($sql);

		$stmt->execute();

		return (int)$stmt->fetchColumn();
	}


	public function findTagById(int $id): ?Tag
	{
		$stmt = $this->pdo->prepare("
        SELECT id, name, is_active, created_at, updated_at
        FROM up_tag
        WHERE id = :id
        LIMIT 1
    ");

		$stmt->execute([':id' => $id]);
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$row) {
			return null;
		}

		return Tag::fromDatabase($row);
	}

	public function updateName(int $id, string $newName): void
	{
		$stmt = $this->pdo->prepare("
        UPDATE up_tag
        SET name = :name, updated_at = NOW()
        WHERE id = :id
    ");

		$stmt->execute([
			':id' => $id,
			':name' => $newName,
		]);
	}

	public function updateStatus(array $tagIds, bool $isActive): void
	{
		if (empty($tagIds)) {
			return;
		}

		$placeholders = str_repeat('?,', count($tagIds) - 1) . '?';
		$stmt = $this->pdo->prepare("
        UPDATE up_tag
        SET is_active = ?, updated_at = NOW()
        WHERE id IN ($placeholders)
    ");

		$params = array_merge([(int)$isActive], $tagIds);
		$stmt->execute($params);
	}

	public function getLastInsertedId(): int
	{
		return (int)$this->pdo->lastInsertId();
	}

	public function findByName(string $name): ?array
	{
		$stmt = $this->pdo->prepare("SELECT * FROM up_tag WHERE BINARY name = :name LIMIT 1");
		$stmt->bindValue(':name', $name, PDO::PARAM_STR);
		$stmt->execute();

		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		return $result !== false ? $result : null;
	}

}