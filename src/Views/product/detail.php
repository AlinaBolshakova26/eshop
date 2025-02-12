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
        <h1><?php echo htmlspecialchars($product->name); ?></h1>
            <p class="price h2 my-4">&#8381; <?php echo number_format($product->price); ?></p>
        <div class="description mb-4">
            <?php echo nl2br(htmlspecialchars($product->description)); ?>
        </div>
        <form action="/order/create/<?php echo $product->id; ?>" method="GET" class="mb-4">
            <button type="submit" class="btn btn-success w-50">Купить</button>
        </form>
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
