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
});
