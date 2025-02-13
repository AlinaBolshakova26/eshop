<?php

namespace Models;

use Core\Database\MySQLDatabase;

class Order
{
    private int $user_id;
    private int $item_id;
    private float $price;
    private string $city;
    private string $street;
    private string $house;
    private ?string $apartment;

    public function __construct(int $user_id, int $item_id, float $price, string $city, string $street, string $house, ?string $apartment)
    {
        $this->user_id = $user_id;
        $this->item_id = $item_id;
        $this->price = $price;
        $this->city = $city;
        $this->street = $street;
        $this->house = $house;
        $this->apartment = $apartment;
    }

    public function saveInDb(): bool
    {
        $db = (new MySQLDatabase())->getConnection();
        $stmt = $db->prepare("
            INSERT INTO up_order (user_id, item_id, price, city, street, house, apartment, status) 
            VALUES (:user_id, :item_id, :price, :city, :street, :house, :apartment, 'Создан')
        ");

        return $stmt->execute([
            'user_id' => $this->user_id,
            'item_id' => $this->item_id,
            'price' => $this->price,
            'city' => $this->city,
            'street' => $this->street,
            'house' => $this->house,
            'apartment' => $this->apartment
        ]);
    }
}
