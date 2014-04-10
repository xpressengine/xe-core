/*
 *  Document   : index.js
 *  Author     : pixelcave
 *  Description: Custom javascript code used in Dashboard page
 */

var Index = function() {

    return {
        init: function() {
            /* Mini Bar Charts with jquery.sparkline plugin, for more examples you can check out http://omnipotent.net/jquery.sparkline/#s-about */
            var miniChartBarOptions = {
                type: 'bar',
                barWidth: 8,
                barSpacing: 6,
                height: '64px',
                tooltipOffsetX: -25,
                tooltipOffsetY: 20,
                barColor: '#999999',
                tooltipPrefix: '+ ',
                tooltipSuffix: ' Sales',
                tooltipFormat: '{{prefix}}{{value}}{{suffix}}'
            };
            $('#mini-chart-sales').sparkline([8,3,1,5,4,8,9,6,5,7,10,5,8,9], miniChartBarOptions);

            miniChartBarOptions['tooltipSuffix'] = '%';
            $('#mini-chart-brand').sparkline([50,65,70,90,95,110,140,160,190,200,220,230,260], miniChartBarOptions);

            /*
             * With Gmaps.js, Check out examples and documentation at http://hpneo.github.io/gmaps/examples.html
             */

            // Set default height to Google Maps container
            $('.gmap').css('height', '220px');

            // Initialize Timeline map
            new GMaps({
                div: '#gmap-timeline',
                lat: -33.863,
                lng: 151.202,
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

            /*
             * Flot 0.8.2 Jquery plugin is used for charts
             *
             * For more examples or getting extra plugins you can check http://www.flotcharts.org/
             * Plugins included in this template: pie, resize, stack, time
             */

            // Get the elements where we will attach the charts
            var dashWidgetChart = $('#dash-widget-chart');

            // Random data for the chart
            var dataEarnings = [[1, 1560], [2, 1650], [3, 1320], [4, 1950], [5, 1800], [6, 2400], [7, 2100], [8, 2550], [9, 3300], [10, 3900], [11, 4200], [12, 4500]];
            var dataSales = [[1, 500], [2, 420], [3, 480], [4, 350], [5, 600], [6, 850], [7, 1100], [8, 950], [9, 1220], [10, 1300], [11, 1500], [12, 1700]];

            // Array with month labels used in chart
            var chartMonths = [[1, 'January'], [2, 'February'], [3, 'March'], [4, 'April'], [5, 'May'], [6, 'June'], [7, 'July'], [8, 'August'], [9, 'September'], [10, 'October'], [11, 'November'], [12, 'December']];

            // Initialize Dash Widget Chart
            $.plot(dashWidgetChart,
                [
                    {
                        data: dataEarnings,
                        lines: {show: true, fill: false},
                        points: {show: true, radius: 6, fillColor: '#cccccc'}
                    },
                    {
                        data: dataSales,
                        lines: {show: true, fill: false},
                        points: {show: true, radius: 6, fillColor: '#ffffff'}
                    }
                ],
                {
                    colors: ['#ffffff', '#353535'],
                    legend: {show: false},
                    grid: {borderWidth: 0, hoverable: true, clickable: true},
                    yaxis: {show: false},
                    xaxis: {show: false, ticks: chartMonths}
                }
            );

            // Creating and attaching a tooltip to the widget
            var previousPoint = null, ttlabel = null;
            dashWidgetChart.bind('plothover', function(event, pos, item) {

                if (item) {
                    if (previousPoint !== item.dataIndex) {
                        previousPoint = item.dataIndex;

                        $('#chart-tooltip').remove();
                        var x = item.datapoint[0], y = item.datapoint[1];

                        // Get xaxis label
                        var monthLabel = item.series.xaxis.options.ticks[item.dataIndex][1];

                        if (item.seriesIndex === 1) {
                            ttlabel = '<strong>' + y + '</strong> sales in <strong>' + monthLabel + '</strong>';
                        } else {
                            ttlabel = '$ <strong>' + y + '</strong> in <strong>' + monthLabel + '</strong>';
                        }

                        $('<div id="chart-tooltip" class="chart-tooltip">' + ttlabel + '</div>')
                            .css({top: item.pageY - 50, left: item.pageX - 50}).appendTo("body").show();
                    }
                }
                else {
                    $('#chart-tooltip').remove();
                    previousPoint = null;
                }
            });
        }
    };
}();