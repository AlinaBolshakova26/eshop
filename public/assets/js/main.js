$(document).ready(function() {
    $('.product-images-slider').each(function() {
        var $slider = $(this);
        var $images = $slider.find('img');
        var currentIndex = 0;
        updateImage();

        // Click event for right arrow
        $slider.find('.right-arrow').click(function() {
            currentIndex = (currentIndex + 1) % $images.length; // Move to the next image
            updateImage();
        });

        // Click event for left arrow
        $slider.find('.left-arrow').click(function() {
            currentIndex = (currentIndex - 1 + $images.length) % $images.length; // Move to the previous image
            updateImage();
        });


        function updateImage() {
            $images.addClass('d-none'); // Hide all images
            $images.eq(currentIndex).removeClass('d-none'); // Show the current image
        }
    });


    // Product gallery
    if ($('.product-gallery').length) {
        $('.thumbnail-images img').click(function() {
            const newSrc = $(this).attr('src');
            $('.main-image img').fadeOut(200, function() {
                $(this).attr('src', newSrc).fadeIn(200);
            });
        });
    }

    // Form validation styles
    $('form input, form textarea').on('input', function() {
        if ($(this).hasClass('is-invalid')) {
            $(this).removeClass('is-invalid');
        }
    });

    // Add to cart animation
    $('.add-to-cart').click(function(e) {
        e.preventDefault();
        const $btn = $(this);
        $btn.prop('disabled', true);
        
        // Show success feedback
        $btn.html('<i class="fas fa-check"></i> Added!');
        
        setTimeout(function() {
            $btn.html('Add to Cart').prop('disabled', false);
        }, 2000);
    });

    // Responsive navbar
    $('.navbar-toggler').click(function() {
        $('.navbar-collapse').slideToggle();
    });

    // Smooth scroll to top
    $('.back-to-top').click(function(e) {
        e.preventDefault();
        $('html, body').animate({scrollTop: 0}, 'slow');
    });
});
