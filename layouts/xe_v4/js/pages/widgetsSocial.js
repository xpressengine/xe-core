/*
 *  Document   : widgetsSocial.js
 *  Author     : pixelcave
 *  Description: Custom javascript code used in Social Widgets page
 */

var WidgetsSocial = function() {

    return {
        init: function() {
            /*
             * With Gmaps.js, Check out examples and documentation at http://hpneo.github.io/gmaps/examples.html
             */

            // Initialize advanced widget map
            new GMaps({
                div: '#gmap-widget',
                lat: -33.8665,
                lng: 151.20,
                zoom: 15,
                disableDefaultUI: true,
                scrollwheel: false
            });

            // Initialize advanced widget map alternative
            new GMaps({
                div: '#gmap-widget-alt',
                lat: -33.8665,
                lng: 151.20,
                zoom: 15,
                disableDefaultUI: true,
                scrollwheel: false
            }).setMapTypeId(google.maps.MapTypeId.SATELLITE);;
        }
    };
}();