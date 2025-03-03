<form id="products-form" action="<?= url('admin.products.process') ?>" method="POST">
    <div class="fixed-top-row">
        <div class="d-flex justify-content-between align-items-center">
            <div class="mx-3 flex-grow-1">
            <input type="text" name="searchInput" class="form-control" placeholder="Поиск товаров..." value="<?php echo htmlspecialchars($searchValue ?? ''); ?>">

            </div>
            <div>
                <button type="submit" name="action" value="search" class="btn btn-primary">Поиск</button>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-center align-items-center gap-3 mt-3">
        <button type="submit" name="action" value="deactivate" class="btn btn-danger">Деактивировать</button>
        <button type="submit" name="action" value="activate" class="btn btn-success">Активировать</button>
        <a href="<?= url('admin.products.create') ?>" class="btn btn-primary">+ Добавить элемент</a>
    </div>

    <div class="mt-5">
        <!-- <?= $error ?> -->
        <table class="table">
            <thead>
            <tr>
                <th><input type="checkbox" id="select-all"></th>
                <th>ID</th>
                <th>Название</th>
                <th>Активность</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($products as $product): ?>
                <tr class="product-row <?php echo $product->getIsActive() ? '' : 'inactive-product'; ?>">
                    <td><input type="checkbox" class="product-checkbox" name="selected_products[]" value="<?php echo $product->getId(); ?>"></td>
                    <td><?php echo htmlspecialchars($product->getId()); ?></td>
                    <td><?php echo htmlspecialchars($product->getName()); ?></td>
                    <td><?php echo $product->getIsActive() ? 'true' : 'false'; ?></td>
                    <td>
                        <a href="<?= url('admin.products.edit', ['id' => $product->getId()]) ?>" class="btn btn-sm btn-warning btn-edit">
                            Редактировать
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</form>

<?php if ($totalPages > 1): ?>
    <?php
    $baseUrl = '/admin/products';
    $searchParam = '';
    
    if ($searchValue) {
        $encodedSearch = urlencode($searchValue);
        $searchParam = 'searchInput=' . $encodedSearch;
    }
    
    // Базовый URL с параметрами поиска (без номера страницы)
    $baseUrlWithSearch = $baseUrl . ($searchParam ? '?' . $searchParam : '');
    
    // URL для пагинации (со страницей > 1)
    $urlWithSearchAndPage = $baseUrl . '?' . $searchParam . ($searchParam ? '&' : '') . 'page=';
    ?>
    <nav aria-label="Product pagination">
        <ul class="pagination justify-content-center">
            <?php if ($currentPage > 1): ?>
                <li class="page-item">
                    <?php if ($currentPage == 2): ?>
                        <a class="page-link" href="<?php echo $baseUrlWithSearch; ?>"><<</a>
                    <?php else: ?>
                        <a class="page-link" href="<?php echo $urlWithSearchAndPage . ($currentPage - 1); ?>"><<</a>
                    <?php endif; ?>
                </li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?php echo $currentPage == $i ? 'active' : ''; ?>">
                    <?php if ($i == 1): ?>
                        <a class="page-link" href="<?php echo $baseUrlWithSearch; ?>"><?php echo $i; ?></a>
                    <?php else: ?>
                        <a class="page-link" href="<?php echo $urlWithSearchAndPage . $i; ?>"><?php echo $i; ?></a>
                    <?php endif; ?>
                </li>
            <?php endfor; ?>

            <?php if ($currentPage < $totalPages): ?>
                <li class="page-item">
                    <a class="page-link" href="<?php echo $urlWithSearchAndPage . ($currentPage + 1); ?>">>></a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
<?php endif; ?>