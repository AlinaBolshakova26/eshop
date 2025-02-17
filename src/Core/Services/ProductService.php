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

    public function getPaginatedProducts(int $page, int $itemsPerPage, ?string $query, ?array $tagId): array
    {

        $offset = ($page - 1) * $itemsPerPage;
        $products = $this->repository->findAllPaginated($itemsPerPage, $offset, $query, $tagId);

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

    public function getTotalPages(int $itemsPerPage, ?array $tagId = null, ?string $query = null): int
    {

        $totalProducts = $this->repository->getTotalCount($tagId, $query);
        
        return ceil($totalProducts / $itemsPerPage);

    }

    public function adminGetPaginatedProducts(int $currentPage, int $itemsPerPage, bool $showOnlyActive = true): array
    {

        $offset = ($currentPage - 1) * $itemsPerPage;

        return $this->repository->findAllPaginatedAdmin($itemsPerPage, $offset, null, $showOnlyActive);

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

    public function searchProducts(int $page, int $itemsPerPage, array $tags, string $query): array 
    {

        $offset = ($page - 1) * $itemsPerPage;
        $query = trim($query, '%');
        
        if ($query === '')
        {
            return [];
        }

        $pattern = '/' . preg_quote($query, '/') . '/ui';
        $results = [];
        $addedProductIds = [];
        $allProducts = $this->repository->findAll();

        foreach ($tags as $tag) 
        {
            if (preg_match($pattern, $tag->getName())) 
            {
                $tagProducts = $this->repository->findByTagId($itemsPerPage, $offset, $tag->getId());
                foreach ($tagProducts as $product) 
                {
                    if (!in_array($product->getId(), $addedProductIds)) 
                    {
                        $results[] = $product;
                        $addedProductIds[] = $product->getId();
                    }
                }            
            }
        }

        foreach ($allProducts as $product) 
        {
            if (preg_match($pattern, $product->getName())) 
            {
                if (!in_array($product->getId(), $addedProductIds)) 
                {
                    $results[] = $product;
                    $addedProductIds[] = $product->getId();
                }
            }
            elseif (preg_match($pattern, $product->getDescription())) 
            {
                if (!in_array($product->getId(), $addedProductIds)) 
                {
                    $results[] = $product;
                    $addedProductIds[] = $product->getId();
                }
            }
        }

        $totalResults = count($results);
        $paginatedResults = array_slice($results, $offset, $itemsPerPage);

        return [
            'items' => array_map(function($product) {
                return $product->toListDTO();
            }, $paginatedResults),
            'total' => $totalResults
        ];

    }

    public function searchAdminProducts(int $page, int $itemsPerPage, string $query): array 
    {

        $offset = ($page - 1) * $itemsPerPage;
        $query = trim($query, '%');
        
        if ($query === '')
        {
            return [];
        }

        $pattern = '/' . preg_quote($query, '/') . '/ui';
        $results = [];
        $addedProductIds = [];
        $allProducts = $this->repository->findAll(false);

        foreach ($allProducts as $product) 
        {
            if (preg_match($pattern, $product->getName())) 
            {
                if (!in_array($product->getId(), $addedProductIds)) 
                {
                    $results[] = $product;
                    $addedProductIds[] = $product->getId();
                }
            }
            elseif (preg_match($pattern, $product->getDescription())) 
            {
                if (!in_array($product->getId(), $addedProductIds)) 
                {
                    $results[] = $product;
                    $addedProductIds[] = $product->getId();
                }
            }
        }

        $totalResults = count($results);
        $paginatedResults = array_slice($results, $offset, $itemsPerPage);

        return [
            'items' => $paginatedResults,
            'total' => $totalResults
        ];

    }

}