<?php

namespace Core\Services\Product;

use Models\Product;

class ProductService
{

    private ProductRepository $repository;

    public function __construct(ProductRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getPaginatedProducts(int $page, int $itemsPerPage): array
    {
        $offset = ($page - 1) * $itemsPerPage;
        $products = $this->repository->findAllPaginated($itemsPerPage, $offset);
       
        return array_map
        (
            fn($product) => $product->toListDTO(), $products
        );
    }

    public function getProductByid(int $id)
    {
        $product = $this->repository->findById($id);

        return $product->toDetailDTO();
    }

    public function getTotalPages(int $itemsPerPage): int
    {
        $totalProducts = $this->repository->getTotalCount();
        return ceil($totalProducts / $itemsPerPage);
    }

}