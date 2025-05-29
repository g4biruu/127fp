<?php
include 'DBConnector.php';

$sql = "
SELECT 
    r.RouteID,
    r.RouteName,
    GROUP_CONCAT(DISTINCT l.LandmarkName ORDER BY l.LandmarkID SEPARATOR ', ') AS Landmarks,
    CONCAT(s.firstTrip, ' - ', s.lastTrip) AS OperationTime,
    MIN(f.MinimumFare) AS MinFare
FROM route r
LEFT JOIN routelandmark rl ON r.RouteID = rl.RouteID
LEFT JOIN landmark l ON rl.LandmarkID = l.LandmarkID
LEFT JOIN routeschedule rs ON r.RouteID = rs.RouteID
LEFT JOIN schedule s ON rs.ScheduleID = s.ScheduleID
LEFT JOIN routefare rf ON r.RouteID = rf.RouteID
LEFT JOIN fare f ON rf.FareID = f.FareID
GROUP BY r.RouteID
ORDER BY r.RouteID;
";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sakay na Iloilo!</title>
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
    <button onclick="location.href='terminal.php'">Terminal</button>
    <button onclick="location.href='route_finder.html'">Route Finder</button>
    <button onclick="location.href='saved_routes.html'">View Saved Routes</button>
    <div class="login-link"><a href="admin_login.html">Admin Login</a></div>
</nav>

<div class="terminal-search">
    <label for="terminal">Select a Terminal:</label>
    <select id="terminal">
        <option>Select Terminal</option>
        <?php
        if ($result && $result->num_rows > 0) {
            $result->data_seek(0); // rewind result set
            while ($row = $result->fetch_assoc()) {
                echo '<option value="' . htmlspecialchars($row['RouteID']) . '">' . htmlspecialchars($row['RouteName']) . '</option>';
            }
        }
        ?>
    </select>
</div>

<div class="map-section">
    <h2>Terminal Map (Static)</h2>
    <div class="map">
        <div class="map-label" style="top: 30px; left: 20px;">Landmark 1</div>
        <div class="map-label" style="top: 20px; right: 20px;">Terminal 1</div>
        <div class="map-label" style="bottom: 20px; left: 50%;">Terminal 2</div>
    </div>
</div>

<div class="table-section">
    <h2>List Of Jeepneys in Terminal</h2>
    <table>
        <thead>
        <tr>
            <th>Route #</th>
            <th>Route Name</th>
            <th>Landmarks</th>
            <th>Operation Time</th>
            <th>Min Fare</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if ($result && $result->num_rows > 0) {
            $result->data_seek(0); // rewind again
            while ($row = $result->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($row['RouteID']) . '</td>';
                echo '<td>' . htmlspecialchars($row['RouteName']) . '</td>';
                echo '<td>' . htmlspecialchars($row['Landmarks']) . '</td>';
                echo '<td>' . ($row['OperationTime'] ? htmlspecialchars($row['OperationTime']) : 'N/A') . '</td>';
                echo '<td>₱' . htmlspecialchars($row['MinFare']) . '</td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="5">No data available</td></tr>';
        }
        ?>
        </tbody>
    </table>
</div>
</body>
</html>

<?php $conn->close(); ?>
