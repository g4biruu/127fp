<?php
include 'DBConnector.php';

$pickup = $_POST['pickup'];
$dropoff = $_POST['dropoff'];
$passengerType = $_POST['passengerType'];

// Get LandmarkIDs and distances
$landmarks = $conn->prepare("SELECT LandmarkName, Distance FROM landmarks WHERE LandmarkName IN (?, ?)");
$landmarks->bind_param("ss", $pickup, $dropoff);
$landmarks->execute();
$res = $landmarks->get_result();

$dist = [];
while ($row = $res->fetch_assoc()) {
    $dist[$row['LandmarkName']] = $row['Distance'];
}
$distance = abs($dist[$pickup] - $dist[$dropoff]);

// Get fare rate
$fareQ = $conn->prepare("SELECT FarePerKM, MinimumFare FROM fare WHERE PassengerType = ?");
$fareQ->bind_param("s", $passengerType);
$fareQ->execute();
$fare = $fareQ->get_result()->fetch_assoc();

$farePerKM = $fare['FarePerKM'];
$minFare = $fare['MinimumFare'];
$fareTotal = max($minFare, $farePerKM * $distance);

// Time estimate
$avgSpeed = 25.0; // km/h
$estTime = round(($distance / $avgSpeed) * 60); // in minutes

// Get all matching routes
$routes = $conn->query("
    SELECT r.RouteID, r.RouteName, s.firstTrip, s.lastTrip
    FROM route r
    JOIN routelandmark rl1 ON r.RouteID = rl1.RouteID
    JOIN routelandmark rl2 ON r.RouteID = rl2.RouteID
    LEFT JOIN routeschedule rs ON r.RouteID = rs.RouteID
    LEFT JOIN schedule s ON rs.ScheduleID = s.ScheduleID
    WHERE rl1.LandmarkID = (
        SELECT LandmarkID FROM landmark WHERE LandmarkName = '$pickup'
    ) AND rl2.LandmarkID = (
        SELECT LandmarkID FROM landmark WHERE LandmarkName = '$dropoff'
    )
    GROUP BY r.RouteID
    ORDER BY r.RouteName ASC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Suggested Routes - Sakay na Iloilo!</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <div class="logo-title">
        <img src="logo.png" alt="Logo" class="logo">
        <h1>Sakay na Iloilo!</h1>
    </div>
</header>

<nav class="main-nav">
    <button onclick="location.href='index.php'">Home</button>
    <button onclick="location.href='find_route.php'">Find Another Route</button>
    <button onclick="location.href='view_archive.php'">View Archive</button>
</nav>

<div class="table-section">
    <h3>Suggested Routes</h3>
    <table>
        <thead>
        <tr>
            <th>Vehicle to Ride</th>
            <th>From</th>
            <th>To</th>
            <th>Time of Travel</th>
            <th>Est. Time (min)</th>
            <th>Est. Fare</th>
            <th>Save</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($routes && $routes->num_rows > 0): ?>
            <?php while ($row = $routes->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['RouteName']) ?></td>
                    <td><?= htmlspecialchars($pickup) ?></td>
                    <td><?= htmlspecialchars($dropoff) ?></td>
                    <td><?= $row['firstTrip'] . " - " . $row['lastTrip'] ?></td>
                    <td><?= $estTime ?></td>
                    <td>₱<?= number_format($fareTotal, 2) ?></td>
                    <td>
                        <form action="save_trip.php" method="POST">
                            <input type="hidden" name="routeName" value="<?= htmlspecialchars($row['RouteName']) ?>">
                            <input type="hidden" name="pickup" value="<?= htmlspecialchars($pickup) ?>">
                            <input type="hidden" name="dropoff" value="<?= htmlspecialchars($dropoff) ?>">
                            <input type="hidden" name="passengerType" value="<?= htmlspecialchars($passengerType) ?>">
                            <input type="hidden" name="fare" value="<?= $fareTotal ?>">
                            <input type="hidden" name="estTime" value="<?= $estTime ?>">
                            <button type="submit">Save</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="7">No matching routes found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
<?php $conn->close(); ?>
