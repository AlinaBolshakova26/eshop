<form id="products-form" action="/admin/products/process" method="POST">
<div class="fixed-top-row">
    <div class="d-flex justify-content-between align-items-center">

        <div class="mx-3 flex-grow-1">
            <input type="text" class="form-control" placeholder="Поиск товаров...">
        </div>
        <div>
            <button type="submit" name="action" value="search" class="btn btn-primary">Поиск</button>
        </div>
    </div>
    <div class="d-flex justify-content-center align-items-center gap-3 mt-3">
            <button type="submit" name="action" value="delete" class="btn btn-danger">Удалить</button>
            <button type="submit" name="action" value="activate" class="btn btn-success">Активировать</button>
            <a href="add" class="btn btn-primary">+ Добавить элемент</a>
    </div>
</div>

<div class="mt-5">
    <?= $error ?>
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
            <tr class="product-row <?php echo $product->is_active ? '' : 'inactive-product'; ?>">
                <td><input type="checkbox" class="product-checkbox" name="selected_products[]" value="<?php echo $product->id; ?>"></td>
                <td><?php echo htmlspecialchars($product->id); ?></td>
                <td><?php echo htmlspecialchars($product->name); ?></td>
                <td><?php echo $product->is_active ? 'true' : 'false'; ?></td>
                <td>
                    <a href="/admin/products/<?php echo $product->id; ?>" class="btn btn-sm btn-warning btn-edit">
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
