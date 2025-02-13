<div class="container mt-5">
    <?php if (isset($error) && $error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if (isset($message) && $message): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if ($user): ?>
        <div class="text-center">
            <div class="avatar-picker-container d-inline-block mb-3">
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
                    <div class="mb-3">
                        <label for="name" class="form-label">Имя</label>
                        <input type="text" class="form-control" name="name" id="name" value="<?= htmlspecialchars($user['name']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" id="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Телефон</label>
                        <input type="text" class="form-control" name="phone" id="phone" value="<?= htmlspecialchars($user['phone']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Новый пароль (необязательно)</label>
                        <input type="password" class="form-control" name="password" id="password">
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-success">Сохранить изменения</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="mt-5">
            <h4 class="text-center mb-4">Активные заказы</h4>
            <?php
            $pendingOrders = array_filter($orders, function($order) {
                return (stripos($order['status'], 'Доставлен') === false && stripos($order['status'], 'Отменен') === false);
            });
            ?>
            <?php if (!empty($pendingOrders)): ?>
                <div class="row">
                    <?php foreach ($pendingOrders as $order): ?>
                        <div class="col-md-6 mb-3">
                            <div class="card shadow-sm border-0 rounded-3">
                                <div class="row g-0">
                                    <div class="col-4">
                                        <img src="<?= htmlspecialchars($order['main_image'] ?? '/img/no-image.png'); ?>" class="img-fluid rounded-start" alt="Товар">
                                    </div>
                                    <div class="col-8">
                                        <div class="card-body">
                                            <h6 class="card-title">
                                                <a href="/product/<?= $order['product_id']; ?>" class="text-dark text-decoration-none">
                                                    <?= htmlspecialchars($order['product_name']); ?>
                                                </a>
                                            </h6>
                                            <p class="mb-1"><strong>Статус:</strong> <?= htmlspecialchars($order['status']); ?></p>
                                            <p class="mb-1"><strong>Цена:</strong> &#8381; <?= number_format($order['price']); ?></p>
                                            <p class="mb-0 text-muted" style="font-size: 0.9rem;">Дата: <?= htmlspecialchars($order['created_at']); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-light text-center">Нет активных заказов.</div>
            <?php endif; ?>
        </div>

        <div class="text-center mt-4">
            <button class="btn btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#otherOrdersCollapse" aria-expanded="false" aria-controls="otherOrdersCollapse">
                Показать другие
            </button>
        </div>
        <div class="collapse mt-4" id="otherOrdersCollapse">
            <h4 class="text-center mb-4">Завершённые заказы</h4>
            <?php
            $otherOrders = array_filter($orders, function($order) {
                return (stripos($order['status'], 'Доставлен') !== false || stripos($order['status'], 'Отменен') !== false);
            });
            ?>
            <?php if (!empty($otherOrders)): ?>
                <div class="row">
                    <?php foreach ($otherOrders as $order): ?>
                        <div class="col-md-6 mb-3">
                            <div class="card shadow-sm border-0 rounded-3">
                                <div class="row g-0">
                                    <div class="col-4">
                                        <img src="<?= htmlspecialchars($order['main_image'] ?? '/img/no-image.png'); ?>" class="img-fluid rounded-start" alt="Товар">
                                    </div>
                                    <div class="col-8">
                                        <div class="card-body">
                                            <h6 class="card-title">
                                                <a href="/product/<?= $order['product_id']; ?>" class="text-dark text-decoration-none">
                                                    <?= htmlspecialchars($order['product_name']); ?>
                                                </a>
                                            </h6>
                                            <p class="mb-1"><strong>Статус:</strong> <?= htmlspecialchars($order['status']); ?></p>
                                            <p class="mb-1"><strong>Цена:</strong> &#8381; <?= number_format($order['price']); ?></p>
                                            <p class="mb-0 text-muted" style="font-size: 0.9rem;">Дата: <?= htmlspecialchars($order['created_at']); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-light text-center">Нет завершённых заказов.</div>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-warning text-center">Пользователь не найден.</div>
    <?php endif; ?>

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
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>