<div class="main-content-detail">
    <div class="container">
        <div class="card shadow mt-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2 class="mb-0">Оценка #<?= htmlspecialchars($rating->id) ?></h2>
                <span class="badge bg-primary"><?= htmlspecialchars($rating->rating) ?>/5</span>
            </div>
            <div class="card-body">
                <h4><i class="fas fa-user"></i> Пользователь</h4>
                <p><strong>ID:</strong> <?= htmlspecialchars($rating->userId ?? '—') ?></p>
                <p><strong>Имя:</strong> <?= htmlspecialchars($rating->userName ?? '—') ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($rating->userEmail ?? '—') ?></p>
                <p><strong>Телефон:</strong> <?= htmlspecialchars($rating->userPhone ?? '—') ?></p>

                <h4 class="mt-3"><i class="fas fa-box"></i> Товар</h4>
                <p><strong>ID:</strong> <?= htmlspecialchars($rating->productId ?? '—') ?></p>
                <p><strong>Название:</strong> <?= htmlspecialchars($rating->productName ?? '—') ?></p>
                <a href="/product/<?= htmlspecialchars($rating->productId ?? '') ?>" class="btn btn-outline-secondary mt-2">
                    <i class="fas fa-arrow-right"></i> Перейти к товару
                </a>

                <h4 class="mt-3"><i class="fas fa-star"></i> Оценка</h4>
                <p><strong>Значение:</strong> <?= htmlspecialchars($rating->rating) ?>/5</p>
                <p><strong>Комментарий:</strong><br><?= nl2br(htmlspecialchars($rating->comment ?? '—')) ?></p>
                <p><strong>Дата оценки:</strong> <?= date('d.m.Y H:i', strtotime($rating->createdAt ?? 'now')) ?></p>

                <h4 class="mt-3"><i class="fas fa-receipt"></i> Связанный заказ</h4>
                <p><strong>ID заказа:</strong> <?= htmlspecialchars($rating->orderId ?? '—') ?></p>
                <p><strong>Дата заказа:</strong> <?= htmlspecialchars($rating->orderDate ?? '—') ?></p>

                <a href="/admin/ratings" class="btn btn-primary mt-3"><i class="fas fa-arrow-left"></i> Назад</a>
            </div>
        </div>
    </div>
</div>