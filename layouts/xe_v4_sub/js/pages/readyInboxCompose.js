/*
 *  Document   : readyInboxCompose.js
 *  Author     : pixelcave
 *  Description: Custom javascript code used in Inbox Compose page
 */

var ReadyInboxCompose = function() {

    return {
        init: function() {
            /* Show Ccc and Bcc fields on the form when the top right buttons are clicked */
            $('#cc-input-btn').click(function(){
                $('#cc-input').removeClass('display-none').addClass('animation-pullDown');
               $(this).fadeOut();
            });

            $('#bcc-input-btn').click(function(){
                $('#bcc-input').removeClass('display-none').addClass('animation-pullDown');
               $(this).fadeOut();
            });
        }
    };
}();