document.querySelectorAll('.star-rating').forEach(rating => {
    const productId = rating.closest('.rating-section').dataset.productId;

    rating.querySelectorAll('.star').forEach(star => {
        star.addEventListener('click', async () => {
            const ratingValue = star.dataset.rating;

            if (rating.querySelector('.star[data-rated="true"]')) {
                showRatingFeedback('Вы уже оценили этот товар', false);
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
                if (!response.ok) {
                    const errorText = await response.text();
                    throw new Error(`HTTP error ${response.status}: ${errorText}`);
                }

                const result = await response.json();

                if (!result.success) {
                    throw new Error(result.error || 'Неизвестная ошибка');
                }

                rating.querySelectorAll('.star').forEach((s, index) => {
                    s.classList.toggle('filled', index < ratingValue);
                    s.dataset.rated = "true";
                });
                showRatingFeedback('Спасибо за оценку!', true);

            } catch (error) {
                console.error('Ошибка:', error);
                showRatingFeedback(error.message, false);
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