<?php
include 'DBConnector.php';

$pickup = $_POST['pickup'];
$dropoff = $_POST['dropoff'];
$passengerType = $_POST['passengerType'];
$acType = $_POST['acType'];

// Get Landmark IDs and distances
$landmarkQuery = $conn->prepare("
    SELECT l.LandmarkID, d.DistanceFromOrigin
    FROM landmark l
    JOIN landmarkdistance d ON l.LandmarkID = d.LandmarkID
    WHERE l.LandmarkName = ? OR l.LandmarkName = ?
");
$landmarkQuery->bind_param("ss", $pickup, $dropoff);
$landmarkQuery->execute();
$res = $landmarkQuery->get_result();

$pickupID = $dropoffID = null;
$pickupDist = $dropoffDist = null;
while ($row = $res->fetch_assoc()) {
    if ($row['LandmarkID'] && is_null($pickupID)) {
        $pickupID = $row['LandmarkID'];
        $pickupDist = $row['DistanceFromOrigin'];
    } else {
        $dropoffID = $row['LandmarkID'];
        $dropoffDist = $row['DistanceFromOrigin'];
    }
}

$distance = abs($dropoffDist - $pickupDist);

// Get fare rates
$fareQuery = $conn->prepare("SELECT FarePerKM, MinimumFare FROM fare WHERE PassengerType = ?");
$fareQuery->bind_param("s", $passengerType);
$fareQuery->execute();
$fareResult = $fareQuery->get_result()->fetch_assoc();

$farePerKM = $fareResult['FarePerKM'];
$minimumFare = $fareResult['MinimumFare'];

$computedFare = max($minimumFare, $distance * $farePerKM);

// Est. time: assume avg. 25km/hr (changeable)
$avgSpeed = 25.0;
$estimatedTime = round(($distance / $avgSpeed) * 60); // in minutes

?>
<!DOCTYPE html>
<html>
<head><title>Estimate Result</title></head>
<body>
    <h2>Trip Estimate</h2>
    <p><strong>Pickup:</strong> <?= htmlspecialchars($pickup) ?></p>
    <p><strong>Drop-off:</strong> <?= htmlspecialchars($dropoff) ?></p>
    <p><strong>Passenger Type:</strong> <?= htmlspecialchars($passengerType) ?></p>
    <p><strong>AC Type:</strong> <?= htmlspecialchars($acType) ?></p>
    <p><strong>Distance:</strong> <?= number_format($distance, 2) ?> km</p>
    <p><strong>Estimated Fare:</strong> ₱<?= number_format($computedFare, 2) ?></p>
    <p><strong>Estimated Time:</strong> <?= $estimatedTime ?> minutes</p>

    <form action="save_trip.php" method="POST">
        <input type="hidden" name="pickup" value="<?= htmlspecialchars($pickup) ?>">
        <input type="hidden" name="dropoff" value="<?= htmlspecialchars($dropoff) ?>">
        <input type="hidden" name="passengerType" value="<?= htmlspecialchars($passengerType) ?>">
        <input type="hidden" name="acType" value="<?= htmlspecialchars($acType) ?>">
        <input type="hidden" name="fare" value="<?= $computedFare ?>">
        <input type="hidden" name="estTime" value="<?= $estimatedTime ?>">
        <button type="submit">Save Trip</button>
    </form>
</body>
</html>
<?php $conn->close(); ?>
