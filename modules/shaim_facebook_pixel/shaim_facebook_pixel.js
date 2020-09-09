$(document).ready(function () {

    // PS 1.4 - #add_to_cart > input
    $('.button-into-cart-large, .button-into-cart, #add_to_cart, #add_to_cart > input, .ajax_add_to_cart_button, .add-to-cart, #awp_add_to_cart > button').click(function () {
        console.log('fbpixel - add to cart click');
        if (typeof fb_product_page !== 'undefined' && fb_product_page == 1) {
            fbq('track', 'AddToCart', {
                content_name: fb_content_name,
                content_category: fb_content_category,
                content_ids: fb_content_ids,
                content_type: fb_content_type,
                value: fb_value,
                currency: fb_currency
            });
        } else {
            fbq('track', 'AddToCart');
        }
    });


    $('#wishlist_button_nopop, #wishlist_button').click(function () {
        if (typeof fb_wishlist !== 'undefined' && fb_wishlist == 1) {
            fbq('track', 'AddToWishlist');
        }
    });

});
