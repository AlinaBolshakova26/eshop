<div class="container mt-4">
    <table class="table table-hover">
        <thead>
        <tr>
            <th>ID</th>
            <th>Пользователь</th>
            <th>Товар</th>
            <th>Оценка</th>
            <th>Комментарий</th>
            <th>Дата оценки</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($ratings as $rating): ?>
            <tr ondblclick="window.location='/admin/ratings/<?= $rating->id ?>';" style="cursor: pointer;">
                <td><?= htmlspecialchars($rating->id) ?></td>
                <td><?= htmlspecialchars($rating->userName) ?></td>
                <td><?= htmlspecialchars($rating->productName) ?></td>
                <td><?= htmlspecialchars($rating->rating) ?>/5</td>
                <td><?= htmlspecialchars($rating->comment ? mb_substr($rating->comment, 0, 30) . '...' : '—') ?></td>
                <td><?= date('d.m.Y H:i', strtotime($rating->createdAt)) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

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