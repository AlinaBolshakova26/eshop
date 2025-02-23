<h1>Создание нового тега</h1>

<form method="POST" action="/admin/tags/create">
    <div>
        <label for="name">Название тега:</label>
        <input type="text" id="name" name="name" required>
    </div>

    <div>
        <label>Продукты:</label>
        <br>
		<?php if (!empty($products)): ?>
			<?php foreach ($products as $product): ?>
                <label>
                    <input type="checkbox" name="products[]" value="<?= htmlspecialchars($product->getId()) ?>">
					<?= htmlspecialchars($product->getName()) ?>
                </label><br>
			<?php endforeach; ?>
		<?php else: ?>
            <p>Нет доступных продуктов.</p>
		<?php endif; ?>
    </div>

    <button type="submit">Создать тег</button>
</form>