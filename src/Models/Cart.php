<?php

namespace Models;

final class CartListDTO
{
    public function __construct(
        public readonly int $id,
        public readonly int $user_id,
        public readonly int $item_id,
        public readonly int $quantity,
        public readonly string $created_at,
        public readonly string $updated_at
    ) {}
}

class Cart
{
    private int $id;
    private int $user_id;
    private int $item_id;
    private int $quantity;
    private string $created_at;
    private string $updated_at;

    public ?string $product_name = null;
    public ?float $product_price = null;
    public ?string $product_image = null;

    public static function fromDatabase(array $row): self
    {
        $cart = new self();
        $cart->id = (int)$row['id'];
        $cart->user_id = (int)$row['user_id'];
        $cart->item_id = (int)$row['item_id'];
        $cart->quantity = (int)$row['quantity'];
        $cart->created_at = $row['created_at'] ?? '';
        $cart->updated_at = $row['updated_at'] ?? '';
        $cart->product_name  = $row['product_name']  ?? null;
        $cart->product_price = isset($row['product_price']) ? (float)$row['product_price'] : null;
        $cart->product_image = $row['product_image'] ?? null;

        return $cart;
    }

    public function getId(): int
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

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function toListDTO(): CartListDTO
    {
        return new CartListDTO(
            $this->id,
            $this->user_id,
            $this->item_id,
            $this->quantity,
            $this->created_at,
            $this->updated_at
        );
    }
}