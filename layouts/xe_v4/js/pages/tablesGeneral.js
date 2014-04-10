/*
 *  Document   : tablesGeneral.js
 *  Author     : pixelcave
 *  Description: Custom javascript code used in Tables General page
 */

var TablesGeneral = function() {

    return {
        init: function() {
            /* Select/Deselect all checkboxes in tables */
            $('thead input:checkbox').click(function() {
                var checkedStatus   = $(this).prop('checked');
                var table           = $(this).closest('table');

                $('tbody input:checkbox', table).each(function() {
                    $(this).prop('checked', checkedStatus);
                });
            });

            /* Table Styles Switcher */
            var genTable = $('#general-table');

            $('#style-default').click(function(){ genTable.removeClass('table-bordered').removeClass('table-borderless'); });
            $('#style-bordered').click(function(){ genTable.removeClass('table-borderless').addClass('table-bordered'); });
            $('#style-borderless').click(function(){ genTable.removeClass('table-bordered').addClass('table-borderless'); });

            $('#style-striped').on('click', function() {
                $(this).toggleClass('active');

                if ($(this).hasClass('active')) {
                    genTable.addClass('table-striped');
                } else {
                    genTable.removeClass('table-striped');
                }
            });

            $('#style-condensed').on('click', function() {
                $(this).toggleClass('active');

                if ($(this).hasClass('active')) {
                    genTable.addClass('table-condensed');
                } else {
                    genTable.removeClass('table-condensed');
                }
            });

            $('#style-hover').on('click', function() {
                $(this).toggleClass('active');

                if ($(this).hasClass('active')) {
                    genTable.addClass('table-hover');
                } else {
                    genTable.removeClass('table-hover');
                }
            });
        }
    };
}();