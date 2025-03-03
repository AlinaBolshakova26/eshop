<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="row mb-4 order-form">
            <div class="col-12 text-center">
                <h2 class="fw-bold">Оформление заказа</h2>
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <img src="<?php echo htmlspecialchars($product['main_image'] ?? '/assets/images/default.jpg'); ?>"
                             class="img-fluid"
                             alt="<?php echo htmlspecialchars($product['name'] ?? 'Товар'); ?>">
                    </div>
                    <div class="col-md-9">
                        <p class="order-form-name"><?php echo htmlspecialchars($product['name'] ?? 'Название не указано'); ?></p>
                        <p><?php echo nl2br(htmlspecialchars($product['description'] ?? 'Описание отсутствует')); ?></p>
                        <p class="mb-1">Количество: <?php echo intval($quantity); ?></p>
                        <p>Цена за единицу: &#8381; <?php echo number_format($product['price']); ?></p>
                        <p class="h5">Итого: &#8381; <?php echo number_format($product['price'] * $quantity); ?></p>
                    </div>
                </div>
            </div>
        </div>
        <form action="<?= url('order.submit') ?>" method="POST" id="orderForm">
            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
            <input type="hidden" name="quantity" value="<?php echo $quantity; ?>">

            <!-- ФИО -->
            <div class="mb-3">
                <label for="customer_name" class="form-label">ФИО *</label>
                <input type="text"
                       class="form-control <?php echo isset($errors['customer_name']) ? 'is-invalid' : ''; ?>"
                       id="customer_name" name="customer_name" required
                       value="<?php echo isset($user['name'])
                           ? htmlspecialchars($user['name'])
                           : (isset($_POST['customer_name']) ? htmlspecialchars($_POST['customer_name']) : ''); ?>">
                <?php if (isset($errors['customer_name'])): ?>
                    <div class="invalid-feedback"><?php echo $errors['customer_name']; ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">Номер телефона *</label>
                <input type="tel" class="form-control <?php echo isset($errors['phone']) ? 'is-invalid' : ''; ?>"
                       id="phone" name="phone" required
                       value="<?php echo isset($user['phone'])
                           ? htmlspecialchars($user['phone'])
                           : (isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''); ?>">
                <?php if (isset($errors['phone'])): ?>
                    <div class="invalid-feedback"><?php echo $errors['phone']; ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Электронная почта *</label>
                <input type="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>"
                       id="email" name="email" required
                       value="<?php echo isset($user['email'])
                           ? htmlspecialchars($user['email'])
                           : (isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''); ?>">
                <?php if (isset($errors['email'])): ?>
                    <div class="invalid-feedback"><?php echo $errors['email']; ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="city" class="form-label">Город *</label>
                <input type="text"
                       class="form-control address-input <?php echo isset($errors['city']) ? 'is-invalid' : ''; ?>"
                       id="city" name="city" required
                       value="<?php echo isset($user['city']) ? htmlspecialchars($user['city']) : (isset($_POST['city']) ? htmlspecialchars($_POST['city']) : ''); ?>">
                <?php if (isset($errors['city'])): ?>
                    <div class="invalid-feedback"><?php echo $errors['city']; ?></div>
                <?php endif; ?>
                <div id="city-dropdown" class="address-dropdown"></div>
            </div>

            <div class="mb-3">
                <label for="street" class="form-label">Улица *</label>
                <input type="text"
                       class="form-control address-input <?php echo isset($errors['street']) ? 'is-invalid' : ''; ?>"
                       id="street" name="street" required
                       value="<?php echo isset($user['street']) ? htmlspecialchars($user['street']) : (isset($_POST['street']) ? htmlspecialchars($_POST['street']) : ''); ?>">
                <?php if (isset($errors['street'])): ?>
                    <div class="invalid-feedback"><?php echo $errors['street']; ?></div>
                <?php endif; ?>
                <div id="street-dropdown" class="address-dropdown"></div>
            </div>

            <div class="mb-3">
                <label for="house" class="form-label">Дом *</label>
                <input type="text"
                       class="form-control address-input <?php echo isset($errors['house']) ? 'is-invalid' : ''; ?>"
                       id="house" name="house" required
                       value="<?php echo isset($user['house']) ? htmlspecialchars($user['house']) : (isset($_POST['house']) ? htmlspecialchars($_POST['house']) : ''); ?>">
                <?php if (isset($errors['house'])): ?>
                    <div class="invalid-feedback"><?php echo $errors['house']; ?></div>
                <?php endif; ?>
                <div id="house-dropdown" class="address-dropdown"></div>
            </div>

            <div class="mb-3">
                <label for="apartment" class="form-label">Квартира</label>
                <input type="text" class="form-control <?php echo isset($errors['apartment']) ? 'is-invalid' : ''; ?>"
                       id="apartment" name="apartment"
                       value="<?php echo isset($user['apartment']) ? htmlspecialchars($user['apartment']) : (isset($_POST['apartment']) ? htmlspecialchars($_POST['apartment']) : ''); ?>">
                <?php if (isset($errors['apartment'])): ?>
                    <div class="invalid-feedback"><?php echo $errors['apartment']; ?></div>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-success">Подтвердить заказ</button>
        </form>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#phone').on('input', function () {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        $('#orderForm').submit(function (e) {
            let isValid = true;

            $('.is-invalid').removeClass('is-invalid');

            if ($('#customer_name').val().trim() === '') {
                $('#customer_name').addClass('is-invalid');
                isValid = false;
            }

            if (!$('#phone').val().match(/^[0-9]{11}$/)) {
                $('#phone').addClass('is-invalid');
                isValid = false;
            }

            if ($('#email').val().trim() === '' || !$('#email').val().match(/^[^\\s@]+@[^\\s@]+\\.[^\\s@]+$/)) {
                $('#email').addClass('is-invalid');
                isValid = false;
            }

            if ($('#address').val().trim() === '') {
                $('#address').addClass('is-invalid');
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
            }
        });
    });
</script>
