$(function() {
    $.getJSON('/?eID=chartData&className=Git\\CommitsOverTime', function(data) {

        var chart,
            categoryValues = new Array(),
            values = new Array();
        /*
        $.each(data, function(key, value) {
            value = value / 1000 / 1000; // From Byte to Megabyte
            categoryValues.push(key);
            values.push(value);
        });
        */

        // The chart
        chart = $('#chart_commitsovertime').highcharts('StockChart', {

            title: {
                text : 'Git commits over time'
            },

            yAxis: {
                title: {
                    text: 'Commits',
                    enabled: true
                }
            },

            credits: {
                enabled: false
            },

            legend: {
                enabled: false
            },

            series: [{
                name : 'Commits',
                data : data
                /*
                dataGrouping: {
                    groupPixelWidth: 40,
                    approximation: "sum",
                    enabled: true,
                    units: [['week',[1]]]
                },
                */
            }]
        });
    });
});