<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card mt-5 shadow-sm">
            <div class="card-header text-center bg-white border-0">
                <h3 class="mb-0">Вход в систему</h3>
            </div>
            <div class="card-body">
                <?php if (isset($error) && $error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <form method="post" action="/user/login">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input
                                type="email"
                                class="form-control"
                                id="email"
                                name="email"
                                placeholder="Введите ваш email"
                                required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Пароль</label>
                        <input
                                type="password"
                                class="form-control"
                                id="password"
                                name="password"
                                placeholder="Введите пароль"
                                required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-login">Войти</button>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center bg-white border-0">
                <p class="mb-2">Еще нет аккаунта?</p>
                <a href="/user/register" class="btn btn-outline-primary">Создайте его!</a>
            </div>
        </div>
    </div>
</div>