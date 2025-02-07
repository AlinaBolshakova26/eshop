<?php 

namespace Models;

// класс продукта и класс productDTO

final class ProductListDTO 
{
    public function __construct
    (
        public readonly int $id,
        public readonly string $name,
        public readonly string $desc_short,
        public readonly string $price,
        public readonly int $is_active,
    ){}
}


class Product
{
    private int $id;
    private string $name;
    private string $description;
    private string $desc_short;
    private string $price;
    private int $is_active;
    private string $created_at;
    private string $updated_at;
    

    public static function fromDatabase(array $row): self
    {   
        
        $product = new self();

        $product->id = $row['id'];
        $product->name = $row['name'];
        $product->description = $row['description'] ?? '';
        $product->desc_short = $row['desc_short'];
        $product->price = $row['price'];
        $product->is_active = $row['is_active'];
        $product->created_at = $row['created_at'] ?? 'Y-m-d H:i:s';
        $product->updated_at = $row['updated_at'] ?? 'Y-m-d H:i:s';

        return $product;
    }




    public function activate(): void
    {
        $this->is_active = 1;
        $this->updated_at = date('Y-m-d H:i:s');
    }
    public function deactivate(): void
    {
        $this->is_active = 0;
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
        );
    }

    

//... описание и тд как DTO...

}