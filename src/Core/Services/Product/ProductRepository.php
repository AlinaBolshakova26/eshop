<?php
namespace Core\Services\Product;

use PDO;
use Models\Product;

class ProductRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }


    public function findAllPaginated(int $limit, int $offset, bool $showOnlyActive = true): array
    {
        $sql = 
        ("
            SELECT 
                i.id, i.name, i.price, i.is_active, i.created_at, i.desc_short,
                img.path AS main_image_path
            FROM up_item i
            LEFT JOIN up_image img ON i.id = img.item_id AND img.is_main = 1
        ");

        if ($showOnlyActive)
        {
            $sql .= "WHERE i.is_active = 1 ";
        }

        $sql .= " ORDER BY i.id ASC LIMIT :limit OFFSET :offset";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $productIds = array_column($products, 'id');

        $additionalImages = $this->findAdditionalImages($productIds);

        $imagesByProduct = [];
        foreach ($additionalImages as $image) {
            $imagesByProduct[$image['item_id']][] = $image['path'];
        }

        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $products = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
        {
            $product = Product::fromDatabase($row);
            $product->setAdditionalImagePaths($this->findAdditionalImagesById($row['id']));
            $products[] = $product;
        }

        return $products;
    }


    public function findProductById(int $id): ?Product
    {
        $stmt = $this->pdo->prepare("
        SELECT i.id, i.name, i.price, i.description, img.path AS main_image_path
        FROM up_item i 
        LEFT JOIN up_image img ON i.id = img.item_id AND img.is_main = 1
        WHERE i.id = :id
    ");

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$product) {
            return null;
        }

        $product = Product::fromDatabase($product);
        $product->setAdditionalImagePaths($this->findAdditionalImagesById($id));

        return $product;
    }

    private function findAdditionalImagesById(int $productId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT path FROM up_image	
            WHERE item_id = :id AND is_main = 0
            ORDER BY id
        ");
        $stmt->bindValue(':id', $productId, PDO::PARAM_INT);
        $stmt->execute();

        return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'path');
    }

    public function getTotalCount(?int $tagId = null): int
    {
        if ($tagId) {
            $stmt = $this->pdo->prepare("
            SELECT COUNT(*) 
            FROM up_item i
            JOIN up_item_tag it ON i.id = it.item_id
            WHERE it.tag_id = :tagId
        ");
            $stmt->bindValue(':tagId', $tagId, PDO::PARAM_INT);
        } else {
            $stmt = $this->pdo->query("SELECT COUNT(*) FROM up_item");
        }
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function updateStatus(array $productIds, bool $newStatus): void
    {   
        $placeholders = str_repeat('?,', count($productIds) - 1) . '?';
        $params = array_merge([$newStatus ? 1 : 0], $productIds);

        $stmt = $this->pdo->prepare("UPDATE up_item 
        SET is_active = ?
        WHERE id IN ($placeholders)");

        $stmt->execute($params);        
    }

    public function getTotalCount(): int
    {
        $stmt = $this->pdo->query('SELECT COUNT(*) FROM up_item');
        return (int)$stmt->fetchColumn();
    }

}
