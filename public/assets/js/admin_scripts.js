$(document).ready(function() {
    $('.sidebar-toggle').click(function() {
        $('.sidebar').toggleClass('open');
        $('.main-content').toggleClass('shift');
    });

    if ($(window).width() >= 768) {
        $('.sidebar').addClass('open');
        $('.main-content').addClass('shift');
    }

    $(document).click(function(event) {
        if (!$(event.target).closest('.sidebar, .sidebar-toggle').length) {
            $('.sidebar').removeClass('open');
            $('.main-content').removeClass('shift');
        }
    });

    $('#select-all').change(function() {
        $('.product-checkbox, .order-checkbox').prop('checked', this.checked);
    });
});