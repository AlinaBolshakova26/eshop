<h1>Редактирование тега</h1>

<form method="POST" action="/admin/tags/edit/<?= htmlspecialchars($tag->getId()) ?>">
	<div>
		<label for="name">Название тега:</label>
		<input type="text" id="name" name="name" value="<?= htmlspecialchars($tag->getName()) ?>" required>
	</div>

	<div>
		<label>
			<input type="checkbox" name="is_active" <?= $tag->getIsActive() ? 'checked' : '' ?>>
			Активный
		</label>
	</div>

	<button type="submit">Сохранить изменения</button>
</form>