<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card mt-5 shadow-sm">
            <div class="card-header text-center bg-white border-0">
                <h3 class="mb-0">Регистрация</h3>
            </div>
            <div class="card-body">
                <?php if (isset($error) && $error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <form method="post" action="/user/register">
                    <div class="mb-3">
                        <label for="name" class="form-label">Имя</label>
                        <input type="text" class="form-control" id="name" name="name"
                               placeholder="Введите ваше имя"
                               value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>"
                               required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Телефон</label>
                        <input type="text" class="form-control" id="phone" name="phone"
                               placeholder="Введите номер телефона"
                               value="<?= isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>"
                               required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email"
                               placeholder="Введите ваш email"
                               value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                               required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Пароль</label>
                        <input type="password" class="form-control" id="password" name="password"
                               placeholder="Введите пароль" required>
                    </div>
                    <div class="mb-3">
                        <label for="password_confirm" class="form-label">Подтверждение пароля</label>
                        <input type="password" class="form-control" id="password_confirm" name="password_confirm"
                               placeholder="Подтвердите пароль" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Зарегистрироваться</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
