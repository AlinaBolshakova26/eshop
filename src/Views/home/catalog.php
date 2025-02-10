<div class="tag-filter mb-4">
    <div class="tag-cloud">
        <a href="/" class="btn <?php echo empty($selectedTagId) ? 'active' : ''; ?>">Все</a>
        <?php foreach ($tags as $tag): ?>
            <a href="/tag/<?php echo $tag->toListDTO()->id; ?>"
               class="<?php echo ($selectedTagId == $tag->toListDTO()->id) ? 'active' : ''; ?>">
                <?php echo htmlspecialchars($tag->toListDTO()->name); ?>
            </a>
        <?php endforeach; ?>
    </div>
</div>
<div class="row">
    <h1 class="mb-4">
        <?php echo $selectedTagName ? "Выбрана категория: " . htmlspecialchars($selectedTagName) : "Галерея цветов"; ?>
    </h1>
    <?php if (!empty($products)): ?>
        <?php foreach ($products as $product): ?>
            <div class="col-md-4 mb-4">
                <div class="card product-card">
                    <div class="product-images-slider position-relative">
                        <div class="image-container">
                            <?php if (!empty($product->main_image_path)): ?>
                                <img src="<?php echo htmlspecialchars($product->main_image_path); ?>"
                                     alt="<?php echo htmlspecialchars($product->name); ?>"
                                     class="card-img-top"
                                     data-index="0"
                                     loading="lazy">
                            <?php else: ?>
                                <div>Нет изображения</div>
                            <?php endif; ?>
							<?php foreach ($product->additional_image_paths as $index => $image): ?>
                               <img src="<?php echo htmlspecialchars($image); ?>"
                                   class="card-img-top --><?php echo $index === 0 ? 'd-none' : ''; ?>"
                                   alt="<?php echo htmlspecialchars($product->name); ?>"
                                    data-index="<?php echo $index + 1; ?>"
                                    loading="lazy">
							<?php endforeach; ?>
                        </div>
                        <button class="btn btn-secondary left-arrow position-absolute" style="left: 10px; top: 50%; transform: translateY(-50%);">❮</button>
                        <button class="btn btn-secondary right-arrow position-absolute" style="right: 10px; top: 50%; transform: translateY(-50%);">❯</button>
                    </div>
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
                    <a class="page-link" href="/tag/<?php echo $selectedTagId; ?>?page=<?php echo $i; ?>">
                        <?php echo $i; ?>
                    </a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
<?php endif; ?>