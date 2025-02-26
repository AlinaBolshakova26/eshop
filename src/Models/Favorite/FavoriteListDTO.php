<?php

namespace Models\Favorite;

class FavoriteListDTO 
{
    
    public int $id;
    public int $user_id;
    public int $item_id;
    public string $created_at;

    public ?string $product_name;
    public ?float $product_price;
    public ?string $main_image;

    public function __construct(array $data) 
    {
        $this->id            = (int)($data['id'] ?? 0);
        $this->user_id       = (int)($data['user_id'] ?? 0);
        $this->item_id       = (int)($data['item_id'] ?? 0);
        $this->created_at    = $data['created_at'] ?? '';
        $this->product_name  = $data['product_name'] ?? null;
        $this->product_price = isset($data['product_price']) ? (float)$data['product_price'] : null;
        $this->main_image    = $data['main_image'] ?? null;
    }

}
