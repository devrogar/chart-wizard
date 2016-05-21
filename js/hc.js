$(function () {
    // Create the chart
    $('#chart').highcharts({
		chart: {
			type: chartType
		},

        title: {
            text: 'Highcharts data from Google Spreadsheets'
        },

        data: {
            googleSpreadsheetKey: key
        },
		
		yAxis: {
            title: {
                text: yaxisLabel
            }
        }

    });
}); 