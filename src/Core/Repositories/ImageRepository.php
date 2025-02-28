<?php


namespace Core\Repositories;

use Exception;
use PDO;

class ImageRepository 
{

    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function processMainImage(array $params): array
    {

        $existingImage = $this->findMainImageByProductId($params['product_id']);

        $result = 
        [
            'action' => null,
            'existingImage' => $existingImage,
            'imageId' => null
        ];

        if ($existingImage) 
        {
            $params = 
            [
                ':path' => $params['image_path'],
                ':height' => $params['height'],
                ':width' => $params['width'],
                ':description' => $params['description'],
                ':id' => $existingImage['id'],
            ];

            $this->updateById($params);

            $result['action'] = 'update';
            $result['imageId'] = $existingImage['id'];                
        } 
        else 
        {
            $params = 
            [
                ':path' => $params['image_path'],
                ':item_id' => $params['product_id'],
                ':is_main' => 1,
                ':height' => $params['height'],
                ':width' => $params['width'],
                ':description' => $params['description'],
            ];

            $this->insert($params);

            $result['action'] = 'insert';
            $result['imageId'] = $this->pdo->lastInsertId();
        }

        return $result;

    }

    public function updateById(array $params): void
    {

        $stmt = $this->pdo->prepare
            ("
                UPDATE up_image 
                SET 
                    path = :path, 
                    height = :height, 
                    width = :width, 
                    description = :description, 
                    updated_at = NOW()
                WHERE id = :id
            ");
        $stmt->execute($params);

    }
    
    public function findMainImageByProductId(int $productId): array|false
    {
        
        $stmt = $this->pdo->prepare
			("
				SELECT 
					id, 
					path 
				FROM up_image 
				WHERE item_id = :item_id 
				AND is_main = 1 FOR UPDATE
			");
			$stmt->execute
			(
				[
					':item_id' => $productId
				]
			);

			return $stmt->fetch(PDO::FETCH_ASSOC);

    }


    public function insert(array $params): void
    {

        $stmt = $this->pdo->prepare
            ("
                INSERT INTO up_image (path, item_id, is_main, height, width, description, created_at, updated_at) 
                VALUES (:path, :item_id, :is_main, :height, :width, :description, NOW(), NOW())
            ");

        $stmt->execute($params);

    }

    public function findPathById(int $imageId): array|false
    {

        $stmt = $this->pdo->prepare
		("
			SELECT 
				path 
			FROM up_image 
			WHERE id = :id
		");
		$stmt->execute
		(
			[
				':id' => $imageId
			]
		);

		return $stmt->fetch(PDO::FETCH_ASSOC);

    }

    public function deleteById(int $imageId): void
    {

        $stmt = $this->pdo->prepare("DELETE FROM up_image WHERE id = :id");
        $stmt->execute([':id' => $imageId]);

    }

}