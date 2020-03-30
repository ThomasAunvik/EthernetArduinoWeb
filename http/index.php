<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Temperature</title>
    <link rel="stylesheet" href="stylesheets/thermometer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.css">
</head>
<body>
    <div id="status">
        <canvas id="tempchart" width="500em" height="100em"></canvas>
        <!-- <canvas id="humiditychart" width="500em" height="75em"></canvas>
        <canvas id="heatindexchart" width="500em" height="75em"></canvas> -->
    </div>
    <div id="wrapper">	
        <div id="termometer">
            <div id="temperature" style="height:0" data-value="0°C"></div>
            <div id="graduations"></div>
        </div>
    </div>

    <p>Time: <var id="time">nil</var></p>
    <p>Temp: <var id="temp">nil</var><i>°C</i></p>
    <p>Humidity: <var id="humidity">nil</var></p>
    <p>Heat Index: <var id="heat_index">nil</var></p>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.js"></script>
    <script src="temperature.js"></script>
</body>
</html>