<?php
namespace Models\Favorite;

use Core\Database\MySQLDatabase;
use PDO;

class Favorite
{
    private PDO $db;

    public function __construct()
    {
        $this->db = (new MySQLDatabase())->getConnection();
    }

    public function toggle(int $userId, int $productId): bool
    {

        $stmt = $this->db->prepare
        ("
            SELECT 
                id 
            FROM up_favorites 
            WHERE user_id = :user_id 
            AND item_id = :item_id
        ");
        $stmt->execute
        (
            [
                'user_id' => $userId, 
                'item_id' => $productId
            ]
        );
        $favorite = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($favorite) 
        {
            return $this->remove($userId, $productId);
        } 
        else
        {
            return $this->add($userId, $productId);
        }
        
    }

    public function add(int $userId, int $productId): bool 
    {

        $stmt = $this->db->prepare
        ("
            INSERT INTO up_favorites (user_id, item_id) 
            VALUES (:user_id, :item_id)
        ");

        return $stmt->execute
        (
            [
                'user_id' => $userId, 
                'item_id' => $productId
            ]
        );

    }

    public function remove(int $userId, int $productId): bool 
    {
        
        $stmt = $this->db->prepare
        ("
            DELETE 
            FROM up_favorites 
            WHERE user_id = :user_id 
            AND item_id = :item_id
        ");

        return $stmt->execute
        (
            [
                'user_id' => $userId, 
                'item_id' => $productId
            ]
        );

    }

}
