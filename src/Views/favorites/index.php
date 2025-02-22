<div class="container mt-5">
    <h2>Избранное</h2>
    <?php if (empty($favorites)): ?>
        <div class="alert alert-info text-center" role="alert">
            <h4 class="alert-heading">Упс!</h4>
            <p>В избранном пока нет товаров. Добавьте понравившиеся товары из каталога.</p>
        </div>
    <?php else: ?>
        <div id="favoritesContainer" class="row">
            <?php foreach ($favorites as $favorite): ?>
                <div class="col-md-4 mb-3" id="favorite-<?= htmlspecialchars($favorite->item_id); ?>">
                    <div class="card">
                        <img src="<?= htmlspecialchars($favorite->main_image ?? '/img/no-image.png'); ?>" class="card-img-top" alt="<?= htmlspecialchars($favorite->product_name); ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($favorite->product_name); ?></h5>
                            <p class="card-text">Цена: &#8381; <?= number_format($favorite->product_price, 2); ?></p>
                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-danger w-100 remove-from-favorites" data-item-id="<?= htmlspecialchars($favorite->item_id); ?>">
                                    Удалить из избранного
                                </button>
                                <form method="POST" action="/cart/add">
                                    <input type="hidden" name="item_id" value="<?= htmlspecialchars($favorite->item_id); ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="btn btn-success w-100">Добавить в корзину</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
