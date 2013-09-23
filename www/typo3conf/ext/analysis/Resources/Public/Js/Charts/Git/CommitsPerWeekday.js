$(function() {

    var urlParams = '',
        url = '/?eID=chartData&className=Git\\CommitsPerWeekday';

    // This part is not very beautiful
    // If we need this more often, we will outsource this to a separate function
    if (typeof project != 'undefined') {
        urlParams = '&project=' + parseInt(project);
    }

    url = url + urlParams;
    $.getJSON(url, function(data) {

        var chart;

        // The chart
        chart = $('#chart_commitsperweekday').highcharts({

            title: {
                text : 'Git commits over time (per day of week)'
            },

            yAxis: {
                title: {
                    text: 'Commits',
                    enabled: true
                }
            },

            xAxis: {
                categories: ['Monday', 'Tuesday', 'Wednesday.', 'Thursday', 'Friday', 'Saturday', 'Sunday']
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