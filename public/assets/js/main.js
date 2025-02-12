$(document).ready(function() {

    $('.product-images-slider').each(function() {
        var $slider = $(this);
        var $images = $slider.find('img');
        var currentIndex = 0;
        updateImage();

        // Обработка клика по правой стрелке
        $slider.find('.right-arrow').click(function() {
            currentIndex = (currentIndex + 1) % $images.length;
            updateImage();
        });

        // Обработка клика по левой стрелке
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
        $btn.prop('disabled', true);

        $btn.html('<i class="fas fa-check"></i> Added!');

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
        if ($(e.target).is('#imageModal')) {
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
