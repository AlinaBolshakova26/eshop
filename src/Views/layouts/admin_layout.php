<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Админка - Товары</title>
	<link href="/assets/css/bootstrap.min.css" rel="stylesheet">
	<link href="/assets/css/admin_style.css" rel="stylesheet">
</head>
<body>

<div class="sidebar-toggle">
	☰
</div>

<nav class="sidebar">
	<div class="sidebar-sticky">
		<ul class="nav flex-column">
			<li class="nav-item mb-4">
				<a class="btn btn-outline-danger logout mb-3" href="/admin/logout">
					<span>Выход</span>
				</a>
			</li>
			<li class="nav-item">
				<a class="nav-link active" href="/admin/products">
					<span>Товары</span>
				</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="/admin/orders">
					<span>Заказы</span>
				</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="/admin/tags">
					<span>Теги</span>
				</a>
			</li>
		</ul>
	</div>
</nav>
<main role="main" class="main-content">
	<?= $content ?>
</main>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="/assets/js/admin_scripts.js"></script>
</body>
</html>