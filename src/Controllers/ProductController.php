<?php

namespace Controllers;

use Core\View;
use Core\Services\ProductService;
use Core\Services\RatingService;
use Core\Database\MySQLDatabase;
use Core\Repositories\ProductRepository;
use Core\Repositories\RatingRepository;
use Utils\RatingHelper;
use PDOException;

class ProductController
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

        try
        {
            $product = $this->productService->getProductByid((int)$id);

            if (!$product)
            {
                http_response_code(404);
                echo '404 Not Found';
                return;
            }

            $rating = $this->ratingService->getProductRating($product->id);

            $averageRating = $rating ? $rating->averageRating : 0;
            $totalReviews = $rating ? $rating->totalReviews : 0;

            $content = View::make
            (__DIR__ . '/../Views/product/detail.php', 
        [
                    'product' => $product,
                    'averageRating' => $averageRating,
                    'totalReviews' => $totalReviews
                ]
            );

            echo View::make
            (__DIR__ . '/../Views/layouts/main_template.php', 
        [
                    'content' => $content
                ]
            );
        }
        catch (PDOException $e)
        {
            error_log("Database error: " . $e->getMessage());
            echo "Произошла ошибка при загрузке товара.";
        }

    }
    
}