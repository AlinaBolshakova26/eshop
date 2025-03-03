<?php if (isset($_GET['error'])): ?>
  <div class="alert alert-danger">
    <?php
    switch ($_GET['error']) {
      case 'no_products_selected':
        echo 'Не выбрано ни одного товара.';
        break;
      case 'invalid_action':
        echo 'Неизвестное действие.';
        break;
      case 'database_error':
        echo 'Произошла ошибка при обработке запроса.';
        break;
      default:
        echo 'Произошла ошибка.';
    }
    ?>
  </div>
<?php endif; ?>

<?php if (isset($_GET['success'])): ?>
  <div class="alert alert-success">
    Действие успешно выполнено.
  </div>
<?php endif; ?>
