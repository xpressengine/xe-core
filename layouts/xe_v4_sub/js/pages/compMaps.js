/*
 *  Document   : compMaps.js
 *  Author     : pixelcave
 *  Description: Custom javascript code used in Maps page
 */

var CompMaps = function() {

    return {
        init: function() {
            /*
             * With Gmaps.js, Check out examples and documentation at http://hpneo.github.io/gmaps/examples.html
             */

            // Set default height to all Google Maps Containers
            $('.gmap').css('height', '350px');

            // Initialize top map
            new GMaps({
                div: '#gmap-top',
                lat: -33.865,
                lng: 151.20,
                zoom: 15,
                disableDefaultUI: true,
                scrollwheel: false
            });

            // Initialize terrain map
            new GMaps({
                div: '#gmap-terrain',
                lat: 0,
                lng: 0,
                zoom: 1,
                scrollwheel: false
            }).setMapTypeId(google.maps.MapTypeId.TERRAIN);

            // Initialize satellite map
            new GMaps({
                div: '#gmap-satellite',
                lat: 0,
                lng: 0,zoom: 1,
                scrollwheel: false
            }).setMapTypeId(google.maps.MapTypeId.SATELLITE);

            // Initialize map with markers
            new GMaps({
                div: '#gmap-markers',
                lat: 0,
                lng: 0,
                zoom: 3,
                scrollwheel: false
            }).addMarkers([
                {lat: 20, lng: -31, title: 'Marker #1', animation: google.maps.Animation.DROP, infoWindow: {content: '<strong>Marker #1: HTML Content</strong>'}},
                {lat: -10, lng: 0, title: 'Marker #2', animation: google.maps.Animation.DROP, infoWindow: {content: '<strong>Marker #2: HTML Content</strong>'}},
                {lat: -20, lng: 85, title: 'Marker #3', animation: google.maps.Animation.DROP, infoWindow: {content: '<strong>Marker #3: HTML Content</strong>'}},
                {lat: -20, lng: -110, title: 'Marker #4', animation: google.maps.Animation.DROP, infoWindow: {content: '<strong>Marker #4: HTML Content</strong>'}}
            ]);

            // Initialize street view panorama
            new GMaps.createPanorama({
                el: '#gmap-street',
                lat: 50.059139,
                lng: -122.958407,
                pov: {heading: 300, pitch: 5},
                scrollwheel: false
            });

            // Initialize map geolocation
            var gmapGeolocation = new GMaps({
                div: '#gmap-geolocation',
                lat: 0,
                lng: 0,
                scrollwheel: false
            });

            GMaps.geolocate({
                success: function(position) {
                    gmapGeolocation.setCenter(position.coords.latitude, position.coords.longitude);
                    gmapGeolocation.addMarker({
                        lat: position.coords.latitude,
                        lng: position.coords.longitude,
                        animation: google.maps.Animation.DROP,
                        title: 'GeoLocation',
                        infoWindow: {
                            content: '<div class="text-success"><i class="fa fa-map-marker"></i> <strong>Your location!</strong></div>'
                        }
                    });
                },
                error: function(error) {
                    alert('Geolocation failed: ' + error.message);
                },
                not_supported: function() {
                    alert("Your browser does not support geolocation");
                },
                always: function() {
                    // Message when geolocation succeed
                }
            });
        }
    };
}();