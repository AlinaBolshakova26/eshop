<div class="container mt-4">
    <form action="/admin/ratings/delete" method="POST">
        <div class="mb-3">
            <button type="submit" class="btn btn-danger">Удалить выбранные</button>
        </div>
        <table class="table table-hover">
            <thead>
            <tr>
                <th><input type="checkbox" id="checkAll"></th>
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
                <tr ondblclick="window.location='/admin/ratings/<?= htmlspecialchars($rating->id) ?>';" style="cursor: pointer;">
                    <td>
                        <input type="checkbox" name="rating_ids[]" value="<?= htmlspecialchars($rating->id) ?>">
                    </td>
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

<script>

    document.getElementById('checkAll').addEventListener('change', function () {
        const checkboxes = document.querySelectorAll('input[name="rating_ids[]"]');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });

    document.querySelectorAll('input[name="rating_ids[]"]').forEach(cb => {
        cb.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });
</script>
