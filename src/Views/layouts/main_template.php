<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle : 'Fleur Éclat'; ?></title>
    <link rel="shortcut icon" href="/assets/images/short_logo.png"/>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
          integrity="sha512-pap5...YOUR_INTEGRITY_HASH_HERE..." crossorigin="anonymous" referrerpolicy="no-referrer"/>

</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <div class="row w-100 align-items-center">
            <div class="col-md-3">
                <a href="/" class="logo-link">
                    <img src="/assets/images/logo.png" alt="logo" class="img-fluid">
                </a>
            </div>
            <div class="col-md-5">
                <form method="GET" action="/search" class="d-flex">
                    <input type="text" name="searchInput" class="form-control" placeholder="Поиск товаров...">
                    <button type="submit" class="btn btn-primary ms-2">Поиск</button>
                </form>
            </div>
            <div class="col-md-4 d-flex justify-content-end align-items-center">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="/cart" class="btn btn-outline-primary me-2">Корзина</a>
                    <a href="/favorites" class="btn btn-outline-primary me-2">
                        <i class="fa fa-heart header-favorite-icon"></i>
                    </a>
                    <a href="/user/profile" class="btn btn-outline-primary me-2">Профиль</a>
                    <a href="/user/logout" class="logout-link">
                        <img src="/assets/images/logout.png" alt="logout" class="img-fluid">
                    </a>
                <?php else: ?>
                    <a href="/user/login" class="btn btn-primary">Войти</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<main class="container my-4">
    <?= $content ?>
</main>

<footer class="bg-light py-4 mt-auto">
    <div class="container">
        <div class="row mt-3">
            <div class="col text-center">
                <h5>Остались вопросы? Свяжитесь с нами</h5>
                <p>Email: superprogeri@vasche.com<br>
                    Телефон: +1234567890</p>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col text-center">
                <p class="mb-0">&copy; <?= date('Y'); ?> Fleur Éclat. Все права защищены.</p>
            </div>
        </div>
    </div>
</footer>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="/assets/js/main.js"></script>
<script src="/assets/js/favorite.js"></script>

</body>
</html>