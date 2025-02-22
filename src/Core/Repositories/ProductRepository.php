<?php

namespace Core\Repositories;

use PDO;
use Models\Product\Product;

class ProductRepository
{

    private PDO $pdo;

    public function __construct(PDO $pdo)
    {

        $this->pdo = $pdo;

    }

	public function getProducts
	(
		int $limit,
		int $offset,
		array $filters = [],
		bool $countOnly = false,

	): array | int
	{

		$params = [];

		$sql = $countOnly 
			? 	"SELECT COUNT(DISTINCT i.id) FROM up_item i "
			: 	"SELECT i.id, i.name, i.price, i.is_active, i.created_at, i.desc_short, " . 
				(!empty($filters['isAdmin']) ? "i.description, " : "") . 
				"img.path AS main_image_path
				FROM up_item i
				LEFT JOIN up_image img ON i.id = img.item_id AND img.is_main = 1
				";

		if (!empty($filters['tagIds'])) 
		{
			$sql .= "JOIN up_item_tag it ON i.id = it.item_id ";
		}

		$sql .= "WHERE 1=1 ";

		if (!empty($filters['showOnlyActive']) && $filters['showOnlyActive'] !== true) 
		{
			$sql .= "AND i.is_active = 1 ";
		}

		if (!empty($filters['tagIds'])) {
			$this->addTagFilterToQuery($sql, $params, $filters['tagIds']);
		}
		
		if (!empty($filters['query'])) {
			$searchOptions = 
			[
				'searchInTags' => !empty($filters['searchInTags']) && $filters['searchInTags'] !== false,
				'productIdsByTagIds' => $filters['productIdsByTagIds'] ?? []
			];
			$this->addSearchFilterToQuery($sql, $params, $filters['query'], $searchOptions);	
		}
		
		if (isset($filters['minPrice']) || isset($filters['maxPrice'])) {
			$this->addPriceFilterToQuery($sql, $params, 
				$filters['minPrice'] ?? null, 
				$filters['maxPrice'] ?? null
			);
		}

		if (!$countOnly) {
			$sql .= "ORDER BY i.id ASC LIMIT :limit OFFSET :offset";
			$params[':limit'] = $limit;
			$params[':offset'] = $offset;
		}
		
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute($params);
		
		if ($countOnly) {
			return (int) $stmt->fetchColumn();
		}
		
		$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

		return $this->mapProductsWithImages($products);

	}

	public function findAllPaginated
	(
		int $limit, int $offset,
		?array $tagIds = null,
		?int $minPrice = null, ?int $maxPrice = null,
		bool $showOnlyActive = true
	): array
    {

		$filters =
		[
			'tagIds' => $tagIds ? array_slice($tagIds, 0, 3) : null,
			'minPrice' => $minPrice,
			'maxPrice' => $maxPrice,
			'showOnlyActive' => $showOnlyActive,
		];
        
		return $this->getProducts($limit, $offset, $filters);
		
    }

	public function findAllPaginatedAdmin(int $limit, int $offset): array
	{

		$filters = 
		[
			'isAdmin' => true, 
			'showOnlyActive' => false,
		];

		return $this->getProducts($limit, $offset, $filters);

	}

	private function mapProductsWithImages(array $productsData): array
	{

		if (empty($productsData))
		{
			return [];
		}

		$productIds = array_column($productsData, 'id');
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
			$productsData
		);

	}

	private function addTagFilterToQuery(string &$sql, array &$params, ?array $tagIds = null): void
	{

		if(!empty($tagIds))
		{
			$placeholders = implode(',', array_map(fn($index) => ":tagId$index", range(0, count($tagIds) - 1)));
			$sql .= "AND i.id IN (
				SELECT item_id
				FROM up_item_tag
				WHERE tag_id IN ($placeholders)
				GROUP BY item_id
        	) ";

			foreach ($tagIds as $index => $tagId) 
			{
				$params[":tagId$index"] = $tagId;
			}

		}

	}

	private function addSearchFilterToQuery(string &$sql, array &$params, ?string $query, ?array $options = null): void
	{

		if ($query !== null && trim($query) !== '') 
		{
			if (!empty($options['searchInTags']) && !empty($options['productIdsByTagIds']))
			{
				$tagPlaceholders = [];
				foreach($options['productIdsByTagIds'] as $index => $id)
				{
					$paramName = ":tagProductId$index";
					$tagPlaceholders[] = $paramName;
					$params[$paramName] = $id;
				}

				$sql .= "AND (
					LOWER(i.name) LIKE LOWER(:query1) 
					OR LOWER(i.description) LIKE LOWER(:query2)
					OR i.id IN (" . implode(',', $tagPlaceholders) . ")
				) ";
			}
			else
			{
				$sql .= "AND (LOWER(i.name) LIKE LOWER(:query1) OR LOWER(i.description) LIKE LOWER(:query2))"; 
			}
			$params[':query1'] = '%' . str_replace('%', '\%', $query) . '%';
			$params[':query2'] = '%' . str_replace('%', '\%', $query) . '%';	
		}

	}

	private function addPriceFilterToQuery(string &$sql, array &$params, ?int $minPrice, ?int $maxPrice): void
	{

		if ($minPrice !== null) {
			$sql .= "AND i.price >= :minPrice ";
			$params[':minPrice'] = $minPrice;
		}

		if ($maxPrice !== null) {
			$sql .= "AND i.price <= :maxPrice ";
			$params[':maxPrice'] = $maxPrice;
		}

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

    public function getTotalCount
	(
		?array $tagIds = null, 
		?string $query = null,
		?int $minPrice = null, 
		?int $maxPrice = null
	): int
    {

		$filters = 
		[
			'tagIds' => $tagIds,
			'query' => $query,
			'minPrice' => $minPrice,
			'maxPrice' => $maxPrice,
			'showOnlyActive' => true,
		];

		return $this->getProducts(0,0,$filters,true);

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
		if (empty($changedFields)) {
			return;
		}

		$sql = "UPDATE up_item SET ";
		$updates = [];
		$params = [':id' => $product->getId()];

		if (array_key_exists('name', $changedFields)) {
			$updates[] = "name = :name";
			$params[':name'] = $changedFields['name'];
		}

		if (array_key_exists('description', $changedFields)) {
			$updates[] = "description = :description";
			$params[':description'] = $changedFields['description'];
		}

		if (array_key_exists('desc_short', $changedFields)) {
			$updates[] = "desc_short = :desc_short";
			$params[':desc_short'] = $changedFields['desc_short'];
		}

		if (array_key_exists('price', $changedFields)) {
			$updates[] = "price = :price";
			$params[':price'] = $changedFields['price'];
		}

		if (array_key_exists('is_active', $changedFields)) {
			$updates[] = "is_active = :is_active";
			$params[':is_active'] = $changedFields['is_active'] ? 1 : 0;
		}

		$updates[] = "updated_at = NOW()";

		$sql .= implode(', ', $updates) . " WHERE id = :id";

		$stmt = $this->pdo->prepare($sql);
		$stmt->execute($params);
	}

	public function findIdsByTagIds(array $tagIds): array 
    {

        $placeholders = rtrim(str_repeat('?,', count($tagIds)), ',');
        
        $sql = "
        SELECT DISTINCT
            item_id
        FROM up_item_tag
        WHERE tag_id IN ($placeholders)
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute($tagIds);

        $productIds = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return (!empty($productIds)) ? array_column($productIds, 'item_id') : [];

    }

	public function findWithAdditionalImages(array $products):array
    {

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

	public function findPrices(array $products): array 
	{

		if (!empty($products))
		{

		}

		return [];
	}

	public function findByIds(array $productIds, bool $showOnlyActive = true): array
    {

        if (empty($productIds))
        {
            return [];
        }

        $productIds = array_values($productIds);
        $placeholders = rtrim(str_repeat('?,', count($productIds)), ',');

        $sql = "
        SELECT 
            i.id, i.name, i.price, i.is_active, i.created_at, i.desc_short, i.description,
            img.path AS main_image_path
        FROM up_item i
        LEFT JOIN up_image img ON i.id = img.item_id AND img.is_main = 1
        WHERE i.id in ($placeholders)
        ";

        if ($showOnlyActive)
        {
            $sql .= " AND i.is_active = 1";
        }

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute($productIds);

        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $products;

    }

	public function findByNameAndDescription(string $query, bool $showOnlyActive = true): array
    {

        $sql = "
        SELECT 
            i.id, i.name, i.price, i.is_active, i.created_at, i.desc_short, i.description,
            img.path AS main_image_path
        FROM up_item i
        LEFT JOIN up_image img ON i.id = img.item_id AND img.is_main = 1
        WHERE (LOWER(i.name) LIKE LOWER(:query1)) OR (LOWER(i.description) LIKE LOWER(:query2))
        ";

        if ($showOnlyActive)
        {
            $sql .= " AND i.is_active = 1";
        }

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindValue(':query1', $query, PDO::PARAM_STR);
        $stmt->bindValue(':query2', $query, PDO::PARAM_STR);

        $stmt->execute();
        
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $results;

    }
}
