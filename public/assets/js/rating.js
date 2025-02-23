document.querySelectorAll('.star-rating').forEach(rating => {
    const productId = rating.closest('.rating-section').dataset.productId;

    rating.querySelectorAll('.star').forEach(star => {
        star.addEventListener('click', async () => {
            const ratingValue = star.dataset.rating;

            if (rating.querySelector('.star[data-rated="true"]')) {
                showRatingStatus('Вы уже оценили этот товар', 'text-danger');
                return;
            }

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
                    rating.querySelectorAll('.star').forEach((s, index) => {
                        s.classList.toggle('filled', index < ratingValue);
                    });
                    showRatingFeedback('Оценка сохранена!', true);
                } else {
                    showRatingFeedback(result.error || 'Ошибка сохранения оценки', false);
                }
                if (response.ok) {
                    e.target.setAttribute('data-rated', 'true');
                    showRatingStatus('Спасибо за оценку!', 'text-success');
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