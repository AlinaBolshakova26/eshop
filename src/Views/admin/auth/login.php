<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход</title>
    <link rel="stylesheet" href="/assets/css/style_admin.css">
    <link rel="shortcut icon" href="/assets/images/short_logo_admin.png" />
</head>
<body>
<div class="login-container">
    <h1>Вход для администраторов</h1>

    <form method="POST" action="<?= url('admin.auth') ?>">
        <div class="form-group">
            <input type="email" name="email" placeholder="Email" required>
        </div>
        <div class="form-group">
            <input type="password" name="password" placeholder="Пароль" required>
        </div>
        <?php if (isset($_GET['error']) && $_GET['error'] == 1): ?>
            <p class="error-message">Email или пароль указаны неверно!</p>
        <?php endif; ?>
        <button type="submit">Войти</button>
    </form>
</div>
</body>
</html>