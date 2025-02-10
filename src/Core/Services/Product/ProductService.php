<?php

namespace Core\Services\Product;

use Core\Services\Product\ProductRepository;

class ProductService
{

    private ProductRepository $repository;

    public function __construct(ProductRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getPaginatedProducts(int $page, int $itemsPerPage, $tagId): array
    {
        $offset = ($page - 1) * $itemsPerPage;
        $products = $this->repository->findAllPaginated($itemsPerPage, $offset, $tagId);

        return array_map
        (
            fn($product) => $product->toListDTO(), $products
        );
    }

    public function getProductByid(int $id)
    {
        $product = $this->repository->findProductById($id);
        return $product->toDetailDTO();
    }

    public function getTotalPages(int $itemsPerPage, ?int $tagId = null): int
    {
        $totalProducts = $this->repository->getTotalCount($tagId);
        return ceil($totalProducts / $itemsPerPage);
    }


    public function adminGetPaginatedProducts(int $currentPage, int $itemsPerPage, bool $showOnlyActive = true): array
    {
        $offset = ($currentPage - 1) * $itemsPerPage;
        return $this->repository->findAllPaginated($itemsPerPage, $offset, null, $showOnlyActive);
    }

    public function adminToggleStatus(array $productIds, bool $newStatus): void
    {
        if (empty($productIds)) {
            throw new \InvalidArgumentException('No products to update');
        }

        $this->repository->updateStatus($productIds, $newStatus);
    }


}
