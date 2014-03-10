/*
 *  Document   : readyInbox.js
 *  Author     : pixelcave
 *  Description: Custom javascript code used in Inbox page
 */

var ReadyInbox = function() {

    return {
        init: function() {
            // Choose one of the highlight classes for the message list rows: 'active', 'success', 'warning', 'danger'
            var rowHighlightClass = 'active';

            /* Select/Deselect all checkboxes in tables */
            $('thead input:checkbox').click(function() {
                var checkedStatus   = $(this).prop('checked');
                var table           = $(this).closest('table');

                if (checkedStatus) {
                    $('tbody tr', table).addClass(rowHighlightClass);
                } else {
                    $('tbody tr', table).removeClass(rowHighlightClass);
                }

                $('tbody input:checkbox', table).each(function() {
                    $(this).prop('checked', checkedStatus);
                });
            });

            /* Add/Remove row highlighting on checkbox click */
            $('tbody input:checkbox').click(function() {
                var checkedStatus   = $(this).prop('checked');
                var tableRow        = $(this).closest('tr');

                if (checkedStatus) {
                    tableRow.addClass(rowHighlightClass);
                } else {
                    tableRow.removeClass(rowHighlightClass);
                }
            });

            /* Toggle on/off star buttons */
            $('.msg-fav-btn').click(function(){
                $(this).toggleClass('text-muted text-warning');
                $('i', this).toggleClass('fa-star-o fa-star');

                // You could give the star buttons unique ids related with each message
                // and use it - $(this).prop('id') - to update with your back end :-)
            });

            /* Toggle on/off read buttons */
            $('.msg-read-btn').click(function(){
                $(this).toggleClass('text-muted text-success');

                // You could give the read buttons unique ids related with each message
                // and use it - $(this).prop('id') - to update with your back end :-)
            });
        }
    };
}();