/*
 *  Document   : readyProfile.js
 *  Author     : pixelcave
 *  Description: Custom javascript code used in User Profile page
 */

var ReadyProfile = function() {

    return {
        init: function() {
            /* Example effect of an update showing up in Newsfeed */
            var exampleUpdate = $('#newsfeed-update-example');

            setTimeout(function(){
                exampleUpdate.removeClass('display-none').find('> a').addClass('animation-fadeIn');
                exampleUpdate.find('> div').addClass('animation-pullDown');
            }, 1500);

            /*
             * With Gmaps.js, Check out examples and documentation at http://hpneo.github.io/gmaps/examples.html
             */

            // Set default height to Google Maps Container
            $('.gmap').css('height', '200px');

            // Initialize map with marker
            new GMaps({
                div: '#gmap-checkin',
                lat: -33.863,
                lng: 151.217,
                zoom: 15,
                disableDefaultUI: true,
                scrollwheel: false
            }).addMarkers([
                {lat: -33.865, lng: 151.215, title: 'Marker #2', animation: google.maps.Animation.DROP, infoWindow: {content: '<strong>Cafe-Bar: Example Address</strong>'}}
            ]);
        }
    };
}();
