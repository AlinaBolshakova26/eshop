<form id="tags-form" action="/admin/tags/process" method="POST">

    <div class="d-flex justify-content-center align-items-center gap-3 mt-3">
        <button type="submit" name="action" value="deactivate" class="btn btn-danger">Деактивировать</button>
        <button type="submit" name="action" value="activate" class="btn btn-success">Активировать</button>
        <a href="<?= url('admin.tags.create') ?>" class="btn btn-primary">+ Создать тег</a>
    </div>

    <table class="table">
        <thead>
        <tr>
            <th><input type="checkbox" id="select-all"></th>
            <th>ID</th>
            <th>Название</th>
            <th>Активность</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
		<?php foreach ($tags as $tag): ?>
            <tr class="tag-row <?php echo $tag->getIsActive() ? '' : 'inactive-tag'; ?>">
                <td><input type="checkbox" class="tag-checkbox" name="selected_tags[]" value="<?php echo htmlspecialchars($tag->getId()); ?>"></td>
                <td><?php echo htmlspecialchars($tag->getId()); ?></td>
                <td><?php echo htmlspecialchars($tag->getName()); ?></td>
                <td><?php echo htmlspecialchars($tag->getIsActive() ? 'true' : 'false'); ?></td>
                <td>
                    <a href="<?= url('admin.tags.edit', ['id' => $tag->getId()]) ?>" class="btn btn-sm btn-warning">Редактировать</a> 
                </td>
            </tr>
		<?php endforeach; ?>
        </tbody>
    </table>
</form>

<?php if ($totalPages > 1): ?>
    <nav aria-label="Tags pagination">
        <ul class="pagination justify-content-center">
			<?php if ($currentPage > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="<?= url('admin.tags', ['page' => $currentPage - 1]) ?>"><<</a>
                </li>
			<?php endif; ?>

			<?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?php echo $currentPage == $i ? 'active' : ''; ?>">
                    <a class="page-link" href="<?= url('admin.tags', ['page' => $i]) ?>"><?php echo $i; ?></a>
                </li>
			<?php endfor; ?>

			<?php if ($currentPage < $totalPages): ?>
                <li class="page-item">
                    <a class="page-link" href="<?= url('admin.tags', ['page' => $currentPage + 1]) ?>"> >> </a>
                </li>
			<?php endif; ?>
        </ul>
    </nav>
<?php endif; ?>