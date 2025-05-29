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
<table width="100%" border="1" cellpadding="5" cellspacing="0">
    <thead>
        <tr>
            <th>From</th>
            <th>Ride Jeep 1</th>
            <th>Drop off at</th>
            <th>Ride Jeep 2</th>
            <th>Destination</th>
            <th>Fare</th>
            <th>Action</th>
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
                    t.Fare,
                    t.ACType,
                    t.PassengerType
                FROM Trips t
                LEFT JOIN Jeep j1 ON t.JeepID1 = j1.JeepID
                LEFT JOIN Jeep j2 ON t.JeepID2 = j2.JeepID
                LEFT JOIN Landmarks l1 ON t.OriginID = l1.LandmarkID
                LEFT JOIN Landmarks l2 ON t.DropOffID = l2.LandmarkID
                LEFT JOIN Landmarks l3 ON t.DestinationID = l3.LandmarkID";

        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Escape once for output & attribute values
                $data = array_map('htmlspecialchars', $row);

                echo "<tr>
                    <td>{$data['Origin']}</td>
                    <td>{$data['Jeep1']}</td>
                    <td>{$data['DropOff']}</td>
                    <td>{$data['Jeep2']}</td>
                    <td>{$data['Destination']}</td>
                    <td>{$data['Fare']}</td>
                    <td>
                        <form method='post' action='saveTrip.php'>
                            <input type='hidden' name='TripID' value='{$data['TripID']}'>
                            <input type='hidden' name='JeepName1' value='{$data['Jeep1']}'>
                            <input type='hidden' name='JeepName2' value='{$data['Jeep2']}'>
                            <input type='hidden' name='PickupPoint' value='{$data['Origin']}'>
                            <input type='hidden' name='DropoffPoint' value='{$data['DropOff']}'>
                            <input type='hidden' name='DestinationPoint' value='{$data['Destination']}'>
                            <input type='hidden' name='Fare' value='{$data['Fare']}'>
                            <input type='hidden' name='PassengerType' value='{$data['PassengerType']}'>
                            <input type='hidden' name='ACType' value='{$data['ACType']}'>
                            <button type='submit'>Save</button>
                        </form>
                    </td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='7'>No trips found.</td></tr>";
        }
        ?>
    </tbody>
</table>
</body>
</html>
