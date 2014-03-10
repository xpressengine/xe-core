/*
 *  Document   : readyTimeline.js
 *  Author     : pixelcave
 *  Description: Custom javascript code used in Timeline page
 */

var ReadyTimeline = function() {

    return {
        init: function() {
            /*
             * With Gmaps.js, Check out examples and documentation at http://hpneo.github.io/gmaps/examples.html
             */

            // Set default height to Google Maps Containers
            $('.gmap').css('height', '200px');

            // Initialize Timeline map
            new GMaps({
                div: '#gmap-timeline',
                lat: -33.863,
                lng: 151.200,
                zoom: 15,
                disableDefaultUI: true,
                scrollwheel: false
            }).addMarkers([
                {
                    lat: -33.863,
                    lng: 151.202,
                    animation: google.maps.Animation.DROP,
                    infoWindow: {content: '<strong>Cafe-Bar: Example Address</strong>'}
                }
            ]);

            // Initialize Feed map
            new GMaps({
                div: '#gmap-checkin',
                lat: -33.863,
                lng: 151.217,
                zoom: 15,
                disableDefaultUI: true,
                scrollwheel: false
            }).addMarkers([
                {
                    lat: -33.865,
                    lng: 151.215,
                    animation: google.maps.Animation.DROP,
                    infoWindow: {content: '<strong>Cafe-Bar: Example Address</strong>'}
                }
            ]);
        }
    };
}();