<?php

namespace Controllers;

// use PDO;

class ProductController
{

    /* ТЕСТ */
    private static $products = 
    [
        1 => ['name' => 'Роза', 'price' => 249],
        2 => ['name' => 'Пион', 'price' => 499],
        3 => ['name' => 'Пион XXL', 'price' => 1399],
        4 => ['name' => 'Тюльпан в ассортименте', 'price' => 179],
        6 => ['name' => 'Хризантема', 'price' => 89],
        100 => ['name' => 'Фиалка', 'price' => 49],
    ];

    public static function show($id)
    {
        require_once __DIR__ . '/../Views/product/show.php';

        echo "ID товара: " . $id . "<br>"; 

        if (isset(self::$products[$id]))
        {
            echo "Название: " . self::$products[$id]['name'] . "<br>";
            echo "Цена: " . self::$products[$id]['price'] . "₽";
        }
        else
        {
            echo "Продукт не найден";
        }
    }
    /* ТЕСТ */
    
}