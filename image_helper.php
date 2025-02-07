<?php
// Параметры подключения к базе данных
$host = 'localhost'; // Хост базы данных
$dbname = 'ecommerce'; // Имя базы данных
$username = 'bx_market'; // Имя пользователя базы данных
$password = '1402'; // Пароль пользователя базы данных

try {
	// Подключение к базе данных
	$pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	// Проверка, был ли отправлен файл
	if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['images'])) {
		$item_id = $_POST['item_id']; // ID продукта из up_item
		$main_image_index = $_POST['main_image']; // Индекс основного изображения

		// Обработка каждого изображения
		foreach ($_FILES['images']['tmp_name'] as $index => $tmp_name) {
			if ($_FILES['images']['error'][$index] === UPLOAD_ERR_OK) {
				$original_name = basename($_FILES['images']['name'][$index]); // Исходное имя файла
				$file_extension = pathinfo($original_name, PATHINFO_EXTENSION); // Расширение файла
				$unique_name = uniqid() . '_' . time() . '.' . $file_extension; // Уникальное имя файла

				// Определение пути для сохранения
				if ($index == $main_image_index) {
					$sql_path = '/assets/images/main_product_images/' . $unique_name; // Путь для сохранения изображения
					$path = '/home/slowpoke/BXFlowerT/eshop/public/assets/images/main_product_images/' . $unique_name;
					$is_main = 1;
				} else {
					$sql_path = '/assets/images/additional_product_images/' . $unique_name; // Путь для сохранения изображения
					$path = '/home/slowpoke/BXFlowerT/eshop/public/assets/images/additional_product_images/' . $unique_name;
					$is_main = 0;
				}

				// Перемещение загруженного файла
				if (move_uploaded_file($tmp_name, $path)) {
					// Получение размеров изображения
					list($width, $height) = getimagesize($path);

					// Текущая дата и время
					$created_at = $updated_at = date('Y-m-d H:i:s');

					// Подготовка SQL-запроса для вставки данных
					$sql = "INSERT INTO up_image (item_id, path, is_main, width, height, created_at, updated_at, description) 
                            VALUES (:item_id, :path, :is_main, :width, :height, :created_at, :updated_at, :description)";
					$stmt = $pdo->prepare($sql);

					// Привязка параметров
					$stmt->bindParam(':item_id', $item_id);
					$stmt->bindParam(':path', $sql_path);
					$stmt->bindParam(':is_main', $is_main);
					$stmt->bindParam(':width', $width);
					$stmt->bindParam(':height', $height);
					$stmt->bindParam(':created_at', $created_at);
					$stmt->bindParam(':updated_at', $updated_at);
					$stmt->bindParam(':description', $original_name);

					// Выполнение запроса
					if ($stmt->execute()) {
						echo "Изображение $original_name успешно загружено и добавлено в таблицу up_image.<br>";
					} else {
						echo "Ошибка при добавлении изображения $original_name в таблицу.<br>";
					}
				} else {
					echo "Ошибка при загрузке изображения $original_name.<br>";
				}
			} else {
				echo "Ошибка при загрузке файла №" . ($index + 1) . ".<br>";
			}
		}
	}
} catch (PDOException $e) {
	echo "Ошибка подключения к базе данных: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Загрузка изображений для продукта</title>
</head>
<body>
<h1>Загрузка изображений для продукта</h1>
<form action="" method="post" enctype="multipart/form-data">
    <label for="item_id">ID продукта (item_id):</label>
    <input type="number" name="item_id" id="item_id" required><br><br>

    <label>Выберите основное изображение:</label><br>
    <input type="radio" name="main_image" value="0" required> Изображение 1<br>
    <input type="radio" name="main_image" value="1"> Изображение 2<br>
    <input type="radio" name="main_image" value="2"> Изображение 3<br><br>

    <label for="images">Выберите 3 изображения:</label><br>
    <input type="file" name="images[]" required><br>
    <input type="file" name="images[]" required><br>
    <input type="file" name="images[]" required><br><br>

    <button type="submit">Загрузить</button>
</form>
</body>
</html>