var HttpClient = function() {
    this.get = function(aUrl, aCallback) {
        var anHttpRequest = new XMLHttpRequest();
        anHttpRequest.onreadystatechange = function() { 
            if (anHttpRequest.readyState == 4 && anHttpRequest.status == 200)
                aCallback(anHttpRequest.responseText);
        }

        anHttpRequest.open( "GET", aUrl, true );            
        anHttpRequest.send( null );
    }
}

var client = new HttpClient();

var tempchart = null;
var humiditychart = null;
var heatindexchart = null;

updateCharts(client);

setInterval(updateCharts, 10000, client, false);

function updateCharts(client, animationOn){
    client.get('/status?count=200', function(response) {
        jsonResponse = JSON.parse(response);

        var today = new Date(); 
        var time = today.getHours() + ":" + today.getMinutes() + ":" + today.getSeconds();
        console.log(time);
        console.log(jsonResponse);

        // Takes the first in the jsonresponse
        document.getElementById("time").innerHTML = jsonResponse[0].time;
        document.getElementById("temp").innerHTML = jsonResponse[0].temperature;
        document.getElementById("humidity").innerHTML = jsonResponse[0].humidity;
        document.getElementById("heat_index").innerHTML = jsonResponse[0].heat_index;

        labels = [];
        temps = [];
        humidity = [];
        heat_index = []; 
        chart_data = jsonResponse.reverse();

        for(var i in chart_data){
            datetime = new Date(chart_data[i].time);
            labels[i] = datetime.toLocaleTimeString();
            temps[i] = chart_data[i].temperature;
            humidity[i] = chart_data[i].humidity;
            heat_index[i] = chart_data[i].heat_index;
        }

        var chartOption = {
            tooltips: {
                mode: 'nearest'
            },
            scales: {
                xAxes: [{
                    ticks: {
                        autoSkipPadding: 10
                    }
                }]
            }
        };

        if(animationOn == false){
            chartOption = {
                tooltips: {
                    mode: 'nearest'
                },
                animation: animationOn,
                scales: {
                    xAxes: [{
                        ticks: {
                            autoSkipPadding: 10
                        }
                    }]
                }
            };
        }

        tempChartOption = chartOption;
        humidChartOption = chartOption;
        indexChartOption = chartOption;

        if(tempchart != null) tempchart.destroy();
        var temp_ctx = document.getElementById('tempchart').getContext('2d');
        tempchart = new Chart(temp_ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Temperature in °C',
                    data: temps,
                    borderColor: 'rgba(255, 0, 0, 1)'
                }]
            },
            options: chartOption
        });

        if(humiditychart != null) humiditychart.destroy();
        var humidity_ctx = document.getElementById('humiditychart').getContext('2d');
        humiditychart = new Chart(humidity_ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Humidity',
                    data: humidity,
                    borderColor: 'rgba(0, 0, 255, 1)'
                }]
            },
            options: chartOption
        });

        if(heatindexchart != null) heatindexchart.destroy();
        var heatindex_ctx = document.getElementById('heatindexchart').getContext('2d');
        heatindexchart = new Chart(heatindex_ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Heat Index',
                    data: heat_index,
                    borderColor: 'rgba(0, 255, 0, 1)'
                }]
            },
            options: chartOption
        });
    });
}