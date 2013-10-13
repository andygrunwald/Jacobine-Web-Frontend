$(function() {

    var urlParams = '',
        url = '/?eID=chartData&className=Git\\CommitsPerMonth';

    // This part is not very beautiful
    // If we need this more often, we will outsource this to a separate function
    if (typeof project != 'undefined') {
        urlParams = '&project=' + parseInt(project);
    }

    url = url + urlParams;
    $.getJSON(url, function(data) {

        var chart;

        // The chart
        chart = $('#chart_commitspermonth').highcharts({

            title: {
                text : 'Git commits over time (per month of year)'
            },

            yAxis: {
                title: {
                    text: 'Commits',
                    enabled: true
                }
            },

            xAxis: {
                categories: ['January', 'February', 'March.', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
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
            }]
        });
    });
});