<!--<style>-->
<!--    .tags-container {-->
<!--        display: flex;-->
<!--        flex-wrap: wrap;-->
<!--        gap: 8px;-->
<!--    }-->
<!---->
<!--    .tag {-->
<!--        display: inline-block;-->
<!--        padding: 4px 8px;-->
<!--        background-color: #e9ecef;-->
<!--        border-radius: 4px;-->
<!--        cursor: pointer;-->
<!--    }-->
<!---->
<!--    .tag input[type="checkbox"] {-->
<!--        display: none;-->
<!--    }-->
<!---->
<!--    .tag input[type="checkbox"]:checked + span {-->
<!--        background-color: #0d6efd;-->
<!--        color: white;-->
<!--    }-->
<!--</style>-->
<div class="main-content-detail">
    <div class="container">
        <div class="card shadow mt-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2 class="mb-0">Продукт #<?= htmlspecialchars($product->getId()) ?></h2>
                <span class="badge bg-success"><?= htmlspecialchars($product->getId()) ?></span>
            </div>
            <div class="card-body">
                <form action="<?= url('admin.products.update', ['id' => htmlspecialchars($product->getId())]) ?>" enctype="multipart/form-data" method="POST">
                    <h4><i class="fas fa-product"></i> Данные</h4>

                    <div class="mb-3">
                        <label for="name" class="form-label"><strong>Название:</strong></label>
                        <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($product->getName()) ?>">
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label"><strong>Описание:</strong></label>
                        <textarea class="form-control" id="description" name="description"><?= htmlspecialchars($product->getDescription()) ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="descShort" class="form-label"><strong>Короткое описание:</strong></label>
                        <input type="text" class="form-control" id="descShort" name="desc_short" value="<?= htmlspecialchars($product->getDescShort()) ?>">
                    </div>

                    <div class="mb-3">
                        <label for="price" class="form-label"><strong>Цена:</strong></label>
                        <input type="number" step="1" class="form-control" id="price" name="price" value="<?= htmlspecialchars($product->getPrice()) ?>">
                    </div>

                    <h4 class="mt-3"><i class="fas fa-tags"></i> Теги</h4>
                    <div class="mb-3">
                        <label class="form-label"><strong>Теги товара:</strong></label>
                        <div class="tags-container" id="tags-container">
							<?php foreach ($allTags as $tag): ?>
                                <label class="tag <?= in_array($tag->getId(), $productTags) ? 'selected' : '' ?>">
                                    <input type="checkbox"
                                           name="tags[]"
                                           value="<?= htmlspecialchars($tag->getId()) ?>"
										<?= in_array($tag->getId(), $productTags) ? 'checked' : '' ?>>
									<?= htmlspecialchars($tag->getName()) ?>
                                </label>
							<?php endforeach; ?>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="isActive" class="form-label"><strong>Статус товара:</strong></label>
                        <select class="form-control" id="isActive" name="is_active">
                            <option value="1" <?= $product->getIsActive() ? 'selected' : '' ?>>Активен</option>
                            <option value="0" <?= !$product->getIsActive() ? 'selected' : '' ?>>Неактивен</option>
                        </select>
                    </div>

                    <h4 class="mt-3"><i class="fas fa-images"></i> Картинки</h4>
                    <div class="mb-3">
                        <label class="form-label"><strong>Главная картинка:</strong></label>
                        <div>
                            <img id="mainImagePreview"
                                 src="<?= htmlspecialchars($product->getMainImagePath()) ?>"
                                 alt="<?= htmlspecialchars($product->getName()) ?>"
                                 class="img-thumbnail mt-2" style="width: 150px; height: auto;">
                            <button type="button" class="btn btn-primary btn-sm mt-2" onclick="document.getElementById('mainImageInput').click()">
                                Изменить
                            </button>
                            <input type="file" id="mainImageInput" name="main_image" style="display: none;" accept="image/*" onchange="previewMainImage(event)">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><strong>Дополнительные картинки:</strong></label>
                        <div id="additionalImagesContainer">
                            <?php if (!empty($product->getAdditionalImagePaths())): ?>

                                <?php foreach ($product->getAdditionalImagePaths() as $imageId => $imagePath): ?>
                                    <div class="mb-2">
                                        <img src="<?= htmlspecialchars($imagePath) ?>"
                                             alt="<?= htmlspecialchars($product->getName()) ?>"
                                             id="<?= $imageId ?>"
                                             class="img-thumbnail mt-2" style="width: 150px; height: auto;">
                                        <button type="button" class="btn btn-danger btn-sm" onclick="deleteImage(<?= $imageId ?>)">
                                            Удалить
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <div class="additional-image-input-container">
                            <input type="file" id="additionalImagesInput" class="additional-image-input" name="additional_images[]" accept="image/*" onchange="handleAdditionalImageUpload(event)">
                            <label for="additionalImagesInput" class="additional-image-input-label">
                                <i class="fas fa-image"></i> + Добавить изображение
                            </label>
                        </div>
                    </div>
                    <h4 class="mt-3"><i class="fas fa-clock"></i> Даты</h4>
                    <p><strong>Создан:</strong> <?= htmlspecialchars($product->getCreatedAt()) ?></p>
                    <p><strong>Обновлен:</strong> <?= htmlspecialchars($product->getUpdatedAt()) ?></p>

                    <button type="submit" class="btn btn-success mt-3"><i class="fas fa-save"></i> Сохранить изменения</button>
                    <a href="<?= url('admin.products') ?>" class="btn btn-secondary mt-3"><i class="fas fa-arrow-left"></i> Назад</a>
                </form>
            </div>
        </div>
    </div>
</div>