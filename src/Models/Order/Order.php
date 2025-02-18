<?php
namespace Models\Order;

use Core\Database\MySQLDatabase;
class Order
{
    private ?int $id;
    private int $user_id;
    private int $item_id;
    private float $price;
    private string $city;
    private string $street;
    private string $house;
    private ?string $apartment;
    private string $status = 'Создан';
    private string $created_at;
    private string $updated_at;

    public function __construct(
        int $user_id,
        int $item_id,
        float $price,
        string $city,
        string $street,
        string $house,
        ?string $apartment = null
    ) {
        $this->user_id = $user_id;
        $this->item_id = $item_id;
        $this->price = $price;
        $this->city = $city;
        $this->street = $street;
        $this->house = $house;
        $this->apartment = $apartment;
        $this->created_at = date('Y-m-d H:i:s');
        $this->updated_at = date('Y-m-d H:i:s');
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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function getItemId(): int
    {
        return $this->item_id;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getStreet(): string
    {
        return $this->street;
    }

    public function getHouse(): string
    {
        return $this->house;
    }

    public function getApartment(): ?string
    {
        return $this->apartment;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getCreatedAt(): string
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): string
    {
        return $this->updated_at;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
        $this->updated_at = date('Y-m-d H:i:s');
    }

    public function setCreatedAt(string $created_at): void
    {
        $this->created_at = $created_at;
    }

    public function setUpdatedAt(string $updated_at): void
    {
        $this->updated_at = $updated_at;
    }
}