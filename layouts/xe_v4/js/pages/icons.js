/*
 *  Document   : icons.js
 *  Author     : pixelcave
 *  Description: Custom javascript code used in Icons pages (Font Awesome and Glyphicons Pro)
 */

var Icons = function() {

    return {
        init: function() {
            var titleAttr;

            // When an icon button is clicked
            $('#page-content .btn').click(function(){
                // Get the icon class from the button attribute (data-original-title is created by tooltip)
                titleAttr = $(this).attr('data-original-title');

                // Set the content of the input and select it
                $('#icon-gen-input')
                    .val( '<i class="' + titleAttr + '"></i>' )
                    .select();

                // Animate scrolling to the icon generator
                $('html,body')
                    .animate({ scrollTop: $("#icon-gen").offset().top - 15 });

                return false;
            });
        }
    };
}();