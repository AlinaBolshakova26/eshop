document.addEventListener("DOMContentLoaded", function () {
    let favoriteItems = JSON.parse(localStorage.getItem('favorites')) || [];

    function updateFavoritesUI() {
        document.querySelectorAll('.favorite-icon i').forEach(icon => {
            let itemId = icon.getAttribute('data-favorite');
            if (favoriteItems.includes(itemId)) {
                icon.classList.add('favorite-active');
                icon.classList.remove('favorite-inactive');
            } else {
                icon.classList.add('favorite-inactive');
                icon.classList.remove('favorite-active');
            }
        });
    }

    fetch('/favorites/data', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        },
        credentials: 'same-origin'
    })
        .then(response => response.json())
        .then(data => {
            if (data.success && Array.isArray(data.favorites)) {
                favoriteItems = data.favorites;
                localStorage.setItem('favorites', JSON.stringify(favoriteItems));
                updateFavoritesUI();
            }
        })
        .catch(error => console.error("Ошибка получения избранного:", error));

    document.querySelectorAll('.favorite-icon').forEach(icon => {
        icon.addEventListener('click', function (e) {
            e.preventDefault();
            let itemId = this.querySelector('i').getAttribute('data-favorite');

            fetch('/favorites/toggle', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'item_id=' + encodeURIComponent(itemId)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        let index = favoriteItems.indexOf(itemId);
                        if (index === -1) {
                            favoriteItems.push(itemId);
                        } else {
                            favoriteItems.splice(index, 1);
                        }
                        localStorage.setItem('favorites', JSON.stringify(favoriteItems));
                        updateFavoritesUI();
                    } else {
                        alert("Ошибка при изменении избранного.");
                    }
                })
                .catch(error => console.error("Ошибка:", error));
        });
    });

    document.querySelectorAll('.add-to-favorite-detail').forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            let itemId = this.getAttribute('data-item-id');
            fetch('/favorites/toggle', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'item_id=' + encodeURIComponent(itemId)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        let index = favoriteItems.indexOf(itemId);
                        if (index === -1) {
                            favoriteItems.push(itemId);
                        } else {
                            favoriteItems.splice(index, 1);
                        }
                        localStorage.setItem('favorites', JSON.stringify(favoriteItems));
                        // После успешного добавления перенаправляем на страницу избранного
                        window.location.href = '/favorites';
                    } else {
                        alert("Ошибка при изменении избранного.");
                    }
                })
                .catch(error => console.error("Ошибка:", error));
        });
    });

    document.querySelectorAll('.remove-from-favorites').forEach(function(button) {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            let itemId = this.getAttribute('data-item-id');
            let self = this;

            fetch('/favorites/remove/' + encodeURIComponent(itemId), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: ''
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        favoriteItems = favoriteItems.filter(id => id !== itemId);
                        localStorage.setItem('favorites', JSON.stringify(favoriteItems));

                        let card = self.closest('.col-md-4');
                        if (card) {
                            card.remove();
                        }

                        if (document.querySelectorAll('#favoritesContainer .col-md-4').length === 0) {
                            window.location.reload();
                        }
                    } else {
                        alert("Ошибка при удалении товара из избранного.");
                    }
                })
                .catch(error => console.error("Ошибка:", error));
        });
    });

    updateFavoritesUI();
});
