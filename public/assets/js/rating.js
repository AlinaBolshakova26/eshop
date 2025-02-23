document.querySelectorAll('.star-rating').forEach(rating => {
    const productId = rating.closest('.rating-section').dataset.productId;

    rating.querySelectorAll('.star').forEach(star => {
        star.addEventListener('click', async () => {
            const ratingValue = star.dataset.rating;

            try {
                const response = await fetch('/rating/create', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        rating: ratingValue
                    })
                });

                const result = await response.json();
                if (result.success) {
                    // Обновляем отображение звезд
                    rating.querySelectorAll('.star').forEach((s, index) => {
                        s.classList.toggle('filled', index < ratingValue);
                    });
                    showRatingFeedback('Оценка сохранена!', true);
                } else {
                    showRatingFeedback(result.error || 'Ошибка сохранения оценки', false);
                }
            } catch (error) {
                showRatingFeedback('Ошибка сети', false);
            }
        });
    });
});

function showRatingFeedback(message, isSuccess) {
    const statusElement = document.querySelector('.rating-status');
    statusElement.textContent = message;
    statusElement.style.color = isSuccess ? 'green' : 'red';
    setTimeout(() => statusElement.textContent = '', 3000);
}