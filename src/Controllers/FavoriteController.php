<?php
namespace Controllers;

use Controllers\BaseController;
use Core\Database\MySQLDatabase;
use Core\Repositories\FavoriteRepository;
use Core\Services\FavoriteService;
use Models\Favorite\Favorite;
;

class FavoriteController extends BaseController 
{
    
    private FavoriteService $favoriteService;

    public function __construct() 
    {

        $database = new MySQLDatabase();
        $pdo = $database->getConnection();

        $favoriteRepository = new FavoriteRepository($pdo);
        $this->favoriteService = new FavoriteService($favoriteRepository);

    }

    public function data() 
    {

        if (!isset($_SESSION['user_id'])) 
        {
            header("Content-Type: application/json");
            echo json_encode
            (
         [
                    'success' => false,
                    'message' => 'Пользователь не авторизован'
                ]
            );
            exit;
        }

        $userId = $_SESSION['user_id'];
        $favorites = $this->favoriteService->getFavorites($userId);

        $favoriteIds = [];
        foreach ($favorites as $favorite) 
        {
            $favoriteIds[] = (string)$favorite->item_id;
        }

        header("Content-Type: application/json");
        echo json_encode
        (
     [
                'success' => true,
                'favorites' => $favoriteIds
            ]
        );
        exit;

    }
    
    public function toggle() 
    {

        if (!isset($_SESSION['user_id'])) 
        {
            header("Content-Type: application/json");
            echo json_encode(['success' => false, 'message' => 'Пользователь не авторизован']);
            exit;
        }

        $userId = $_SESSION['user_id'];
        $itemId = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;

        if ($itemId <= 0) 
        {
            header("Content-Type: application/json");
            echo json_encode(['success' => false, 'message' => 'Неверный id товара']);
            exit;
        }

        $result = $this->favoriteService->toggleFavorite($userId, $itemId);
        header("Content-Type: application/json");
        echo json_encode(['success' => $result]);
        exit;

    }

    public function index() 
    {

        $this->checkLogin();

        $userId = $_SESSION['user_id'];
        $favorites = $this->favoriteService->getFavorites($userId);

        $this->render
        (
            'favorites/index', 
            [
                'favorites' => $favorites
            ]
        );
    }

    public function remove($id) 
    {

        if (!isset($_SESSION['user_id'])) 
        {
            header("Content-Type: application/json");
            echo json_encode(['success' => false, 'message' => 'Пользователь не авторизован']);
            exit;
        }

        $userId = $_SESSION['user_id'];
        $productId = (int)$id;

        $favoriteModel = new Favorite();
        $success = $this->favoriteService->removeFavorite($userId, $productId);

        header("Content-Type: application/json");
        echo json_encode(['success' => $success]);
        exit;
        
    }

}
