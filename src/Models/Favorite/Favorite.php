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
    
}
