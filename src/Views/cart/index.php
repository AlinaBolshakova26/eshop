<div class="container mt-5">
    <?php if (!empty($cartItems)): ?>
        <div class="row">
            <?php foreach ($cartItems as $item): ?>
                <div class="col-md-3 col-sm-6 mb-4">
                    <div class="card h-100">
                        <?php if (!empty($item->product_image)): ?>
                            <img src="<?= htmlspecialchars($item->product_image ?? '') ?>"
                                 class="card-img-top"
                                 alt="<?= htmlspecialchars($item->product_name ?? 'Нет изображения') ?>"
                                 style="height: 150px; object-fit: cover;">
                        <?php else: ?>
                            <img src="/img/no-image.png"
                                 class="card-img-top"
                                 alt="Нет изображения"
                                 style="height: 150px; object-fit: cover;">
                        <?php endif; ?>
                        <div class="card-body p-2">
                            <h6 class="card-title mb-1"><?= htmlspecialchars($item->product_name ?? '') ?></h6>
                            <p class="card-text mb-1">₽<?= number_format($item->product_price ?? 0, 2) ?></p>
                            <form action="/cart/update" method="POST" class="d-flex mb-2">
                                <input type="hidden" name="item_id" value="<?= $item->getItemId() ?>">
                                <input type="number" name="quantity"
                                       value="<?= $item->getQuantity() ?>"
                                       min="1"
                                       class="form-control form-control-sm me-2 update-quantity"
                                       data-price="<?= $item->product_price ?>"
                                       style="width: 70px;">
                            </form>
                            <form action="/cart/remove" method="POST">
                                <input type="hidden" name="item_id" value="<?= $item->getItemId() ?>">
                                <button type="submit" class="btn btn-danger btn-sm w-100">Удалить</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
        $total = 0;
        foreach ($cartItems as $item) {
            $total += ($item->product_price ?? 0) * $item->getQuantity();
        }
        ?>
        <div class="text-end mb-4">
            <h5 id="cart-total">Итого: ₽<?= number_format($total, 2) ?></h5>
        </div>
        <div class="text-center mb-5">
            <a href="<?= url('order.checkout-cart') ?>" class="btn btn-success btn-lg">Купить</a>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center">Ваша корзина пуста</div>
    <?php endif; ?>
</div>

<script src="./assets/js/cart.js"></script>

