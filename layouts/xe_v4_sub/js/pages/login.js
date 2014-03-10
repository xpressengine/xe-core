/*
 *  Document   : login.js
 *  Author     : pixelcave
 *  Description: Custom javascript code used in Login page
 */

var Login = function() {

    return {
        init: function() {
            /* Login & Register show-hide */
            var formLogin       = $('#form-login'),
                formRegister    = $('#form-register');

            $('#link-login').click(function(){ formLogin.slideUp(250); formRegister.slideDown(250, function(){$('input').placeholder();}); });
            $('#link-register').click(function(){ formRegister.slideUp(250); formLogin.slideDown(250, function(){$('input').placeholder();}); });

            // If the link includes the hashtag register, show the register form instead of login
            if (window.location.hash === '#register') {
                formLogin.hide();
                formRegister.show();
            }
        }
    };
}();