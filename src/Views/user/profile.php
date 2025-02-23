<div class="container mt-5">
    <?php if (isset($error) && $error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if (isset($message) && $message): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if ($user): ?>
        <div class="text-center">
            <div class="avatar-picker-container d-inline-block mb-3 position-relative">
                <img id="current-avatar" src="/assets/images/avatars/<?= htmlspecialchars($user['avatar'] ?? 'default.jpg'); ?>"
                     alt="Аватар" class="rounded-circle border shadow-sm avatar-picker-image" width="150">
                <div class="avatar-picker-overlay" id="open-avatar-picker">Изменить</div>
            </div>
            <h2><?= htmlspecialchars($user['name']) ?></h2>
            <p><i class="bi bi-envelope"></i> <?= htmlspecialchars($user['email']) ?></p>
            <p><i class="bi bi-telephone"></i> <?= htmlspecialchars($user['phone']) ?></p>
        </div>
        <div class="text-center mt-4">
            <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#editProfileCollapse" aria-expanded="false" aria-controls="editProfileCollapse">
                Редактировать профиль
            </button>
        </div>
        <div class="collapse mt-4" id="editProfileCollapse">
            <div class="card card-body">
                <form method="post" action="/user/update">
                    <input type="hidden" name="avatar" id="selected-avatar" value="<?= htmlspecialchars($user['avatar'] ?? 'default.jpg'); ?>">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="name" class="form-label">Имя</label>
                            <input type="text" class="form-control" name="name" id="name" value="<?= htmlspecialchars($user['name']) ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" id="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label for="phone" class="form-label">Телефон</label>
                            <input type="text" class="form-control" name="phone" id="phone" value="<?= htmlspecialchars($user['phone']) ?>" required>
                        </div>
                        <div class="col-12">
                            <label for="password" class="form-label">Новый пароль (необязательно)</label>
                            <input type="password" class="form-control" name="password" id="password">
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <button type="submit" class="btn btn-success">Сохранить изменения</button>
                        <a href="/" class="btn btn-outline-secondary">На главную</a>
                    </div>
                </form>
            </div>
        </div>
        <div class="mt-4">
            <h5 class="text-center mb-3">Активные заказы</h5>
            <?php
            $pendingOrders = array_filter($orders, function($order) {
                return (stripos($order['status'], 'Доставлен') === false && stripos($order['status'], 'Отменен') === false);
            });
            ?>
            <?php if (!empty($pendingOrders)): ?>
                <div class="order-carousel-container position-relative">
                    <button class="carousel-arrow left-arrow"><i class="bi bi-chevron-left"></i></button>
                    <div class="order-carousel">
                        <?php foreach ($pendingOrders as $order): ?>
                            <a href="/product/<?= $order['product_id']; ?>" class="order-card-link">
                                <div class="card order-card">
                                    <img src="<?= htmlspecialchars($order['main_image'] ?? '/img/no-image.png'); ?>" class="order-img" alt="Товар">
                                    <div class="order-card-body">
                                        <p class="order-product-name" title="<?= htmlspecialchars($order['product_name']); ?>">
                                            <?= htmlspecialchars($order['product_name']); ?>
                                        </p>
                                        <?php
                                        $statusClass = (stripos($order['status'], 'Отменен') !== false) ? 'bg-danger' : 'bg-warning';
                                        ?>
                                        <span class="badge order-status <?= $statusClass; ?>">
                                    <?= htmlspecialchars($order['status']); ?>
                                </span>
                                        <p class="order-quantity">Количество: <?= htmlspecialchars($order['quantity']); ?></p>
                                        <p class="order-total">Сумма заказа: &#8381; <?= number_format($order['price']); ?></p>

                                        <p class="order-date"><?= htmlspecialchars(date('d.m.Y', strtotime($order['created_at']))); ?></p>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                    <button class="carousel-arrow right-arrow"><i class="bi bi-chevron-right"></i></button>
                </div>
            <?php else: ?>
                <div class="alert alert-light text-center small">Нет активных заказов.</div>
            <?php endif; ?>
        </div>
        <div class="mt-4">
            <div class="text-center">
                <button class="btn btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#otherOrdersCollapse" aria-expanded="false" aria-controls="otherOrdersCollapse">
                    Показать другие
                </button>
            </div>
            <div class="collapse mt-3" id="otherOrdersCollapse">
                <h5 class="text-center mb-3">Завершённые заказы</h5>
                <?php
                $otherOrders = array_filter($orders, function($order) {
                    return (stripos($order['status'], 'Доставлен') !== false || stripos($order['status'], 'Отменен') !== false);
                });
                ?>
                <?php if (!empty($otherOrders)): ?>
                    <div class="order-carousel">
                        <?php foreach ($otherOrders as $order): ?>
                            <a href="/product/<?= $order['product_id']; ?>" class="order-card-link">
                                <div class="card order-card">
                                    <img src="<?= htmlspecialchars($order['main_image'] ?? '/img/no-image.png'); ?>" class="order-img" alt="Товар">
                                    <div class="order-card-body">
                                        <p class="order-product-name" title="<?= htmlspecialchars($order['product_name']); ?>">
                                            <?= htmlspecialchars($order['product_name']); ?>
                                        </p>
                                        <?php
                                        $statusClass = (stripos($order['status'], 'Отменен') !== false) ? 'bg-danger' : 'bg-success';
                                        ?>
                                        <span class="badge order-status <?= $statusClass; ?>">
                                            <?= htmlspecialchars($order['status']); ?>
                                        </span>
                                        <p class="order-quantity">Количество: <?= htmlspecialchars($order['quantity']); ?></p>
                                        <p class="order-total">Сумма заказа: &#8381; <?= number_format($order['price']); ?></p>

                                        <p class="order-date"><?= htmlspecialchars(date('d.m.Y', strtotime($order['created_at']))); ?></p>
                                    </div>
                                </div>
                            </a>
                            <?php if (stripos($order['status'], 'Доставлен') !== false): ?>
                                <div class="rating-section mt-2" data-product-id="<?= $order['product_id'] ?>">
                                    <?php if ($ratings[$order['product_id']]['rated'] ?? false): ?>
                                        <div class="text-muted small">
                                            Вы уже оценили этот товар
                                            <div class="d-inline-block ms-2">
                                                <?= Utils\RatingHelper::getRatingStars($ratings[$order['product_id']]['value']) ?>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="star-rating">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <span class="star <?= ($i <= ($ratings[$order['product_id']]['value'] ?? 0)) ? 'filled' : '' ?>"
                                                      data-rating="<?= $i ?>">★</span>
                                            <?php endfor; ?>
                                        </div>
                                        <small class="rating-status text-muted"></small>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-light text-center small">Нет завершённых заказов.</div>
                <?php endif; ?>
            </div>
        </div>

        <div id="avatar-picker-modal" class="avatar-picker-modal">
            <div class="avatar-picker-modal-content">
                <span class="avatar-picker-close">&times;</span>
                <h5 class="fw-semibold text-center mb-3">Выберите аватар</h5>
                <div class="d-flex flex-wrap gap-2 justify-content-center" id="avatar-picker-selection">
                    <?php
                    $avatarPath = "/assets/images/avatars/";
                    foreach ($avatars as $avatar) {
                        echo '<img src="' . $avatarPath . $avatar . '" class="avatar-picker-option rounded-circle border shadow-sm" width="60" data-avatar="' . $avatar . '">';
                    }
                    ?>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-warning text-center">Пользователь не найден.</div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>