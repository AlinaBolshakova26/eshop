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


    public function findAllPaginated(int $limit, int $offset): array
    {

        $stmt = $this->pdo->prepare
			("
				SELECT id, name, price, is_active, created_at, desc_short
				FROM up_item
				WHERE is_active = 1
				GROUP BY id
				ORDER BY created_at DESC
				LIMIT :limit OFFSET :offset
			");
        
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT );
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT );
        $stmt->execute();

        return array_map
        (
            fn($row) => Product::fromDatabase($row),
            $stmt->fetchAll(PDO::FETCH_ASSOC),
        );
    }

    public function findById(int $id)
    {

        $stmt = $this->pdo->prepare
			("
				SELECT id, name, price, description
				FROM up_item WHERE id = :id	
		    ");

        $stmt->bindValue(':id', $id, PDO::PARAM_INT );
        $stmt->execute();

        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $product ? Product::fromDatabase($product) : null;
    }

    public function getTotalCount(): int
    {
        $stmt = $this->pdo->query('SELECT COUNT(*) FROM up_item');
        return (int)$stmt->fetchColumn();
    }

}