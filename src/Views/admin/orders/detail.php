<div class="main-content-detail">
    <div class="container">
        <div class="card shadow mt-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2 class="mb-0">Заказ #<?= htmlspecialchars($order['id']) ?></h2>
                <span class="badge bg-success"><?= htmlspecialchars($order['status']) ?></span>
            </div>
            <div class="card-body">
                <h4><i class="fas fa-user"></i> Клиент</h4>
                <p><strong>ID пользователя:</strong> <?= htmlspecialchars($order['user_id']) ?></p>
                <p><strong>Имя:</strong> <?= htmlspecialchars($order['name']) ?></p>
                <p><strong>Адрес:</strong> <?= htmlspecialchars($order['city'] . ', ' . $order['street'] . ', ' . $order['house'] . ($order['apartment'] ? ', кв. ' . $order['apartment'] : '')) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($order['email']) ?></p>
                <p><strong>Телефон:</strong> <?= htmlspecialchars($order['phone']) ?></p>

                <h4 class="mt-3"><i class="fas fa-box"></i> Товар</h4>
                <p><strong>ID товара:</strong> <?= htmlspecialchars($order['item_id']) ?></p>
                <p><strong>Название:</strong> <?= htmlspecialchars($order['item_name']) ?></p>
                <p><strong>Цена:</strong> <span class="text-success fw-bold"><?= htmlspecialchars($order['price']) ?> ₽</span></p>
                <a href="/product/<?= htmlspecialchars($order['item_id']) ?>" class="btn btn-outline-secondary mt-2">
                    <i class="fas fa-arrow-right"></i> Ссылка на товар
                </a>

                <h4 class="mt-3"><i class="fas fa-clock"></i> Даты</h4>
                <p><strong>Создан:</strong> <?= htmlspecialchars($order['created_at']) ?></p>
                <p><strong>Обновлен:</strong> <?= htmlspecialchars($order['updated_at']) ?></p>

                <a href="/admin/orders" class="btn btn-primary mt-3"><i class="fas fa-arrow-left"></i> Назад</a>
            </div>
        </div>
    </div>
</div>