<?php
    $servername = "localhost";
    $username = "c3gruppe1";
    $password = "#########";
    $dbname = "c3gruppe1";
    
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }


    $count = $_GET["count"];
    if($count == NULL) $count = 1;
    if(intval($count) == 0){
        die("Invalid Count Value: Integer");
    }
    if($count <= 0) $count = 1;

    // SEND DATA

    $sql = @"SELECT * FROM status
    ORDER BY time DESC
    LIMIT $count;";

    $result = $conn->query($sql);

    $row_count = 0;
    if ($result->num_rows > 0) {
        $last_id = $conn->insert_id;
        echo "[";
        while($row = $result->fetch_assoc()) {
            $row_count++;
            echo "{ 
                \"time\": \"" . $row["time"]. "\", 
                \"temperature\": " . $row["temperature"].  ", 
                \"humidity\": " . $row["humidity"].  ", 
                \"heat_index\": " . $row["heat_index"].
            "}";
            if($row_count != $result->num_rows) echo ",";
        }
        echo "]";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
?>