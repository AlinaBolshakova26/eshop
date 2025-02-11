<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <?php if (isset($error) && $error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <?php if (isset($message) && $message): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
                    <?php endif; ?>

                    <?php if ($user): ?>
                        <div class="text-center mb-4">
                            <h4 class="fw-bold"><?= htmlspecialchars($user['name']) ?></h4>
                            <p class="text-muted mb-1"><?= htmlspecialchars($user['email']) ?></p>
                            <p class="text-muted"><?= htmlspecialchars($user['phone']) ?></p>
                        </div>
                        <form method="post" action="/user/update">
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
                            <div>
                                <label for="address" class="form-label"> Адрес:</label>
                                <input type="text" class="form-control" name="address" id="address" value="<?= htmlspecialchars($user['address'] ?? '') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Новый пароль (оставьте пустым, если не хотите менять)</label>
                                <input type="password" class="form-control" name="password" id="password">
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success">Обновить профиль</button>
                                <a href="/" class="btn btn-outline-secondary">На главную</a>
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-warning text-center">Пользователь не найден.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
