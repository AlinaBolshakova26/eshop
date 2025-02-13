<?php

namespace Core\Services;

use Core\Repositories\ProductRepository;
use Core\Repositories\TagRepository;
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

}
