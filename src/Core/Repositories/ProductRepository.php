<?php

namespace Core\Repositories;

use PDO;
use Models\Product;

class ProductRepository
{

    private PDO $pdo;

    public function __construct(PDO $pdo)
    {

        $this->pdo = $pdo;

    }

    public function findAll(bool $showOnlyActive = true): array
    {

        $sql = "
            SELECT 
                i.id, i.name, i.price, i.is_active, i.created_at, i.desc_short, i.description,
                img.path AS main_image_path
            FROM up_item i
            LEFT JOIN up_image img ON i.id = img.item_id AND img.is_main = 1
        ";

        if ($showOnlyActive)
        {
            $sql .= "AND i.is_active = 1 ";
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (!empty($products))
        {
            $productIds = array_column($products, 'id');
            $additionalImages = $this->findAdditionalImages($productIds);
            $imagesByProduct = [];

            foreach ($additionalImages as $image)
            {
                $imagesByProduct[$image['item_id']][] = $image['path'];
            }

            return array_map(
                function ($productData) use ($imagesByProduct) {
                    $product = Product::fromDatabase($productData);
                    $additionalImages = $imagesByProduct[$productData['id']] ?? [];
                    $product->setAdditionalImagePaths($additionalImages);
                    return $product;
                },
                $products
            );  
        }
        
        return [];

    }

    public function findAllPaginated(int $limit, int $offset, ?string $query, ?int $tagId = null, bool $showOnlyActive = true): array
    {

        $sql = "
            SELECT 
                i.id, i.name, i.price, i.is_active, i.created_at, i.desc_short, i.description,
                img.path AS main_image_path
            FROM up_item i
            LEFT JOIN up_image img ON i.id = img.item_id AND img.is_main = 1
        ";

        if ($tagId)
        {
            $sql .= "JOIN up_item_tag it ON i.id = it.item_id ";
        }

        $sql .= "WHERE 1=1 ";

        if ($showOnlyActive)
        {
            $sql .= "AND i.is_active = 1 ";
        }

        if ($tagId) 
        {
            $sql .= "AND it.tag_id = :tagId ";
        }

        if ($query)
        {
            $sql .= "AND LOWER(i.name) LIKE LOWER(:query) ";
        }

        $sql .= "ORDER BY i.id ASC LIMIT :limit OFFSET :offset";

        $stmt = $this->pdo->prepare($sql);

        if ($tagId)
        {
            $stmt->bindParam(':tagId', $tagId, PDO::PARAM_INT);
        }

        if ($query)
        {
            $stmt->bindParam(':query', $query, PDO::PARAM_STR);
        }

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (!empty($products))
        {
            $productIds = array_column($products, 'id');
            $additionalImages = $this->findAdditionalImages($productIds);
            $imagesByProduct = [];

            foreach ($additionalImages as $image)
            {
                $imagesByProduct[$image['item_id']][] = $image['path'];
            }

            return array_map(
                function ($productData) use ($imagesByProduct) {
                    $product = Product::fromDatabase($productData);
                    $additionalImages = $imagesByProduct[$productData['id']] ?? [];
                    $product->setAdditionalImagePaths($additionalImages);
                    return $product;
                },
                $products
            );  
        }
        
        return [];

    }

    public function findProductById(int $id, bool $isAdmin = false): ?Product
    {

        $fields = $isAdmin
        ? "i.id, i.name, i.description, i.desc_short, i.price, i.is_active, i.created_at, i.updated_at, img.path AS main_image_path"
        : "i.id, i.name, i.price, i.description, img.path AS main_image_path";

        $stmt = $this->pdo->prepare("
            SELECT {$fields}
            FROM up_item i 
            LEFT JOIN up_image img ON i.id = img.item_id AND img.is_main = 1
            WHERE i.id = :id
            LIMIT 1
        ");

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) 
        {
            return null;
        }

        $product = Product::fromDatabase($product);
        $product->setAdditionalImagePaths($this->findAdditionalImagesById($id));

        return $product;

    }

    private function findAdditionalImagesById(int $productId): array
    {

        $stmt = $this->pdo->prepare("
            SELECT id, path FROM up_image	
            WHERE item_id = :id AND is_main = 0
            ORDER BY id
        ");

        $stmt->bindValue(':id', $productId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    }

    public function getTotalCount(?int $tagId = null, ?string $query = null): int
    {

        $sql = "SELECT COUNT(*) FROM up_item i ";
        $params = [];

        if ($tagId) 
        {
            $sql .= "JOIN up_item_tag it ON i.id = it.item_id ";
            $sql .= "WHERE it.tag_id = :tagId ";
            $params[':tagId'] = $tagId;
        }

        if ($query)
        {
            $sql .= ($tagId ? "AND" : "WHERE") . " LOWER(i.name) LIKE LOWER(:query) ";
            $params[':query'] = $query;
        }

        $stmt = $this->pdo->prepare($sql);
        
        foreach ($params as $key => $value) 
        {
            $stmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
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

    public function findAdditionalImages(array $productIds)
    {

        $stmt = $this->pdo->prepare("
            SELECT item_id, path
            FROM up_image
            WHERE item_id IN (" . implode(',', array_fill(0, count($productIds), '?')) . ")        
            AND is_main = 0
            ORDER BY id
        ");

        $stmt->execute($productIds);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }

    public function create(array $data): int
    {

        $stmt = $this->pdo->prepare("
            INSERT INTO up_item (name, description, desc_short, price, is_active, created_at, updated_at)
            VALUES (:name, :description, :desc_short, :price, 1, NOW(), NOW())
        ");
        $stmt->execute([
            ':name' => $data['name'],
            ':description' => $data['description'],
            ':desc_short' => $data['desc_short'],
            ':price' => $data['price'],
        ]);

        return $this->pdo->lastInsertId();

    }

    public function updateProduct(Product $product, array $changedFields): void
    {

        if (empty($changedFields)) 
        {
            return;
        }

        $sql = "UPDATE up_item SET ";
        $updates = [];
        $params = [':id' => $product->getId()];

        if (array_key_exists('name', $changedFields))
        {
            $updates[] = "name = :name";
            $params[':name'] = $changedFields['name'];
        }

        if (array_key_exists('description', $changedFields))
        {
            $updates[] = "description = :description";
            $params[':description'] = $changedFields['description'];
        }

        if (array_key_exists('desc_short', $changedFields))
        {
            $updates[] = "desc_short = :desc_short";
            $params[':desc_short'] = $changedFields['desc_short'];
        }

        if (array_key_exists('price', $changedFields))
        {
            $updates[] = "price = :price";
            $params[':price'] = $changedFields['price'];
        }

        if (array_key_exists('is_active', $changedFields))
        {
            $updates[] = "is_active = :is_active";
            $params[':is_active'] = $changedFields['is_active'] ? 1 : 0;
        }

        $updates[] = "updated_at = NOW()";

        $sql .= implode(', ', $updates) . " WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

    }

    public function findByTagId(int $limit, int $offset, int $tagId, bool $showOnlyActive = true)
    {

        $sql = "
        SELECT 
            i.id, i.name, i.price, i.is_active, i.created_at, i.desc_short, i.description,
            img.path AS main_image_path
        FROM up_item i 
        LEFT JOIN up_image img ON i.id = img.item_id AND img.is_main = 1
        LEFT JOIN up_item_tag it ON i.id = it.item_id WHERE it.tag_id = :tagId 
        ";

        if ($showOnlyActive)
        {
            $sql .= "AND i.is_active = 1 ";
        }

        $sql .= "ORDER BY i.id ASC LIMIT :limit OFFSET :offset";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(':tagId', $tagId, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($products))
        {
            $productIds = array_column($products, 'id');
            $additionalImages = $this->findAdditionalImages($productIds);
            $imagesByProduct = [];

            foreach ($additionalImages as $image)
            {
                $imagesByProduct[$image['item_id']][] = $image['path'];
            }

            return array_map(
                function ($productData) use ($imagesByProduct) {
                    $product = Product::fromDatabase($productData);
                    $additionalImages = $imagesByProduct[$productData['id']] ?? [];
                    $product->setAdditionalImagePaths($additionalImages);
                    return $product;
                },
                $products
            );  
        }
        
        return [];

    }

}