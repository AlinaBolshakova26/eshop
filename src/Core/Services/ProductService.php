<?php

namespace Core\Services;

use Core\Repositories\ProductRepository;
use Core\Repositories\RatingRepository;
use Models\Rating\RatingListDTO;
use Models\Product\Product;

class ProductService
{

    private ProductRepository $repository;
    private RatingRepository $ratingRepository;

    public function __construct(ProductRepository $repository, RatingRepository $ratingRepository)
    {

        $this->repository = $repository;
        $this->ratingRepository = $ratingRepository;

    }

    public function getPaginatedProducts(
        int $page,
        int $itemsPerPage,
        ?array $tagIds,
        ?int $minPrice = null,
        ?int $maxPrice = null
    ): array 
    {

        $offset = ($page - 1) * $itemsPerPage;
        // $products = $this->repository->findAllPaginated($itemsPerPage, $offset, $tagIds, $minPrice, $maxPrice,true);

        $products = $this->repository->findAllPaginated(
            $itemsPerPage,
            $offset,
            $tagIds,
            $minPrice,
            $maxPrice
        );

        if (empty($products)) {
            return [];
        }

        $productIds = array_map(fn(Product $p) => $p->getId(), $products);

        $ratings = $this->ratingRepository->getAverageRatingsForProducts($productIds);

        return array_map(
            function(Product $product) use ($ratings)
            {
                $productWithRating = $product->withRating(
                    $ratings[$product->getId()] ?? new RatingListDTO(0, 0)
                );

                return $productWithRating->toListDTO();
            },
            $products
        );
    }

    // private function addRatingsToProducts(array $products): array
    // {
    //     $productIds = array_map(fn($p) => $p->getId(), $products);
    //     $ratings = $this->ratingRepository->getAverageRatingsForProducts($productIds);

    //     return array_map(fn($product) => $product->withRating(
    //         $ratings[$product->getId()] ?? null
    //     ), $products);
    // }

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

    public function adminGetPaginatedProducts(int $currentPage, int $itemsPerPage): array
    {

        $offset = ($currentPage - 1) * $itemsPerPage;

        return $this->repository->findAllPaginatedAdmin($itemsPerPage, $offset);

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

    public function searchProducts(int $page, int $itemsPerPage, array $productIdsByTagIds, string $query, bool $showOnlyActive = true, ?int $minPrice = null, ?int $maxPrice = null): array 
    {

        $filters = 
        [
            'query' => $query,
            'showOnlyActive' => $showOnlyActive,
            'searchInTags' => true,
            'productIdsByTagIds' => !empty($productIdsByTagIds) ? $productIdsByTagIds : [], 
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice,
        ];

        $offset = ($page - 1) * $itemsPerPage;

        $totalProducts = $this->repository->getProducts(0, 0, $filters, true);

        $products = $this->repository->getProducts($itemsPerPage, $offset, $filters);

        if ($showOnlyActive)
        {
            $products = array_map(fn($product) => $product->toListDTO(), $products);
        }

        return
        [
            'products' => $products,
            'totalProducts' => $totalProducts,
        ];

    }

	public function getAllProducts(): array
	{
		return $this->repository->getAllProducts();
	}

}