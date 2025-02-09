<?php

namespace Models;

final class ProductListDTO
{
    public function __construct
    (
        public readonly int $id,
        public readonly string $name,
        public readonly string $desc_short,
        public readonly string $price,
        public readonly int $is_active,
        public readonly ?string $main_image_path
    ) {}
}

final class ProductDetailDTO
{

    public function __construct
    (
        public readonly int $id,
        public readonly string $name,
        public readonly string $price,
        public readonly string $description
    ) {}
}

class Product
{

    private int $id;
    private string $name;
    private string $description;
    private string $desc_short;
    private ?string $price;
    private bool $is_active;
    private string $created_at;
    private string $updated_at;
    private ?string $main_image_path;

    public static function fromDatabase(array $row): self
    {

        $product = new self();

        $product->id = $row['id'];
        $product->name = $row['name'];
        $product->description = $row['description'] ?? '';
        $product->desc_short = $row['desc_short'] ?? '';
        $product->price = $row['price'];
        $product->is_active = $row['is_active'] ?? '';
        $product->main_image_path = $row['main_image_path'] ?? null;
//        $product->created_at = $row['created_at'] ?? 'Y-m-d H:i:s';
//        $product->updated_at = $row['updated_at'] ?? 'Y-m-d H:i:s';
        $product->created_at = $row['created_at'] ?? date('Y-m-d H:i:s');
        $product->updated_at = $row['updated_at'] ?? date('Y-m-d H:i:s');

        return $product;
    }

    public function activate(): void
    {
        $this->is_active = true;
        $this->updated_at = date('Y-m-d H:i:s');
    }
    public function deactivate(): void
    {
        $this->is_active = false;
        $this->updated_at = date('Y-m-d H:i:s');
    }

    public function toListDTO(): ProductListDTO
    {
        return new ProductListDTO
        (
            $this->id,
            $this->name,
            $this->desc_short,
            $this->price,
            $this->is_active,
            $this->main_image_path
        );
    }

    public function toDetailDTO(): ProductDetailDTO
    {
        return new ProductDetailDTO
        (
            $this->id,
            $this->name,
            $this->price,
            $this->description,
        );
    }



//... описание и тд как DTO...

}
