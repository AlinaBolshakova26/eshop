<div class="container mt-4">
    <form method="POST" action="/admin/orders">
        <div class="d-flex justify-content-between mb-4">
            <button type="submit" class="btn btn-danger" name="cancel_orders">
                Отменить выбранные
            </button>
        </div>

        <table class="table table-hover">
            <thead>
            <tr>
                <th><input type="checkbox" id="select-all"></th>
                <th>ID</th>
                <th>Пользователь</th>
                <th>Товар</th>
                <th>Цена</th>
                <th>Адрес</th>
                <th>Статус</th>
                <th>Действия</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($orders as $order): ?>
                <tr ondblclick="window.location='/admin/orders/<?= $order['id'] ?>';" style="cursor: pointer;">
                    <td>
                        <input type="checkbox" class="order-checkbox" name="cancel_order_ids[]" value="<?= htmlspecialchars($order['id']) ?>">
                    </td>
                    <td><?= htmlspecialchars($order['id']) ?></td>
                    <td><?= htmlspecialchars($order['user_id']) ?></td>
                    <td><?= htmlspecialchars($order['item_id']) ?></td>
                    <td><?= number_format($order['price'], 2) ?> ₽</td>
                    <td><?= htmlspecialchars($order['city'] . ', ' . $order['street'] . ', ' . $order['house'] . ($order['apartment'] ? ', кв. ' . $order['apartment'] : '')) ?></td>
                    <td>
                        <span class="badge bg-<?= match($order['status']) {
                            'Создан' => 'secondary',
                            'В пути' => 'info',
                            'Доставлен' => 'success',
                            default => 'danger'
                        } ?>">
                            <?= htmlspecialchars($order['status']) ?>
                        </span>
                    </td>
                    <td>
                        <form method="POST" action="/admin/orders">
                            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                            <div class="btn-group">
                                <?php foreach (['Создан', 'В пути', 'Доставлен'] as $status): ?>
                                    <button
                                        type="submit"
                                        name="status"
                                        value="<?= $status ?>"
                                        class="btn btn-sm <?= $order['status'] === $status ? 'btn-dark disabled' : 'btn-outline-primary' ?>"
                                    >
                                        <?= $status ?>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </form>

    <?php if ($totalPages > 1): ?>
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>