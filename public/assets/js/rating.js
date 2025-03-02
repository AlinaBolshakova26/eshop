document.querySelectorAll('.rating-section').forEach(ratingSection => {
    const productId = ratingSection.dataset.productId;
    const starRating = ratingSection.querySelector('.star-rating');
    const commentSection = document.querySelector(`.comment-section[data-product-id="${productId}"]`);
    let selectedRating = 0;

    // Если нет секции с рейтингом или пользователь уже оценил товар, останавливаем обработку
    if (!starRating || starRating.querySelector('.star[data-rated="true"]')) {
        return;
    }

    // Обработка выбора звезд
    starRating.querySelectorAll('.star').forEach(star => {
        star.addEventListener('click', () => {
            selectedRating = parseInt(star.dataset.rating);
            starRating.querySelectorAll('.star').forEach((s, index) => {
                s.classList.toggle('filled', index < selectedRating);
            });
        });
    });

    // Обработка комментария и отправки формы
    if (commentSection) {
        const textarea = commentSection.querySelector('textarea');
        const charCounter = commentSection.querySelector('.char-counter');
        const submitButton = commentSection.querySelector('.submit-comment');

        // Счетчик символов
        if (textarea && charCounter) {
            textarea.addEventListener('input', () => {
                const remaining = 500 - textarea.value.length;
                charCounter.textContent = `Осталось символов: ${remaining}`;
            });
        }

        // Отправка формы
        if (submitButton) {
            submitButton.addEventListener('click', async () => {
                if (!selectedRating) {
                    showFeedback(ratingSection, 'Пожалуйста, выберите оценку', false);
                    return;
                }

                const comment = textarea ? textarea.value.trim() : '';

                try {
                    const response = await fetch('/rating/create', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            product_id: productId,
                            rating: selectedRating,
                            comment: comment
                        })
                    });

                    if (!response.ok) {
                        const errorData = await response.json();
                        throw new Error(errorData.error || `HTTP error ${response.status}`);
                    }

                    const result = await response.json();

                    if (result.success) {
                        // Обновляем UI, показывая комментарий как отправленный
                        const cardBody = commentSection.querySelector('.card-body');
                        if (cardBody) {
                            cardBody.innerHTML = `
                                <p class="mb-1">Ваш комментарий:</p>
                                <p class="comment-text">${comment || 'Без комментария'}</p>
                            `;
                            cardBody.classList.add('text-muted', 'small');
                        }

                        // Помечаем звезды как заблокированные
                        starRating.querySelectorAll('.star').forEach((s, index) => {
                            s.classList.toggle('filled', index < selectedRating);
                            s.dataset.rated = 'true';
                        });

                        showFeedback(ratingSection, 'Спасибо за отзыв!', true);
                    } else {
                        throw new Error(result.error || 'Неизвестная ошибка');
                    }

                } catch (error) {
                    console.error('Ошибка:', error);
                    showFeedback(ratingSection, error.message, false);
                }
            });
        }
    }
});

function showFeedback(container, message, isSuccess) {
    const statusElement = container.querySelector('.rating-status');
    if (statusElement) {
        statusElement.textContent = message;
        statusElement.style.color = isSuccess ? 'green' : 'red';
        if (isSuccess) {
            setTimeout(() => statusElement.textContent = '', 3000);
        }
    }
}

