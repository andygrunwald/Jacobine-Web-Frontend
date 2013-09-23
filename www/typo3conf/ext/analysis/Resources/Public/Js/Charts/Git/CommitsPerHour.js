$(function() {

    var urlParams = '',
        url = '/?eID=chartData&className=Git\\CommitsPerHour';

    // This part is not very beautiful
    // If we need this more often, we will outsource this to a separate function
    if (typeof project != 'undefined') {
        urlParams = '&project=' + parseInt(project);
    }

    url = url + urlParams;
    $.getJSON(url, function(data) {

        var chart,
            categoryValues = new Array(),
            values = new Array();
        $.each(data, function(key, value) {
            categoryValues.push((key - 1));
            values.push(value);
        });

        // The chart
        chart = $('#chart_commitsperhour').highcharts({

            title: {
                text : 'Git commits over time (per hour)'
            },

            yAxis: {
                title: {
                    text: 'Commits',
                    enabled: true
                }
            },

            xAxis: {
                categories: categoryValues
            },

            credits: {
                enabled: false
            },

            legend: {
                enabled: false
            },

            series: [{
                name : 'Commits',
                data : values
            }]
        });
    });
});