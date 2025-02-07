<div class="row">
    <div class="col-12">
        <h1 class="mb-4">Наши товары</h1>
    </div>
    <?php if (!empty($products)): ?>
        <?php foreach ($products as $product): ?>
            <div class="col-md-4 mb-4">
                <div class="card product-card">
                    <?php if (!empty($product->main_image_path)): ?>
                        <img src="<?php echo htmlspecialchars($product->main_image_path); ?>" alt="Main Image">
                    <?php else: ?>
                        <div>Нет изображения</div>
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($product->name); ?></h5>
                        <p class="card-text"><?php echo nl2br(htmlspecialchars($product->desc_short)); ?></p>
                        <p class="card-text"><strong>&#8381; <?php echo number_format($product->price); ?></strong></p>
                        <div class="d-flex justify-content-between">
                            <a href="/product/<?php echo $product->id; ?>" class="btn btn-primary">Подробнее</a>
                            <a href="/order/create/<?php echo $product->id; ?>" class="btn btn-success ml-auto">Купить</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Товаров не найдено.</p>
    <?php endif; ?>
</div>
<?php if ($totalPages > 1): ?>
    <nav aria-label="Product pagination">
        <ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?php echo $currentPage == $i ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
<?php endif; ?>