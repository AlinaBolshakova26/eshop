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
<main class="container my-4">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <h1 class="display-4">Ошибка <?= $code ?></h1>
            <p class="lead"><?= htmlspecialchars($error) ?></p>
            <a href="/" class="btn btn-primary">Вернуться на главную</a>
        </div>
    </div>
</div>
</main>
</body>
</html>
