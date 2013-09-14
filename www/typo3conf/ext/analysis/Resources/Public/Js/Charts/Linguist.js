$(function() {
    $.getJSON('/?eID=chartData&className=Linguist', function(data) {

        var chart,
            visible = false,
            categoryValues = data[0],
            series = new Array();

        $.each(data[1], function(key, value) {
            visible = false;
            if (['PHP', 'JavaScript'].indexOf(key) >= 0) {
                visible = true;
            }

            series.push({
                name : key,
                data : value,
                visible: visible,
                tooltip : {
                    valueSuffix: '%'
                }
            });
        });

        // The chart
        // $('#chart_languages').highcharts().xAxis[0].update({max: 25})
        // TODO: Make zoom feature flexible
        chart = $('#chart_languages').highcharts({
            chart: {
                zoomType: 'x',
                type: "line"
            },

            title: {
                text : 'Programming languages per release'
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
                    text: 'Percent',
                    enabled: true
                }
            },

            credits: {
                enabled: false
            },

            legend: {
                enabled: true
            },

            series: series
        });
    });
});