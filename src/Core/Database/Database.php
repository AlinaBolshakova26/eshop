<?php

namespace Core\Database;

class Database
{
    
    private static ?\PDO $pdo = null;

    public function getConnection(): \PDO
    {

        if (self::$pdo === null) 
        {
            $config = require __DIR__ . '/../../config/database.php';
            self::$pdo = new \PDO
            (
                "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}",
                $config['user'],
                $config['password'],
                [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
            );
        }
        
        return self::$pdo;

    }

}
