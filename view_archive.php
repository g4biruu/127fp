<?php
include 'DBConnector.php';

// Handle delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_trip_id'])) {
    $deleteTripID = intval($_POST['delete_trip_id']);
    $stmtDelete = $conn->prepare("DELETE FROM Trips WHERE TripID = ?");
    $stmtDelete->bind_param("i", $deleteTripID);
    $stmtDelete->execute();
    $stmtDelete->close();
    // Redirect to avoid form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Helper functions
function getJeepName($conn, $jeepID) {
    if (!$jeepID) return null;
    $stmt = $conn->prepare("SELECT JeepName FROM Jeep WHERE JeepID = ?");
    $stmt->bind_param("i", $jeepID);
    $stmt->execute();
    $result = $stmt->get_result();
    return ($row = $result->fetch_assoc()) ? $row['JeepName'] : null;
}

function getLandmarkName($conn, $landmarkID) {
    if (!$landmarkID) return null;
    $stmt = $conn->prepare("SELECT LandmarkName FROM Landmarks WHERE LandmarkID = ?");
    $stmt->bind_param("i", $landmarkID);
    $stmt->execute();
    $result = $stmt->get_result();
    return ($row = $result->fetch_assoc()) ? $row['LandmarkName'] : null;
}

function getSavedTripTimestamp($conn, $tripID) {
    $stmt = $conn->prepare("SELECT DateCreated, TimeCreated FROM SavedTrips WHERE TripID = ?");
    $stmt->bind_param("i", $tripID);
    $stmt->execute();
    $result = $stmt->get_result();
    return ($row = $result->fetch_assoc()) ? $row : null;
}

// Fetch trips
$result = $conn->query("SELECT * FROM Trips ORDER BY TripID DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Saved Trips</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <div class="logo-title">
        <img src="123.png" alt="Logo" class="logo">
        <h1>Navigation System</h1>
    </div>
</header>
<nav class="main-nav">
    <button onclick="location.href='index.php'">Home</button>
    <button onclick="location.href='routeFinder.php'">Route Finder</button>
</nav>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Trip ID</th>
                <th>Jeep 1</th>
                <th>Jeep 2</th>
                <th>Origin</th>
                <th>Drop-Off</th>
                <th>Destination</th>
                <th>AC Type</th>
                <th>Passenger Type</th>
                <th>Fare (₱)</th>
                <th>Date Created</th>
                <th>Time Created</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($trip = $result->fetch_assoc()):
                $timestamps = getSavedTripTimestamp($conn, $trip['TripID']);
            ?>
                <tr>
                    <td data-label="Trip ID"><?= htmlspecialchars($trip['TripID']) ?></td>
                    <td data-label="Jeep 1"><?= htmlspecialchars(getJeepName($conn, $trip['JeepID1'])) ?: 'N/A' ?></td>
                    <td data-label="Jeep 2"><?= htmlspecialchars(getJeepName($conn, $trip['JeepID2'])) ?: 'N/A' ?></td>
                    <td data-label="Origin"><?= htmlspecialchars(getLandmarkName($conn, $trip['OriginID'])) ?: 'N/A' ?></td>
                    <td data-label="Drop-Off"><?= htmlspecialchars(getLandmarkName($conn, $trip['DropOffID'])) ?: 'N/A' ?></td>
                    <td data-label="Destination"><?= htmlspecialchars(getLandmarkName($conn, $trip['DestinationID'])) ?: 'N/A' ?></td>
                    <td data-label="AC Type"><?= htmlspecialchars($trip['ACType']) ?: 'N/A' ?></td>
                    <td data-label="Passenger Type"><?= htmlspecialchars($trip['PassengerType']) ?: 'N/A' ?></td>
                    <td data-label="Fare"><?= number_format($trip['Fare'], 2) ?></td>
                    <td data-label="Date Created"><?= $timestamps ? htmlspecialchars($timestamps['DateCreated']) : 'Not saved yet' ?></td>
                    <td data-label="Time Created"><?= $timestamps ? htmlspecialchars($timestamps['TimeCreated']) : 'Not saved yet' ?></td>
                    <td class="delete-cell" data-label="Action">
                        <form method="POST" class="delete-form" onsubmit="return confirm('Are you sure you want to delete Trip ID <?= htmlspecialchars($trip['TripID']) ?>?');">
                            <input type="hidden" name="delete_trip_id" value="<?= htmlspecialchars($trip['TripID']) ?>" />
                            <button type="submit" class="delete-btn" title="Delete Trip">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>

<?php
$conn->close();
?>
