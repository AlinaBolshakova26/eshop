<?php

namespace Core\Services;

use Core\Repositories\CartRepository;

class CartService
{
    private CartRepository $cartRepository;

    public function __construct(CartRepository $cartRepository)
    {
        $this->cartRepository = $cartRepository;
    }

    public function getCartItems(int $userId): array
    {
        return $this->cartRepository->getCartItemsByUserId($userId);
    }

    public function addToCart(int $userId, int $itemId, int $quantity): bool
    {
        if ($quantity < 1)
        {
            throw new \InvalidArgumentException("Количество должно быть не менее 1");
        }
        return $this->cartRepository->addItem($userId, $itemId, $quantity);
    }

    public function updateCartItem(int $userId, int $itemId, int $quantity): bool
    {
        if ($quantity < 1)
        {
            throw new \InvalidArgumentException("Количество должно быть не менее 1");
        }
        return $this->cartRepository->updateItem($userId, $itemId, $quantity);
    }


    public function removeCartItem(int $userId, int $itemId): bool
    {
        return $this->cartRepository->removeItem($userId, $itemId);
    }

    public function clearCart(int $userId): bool
    {
        return $this->cartRepository->clearCart($userId);
    }
}