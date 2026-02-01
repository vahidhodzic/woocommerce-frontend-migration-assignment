jQuery(document).ready(function ($) {
    // Enhance add to cart forms
    $('.variations_form').on('show_variation', function (event, variation) {
        // Custom variation logic
    });

    // Quantity stepper
    $('.qty-btn-plus').click(function () {
        var qty = $(this).siblings('.qty');
        qty.val(parseInt(qty.val()) + 1);
    });
});
