<?php
include 'DBConnector.php';

$originID = $_POST['origin'];
$destinationID = $_POST['destination'];
$PassengerType = $_POST['PassengerType'];
$ACType = $_POST['ACType'];
$dropOffName = $_POST['dropOff'];
$originDistance = $_POST['originDistance'];
$destDistance = $_POST['destDistance'];

// Get dropOff ID and distance
$sql = "SELECT * FROM Landmarks WHERE LandmarkName = '$dropOffName'";
$dropOffRow = $conn->query($sql)->fetch_assoc();
$dropOffID = $dropOffRow['LandmarkID'];
$dropOffDistance = $dropOffRow['Distance'];

// Find jeeps for first leg (origin to dropOff)
$sql1 = "SELECT DISTINCT J.JeepID, J.JeepName
FROM Jeep J
INNER JOIN jeepLandmarks JL ON J.JeepID = JL.JeepID
INNER JOIN Landmarks L ON JL.LandmarkID = L.LandmarkID
WHERE L.LandmarkID = $originID AND J.ACType = '$ACType'
AND J.JeepID IN (
    SELECT JL2.JeepID FROM jeepLandmarks JL2 WHERE JL2.LandmarkID = $dropOffID
)";

// Find jeeps for second leg (dropOff to destination)
$sql2 = "SELECT DISTINCT J.JeepID, J.JeepName
FROM Jeep J
INNER JOIN jeepLandmarks JL ON J.JeepID = JL.JeepID
INNER JOIN Landmarks L ON JL.LandmarkID = L.LandmarkID
WHERE L.LandmarkID = $dropOffID AND J.ACType = '$ACType'
AND J.JeepID IN (
    SELECT JL2.JeepID FROM jeepLandmarks JL2 WHERE JL2.LandmarkID = $destinationID
)";

$result1 = $conn->query($sql1);
$result2 = $conn->query($sql2);

if ($result1 && $result2 && $result1->num_rows > 0 && $result2->num_rows > 0) {
    $jeeps1 = [];
    while ($row = $result1->fetch_assoc()) {
        $jeeps1[] = $row;
    }

    $jeeps2 = [];
    while ($row = $result2->fetch_assoc()) {
        $jeeps2[] = $row;
    }

    // DELETE ALL ROWS USING TRUNCHATE (para di confusing)
    $conn->query("TRUNCATE TABLE Trips;");

    foreach ($jeeps1 as $jeep1) {
        foreach ($jeeps2 as $jeep2) {
            // Skip if both jeeps are the same (optional, depends on logic)
            if ($jeep1['JeepID'] == $jeep2['JeepID']) {
                continue;
            }

            // Calculate fare for first leg
            $sqlFare1 = "SELECT F.FarePerKM, F.MinimumFare
                         FROM jeepFare JF
                         INNER JOIN Fare F ON JF.FareID = F.FareID
                         WHERE JF.JeepID = " . $jeep1['JeepID'] . " AND F.PassengerType = '$PassengerType'";
            $fareResult1 = $conn->query($sqlFare1);
            $fareRow1 = $fareResult1->fetch_assoc();
            $dist1 = abs($dropOffDistance - $originDistance);
            if ($dist1 <= 4) {
                $fare1 = $fareRow1['MinimumFare'];
            } else {
                $fare1 = (($dist1 - 4) * $fareRow1['FarePerKM']) + $fareRow1['MinimumFare'];
            }

            // Calculate fare for second leg
            $sqlFare2 = "SELECT F.FarePerKM, F.MinimumFare
                         FROM jeepFare JF
                         INNER JOIN Fare F ON JF.FareID = F.FareID
                         WHERE JF.JeepID = " . $jeep2['JeepID'] . " AND F.PassengerType = '$PassengerType'";
            $fareResult2 = $conn->query($sqlFare2);
            $fareRow2 = $fareResult2->fetch_assoc();
            $dist2 = abs($destDistance - $dropOffDistance);
            if ($dist2 <= 4) {
                $fare2 = $fareRow2['MinimumFare'];
            } else {
                $fare2 = (($dist2 - 4) * $fareRow2['FarePerKM']) + $fareRow2['MinimumFare'];
            }

            $totalFare = $fare1 + $fare2;

            $sqlInsert = "INSERT INTO Trips (`TripID`, `JeepID1`, `JeepID2`, `OriginID`, `DropOffID`, `DestinationID`, `ACType`, `PassengerType`, `Fare`) VALUES 
                          (NULL, " . $jeep1['JeepID'] . ", " . $jeep2['JeepID'] . ", $originID, $dropOffID, $destinationID, '$ACType', '$PassengerType', $totalFare)";
            $conn->query($sqlInsert);
        }
    }

    header("Location: http://localhost/127fp/routeFinder.php");
    exit();

} else {
    echo "No available two-jeep routes found.";
}
?>
