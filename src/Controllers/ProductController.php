<?php

namespace Controllers;

;
use Core\Services\ProductService;
use Core\Services\RatingService;
use Core\Database\MySQLDatabase;
use Core\Repositories\ProductRepository;
use Core\Repositories\RatingRepository;
use Controllers\BaseController; 
use Utils\RatingHelper;
use PDOException;

class ProductController extends BaseController
{
    
    private ProductService $productService;
    private RatingService $ratingService;

    public function __construct()
    {
        $database = new MySQLDatabase();
        $pdo = $database->getConnection();

        $productRepository = new ProductRepository($pdo);
        $ratingRepository = new RatingRepository($pdo);

        $this->productService = new ProductService($productRepository, $ratingRepository);
        $this->ratingService = new RatingService($ratingRepository);
    }

    public function show($id)
    {

        $product = $this->productService->getProductByid((int)$id);

        $rating = $this->ratingService->getProductRating($product->id);

        $averageRating = $rating ? $rating->averageRating : 0;
        $totalReviews = $rating ? $rating->totalReviews : 0;

        $this->render
        (
            'product/detail',
            [
                'product' => $product,
                'averageRating' => $averageRating,
                'totalReviews' => $totalReviews
            ]
        );
    }
}
