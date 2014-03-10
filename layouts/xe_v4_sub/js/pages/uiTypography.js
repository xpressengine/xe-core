/*
 *  Document   : uitypography.js
 *  Author     : pixelcave
 *  Description: Custom javascript code used in Typography page
 */

var UiTypography = function() {

    return {
        init: function() {
            var headings = $('h1, h2, h3, h4, h5, h6', '.headings-container');

            /* Toggle .page-header class */
            $('.toggle-headings-page').click(function() {
                headings
                    .removeClass('sub-header')
                    .toggleClass('page-header');
            });

            /* Toggle .sub-header class */
            $('.toggle-headings-sub').click(function() {
                headings
                    .removeClass('page-header')
                    .toggleClass('sub-header');
            });
        }
    };
}();