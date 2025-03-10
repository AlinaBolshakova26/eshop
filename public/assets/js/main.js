$(document).ready(function() {

    $('.product-images-slider').each(function() {
        var $slider = $(this);
        var $images = $slider.find('img');
        var currentIndex = 0;
        updateImage();

        $slider.find('.right-arrow').click(function() {
            currentIndex = (currentIndex + 1) % $images.length;
            updateImage();
        });

        $slider.find('.left-arrow').click(function() {
            currentIndex = (currentIndex - 1 + $images.length) % $images.length;
            updateImage();
        });

        function updateImage() {
            $images.addClass('d-none');
            $images.eq(currentIndex).removeClass('d-none');
        }
    });

    if ($('.product-gallery').length) {
        $('.thumbnail-images img').click(function() {
            const newSrc = $(this).attr('src');
            $('.main-image img').fadeOut(200, function() {
                $(this).attr('src', newSrc).fadeIn(200);
            });
        });
    }

    $('form input, form textarea').on('input', function() {
        if ($(this).hasClass('is-invalid')) {
            $(this).removeClass('is-invalid');
        }
    });

    $('.add-to-cart').click(function(e) {
        e.preventDefault();
        const $btn = $(this);
        if ($btn.prop('disabled')) return;

        $btn.prop('disabled', true).html('<i class="fas fa-check"></i> Added!');

        setTimeout(function() {
            $btn.html('Add to Cart').prop('disabled', false);
        }, 2000);
    });

    $('.navbar-toggler').click(function() {
        $('.navbar-collapse').slideToggle();
    });

    $('.back-to-top').click(function(e) {
        e.preventDefault();
        $('html, body').animate({scrollTop: 0}, 'slow');
    });

    $('.product-card').dblclick(function() {
        const productLink = $(this).find('.btn-primary').attr('href');
        if (productLink) {
            window.location.href = productLink;
        }
    });

    var modalImages = [];
    $('.product-gallery img').each(function() {
        var src = $(this).attr('src');
        if (modalImages.indexOf(src) === -1) {
            modalImages.push(src);
        }
    });

    var modalCurrentIndex = 0;
    var $imageModal = $('#imageModal');
    var $modalImage = $('#modalImage');

    $('.product-gallery img').click(function() {
        var src = $(this).attr('src');
        modalCurrentIndex = modalImages.indexOf(src);
        $modalImage.attr('src', src);
        $imageModal.fadeIn(300);
    });

    $('.modal-close').click(function() {
        $imageModal.fadeOut(300);
    });

    $imageModal.click(function(e) {
        if (!$(e.target).closest('.modal-content').length) {
            $imageModal.fadeOut(300);
        }
    });

    $('.modal-next').click(function(e) {
        e.stopPropagation();
        modalCurrentIndex = (modalCurrentIndex + 1) % modalImages.length;
        $modalImage.fadeOut(200, function() {
            $modalImage.attr('src', modalImages[modalCurrentIndex]).fadeIn(200);
        });
    });

    $('.modal-prev').click(function(e) {
        e.stopPropagation();
        modalCurrentIndex = (modalCurrentIndex - 1 + modalImages.length) % modalImages.length;
        $modalImage.fadeOut(200, function() {
            $modalImage.attr('src', modalImages[modalCurrentIndex]).fadeIn(200);
        });
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const avatarPickerModal = document.getElementById("avatar-picker-modal");
    const openAvatarPickerBtn = document.getElementById("open-avatar-picker");
    const closeAvatarPicker = document.querySelector(".avatar-picker-close");
    const avatarPickerOptions = document.querySelectorAll(".avatar-picker-option");
    const selectedAvatarInput = document.getElementById("selected-avatar");
    const currentAvatar = document.getElementById("current-avatar");

    openAvatarPickerBtn.addEventListener("click", function () {
        avatarPickerModal.style.display = "flex";
    });

    closeAvatarPicker.addEventListener("click", function () {
        avatarPickerModal.style.display = "none";
    });

    avatarPickerOptions.forEach(avatar => {
        avatar.addEventListener("click", function () {
            avatarPickerOptions.forEach(img => img.classList.remove("selected"));
            this.classList.add("selected");
            selectedAvatarInput.value = this.getAttribute("data-avatar");
            currentAvatar.src = "/assets/images/avatars/" + this.getAttribute("data-avatar");

            fetch("/user/update-avatar", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ avatar: this.getAttribute("data-avatar") })
            })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        alert("Ошибка: " + (data.error || "Не удалось обновить аватар"));
                    }
                })
                .catch(error => console.error("Ошибка сохранения аватара:", error));

            avatarPickerModal.style.display = "none";
        });
    });

    window.addEventListener("click", function (e) {
        if (e.target === avatarPickerModal) {
            avatarPickerModal.style.display = "none";
        }
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const tagButtons = document.querySelectorAll(".tag-btn");
    const resultsContainer = document.getElementById("results-container");
    const selectedTagsQueue = [];

    function updateUrl() {
        const selectedTags = Array.from(selectedTagsQueue)
            .map(button => button.getAttribute("data-tag-id"))
            .filter(id => id !== "all");

        let url = "";
        if (selectedTags.length > 0) {
            url += `?tags=${selectedTags.join(',')}`;
        }

        history.pushState(null, null, url);
    }

    tagButtons.forEach(button => {
        button.addEventListener("click", function () {
            const isAllButton = button.getAttribute("data-tag-id") === "all";

            if (isAllButton) {
                clearFilters();
                return;
            }

            if (button.classList.contains("active")) {
                const index = selectedTagsQueue.indexOf(button);
                if (index !== -1) {
                    selectedTagsQueue.splice(index, 1);
                    button.classList.remove("active");
                }
            } else {
                button.classList.add("active");
                selectedTagsQueue.push(button);

                if (selectedTagsQueue.length > 3) {
                    const firstTag = selectedTagsQueue.shift();
                    firstTag.classList.remove("active");
                }
            }
            updateUrl();
            fetchResults();
        });
    });

    function clearFilters() {
        tagButtons.forEach(btn => btn.classList.remove("active"));
        document.querySelector('.tag-btn[data-tag-id="all"]').classList.add("active");
        selectedTagsQueue.length = 0;

        window.location.href = "/";
    }

    function fetchResults() {
        const selectedTags = Array.from(selectedTagsQueue)
            .map(button => button.getAttribute("data-tag-id"))
            .filter(id => id !== "all");

        let url = "";
        if (selectedTags.length > 0) {
            url += `?tags=${selectedTags.join(',')}`;
        }

        fetch(url)
            .then(response => response.text())
            .then(html => {
                resultsContainer.innerHTML = html; // Заменяем содержимое контейнера
            })
            .catch(error => console.error("Ошибка при загрузке данных:", error));
    }

    function restoreSelectedTagsFromUrl() {
        const urlParams = new URLSearchParams(window.location.search);
        const tagsParam = urlParams.get('tags');

        if (tagsParam) {
            const tagIds = tagsParam.split(',').map(id => parseInt(id, 10));

            tagButtons.forEach(button => {
                const tagId = parseInt(button.getAttribute("data-tag-id"), 10);
                if (tagIds.includes(tagId)) {
                    button.classList.add("active");
                    selectedTagsQueue.push(button);
                }
            });
        }
    }
    restoreSelectedTagsFromUrl();
});
document.querySelectorAll('.star-rating .star').forEach(star => {
    star.addEventListener('click', function(e) {
        e.stopPropagation(); // Предотвращаем всплытие события
        const productId = this.closest('.rating-section').dataset.productId;
        const ratingValue = this.dataset.rating;

        if (this.dataset.rated === 'true') return;

        rateProduct(productId, ratingValue);
    });
});

document.querySelectorAll('.order-card').forEach(card => {
    card.addEventListener('click', function(e) {
        if (!e.target.closest('.rating-section')) {
            window.location = this.closest('.order-card-link').href;
        }
    });
});
