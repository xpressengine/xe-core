/*
 *  Document   : uiProgress.js
 *  Author     : pixelcave
 *  Description: Custom javascript code used in Progress & Loading page
 */

var UiProgress = function() {

    // Get random number function from a given range
    var getRandomInt = function(min, max) {
        return Math.floor(Math.random() * (max - min + 1)) + min;
    };

    return {
        init: function() {
            /* Randomize progress bars width */
            var random = 0;

            $('.toggle-bars').click(function() {
                $('.progress-bar', '.bars-container').each(function() {
                    random = getRandomInt(10, 100) + '%';
                    $(this).css('width', random).html(random);
                });

                $('.progress-bar', '.bars-stacked-container').each(function() {
                    random = getRandomInt(10, 25) + '%';
                    $(this).css('width', random).html(random);
                });
            });

            /* With NProgress, Check out more examples at https://github.com/rstacruz/nprogress */
            var startBtn    = $('#top-loading-start');
            var stopBtn     = $('#top-loading-stop');

            // User Bootstrap functionality to disable both buttons just to demostrate one time on page load
            startBtn.add(stopBtn).button('loading');

            // Start top loading bar and finish after 2,5 seconds just for demostration
            // You could bind NProgress.start() and NProgress.done() to Jquery events ajaxStart and ajaxStop to add progress to your ajax calls :-)
            NProgress.start();
            setTimeout(function(){
                NProgress.done();
                startBtn.button('reset');
            }, 2500);

            // On start button click start loading again
            startBtn.on('click', function(){
                NProgress.start();
                $(this).button('loading');
                stopBtn.button('reset');
            });

            // On stop button click stop loading
            stopBtn.on('click', function(){
                NProgress.done();
                $(this).button('loading');
                startBtn.button('reset');
            });
        }
    };
}();