<?php
namespace Models\Tag;

use PDO;
use PDOException;

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


	private function save(PDO $pdo): bool
	{
		try {
			$stmt = $pdo->prepare("
                UPDATE up_tag 
                SET name = :name, updated_at = :updated_at 
                WHERE id = :id
            ");

			return $stmt->execute([
				'id' => $this->id,
				'name' => $this->name,
				'updated_at' => $this->updated_at,
			]);
		} catch (PDOException $e) {
			error_log("Database error: " . $e->getMessage());
			return false;
		}
	}

	public function getId(): int
	{
		return $this->id;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function setId(int $id): void
	{
		$this->id = $id;
	}

	public function setName(string $name): void
	{
		$this->name = $name;
	}

	public function getCreatedAt(): string
	{
		return $this->created_at;
	}

	public function getUpdatedAt(): string
	{
		return $this->updated_at;
	}

	public function setCreatedAt(string $created_at): void
	{
		$this->created_at = $created_at;
	}

	public function setUpdatedAt(string $updated_at): void
	{
		$this->updated_at = $updated_at;
	}
}