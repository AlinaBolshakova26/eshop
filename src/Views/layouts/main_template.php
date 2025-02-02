<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo isset($pageTitle) ? $pageTitle : 'Интернет-магазин'; ?></title>


	<link href="/assets/css/bootstrap.min.css" rel="stylesheet">
	<link href="/assets/css/style.css" rel="stylesheet">

</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
	<div class="container">
		<a class="navbar-brand" href="/">Интернет-магазин</a>
		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarNav">
			<ul class="navbar-nav">
				<li class="nav-item">
					<a class="nav-link" href="/">Главная</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="/products">Продукты</a>
				</li>
			</ul>
		</div>
	</div>
</nav>

<main class="container my-4">
	<?= $content ?>
</main>


<footer class="bg-light py-4 mt-auto">
	<div class="container">
		<div class="row">
			<div class="col-md-4">
				<h5>Связаться с нами</h5>
				<p>Email: info@example.com<br>
					Телефон: +1234567890</p>
			</div>
			<div class="col-md-4">
				<h5>Быстрые ссылки</h5>
				<ul class="list-unstyled">
					<li><a href="/privacy-policy">Политика конфиденциальности</a></li>
					<li><a href="/terms">Условия использования</a></li>
				</ul>
			</div>
			<div class="col-md-4">
				<h5>Подписаться на нас</h5>
				<div class="social-links">
					<a href="#" class="me-2">Facebook</a>
					<a href="#" class="me-2">Twitter</a>
					<a href="#">Instagram</a>
				</div>
			</div>
		</div>
		<div class="row mt-3">
			<div class="col text-center">
				<p class="mb-0">&copy; <?php echo date('Y'); ?> Интернет-магазин. Все права защищены.</p>
			</div>
		</div>
	</div>
</footer>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="/assets/js/main.js"></script>
</body>
</html>