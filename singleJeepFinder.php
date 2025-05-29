<?php
include 'DBConnector.php';

$origin = $_POST['origin'];
$destination = $_POST['destination'];
$PassengerType = $_POST['passengerType'];
$ACType = $_POST['ACType'];

$sql = "SELECT * FROM Landmarks WHERE LandmarkName = '$origin'";
$originRow = $conn->query($sql)->fetch_assoc();
$originID = $originRow['LandmarkID'];
$originDistance = $originRow['Distance'];

$sql = "SELECT * FROM Landmarks WHERE LandmarkName = '$destination'";
$destinationRow = $conn->query($sql)->fetch_assoc();
$destinationID = $destinationRow['LandmarkID'];
$destDistance = $destinationRow['Distance'];

$sql = "SELECT J.JeepID, J.JeepName
FROM Jeep as J
INNER JOIN jeeplandmarks as JL ON J.JeepID = JL.JeepID
INNER JOIN Landmarks as L ON L.LandMarkID = JL.LandMarkID
WHERE L.LandmarkName = '$origin' AND J.ACType = '$ACType'

INTERSECT

SELECT J.JeepID, J.JeepName
FROM Jeep as J
INNER JOIN jeeplandmarks as JL ON J.JeepID = JL.JeepID
INNER JOIN Landmarks as L ON L.LandMarkID = JL.LandMarkID
WHERE L.LandmarkName = '$destination' AND J.ACType = '$ACType';";

$result = $conn->query($sql);

// Collect all matching jeeps
$matchingJeeps = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $matchingJeeps[] = $row;
    }

    // Truncate Trips before inserting new trips
    $conn->query("TRUNCATE TABLE Trips;");

    foreach ($matchingJeeps as $jeep) {
        $jeepID = $jeep["JeepID"];

        $sqlFare = "SELECT F.FarePerKM, F.MinimumFare
                    FROM jeepFare JF
                    INNER JOIN Fare F ON JF.FareID = F.FareID
                    WHERE JF.JeepID = $jeepID AND F.PassengerType = '$PassengerType';";
        $resultFare = $conn->query($sqlFare);
        $rowFare = $resultFare->fetch_assoc();

        if (abs($destDistance - $originDistance) <= 4) {
            $Fare = $rowFare["MinimumFare"];
        } else {
            $Fare = ((abs($destDistance - $originDistance) - 4) * $rowFare["FarePerKM"]) + $rowFare["MinimumFare"];
        }

        $sqlInsert = "INSERT INTO Trips (`TripID`, `JeepID1`, `JeepID2`, `OriginID`, `DropOffID`, `DestinationID`, `ACType`, `PassengerType`, `Fare`) VALUES 
                      (NULL, '$jeepID', NULL, '$originID', NULL, '$destinationID', '$ACType', '$PassengerType', '$Fare');";
        $conn->query($sqlInsert);
    }

    header("Location: http://localhost/127fp/routeFinder.php");
    exit();
} else {
    $sql = "SELECT L.LandmarkName
            FROM Jeep AS J
            INNER JOIN jeepLandmarks AS JL ON J.JeepID = JL.JeepID
            INNER JOIN Landmarks AS L ON JL.LandmarkID = L.LandmarkID
            WHERE J.JeepID IN (
                SELECT JL2.JeepID
                FROM jeepLandmarks JL2
                JOIN Landmarks AS L2 ON JL2.LandmarkID = L2.LandmarkID
                WHERE L2.LandmarkName = '$origin' AND J.ACType = '$ACType'
            )

            INTERSECT

            SELECT L.LandmarkName
            FROM Jeep AS J
            INNER JOIN jeepLandmarks AS JL ON J.JeepID = JL.JeepID
            INNER JOIN Landmarks AS L ON JL.LandmarkID = L.LandmarkID
            WHERE J.JeepID IN (
                SELECT JL2.JeepID
                FROM jeepLandmarks JL2
                JOIN Landmarks AS L2 ON JL2.LandmarkID = L2.LandmarkID
                WHERE L2.LandmarkName = '$destination' AND J.ACType = '$ACType'
            );";

    $result = $conn->query($sql);

    echo "No JEEP AVAILABLE!, where do you want to drop off?";
    echo "<form method='post' action='doubleJeepFinder.php'>
            <input type='hidden' name='origin' value='" . $originID . "'>
            <input type='hidden' name='destination' value='" . $destinationID . "'>
            <input type='hidden' name='ACType' value='" . $ACType . "'>
            <input type='hidden' name='PassengerType' value='" . $PassengerType . "'>
            <input type='hidden' name='originDistance' value='" . $originDistance . "'>
            <input type='hidden' name='destDistance' value='" . $destDistance . "'>
            <select class='expand' name='dropOff'>
                <option disabled selected>--Select DropOff Point--</option>";
    while ($row = $result->fetch_assoc()) {
        echo "<option value='" . $row["LandmarkName"] . "'>" . $row["LandmarkName"] . "</option>";
    }
    echo    "</select>
            <td>
                <input type='submit' value='Submit'>
            </td>
        </form>";
}
?>
