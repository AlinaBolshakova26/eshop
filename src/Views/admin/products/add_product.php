<div class="container mt-5">
    <h2>Создание нового товара</h2>
    <form action="/admin/products/create" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="name" class="form-label">Название товара:</label>
            <input type="text" id="name" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Описание товара:</label>
            <textarea id="description" name="description" class="form-control"></textarea>
        </div>
        <div class="mb-3">
            <label for="desc_short" class="form-label">Короткое описание:</label>
            <input type="text" id="desc_short" name="desc_short" class="form-control">
        </div>
        <div class="mb-3">
            <label for="price" class="form-label">Цена:</label>
            <input type="number" id="price" name="price" class="form-control" step="1" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Теги:</label>
            <div class="tags-container" id="tags-container">
				<?php foreach ($tags as $tag): ?>
                    <label class="tag">
                        <input type="checkbox" name="tags[]" value="<?= $tag->getId() ?>">
						<?= htmlspecialchars($tag->getName()) ?>
                    </label>
				<?php endforeach; ?>
            </div>
        </div>
        <div class="mb-3">
            <label for="main_image" class="form-label">Главное изображение:</label>
            <input type="file" id="main_image" name="main_image" class="form-control" accept="image/*" required>
            <div class="preview-container" id="main-image-preview"></div>
        </div>
        <div class="mb-3">
            <label class="form-label">Дополнительные изображения:</label>
            <input type="file" id="additional_images" name="additional_images[]" class="form-control" accept="image/*" multiple>
            <div class="preview-container" id="additional-images-preview"></div>
        </div>
        <button type="submit" class="btn btn-primary">Создать товар</button>
        <a href="<?= url('admin.products') ?>" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Назад</a>
    </form>
</div>
<script defer>
    document.addEventListener('DOMContentLoaded', function() {
        initProductForm();
    });
</script>