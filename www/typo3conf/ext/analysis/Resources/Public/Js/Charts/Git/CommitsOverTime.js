$(function() {

    var urlParams = '',
        url = '/?eID=chartData&className=Git\\CommitsOverTime';

    // This part is not very beautiful
    // If we need this more often, we will outsource this to a separate function
    if (typeof project != 'undefined') {
        urlParams = '&project=' + parseInt(project);
    }

    url = url + urlParams;
    $.getJSON(url, function(data) {

        var chart;

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