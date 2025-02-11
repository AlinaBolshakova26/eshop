<?php
namespace Core\Database;

use PDO;
use PDOException;
use Exception;

require_once __DIR__ . '/../../config/config_database.php';

class MySQLDatabase extends Database
{

    private ?PDO $connection = null;

    public function getConnection(): PDO
    {

        if ($this->connection === null)
        {
            $dbHost = option('DB_HOST');
            $dbUser = option('DB_USER');
            $dbPassword = option('DB_PASSWORD');
            $dbName = option('DB_NAME');
            $dsn = "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4";

            try
            {
                $this->connection = new PDO($dsn, $dbUser, $dbPassword, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            }
            catch (PDOException $e)
            {
                throw new Exception("Database connection error: " . $e->getMessage());
            }
        }

        return $this->connection;

    }

    public function disconnect(): void
    {

        $this->connection = null;
        
    }
}