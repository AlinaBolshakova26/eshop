<div class="row">
    <div class="col-md-6">
        <div class="product-gallery">
                <div class="main-image mb-3">
                    <img src="<?php echo htmlspecialchars($product->main_image_path); ?>" alt="<?php echo htmlspecialchars($product->name); ?>" class="img-fluid">
                </div>
                <?php if (!empty($product->getAdditionalImagePaths())): ?>
                    <div class="thumbnail-images">


                    </div>
                <?php else: ?>
                    <p>Изображений нет.</p>
                <?php endif; ?>
        </div>
    </div>
    <div class="col-md-6">
        <h1><?php echo htmlspecialchars($product->name); ?></h1>
        <p class="price h2 my-4">&#8381; <?php echo number_format($product->price); ?></p>
        <div class="description mb-4">
            <?php echo nl2br(htmlspecialchars($product->description)); ?>
        </div>
        <form action="/order/create/<?php echo $product->id; ?>" method="GET" class="mb-4">
            <button type="submit" class="btn btn-success w-50">Купить</button>
        </form>
    </div>
</div>
