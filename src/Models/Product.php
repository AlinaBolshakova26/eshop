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
        public readonly ?string $main_image_path,
        public readonly ?array $additional_image_paths,
    ) {}

}

final class ProductDetailDTO
{

    public function __construct
    (
        public readonly int $id,
        public readonly string $name,
        public readonly string $price,
        public readonly string $description,
        public readonly ?string $main_image_path,
        public readonly ?array $additional_image_paths,
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
    private ?array $additional_image_paths;

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
        // $product->created_at = $row['created_at'] ?? 'Y-m-d H:i:s';
        // $product->updated_at = $row['updated_at'] ?? 'Y-m-d H:i:s';
        $product->created_at = $row['created_at'] ?? date('Y-m-d H:i:s');
        $product->updated_at = $row['updated_at'] ?? date('Y-m-d H:i:s');
        $product->additional_image_paths = $row['additional_image_paths'] ?? null;

        return $product;

    }

    public function setAdditionalImagePaths(?array $additional_image_paths)
    {
        $this->additional_image_paths = $additional_image_paths;
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
            $this->main_image_path,
            $this->additional_image_paths,
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
            $this->main_image_path,
            $this->additional_image_paths
        );
        
    }

    // Геттеры
    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getDescShort(): string
    {
        return $this->desc_short;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function getIsActive(): bool
    {
        return $this->is_active;
    }

    public function getCreatedAt(): string
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): string
    {
        return $this->updated_at;
    }

    public function getMainImagePath(): ?string
    {
        return $this->main_image_path;
    }

    public function getAdditionalImagePaths(): ?array
    {
        return $this->additional_image_paths;
    }

// Сеттеры
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function setDescShort(string $desc_short): void
    {
        $this->desc_short = $desc_short;
    }

    public function setPrice(?string $price): void
    {
        $this->price = $price;
    }

    public function setIsActive(bool $is_active): void
    {
        $this->is_active = $is_active;
    }

    public function setCreatedAt(string $created_at): void
    {
        $this->created_at = $created_at;
    }

    public function setUpdatedAt(string $updated_at): void
    {
        $this->updated_at = $updated_at;
    }

    public function setMainImagePath(?string $main_image_path): void
    {
        $this->main_image_path = $main_image_path;
    }
}