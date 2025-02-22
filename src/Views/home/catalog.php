<div class="tag-filter mb-4">
    <div class="tag-cloud">
        <a href="/" class="btn tag-btn <?php echo empty($selectedTagIds) ? 'active' : ''; ?>" data-tag-id="all">Все</a>
		<?php foreach ($tags as $tag): ?>
            <a href="/?tags=<?php
			echo Utils\PaginationHelper::buildTagParam($selectedTagIds, $tag->toListDTO()->id);
			?>"
               class="btn tag-btn <?php echo in_array($tag->toListDTO()->id, $selectedTagIds ?? []) ? 'active' : ''; ?>"
               data-tag-id="<?php echo htmlspecialchars($tag->toListDTO()->id); ?>">
				<?php echo htmlspecialchars($tag->toListDTO()->name); ?>
            </a>
		<?php endforeach; ?>
    </div>
</div>
<?php if (!empty($priceError)): ?>
    <div class="alert alert-danger mb-4" role="alert">
		<?= htmlspecialchars($priceError) ?>
    </div>
<?php endif; ?>
<div class="price-filter mb-4">
    <form method="GET" action="" class="row g-3 align-items-end">
        <div class="col-md-3">
            <label for="minPrice" class="form-label">Минимальная цена</label>
            <input type="number" id="minPrice" name="minPrice" class="form-control"
                   value="<?= htmlspecialchars($_GET['minPrice'] ?? '') ?>" placeholder="От" min="0">
        </div>

        <div class="col-md-3">
            <label for="maxPrice" class="form-label">Максимальная цена</label>
            <input type="number" id="maxPrice" name="maxPrice" class="form-control"
                   value="<?= htmlspecialchars($_GET['maxPrice'] ?? '') ?>" placeholder="До" min="0">
        </div>

		<?php if (!empty($selectedTagIds)): ?>
            <input type="hidden" name="tags" value="<?= implode(',', $selectedTagIds) ?>">
		<?php endif; ?>

		<?php if (!empty($searchValue)): ?>
            <input type="hidden" name="searchInput" value="<?= htmlspecialchars($searchValue) ?>">
		<?php endif; ?>

        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Применить</button>
        </div>

        <div class="col-md-2">
            <a href="<?= Utils\PaginationHelper::removeQueryParams(['minPrice', 'maxPrice']); ?>"
               class="btn btn-outline-secondary w-100">Сбросить</a>
        </div>
    </form>
</div>
<div class="row">
    <h1 class="mb-4">
		<?php
		$selectedTagNames = Utils\PaginationHelper::getActiveTags($tags, $selectedTagIds);
		echo !empty($selectedTagNames) ? "Выбрана категория: " . htmlspecialchars(implode(', ', $selectedTagNames)) : "Галерея цветов";
		?>
    </h1>

	<?php if (!empty($products)): ?>
		<?php foreach ($products as $product): ?>
            <div class="col-md-4 mb-4">
                <div class="card product-card h-100">
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
                                     class="card-img-top <?php echo $index === 0 ? 'd-none' : ''; ?>"
                                     alt="<?php echo htmlspecialchars($product->name); ?>"
                                     data-index="<?php echo $index + 1; ?>"
                                     loading="lazy">
							<?php endforeach; ?>
                        </div>
                        <button class="btn btn-secondary left-arrow position-absolute"
                                style="left: 10px; top: 50%; transform: translateY(-50%);">❮
                        </button>
                        <button class="btn btn-secondary right-arrow position-absolute"
                                style="right: 10px; top: 50%; transform: translateY(-50%);">❯
                        </button>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <div class="position-absolute top-0 end-0 p-2">
                                <a href="#" class="favorite-icon" data-item-id="<?= $product->id; ?>">
                                    <i class="fa fa-heart favorite-inactive" data-favorite="<?= $product->id; ?>"></i>
                                </a>
                            </div>
                        <?php endif; ?>

                        <h5 class="card-title"><?php echo htmlspecialchars($product->name); ?></h5>
                        <p class="card-text"><?php echo nl2br(htmlspecialchars($product->desc_short)); ?></p>
                        <p class="card-text"><strong>&#8381; <?php echo number_format($product->price); ?></strong></p>
                        <div class="d-flex justify-content-between mt-auto">
                            <a href="/product/<?php echo $product->id; ?>" class="btn btn-primary">Подробнее</a>
							<?php if (isset($_SESSION['user_id'])): ?>
                                <form method="POST" action="/cart/add" style="margin:0;">
                                    <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($product->id); ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="btn btn-success">Добавить в корзину</button>
                                </form>
							<?php else: ?>
                                <a href="/order/create/<?php echo $product->id; ?>" class="btn btn-success">Купить</a>
							<?php endif; ?>
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
	<?php
	if ($totalPages <= 4) {
		$pages = range(1, $totalPages);
	} else {
		if ($currentPage <= 3) {
			$pages = range(1, 4);
		} elseif ($currentPage >= $totalPages - 1) {
			$pages = range($totalPages - 3, $totalPages);
		} else {
			$pages = [1, 2, $currentPage - 1, $currentPage];
		}
	}
	?>
    <nav aria-label="Product pagination">
        <ul class="pagination justify-content-center">
			<?php if ($currentPage > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="<?php echo Utils\PaginationHelper::buildPaginationUrl($selectedTagIds, $currentPage - 1, $minPrice, $maxPrice, $searchQuery); ?>"><<</a>
                </li>
			<?php endif; ?>

			<?php foreach ($pages as $index => $page): ?>
				<?php
				if ($index === 2 && $pages[2] > 3) {
					echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
				}
				?>
                <li class="page-item <?php echo ($page == $currentPage) ? 'active' : ''; ?>">
                    <a class="page-link"
                       href="<?php echo Utils\PaginationHelper::buildPaginationUrl($selectedTagIds, $page, $minPrice, $maxPrice, $searchValue); ?>"><?php echo $page; ?></a>
                </li>
			<?php endforeach; ?>

			<?php if ($currentPage < $totalPages): ?>
                <li class="page-item">
                    <a class="page-link" href="<?php echo Utils\PaginationHelper::buildPaginationUrl($selectedTagIds, $currentPage + 1, $minPrice, $maxPrice, $searchValue); ?>">>></a>
                </li>
			<?php endif; ?>
        </ul>
    </nav>
<?php endif; ?>
