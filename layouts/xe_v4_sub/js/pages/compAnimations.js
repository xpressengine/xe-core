/*
 *  Document   : compAnimations.js
 *  Author     : pixelcave
 *  Description: Custom javascript code used in Animations page
 */

var CompAnimations = function() {

    return {
        init: function() {
            var animPageButtons = $('.animation-page-buttons .btn');
            var animButtons     = $('.animation-buttons .btn');
            var animClass       = '';

            /* Add/Remove Animation for page */
            animPageButtons.click(function() {
                animPageButtons.removeClass('active');
                $(this).addClass('active');
                animClass = $(this).data('animation');

                $('body').removeClass().addClass(animClass);
                $('#animation-page-class').text(animClass);
            });

            /* Add/Remove Animation for element */
            animButtons.click(function() {
                animButtons.removeClass('active');
                $(this).addClass('active');
                animClass = $(this).data('animation');

                $('#animation-element').removeClass().addClass(animClass);
                $('#animation-class').text(animClass);
            });
        }
    };
}();