document.addEventListener("DOMContentLoaded", function () {
    function updateCartTotal() {
        let total = 0;
        document.querySelectorAll('.update-quantity').forEach(function(input) {
            const price = parseFloat(input.dataset.price);
            const quantity = parseInt(input.value) || 0;
            total += price * quantity;
        });
        const totalElement = document.getElementById('cart-total');
        if (totalElement) {
            totalElement.textContent = 'Итого: ₽' + total.toFixed(2);
        }
    }

    document.querySelectorAll('.update-quantity').forEach(function(input) {
        input.addEventListener('change', function() {
            let form = input.closest('form');
            let formData = new FormData(form);
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateCartTotal();
                    }
                })
                .catch(error => console.error('Ошибка при обновлении корзины:', error));
        });

        input.addEventListener('input', function() {
            updateCartTotal();
        });
    });

    document.querySelectorAll('.remove-from-cart').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const itemId = this.dataset.itemId;

            fetch('/cart/remove', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: 'item_id=' + encodeURIComponent(itemId)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Удаляем элемент из DOM
                        this.closest('.cart-item').remove();
                        updateCartTotal(); // Обновляем общую стоимость
                    } else {
                        alert('Ошибка: Не удалось удалить товар');
                    }
                })
                .catch(error => console.error('Ошибка:', error));
        });
    });
});