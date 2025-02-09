<?php

namespace Models;

use Core\Database\MySQLDatabase;
use PDO;

class Order
{
    private int $id;
    private int $user_id;
    private int $item_id;
    private float $price;
    private string $address;
    private string $status;
    private string $created_at;
    private string $updated_at;

    /**
     * Конструктор заказа
     *
     * @param int $user_id
     * @param int $item_id
     * @param float $price
     * @param string $address
     * @param string $status
     */
    public function __construct(int $user_id, int $item_id, float $price, string $address, string $status = 'Создан')
    {
        $this->user_id = $user_id;
        $this->item_id = $item_id;
        $this->price = $price;
        $this->address = $address;
        $this->status = $status;
    }

    /**
     *
     * @return bool
     */
    public function save(): bool
    {
        $db = (new MySQLDatabase())->getConnection();
        $stmt = $db->prepare("INSERT INTO up_order (user_id, item_id, price, address, status) VALUES (:user_id, :item_id, :price, :address, :status)");
        return $stmt->execute([
            'user_id' => $this->user_id,
            'item_id' => $this->item_id,
            'price' => $this->price,
            'address' => $this->address,
            'status' => $this->status
        ]);
    }
}
