<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="row mb-4 order-form">
            <div class="col-12 text-center">
                <h2 class="fw-bold">Оформление заказа</h2>
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-body">
                <?php foreach ($cartItems as $item): ?>
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <?php if (!empty($item->product_image)): ?>
                                <img src="<?= htmlspecialchars($item->product_image) ?>"
                                     class="img-fluid"
                                     alt="<?= htmlspecialchars($item->product_name) ?>">
                            <?php else: ?>
                                <img src="/img/no-image.png" class="img-fluid" alt="Нет изображения">
                            <?php endif; ?>
                        </div>
                        <div class="col-md-9">
                            <p class="mb-1"><strong><?= htmlspecialchars($item->product_name) ?></strong></p>
                            <p class="mb-1">Количество: <?= $item->getQuantity() ?></p>
                            <p class="mb-1">Сумма: <strong>₽<?= number_format(($item->product_price ?? 0) * $item->getQuantity(), 2) ?></strong></p>
                        </div>
                    </div>
                    <hr>
                <?php endforeach; ?>
                <div class="row">
                    <div class="col-12 text-center">
                        <p class="display-4">Итого: ₽<?= number_format($total, 2) ?></p>
                    </div>
                </div>
            </div>
        </div>
        <form action="<?= url('order.submit-cart') ?>" method="POST" id="orderForm">
            <div class="mb-3">
                <label for="customer_name" class="form-label">ФИО *</label>
                <input type="text"
                       class="form-control <?= isset($errors['customer_name']) ? 'is-invalid' : ''; ?>"
                       id="customer_name" name="customer_name" required
                       value="<?= htmlspecialchars($user['name'] ?? '') ?>">
                <?php if (isset($errors['customer_name'])): ?>
                    <div class="invalid-feedback"><?= $errors['customer_name']; ?></div>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Номер телефона *</label>
                <input type="tel"
                       class="form-control <?= isset($errors['phone']) ? 'is-invalid' : ''; ?>"
                       id="phone" name="phone" required
                       value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                <?php if (isset($errors['phone'])): ?>
                    <div class="invalid-feedback"><?= $errors['phone']; ?></div>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Электронная почта *</label>
                <input type="email"
                       class="form-control <?= isset($errors['email']) ? 'is-invalid' : ''; ?>"
                       id="email" name="email" required
                       value="<?= htmlspecialchars($user['email'] ?? '') ?>">
                <?php if (isset($errors['email'])): ?>
                    <div class="invalid-feedback"><?= $errors['email']; ?></div>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label for="city" class="form-label">Город *</label>
                <input type="text"
                       class="form-control <?= isset($errors['city']) ? 'is-invalid' : ''; ?>"
                       id="city" name="city" required
                       value="<?= htmlspecialchars($user['city'] ?? '') ?>">
                <?php if (isset($errors['city'])): ?>
                    <div class="invalid-feedback"><?= $errors['city']; ?></div>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label for="street" class="form-label">Улица *</label>
                <input type="text"
                       class="form-control <?= isset($errors['street']) ? 'is-invalid' : ''; ?>"
                       id="street" name="street" required
                       value="<?= htmlspecialchars($user['street'] ?? '') ?>">
                <?php if (isset($errors['street'])): ?>
                    <div class="invalid-feedback"><?= $errors['street']; ?></div>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label for="house" class="form-label">Дом *</label>
                <input type="text"
                       class="form-control <?= isset($errors['house']) ? 'is-invalid' : ''; ?>"
                       id="house" name="house" required
                       value="<?= htmlspecialchars($user['house'] ?? '') ?>">
                <?php if (isset($errors['house'])): ?>
                    <div class="invalid-feedback"><?= $errors['house']; ?></div>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label for="apartment" class="form-label">Квартира</label>
                <input type="text"
                       class="form-control <?= isset($errors['apartment']) ? 'is-invalid' : ''; ?>"
                       id="apartment" name="apartment"
                       value="<?= htmlspecialchars($user['apartment'] ?? '') ?>">
                <?php if (isset($errors['apartment'])): ?>
                    <div class="invalid-feedback"><?= $errors['apartment']; ?></div>
                <?php endif; ?>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-success btn-lg">Подтвердить заказ</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('orderForm').addEventListener('submit', function(e) {
    });
</script>
