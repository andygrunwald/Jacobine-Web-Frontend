$(function() {
    $.getJSON('/?eID=chartData&className=Filesize', function(data) {

        var chart,
            categoryValues = new Array(),
            values = new Array();
        $.each(data, function(key, value) {
            value = value / 1000 / 1000; // From Byte to Megabyte
            categoryValues.push(key);
            values.push(value);
        });

        // The chart
        // $('#chart_filesize').highcharts().xAxis[0].update({max: 25})
        // TODO: Make zoom feature flexible
        chart = $('#chart_filesize').highcharts({
            chart: {
                zoomType: 'x',
                type: "line"
            },

            title: {
                text : 'tar.gz archives in (mega) byte'
            },

            xAxis: {
                title: {
                    text: 'Releases',
                    enabled: true
                },
                labels: {
                    rotation: 45,
                    y: 20
                },
                maxZoom: 1,
                min: 50,
                max: 100,
                categories: categoryValues
            },

            scrollbar: {
                enabled: true
            },

            yAxis: {
                title: {
                    text: 'Size in megabyte',
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
                name : 'Filesize',
                data : values,
                tooltip : {
                    valueDecimals : 2,
                    valueSuffix: ' MB'
                }
            }]
        });
    });
});