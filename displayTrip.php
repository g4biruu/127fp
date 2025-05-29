<!DOCTYPE html>
<?php
    include 'DBConnector.php';
?>
<html lang="en">
    <head>
    <meta charset="UTF-8">
    <title>Route Finder - Sakay na Iloilo!</title>
    <link rel="stylesheet" href="style.css">
    </head>
    
    <body>
        <table width =100%>
            <thead>
            <tr>
                <th>From</th>
                <th>Ride Jeep 1</th>
                <th>Drop off at</th>
                <th>Ride Jeep 2</th>
                <th>Destination</th>
                <th>Fare</th>

                <!-- <th>Time of Travel</th> -->
                <!-- <th>Est. Total Time of Travel</th> -->
                <!-- <th>Est. Total Fare</th> -->
            </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT 
            t.TripID,
            j1.JeepName AS Jeep1,
            j2.JeepName AS Jeep2,
            l1.LandmarkName AS Origin,
            l2.LandmarkName AS DropOff,
            l3.LandmarkName AS Destination,
            t.Fare
            FROM Trips t
            LEFT JOIN Jeep j1 ON t.JeepID1 = j1.JeepID
            LEFT JOIN Jeep j2 ON t.JeepID2 = j2.JeepID
            LEFT JOIN Landmarks l1 ON t.OriginID = l1.LandmarkID
            LEFT JOIN Landmarks l2 ON t.DropOffID = l2.LandmarkID
            LEFT JOIN Landmarks l3 ON t.DestinationID = l3.LandmarkID;";

            $result = $conn->query($sql);

            while ($row = $result->fetch_assoc()) {
            echo "<tr>".
                    // "<td>" . $row['TripID'] . "</td>".
                    "<td>" . $row['Origin'] . "</td>".
                    "<td>" . $row['Jeep1'] . "</td>".
                    "<td>" . $row['DropOff'] . "</td>".
                    "<td>" . $row['Jeep2'] . "</td>".
                    "<td>" . $row['Destination'] . "</td>".
                    "<td>" . $row['Fare'] . "</td>".
                    "</tr>";
}
                ?>
                </tbody>
        </table>

    </body>

</html>