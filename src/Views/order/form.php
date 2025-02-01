<div class="row">
	<div class="col-md-8 mx-auto">
		<h2 class="mb-4">Детали заказа</h2>


		<div class="card mb-4">
			<div class="card-body">
				<h5 class="card-title">Описание заказа</h5>
				<div class="row">
					<div class="col-md-3 mb-3">
						<img src="<?php echo htmlspecialchars($product['main_image']); ?>"
							 class="img-fluid"
							 alt="<?php echo htmlspecialchars($product['name']); ?>">
					</div>
					<div class="col-md-9">
						<p class="h4"><?php echo htmlspecialchars($product['name']); ?></p>
						<p class="mb-1">Количество: <?php echo intval($quantity); ?></p>
						<p>Цена за единицу: &#8381; <?php echo number_format($product['price']); ?></p>
						<p class="h5">Итого: &#8381; <?php echo number_format($product['price'] * $quantity); ?></p>
					</div>
				</div>
			</div>
		</div>


		<form action="/order/submit" method="POST" id="orderForm">
			<input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
			<input type="hidden" name="quantity" value="<?php echo $quantity; ?>">

			<div class="mb-3">
				<label for="customer_name" class="form-label">ФИО *</label>
				<input type="text" class="form-control <?php echo isset($errors['customer_name']) ? 'is-invalid' : ''; ?>"
					   id="customer_name" name="customer_name" required>
				<?php if (isset($errors['customer_name'])): ?>
					<div class="invalid-feedback"><?php echo $errors['customer_name']; ?></div>
				<?php endif; ?>
			</div>

			<div class="mb-3">
				<label for="phone" class="form-label">Номер телефона *</label>
				<input type="tel" class="form-control <?php echo isset($errors['phone']) ? 'is-invalid' : ''; ?>"
					   id="phone" name="phone" required>
				<?php if (isset($errors['phone'])): ?>
					<div class="invalid-feedback"><?php echo $errors['phone']; ?></div>
				<?php endif; ?>
			</div>

			<div class="mb-3">
				<label for="address" class="form-label">Адрес доставки *</label>
				<textarea class="form-control <?php echo isset($errors['address']) ? 'is-invalid' : ''; ?>"
						  id="address" name="address" rows="3" required></textarea>
				<?php if (isset($errors['address'])): ?>
					<div class="invalid-feedback"><?php echo $errors['address']; ?></div>
				<?php endif; ?>
			</div>

			<button type="submit" class="btn btn-success">Подтвердить заказ</button>
		</form>
	</div>
</div>

<script>
    $(document).ready(function() {
        $('#phone').on('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        $('#orderForm').submit(function(e) {
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
