<div class="main-content-detail">
    <div class="container">
        <div class="card shadow mt-3">
        <div class="card-header d-flex justify-content-between align-items-center">
                <h2 class="mb-0">Продукт #<?= htmlspecialchars($product->getId()) ?></h2>
                <span class="badge bg-success"><?= htmlspecialchars($product->getId()) ?></span>
            </div>
            <div class="card-body">
                
                <h4><i class="fas fa-product"></i> Данные</h4>
                
                <p><strong>Название:</strong> <?= htmlspecialchars($product->getName()) ?></p>
                <p><strong>Описание:</strong> <?= htmlspecialchars($product->getDescription()) ?></p>
                <p><strong>Короткое описание:</strong> <?= htmlspecialchars($product->getDescShort()) ?></p>
                <p><strong>Цена:</strong> <?= htmlspecialchars($product->getPrice()) ?></p>
                <p><strong>Статус:</strong> <?php echo $product->getIsActive() ? 'true' : 'false'; ?></p>

                <h4 class="mt-3"><i class="fas fa-images"></i> Картинки</h4>
                <p>
                    <strong>Главная:</strong> 
                    <img src="<?php echo htmlspecialchars($product->getMainImagePath()); ?>" 
                    alt="<?php echo htmlspecialchars($product->getName()); ?>" 
                    class="img-fluid" style="width: 100px; height: auto;">
                </p>
                
                <?php if (!empty($product->getAdditionalImagePaths())): ?>
                    <div class="thumbnail-images">
                        <?php foreach ($product->getAdditionalImagePaths() as $id => $path): ?>
                            <p>
                            <strong>Дополнительная:<?= $id?></strong>  
                                <img src="<?php echo htmlspecialchars($path); ?>" 
                                alt="<?php echo htmlspecialchars($product->getName()); ?>" 
                                class="img-thumbnail" style="width: 100px; height: auto;">
                            </p>
                            <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p>Изображений нет.</p>
                <?php endif; ?>

                <h4 class="mt-3"><i class="fas fa-clock"></i> Даты</h4>
                <p><strong>Создан:</strong> <?= htmlspecialchars($product->getCreatedAt()) ?></p>
                <p><strong>Обновлен:</strong> <?= htmlspecialchars($product->getUpdatedAt()) ?></p>

                <a href="/admin/products" class="btn btn-primary mt-3"><i class="fas fa-arrow-left"></i> Назад</a>
            
            </div>
        </div>
    </div>
</div>