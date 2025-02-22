<?php

namespace Core\Services;

use Core\Repositories\ProductRepository;
use Core\Repositories\TagRepository;
use Models\Product;
use Models\ProductListDTO;

class ProductService
{

    private ProductRepository $repository;

    public function __construct(ProductRepository $repository)
    {

        $this->repository = $repository;

    }

    public function getPaginatedProducts(int $page, int $itemsPerPage,
										 ?string $query, ?array $tagId,
										 ?int $minPrice = null, ?int $maxPrice = null): array
    {

        $offset = ($page - 1) * $itemsPerPage;
        $products = $this->repository->findAllPaginated($itemsPerPage, $offset, $query, $tagId, $minPrice, $maxPrice);

        return array_map(
            fn($product) => $product->toListDTO(), 
            $products
        );

    }

    public function getProductByid(int $id)
    {

        $product = $this->repository->findProductById($id);

        return $product->toDetailDTO();

    }

    public function getTotalPages(int $itemsPerPage, ?array $tagId = null, ?string $query = null, ?int $minPrice = null, ?int $maxPrice = null): int
    {

        $totalProducts = $this->repository->getTotalCount($tagId, $query, $minPrice, $maxPrice);
        
        return ceil($totalProducts / $itemsPerPage);

    }

    public function adminGetPaginatedProducts(int $currentPage, int $itemsPerPage, bool $showOnlyActive = true): array
    {

        $offset = ($currentPage - 1) * $itemsPerPage;

        return $this->repository->findAllPaginatedAdmin($itemsPerPage, $offset, null, null, $showOnlyActive);

    }

    public function adminGetProductByid(int $id)
    {

        return $this->repository->findProductById($id, true);

    }

    public function adminToggleStatus(array $productIds, bool $newStatus): void
    {

        if (empty($productIds)) 
        {
            throw new \InvalidArgumentException('No products to update');
        }
        $this->repository->updateStatus($productIds, $newStatus);

    }

    public function createProduct(array $data): int
    {

        return $this->repository->create($data);

    }

    public function updateProduct(int $id, array $data): void
    {

        $product = $this->repository->findProductById($id, true);

        if (!$product)
        {
            throw new \InvalidArgumentException('Product not found');
        }

        $changedFields = [];
        if ($data['name'] !== $product->getName())
        {
            $changedFields['name'] = $data['name'];
        }
        if ($data['description'] !== $product->getDescription())
        {
            $changedFields['description'] = $data['description'];
        }
        if ($data['desc_short'] !== $product->getDescShort())
        {
            $changedFields['desc_short'] = $data['desc_short'];
        }
        if ($data['price'] !== $product->getPrice())
        {
            $changedFields['price'] = $data['price'];
        }
        if ($data['is_active'] !== $product->getIsActive())
        {
            $changedFields['is_active'] = $data['is_active'];
        }

        if (!empty($changedFields))
        {
            $this->repository->updateProduct($product, $changedFields);
        }

    }

    public function getIdsByTagIds(array $tagIds):array
    {
        return (!empty($tagIds)) ? $this->repository->findIdsByTagIds($tagIds) : [];
    }

    public function searchProducts(int $page, int $itemsPerPage, array $productIdsbyTagIds, string $query, bool $showOnlyActive = true): array 
    {

        $offset = ($page - 1) * $itemsPerPage;

        $query = '%' . $query . '%';

        $productsByNameAndDescription = $this->repository->findByNameAndDescription($query, $showOnlyActive);
        
        $productIds = array_column($productsByNameAndDescription, 'id'); 
        $diff = array_diff($productIdsbyTagIds, $productIds);

        $diffProducts = $this->repository->findByIds($diff, $showOnlyActive);

        $allProducts = array_merge($productsByNameAndDescription, $diffProducts);
        $allProducts = $this->repository->findWithAdditionalImages($allProducts);

        if ($showOnlyActive)
        {
            $allProducts = array_map(fn($product) => $product->toListDTO(), $allProducts);
        }
        
        $totalProducts = count($allProducts);

        $paginatedResults = array_slice($allProducts, $offset, $itemsPerPage);

        return 
        [
            'products' => $paginatedResults,
            'totalProducts' => $totalProducts,
        ];

    }

}