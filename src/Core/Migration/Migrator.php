<?php

namespace Core\Migration;

use PDO;
use PDOException;

class Migrator
{

    private PDO $pdo;
    private string $migrationsPath;

    public function __construct(PDO $pdo, string $migrationsPath)
    {

        self::$pdo = $pdo;
        self::$migrationsPath = $migrationsPath;

    }

    public function migrate(): void
    {

        $stmt = self::$pdo->query("SELECT migration_name FROM migrations ORDER BY id DESC LIMIT 1");
        $lastExecutedMigration = $stmt->fetchColumn() ?: null;

        $allMigrations = scandir(self::$migrationsPath);
        $newMigrations = [];

        foreach ($allMigrations as $migration)
        {
            if (pathinfo($migration, PATHINFO_EXTENSION) === 'sql')
            {
                if (!$lastExecutedMigration || strcmp($migration, $lastExecutedMigration) > 0)
                {
                    $newMigrations[] = $migration;
                }
            }
        }

        sort($newMigrations);

        foreach ($newMigrations as $migration)
        {
            echo "Выполняется миграция: $migration\n";
            $sql = file_get_contents(self::$migrationsPath . DIRECTORY_SEPARATOR . $migration);

            try
            {
                self::$pdo->exec($sql);
                $stmt = self::$pdo->prepare("INSERT INTO migrations (migration_name) VALUES (:migration_name)");
                $stmt->execute(['migration_name' => $migration]);
                echo "Миграция $migration выполнена успешно\n";
            }
            catch (PDOException $e)
            {
//              echo "Ошибка выполнения миграции $migration: " . $e->getMessage() . "\n";
                error_log("Ошибка выполнения миграции $migration: " . $e->getMessage());
                exit(1);
            }
        }

        if (empty($newMigrations))
        {
            echo "Нет новых миграций.\n";
        }
        
    }

}
