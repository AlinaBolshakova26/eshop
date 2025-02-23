<div class="row">
    <div class="col-md-6 order-2 order-md-1">
        <div class="product-gallery">
            <div class="main-image mb-3">
                <img src="<?php echo htmlspecialchars($product->main_image_path); ?>"
                     alt="<?php echo htmlspecialchars($product->name); ?>"
                     class="img-fluid">
            </div>
            <?php if (!empty($product->additional_image_paths)): ?>
                <div class="thumbnail-images">
                    <?php foreach ($product->additional_image_paths as $image): ?>
                        <img src="<?php echo htmlspecialchars($image); ?>"
                             alt="<?php echo htmlspecialchars($product->name); ?>"
                             class="img-thumbnail" style="width: 100px; height: auto;">
                    <?php endforeach; ?>
                    <img src="<?php echo htmlspecialchars($product->main_image_path); ?>"
                         alt="<?php echo htmlspecialchars($product->name); ?>"
                         class="img-thumbnail" style="width: 100px; height: auto;">
                </div>
            <?php else: ?>
                <p>Изображений нет.</p>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-md-6 order-1 order-md-2 desc">
        <div class="product-rating mb-2">
            <div class="stars-detail" title="<?= Utils\RatingHelper::getRatingText($averageRating, $totalReviews) ?>">
                <?= Utils\RatingHelper::getRatingStars($averageRating) ?>
            </div>
            <small class="text-muted-rating">
                <?= Utils\RatingHelper::getRatingText($averageRating, $totalReviews) ?>
            </small>
        </div>
        <h1><?php echo htmlspecialchars($product->name); ?></h1>
        <p class="price h2 my-4">&#8381; <?php echo number_format($product->price); ?></p>
        <div class="description mb-4">
            <?php echo nl2br(htmlspecialchars($product->description)); ?>
        </div>

        <?php if (isset($_SESSION['user_id'])): ?>
            <form method="POST" action="/cart/add" class="mb-4">
                <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($product->id); ?>">
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="btn btn-success w-50">Добавить в корзину</button>
            </form>
            <button type="button" class="btn add-to-favorite-detail w-50" data-item-id="<?= htmlspecialchars($product->id); ?>" style="background-color: #EA80AE; color: white; font-size: 1.2rem;">
                Добавить в избранное
            </button>
        <?php else: ?>
            <div class="mb-4 d-flex gap-2">
                <a href="/order/create/<?php echo $product->id; ?>" class="btn btn-success w-50">Купить</a>
                <a href="/user/login?redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>" class="btn btn-warning w-50">Добавить в корзину</a>
            </div>
        <?php endif; ?>

    </div>
    <div id="imageModal" class="modal-overlay">
        <div class="modal-content">
            <span class="modal-close">&times;</span>
            <img id="modalImage" src="" alt="Enlarged Image" class="img-fluid">
            <div class="modal-navigation">
                <button class="modal-prev btn btn-outline-secondary">&larr;</button>
                <button class="modal-next btn btn-outline-secondary">&rarr;</button>
            </div>
        </div>
    </div>
</div>