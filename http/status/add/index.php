<?php
    try{
        
        $temperature = $_GET["temperature"];
        $humidity = $_GET["humidity"];
        $heat_index = $_GET["heat_index"];

        if($temperature == NULL){
            die("Missing Temperature Value: Float");
        }

        if(floatval($temperature) == 0){
            die("Invalid Temperature Value: Float");
        }

        if($humidity == NULL){
            die("Missing Humidity Value: Float");
        }

        if(floatval($humidity) == 0){
            die("Invalid Humidity Value: Float");
        }

        if($heat_index == NULL){
            die("Missing Heat Index Value: Float");
        }

        if(floatval($heat_index) == 0){
            die("Invalid Heat Index Value: Float");
        }
        
        // Offset for GMT+1
        $offset = 1*60*60;

        $epochtime = time() + $offset;
        $datetime = new DateTime("@$epochtime");
        $formattedtime = $datetime->format('Y-m-d H:i:s');


        // DATABASE

        $servername = "localhost";
        $username = "c3gruppe1";
        $password = "sX#d8bS7";
        $dbname = "c3gruppe1";
        
        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // SEND DATA

        $sql = @"INSERT INTO status 
                    (time, temperature, humidity, heat_index) VALUES 
                    ('$formattedtime', $temperature, $humidity, $heat_index)";

        if ($conn->query($sql) === TRUE) {
            $last_id = $conn->insert_id;
            //echo "New record created successfully. Last inserted ID is: " . $last_id;
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }

        $conn->close();
        
        echo "{ 
            \"time\": \"" . $formattedtime. "\", 
            \"temperature\": " . $temperature.  ", 
            \"humidity\": " . $humidity.  ", 
            \"heat_index\": " . $heat_index.
        "}";

    } catch (Exception $e){
        echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
?>