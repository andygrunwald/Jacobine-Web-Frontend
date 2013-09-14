$(function() {
    $.getJSON('/?eID=chartData&className=PHPLoc', function(data) {

        var chart,
            visible = false,
            categoryValues = data[0],
            series = new Array();

        $.each(data[1], function(key, value) {
            visible = false;
            if (['Directories', 'Files'].indexOf(key) >= 0) {
                visible = true;
            }

            series.push({
                name : key,
                data : value,
                visible: visible
            });
        });

        // The chart
        // $('#chart_phploc').highcharts().xAxis[0].update({max: 25})
        // TODO: Make zoom feature flexible
        chart = $('#chart_phploc').highcharts({
            chart: {
                zoomType: 'x',
                type: "line"
            },

            title: {
                text : 'PHPLoc analysis per release'
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
                    text: 'Number',
                    enabled: true
                }
            },

            credits: {
                enabled: false
            },

            // TODO optimize legend with maaaany items
            legend: {
                enabled: true
            },

            series: series
        });
    });
});